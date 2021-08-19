<?php
session_start();
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

FusNuloPOST('ruta', '');
$RutaFiles = test_input($_POST['ruta']);
// PrintRespuestaJson('ok', 'Archivo '.$RutaFiles);
// exit;
$files = glob($RutaFiles); //obtenemos el nombre de todos los ficheros
foreach ($files as $file) {
    if (is_file($file)){
        /** borra arcchivos con diferencia de horas mayor a 1 */
        unlink($file); //elimino el fichero
        PrintRespuestaJson('ok', 'Archivo '.$file.' borrado');
    }else {
        PrintRespuestaJson('ok', 'Archivo '.$file.' no existe');
    }
}
