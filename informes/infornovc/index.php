<?php
session_start();
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '29';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$bgcolor = 'bg-custom';
$FirstYear = '2000';
$maxYear = '2100';

require __DIR__ . '/../../config/conect_mysql.php';
$cliente = $_SESSION['ID_CLIENTE'];
$_SESSION["CONCEPTO_PRESENTES"] = $_SESSION["CONCEPTO_PRESENTES"] ?? '';
$query = "SELECT valores FROM params WHERE modulo=29 and descripcion='presentes' and cliente = $cliente LIMIT 1";
$result = mysqli_query($link, $query);
$valorPresentes = mysqli_fetch_assoc($result);
$p = explode('@', $valorPresentes['valores']);
$p[0] = $p[0] ?? '';
$p[1] = $p[1] ?? '';
$p[2] = $p[2] ?? '';
$_SESSION["CONCEPTO_PRESENTES"] = $p[0];
$_SESSION["DIAS_FRANCO"] = $p[1];
$_SESSION["DIAS_FERIADOS"] = $p[2];
mysqli_free_result($result);

$_SESSION["CONCEPTO_AUSENTES"] = $_SESSION["CONCEPTO_AUSENTES"] ?? '';
$query = "SELECT valores FROM params WHERE modulo=29 and descripcion='ausentes' and cliente = $cliente LIMIT 1";
$result = mysqli_query($link, $query);
$valorAusentes = mysqli_fetch_assoc($result);
$_SESSION["CONCEPTO_AUSENTES"] = $valorAusentes['valores'];
mysqli_free_result($result);
mysqli_close($link);

require pagina('inforpresentismo.php');
