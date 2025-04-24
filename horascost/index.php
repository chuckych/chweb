<?php
session_start();
require __DIR__ . '/../config/index.php';
secure_auth_ch();
$Modulo = '28';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$getData = 'GetHoras';
$_datos = 'horas';
$bgcolor = 'bg-custom';
$CookieAgrupar = (isset($_COOKIE['AgrupaHorasCost'])) ? $_COOKIE['AgrupaHorasCost'] : 'Tarea';
$TextCookieAgrupar = (isset($_COOKIE['AgrupaHorasCost'])) ? $_COOKIE['AgrupaHorasCost'] : 'Tarea';
define("TIPO_PER", [
    'Todos' => '',
    'Mensuales' => '2',
    'Jornales' => '1',
]);
define("AGRUPAR", [
    $TextCookieAgrupar => $CookieAgrupar,
    'Tarea' => 'Tarea',
    'Planta' => 'Planta',
    'Grupo' => 'Grupo',
    'Sucursal' => 'Sucursal',
    'Sector' => 'Sector',
    // 'Sección'  => 'Sección',
]);
define("SHORAS", [
    'Hechas' => '1',
    'Autorizadas' => '2',
]);
$FechaIni2 = date("Y-m-d", strtotime(hoy() . "- 1 month"));
$FechaFin2 = date("Y-m-d", strtotime(hoy() . "- 0 days"));
require pagina('horascost.php');
