<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='10';
ExisteModRol($Modulo);
$getData = 'GetPersonal';
$_datos  = 'personal';
$bgcolor = 'bg-custom';
require pagina('personal.php');