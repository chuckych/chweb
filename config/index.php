<?php
require_once __DIR__ . '/../funciones.php';
E_ALL();
header("Content-Type: text/html;charset=UTF-8");
timeZone();
timeZone_lang();

defined('HOMEHOST') || define('HOMEHOST', 'chweb');
defined('CUSTOMER') || define('CUSTOMER', 'HRConsulting');
defined('MODULOS') || define('MODULOS', loadModulos());

$_SESSION['HOST_CHWEB'] ??= gethostCHWeb();
require __DIR__ . '/labels.php';
// $_SESSION['HOST_CHWEB'] = $_SESSION['HOST_CHWEB'] ?: gethostCHWeb();