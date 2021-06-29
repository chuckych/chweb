<?php
// session_start();
require __DIR__ . '../../funciones.php';
E_ALL();
header("Content-Type: text/html;charset=UTF-8");
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "spanish");
define("HOMEHOST", 'chweb');
define("CUSTOMER", 'HRConsulting');
define("MODULOS", [
       /** ID = 01 */ 'cuentas'   => 'Cuentas',
       /** ID = 02 */ 'novedades' => 'Novedades',
       /** ID = 03 */ 'fichadas'  => 'Fichadas',
       /** ID = 04 */ 'general'   => 'General',
       /** ID = 05 */ 'mobile'    => 'Mobile',
       /** ID = 06 */ 'mishoras'  => 'Mis Horas',
       /** ID = 07 */ 'micuenta'  => 'Mi Cuenta',
       /** ID = 08 */ 'Dashboard' => 'Dashboard',
       /** ID = 09 */ 'ctacte'    => 'Cta Cte Novedades',
       /** ID = 10 */ 'personal'  => 'Personal',
       /** ID = 11 */ 'fichar'  => 'Ingreso de Fichadas',
       /** ID = 12 */ 'procesar'  => 'Procesar',
       /** ID = 13 */ 'ctactehoras'  => 'Cta Cte Horas',
       /** ID = 14 */ 'cierres'  => 'Generar Cierres',
       /** ID = 15 */ 'liquidar'  => 'Generar Liquidación',
       /** ID = 16 */ 'horas'  => 'Horas Trabajadas',
       /** ID = 17 */ 'otrasnov'  => 'Otras Novedades',
       /** ID = 18 */ 'auditoria'  => 'Auditoría',
       /** ID = 19 */ 'horasign'  => 'Horarios Asignados',
       /** ID = 20 */ 'horplan'  => 'Planilla Horaria',
       /** ID = 21 */ 'partedia'  => 'Parte Diario',
       /** ID = 22 */ 'infornov'  => 'Informe de Novedades',
       /** ID = 23 */ 'inforfic'  => 'Informe de Fichadas',
       /** ID = 24 */ 'inforhora'  => 'Informe de Horas',
       /** ID = 25 */ 'mobilezonas'  => 'Zonas Mobile',
       /** ID = 26 */ 'mobileuser'  => 'Usuarios Mobile',
       /** ID = 27 */ 'mobilesms'  => 'Mensajes Mobile',
       /** ID = 28 */ 'horascost'  => 'Horas Costeadas',
       /** ID = 29 */ 'infornovc'  => 'Informe de Presentismo',
       /** ID = 30 */ 'datos'  => 'Datos',
       /** ID = 31 */ 'estruct'  => 'Estructura',
       /** ID = 33 */ 'horarios'  => 'Horarios',
]);