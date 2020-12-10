<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

$tk = $_POST['tk'];
$_SESSION["TK_MOBILE"] = $tk;

$data = array('status' => 'ok');
echo json_encode($data);
