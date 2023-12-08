<?php
$lifetime = 1209600;
@session_start();
setcookie(session_name(), session_id(), time() + $lifetime);

$folder = explode("/", $_SERVER['REQUEST_URI']);
define('FOLDER', $folder[1]);

// Definiendo la ruta de la web 
define('HTTP_HOST', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . FOLDER . '/');
// Definiendo el directorio del proyecto
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/' . $folder[1] . '/');

define('DB_NAME', 'db_actas');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');



function subirArchivoReverso($inputName)
{

    // Ruta donde se guardará la imagen en el servidor
    $directorioDestino = DOCUMENT_ROOT . "reversos_file/";
    $url_file = HTTP_HOST . "reversos_file/";

    if (!file_exists($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $imagen = $_FILES[$inputName];

    // Generar un nombre único para el archivo
    $nombreArchivo = $imagen["name"];
    // Ruta completa del archivo de destino
    $rutaDestino = $directorioDestino . $nombreArchivo;
    $url_file = $url_file . $nombreArchivo;

    // Mover el archivo subido al directorio de destino
    move_uploaded_file($imagen["tmp_name"], $rutaDestino);

    return $rutaDestino;
}

function iniciarApp()
{

    include_once './principal.php';
}
