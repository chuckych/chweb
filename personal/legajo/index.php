<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='10';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$getData = 'GetPersonal';
$_datos  = 'personal';
$bgcolor = 'bg-custom';
define("TIPO_DOC", [
   'DU'  => '0',
   'DNI' => '1',
   'CI'  => '2',
   'LC'  => '3',
   'LE'  => '4',
//    'PAS' => '5'
]);
define("ESTADO_CIVIL", [
   'Soltero/a'  => '0',
   'Casado/a' => '1',
   'Viudo/a'  => '2',
   'Divorciado/a'  => '3',
   'No Determinado'  => '4',
]);
define("INFOR_EN_HORAS", [
   'En todos los días'  => '0',
   'En laboral' => '1',
   'En no laboral'  => '2',
   'En hábiles'  => '3',
   'En no hábiles'  => '4',
]);
define("ConTDias", [
   'Días trabajados'  => '0',
   'Días hábiles' => '1',
   'Días'  => '2'
]);
define("SEXO", [
   'Masculino' => '1',
   'Femenino'  => '0',
]);
define("TIPO_EMP", [
   'Interna'  => '0',
   'Externa' => '1',
]);
define("TIPO_PER", [
   'Mensual'  => '0',
   'Jornal' => '1',
]);
define("TIPO_ASIGN", [
   'Según asignación'  => '0',
   'Alternativo según fichadas' => '1',
]);
define("INCUMPLIMIENTO", [
   'Estándar sin control de descanso'  => '0',
   'Estándar con control de descanso'  => '1',
   '(Hs. a Trabajar - Hs. Trabajadas)' => '2',
   '(Hs. a Trabajar - Hs. Trabajadas) - Descanso como tolerancia' => '3',
   '(Hs. a Trabajar - Hs. Trabajadas) + Incumplimiento de descanso' => '4',
   'Recortado sin control de descanso' => '5',
   'Recortado con control de descanso' => '6',
]);
$naciones=nacionalidades();
require pagina('alta.php');
