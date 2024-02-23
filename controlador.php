<?php
include_once './config.php';
require_once './modelo.php';
class Controlador
{
    public  function cargarArchivo()
    {
        $rutaActual = getcwd();  // Obtener la ruta absoluta del directorio actual
        $target_dir = $rutaActual . "/temp_file/"; // Ruta absoluta para la carpeta de temp_file

        // Verifica si el directorio no existe
        if (!file_exists($target_dir)) {
            // Crea el directorio y establece permisos 755
            mkdir($target_dir, 0755);
        }

        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $fileNameWithoutExt = pathinfo($target_file, PATHINFO_FILENAME);

        $estados = Controlador::obtenerClavesEstados();
        // Comprueba si el archivo ya existe
        if (file_exists($target_file)) {

            return array(
                'status' => true,
                'mensaje' => "El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido cargado.",
                'ruta' => $target_file,
                'nombre_archivo' => $fileNameWithoutExt,
                'estado' => Controlador::obtenerCalveEstadoDeCurp($fileNameWithoutExt)
            );
        }

        // Comprueba el tamaño del archivo (por ejemplo, limita a 5MB)
        if ($_FILES["fileToUpload"]["size"] > 5000000) {
            return array(
                'status' => false,
                'mensaje' => "El archivo es demasiado grande.",
                'ruta' => '',
                'nombre_archivo' => $fileNameWithoutExt,
            );
        }

        // Permite solo ciertos formatos de archivo (en este caso, solo PDF)
        if ($fileType != "pdf") {
            return array(
                'status' => false,
                'mensaje' => "Solo se permiten archivos PDF.",
                'ruta' => '',
                'nombre_archivo' => $fileNameWithoutExt
            );
        }

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            return array(
                'status' => true,
                'mensaje' => "El archivo " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " ha sido cargado.",
                'ruta' => $target_file,
                'nombre_archivo' => $fileNameWithoutExt,
                'estado' => Controlador::obtenerCalveEstadoDeCurp($fileNameWithoutExt)
            );
        } else {
            return array(
                'status' => false,
                'mensaje' => "Hubo un error al cargar el archivo.",
                'ruta' => '',
                'nombre_archivo' => $fileNameWithoutExt
            );
        }
    }
    public  function eliminarArchivo($rutaCompleta)
    {
        if (file_exists($rutaCompleta)) {
            // Intenta eliminar el archivo
            if (unlink($rutaCompleta)) {
                return array(
                    'status' => true,
                    'mensaje' => "El archivo ha sido eliminado exitosamente."
                );
            } else {
                return array(
                    'status' => false,
                    'mensaje' => "Hubo un error al eliminar el archivo."
                );
            }
        } else {
            return array(
                'status' => false,
                'mensaje' => "El archivo especificado no existe."
            );
        }
    }

    public static function obtenerEstadoDeCurp($curp, $estados)
    {
        $curpExtraida = substr($curp, 0, 18);
        // Extraemos las letras correspondientes al estado (posiciones 12 y 13)
        $claveEstado = substr($curpExtraida, 11, 2);

        // Buscamos el estado en el arreglo
        if (isset($estados[$claveEstado])) {
            return $estados[$claveEstado];
        } else {
            return "Clave de estado no reconocida";
        }
    }

    public static function obtenerEstadoDeClave($clave, $estados)
    {
        // Buscamos el estado en el arreglo
        if (isset($estados[$clave])) {
            return $estados[$clave];
        } else {
            return "Clave de estado no reconocida";
        }
    }

    public static function obtenerCalveEstadoDeCurp($curp)
    {
        $curpExtraida = substr($curp, 0, 18);
        // Extraemos las letras correspondientes al estado (posiciones 12 y 13)
        return substr($curpExtraida, 11, 2);
    }

    public static function obtenerClavesEstados()
    {
        return array(
            "AS" => "Aguascalientes",
            "BC" => "Baja California",
            "BS" => "Baja California Sur",
            "CC" => "Campeche",
            "CL" => "Coahuila",
            "CM" => "Colima",
            "CS" => "Chiapas",
            "CH" => "Chihuahua",
            "DF" => "Ciudad de México",
            "DG" => "Durango",
            "GT" => "Guanajuato",
            "GR" => "Guerrero",
            "HG" => "Hidalgo",
            "JC" => "Jalisco",
            "MC" => "México",
            "MN" => "Michoacán",
            "MS" => "Morelos",
            "NT" => "Nayarit",
            "NL" => "Nuevo León",
            "OC" => "Oaxaca",
            "PL" => "Puebla",
            "QT" => "Querétaro",
            "QR" => "Quintana Roo",
            "SP" => "San Luis Potosí",
            "SL" => "Sinaloa",
            "SR" => "Sonora",
            "TC" => "Tabasco",
            "TS" => "Tamaulipas",
            "TL" => "Tlaxcala",
            "VZ" => "Veracruz",
            "YN" => "Yucatán",
            "ZS" => "Zacatecas",
            "NE" => "Nacido en el extranjero"
        );
    }

    public static function generarActa()
    {
        // Configura la solicitud HTTP POST
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(array('ruta' => $_POST['ruta'])),
            ),
        );
        $context = stream_context_create($options); // Crea un contexto de flujo

        // Realiza la solicitud HTTP POST al archivo de destino
        $resultado = file_get_contents(HTTP_HOST . 'combinacion/guardar.php', false, $context);
        $result = json_decode($resultado, true);
        if ($result['status']) {
            $respuesta = Controlador::combinarActa($_POST['ruta']);
            $result2 = json_decode($respuesta, true);
            if ($result2['status']) {
                if (!isset($_POST['sinReverso'])) {
                    $rvs = Modelo::mdlMostrarReversoByClave($_POST['clave_estado']);
                    $rvs_ruta = $rvs['rvs_ruta'];
                    if (!$rvs_ruta) {
                        unlink($_POST['ruta']);
                        return json_encode(array(
                            'status' => false,
                            'mensaje' => 'El reverso aun no se agrega al sistema, por favor agreguelo o seleccione sin reverso.',
                        ), true);
                    }
                    $respuesta2 = Controlador::generarActaCompleta($_POST['ruta'], $rvs_ruta);
                    $result3 = json_decode($respuesta2, true);
                    if ($result3['status']) {
                        $upload_result = Controlador::guardarActas($_POST['ruta']);
                        return $upload_result;
                    } else {
                        return $respuesta2;
                    }
                } else {
                    $upload_result = Controlador::guardarActas($_POST['ruta']);
                    return $upload_result;
                }
            } else {
                return $respuesta;
            }
        } else {
            // Error en la combinación de archivos
            return $resultado;
        }
    }
    public static function combinarActa($ruta)
    {
        // Configura la solicitud HTTP POST
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(array('ruta' => $ruta)),
            ),
        );
        $context = stream_context_create($options); // Crea un contexto de flujo

        // Realiza la solicitud HTTP POST al archivo de destino
        $resultado = file_get_contents(HTTP_HOST . 'unir/unir.php', false, $context);
        return $resultado;
    }
    public static function generarActaCompleta($ruta, $rvs_ruta)
    {
        // Configura la solicitud HTTP POST
        $options = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(array('ruta' => $ruta, 'rvs_ruta' => $rvs_ruta)),
            ),
        );
        $context = stream_context_create($options); // Crea un contexto de flujo

        // Realiza la solicitud HTTP POST al archivo de destino
        $resultado = file_get_contents(HTTP_HOST . 'combinacion/generar.php', false, $context);
        return $resultado;
    }

    public static function guardarReversos()
    {
        if (isset($_FILES["rvs_ruta"])) {
            $inputName = "rvs_ruta";
            $upload_result = subirArchivoReverso($inputName);
            if (is_array($upload_result) && isset($upload_result['status']) && $upload_result['status'] === false) {
                return $upload_result;
            } else {
                $url_file = $upload_result;
            }
        }

        $archivoGuardado = Modelo::mdlBuscarReversoByRuta($url_file);
        if ($archivoGuardado) {
            return array(
                'mensaje' => 'El archivo ya fue guardado en el servidor.',
            );
        } else {
            $datos = array(
                'rvs_clave' => $_POST['rvs_clave'],
                'rvs_ruta' => $url_file,
            );
            $archivo = Modelo::mdlAgregarReversos($datos);
            if ($archivo) {
                return array(
                    'status' => true,
                    'mensaje' => 'El reverso se a agregado con éxito',
                );
            } else {
                return array(
                    'status' => false,
                    'mensaje' => 'Hubo un error.',
                );
            }
        }
    }

    public static function guardarActas($ruta)
    {
        // Ruta donde se guardará la imagen en el servidor
        $directorioDestino = DOCUMENT_ROOT . "actas_realizadas/";
        $url_file = HTTP_HOST . "actas_realizadas/";

        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }


        // Generar un nombre único para el archivo
        $nombreArchivo = basename($ruta);
        // Ruta completa del archivo de destino
        $rutaDestino = $directorioDestino . $nombreArchivo;
        $url_file = $url_file . $nombreArchivo;

        // Mover el archivo subido al directorio de destino
        if (copy($ruta, $rutaDestino)) {
            if (file_exists($ruta)) {
                unlink($ruta);
            }
            $archivoGuardado = Modelo::mdlBuscarActaByRuta($url_file);
            if ($archivoGuardado) {
                return json_encode(array(
                    'mensaje' => 'El archivo ya fue guardado en el servidor.',
                ), true);
            } else {
                $datos = array(
                    'ar_curp' => pathinfo($nombreArchivo, PATHINFO_FILENAME),
                    'ar_ruta' => $url_file,
                );
                $archivo = Modelo::mdlAgregarActaCompletada($datos);
                if ($archivo) {

                    return json_encode(array(
                        'status' => true,
                        'mensaje' => 'La acta se a agregado con éxito',
                        'ruta_acta' => $url_file,
                    ), true);
                } else {
                    return json_encode(array(
                        'status' => false,
                        'mensaje' => 'Hubo un error.',
                    ), true);
                }
            }
        }
    }
    public static function eliminarActa()
    {
        $ar = Modelo::mdlBuscarActaById($_POST['ar_id']);
        $archivo = basename($ar['ar_ruta']);
        $imagen = DOCUMENT_ROOT . "actas_realizadas/" . $archivo;

        // Verificar si el archivo existe antes de intentar eliminarlo
        if (file_exists($imagen)) {
            unlink($imagen);
        }
        $res = Modelo::mdlEliminarActaCompletada($ar['ar_id']);
        if ($res) {
            return array('status' => true, 'mensaje' => 'La acta se elimino correctamente.');
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al eliminar el acta.');
        }
    }
    public static function eliminarReverso()
    {
        $rvs = Modelo::mdlBuscarReversosById($_POST['rvs_id']);
        $archivo = $rvs['rvs_ruta'];


        // Verificar si el archivo existe antes de intentar eliminarlo
        if (file_exists($archivo)) {
            unlink($archivo);
        }
        $res = Modelo::mdlEliminarReversoCompletado($rvs['rvs_id']);
        if ($res) {
            return array('status' => true, 'mensaje' => 'El reverso se elimino correctamente.');
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al eliminar el reverso.');
        }
    }
    public static function mostrarActas()
    {
        $dt_actas = array();
        $categorias = Modelo::mdlMostrarActas();
        foreach ($categorias as $key => $ar) {
            $dt_aux = array(
                'ar_id' => $ar['ar_id'],
                'ar_curp' => $ar['ar_curp'],
                'ar_acciones' => '
                <div class="btn-group" role="group" aria-label="">
                    <a type="button" class="btn btn-light" href="' . $ar['ar_ruta'] . '" target="_blank"><i class="fas fa-eye"></i></a>
                    <button type="button" class="btn btn-danger btnEliminarActa" ar_id="' . $ar['ar_id'] . '"><i class="fa fa-trash-alt"></i></button>
                </div>
                ',
            );

            array_push($dt_actas, $dt_aux);
        }

        return $dt_actas;
    }

    public static function mostrarReversos()
    {
        $dt_actas = array();
        $categorias = Modelo::mdlMostrarReversos();
        foreach ($categorias as $key => $rvs) {
            $dt_aux = array(
                'rvs_id' => $rvs['rvs_id'],
                'rvs_estado' => Controlador::obtenerEstadoDeClave($rvs['rvs_clave'], Controlador::obtenerClavesEstados()),
                'rvs_acciones' => '
                <div class="btn-group" role="group" aria-label="">
                    <a type="button" class="btn btn-light" href="' . HTTP_HOST . 'reversos_file/' . basename($rvs['rvs_ruta']) . '" target="_blank"><i class="fas fa-eye"></i></a>
                    <button type="button" class="btn btn-danger btnEliminarReverso" rvs_id="' . $rvs['rvs_id'] . '"><i class="fa fa-trash-alt"></i></button>
                </div>
                ',
            );

            array_push($dt_actas, $dt_aux);
        }

        return $dt_actas;
    }
    public static function mostrarUsuarios()
    {
        $dt_usuarios = array();
        $usuarios = Modelo::mdlMostrarUsuarios();
        foreach ($usuarios as $key => $usr) {
            $dt_aux = array(
                'usr_id' => $usr['usr_id'],
                'usr_correo' => $usr['usr_correo'],
                'usr_acciones' => '
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="btn btn-warning btnEditarUsuario" usr_id="' . $usr['usr_id'] . '" usr_correo="' . $usr['usr_correo'] . '"><i class="fas fa-edit"></i></a>
                    <button type="button" class="btn btn-danger btnEliminarUsuario" usr_id="' . $usr['usr_id'] . '"><i class="fa fa-trash-alt"></i></button>
                </div>
                ',
            );

            array_push($dt_usuarios, $dt_aux);
        }

        return $dt_usuarios;
    }
    public static function guardarUsuarios()
    {
        $_POST['usr_correo'] = trim($_POST['usr_correo']);
        $_POST['usr_contraseña'] = crypt(trim($_POST['usr_contraseña']), '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
        if ($_POST['usr_id'] == "") {
            $usr = Modelo::mdlMostrarUsuariosByCorreo($_POST['usr_correo']);
            if ($usr) {
                return array('status' => false, 'mensaje' => 'El correo ' . $_POST['usr_correo'] . ' ya existe. Intente con otro diferente.');
            }

            $res = Modelo::mdlAgregarUsuario($_POST);
            if ($res) {
                return array('status' => true, 'mensaje' => 'El usuario se guardo correctamente.');
            } else {
                return array('status' => false, 'mensaje' => 'Hubo un error al guardar el usuario.');
            }
        } else {
            $res = Modelo::mdlActualizarUsuario($_POST);
            if ($res) {
                return array('status' => true, 'mensaje' => 'El usuario se actualizo correctamente.');
            } else {
                return array('status' => false, 'mensaje' => 'Hubo un error al actualizar el usuario.');
            }
        }
    }
    public static function EliminarUsuarios()
    {
        $res = Modelo::mdlEliminarUsuario($_POST['usr_id']);
        if ($res) {
            return array('status' => true, 'mensaje' => 'El usuario se elimino correctamente.');
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al eliminar el usuario.');
        }
    }
    public static function iniciarSesion()
    {
        $encriptar = crypt($_POST["usr_contraseña"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
        $usr = Modelo::mdlMostrarUsuariosByCorreo(trim($_POST['usr_correo']));
        if ($usr && $encriptar == $usr["usr_contraseña"]) {
            if ($usr["usr_estado_borrado"] == 1) {
                $_SESSION["session"] = true;
                $_SESSION['session_usr'] = $usr;
                return array('status' => true, 'mensaje' => 'Bienvenido al sistema de actas.');
            } else {
                return array('status' => false, 'mensaje' => 'Tu usuario fue desactivado por los administradores.');
            }
        } else {
            return array('status' => false, 'mensaje' => 'Correo o contraseña incorrectos. Intente de nuevo.');
        }
    }

    public static function limpiar()
    {
        $actas = Modelo::mdlMostrarActasAll();
        $contador_ar = 0;
        foreach ($actas as $key => $ar) {
            $archivo = basename($ar['ar_ruta']);
            $imagen = DOCUMENT_ROOT . "actas_realizadas/" . $archivo;

            // Verificar si el archivo existe antes de intentar eliminarlo
            if (file_exists($imagen)) {
                unlink($imagen);
            }
            $res = Modelo::mdlEliminarActa($ar['ar_id']);
            $contador_ar++;
        }
        $carpeta = DOCUMENT_ROOT . "temp_file";
        $archivos = scandir($carpeta);
        // Elimina cada archivo
        $contador_at = 0;
        foreach ($archivos as $archivo) {
            $rutaCompleta = $carpeta . '/' . $archivo;
            if (is_file($rutaCompleta)) {
                unlink($rutaCompleta);
                $contador_at++;
            }
        }

        return array('mensaje' => 'Se eliminaron ' . $contador_ar . ' actas realizadas. Y ' . $contador_at . ' archivos temporales.');
    }

    public static function mostrarServicios()
    {
        $dt_servicios = array();
        $servicios = Modelo::mdlMostrarServicios();
        foreach ($servicios as $key => $srv) {
            $dt_aux = array(
                'srv_id' => $srv['srv_id'],
                'srv_nombre' => $srv['srv_nombre'],
                'srv_acciones' => '
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="btn btn-warning btnEditarServicio" srv_id="' . $srv['srv_id'] . '" srv_nombre="' . $srv['srv_nombre'] . '"><i class="fas fa-edit"></i></a>
                    <button type="button" class="btn btn-danger btnEliminarServicio" srv_id="' . $srv['srv_id'] . '"><i class="fa fa-trash-alt"></i></button>
                </div>
                ',
            );

            array_push($dt_servicios, $dt_aux);
        }

        return $dt_servicios;
    }
    public static function guardarServicios()
    {
        $_POST['srv_nombre'] = trim(strtoupper($_POST['srv_nombre']));
        if ($_POST['srv_id'] == "") {
            $srv = Modelo::mdlMostrarServiciosByNombre($_POST['srv_nombre']);
            if ($srv) {
                return array(
                    'status' => false,
                    'mensaje' => 'El servicio con nombre ' . $_POST['srv_nombre'] . ' ya existe.',
                );
            }
            $res = Modelo::mdlAgregarServicio($_POST);
            $paquetes = Modelo::mdlMostrarPaquetes();
            foreach ($paquetes as $key => $pqt) {
                $datos = array(
                    'prc_id_srv' => $res,
                    'prc_id_pqt' => $pqt['pqt_id'],
                    'prc_precio' => 0,
                );
                Modelo::mdlAgregarPrecios($datos);
            }
        } else {
            $res = Modelo::mdlActualizarServicio($_POST);
        }
        if ($res) {
            return array('status' => true, 'mensaje' => 'El servicio se guardo correctamente.');
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al guardar el servicio.');
        }
    }
    public static function eliminarServicios()
    {
        $precio = Modelo::mdlEliminarPreciosByServicio($_POST['srv_id']);
        $res = Modelo::mdlEliminarServicio($_POST['srv_id']);
        if ($res) {
            return array('status' => true, 'mensaje' => 'El servicio se elimino correctamente.');
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al eliminar el servicio.');
        }
    }

    public static function mostrarServicios2()
    {
        $dt_servicios = array();
        $servicios = Modelo::mdlMostrarServicios();
        foreach ($servicios as $key => $srv) {
            $dt_aux = array(
                'srv_id' => $srv['srv_id'],
                'srv_nombre' => $srv['srv_nombre'],
                'srv_precio' => '<input type="text" class="form-control inputN" name="precios[' . $srv['srv_id'] . ']" id="" value="0" required>',
            );

            array_push($dt_servicios, $dt_aux);
        }

        return $dt_servicios;
    }
    public static function guardarPaquetes()
    {
        $_POST['pqt_nombre'] = trim(strtoupper($_POST['pqt_nombre']));
        $pqt = Modelo::mdlMostrarPaquetesByNombre($_POST['pqt_nombre']);
        if ($pqt) {
            return array(
                'status' => false,
                'mensaje' => 'El paquete con nombre ' . $_POST['pqt_nombre'] . ' ya existe.',
            );
        }

        $pqt_id = Modelo::mdlAgregarPaquete($_POST);
        $precios = $_POST['precios'];
        foreach ($precios as $srv_id => $precio) {
            $datos = array(
                'prc_id_srv' => $srv_id,
                'prc_id_pqt' => $pqt_id,
                'prc_precio' => dnum($precio),
            );
            Modelo::mdlAgregarPrecios($datos);
        }
        return array('status' => true, 'mensaje' => 'El paquete se guardo correctamente.', 'pqt_id' => $pqt_id);
    }
    public static function mostrarPrecios()
    {
        $dt_precios = array();
        $precios = Modelo::mdlMostrarPreciosByPaquete($_POST['pqt_id']);
        foreach ($precios as $key => $prc) {
            $dt_aux = array(
                'prc_id' => $prc['prc_id'],
                'srv_nombre' => $prc['srv_nombre'],
                'prc_precio' => '<input type="text" class="form-control inputN inputPqt" name="precios[' . $prc['prc_id'] . ']" id="" value="' . $prc['prc_precio'] . '" readonly>',
            );

            array_push($dt_precios, $dt_aux);
        }

        return $dt_precios;
    }
    public static function actualizarPrecios()
    {
        // $_POST['pqt_nombre'] = trim(strtoupper($_POST['pqt_nombre']));
        // $pqt = Modelo::mdlMostrarPaquetesByNombre($_POST['pqt_nombre']);
        // if ($pqt) {
        //     return array(
        //         'status' => false,
        //         'mensaje' => 'El paquete con nombre ' . $_POST['pqt_nombre'] . ' ya existe.',
        //     );
        // }

        // $pqt_id = Modelo::mdlAgregarPaquete($_POST);
        $precios = $_POST['precios'];
        foreach ($precios as $prc_id => $precio) {
            $datos = array(
                'prc_id' => $prc_id,
                'prc_id_pqt' => $_POST['pqt_id'],
                'prc_precio' => dnum($precio),
            );
            Modelo::mdlActualizarPrecios($datos);
        }
        return array('status' => true, 'mensaje' => 'Los datos se actualizaron correctamente.');
    }
    public static function mostrarPaquetes()
    {
        $paquetes = Modelo::mdlMostrarPaquetes();
        return $paquetes;
    }
    public static function eliminarPaquete()
    {
        $precio = Modelo::mdlEliminarPreciosByPaquete($_POST['pqt_id']);
        if ($precio) {
            $res = Modelo::mdlEliminarPaquete($_POST['pqt_id']);
            if ($res) {
                return array('status' => true, 'mensaje' => 'El paquete se elimino correctamente.');
            } else {
                return array('status' => false, 'mensaje' => 'Hubo un error al eliminar el paquete.');
            }
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al eliminar el paquete.');
        }
    }

    public static function guardarCliente()
    {
        if ($_POST['clt_tipo_corte'] == "W") {
            $array_tipo = array('tipo' => "W", 'valor' => $_POST['datos_corte_wpp']);
        } else if ($_POST['clt_tipo_corte'] == "G") {
            $array_tipo = array('tipo' => "G", 'valor' => $_POST['datos_corte_gpo']);
        }

        $_POST['clt_tipo_corte'] = json_encode($array_tipo, true);
        $_POST['clt_nombre'] = strtoupper($_POST['clt_nombre']);
        $mensaje = "";
        if ($_POST['clt_id'] == "") {
            $grupo = Modelo::mdlMostrarClienteByGrupo($_POST['clt_gpo_wpp']);
            $tipo = Modelo::mdlMostrarClienteByTipo($_POST['clt_tipo_corte']);
            if ($grupo) {
                return array('status' => false, 'mensaje' => 'Ya hay un cliente registrado con ese grupo. Intente con uno nuevo');
            } else if ($tipo) {
                return array('status' => false, 'mensaje' => 'Ya hay un cliente registrado con esa información de corte. Intente con uno nuevo');
            }
            $res = Modelo::mdlGuardarClientes($_POST);
            $mensaje = 'El cliente se guardo correctamente.';
        } else {
            $res = Modelo::mdlAcutualizarClientes($_POST);
            $mensaje = 'El cliente se actualizo correctamente.';
        }
        if ($res) {
            return array('status' => true, 'mensaje' => $mensaje);
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al guardar el cliente.');
        }
    }
    public static function mostrarClientes()
    {
        $dt_clientes = array();
        $clientes = Modelo::mdlMostrarClientes();
        foreach ($clientes as $key => $clt) {
            $disabled = ($clt['clt_estado_enviado'] == 1) ? "disabled" : "";
            $dt_aux = array(
                'inputs' => '<div class="form-check">
                <input ' . $disabled . ' class="form-check-input contadorClt" type="checkbox" name="cltSelect[]" value="' . $clt['clt_id'] . '" id="' . $clt['clt_id'] . ' readonly">
                <label class="form-check-label" for="' . $clt['clt_id'] . '">
                  Seleccionar
                </label>
              </div>',
                'clt_id' => $clt['clt_id'],
                'clt_nombre' => $clt['clt_nombre'],
                'clt_gpo_wpp' => $clt['clt_nombre_gpo'],
                'clt_estado_enviado' => $clt['clt_estado_enviado'] == 1 ? '<span class="text-success"><i class="fa fa-check"></i> Enviado</span>' : '<span class="text-dark"><i class="fa fa-clock"></i> Pendiente</span>',
                'srv_acciones' => '
                <div class="btn-group" role="group" aria-label="">
                    <button type="button" class="btn btn-warning btnEditarCliente" clt_id="' . $clt['clt_id'] . '"><i class="fas fa-edit"></i></a>
                    <button type="button" class="btn btn-danger btnEliminarCliente" clt_id="' . $clt['clt_id'] . '"><i class="fa fa-trash-alt"></i></button>
                    <button type="button" class="btn btn-success btnGenerarCorte btn-load" clt_id="' . $clt['clt_id'] . '"><i class="fab fa-whatsapp"></i></button>
                </div>
                ',
            );

            // <a type="button" class="btn btn-light" href="' . HTTP_HOST . 'cortes.php?cliente=' . $clt['clt_id'] . '"><i class="fa fa-cash-register"></i></a>


            array_push($dt_clientes, $dt_aux);
        }

        return $dt_clientes;
    }
    public static function mostrarClientesById()
    {
        $clt = Modelo::mdlMostrarClienteById($_POST['clt_id']);
        return $clt;
    }
    public static function eliminarClientes()
    {
        $res = Modelo::mdlEliminarCliente($_POST['clt_id']);
        if ($res) {
            return array('status' => true, 'mensaje' => 'El cliente se elimino correctamente.');
        } else {
            return array('status' => false, 'mensaje' => 'Hubo un error al eliminar el cliente.');
        }
    }
    public static function generarCorte()
    {
        $clt = Modelo::mdlMostrarClienteById($_POST['clt_id']);
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
                $sum_total = 0;
                $saldo = 0;
                $mensaje_saldo = "";
                foreach ($reversedArray as $key => $msg) {
                    // $opciones = array('ACTAS', 'RFC', 'CFE', 'NSS');
                    $informacionPedido = Controlador::extraerInformacionPedido($msg['body'], $servicios);
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
                }

                $sum_total += $precio_total_actas + $precio_total_rfc + $precio_total_cfe + $precio_total_nss + $precio_total_curp + $precio_total_susret + $precio_total_edoinfo;
                $referencia = generarCodigoNumeros(6);

                //                 $messageBody = "
                // Total ACTAS: $totalActas = $$precio_total_actas
                // Total RFC: $totalRfc = $$precio_total_rfc
                // Total CFE: $totalCfe = $$precio_total_cfe
                // Total NSS: $totalNss = $$precio_total_nss 

                // Total: $$sum_total

                // Datos bancarios:
                // Banco: BBVA
                // Cuenta: 3263 7876 6723 7890
                // Nombre: Daniel...
                // Referencia: $referencia
                // ";

                //                 // Número de teléfono de destino
                //                 $destinyNumber = '+52' . $clt['clt_wpp'];

                //                 // Redirige al usuario a WhatsApp con el mensaje y el enlace
                //                 $whatsappLink = "https://wa.me/$destinyNumber?text=" . rawurlencode($messageBody);

                // Primero, envías el primer mensaje
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

$mensaje_saldo

Total: $$sum_total

Apóyenos en generar su pago antes de terminar el día. ¡Muchas gracias! 🎇"
                );
                if ($totalActas == 0 && $totalRfc == 0 && $totalCfe == 0 && $totalNss == 0 && $totalCurp == 0 && $totalSusRet == 0 && $totalEdoInfo == 0) {
                    return array('status' => true, 'mensaje' => 'No se envio mensaje a ' . $clt['clt_nombre'] . ' ya que no hay nada en conteo');
                }
                $response1 = Controlador::enviarMensaje($mensaje1);

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
                    $response2 = Controlador::enviarMensaje($mensaje2);

                    if ($response2['status']) {
                        return array('status' => true, 'mensaje' => 'Los mensajes se enviaron correctamente a ' . $clt['clt_nombre']);
                    } else {
                        return array('status' => false, 'mensaje' => 'Hubo un error al enviar el segundo mensaje');
                    }
                } else {
                    return array('status' => false, 'mensaje' => 'Hubo un error al enviar el primer mensaje');
                }
            }
        }
    }

    public static function extraerInformacionPedido($texto, $opciones)
    {
        // Buscar la palabra "Pedido" y extraer información
        if (strpos($texto, 'Conteo') !== false || strpos($texto, 'CONTEO') !== false || strpos($texto, 'conteo') !== false) {
            $informacion = array();

            foreach ($opciones as $opcion) {
                // Para cada opción, buscar la correspondiente línea en el texto
                preg_match("#{$opcion['srv_nombre']}:(.*)#i", $texto, $coincidencia);

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

    public static function enviarMensaje($mensaje)
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

    public static function obtenerGruposByNombre($clt_gpo_wpp)
    {
        $params = array(
            'token' => WA_TOKEN
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => WA_API_URL . "groups?" . http_build_query($params),
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
            echo "cURL Error #:" . $err;
        } else {
            $grupos = json_decode($response, true);
            foreach ($grupos as $key => $gpo) {
                if ($clt_gpo_wpp === $gpo['id']) {
                    return $gpo['name'];
                } else {
                    continue;
                }
            }
        }
    }

    public static function restablecerCortesClientes()
    {
        $clientes = Modelo::mdlMostrarClientesCortesEnviados();
        $contador = 0;
        foreach ($clientes as $key => $clt) {
            $res = Modelo::mdlActualizarEstadoEnvioCliente(0, $clt['clt_id']);
            if ($res) {
                $contador++;
            }
        }
        return array('status' => true, 'mensaje' => 'Se restablecieron ' . $contador . ' clientes');
    }
}



if (isset($_POST['cargarArchivo'])) {
    $cargarArvhivo = new Controlador();
    echo json_encode($cargarArvhivo->cargarArchivo(), true);
}
if (isset($_POST['btnGenerarActa'])) {
    $generarActa = new Controlador();
    echo $generarActa->generarActa();
}
if (isset($_POST['btnGuardarReversos'])) {
    $guardarReversos = new Controlador();
    echo json_encode($guardarReversos->guardarReversos(), true);
}
if (isset($_POST['btnEliminarActa'])) {
    $eliminarActa = new Controlador();
    echo json_encode($eliminarActa->eliminarActa(), true);
}
if (isset($_POST['btnEliminarReverso'])) {
    $eliminarReverso = new Controlador();
    echo json_encode($eliminarReverso->eliminarReverso(), true);
}
if (isset($_POST['btnMostrarActas'])) {
    $mostrarActas = new Controlador();
    echo json_encode($mostrarActas->mostrarActas(), true);
}
if (isset($_POST['btnMostrarReversos'])) {
    $mostrarReversos = new Controlador();
    echo json_encode($mostrarReversos->mostrarReversos(), true);
}
if (isset($_POST['btnMostrarUsuarios'])) {
    $mostrarUsuarios = new Controlador();
    echo json_encode($mostrarUsuarios->mostrarUsuarios(), true);
}
if (isset($_POST['btnGuardarUsuario'])) {
    $guardarUsuarios = new Controlador();
    echo json_encode($guardarUsuarios->guardarUsuarios(), true);
}
if (isset($_POST['btnEliminarUsuario'])) {
    $eliminarUsuarios = new Controlador();
    echo json_encode($eliminarUsuarios->EliminarUsuarios(), true);
}
if (isset($_POST['btnIniciarSesion'])) {
    $iniciarSesion = new Controlador();
    echo json_encode($iniciarSesion->iniciarSesion(), true);
}
if (isset($_POST['btnCerrarSesion'])) {
    session_destroy();
    echo json_encode(array('status' => true));
    exit();
}
if (isset($_POST['btnMostrarServicios'])) {
    $mostrarServicios = new Controlador();
    echo json_encode($mostrarServicios->mostrarServicios(), true);
}
if (isset($_POST['btnGuardarServicio'])) {
    $guardarServicio = new Controlador();
    echo json_encode($guardarServicio->guardarServicios(), true);
}
if (isset($_POST['btnMostrarServicios2'])) {
    $mostrarServicios2 = new Controlador();
    echo json_encode($mostrarServicios2->mostrarServicios2(), true);
}
if (isset($_POST['btnGuardarPaquetes'])) {
    $guardarPaquete = new Controlador();
    echo json_encode($guardarPaquete->guardarPaquetes(), true);
}
if (isset($_POST['btnActualizarPrecios'])) {
    $actualizarPrecios = new Controlador();
    echo json_encode($actualizarPrecios->actualizarPrecios(), true);
}
if (isset($_POST['btnMostrarPrecios'])) {
    $mostrarPrecios = new Controlador();
    echo json_encode($mostrarPrecios->mostrarPrecios(), true);
}
if (isset($_POST['btnMostrarPaquetes'])) {
    $mostrarPaquetes = new Controlador();
    echo json_encode($mostrarPaquetes->mostrarPaquetes(), true);
}
if (isset($_POST['btnEliminarPaquete'])) {
    $eliminarPaquete = new Controlador();
    echo json_encode($eliminarPaquete->eliminarPaquete(), true);
}
if (isset($_POST['btnEliminarServicio'])) {
    $eliminarServicio = new Controlador();
    echo json_encode($eliminarServicio->eliminarServicios(), true);
}
if (isset($_POST['btnGuardarCliente'])) {
    $guardarCliente = new Controlador();
    echo json_encode($guardarCliente->guardarCliente(), true);
}
if (isset($_POST['btnMostrarClientes'])) {
    $mostrarClientes = new Controlador();
    echo json_encode($mostrarClientes->mostrarClientes(), true);
}
if (isset($_POST['btnMostrarClienteById'])) {
    $mostrarClienteById = new Controlador();
    echo json_encode($mostrarClienteById->mostrarClientesById(), true);
}
if (isset($_POST['btnEliminarCliente'])) {
    $eliminarCliente = new Controlador();
    echo json_encode($eliminarCliente->eliminarClientes(), true);
}
if (isset($_POST['btnGenerarCorte'])) {
    $generarCorte = new Controlador();
    echo json_encode($generarCorte->generarCorte(), true);
}
if (isset($_POST['btnResetarCortes'])) {
    $restablecerCortes = new Controlador();
    echo json_encode($restablecerCortes->restablecerCortesClientes(), true);
}



// // Ejemplo de uso
// $resultado = cargarArchivo();
// print_r($resultado);

// $rutaDelArchivo = $resultado['ruta'];
// $resultado = eliminarArchivo($rutaDelArchivo);
// print_r($resultado);
