<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='19';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$FirstDate = date("Y-m-d", strtotime(hoy() . "- 1 year"));
$FirstYear = date("Y", strtotime(hoy() . "- 1 year"));
$maxDate   = date("Y-m-d", strtotime(hoy() . "+ 1 year"));;
$maxYear   = date("Y", strtotime(hoy() . "+ 1 year"));
$FechaIni  = date("Y-m-d", strtotime(hoy() . "- 29 days"));
$FechaFin  = date("Y-m-d", strtotime(hoy()));
require pagina('asignados.php');