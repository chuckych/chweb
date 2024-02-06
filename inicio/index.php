<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo = '999';
access_log('Inicio');
ExisteModRol(0);
$bgcolor = 'bg-custom';
if ($_SESSION['MODS_ROL']) {
    $countModRol = (count($_SESSION['MODS_ROL']));
    $mishoras = ($_SESSION['MODS_ROL'][0]['modsrol']);
    if (($countModRol == '1') && $mishoras == 6) {
        header('Location:../mishoras/');
    }
}
// echo '<pre>';
// print_r($_SESSION);
// echo 'EstrUser : '.$_SESSION['EstrUser'].'<br>';
// echo 'PlanRol : '.$_SESSION['PlanRol'].'<br>';
// echo 'ConvRol : '.$_SESSION['ConvRol'].'<br>';
// echo 'SectRol : '.$_SESSION['SectRol'].'<br>';
// echo 'Sec2Rol : '.$_SESSION['Sec2Rol'].'<br>';
// echo 'GrupRol : '.$_SESSION['GrupRol'].'<br>';
// echo 'SucuRol : '.$_SESSION['SucuRol'].'<br>';

// exit;
require pagina('inicio.php');