<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='16';
ExisteModRol($Modulo);
$getData = 'GetHoras';
$_datos  = 'horas';
$bgcolor = 'bg-custom';
define("TIPO_PER", [
    'Todos'     => '',
    'Mensuales' => '2',
    'Jornales'  => '1',
    ]);
define("SHORAS", [
    'Hechas'     => '1',
    'Autorizadas' => '2',
    ]);
$FechaIni2 = date("Y-m-d", strtotime(hoy() . "- 1 month"));
$FechaFin2 = date("Y-m-d", strtotime(hoy() . "- 0 days"));
require pagina('horas.php');
