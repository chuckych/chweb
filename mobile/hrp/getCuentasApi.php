<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();
$q = test_input($_POST['q'] ?? '') ?? '';
$data = getDataIni('../../mobileApikey.php');
if (!$data)
    echo json_encode($data) . exit;
$arrayData = [];
$a = [];

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

foreach ($data as $row) {

    if ((!$row['apiMobileHRP']))
        continue;

    $html = '<div class="d-flex justify-content-start"><span class="badge badge-light w30 p-2">' . $row['idCompany'] . '</span><span class="ml-2">' . $row['nameCompany'] . '</span></div>';

    $a = [
        'id' => $row['idCompany'],
        'text' => trim($row['nameCompany']),
        'recid' => $row['recidCompany'],
        'html' => $html,
    ];

    $cadena = $row['nameCompany'] . ' ' . $row['idCompany'];
    $q ? (((stripos($cadena, $q) !== false)) ? $arrayData[] = $a : '') : $arrayData[] = $a;
    /**
     * Esta línea de código está comprobando si la cadena $cadena contiene el valor de $q. Si contiene el valor, entonces se agrega el valor $a al array $arrayData. Si no se encuentra el valor $q, entonces se agregará el valor $a al array $arrayData.
     */
}

echo json_encode($arrayData);
exit;
