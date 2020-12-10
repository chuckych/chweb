<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='1';
ExisteModRol($Modulo);
// ExisteModRol($_SESSION['RECID_ROL'], $Modulo);
require pagina('cuentas.php');
