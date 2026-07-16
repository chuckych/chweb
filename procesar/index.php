<?php
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
secure_auth_ch();
$Modulo = '12';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexión a MSSQL redirigimos al inicio
define("TIPO_PER", [
    'Todos' => '0',
    'Mensuales' => '1',
    'Jornales' => '2',
]);
require pagina('procesar_.php');
