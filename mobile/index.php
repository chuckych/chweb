<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='5';
ExisteModRol($Modulo);
// $getData = 'GetFichadas';
$_datos     = 'mobile';
$bgcolor    = 'bg-mob';
define("RADIOS", [
    '200 Metros'  => '200', /** el Primero es el default */
    '100 Metros'  => '100',
    '300 Metros'  => '300',
    '400 Metros'  => '400',
    '500 Metros'  => '500',
    '600 Metros'  => '600',
    '700 Metros'  => '700',
    '800 Metros'  => '800',
    '900 Metros'  => '900',
    '1000 Metros' => '1000',
 ]);
require pagina('mobile.php');
