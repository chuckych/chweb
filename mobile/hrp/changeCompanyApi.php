<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$recid = $_POST['recid'];

$sql = "SELECT clientes.id as 'id', clientes.recid 'recid' FROM clientes WHERE clientes.recid = '$recid'";
$data = simple_pdoQuery($sql);

$_SESSION['ID_CLIENTE']    = $data['id'];
$_SESSION['RECID_CLIENTE'] = $data['recid'];

$data = array('status' => 'ok');
echo json_encode($data);
exit;