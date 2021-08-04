<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
// session_start();
require __DIR__ . '../../config/index.php';
E_ALL();
UnsetGet('q');

FusNuloGET("Emp",'');
FusNuloGET("Plan",'');
FusNuloGET("Sect",'');
FusNuloGET("Sec2",'');
FusNuloGET("Grup",'');
FusNuloGET("Sucur",'');
FusNuloGET("Tipo",'');

$estruct = '&Emp='.$_GET['Emp'];
$estruct .= '&Plan='.$_GET['Plan'];
$estruct .= '&Sect='.$_GET['Sect'];
$estruct .= '&Sec2='.$_GET['Sec2'];
$estruct .= '&Grup='.$_GET['Grup'];
$estruct .= '&Sucur='.$_GET['Sucur'];
$estruct .= '&Tipo='.$_GET['Tipo'];

$url   = host() . "/" . HOMEHOST . "/data/GetPersonal.php?tk=" . token() . "&_c=" . $_GET['_c'] . "&_r=" . $_GET['_r']."&act&q=".$_GET['q'].$estruct;
// echo $url; exit;
// $json  = file_get_contents($url);
// $array = json_decode($json, true);
$array = json_decode(getRemoteFile($url), true);
$datos = $array[0]['personal'];

// print_r($datos);exit;
if(isset($_GET['q'])){
    $q = $_GET['q'];
    $data =array();
foreach ($datos as $key => $value) {
    $data[]= array(
        'id'   => $value['pers_legajo'],
        'text' => $value['pers_nombre'],
    );
}
}

echo json_encode($data);