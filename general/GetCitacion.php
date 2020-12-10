<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();
require __DIR__ . '../../config/index.php';
$Datos = explode('-', $_GET['Datos']);
$Fecha  = test_input($Datos[1]);
$Legajo = test_input($Datos[0]);
$data = array();
require __DIR__ . '../../config/conect_mssql.php';
$param   = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
/** CITACION */
$queryCit="SELECT CITACION.CitLega AS 'CitLega',CITACION.CitFech AS 'CitFech',CITACION.CitTurn AS 'CitTurn',CITACION.CitEntra 'CitEntra',CITACION.CitSale AS 'CitSale',CITACION.CitDesc AS 'CitDesc',CITACION.FechaHora AS 'FechaHora' FROM CITACION WHERE CITACION.CitLega = '$Legajo' and CITACION.CitFech = '$Fecha' and CITACION.CitTurn = 1";
$result = sqlsrv_query($link, $queryCit, $param, $options);
// print_r($queryCit); exit;
if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :
        $CitEntra = $row['CitEntra'];
        $CitSale  = $row['CitSale'];
        $CitDesc  = $row['CitDesc'];
    endwhile;
    $data = array(
        'CitEntra' => $CitEntra,
        'CitSale'  => $CitSale,
        'CitDesc'  => $CitDesc,
    );
    sqlsrv_free_stmt($result);
}else{
    $data = array(
        'CitEntra' => 0,
        'CitSale'  => 0,
        'CitDesc'  => 0,
    );
}
echo json_encode($data);
sqlsrv_close($link);
exit;