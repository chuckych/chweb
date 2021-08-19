<?php
session_start();
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

FusNuloPOST('ruta', '');
$RutaFiles = test_input($_POST['ruta']);

$files = glob($RutaFiles);
foreach ($files as $file) {
    if (is_file($file)){
        unlink($file); //elimino el fichero
        PrintRespuestaJson('ok', 'Archivo '.$file.' borrado');
    }else {
        PrintRespuestaJson('ok', 'Archivo '.$file.' no existe');
    }
}
