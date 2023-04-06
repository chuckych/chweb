<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
$_POST['recid'] = $_POST['recid'] ?? '';
$recid = $_POST['recid'];
$data = array();

$data = getDataIni('../../mobileApikey.php');

foreach ($data as $key => $value) {
    if ($value['idCompany'] == $recid) {
        $data = $value;
    }
}

$_SESSION['ID_CLIENTE']    = $data['idCompany'];
$_SESSION['RECID_CLIENTE'] = $data['recidCompany'];
$_SESSION["APIMOBILEHRP"]  = $data['apiMobileHRP'];
$_SESSION["CLIENTE"]       = $data['nameCompany'];

do {
    $data = array(
        'status'       => 'ok',
        'api'          => $data['apiMobileHRP'],
        // 'idCompany'    => $_SESSION['ID_CLIENTE'],
        // 'recidCompany' => $_SESSION['RECID_CLIENTE'],
        'cliente'      => $_SESSION['CLIENTE'],
    );
} while ($_SESSION['ID_CLIENTE'] != $recid);

echo json_encode($data);
exit;