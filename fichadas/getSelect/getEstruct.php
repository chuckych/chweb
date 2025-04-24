<?php
require __DIR__ . '/../../config/session_start.php';
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
        $FicEstruct = 'FICHAS.FicGrup';
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
        $FicEstruct = 'FICHAS.Ficlega';
        $ColEstruc = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod = 'PERSONAL.LegNume';
        break;
    case 'Tipo':
        $ColEstruc = 'PERSONAL';
        $ColEstrucCod = 'PERSONAL.LegTipo';
        break;

    default:
        # code...
        break;
}



$FiltroQ = (!empty($q)) ? "AND CONCAT($FicEstruct, $ColEstrucDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';

switch ($estruct) {
    case 'Tipo':
        $query = "SELECT $ColEstrucCod AS 'id' FROM $ColEstruc INNER JOIN REGISTRO ON PERSONAL.LegNume=REGISTRO.RegLega INNER JOIN FICHAS ON REGISTRO.RegLega=FICHAS.FicLega AND FICHAS.FicFech=REGISTRO.RegFeAs WHERE FICHAS.Ficfech BETWEEN '$FechaIni' AND '$FechaFin' GROUP BY $ColEstrucCod ORDER BY $ColEstrucCod";
        break;
    case 'Lega':
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM FICHAS INNER JOIN $ColEstruc ON $FicEstruct=$ColEstrucCod INNER JOIN REGISTRO ON FICHAS.FicLega=REGISTRO.RegLega AND FICHAS.FicFech=REGISTRO.RegFeAs WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $filtros GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
        break;
    case 'Sec2':
        $FiltroQ = (!empty($q)) ? "AND CONCAT(FICHAS.FicSec2, SECCION.Se2Desc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';
        $query = "SELECT FICHAS.FicSec2 AS 'id', CONCAT(FICHAS.FicSect ,FICHAS.FicSec2) AS 'id2', SECCION.Se2Desc AS 'Desc' FROM FICHAS INNER JOIN REGISTRO ON FICHAS.FicLega=REGISTRO.RegLega AND FICHAS.FicFech=REGISTRO.RegFeAs INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume INNER JOIN SECCION ON FICHAS.FicSec2=SECCION.Se2Codi AND FICHAS.FicSect=SECCION.SecCodi WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $filtros GROUP BY FICHAS.FicSec2, CONCAT(FICHAS.FicSect ,FICHAS.FicSec2), SECCION.Se2Desc ORDER BY FICHAS.FicSec2";
        break;
    default:
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume INNER JOIN $ColEstruc ON $FicEstruct=$ColEstrucCod INNER JOIN REGISTRO ON FICHAS.FicLega=REGISTRO.RegLega AND FICHAS.FicFech=REGISTRO.RegFeAs WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $filtros GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
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
                $id2 = $row['id2'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id' => $id2,
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
