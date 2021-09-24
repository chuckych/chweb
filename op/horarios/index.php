<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='33';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$checkModulo = CountRegMayorCeroMySql("SELECT * FROM modulos where id = 33 AND estado = '0'");
($checkModulo) ? require pagina('horarios.php'): header('Location: /'.HOMEHOST);