<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
ExisteModRol('7');
$Modulo='999';
require pagina('rol.php');