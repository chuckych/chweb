<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='999';
access_log('Inicio');
$bgcolor = 'bg-custom';
if ($_SESSION['MODS_ROL']) {
    $countModRol = (count($_SESSION['MODS_ROL']));
    $mishoras = ($_SESSION['MODS_ROL'][0]['modsrol']);
    if (($countModRol == '1')&& $mishoras == 6) {
        header('Location:../mishoras/');
    }
}
require pagina('inicio.php');