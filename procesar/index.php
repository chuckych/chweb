<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='12';
ExisteModRol($Modulo);
// $getData = 'GetFichadas';
// $_datos  = 'fichadas';
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
require pagina('procesar.php');