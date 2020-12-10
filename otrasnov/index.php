<?php
session_start();
require __DIR__ . '../../config/index.php';
secure_auth_ch();
$Modulo='17';
ExisteModRol($Modulo);
require pagina('otrasnov.php');
