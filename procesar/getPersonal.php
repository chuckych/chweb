<?php
session_start();
require __DIR__ . '/../config/index.php';
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();
require __DIR__ . '/../config/conect_mssql.php';

FusNuloGET("Per", '');
FusNuloGET("Emp", '');
FusNuloGET("Plan", '');
FusNuloGET("Sect", '');
FusNuloGET("Sec2", '');
FusNuloGET("Grup", '');
FusNuloGET("Sucur", '');
FusNuloGET("Tipo", '');
FusNuloGET("Modulo", '');

$Per = !empty(($_POST['Per'])) ? implode(',', $_POST['Per']) : '';

$Tipo = test_input($_POST['Tipo']);

switch ($Tipo) {
    case '1':
        $Tipo = "AND PERSONAL.LegTipo = '0'";
        break;
    case '2':
        $Tipo = "AND PERSONAL.LegTipo = '1'";
        break;
    default:
        $Tipo = "";
        break;
}

// $Tipo = empty(($_POST['Tipo'])) ? "" : $Tipo;

$Per = !empty(($_POST['Per'])) ? "AND PERSONAL.LegNume IN ($Per)" : '';

$Empresa = datosGet($_POST['Emp'], "PERSONAL.LegEmpr");
$Planta = datosGet($_POST['Plan'], "PERSONAL.LegPlan");
$Sector = datosGet($_POST['Sect'], "PERSONAL.LegSect");
$Seccion = datosGet($_POST['Sec2'], "PERSONAL.LegSec2");
$Grupo = datosGet($_POST['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGet($_POST['Sucur'], "PERSONAL.LegSucu");
$TipoPersonal = $Tipo;

$FilterEstruct = $Empresa;
$FilterEstruct .= $Planta;
$FilterEstruct .= $Sector;
$FilterEstruct .= $Seccion;
$FilterEstruct .= $Grupo;
$FilterEstruct .= $Sucursal;
$FilterEstruct .= $TipoPersonal;
$FilterEstruct .= $Per;

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT PERSONAL.LegNume AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre' FROM PERSONAL WHERE PERSONAL.LegNume >'0' AND PERSONAL.LegFeEg = '17530101' AND PERSONAL.LegEsta = 0 $FilterEstruct";

// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND ";
    $where_condition .= " (dbo.fn_Concatenar(PERSONAL.LegNume,PERSONAL.LegApNo) collate SQL_Latin1_General_CP1_CI_AS LIKE '%" . $params['search']['value'] . "%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}
$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$sqlRec .= " ORDER BY PERSONAL.LegNume OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
// $sqlRec .=  $OrderBy;
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r($sqlRec); exit;

while ($row = sqlsrv_fetch_array($queryRecords)) {

    $pers_legajo = $row['pers_legajo'];
    $pers_nombre = empty($row['pers_nombre']) ? 'Sin Nombre' : $row['pers_nombre'];

    $data[] = array(
        'pers_legajo2' => '<label class="fontq align-middle m-0 fw4" style="margin-top:2px" for="' . $pers_legajo . '">' . $pers_legajo . '</label>',
        'pers_legajo3' => '<input type="number" class="border w85 bg-light border-0 animate__animated animate__fadeIn" id="_l" value=' . $pers_legajo . '>',
        'pers_nombre2' => '<label class="fontq align-middle m-0 fw4" style="margin-top:2px" for="' . $pers_legajo . '">' . $pers_nombre . '</label>',
        'pers_nombre3' => '<span class="animate__animated animate__fadeIn">' . $pers_nombre . '</span>',
        'check' => '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input check" name="legajo[]" id="' . $pers_legajo . '" value="' . $pers_legajo . '"><label class="custom-control-label" for="' . $pers_legajo . '"></label></div>',
        'null' => '',
    );
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);
echo json_encode($json_data);
exit;
