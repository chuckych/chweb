<?php
session_start();
require __DIR__ . '/../config/index.php';
secure_auth_ch();
$Modulo = '10';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$getData = 'GetPersonal';
$_datos = 'personal';
$bgcolor = 'bg-custom';
require pagina('personal.php');