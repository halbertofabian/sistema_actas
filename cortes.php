<?php
include_once './config.php';
require_once('./libs/TCPDF/tcpdf.php');
require_once './modelo.php';
class Cortes
{
    public function generarCortes()
    {
        if (isset($_GET['clientes'])) {
            // $clientes = Modelo::mdlMostrarClientes();
            $clientesDecodificados = base64_decode($_GET['clientes']);
            $clientes = explode(',', $clientesDecodificados);
            $array_send_info = array();
            $array_Nosend_info = array();
            $countSend = 0;
            $countNoSend = 0;
            foreach ($clientes as $key => $cliente) {
                $clt = Modelo::mdlMostrarClienteById($cliente);
                # code...
                $clt_tipo_corte = json_decode($clt['clt_tipo_corte'], true);
                $params = array(
                    'token' => WA_TOKEN,
                    'chatId' => $clt['clt_gpo_wpp'],
                    'limit' => '10'
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
                        $totalCurp = 0;
                        $totalSusRet = 0;
                        $totalEdoInfo = 0;
                        $total32D = 0;
                        $sum_total = 0;
                        $saldo = 0;
                        $mensaje_saldo = "";
                        foreach ($reversedArray as $key => $msg) {
                            // $opciones = array('ACTAS', 'RFC', 'CFE', 'NSS');
                            $informacionPedido = Cortes::extraerInformacionPedido($msg['body'], $servicios);
                            if ($informacionPedido !== null) {
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
                                if ($informacionPedido !== null && isset($informacionPedido['CURP']) && $informacionPedido['CURP'] != "") {
                                    $totalCurp += intval($informacionPedido['CURP']);
                                }
                                if ($informacionPedido !== null && isset($informacionPedido['SUS/RET']) && $informacionPedido['SUS/RET'] != "") {
                                    $totalSusRet += intval($informacionPedido['SUS/RET']);
                                }
                                if ($informacionPedido !== null && isset($informacionPedido['EDO INFO']) && $informacionPedido['EDO INFO'] != "") {
                                    $totalEdoInfo += intval($informacionPedido['EDO INFO']);
                                }
                                if ($informacionPedido !== null && isset($informacionPedido['32D']) && $informacionPedido['32D'] != "") {
                                    $total32D += intval($informacionPedido['32D']);
                                }
                                if ($informacionPedido !== null && $informacionPedido['TipoSaldo'] !== null && $informacionPedido['Saldo'] !== null) {
                                    $tipoSaldo = $informacionPedido['TipoSaldo'];
                                    $saldo = $informacionPedido['Saldo'];

                                    if ($tipoSaldo === 'A favor') {
                                        $sum_total -= $saldo;
                                        $mensaje_saldo = "Saldo a favor: $$saldo";
                                    } else if ($tipoSaldo === 'En contra') {
                                        $sum_total += $saldo;
                                        $mensaje_saldo = "Saldo pendiente: $$saldo";
                                    }
                                }
                                break;
                            } else {
                                continue;
                            }
                        }

                        $paquete = Modelo::mdlMostrarPreciosByPaquete($clt['clt_paquete']);

                        $precio_total_actas = 0;
                        $precio_total_rfc = 0;
                        $precio_total_cfe = 0;
                        $precio_total_nss = 0;
                        $precio_total_curp = 0;
                        $precio_total_susret = 0;
                        $precio_total_edoinfo = 0;
                        $precio_total_32D = 0;
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
                            if ($pqt['srv_nombre'] == "CURP") {
                                $precio_total_curp = $totalCurp * $pqt['prc_precio'];
                            }
                            if ($pqt['srv_nombre'] == "SUS/RET") {
                                $precio_total_susret = $totalSusRet * $pqt['prc_precio'];
                            }
                            if ($pqt['srv_nombre'] == "EDO INFO") {
                                $precio_total_edoinfo = $totalEdoInfo * $pqt['prc_precio'];
                            }
                            if ($pqt['srv_nombre'] == "32D") {
                                $precio_total_32D = $total32D * $pqt['prc_precio'];
                            }
                        }

                        $sum_total += $precio_total_actas + $precio_total_rfc + $precio_total_cfe + $precio_total_nss + $precio_total_curp + $precio_total_susret + $precio_total_edoinfo + $precio_total_32D;
                        $referencia = generarCodigoNumeros(6);


                        $mensaje1 = array(
                            'token' => WA_TOKEN,
                            'to' => $clt_tipo_corte['valor'],
                            'body' => "Buenos días, esperamos y se encuentren bien, el día de hoy hacemos corte, apóyenos en verificar si su corte está bien, esperamos su depósito, gracias por su preferencia. 🖥️❗️❗️
Favor de ingresar la siguiente *Referencia* a la hora de hacer su pago: *$referencia*

Total ACTAS: $totalActas = $$precio_total_actas
Total RFC: $totalRfc = $$precio_total_rfc
Total CFE: $totalCfe = $$precio_total_cfe
Total NSS: $totalNss = $$precio_total_nss 
Total CURP: $totalCurp = $$precio_total_curp 
Total SUS/RET: $totalSusRet = $$precio_total_susret 
Total EDO INFO: $totalEdoInfo = $$precio_total_edoinfo 
Total 32D: $total32D = $$precio_total_32D 

$mensaje_saldo

Total: $$sum_total

Apóyenos en generar su pago antes de terminar el día. ¡Muchas gracias! 🎇"
                        );
                        if ($totalActas == 0 && $totalRfc == 0 && $totalCfe == 0 && $totalNss == 0 && $totalCurp == 0 && $totalSusRet == 0 && $totalEdoInfo == 0 && $total32D == 0) {
                            continue;
                        }
                        $response1 = Cortes::enviarMensaje($mensaje1);

                        if ($response1['status']) {
                            // Si el primer mensaje se envió correctamente, envías el segundo mensaje
                            $mensaje2 = array(
                                'token' => WA_TOKEN,
                                'to' => $clt_tipo_corte['valor'],
                                'body' => "FORMAS DE PAGO ⚠️

*REFERENCIA $referencia*

DEPÓSITO CAJERO BBVA:
TARJETA: 4152-3141-7083-0825
BENEFICIARIO: Fernando Daniel Romo.


TRANSFERENCIA
TARJETA: 4152-3139-8638-7491
BENEFICIARIO: Fernando Daniel Romo.
BANCO: 🏦 BANCOMER.


DEPÓSITOS OXXO SPIN
TARJETA: 4217-4700-5566-8236
BENEFICIARIO: Fernando Daniel Romo.
BANCO: 🏦 STP"
                            );
                            $response2 = Cortes::enviarMensaje($mensaje2);

                            if ($response2['status']) {
                                array_push($array_send_info, array(
                                    'clt_nombre' => $clt['clt_nombre'],
                                    'clt_nombre_gpo' => $clt['clt_nombre_gpo'],
                                    'clt_tramites' => "
                                        ACTAS: $totalActas = $$precio_total_actas\r 
                                        RFC: $totalRfc = $$precio_total_rfc\r
                                        CFE: $totalCfe = $$precio_total_cfe\r
                                        NSS: $totalNss = $$precio_total_nss\r
                                        CURP: $totalCurp = $$precio_total_curp\r
                                        SUS/RET: $totalSusRet = $$precio_total_susret\r 
                                        EDO INFO: $totalEdoInfo = $$precio_total_edoinfo",
                                    'clt_saldo' => $mensaje_saldo,
                                    'clt_total' => "$" . $sum_total
                                ));
                                Modelo::mdlActualizarEstadoEnvioCliente(1, $clt['clt_id']);
                            } else {
                                array_push($array_Nosend_info, array('clt_nombre' => $clt['clt_nombre'], 'clt_nombre_gpo' => $clt['clt_nombre_gpo']));
                            }
                        } else {
                            array_push($array_Nosend_info, array('clt_nombre' => $clt['clt_nombre'], 'clt_nombre_gpo' => $clt['clt_nombre_gpo']));
                        }
                    }
                }
            }
            Cortes::imprimirReporte($array_send_info, $array_Nosend_info);
        }
    }

    public function extraerInformacionPedido($texto, $opciones)
    {
        // Buscar la palabra "Pedido" y extraer información
        if (strpos($texto, 'Conteo') !== false || strpos($texto, 'CONTEO') !== false || strpos($texto, 'conteo') !== false) {
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


            // Extraer el saldo del texto
            preg_match("/Saldo:(.*)/i", $texto, $coincidenciaSaldo);
            if (!empty($coincidenciaSaldo)) {
                $saldoString = trim($coincidenciaSaldo[1]); // Eliminar espacios en blanco al inicio y al final

                // Convertir el saldo a un número entero
                $saldo = (int) $saldoString;

                // Determinar si el saldo es a favor o en contra
                if ($saldo < 0) {
                    $tipoSaldo = 'En contra';
                    $saldo = abs($saldo); // Hacer el saldo positivo para facilitar el cálculo
                } else {
                    $tipoSaldo = 'A favor';
                    $saldo = abs($saldo);
                }

                $informacion['TipoSaldo'] = $tipoSaldo;
                $informacion['Saldo'] = $saldo;
            } else {
                // Si no se encuentra la línea, asignar un valor por defecto o null según sea necesario
                $informacion['TipoSaldo'] = null;
                $informacion['Saldo'] = null;
            }

            return $informacion;
        } else {
            return null; // Retorna null si no se encuentra la palabra "Pedido" en el texto.
        }
    }

    public function enviarMensaje($mensaje)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => WA_API_URL . "messages/chat",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($mensaje),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return array('status' => false, 'mensaje' => 'Hubo un error al enviar el mensaje');
        } else {
            return array('status' => true, 'mensaje' => 'El mensaje se envió correctamente');
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
        $fecha_corte = date('d-m-Y');

        $body1 = "";
        $body2 = "";

        $total_general = 0;
        foreach ($array_send_info as $key => $clt) {
            $total_general += dnum($clt['clt_total']);
            $body1 .= "
                <tr>
                    <td style='vertical-align: middle;'>$clt[clt_nombre]</td>
                    <td style='vertical-align: middle;'>$clt[clt_nombre_gpo]</td>
                    <td>" . nl2br($clt['clt_tramites']) . "</td>
                    <td style='vertical-align: middle;'>$clt[clt_saldo]</td>
                    <td style='vertical-align: middle;'>$clt[clt_total]</td>
                </tr>
            ";
        }
        foreach ($array_Nosend_info as $key => $clt) {
            $body2 .= "
                <tr>
                    <td>$clt[clt_nombre]</td>
                    <td>$clt[clt_nombre_gpo]</td>
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
            <td style="width:70%;">
                <table style="width:100%; text-align: center;" border="1">
                    <thead>
                        <tr>
                            <th colspan="5" style="background-color:#014C50; width:100%; color:#fff;text-align: center;vertical-align:text-top; font-size:12px;">CORTES ENVIADOS :)</th>
                        </tr>
                        <tr style="background-color:#014C50; width:100%; color:#fff;text-align: center;vertical-align:text-top; font-size:12px ">
                            <th>NOMBRE</th>
                            <th>GRUPO</th>
                            <th>TRAMITES</th>
                            <th>SALDO</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        $body1
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="background-color:#014C50; color:#fff;text-align: right;vertical-align:text-top; font-size:12px;">TOTAL</td>
                            <td style="background-color:#014C50; color:#fff;text-align: center;vertical-align:text-top; font-size:12px;">$$total_general</td>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td style="width:30%;">
                <table style="width:100%;" border="1">
                    <thead>
                        <tr>
                            <th colspan="2" style="background-color:#014C50; width:100%; color:#fff;text-align: center;vertical-align:text-top; font-size:12px;">CORTES NO ENVIADOS :(</th>
                        </tr>
                        <tr style="background-color:#014C50; width:100%; color:#fff;text-align: center;vertical-align:text-top; font-size:12px ">
                            <th>NOMBRE</th>
                            <th>GRUPO</th>
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
