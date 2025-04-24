<?php
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
secure_auth_ch();
$Modulo = '8';
$bgcolor = 'bg-custom';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
require pagina('dashboard.php');