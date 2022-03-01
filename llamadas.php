<meta charset="utf-8">
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
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/rapid-icon-font/icons-min.css" type="text/css">
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/bootstrap.min.css" type="text/css">
<!-- <link rel="stylesheet" href="/<?= HOMEHOST ?>/css/bootstrap-icons-1.5.0/bootstrap-icons.css" type="text/css"> -->
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/icons/font/bootstrap-icons.css" type="text/css">
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"> -->
<?php
switch ($Modulo) {
	case '1':
		/** Modulo novedades. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #795548; } </style>';
		break;
	case '2':
	case '22':
		/** Modulo novedades. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #0288d1; } </style>';
		break;
	case '29':
		/** Modulo novedades custom. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #0276aa; } </style>';
		break;
	case '3':
		/** Modulo fichadas. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #009688; } </style>';
		break;
	case '23':
		/** Modulo fichadas. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #009688; } </style>';
		break;
	case '4':
		/** Modulo general. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #68518f; } </style>';
		break;
	case '5':
		/** Modulo mobile fichadas. Color predeterminado */
	case '25':
		/** Modulo mobile zonas. Color predeterminado */
	case '26':
		/** Modulo mobile Usuarios. Color predeterminado */
	case '27':
		/** Modulo mobile Mensajes. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #b71c1c; } </style>';
		break;
	case '6':
		/** Modulo Mis Horas. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #2196f3; } </style>';
		/** AZUL */
		// echo '<style>:root { --main-bg-modcolor : #646C74; } </style>'; /** GRIS */
		break;
	case '8':
		/** Modulo Dashboard. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #b22a00; } </style>';
		break;
	case '9':
		/** Modulo cta cte. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #78909c; } </style>';
		break;
	case '10':
		/** Modulo Personal. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #2980B9; } </style>';
		break;
	case '12':
		/** Modulo Procesar. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #455a64; } </style>';
		break;
	case '14':
		/** Modulo Cierres. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #795548; } </style>';
		break;
	case '15':
		/** Modulo Liquidar. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #455a64; } </style>';
		break;
	case '16':
		/** Modulo Informe Horas. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #bc5100; } </style>';
		break;
	case '24':
	case '34':
		/** Modulo Horas. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #bc5100; } </style>';
		break;
	case '28':
		/** Modulo Horas costeadas. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #bf360c; } </style>';
		break;
	case '17':
		/** Modulo OTRAS novedades. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #4267B2; } </style>';
		break;
	case '19':
		/** Modulo Horarios Asignados. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #d32f2f; } </style>';
		break;
	case '21':
		/** Modulo Horarios Asignados. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #d32f2f; } </style>';
		break;
	case '99':
		/** Modulo custom. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #689f38; } </style>';
		break;
	case '30':
	case '31':
		/** Modulo Configuración. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #757575; } </style>';
		break;
	case '999':
		/** Modulo custom2. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #009688; } </style>';
		break;
	case '32':
		/** Modulo custom2. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #F48024; } </style>';
		break;
	case '33':
		/** Modulo custom2. Color predeterminado */
		echo '<style>:root { --main-bg-modcolor : #198754; } </style>';
		break;
	default:
		echo '<style>:root { --main-bg-modcolor : #17a2b8; } </style>';
		break;
}
?>
<link rel="stylesheet" href="/<?= HOMEHOST ?>/css/style-min.css?v=<?=vjs()?>">
<?php
if (getBrowser($_SERVER['HTTP_USER_AGENT'])=='Internet explorer') {
	echo '<link rel="stylesheet" type="text/css" href="/'. HOMEHOST .'/css/ie.css?v='.vjs().'" />';
}
?>
<!--
****************************
****************************
 Autor : nch@outlook.com.ar
****************************
****************************
-->