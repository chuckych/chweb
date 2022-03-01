<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='32';
ExisteModRol($Modulo);
require pagina('mobile-hrp.php');