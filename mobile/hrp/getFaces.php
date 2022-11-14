<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();
$respuesta = array();
$data = array();
$data2 = array();
$error = '';
$_SESSION["APIMOBILEHRP"] = $_SESSION["APIMOBILEHRP"] ?? '';

$params = $columns = $totalRecords = '';
$params = ($_REQUEST);

if (empty($params['userID'])) {
    $json_data = array(
        "draw"            => 0,
        "recordsTotal"    => 0,
        "recordsFiltered" => 0,
        "data"            => array(),
        "timeScript"      => 0,
    );
    echo json_encode($json_data);
    exit;
}

$idCompany = $_SESSION['ID_CLIENTE'];

$paramsApi = array(
    'key'    => $_SESSION["RECID_CLIENTE"],
    'start'  => 0,
    'length' => 50,
    'userID' => intval($params['userID']),
);
// checkenroll($_SESSION["RECID_CLIENTE"], intval($params['userID']), $_SESSION["APIMOBILEHRP"], 0);
$parametros = '';

foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}

$api = "api/v1/faces/$parametros";
$api2 = "api/v1/enroll/get/$parametros";

$url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$url2 = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api2;

$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);


$api2 = getRemoteFile($url2, $timeout = 10);
$api2 = json_decode($api2, true);

$totalRecords = $api['TOTAL'];
$totalRecords2 = $api2['TOTAL'];

$api['RESPONSE_DATA'] = $api['RESPONSE_DATA'] ?? array();
$api2['RESPONSE_DATA'] = $api2['RESPONSE_DATA'] ?? array();
$startScript = microtime(true);

if ($api['COUNT'] > 0) {
    $data = $api['RESPONSE_DATA'];
}
if ($api2['COUNT'] > 0) {
    $data2 = $api2['RESPONSE_DATA'];
}

$endScript = microtime(true);
$timeScript = round($endScript - $startScript, 2);

$json_data = array(
    "draw"            => 0,
    "recordsTotal"    => $api['TOTAL'],
    "recordsFiltered" => $api['COUNT'],
    "data"            => $data,
    "data2"            => $data2,
    "timeScript"      => $timeScript,
);

echo json_encode($json_data);
exit;
