<?php
session_start();
require __DIR__ . '../../config/index.php';
$Modulo  = '999';
$_datos  = 'login_logs';
$bgcolor = 'bg-custom';
require pagina('login.php');
access_log('Login');
