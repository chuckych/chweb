<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo = '3';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
// $FechaIni2 = date("Y-m-d", strtotime(hoy() . "- 1 month"));
// $FechaFin2 = date("Y-m-d", strtotime(hoy() . "- 0 days"));
require pagina('fichadas.php');
