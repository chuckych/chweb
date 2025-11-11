<?php
require __DIR__ . '/../../config/session_start.php';
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '19';
ExisteModRol($Modulo);

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

require pagina('horarios.php');
