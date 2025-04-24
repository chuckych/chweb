<?php
require __DIR__ . '/../../config/session_start.php';
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '45';
ExisteModRol($Modulo);
require pagina('reporte.php');
