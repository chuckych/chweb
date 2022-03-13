<?php
require __DIR__ . '../../../config/index.php';
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    (response(array(), 0, 'Invalid Token', 400, 0, 0, 0));
    exit;
}
$_GET['token'] = $_GET['token'] ?? '';

if (empty($_GET['token'])) {
    echo 'Establecer Token';
    exit;
}
// $tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');
// $tkcliente = '52a8a5a1802060f8fbe343dcf657a307'; // MGV
$tkcliente = '0e3a80969b4d69c9463d276828bd9be8'; // Scania


$url = "https://app.xmartclock.com/xmart/be/xmart_end_point.php?TYPE=LIST&col=zones&tk=" . $_GET['token'];

$json = file_get_contents($url);
$array = json_decode($json, TRUE);

$data = array();
// print_r(($array));
// exit;
foreach ($array['DATA'] as $key => $v) {
    
        $paramsApi = array(
            'key'      => $_SESSION["RECID_CLIENTE"],
            'zoneName' => urlencode($v['name']),
            'zoneLng'  => urlencode($v['lng']),
            'zoneLat'  => urlencode($v['lat']),
            'zoneRadio'  => urlencode($v['map_size']),
        );
        $api = "api/v1/zones/add/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);
}
exit;