<?php
require __DIR__ . '../../funciones.php';
E_ALL();
header("Content-Type: text/html;charset=UTF-8");
timeZone();
timeZone_lang();
define("HOMEHOST", 'chweb');
define("CUSTOMER", 'HRConsulting');
$array_mod = [
       'cuentas' => 'Cuentas', // Cuentas ID 01
       'novedades' => 'Novedades', // Novedades ID 02
       'fichadas' => 'Fichadas', // Fichadas ID 03
       'general' => 'General', // General ID 04
       'mobile' => 'Mobile', // Mobile ID 05
       'mishoras' => 'Mis Horas', // Mis Horas ID 06
       'micuenta' => 'Mi Cuenta', // Mi Cuenta ID 07
       'Dashboard' => 'Dashboard', // Dashboard ID 08
       'ctacte' => 'Cta Cte Novedades', // Cta Cte Novedades ID 09
       'personal' => 'Personal', // Personal ID 10
       'fichar' => 'Ingreso de Fichadas', // Ingreso de Fichadas ID 11
       'procesar' => 'Procesar', // Procesar ID 12
       'ctactehoras' => 'Cta Cte Horas', // Cta Cte Horas ID 13
       'cierres' => 'Generar Cierres', // Generar Cierres ID 14
       'liquidar' => 'Generar Liquidación', // Generar Liquidación ID 15
       'horas' => 'Horas Trabajadas', // Horas Trabajadas ID 16
       'otrasnov' => 'Otras Novedades', // Otras Novedades ID 17
       'auditoria' => 'Auditoría', // Auditoría ID 18
       'horasign' => 'Horarios Asignados', // Horarios Asignados ID 19
       'horplan' => 'Planilla Horaria', // Planilla Horaria ID 20
       'partedia' => 'Parte Diario', // Parte Diario ID 21
       'infornov' => 'Informe de Novedades', // Informe de Novedades ID 22
       'inforfic' => 'Informe de Fichadas', // Informe de Fichadas ID 23
       'inforhora' => 'Informe de Horas', // Informe de Horas ID 24
       'mobilezonas' => 'Zonas Mobile', // Zonas Mobile ID 25
       'mobileuser' => 'Usuarios Mobile', // Usuarios Mobile ID 26
       'mobilesms' => 'Mensajes Mobile', // Mensajes Mobile ID 27
       'horascost' => 'Horas Costeadas', // Horas Costeadas ID 28
       'infornovc' => 'Informe de Presentismo', // Informe de Presentismo ID 29
       'datos' => 'Datos', // Datos ID 30
       'estruct' => 'Estructura', // Estructura ID 31
       'horarios' => 'Horarios', // Horarios ID 33
       'inforfar' => 'Informe FAR', // Informe FAR ID 34
       'Proyectos' => 'Proyectos', // Proyectos ID 35
       'reporte' => 'Reporte de Totales', // Reporte de totales ID 45
       'prysmian' => 'Reporte Prysmian' // Reporte de totales ID 46
];
define("MODULOS", $array_mod);