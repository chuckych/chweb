<?php
session_start();
require __DIR__ . '../../config/index.php';
$Modulo  = '999';
$_datos  = 'login_logs';
$bgcolor = 'bg-custom';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? '';
if($_SERVER['HTTP_HOST']){
    if ($_SERVER['HTTP_HOST'] == '186.137.65.152') {
        header('Location: http://chweb.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == '186.137.65.152:8050') {
        header('Location: http://chweb.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == 'chweb.ar:8050') {
        header('Location: http://chweb.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == 'hrconsulting.ddns.net') {
        header('Location: http://chweb.ar/chweb/index.php');
        exit;
    } elseif ($_SERVER['HTTP_HOST'] == 'hrconsulting.ddns.net:8050') {
        header('Location: http://chweb.ar/chweb/index.php');
        exit;
    }
}
require pagina('login.php');
access_log('Login');
