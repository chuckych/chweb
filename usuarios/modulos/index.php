<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
ExisteModRol('7');
access_log('Modulos');
$Modulo='999';
$bgcolor = 'bg-custom';
require pagina('modulos.php');