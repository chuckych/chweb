<?php
require __DIR__ . '/../../config/session_start.php';
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '31';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$bgcolor = 'bg-custom';
require pagina('estruct.php');