<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='23';
ExisteModRol($Modulo);
$bgcolor = 'bg-custom';
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
define("TIPO_HOJA", [
    'A4'     => 'A4',
    'Oficio' => 'LEGAL',
    'Carta'  => 'LETTER',
    'A3'     => 'A3',
 ]);
define("ORIENTACION", [
    'Horizontal' => 'L',
    'Vertical'   => 'P',
 ]);
define("DESTINO", [
    'Mostrar Pantalla' => 'I',
    'En Otra Pestaña'  => 'V',
 ]);
require pagina('inforfic.php');

