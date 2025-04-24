<?php
require __DIR__ . '/../../config/session_start.php';
require __DIR__ . '/../../config/index.php';
$Modulo = '1';
$bgcolor = 'bg-custom';
access_log('Auditoria');
require pagina('auditoria.php');
