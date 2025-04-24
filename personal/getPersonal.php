<?php
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '/../config/index.php';
ultimoacc();
secure_auth_ch();
E_ALL();
require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/../config/conect_mssql.php';
require __DIR__ . '/valores.php';

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT PERSONAL.LegNume AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre', PERSONAL.LegTipo AS 'pers_tipo', PERSONAL.LegDocu AS 'pers_dni', PERSONAL.LegCUIT AS 'pers_cuit', PERSONAL.LegSect AS 'pers_LegSect', EMPRESAS.EmpRazon AS 'pers_empresa', PLANTAS.PlaDesc AS 'pers_planta', CONVENIO.ConDesc AS 'pers_convenio', SECTORES.SecDesc AS 'pers_sector', SECCION.Se2Desc AS 'pers_seccion', GRUPOS.GruDesc AS 'pers_grupo', SUCURSALES.SucDesc AS 'pers_sucur', PERSONAL.LegMail AS 'pers_mail', PERSONAL.LegDomi AS 'pers_domic', PERSONAL.LegDoNu AS 'pers_numero', PERSONAL.LegDoOb AS 'pers_observ', PERSONAL.LegDoPi AS 'pers_piso', PERSONAL.LegDoDP AS 'pers_depto', LOCALIDA.LocDesc AS 'pers_localidad', PERSONAL.LegCOPO AS 'pers_cp', PROVINCI.ProDesc AS 'pers_prov', NACIONES.NacDesc AS 'pers_nacion', ( CASE PERSONAL.LegFeEg WHEN '17530101' THEN '0' ELSE '1' END ) AS pers_estado, PERSONAL.LegTel1 AS 'LegTel1',  PERSONAL.LegTeO1 AS 'LegTeO1', PERSONAL.LegTel2 AS 'LegTel2',  PERSONAL.LegTeO2 AS 'LegTeO2', PERSONAL.LegRegCH AS 'LegRegl', REGLASCH.RCDesc AS 'RCHDesc' FROM PERSONAL INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND SECTORES.SecCodi=SECCION.SecCodi INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi INNER JOIN CONVENIO ON PERSONAL.LegConv=CONVENIO.ConCodi INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi INNER JOIN PROVINCI ON PERSONAL.LegProv=PROVINCI.ProCodi INNER JOIN LOCALIDA ON PERSONAL.LegLoca=LOCALIDA.LocCodi INNER JOIN NACIONES ON PERSONAL.LegNaci=NACIONES.NacCodi LEFT JOIN REGLASCH ON PERSONAL.LegRegCH = REGLASCH.RCCodi WHERE PERSONAL.LegNume >'0' $filtros $FilterEstruct";

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
$sqlRec .= $OrderBy . " OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
// $sqlRec .=  $OrderBy;
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r($sqlRec); exit;
function tooltipTittle($desc, $estruct)
{
    $t = 'data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="' . $desc . ': <strong>' . $estruct . '</strong>"';
    $t = ($estruct == '-') ? '' : $t;
    return $t;
}
function tooltipTittle2($desc, $estruct, $desc2, $estruct2)
{
    $t = 'data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="' . $desc . ': <strong>' . $estruct . '</strong><br>' . $desc2 . ': <strong>' . $estruct2 . '</strong>"';
    $t = ($estruct == '-') ? '' : $t;
    return $t;
}

while ($row = sqlsrv_fetch_array($queryRecords)) {
    $pers_legajo = $row['pers_legajo'];
    // $FechaCierre      = !empty($row['FechaCierre']) ? $row['FechaCierre']->format('d/m/Y') : $row['FechaCierre'];
    // $FechaCierre      = ($FechaCierre == '01/01/1753') ? '-' : $FechaCierre;
    $pers_nombre = empty($row['pers_nombre']) ? 'Sin Nombre' : $row['pers_nombre'];
    $pers_dni = ceronull($row['pers_dni']);
    $pers_cuit = ceronull($row['pers_cuit']);
    $pers_estado = ($row['pers_estado'] == 0) ? 'Activo' : 'De Baja';
    $pers_empresa = ceronull($row['pers_empresa']);
    $pers_planta = ceronull($row['pers_planta']);
    $pers_convenio = ceronull($row['pers_convenio']);
    $pers_sector = ceronull($row['pers_sector']);
    $pers_seccion = ceronull($row['pers_seccion']);
    $pers_grupo = ceronull($row['pers_grupo']);
    $pers_sucur = ceronull($row['pers_sucur']);
    $pers_domic = $row['pers_domic'];
    $pers_numero = $row['pers_numero'];
    $pers_piso = $row['pers_piso'];
    $pers_depto = $row['pers_depto'];
    $pers_localidad = $row['pers_localidad'];
    $pers_cp = $row['pers_cp'];
    $pers_prov = $row['pers_prov'];
    $pers_nacion = $row['pers_nacion'];
    $LegTel1 = $row['LegTel1'];
    $LegTeO1 = $row['LegTeO1'];
    $LegTel2 = $row['LegTel2'];
    $LegTeO2 = $row['LegTeO2'];
    $LegRegl = $row['LegRegl'];
    $RCHDesc = ceronull($row['RCHDesc']);
    $pers_tipo = ($row['pers_tipo'] == 0) ? 'M' : 'J';
    $pers_tipo_nombre = ($row['pers_tipo'] == 0) ? 'Mensual' : 'Jornal';

    $tooltipNombre = '<strong>' . $pers_nombre . '</strong><br>DNI: ' . $pers_dni . '<br>CUIL: ' . $pers_cuit . '<br>Domicilio:<br>' . $pers_domic . ' ' . $pers_numero . '. Piso: ' . $pers_piso . '. Depto: ' . $pers_depto . '. CP: ' . $pers_cp . ', Loc: ' . $pers_localidad . ', Prov : ' . $pers_prov . '<br>Nacionalidad: ' . $pers_nacion . '<br>Tel: <br>' . $LegTel1 . ' ' . $LegTeO1 . ', ' . $LegTel2 . ' ' . $LegTeO2;

    $data[] = array(
        'pers_legajo' => '<span class="d-inline-block text-truncate" style="min-width: 50px;">' . $pers_legajo . '</span>',
        'pers_nombre' => '<span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="' . $tooltipNombre . '" class="d-inline-block text-truncate pointer" style="width: 150px;">' . $pers_nombre . '</span>',
        'pers_tipo' => '<span ' . tooltipTittle('Tipo', $pers_tipo_nombre) . ' class=" pointer">' . $pers_tipo . '</span>',
        'pers_empresa' => '<span ' . tooltipTittle2('Empresa', $pers_empresa, 'Planta', $pers_planta) . '>' . $pers_empresa . '<br>' . $pers_planta . '</span>',
        'pers_convenio' => '<span ' . tooltipTittle2('Convenio', $pers_convenio, 'Regla', $RCHDesc) . '>' . $pers_convenio . '<br>' . $RCHDesc . '</span>',
        'pers_sector' => '<span ' . tooltipTittle2('Sector', $pers_sector, 'Sección', $pers_seccion) . '>' . $pers_sector . '<br>' . $pers_seccion . '</span>',
        'pers_grupo' => '<span ' . tooltipTittle2('Grupo', $pers_grupo, 'Sucursal', $pers_sucur) . '>' . $pers_grupo . '<br>' . $pers_sucur . '</span>',
        'editar' => '<a data-titlet="Editar" href="/' . HOMEHOST . '/personal/legajo/?_leg=' . $pers_legajo . '" id="editar" class="btn btn-sm btn-outline-custom border-ddd text-decoration-none"><i class="bi bi-pencil"></i></a>',
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
