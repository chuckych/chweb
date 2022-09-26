<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params    = $_REQUEST;
$data      = array();
$authBasic = base64_encode('chweb:'.HOMEHOST);
$token     = sha1($_SESSION['RECID_CLIENTE']);

$params['start']    = $params['start'] ?? 0;
$params['length']   = $params['length'] ?? 9999;
$params['legajo']   = $params['legajo'] ?? '';
$params['fechaIni'] = $params['fechaIni'] ?? date('Y-m-d');
$params['fechaFin'] = $params['fechaFin'] ?? date('Y-m-d');

// (!$params['length']) ? exit : '';
(!$params['legajo']) ? exit : '';

$dataParametros = array(
    'Lega'    => array($params['legajo']),
    'FechIni' => FechaFormatVar($params['fechaIni'], 'Y-m-d'),
    'FechFin' => FechaFormatVar($params['fechaFin'], 'Y-m-d'),
    'start'   => intval($params['start']),
    'length'  => intval($params['length']),
    'getReg'  => 1,
    'getNov'  => 1,
    'getONov' => 1,
    'getHor'  => 1,
    'onlyReg' => 1
);
$u = (explode('/',$_SERVER['PHP_SELF']));

$url = "$_SERVER[HTTP_ORIGIN]/".HOMEHOST."/api/ficnovhor/";

$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';

$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);

$json_data = array(
    "draw"            => intval($params['draw'] ?? 0),
    "recordsTotal"    => intval($dataApi['TOTAL'] ?? 0),
    "recordsFiltered" => intval($dataApi['TOTAL'] ?? 0),
    "data"            => $dataApi['DATA'],
    "dataParametros"  => $dataParametros,
    "Mensaje" => $dataApi['MESSAGE'] 
);

echo json_encode($json_data);
exit;