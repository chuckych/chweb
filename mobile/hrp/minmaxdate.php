<?php
require __DIR__ . '../../../config/index.php';
session_start();
ultimoacc();
secure_auth_ch_json();
E_ALL();
header("Content-Type: application/json");
$data = array();
/** Para dateRangePicker */
$arrayFech = (fecha_min_max_mysql('reg_', 'fechaHora'));
$min = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'd-m-Y') : date('d-m-Y');
$max = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'd-m-Y') : date('d-m-Y');
$aniomin = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'Y') : date('Y');
$aniomax = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'Y') : date('Y');

$data = array(
    'min'     => $min,
    'max'     => $max,
    'aniomin' => $aniomin,
    'aniomax' => $aniomax
);

echo json_encode($data);
exit;