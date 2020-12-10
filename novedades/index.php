<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='2';
ExisteModRol($Modulo);
define("TIPO_PER", [
    ''  => '',
    'Mensuales'  => 2,
    'Jornales' => 1,
    ]);
require pagina('novedades.php');
