<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='32';
ExisteModRol($Modulo);
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    borrarLogs(__DIR__ . '/', 1, '.log');
} else {
    borrarLogs(__DIR__ . '/', 30, '.log');
}
require pagina('mobile.php');