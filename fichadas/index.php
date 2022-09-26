<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='3';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$FechaIni2 = date("Y-m-d", strtotime(hoy() . "- 1 month"));
$FechaFin2 = date("Y-m-d", strtotime(hoy() . "- 0 days"));
// echo '<pre>';
// foreach(glob('../logs/info/*.log') as $ent)
// {
//     if(is_dir($ent))
//     {
//         continue;
//     }

//     echo  md5_file($ent) .  PHP_EOL;
// }
// exit;

require pagina('fichadas.php');
