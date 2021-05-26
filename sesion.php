<?php
require __DIR__ . '/config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
if (!secure_auth_ch_json()) {
    PrintRespuestaJson('activa', 'Sesión Activa');
}
E_ALL();
exit;