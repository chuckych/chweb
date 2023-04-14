<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
E_ALL();
UnsetGet('q');
UnsetGet('Emp');
session_start();
$data = array();
// $_SESSION['RECID_CLIENTE'] = 'aNGL89kv';
$token = sha1($_SESSION['RECID_CLIENTE']);
$pathApiCH = gethostCHWeb()."/".HOMEHOST."/api";

$q = $_GET['q'];
$estruct  = $_GET['Estruct'];
$Emp  = $_GET['Emp'];

$Emp = ($_SESSION['EmprRol']) ? explode(',', $_SESSION['EmprRol']) : '';
$empresas = array();
$data = array();

if ($Emp) {
    foreach ($Emp as $key => $value) {
        array_push($empresas, 'Codi[]='. $value);
    }
    $empresas = '&'.(implode('&',$empresas));
}

$emp = ($empresas) ? $empresas : '';

$payload = array();

$sendApi['DATA'] = $sendApi['DATA'] ?? '';
$sendApi['MESSAGE'] = $sendApi['MESSAGE'] ?? '';
$url = "$pathApiCH/estruct/?Des=$q&Estruct=$estruct$emp";

// print_r($empresas).exit;

$sendApi = curlAPI($url, $payload, 'GET', $token);
$sendApi = json_decode($sendApi, true);


if ($sendApi['MESSAGE'] == 'OK') {
    if ($sendApi['DATA']) {
        foreach ($sendApi['DATA'] as $key => $fila) {
            $data[] = array(
                "id"  => $fila['Codi'],
                "text"  => $fila['Desc'],
            );
        }
    }
}
Flight::json(($data));
exit;
