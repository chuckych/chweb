<?php
session_start();
require __DIR__ . '/../../../config/index.php';
secure_auth_ch();
$Modulo = '46';
ExisteModRol($Modulo);
$bgcolor = 'bg-custom';
define("TIPO_PER", [
    'Mensuales' => '0',
    'Jornales' => '1',
]);
define("QUINCENA", [
    'Primer' => '1',
    'Segundo' => '2',
]);
$cliente = strtolower($_SESSION['CLIENTE']);
$cliente = ucfirst($cliente);
require pagina('prysmian.php');
