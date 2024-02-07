<?php
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo = '2';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexión a MSSQL redirigimos al inicio
define("TIPO_PER", [
    'Todos' => '',
    'Mensuales' => 2,
    'Jornales' => 1,
]);
require pagina('novedades.php');
