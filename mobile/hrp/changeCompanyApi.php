<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
$recid = $_POST['recid'];

$sql = "SELECT clientes.id as 'id', clientes.recid as 'recid', clientes.ApiMobileHRP as 'ApiMobileHRP', clientes.nombre as 'cliente' FROM clientes WHERE clientes.id = '$recid'";
$data = simple_pdoQuery($sql);

$_SESSION['ID_CLIENTE']    = $data['id'];
$_SESSION['RECID_CLIENTE'] = $data['recid'];
$_SESSION["APIMOBILEHRP"]  = $data['ApiMobileHRP'];
$_SESSION["CLIENTE"]  = $data['cliente'];

usleep(500000);

$data = array(
    'status'       => 'ok',
    'api'          => $data['ApiMobileHRP'],
    'idCompany'    => $_SESSION['ID_CLIENTE'],
    'recidCompany' => $_SESSION['RECID_CLIENTE'],
    'cliente'      => $_SESSION['CLIENTE'],
);
echo json_encode($data);
exit;