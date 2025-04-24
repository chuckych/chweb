<?php
session_start();
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '30';
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
ExisteModRol($Modulo);
$bgcolor = 'bg-custom';
require pagina('datos.php');
