<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', '0');

$tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');
$id        = $_POST['id'];
$url = "https://server.xenio.uy/list.php?u_id=".$id."&tk=".$tkcliente ."&TYPE=LIST_TRAIN";
$json = file_get_contents($url);
$array = json_decode($json, TRUE);

echo json_encode(($array));
exit;
