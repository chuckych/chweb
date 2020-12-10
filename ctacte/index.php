<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='9';
ExisteModRol($Modulo);
require pagina('ctacte.php');
$Peri    = peri_min_max();
$Peri    = $Peri['max'];
echo '<input type="hidden" name="" id="UltPeri" value="'.$Peri .'">';