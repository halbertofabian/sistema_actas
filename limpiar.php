<?php
require_once 'controlador.php';

$res = Controlador::limpiar();
echo $res['mensaje'];
