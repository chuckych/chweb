<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='31';
ExisteModRol($Modulo);
$bgcolor = 'bg-custom';
require pagina('estruct.php');