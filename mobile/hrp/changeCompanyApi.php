<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
$recid = $_POST['recid'] ?? '';
$data = [];

$data = getDataIni('../../mobileApikey.php');

foreach ($data as $key => $value) {
    if ($value['idCompany'] == $recid) {
        $data = $value;
    }
}

$_SESSION['ID_CLIENTE_MOBILE'] = $data['idCompany'];
$_SESSION['RECID_CLIENTE_MOBILE'] = $data['recidCompany'];
$_SESSION["APIMOBILEHRP_MOBILE"] = $data['apiMobileHRP'];
$_SESSION["CLIENTE_MOBILE"] = $data['nameCompany'];

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

do {
    $data = [
        'status' => 'ok',
        'api' => $data['apiMobileHRP'],
        'cliente' => $_SESSION['ID_CLIENTE_MOBILE'],
    ];
} while ($_SESSION['ID_CLIENTE_MOBILE'] != $recid);

echo json_encode($data);
exit;