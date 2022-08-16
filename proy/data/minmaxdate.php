<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
require __DIR__ . '../../../vendor/autoload.php';

use Carbon\Carbon;

header("Content-Type: application/json");
E_ALL();
timeZone();

$data = array();
(!$_SERVER['REQUEST_METHOD'] == 'POST') ? PrintRespuestaJson('error', 'Invalid Request Method') . exit : '';
$p = $_REQUEST;

$r = "SELECT MIN($p[f1]) AS 'min', MAX($p[f1]) AS 'max' FROM $p[t] WHERE Cliente = '$_SESSION[ID_CLIENTE]'";

$a = simple_pdoQuery($r);

$min       = !empty($a['min']) ? Carbon::parse($a['min'], 'UTC')->format('Y-m-d') : date('Y-m-d');
$max       = !empty($a['max']) ? Carbon::parse($a['max'], 'UTC')->format('Y-m-d') : date('Y-m-d');
$minFormat = !empty($a['min']) ? Carbon::parse($a['min'], 'UTC')->format('d/m/Y') : date('d/m/Y');
$maxFormat = !empty($a['max']) ? Carbon::parse($a['max'], 'UTC')->format('d/m/Y') : date('d/m/Y');
$aniomin   = !empty($a['min']) ? Carbon::parse($a['min'], 'UTC')->format('Y') : date('Y');
$aniomax   = !empty($a['max']) ? Carbon::parse($a['max'], 'UTC')->format('Y') : date('Y');

$data = array(
    "anio" => array(
        "min" => $aniomin,
        "max" => $aniomax
    ),
    "fecha" => array(
        "min" => $min,
        "max" => $max,
        "minFormat" => $minFormat,
        "maxFormat" => $maxFormat
    )
);

PrintRespuestaJson('ok',$data);
exit;
