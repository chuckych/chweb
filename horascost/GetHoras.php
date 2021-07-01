<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

$data = array();

$legajo = test_input(FusNuloPOST('_l', ''));

// if($legajo=='vacio'){

//     $json_data = array(
//         "draw"            => '',
//         "recordsTotal"    => '',
//         "recordsFiltered" => '',
//         "data"            => $data
//     );
    
//     echo json_encode($json_data);
//     exit;
// }

require __DIR__ . '../valores.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$params = $columns = $totalRecords ='';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$Calculos = (!$Calculos==1) ? "AND TIPOHORA.THoColu > 0" : '';

 $sql_query="SELECT FICHAS01.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', FICHAS01.FicFech AS 'FicFech', dbo.fn_HorarioAsignado( FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF ) AS 'Horario', FICHAS01.FicCFec as 'Desde', FICHAS01.FicHora AS 'Hora', TIPOHORA.THoDesc AS 'HoraDesc', TIPOHORA.THoDesc2 AS 'HoraDesc2', FICHAS01.FicHsHeC AS 'FicHsHeC', FICHAS01.FicHsAuC AS 'FicHsAuC', (dbo.fn_STRMinutos(FICHAS01.FicHsHeC)) AS 'MinFicHsHeC', (dbo.fn_STRMinutos(FICHAS01.FicHsAuC)) AS 'MinFicHsAuC', FICHAS01.FicEsta AS 'Estado', TIPOHORA.THoColu AS 'THoColu', dbo.fn_DiaDeLaSemana(FICHAS01.FicFech) AS 'Dia', FICHAS01.FicEmpr As 'CodEmpr', FICHAS01.FicPlan As 'CodPlan', FICHAS01.FicSucu As 'CodSucu', FICHAS01.FicGrup As 'CodGrupo', FICHAS01.FicSect As 'CodSect', FICHAS01.FicSec2 As 'CodSec2', FICHAS01.FicTare As 'CodTar', TAREAS.TareDesc AS 'Tarea', PLANTAS.PlaDesc AS 'Planta', SUCURSALES.SucDesc AS 'Sucursal', GRUPOS.GruDesc AS 'Grupos', SECTORES.SecDesc AS 'Sector', SECCION.Se2Desc AS 'Seccion', FICHAS01.FicCosto AS 'Costo' FROM FICHAS01 INNER JOIN FICHAS ON FICHAS01.FicLega=FICHAS.FicLega AND FICHAS01.FicFech=FICHAS.FicFech AND FICHAS01.FicTurn=FICHAS.FicTurn INNER JOIN PERSONAL ON FICHAS01.FicLega=PERSONAL.LegNume INNER JOIN TIPOHORA ON FICHAS01.FicHora=TIPOHORA.THoCodi INNER JOIN TAREAS ON FICHAS01.FicTare=TAREAS.TareCodi INNER JOIN PLANTAS ON FICHAS01.FicPlan=PLANTAS.PlaCodi INNER JOIN SUCURSALES ON FICHAS01.FicSucu=SUCURSALES.SucCodi INNER JOIN GRUPOS ON FICHAS01.FicGrup=GRUPOS.GruCodi INNER JOIN SECTORES ON FICHAS01.FicSect=SECTORES.SecCodi INNER JOIN SECCION ON FICHAS01.FicSec2=SECCION.Se2Codi AND SECCION.SecCodi=SECTORES.SecCodi WHERE FICHAS01.FicLega='$legajo' AND FICHAS01.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $Calculos $FilterEstruct $FiltrosFichas";

// print_r($sql_query); exit;   

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%" . $params['search']['value'] . "%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

// $sqlRec .=  " ORDER BY FICHAS01.FicFech, TIPOHORA.THoColu, FICHAS01.FicHora OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$sqlRec .=  " ORDER BY FICHAS01.FicLega,FICHAS01.FicFech,FICHAS01.FicHora,FICHAS01.FicCFec";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);

$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r($sqlRec).PHP_EOL; exit;

while ($row = sqlsrv_fetch_array($queryRecords)) :
    $desde    = $row['Desde']->format('H:i');
    $desde    = $desde =='00:00' ? 'Inicio': $desde;
    $desde    = ($desde == 'Inicio') ? '<span class="fw3">Desde: <span class="fw4">'.$desde.'</span></span>':'<span class="fw3">Desde: <span class="ls1 fw4">'.$desde.'</span></span>';
    $Sucursal = ceronull($row['Sucursal']);
    $Tarea    = ($row['CodTar']=='0')?'Sin Tarea':$row['Tarea'];
    $Planta   = ceronull($row['Planta']);
    $Grupo    = ceronull($row['Grupos']);
    $Sector   = ceronull($row['Sector']);
    $Seccion  = ceronull($row['Seccion']);
    $data[] = array(
             'Legajo'      => $row['Legajo'],
             'Nombre'      => $row['Nombre'],
             'Costo'       => ceronull($row['Costo']),
             'FicFechStr'  => $row['FicFech']->format('Ymd'),
             //  'FicFech' => $row['FicFech']->format('d/m/Y'),
             'FicFech'     => '<span class="ls1 mr-2">'.$row['FicFech']->format('d/m/Y').'</span>'.$row['Dia'],
             'FechaDia'    => '<span class="ls1">'.$row['FicFech']->format('d/m/Y').'</span><br>'.$row['Dia'],
             'Horario'     => '<span class="ls1">'.$row['Horario'].'</span><br>'.$desde,
             'Hora'        => $row['Hora'],
             'Desde'       => $desde,
             'HoraDesc'    => $row['HoraDesc'].'<br><span class="fw3">Autorizadas</span>',
             'HoraDesc2'   => $row['HoraDesc2'],
             'FicHsHeC'    => $row['FicHsHeC'],
             'FicHsAuC'    => $row['FicHsAuC'],
             'CalcHoras'   => $row['FicHsHeC'].'<br><b class="text-secondary">'.$row['FicHsAuC'].'</b>',
             'Estado'      => $row['Estado'],
             'Dia'         => $row['Dia'],
             'CodEmpr'     => $row['CodEmpr'],
             'CodPlan'     => $row['CodPlan'],
             'CodSucu'     => $row['CodSucu'],
             'CodGrupo'    => $row['CodGrupo'],
             'CodSect'     => $row['CodSect'],
             'CodSec2'     => $row['CodSec2'],
             'CodTar'      => $row['CodTar'],
             'Tarea Prod'  => $row['Tarea'],
             'TareaDesc'   => '<span title="'.$row['Tarea'].'" class="d-inline-block text-truncate Mxw150">'.$Tarea.'</span>',
             'Tarea'       => $Tarea,
             'Planta'      => $Planta,
             'Sucursal'    => $Sucursal,
             'Grupo'       => $Grupo,
             'Sector'      => $Sector,
             'Seccción'    => $Seccion,
             'PlantaDesc'  => '<span title="'.$row['Planta'].'" class="d-inline-block text-truncate Mxw150">'.$Planta.'</span>',
             'SucurDesc'   => '<span title="'.$row['Sucursal'].'" class="d-inline-block text-truncate Mxw150">'.$Sucursal.'</span>',
             'GrupDesc'    => '<span title="'.$row['Grupos'].'" class="d-inline-block text-truncate Mxw150">'.$Grupo.'</span>',
             'SectDesc'    => '<span title="'.$row['Sector'].'" class="d-inline-block text-truncate Mxw150">'.$Sector.'</span>',
             'Sec2Desc'    => '<span title="'.$row['Seccion'].'" class="d-inline-block text-truncate Mxw150">'.$Seccion.'</span>',
    );
endwhile;

sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);

$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);

echo json_encode($json_data);
