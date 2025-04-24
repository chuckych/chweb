<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
secure_auth_ch();
$Modulo = '10';
ExisteModRol($Modulo);
existConnMSSQL(); // si no existe conexion a MSSQL redirigimos al inicio
$getData = 'GetPersonal';
$_datos = 'personal';
$bgcolor = 'bg-custom';
$token = sha1($_SESSION['RECID_CLIENTE']);
define("TIPO_DOC", [
   'DU' => '0',
   'DNI' => '1',
   'CI' => '2',
   'LC' => '3',
   'LE' => '4'
]);
define("ESTADO_CIVIL", [
   'Soltero/a' => '0',
   'Casado/a' => '1',
   'Viudo/a' => '2',
   'Divorciado/a' => '3',
   'No Determinado' => '4',
]);
define("INFOR_EN_HORAS", [
   'En todos los días' => '0',
   'En laboral' => '1',
   'En no laboral' => '2',
   'En hábiles' => '3',
   'En no hábiles' => '4',
]);
define("ConTDias", [
   'Días trabajados' => '0',
   'Días hábiles' => '1',
   'Días' => '2'
]);
define("SEXO", [
   'Masculino' => '1',
   'Femenino' => '0',
]);
define("TIPO_EMP", [
   'Interna' => '0',
   'Externa' => '1',
]);
define("TIPO_PER", [
   'Mensual' => '0',
   'Jornal' => '1',
]);
define("TIPO_ASIGN", [
   'Según asignación' => '0',
   'Alternativo según fichadas' => '1',
]);
define("INCUMPLIMIENTO", [
   'Estándar sin control de descanso' => '0',
   'Estándar con control de descanso' => '1',
   '(Hs. a Trabajar - Hs. Trabajadas)' => '2',
   '(Hs. a Trabajar - Hs. Trabajadas) - Descanso como tolerancia' => '3',
   '(Hs. a Trabajar - Hs. Trabajadas) + Incumplimiento de descanso' => '4',
   'Recortado sin control de descanso' => '5',
   'Recortado con control de descanso' => '6'
]);

$_GET["_leg"] = intval($_GET["_leg"]) ?? '';

$_leg = ($_GET["_leg"]);

if (empty($_leg)) {
   echo "<h1>Legajo Inválido</h1>";
   echo '<script>setTimeout(()=>{ window.location.href="../index.php";}, 1000);</script>';
   exit;
}

$EstrUser = explode(',', $_SESSION['EstrUser']);
$Empr = explode(',', $_SESSION['EmprRol']);
$Plan = explode(',', $_SESSION['PlanRol']);
$Conv = explode(',', $_SESSION['ConvRol']);
$Sect = explode(',', $_SESSION['SectRol']);
$Sec2 = explode(',', $_SESSION['Sec2Rol']);
$Grup = explode(',', $_SESSION['GrupRol']);
$Sucu = explode(',', $_SESSION['SucuRol']);

$dataParametros = array(
   'Nume' => ($EstrUser),
   'ApNo' => "",
   'Docu' => [],
   'ApNoNume' => "",
   'Empr' => ($Empr),
   'Plan' => ($Plan),
   'Sect' => ($Sect),
   'Sec2' => ($Sec2),
   'Conv' => ($Conv),
   'Grup' => ($Grup),
   'Sucu' => ($Sucu),
   'TareProd' => [],
   'RegCH' => [],
   'Tipo' => [],
   "Baja" => [],
   "IntExt" => [],
   "getDatos" => 0,
   "getLiqui" => 0,
   "getEstruct" => 0,
   "getControl" => 0,
   "getHorarios" => 0,
   "getAcceso" => 0,
   'start' => 0,
   'length' => 5000
);

$url = gethostCHWeb() . "/" . HOMEHOST . "/api/personal/";

$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';
$dataApi = json_decode(requestApi($url, $token, "", $dataParametros, 10), true);
$colLega = array();

if ($dataApi['DATA']) {
   $colLega = array_column($dataApi['DATA'], "Lega");
}

try {
   $searchValue = $_leg;
   $key = array_search($searchValue, $colLega);

   if ($key === false) {
      echo "El Legajo $searchValue No es valido";
      echo '<script>setTimeout(()=>{ window.location.href="../index.php";}, 1000);</script>';
      exit;
   }
} catch (Exception $e) {
   echo 'Error: ' . $e->getMessage();
   echo '<script>setTimeout(()=>{ window.location.href="../index.php";}, 1000);</script>';
   exit;
}
require pagina('alta.php');
