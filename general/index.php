<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='4';
ExisteModRol($Modulo);
$bgcolor = 'bg-custom';
$FechaIni2 = date("Y-m-d", strtotime(hoy() . "- 1 month"));
$FechaFin2 = date("Y-m-d", strtotime(hoy() . "- 0 days"));
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
    // 'Descargar'  => 'D',
 ]);
define("PLANTILLAS", [
    ''           => '',
    'Novedades'           => 'p_nov',
    'Horas'               => 'p_hor',
    'Fichadas'            => 'p_fic',
    'Llegadas Tarde'      => 'p_tar',
    'Ausencias'           => 'p_aus',
    'Salidas Anticipadas' => 'p_sal',
    'Incumplimientos'     => 'p_inc',
 ]);
require pagina('general.php');

