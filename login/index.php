<?php
session_start();
require __DIR__ . '/../config/index.php';
$Modulo = '999';
$_datos = 'login_logs';
$bgcolor = 'bg-custom';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? '';
if ($_SERVER['HTTP_HOST']) {
    if ($_SERVER['HTTP_HOST'] == '181.104.117.38') {
        header('Location: http://cloudhr.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == '181.104.117.38:8050') {
        header('Location: https://cloudhr.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == 'chweb.ar:8050') {
        header('Location: https://cloudhr.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == 'hrconsulting.ddns.net') {
        header('Location: https://cloudhr.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == 'hrconsulting.ddns.net:8050') {
        header('Location: https://cloudhr.ar/chweb/index.php');
        exit;
    }
}
require pagina('login.php');
access_log('Login');
