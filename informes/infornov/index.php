<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='22';
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
define("AGRUPAR", [
    ''         => '',
    'Empresa'  => 'a_Empr',
    'Planta'   => 'a_Plan',
    'Sector'   => 'a_Sect',
    'Grupo'    => 'a_Grup',
    'Sucursal' => 'a_Sucu',
 ]);
define("RESALTAR", [
    ''                    => '',
    'Llegadas Tarde'      => 'r_tar',
    'Incumplimientos'     => 'r_inc',
    'Salidas anticipadas' => 'r_sal',
    'Ausencias'           => 'r_aus',
 ]);
require pagina('infornov.php');

