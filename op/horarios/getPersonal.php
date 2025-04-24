<?php
require __DIR__ . '/../../filtros/filtros.php';
require __DIR__ . '/valores.php';

$columns = $where_condition = $totalRecords = '';
$params = $_REQUEST;
$where_condition = "";
$arrLegajos = [];
$data = [];
$error = '';

$cols = [
    "PERSONAL.LegNume AS 'pers_legajo'",
    "PERSONAL.LegApNo AS 'pers_nombre'",
    "PERSONAL.LegTipo AS 'pers_tipo'",
    "PERSONAL.LegDocu AS 'pers_dni'",
    "PERSONAL.LegCUIT AS 'pers_cuit'",
    "PERSONAL.LegSect AS 'pers_LegSect'",
    "EMPRESAS.EmpRazon AS 'pers_empresa'",
    "PLANTAS.PlaDesc AS 'pers_planta'",
    "CONVENIO.ConDesc AS 'pers_convenio'",
    "SECTORES.SecDesc AS 'pers_sector'",
    "SECCION.Se2Desc AS 'pers_seccion'",
    "GRUPOS.GruDesc AS 'pers_grupo'",
    "SUCURSALES.SucDesc AS 'pers_sucur'",
    "PERSONAL.LegMail AS 'pers_mail'",
    "PERSONAL.LegDomi AS 'pers_domic'",
    "PERSONAL.LegDoNu AS 'pers_numero'",
    "PERSONAL.LegDoOb AS 'pers_observ'",
    "PERSONAL.LegDoPi AS 'pers_piso'",
    "PERSONAL.LegDoDP AS 'pers_depto'",
    "LOCALIDA.LocDesc AS 'pers_localidad'",
    "PERSONAL.LegCOPO AS 'pers_cp'",
    "PROVINCI.ProDesc AS 'pers_prov'",
    "NACIONES.NacDesc AS 'pers_nacion'",
    "(CASE PERSONAL.LegFeEg WHEN '17530101' THEN '0' ELSE '1' END ) AS 'pers_estado'",
    "PERSONAL.LegTel1 AS 'LegTel1'",
    "PERSONAL.LegTeO1 AS 'LegTeO1'",
    "PERSONAL.LegTel2 AS 'LegTel2'",
    "PERSONAL.LegTeO2 AS 'LegTeO2'",
    "PERSONAL.LegRegCH AS 'LegRegl'",
    "REGLASCH.RCDesc AS 'RCHDesc'"
];

$cols = implode(',', $cols);

$Joins = [
    "INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi",
    "INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi",
    "INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND SECTORES.SecCodi=SECCION.SecCodi",
    "INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi",
    "INNER JOIN CONVENIO ON PERSONAL.LegConv=CONVENIO.ConCodi",
    "INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi",
    "INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi",
    "INNER JOIN PROVINCI ON PERSONAL.LegProv=PROVINCI.ProCodi",
    "INNER JOIN LOCALIDA ON PERSONAL.LegLoca=LOCALIDA.LocCodi",
    "INNER JOIN NACIONES ON PERSONAL.LegNaci=NACIONES.NacCodi",
    "LEFT JOIN REGLASCH ON PERSONAL.LegRegCH = REGLASCH.RCCodi",
    "WHERE PERSONAL.LegNume >'0'"
];

$Joins = implode(' ', $Joins);

$sqlRec = "SELECT $cols FROM PERSONAL {$Joins} {$filtros} {$FilterEstruct}";
$sqlTot = "SELECT COUNT(PERSONAL.LegNume) as 'total' FROM PERSONAL {$Joins} {$filtros} {$FilterEstruct}";

$SearchValue = $params['search']['value'] ?? '';
$SearchLike = " AND (dbo.fn_Concatenar(PERSONAL.LegNume,PERSONAL.LegApNo) collate SQL_Latin1_General_CP1_CI_AS LIKE '%{$SearchValue}%') ";
$where_condition .= $SearchValue ? $SearchLike : '';

$sqlTot .= $where_condition;
$sqlRec .= $where_condition;

$sqlRec .= "$OrderBy OFFSET {$params['start']} ROWS FETCH NEXT {$params['length']} ROWS ONLY";
try {

    $totalRecords = simple_MSQuery($sqlTot)['total'];
    $records = arrMSQuery($sqlRec);

    if ($records) {
        foreach ($records as $row) {
            $pers_legajo = $row['pers_legajo'];
            $pers_nombre = empty($row['pers_nombre']) ? 'Sin Nombre' : $row['pers_nombre'];
            $pers_estado = ($row['pers_estado'] == 0) ? 'Activo' : 'De Baja';
            $pers_empresa = ceronull($row['pers_empresa']);
            $pers_planta = ceronull($row['pers_planta']);
            $pers_convenio = ceronull($row['pers_convenio']);
            $pers_sector = ceronull($row['pers_sector']);
            $pers_seccion = ceronull($row['pers_seccion']);
            $pers_grupo = ceronull($row['pers_grupo']);
            $pers_sucur = ceronull($row['pers_sucur']);
            $RCHDesc = ceronull($row['RCHDesc']);
            $pers_tipo = ($row['pers_tipo'] == 0) ? 'M' : 'J';
            $pers_tipo_nombre = ($row['pers_tipo'] == 0) ? 'Mensual' : 'Jornal';

            $data[] = [
                'pers_legajo' => $pers_legajo,
                'pers_nombre' => ucwords(mb_strtolower($pers_nombre, 'UTF-8')),
                'pers_tipo' => $pers_tipo_nombre,
                'pers_empresa' => $pers_empresa,
                'pers_planta' => $pers_planta,
                'pers_convenio' => $pers_convenio,
                'RCHDesc' => $RCHDesc,
                'pers_sector' => $pers_sector,
                'pers_seccion' => $pers_seccion,
                'pers_grupo' => $pers_grupo,
                'pers_sucur' => $pers_sucur,
                'pers_horario' => '',
            ];
            $arrLegajos[] = $pers_legajo;
        }
    }
} catch (Exception $th) {
    $error = $th->getMessage();
}
