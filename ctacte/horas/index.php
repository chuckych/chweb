<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='13';
ExisteModRol($Modulo);
// $getData = 'GetCtaCteHoras2';
$_datos  = 'cta_horas';
$bgcolor = 'bg-custom';
require pagina('horas.php');