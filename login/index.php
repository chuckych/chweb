<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../config/index.php';
$Modulo = '999';
$_datos = 'login_logs';
$bgcolor = 'bg-custom';
$_SERVER['HTTP_HOST'] ??= '';
require pagina('login.php');
access_log('Login');
