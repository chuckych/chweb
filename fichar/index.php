<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='11';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$bgcolor = 'bg-custom';
define("TIPO_PER", [
    'Todos'  => '0',
    'Mensuales'  => '1',
    'Jornales' => '2',
    ]);
define("LABORAL", [
    'En todos los días'  => '0',
    'Solamente en días laborales'  => '1',
    ]);
define("INGRESAR", [
    'Según Horario de Entrada'  => '0',
    'Según Horario de Salida'  => '1',
    'Según Horario de Entrada Aproximado' => '2',
    'Según Horario de Salida Aproximada' => '3',
    'Según Horario de Entrada y Salida' => '4',
    'Según Horario de Entrada y Salida Aprox.' => '5',
    ]);
require pagina('fichar.php');