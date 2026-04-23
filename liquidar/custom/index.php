<?php
session_start();
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '48';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$bgcolor = 'bg-custom';
// error_log(print_r($_SESSION, true));
require pagina('liquidar_custom.php');
