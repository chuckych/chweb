<?php
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
E_ALL();


$estruct = FusNuloPOST('estruct', '');
$q = FusNuloPOST('q', '');

require __DIR__ . '../../valores.php';
require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';


switch ($estruct) {
    case 'Empr':
        $FicEstruct    = 'PERSONAL.LegEmpr';
        $ColEstruc     = 'EMPRESAS';
        $ColEstrucDesc = 'EMPRESAS.EmpRazon';
        $ColEstrucCod  = 'EMPRESAS.EmpCodi';
        break;
    case 'Plan':
        $FicEstruct    = 'PERSONAL.LegPlan';
        $ColEstruc     = 'PLANTAS';
        $ColEstrucDesc = 'PLANTAS.PlaDesc';
        $ColEstrucCod  = 'PLANTAS.PlaCodi';
        break;
    case 'Grup':
        $FicEstruct    = 'PERSONAL.LegGrup';
        $ColEstruc     = 'GRUPOS';
        $ColEstrucDesc = 'GRUPOS.GruDesc';
        $ColEstrucCod  = 'GRUPOS.GruCodi';
        break;
    case 'Sect':
        $FicEstruct    = 'PERSONAL.LegSect';
        $ColEstruc     = 'SECTORES';
        $ColEstrucDesc = 'SECTORES.SecDesc';
        $ColEstrucCod  = 'SECTORES.SecCodi';
        break;
    case 'Sucu':
        $FicEstruct    = 'PERSONAL.LegSucu';
        $ColEstruc     = 'SUCURSALES';
        $ColEstrucDesc = 'SUCURSALES.SucDesc';
        $ColEstrucCod  = 'SUCURSALES.SucCodi';
        break;
    case 'Tare':
        $FicEstruct    = 'PERSONAL.LegTareProd';
        $ColEstruc     = 'TAREAS';
        $ColEstrucDesc = 'TAREAS.TareDesc';
        $ColEstrucCod  = 'TAREAS.TareCodi';
        break;
    case 'Conv':
        $FicEstruct    = 'PERSONAL.LegConv';
        $ColEstruc     = 'CONVENIO';
        $ColEstrucDesc = 'CONVENIO.ConDesc';
        $ColEstrucCod  = 'CONVENIO.ConCodi';
        break;
    case 'Regla':
        $FicEstruct    = 'PERSONAL.LegRegCH';
        $ColEstruc     = 'REGLASCH';
        $ColEstrucDesc = 'REGLASCH.RCDesc';
        $ColEstrucCod  = 'REGLASCH.RCCodi';
        break;
    case 'Lega':
        $FicEstruct    = 'PERSONAL.LegNume';
        $ColEstruc     = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod  = 'PERSONAL.LegNume';
        break;
    case 'Sec2':
        $FicEstruct    = 'PERSONAL.LegNume';
        $ColEstruc     = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod  = 'PERSONAL.LegNume';
        break;
    case 'Tipo':
        $ColEstruc     = 'PERSONAL';
        $ColEstrucCod  = 'PERSONAL.LegTipo';
        break;

    default:
        # code...
        break;
}



$FiltroQ  = (!empty($q)) ? "AND CONCAT($ColEstrucCod, $ColEstrucDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';

switch ($estruct) {
    case 'Tipo':
        $query = "SELECT $ColEstrucCod AS 'id' FROM $ColEstruc WHERE PERSONAL.LegNume > 0 $FilterEstruct $filtros GROUP BY $ColEstrucCod ORDER BY $ColEstrucCod";
        break;
    case 'Lega':
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM PERSONAL WHERE $FicEstruct > 0 $FiltroQ $FilterEstruct $filtros GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
        break;
    case 'Sec2':
        $FiltroQ  = (!empty($q)) ? "AND CONCAT(SECCION.SecCodi, SECCION.Se2Desc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';
        $query = "SELECT PERSONAL.LegSec2 AS 'id', CONCAT(PERSONAL.LegSect, PERSONAL.LegSec2) AS 'id2', SECCION.Se2Desc AS 'Desc' 
        FROM PERSONAL 
        INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND PERSONAL.LegSect=SECCION.SecCodi 
        WHERE PERSONAL.LegSec2 > 0 $FiltroQ $FilterEstruct $filtros 
        GROUP BY PERSONAL.LegSec2, CONCAT(PERSONAL.LegSect, PERSONAL.LegSec2), SECCION.Se2Desc 
        ORDER BY PERSONAL.LegSec2";
        break;
    default:
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM PERSONAL INNER JOIN $ColEstruc ON $FicEstruct=$ColEstrucCod 
        WHERE PERSONAL.LegNume > 0 $FiltroQ $FilterEstruct $filtros GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
        break;
}

// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        switch ($estruct) {
            case 'Tipo':
                $id   = $row['id'];
                $id2 = ($row['id']==0)?2:1;
                $text = ($id == '0') ? 'Mensuales' : 'Jornales';
                $data[] = array(
                    'id'    => $id2,
                    'text'  => $text,
                    'title' => $text,
                );
                break;
            case 'Sec2':
                $id   = $row['id'];
                $id2  = $row['id2'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id'    => $id2,
                    'text'  => $id . ' - ' . $text,
                    'title' => $id . ' - ' . $text,
                );
                break;
            default:
                $id   = $row['id'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id'    => $id,
                    'text'  => $id . ' - ' . $text,
                    'title' => $id . ' - ' . $text,
                    'data-title' => $id . ' - ' . $text,
                );
                break;
        }
        
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
