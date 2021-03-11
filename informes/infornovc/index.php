<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo = '29';
ExisteModRol($Modulo);
$bgcolor = 'bg-custom';
$FirstYear='2000';
$maxYear='2100';
define("TIPO_HOJA", [
    'A4'     => 'A4',
    'Oficio' => 'LEGAL',
    'Carta'  => 'LETTER',
    'A3'     => 'A3',
]);
define("ORIENTACION", [
    'Vertical'   => 'P',
    'Horizontal' => 'L',
]);
define("DESTINO", [
    'Mostrar Pantalla' => 'I',
    'En Otra Pestaña'  => 'V',
]);
define("AGRUPAR", [
    ''         => '',
    'Empresa'  => 'a_Empr',
    'Planta'   => 'a_Plan',
    'Sector'   => 'a_Sect',
    'Grupo'    => 'a_Grup',
    'Sucursal' => 'a_Sucu',
]);
define("RESALTAR", [
    ''                    => '',
    'Llegadas Tarde'      => 'r_tar',
    'Incumplimientos'     => 'r_inc',
    'Salidas anticipadas' => 'r_sal',
    'Ausencias'           => 'r_aus',
]);

require __DIR__ . '../../../config/conect_mysql.php';
$cliente   = $_SESSION['ID_CLIENTE'];
$_SESSION["CONCEPTO_PRESENTES"] = $_SESSION["CONCEPTO_PRESENTES"] ?? '';
$query  = "SELECT valores FROM params WHERE modulo=29 and descripcion='presentes' and cliente = $cliente LIMIT 1";
$result = mysqli_query($link, $query);
$valorPresentes = mysqli_fetch_assoc($result);
$_SESSION["CONCEPTO_PRESENTES"] = $valorPresentes['valores'];
mysqli_free_result($result);

$_SESSION["CONCEPTO_AUSENTES"] = $_SESSION["CONCEPTO_AUSENTES"] ?? '';
$query  = "SELECT valores FROM params WHERE modulo=29 and descripcion='ausentes' and cliente = $cliente LIMIT 1";
$result = mysqli_query($link, $query);
$valorAusentes = mysqli_fetch_assoc($result);
$_SESSION["CONCEPTO_AUSENTES"] = $valorAusentes['valores'];
mysqli_free_result($result);
mysqli_close($link);

require pagina('inforpresentismo.php');
