<?php
require __DIR__ . '../../../config/index.php';
session_start();
ultimoacc();
secure_auth_ch_json();
E_ALL();
header("Content-Type: application/json");
$data = array();
/** Para dateRangePicker */
// $arrayFech = (fecha_min_max_mysql('reg_', 'fechaHora'));
// $query = "SELECT MIN(fechaHora) AS 'min', MAX(fechaHora) AS 'max' FROM reg_ WHERE id_company = '$_SESSION[ID_CLIENTE]'";

$idCompany = $_SESSION['ID_CLIENTE'];
$api = "api/v1/checks/dates.php?key=$_SESSION[RECID_CLIENTE]";
$url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);
$arrayFech = $api['RESPONSE_DATA'];

// $arrayFech = simple_pdoQuery($query);

$min = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'd-m-Y') : date('d-m-Y');
$minFormat = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'd/m/Y') : date('d/m/Y');
$max = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'd-m-Y') : date('d-m-Y');
$maxFormat = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'd/m/Y') : date('d/m/Y');
$aniomin = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'Y') : date('Y');
$aniomax = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'Y') : date('Y');

$data = array(
    'aniomax'   => $aniomax,
    'aniomin'   => $aniomin,
    'max'       => $max,
    'maxFormat' => $maxFormat,
    'min'       => $min,
    'minFormat' => $minFormat,
);

echo json_encode($data);
exit;