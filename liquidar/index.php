<?php
session_start();
require __DIR__ . '/../config/index.php';
secure_auth_ch();
$Modulo = '15';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$bgcolor = 'bg-custom';
define("TIPO_PER", [
    'Mensuales' => '0',
    'Jornales' => '1',
]);
define("QUINCENA", [
    'Primer' => '1',
    'Segundo' => '2',
]);
define("LABORAL", [
    'En todos los días' => '0',
    'Solamente en días laborales' => '1',
]);
define("INGRESAR", [
    'Según Horario de Entrada' => '0',
    'Según Horario de Salida' => '1',
    'Según Horario de Entrada Aproximado' => '2',
    'Según Horario de Salida Aproximada' => '3',
    'Según Horario de Entrada y Salida' => '4',
    'Según Horario de Entrada y Salida Aprox.' => '5',
]);

// $Path=str_replace("/index.php","",$_SERVER['SCRIPT_FILENAME']);
// $Path="//DESKTOP-8FK3BRD/Liquidacion";

// UpdateRegistro("UPDATE ARCHIVOS SET ARCHIVOS.ArchNomb='Liquidar.txt', ARCHIVOS.ArchPath='$Path' WHERE ARCHIVOS.ArchCodi='0' AND ARCHIVOS.ArchIO='0' AND ARCHIVOS.ArchModu='0' AND ArchTipo='0'");

require pagina('liquidar.php');
