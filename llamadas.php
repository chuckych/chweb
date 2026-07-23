<meta charset="utf-8">
<meta name="view-transition" content="same-origin">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Control Horario WEB HRProcess">
<meta name="author" content="Norberto CH  - nch@outlook.com.ar">
<meta name="msapplication-TileColor" content="#2d89ef" />
<link rel="apple-touch-icon" sizes="76x76" href="/<?= HOMEHOST ?>/img/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/<?= HOMEHOST ?>/img/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/<?= HOMEHOST ?>/img/favicon/favicon-16x16.png">
<link rel="manifest" href="/<?= HOMEHOST ?>/img/favicon/site.webmanifest">
<link rel="mask-icon" href="/<?= HOMEHOST ?>/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#fafafa">
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/normalize-min.css" type="text/css" />
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/animate.min.css" type="text/css" />
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/bootstrap.min.css?v=<?= version_file("/css/bootstrap.min.css") ?>" type="text/css">
<link rel="stylesheet" href="/<?= HOMEHOST ?>/vendor/twbs/bootstrap-icons/font/bootstrap-icons.css?v=<?= version_file("/vendor/twbs/bootstrap-icons/font/bootstrap-icons.css") ?>" type="text/css">
<?php
$mapColor = [
	// ─────────────────────────────────────────────────────────────────
    // Operaciones
    // ─────────────────────────────────────────────────────────────────
    '2'   => '#0288d1', // Novedades
    '3'   => '#009688', // Fichadas
    '4'   => '#68518f', // General
    '6'   => '#2196f3', // Mis Horas
    '8'   => '#b22a00', // Dashboard
    '9'   => '#78909c', // Cta Cte
    '10'  => '#2980B9', // Personal
    '11'  => '#2980B9', // Fichar
    '12'  => '#455a64', // Procesar
    '13'  => '#78909c', // Cta Cte Horas
    '14'  => '#795548', // Cierres
    '15'  => '#455a64', // Liquidar
    '16'  => '#bc5100', // Horas
    '17'  => '#4267B2', // Otras Novedades
    '28'  => '#bf360c', // Horas Costeadas
	'33'  => '#198754', // Horarios
    '47'  => '#455a64', // Proyectar Horas
    '48'  => '#455a64', // Liquidar Custom
	// ─────────────────────────────────────────────────────────────────
    // Informes
    // ─────────────────────────────────────────────────────────────────
    '19'  => '#4263eb', // Horarios Asignados
    '20'  => '#4263eb', // Planilla Horaria
	'21'  => '#d32f2f', // Parte Diario
    '22'  => '#0288d1', // Informe de Novedades
    '51'  => '#0374b1', // Informe Otras Novedades
	'23'  => '#009688', // Informe de Fichadas
    '24'  => '#bc5100', // Informe de Horas
	'29'  => '#0276aa', // Informe de Presentismo
    '34'  => '#bc5100', // Informe FAR
    '45'  => '#d9403a', // Reporte de Totales
    '46'  => '#343a40', // Reporte Prysmian
	// ─────────────────────────────────────────────────────────────────
    // Configuración
    // ─────────────────────────────────────────────────────────────────
    '30'  => '#343a40', // Datos
    '31'  => '#343a40', // Estructura
    '49'  => '#343a40', // Horarios
    '50'  => '#343a40', // Rotaciones
	// ─────────────────────────────────────────────────────────────────
    // Mobile
    // ─────────────────────────────────────────────────────────────────
    '5'   => '#b71c1c', // Mobile
    '25'  => '#b71c1c', // Zonas Mobile
    '26'  => '#b71c1c', // Usuarios Mobile
    '27'  => '#b71c1c', // Mensajes Mobile
    '32'  => '#4a5461', // Mobile HRP
	// ─────────────────────────────────────────────────────────────────
    // Cuentas
    // ─────────────────────────────────────────────────────────────────
    '1'  => '#795548', // Cuentas
	'7'  => '#795548', // Mi Cuenta
	'18' => '#795548', // Auditoría
	// ─────────────────────────────────────────────────────────────────
    // Otros
    // ─────────────────────────────────────────────────────────────────
    '99'  => '#689f38', // Otros
    '999' => '#009688', // Otros
];
echo '<style>:root { --main-bg-modcolor : ' . ($mapColor[$Modulo ?? ''] ?? '#17a2b8') . '; } </style>';
echo PHP_EOL;
?>
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/hint/hint.css?v=<?= version_file("/css/hint/hint.css") ?>">
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/style.css?v=<?= version_file("/css/style.css") ?>">
<?php
if (getBrowser($_SERVER['HTTP_USER_AGENT']) == 'Internet explorer') {
	echo '<link rel="stylesheet" type="text/css" href="/' . HOMEHOST . '/css/ie.css?v=' . version_file("/css/ie.css") . '" />';
}
?>
<!-- 
// ─────────────────────────────────────────────────────────────────
// Autor : nch@outlook.com.ar
// ───────────────────────────────────────────────────────────────── 
-->
