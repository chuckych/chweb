<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

$respuesta = array();
$arrayData = array();

$params = $columns = $totalRecords = '';
$params = $_REQUEST;

$params['zoneLat'] = $params['zoneLat'] ?? '';
$params['zoneLng'] = $params['zoneLng'] ?? '';
$params['start']  = $params['start'] ?? '';
$params['length'] = $params['length'] ?? '';
$params['key']    = $params['key'] ?? '';

$idCompany = $_SESSION['ID_CLIENTE'];

$paramsApi = array(
    'key'     => $_SESSION["RECID_CLIENTE"],
    'start'   => ($params['start']),
    'length'  => ($params['length']),
    'zoneLat' => ($params['zoneLat']),
    'zoneLng' => ($params['zoneLng']),
);
$parametros = '';
foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}
$api = "api/v1/zones/near/$parametros";
$url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);

$totalRecords = $api['TOTAL'];
if ($api['COUNT'] > 0) {
    foreach ($api['RESPONSE_DATA'] as $r) {
        $arrayData[] = array(
            'zoneID'    => $r['zoneID'],
            'zoneName'  => $r['zoneName'],
            'zoneRadio' => $r['zoneRadio'],
            'zoneLat'   => $r['zoneLat'],
            'zoneLng'   => $r['zoneLng'],
            'idCompany' => $r['idCompany'],
            'distance'  => round(floatval($r['zoneDistance']),2)
        );
    }
}
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $arrayData
);
echo json_encode($json_data);
