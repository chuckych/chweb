<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/index.php';
E_ALL();
UnsetGet('q');
UnsetGet('Emp');
session_start();
$data = [];

$token = sha1($_SESSION['RECID_CLIENTE']);
$pathApiCH = $_SESSION['HOST_CHWEB'] . "/" . HOMEHOST . "/api";

$q = $_GET['q'] ?? '';
$estruct = $_GET['Estruct'] ?? '';
$Emp = $_GET['Emp'] ?? '';

$Emp = ($_SESSION['EmprRol']) ? explode(',', $_SESSION['EmprRol']) : '';
$empresas = [];
$data = [];

if ($Emp) {
    foreach ($Emp as $key => $value) {
        array_push($empresas, 'Codi[]=' . $value);
    }
    $empresas = '&' . (implode('&', $empresas));
}

$emp = $empresas ?: '';

$payload = [];

$sendApi['DATA'] ??= '';
$sendApi['MESSAGE'] ??= '';
$url = "$pathApiCH/estruct/?Des=$q&Estruct=$estruct$emp";
$sendApi = curlAPI($url, $payload, 'GET', $token);
$sendApi = json_decode($sendApi, true);


if ($sendApi['MESSAGE'] == 'OK') {
    if ($sendApi['DATA']) {
        foreach ($sendApi['DATA'] as $key => $fila) {
            $data[] = [
                "id" => $fila['Codi'],
                "text" => $fila['Desc'],
            ];
        }
    }
}
Flight::json(($data));
exit;