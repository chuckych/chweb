<?php
require __DIR__ . '/../config/session_start.php';
// header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../config/index.php';

ultimoacc();
secure_auth_ch();

require_once __DIR__ . '/configuracion.php';