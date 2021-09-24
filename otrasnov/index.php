<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='17';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
require pagina('otrasnov.php');
