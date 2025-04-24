<?php
require __DIR__ . '/../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

$query = "SELECT MIN(fecha) as 'start_date', MAX(fecha) as 'end_date' FROM auditoria";
$fechas = simple_pdoQuery($query);
$json_data = array(
    "start_date" => ($fechas['start_date']) ? $fechas['start_date'] : date('Y-m-d'),
    "end_date" => ($fechas['end_date']) ? $fechas['end_date'] : date('Y-m-d')
);
echo json_encode($json_data);