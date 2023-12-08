<?php ob_start();
include_once './config.php';
require_once 'controlador.php';

iniciarApp();

ob_end_flush();
?>
