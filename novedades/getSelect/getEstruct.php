<?php
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
E_ALL();


$estruct = FusNuloPOST('estruct', '');

require __DIR__ . '/../valores.php';

require __DIR__ . '/../../filtros/filtros.php';
require __DIR__ . '/../../config/conect_mssql.php';

switch ($estruct) {
    case 'Empr':
        $FicEstruct = 'FICHAS.FicEmpr';
        $ColEstruc = 'EMPRESAS';
        $ColEstrucDesc = 'EMPRESAS.EmpRazon';
        $ColEstrucCod = 'EMPRESAS.EmpCodi';
        break;
    case 'Plan':
        $FicEstruct = 'FICHAS.FicPlan';
        $ColEstruc = 'PLANTAS';
        $ColEstrucDesc = 'PLANTAS.PlaDesc';
        $ColEstrucCod = 'PLANTAS.PlaCodi';
        break;
    case 'Grup':
        $FicEstruct = 'FICHAS.FicPlan';
        $ColEstruc = 'GRUPOS';
        $ColEstrucDesc = 'GRUPOS.GruDesc';
        $ColEstrucCod = 'GRUPOS.GruCodi';
        break;
    case 'Sect':
        $FicEstruct = 'FICHAS.FicSect';
        $ColEstruc = 'SECTORES';
        $ColEstrucDesc = 'SECTORES.SecDesc';
        $ColEstrucCod = 'SECTORES.SecCodi';
        break;
    case 'Sucu':
        $FicEstruct = 'FICHAS.FicSucu';
        $ColEstruc = 'SUCURSALES';
        $ColEstrucDesc = 'SUCURSALES.SucDesc';
        $ColEstrucCod = 'SUCURSALES.SucCodi';
        break;
    case 'Lega':
        $FicEstruct = 'FICHAS.Ficlega';
        $ColEstruc = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod = 'PERSONAL.LegNume';
        break;
    case 'Sec2':
        // $FicEstruct    = 'FICHAS.Ficlega';
        // $ColEstruc     = 'PERSONAL';
        // $ColEstrucDesc = 'PERSONAL.LegApNo';
        // $ColEstrucCod  = 'PERSONAL.LegNume';
        break;
    case 'Tipo':
        $ColEstruc = 'FICHAS';
        $ColEstrucCod = 'PERSONAL.LegTipo';
        break;

    default:
        # code...
        break;
}

$FiltroQ = (!empty($q)) ? "AND CONCAT($FicEstruct, $ColEstrucDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';

switch ($estruct) {
    case 'Tipo':
        $query = "SELECT $ColEstrucCod AS 'id' FROM $ColEstruc INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN NOVECAUSA ON FICHAS3.FicCaus = NOVECAUSA.NovCCodi AND FICHAS3.FicNove = NOVECAUSA.NovCNove WHERE FICHAS.Ficfech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas GROUP BY $ColEstrucCod ORDER BY $ColEstrucCod";
        break;
    case 'Lega':
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM FICHAS INNER JOIN $ColEstruc ON $FicEstruct=$ColEstrucCod INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN NOVECAUSA ON FICHAS3.FicCaus = NOVECAUSA.NovCCodi AND FICHAS3.FicNove = NOVECAUSA.NovCNove WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $FiltrosFichas GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
        break;
    case 'Sec2':
        $FiltroQ = (!empty($q)) ? "AND CONCAT(FICHAS.FicSec2, SECCION.Se2Desc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';
        $query = "SELECT FICHAS.FicSec2 AS 'id', CONCAT(FICHAS.FicSect ,FICHAS.FicSec2) AS 'id2', FICHAS.FicSect AS 'idsector', SECCION.Se2Desc AS 'Desc' FROM FICHAS INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume INNER JOIN SECCION ON FICHAS.FicSec2=SECCION.Se2Codi AND FICHAS.FicSect=SECCION.SecCodi INNER JOIN NOVECAUSA ON FICHAS3.FicCaus = NOVECAUSA.NovCCodi AND FICHAS3.FicNove = NOVECAUSA.NovCNove WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS.FicSec2 >0 $FiltroQ $FilterEstruct $FiltrosFichas GROUP BY FICHAS.FicSec2, CONCAT(FICHAS.FicSect ,FICHAS.FicSec2), FICHAS.FicSect, SECCION.Se2Desc ORDER BY FICHAS.FicSec2";
        break;
    case 'FicNoTi':
        $query = "SELECT FICHAS3.FicNoTi AS 'id' FROM FICHAS3 INNER JOIN PERSONAL ON FICHAS3.FicLega=PERSONAL.LegNume INNER JOIN FICHAS ON FICHAS3.FicFech=FICHAS.FicFech AND FICHAS3.FicLega=FICHAS.FicLega INNER JOIN NOVECAUSA ON FICHAS3.FicCaus = NOVECAUSA.NovCCodi AND FICHAS3.FicNove = NOVECAUSA.NovCNove WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas AND FICHAS3.FicTurn=1 GROUP BY FICHAS3.FicNoTi ORDER BY FICHAS3.FicNoTi";
        break;
    case 'FicNove':
        $FiltroQ = (!empty($q)) ? "AND CONCAT(FICHAS3.FicNove, NOVEDAD.NovDesc) LIKE '%$q%'" : '';
        $query = "SELECT FICHAS3.FicNove AS 'id', NOVEDAD.NovDesc AS 'Desc' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi INNER JOIN NOVECAUSA ON FICHAS3.FicCaus = NOVECAUSA.NovCCodi AND FICHAS3.FicNove = NOVECAUSA.NovCNove WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $FiltrosFichas GROUP BY FICHAS3.FicNove, NOVEDAD.NovDesc ORDER BY FICHAS3.FicNove";
        break;
    case 'FicCausa':
        // $FiltroQ  = (!empty($q)) ? "AND CONCAT(FICHAS3.FicCaus, NOVECAUSA.NovCDesc) LIKE '%$q%'":'';
        $query = "SELECT FICHAS3.FicCaus AS 'id', CONCAT(FICHAS3.FicNove ,FICHAS3.FicCaus) AS 'id2', NOVECAUSA.NovCDesc AS 'Desc', FICHAS3.FicNove AS 'idnovedad' FROM FICHAS INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume 
        INNER JOIN NOVECAUSA ON FICHAS3.FicCaus = NOVECAUSA.NovCCodi AND FICHAS3.FicNove = NOVECAUSA.NovCNove 
        WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS3.FicCaus > 0 $FiltroQ $FilterEstruct $FiltrosFichas GROUP BY FICHAS3.FicCaus, CONCAT(FICHAS3.FicNove ,FICHAS3.FicCaus), NOVECAUSA.NovCDesc, FICHAS3.FicNove  ORDER BY FICHAS3.FicCaus";
        break;
    default:
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM FICHAS 
        INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume 
        INNER JOIN $ColEstruc ON $FicEstruct=$ColEstrucCod 
        INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech
        WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $FilterEstruct2 $FiltrosFichas GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
        break;
}

// print_r($query); exit;

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result = sqlsrv_query($link, $query, $params, $options);
$data = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)):

        switch ($estruct) {
            case 'Tipo':
                $id = $row['id'];
                $text = ($id == '0') ? 'Mensuales' : 'Jornales';
                $data[] = array(
                    'id' => $id,
                    'text' => $text,
                    'title' => $text,
                );
                break;
            case 'Sec2':
                $id = $row['id'];
                $idsector = $row['idsector'];
                $id2 = $row['id2'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id' => $id2,
                    'text' => $idsector . ' - ' . $id . ' - ' . $text,
                    'title' => 'Sector: ' . $idsector . '. Sección: ' . $id . ' ' . $text,
                );
                break;
            case 'FicCausa':
                $id = $row['id'];
                $id2 = $row['id2'];
                $idnovedad = $row['idnovedad'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id' => $id2,
                    'text' => $idnovedad . ' - ' . $id . ' - ' . $text,
                    'title' => 'Nov: ' . $idnovedad . '. Causa: ' . $id . ' ' . $text,
                );
                break;
            case 'FicNoTi':
                $id = $row['id'];
                $text = TipoNov($row['id']);
                $data[] = array(
                    'id' => $id,
                    'text' => $id . ' - ' . $text,
                    'title' => $id . ' - ' . $text,
                );
                break;
            default:
                $id = $row['id'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id' => $id,
                    'text' => $id . ' - ' . $text,
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
