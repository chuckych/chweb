<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='24';
ExisteModRol($Modulo);
$bgcolor = 'bg-custom';
define("TIPO_HOJA", [
    'A4'     => 'A4',
    'Oficio' => 'LEGAL',
    'Carta'  => 'LETTER',
    'A3'     => 'A3',
 ]);
define("ORIENTACION", [
    'Vertical'   => 'P',
    'Horizontal' => 'L',
 ]);
define("DESTINO", [
    'Mostrar Pantalla' => 'I',
    'En Otra Pestaña'  => 'V',
 ]);
require pagina('inforhora.php');