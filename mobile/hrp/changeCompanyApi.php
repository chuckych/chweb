<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
$recid = $_POST['recid'];

$sql = "SELECT clientes.id as 'id', clientes.recid as 'recid', clientes.ApiMobileHRP as 'ApiMobileHRP' FROM clientes WHERE clientes.id = '$recid'";
$data = simple_pdoQuery($sql);

$_SESSION['ID_CLIENTE']    = $data['id'];
$_SESSION['RECID_CLIENTE'] = $data['recid'];
$_SESSION["APIMOBILEHRP"]  = $data['ApiMobileHRP'];

$data = array('status' => 'ok', 'api' => $data['ApiMobileHRP']);
echo json_encode($data);
exit;