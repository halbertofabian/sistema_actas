<?php
include_once './config.php';
require_once './modelo.php';
if (isset($_GET['cliente'])) {
    $clt_id = $_GET['cliente'];
    $clt = Modelo::mdlMostrarClienteById($clt_id);
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
            // var_dump($reversedArray);
            // return;
            $servicios = Modelo::mdlMostrarServicios();
            $totalActas = 0;
            $totalRfc = 0;
            $totalCfe = 0;
            $totalNss = 0;
            foreach ($reversedArray as $key => $msg) {
                // $opciones = array('ACTAS', 'RFC', 'CFE', 'NSS');
                if ($autor == $msg['author']) {
                    $informacionPedido = extraerInformacionPedido($msg['body'], $servicios);
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

            echo 'Total de actas: ' . $totalActas . '<br>';
            echo 'Total de rfc: ' . $totalRfc . '<br>';
            echo 'Total de cfe: ' . $totalCfe . '<br>';
            echo 'Total de nss: ' . $totalNss . '<br>';
            var_dump($paquete);
            echo 'Precio total actas: ' . $precio_total_actas . '<br>';
            echo 'Precio total rfc: ' . $precio_total_rfc . '<br>';
            echo 'Precio total cfe: ' . $precio_total_cfe . '<br>';
            echo 'Precio total nss: ' . $precio_total_nss . '<br>';
        }
    }
}

function extraerInformacionPedido($texto, $opciones)
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
