<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
// require __DIR__ . '../../config/index.php';
require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

E_ALL();

$eg = $_POST['_eg'] ?? '';
$estado = (($eg === 'on')) ? "AND PERSONAL.LegFeEg != '17530101'" : "AND PERSONAL.LegFeEg = '17530101'";
// echo $estado;exit;

FusNuloGET("Per", '');
FusNuloGET("Emp", '');
FusNuloGET("Plan", '');
FusNuloGET("Sect", '');
FusNuloGET("Sec2", '');
FusNuloGET("Grup", '');
FusNuloGET("Sucur", '');
FusNuloGET("Tipo", '');
FusNuloGET("Modulo", '');

$Per = !empty(($_GET['Per'])) ? implode(',', $_GET['Per']) : '';

$Tipo = ($_GET['Tipo'] == '2') ? "AND PERSONAL.LegTipo = '0'" : "AND PERSONAL.LegTipo = '$_GET[Tipo]'";
$Tipo = empty(($_GET['Tipo'])) ? "" : $Tipo;

$Per = !empty(($_GET['Per'])) ? "AND PERSONAL.LegNume IN ($Per)" : '';

$Empresa      = datosGet($_GET['Emp'], "PERSONAL.LegEmpr");
$Planta       = datosGet($_GET['Plan'], "PERSONAL.LegPlan");
$Sector       = datosGet($_GET['Sect'], "PERSONAL.LegSect");
$Seccion      = datosGet($_GET['Sec2'], "PERSONAL.LegSec2");
$Grupo        = datosGet($_GET['Grup'], "PERSONAL.LegGrup");
$Sucursal     = datosGet($_GET['Sucur'], "PERSONAL.LegSucu");
$TipoPersonal = $Tipo;

$FilterEstruct  = $Empresa;
$FilterEstruct  .= $Planta;
$FilterEstruct  .= $Sector;
$FilterEstruct  .= $Seccion;
$FilterEstruct  .= $Grupo;
$FilterEstruct  .= $Sucursal;
$FilterEstruct  .= $TipoPersonal;
$FilterEstruct  .= $Per;

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

if ($_GET['Modulo'] == 'Cierres') {
    $sql_query = "SELECT PERSONAL.LegNume AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre', PERCIERRE.CierreFech AS 'FechaCierre' FROM PERSONAL LEFT JOIN PERCIERRE ON PERSONAL.LegNume=PERCIERRE.CierreLega WHERE PERSONAL.LegNume >'0' AND PERSONAL.LegEsta = 0 $estado $filtros $FilterEstruct";
} else {
    $sql_query="SELECT PERSONAL.LegNume AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre', PERSONAL.LegDocu AS 'pers_dni', PERSONAL.LegSect AS 'pers_LegSect', EMPRESAS.EmpRazon AS 'pers_empresa', PLANTAS.PlaDesc AS 'pers_planta', CONVENIO.ConDesc AS 'pers_convenio', SECTORES.SecDesc AS 'pers_sector', SECCION.Se2Desc AS 'pers_seccion', GRUPOS.GruDesc AS 'pers_grupo', SUCURSALES.SucDesc AS 'pers_sucur', PERSONAL.LegMail AS 'pers_mail', PERSONAL.LegDomi AS 'pers_domic', PERSONAL.LegDoNu AS 'pers_numero', PERSONAL.LegDoOb AS 'pers_observ', PERSONAL.LegDoPi AS 'pers_piso', PERSONAL.LegDoDP AS 'pers_depto', LOCALIDA.LocDesc AS 'pers_localidad', PERSONAL.LegCOPO AS 'pers_cp', PROVINCI.ProDesc AS 'pers_prov', NACIONES.NacDesc AS 'pers_nacion', ( CASE PERSONAL.LegFeEg WHEN '17530101' THEN '0' ELSE '1' END ) AS pers_estado FROM PERSONAL INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND SECTORES.SecCodi=SECCION.SecCodi INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi INNER JOIN CONVENIO ON PERSONAL.LegConv=CONVENIO.ConCodi INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi INNER JOIN PROVINCI ON PERSONAL.LegProv=PROVINCI.ProCodi INNER JOIN LOCALIDA ON PERSONAL.LegLoca=LOCALIDA.LocCodi INNER JOIN NACIONES ON PERSONAL.LegNaci=NACIONES.NacCodi WHERE PERSONAL.LegNume >'0' $estado $filtros $FilterEstruct";
}
// print_r($sql_query); exit;
$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND ";
    $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) collate SQL_Latin1_General_CP1_CI_AS LIKE '%" . $params['search']['value'] . "%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$_GET['NoPag'] = $_GET['NoPag']?? '';
// if ($_GET['NoPag']) {
//     $sqlRec .=  "ORDER BY PERSONAL.LegFeEg, PERSONAL.LegNume";
// } else {
//     $sqlRec .=  "ORDER BY PERSONAL.LegFeEg, PERSONAL.LegNume OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
// }
$sqlRec .=  "ORDER BY PERSONAL.LegFeEg, PERSONAL.LegNume OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r($sqlRec); exit;

while ($row = sqlsrv_fetch_array($queryRecords)) {

    $pers_legajo   = $row['pers_legajo'];
    $FechaCierre   = !empty($row['FechaCierre']) ? $row['FechaCierre']->format('d/m/Y') : $row['FechaCierre'];
    $FechaCierre   = ($FechaCierre == '01/01/1753') ? '-' : $FechaCierre;
    $pers_nombre   = empty($row['pers_nombre']) ? 'Sin Nombre' : $row['pers_nombre'];

    if ($_GET['Modulo'] == 'Cierres') {
    } else {
        $pers_dni      = ceronull($row['pers_dni']);
        $pers_estado   = ($row['pers_estado'] == 0) ? 'Activo' : 'De Baja';
        $pers_empresa  = $row['pers_empresa'];
        $pers_planta   = $row['pers_planta'];
        $pers_convenio = $row['pers_convenio'];
        $pers_sector   = $row['pers_sector'];
        $pers_seccion   = $row['pers_seccion'];
        $pers_grupo    = $row['pers_grupo'];
        $pers_sucur    = $row['pers_sucur'];
    }


    if ($_GET['Modulo'] == 'Cierres') {
        $data[] = array(
            'pers_legajo2' => '<label class="fontq align-middle m-0 fw4" style="margin-top:2px" for="' . $pers_legajo . '">' . $pers_legajo . '</label>',
            'pers_legajo3' => '<input type="number" class="border w85 bg-light border-0 animate__animated animate__fadeIn" id="_l" value=' . $pers_legajo . '>',
            'pers_nombre2' => '<label class="fontq align-middle m-0 fw4" style="margin-top:2px" for="' . $pers_legajo . '">' . $pers_nombre . '</label>',
            'pers_nombre3' => '<span class="animate__animated animate__fadeIn">' . $pers_nombre . '</span>',
            'FechaCierre' => $FechaCierre,
            'check' => '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input check checkLega" name="legajo[]" id="' . $pers_legajo . '" value="' . $pers_legajo . '"><label class="custom-control-label" for="' . $pers_legajo . '"></label></div>',
            'null' => '',
        );
    } else {
        $data[] = array(
            'pers_legajo'   => $pers_legajo,
            'pers_nombre'   => $pers_nombre,
            'pers_dni'      => $pers_dni,
            'pers_estado'   => $pers_estado,
            'pers_empresa'  => $pers_empresa,
            'pers_planta'   => $pers_planta,
            'pers_convenio' => $pers_convenio,
            'pers_sector'   => $pers_sector,
            'pers_seccion'   => $pers_seccion,
            'pers_grupo'    => $pers_grupo,
            'pers_sucur'    => $pers_sucur,
            'editar'    => '<a title="Editar Legajo: ' . $pers_nombre . '" href="/' . HOMEHOST . '/personal/legajo/?_leg=' . $pers_legajo . '" id="editar" class="p-2 btn btn-sm btn-link text-decoration-none fontq"><span data-icon="&#xe042;" class="icon ml-2 align-middle mt-1 text-gris"></span></a>',
            'null' => '',
        );
    }
}


sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);
echo json_encode($json_data);
exit;