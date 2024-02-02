<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo = '2';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
define("TIPO_PER", [
    'Todos' => '',
    'Mensuales' => 2,
    'Jornales' => 1,
]);
require pagina('novedades.php');
