<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

require __DIR__ . '../../config/index.php';

$Datos = (explode('-', ($_GET['Datos'])));

$FicFech = test_input($Datos[1]);
$FicLega = test_input($Datos[0]);

$FechaDeCierre = (PerCierreFech($FicFech,$FicLega));
// echo $FechaDeCierre; exit;

if($FicFech <= $FechaDeCierre){
    $data = array('status' => 'ok', 'dato' => Fech_Format_Var($FechaDeCierre, ('d/m/Y')));
    echo json_encode($data);
    exit;
}else{
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}