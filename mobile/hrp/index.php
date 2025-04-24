<?php
session_start();
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '32';
ExisteModRol($Modulo);
borrarLogs(__DIR__ . '/logs/', 10, '.log');
require pagina('mobile.php');