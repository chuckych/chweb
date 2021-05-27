<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

$tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');

$url = "https://server.xenio.uy/persons.php?TYPE=LIST_PERSON_BY_ID&tk=" . $tkcliente . "&id=" . $_POST['id'];
// echo $url; exit;
$json = file_get_contents($url);
$array = json_decode($json, TRUE);
// $array = json_decode(getRemoteFile($url), true);

echo json_encode($array['MESSAGE']);

