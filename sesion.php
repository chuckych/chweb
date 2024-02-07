<?php
require __DIR__ . '/config/session_start.php';
require __DIR__ . '/config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
if (!secure_auth_ch_json()) {
    PrintRespuestaJson('activa', 'Sesión Activa');
} else {
    PrintRespuestaJson('inactiva', 'Sesión Inactiva');
}
E_ALL();
exit;