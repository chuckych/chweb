<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='8';
$bgcolor = 'bg-custom';
ExisteModRol($Modulo);
require pagina('dashboard.php');