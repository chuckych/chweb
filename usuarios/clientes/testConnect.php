<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../../config/index.php';
header("Content-Type: application/json");
E_ALL();

if (valida_campo($_GET['_c'])) {
    PrintRespuestaJson('error', 'Campo Legajo requerido');
    exit;
}
;
require __DIR__ . '../../../config/conect_mssql.php';

$stmt = sqlsrv_query($link, "SELECT @@VERSION");


if (($stmt)) {
    $info = sqlsrv_server_info($link);
    $rs = sqlsrv_fetch_array($stmt);
    $CurrentDatabase = $info['CurrentDatabase'];
    $SQLServerVersion = $info['SQLServerVersion'];
    $SQLServerName = $info['SQLServerName'];
    $InfoHTML = "<br>Database: <b>$CurrentDatabase</b> <br> Server Version: <b>$SQLServerVersion</b> <br> Server Name: <b>$SQLServerName</b> <br>";
    PrintRespuestaJson('ok', "<h6>Conexión exitosa . . .</h6>" . nl2br($rs[0] . $InfoHTML));
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($link);
    exit;
} else {
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            $mensaje = explode(']', $error['message']);
            $data[] = array("status" => "error", "Mensaje" => $mensaje[3]);
            exit;
        }
    }
    echo json_encode($data[0]);
    sqlsrv_close($link);
    exit;
}

