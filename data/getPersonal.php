<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "spanish");
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
UnsetGet('tk');
UnsetGet('q');
header("Content-Type: application/json");
FusNuloGET("Emp", '');
FusNuloGET("Plan", '');
FusNuloGET("Sect", '');
FusNuloGET("Sec2", '');
FusNuloGET("Grup", '');
FusNuloGET("Sucur", '');
FusNuloGET("Tipo", '');

$Tipo = test_input($_GET['Tipo']);
switch ($Tipo) {
    case '2':
        $Tipo = "AND PERSONAL.LegTipo = '0'";
        break;
    case '1':
        $Tipo = "AND PERSONAL.LegTipo = '1'";
        break;
    default:
        $Tipo = "AND PERSONAL.LegTipo = '0'";
        break;
}
$Tipo = empty(($_GET['Tipo'])) ? "" : $Tipo;

$Empresa = datosGet($_GET['Emp'], "PERSONAL.LegEmpr");
$Planta = datosGet($_GET['Plan'], "PERSONAL.LegPlan");
$Sector = datosGet($_GET['Sect'], "PERSONAL.LegSect");
$Seccion = datosGet($_GET['Sec2'], "PERSONAL.LegSec2");
$Grupo = datosGet($_GET['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGet($_GET['Sucur'], "PERSONAL.LegSucu");
$TipoPersonal = $Tipo;

$FilterEstruct = $Empresa;
$FilterEstruct .= $Planta;
$FilterEstruct .= $Sector;
$FilterEstruct .= $Seccion;
$FilterEstruct .= $Grupo;
$FilterEstruct .= $Sucursal;
$FilterEstruct .= $TipoPersonal;
$_GET['ln'] = $_GET['ln'] ?? '';
$respuesta = '';
$getFicLegaln = (isset($_GET['ln'])) ? implode(",", $_GET['ln']) : '';
$getFicLegaln = ($getFicLegaln) ? $getFicLegaln : '0';
$getFicLega = (isset($_GET['_l'])) ? implode(",", $_GET['_l']) : '';
$FicLegaln = (!empty($_GET['ln'])) ? "AND PERSONAL.LegNume NOT IN ($getFicLegaln)" : "";
// print_r(($getFicLegaln));exit;
$FicLega = (isset($_GET['_l'])) ? "AND PERSONAL.LegNume IN ($getFicLega)" : "";
$token = token();
$data = array();
if ($_GET['tk'] == $token) {
    require __DIR__ . '../../filtros/filtros.php';
    require __DIR__ . '../../config/conect_mssql.php';
    $q = $_GET['q'];

    // $hoy=date("Ymd",strtotime(hoy()."- 0 day"))
    $query = "SELECT PERSONAL.LegNume AS pers_legajo, PERSONAL.LegApNo AS pers_nombre, PERSONAL.LegDocu AS pers_dni, PERSONAL.LegSect AS pers_LegSect, EMPRESAS.EmpRazon AS pers_empresa, PLANTAS.PlaDesc AS pers_planta, CONVENIO.ConDesc AS pers_convenio, SECTORES.SecDesc AS pers_sector, GRUPOS.GruDesc AS pers_grupo, SUCURSALES.SucDesc AS pers_sucur, PERSONAL.LegMail AS pers_mail, PERSONAL.LegDomi AS pers_domic, PERSONAL.LegDoNu AS pers_numero, PERSONAL.LegDoOb AS pers_observ, PERSONAL.LegDoPi AS pers_piso, PERSONAL.LegDoDP AS pers_depto, LOCALIDA.LocDesc AS pers_localidad, PERSONAL.LegCOPO AS pers_cp, PROVINCI.ProDesc AS pers_prov, NACIONES.NacDesc AS pers_nacion, (CASE PERSONAL.LegFeEg WHEN '1753-01-01 00:00:00.000' THEN '0' ELSE '1' END) AS pers_estado FROM PERSONAL INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND SECTORES.SecCodi=SECCION.SecCodi INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi INNER JOIN CONVENIO ON PERSONAL.LegConv=CONVENIO.ConCodi INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi INNER JOIN PROVINCI ON PERSONAL.LegProv=PROVINCI.ProCodi INNER JOIN LOCALIDA ON PERSONAL.LegLoca=LOCALIDA.LocCodi INNER JOIN NACIONES ON PERSONAL.LegNaci=NACIONES.NacCodi WHERE PERSONAL.LegNume >'0' AND CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%$q%' AND PERSONAL.LegFeEg='1753-01-01 00:00:00.000' $FicLega $FicLegaln $FilterEstruct $filtros ORDER BY pers_legajo ASC";

    // print_r($query);exit;
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $result = sqlsrv_query($link, $query, $params, $options);

    if (sqlsrv_num_rows($result) > 0) {
        while ($row = sqlsrv_fetch_array($result)):
            $pers_legajo = $row['pers_legajo'];
            $pers_nombre = $row['pers_nombre'];
            $pers_dni = $row['pers_dni'];
            $pers_empresa = $row['pers_empresa'];
            $pers_planta = $row['pers_planta'];
            $pers_convenio = $row['pers_convenio'];
            $pers_sector = $row['pers_sector'];
            $pers_LegSect = $row['pers_LegSect'];
            $pers_grupo = $row['pers_grupo'];
            $pers_sucur = $row['pers_sucur'];
            $pers_estado = $row['pers_estado'];
            $pers_estado = ($pers_estado == '1') ? 'Inactivo' : 'Activo';
            $pers_mail = $row['pers_mail'];
            $pers_domic = $row['pers_domic'];
            $pers_numero = $row['pers_numero'];
            $pers_observ = $row['pers_observ'];
            $pers_piso = $row['pers_piso'];
            $pers_depto = $row['pers_depto'];
            $pers_localidad = $row['pers_localidad'];
            $pers_cp = $row['pers_cp'];
            $pers_prov = $row['pers_prov'];
            $pers_nacion = $row['pers_nacion'];
            $data[] = array(
                'pers_legajo' => $pers_legajo,
                'pers_nombre' => $pers_nombre,
                'pers_dni' => $pers_dni,
                'pers_empresa' => $pers_empresa,
                'pers_planta' => $pers_planta,
                'pers_convenio' => $pers_convenio,
                'pers_sector' => $pers_sector,
                'pers_grupo' => $pers_grupo,
                'pers_sucur' => $pers_sucur,
                'pers_estado' => $pers_estado,
                'pers_mail' => $pers_mail,
                'pers_domic' => $pers_domic,
                'pers_numero' => $pers_numero,
                'pers_observ' => $pers_observ,
                'pers_piso' => $pers_piso,
                'pers_depto' => $pers_depto,
                'pers_localidad' => $pers_localidad,
                'pers_cp' => $pers_cp,
                'pers_prov' => $pers_prov,
                'pers_nacion' => $pers_nacion
            );
        endwhile;
        sqlsrv_free_stmt($result);
        sqlsrv_close($link);
        $respuesta = array('success' => 'YES', 'error' => '0', 'personal' => $data);
    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', 'personal' => $data);
        // $data[] = array('text' => 'Empleado no encontrado');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'personal' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
