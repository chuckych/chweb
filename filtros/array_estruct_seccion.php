<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', '0');
// session_start();
require __DIR__ . '../../config/index.php';

$url   = host() . "/" . HOMEHOST . "/data/GetEstructuraSeccion.php?tk=" . token() . "&_c=" . $_GET['_c'] . "&_r=" . $_GET['_r']."&e=" . $_GET['e']."&act&q=".$_GET['q'];
// echo $url;

$json  = file_get_contents($url);
$array = json_decode($json, true);
$datos = $array[0][$_GET['e']];

if(isset($_GET['q'])){
    $q = $_GET['q'];
foreach ($datos as $key => $value) {
    $data[]= array(
        'id'   => $value['cod'],
        'text' => $value['desc'],
    );
}
}

echo json_encode($data);