<?php
include_once './config.php';
require_once('./libs/TCPDF/tcpdf.php');
require_once './modelo.php';
class Cortes
{
    public function generarCortes()
    {
        $clientes = Modelo::mdlMostrarClientes();
        $array_send_info = array();
        $array_Nosend_info = array();
        $countSend = 0;
        $countNoSend = 0;
        foreach ($clientes as $key => $clt) {
            # code...
            $clt_tipo_corte = json_decode($clt['clt_tipo_corte'], true);
            $params = array(
                'token' => WA_TOKEN,
                'chatId' => $clt['clt_gpo_wpp'],
                'limit' => '20'
            );
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => WA_API_URL . "chats/messages?" . http_build_query($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "CURL Error #:" . $err;
            } else {
                $mensajes = json_decode($response, true);
                if ($mensajes === null) {
                    echo "Error al decodificar la respuesta JSON.";
                } else {
                    $autor = '521' . WA_NUMERO . '@c.us';
                    $reversedArray = array_reverse($mensajes);
                    $servicios = Modelo::mdlMostrarServicios();
                    $totalActas = 0;
                    $totalRfc = 0;
                    $totalCfe = 0;
                    $totalNss = 0;
                    foreach ($reversedArray as $key => $msg) {
                        // $opciones = array('ACTAS', 'RFC', 'CFE', 'NSS');
                        if ($autor == $msg['author']) {
                            $informacionPedido = Cortes::extraerInformacionPedido($msg['body'], $servicios);
                            if ($informacionPedido !== null && isset($informacionPedido['ACTAS']) && $informacionPedido['ACTAS'] != "") {
                                $totalActas += intval($informacionPedido['ACTAS']);
                            }
                            if ($informacionPedido !== null && isset($informacionPedido['RFC']) && $informacionPedido['RFC'] != "") {
                                $totalRfc += intval($informacionPedido['RFC']);
                            }
                            if ($informacionPedido !== null && isset($informacionPedido['CFE']) && $informacionPedido['CFE'] != "") {
                                $totalCfe += intval($informacionPedido['CFE']);
                            }
                            if ($informacionPedido !== null && isset($informacionPedido['NSS']) && $informacionPedido['NSS'] != "") {
                                $totalNss += intval($informacionPedido['NSS']);
                            }
                        } else {
                            continue;
                        }
                    }

                    $paquete = Modelo::mdlMostrarPreciosByPaquete($clt['clt_paquete']);

                    $precio_total_actas = 0;
                    $precio_total_rfc = 0;
                    $precio_total_cfe = 0;
                    $precio_total_nss = 0;
                    $sum_total = 0;
                    foreach ($paquete as $key => $pqt) {
                        if ($pqt['srv_nombre'] == "ACTAS") {
                            $precio_total_actas = $totalActas * $pqt['prc_precio'];
                        }
                        if ($pqt['srv_nombre'] == "RFC") {
                            $precio_total_rfc = $totalRfc * $pqt['prc_precio'];
                        }
                        if ($pqt['srv_nombre'] == "CFE") {
                            $precio_total_cfe = $totalCfe * $pqt['prc_precio'];
                        }
                        if ($pqt['srv_nombre'] == "NSS") {
                            $precio_total_nss = $totalNss * $pqt['prc_precio'];
                        }
                    }

                    $sum_total = $precio_total_actas + $precio_total_rfc + $precio_total_cfe + $precio_total_nss;
                    $referencia = generarCodigoNumeros(10);


                    $params2 = array(
                        'token' => WA_TOKEN,
                        'to' => $clt_tipo_corte['valor'],
                        'body' => "Total ACTAS: $totalActas = $$precio_total_actas
Total RFC: $totalRfc = $$precio_total_rfc
Total CFE: $totalCfe = $$precio_total_cfe
Total NSS: $totalNss = $$precio_total_nss 

Total: $$sum_total 

Datos bancarios:
Banco: BBVA 
Cuenta: 3263 7876 6723 7890
Nombre: Daniel...
Referencia: $referencia
                "
                    );
                    $curl2 = curl_init();
                    curl_setopt_array($curl2, array(
                        CURLOPT_URL => WA_API_URL . "messages/chat",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => http_build_query($params2),
                        CURLOPT_HTTPHEADER => array(
                            "content-type: application/x-www-form-urlencoded"
                        ),
                    ));

                    $response2 = curl_exec($curl2);
                    $err2 = curl_error($curl2);

                    curl_close($curl2);

                    if ($err2) {
                        // echo "cURL Error #:" . $err2;
                        array_push($array_Nosend_info, array('clt_nombre' => $clt['clt_nombre'], 'clt_nombre_gpo' => $clt['clt_nombre_gpo']));
                    } else {
                        // echo $response2;
                        array_push($array_send_info, array('clt_nombre' => $clt['clt_nombre'], 'clt_nombre_gpo' => $clt['clt_nombre_gpo']));
                    }
                }
            }
        }
        Cortes::imprimirReporte($array_send_info, $array_Nosend_info);
    }

    public function extraerInformacionPedido($texto, $opciones)
    {
        // Buscar la palabra "Pedido" y extraer información
        if (strpos($texto, 'Pedido') !== false || strpos($texto, 'PEDIDO') !== false || strpos($texto, 'pedido') !== false) {
            $informacion = array();

            foreach ($opciones as $opcion) {
                // Para cada opción, buscar la correspondiente línea en el texto
                preg_match("/{$opcion['srv_nombre']}:(.*)/i", $texto, $coincidencia);

                if (!empty($coincidencia)) {
                    $valor = $coincidencia[1];
                    $informacion[$opcion['srv_nombre']] = $valor;
                } else {
                    // Si no se encuentra la línea, asignar un valor por defecto o null según sea necesario
                    $informacion[$opcion['srv_nombre']] = null;
                }
            }

            return $informacion;
        } else {
            return null; // Retorna null si no se encuentra la palabra "Pedido" en el texto.
        }
    }

    public function imprimirReporte($array_send_info, $array_Nosend_info)
    {
        // $array_send_info = ['victor', 'sarai', 'alberto'];
        // $array_Nosend_info = [];
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('');
        $pdf->SetTitle('');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');



        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 9, '', true);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage('P');

        $logo = HTTP_HOST . '/app-assets/imagenes/logo-dany.jpg';
        $img_success = '<img src="' . HTTP_HOST . '/app-assets/imagenes/v_1.png" width="20" />';
        $img_error = '<img src="' . HTTP_HOST . '/app-assets/imagenes/v_0.png" width="20" />';
        $fecha_corte = date('d-m-Y');

        $body1 = "";
        $body2 = "";

        foreach ($array_send_info as $key => $clt) {
            $body1 .= "
                <tr>
                    <td>$clt[clt_nombre]</td>
                    <td>$clt[clt_nombre_gpo]</td>
                    <td>$img_success</td>
                </tr>
            ";
        }
        foreach ($array_Nosend_info as $key => $clt) {
            $body2 .= "
                <tr>
                    <td>$clt[clt_nombre]</td>
                    <td>$clt[clt_nombre_gpo]</td>
                    <td>$img_error</td>
                </tr>
            ";
        }

        $header = <<<EOF
<table cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left; width:30%;">
                <img src="$logo" width="120" /><br>
            </td>
            <td style="text-align:center;font-weight: bold; width:40%;">
                Tramites Dany <br>
                Calle Amistad #1394, Guadalajara, Mexico<br>
                33 3327 5876
            </td>
            <td style="text-align:center;width:30%;">
                <strong>Fecha corte:</strong> $fecha_corte<br>
            </td>
        </tr>
        <tr>
            <td style="background-color:black; width:100%; color:#fff;text-align: center;vertical-align:text-top; font-size:16px ">
                    REPORTE DE CORTE
            </td>
        </tr>
        <br>
        <br>
        <tr>
            <td style="width:50%;">
                <table style="width:100%; text-align: center;" border="1">
                    <thead>
                        <tr style="background-color:#014C50; width:100%; color:#fff;text-align: center;vertical-align:text-top; font-size:12px ">
                            <th>NOMBRE</th>
                            <th>GRUPO</th>
                            <th>CHECK</th>
                        </tr>
                    </thead>
                    <tbody>
                        $body1
                    </tbody>
                </table>
            </td>
            <td style="width:50%;">
                <table style="width:100%;" border="1">
                    <thead>
                        <tr style="background-color:#014C50; width:100%; color:#fff;text-align: center;vertical-align:text-top; font-size:12px ">
                            <th>NOMBRE</th>
                            <th>GRUPO</th>
                            <th>CHECK</th>
                        </tr>
                    </thead>
                    <tbody>
                        $body2
                    </tbody>
                </table>
            </td>
        </tr>
</table>

EOF;
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell(0, 0, '', '', $header, 0, 1, 0, true, '', true);


        ob_end_clean();

        $registro = str_replace(".", "", "Corte-" . date('d-m-Y'));
        $pdf->Output($registro . '.pdf', 'I');
    }
}

$corte = new Cortes();
// $corte->imprimirReporte("", "");
$corte->generarCortes();
