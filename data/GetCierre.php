<?php
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
secure_auth_ch_json();
E_ALL();

$Datos = (explode('-', ($_GET['Datos'])));

$FicFech = test_input($Datos[1]);
$FicLega = test_input($Datos[0]);

$FechaDeCierre = (PerCierreFech($FicFech, $FicLega));
// echo $FechaDeCierre; exit;

if ($FicFech <= $FechaDeCierre) {
    $data = array('status' => 'ok', 'dato' => Fech_Format_Var($FechaDeCierre, ('d/m/Y')));
    echo json_encode($data);
    exit;
} else {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}