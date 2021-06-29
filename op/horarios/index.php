<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='33';
ExisteModRol($Modulo);
require pagina('horarios.php');