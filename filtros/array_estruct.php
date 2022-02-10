<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
// session_start();
require __DIR__ . '../../config/index.php';
E_ALL();
$data = array();
$url   = host() . "/" . HOMEHOST . "/data/GetEstructura.php?tk=" . token() . "&_c=" . $_GET['_c'] . "&_r=" . $_GET['_r'] . "&e=" . $_GET['e'] . "&act&q=" . $_GET['q'];
// echo $url; exit;
// $json  = file_get_contents($url);
// $array = json_decode($json, true);
$array = json_decode(getRemoteFile($url), true);
// $array = (getRemoteFile($url));
$datos = $array[0][$_GET['e']];

// print_r($array); exit;

if (isset($_GET['q'])) {
    $q = $_GET['q'];
    foreach ($datos as $key => $value) {
        $data[] = array(
            'id'   => $value['cod'],
            'text' => $value['desc'],
        );
    }
}

echo json_encode($data);
