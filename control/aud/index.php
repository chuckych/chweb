<?php
session_start();
require __DIR__ . '../../../config/index.php';
$Modulo  = '1';
$bgcolor = 'bg-custom';
access_log('Auditoria');
require pagina('auditoria.php');
