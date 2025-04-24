<?php
// require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/../../config/conect_mssql.php';
// E_ALL();
$data = array();

$params = $_REQUEST;

require '/filtros.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$params = $columns = $totalRecords = '';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT FICHAS1.Ficlega AS 'Legajo', PERSONAL.LegTipo 'JM', SUCURSALES.SucDesc AS 'Compania', GRUPOS.GruDesc AS 'Seccion', SECCION.Se2Desc AS 'Sector/Linea', (ISNULL(SUM(CASE WHEN FICHAS1.FicHora=90 THEN (dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) ELSE 0 END),0)) AS '90', (ISNULL(SUM(CASE WHEN FICHAS1.FicHora=3 THEN (dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) ELSE 0 END),0)) AS '3', (ISNULL(SUM(CASE WHEN FICHAS1.FicHora=80 THEN (dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) ELSE 0 END),0)) AS '80', (ISNULL(SUM(CASE WHEN FICHAS1.FicHora=70 THEN (dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) ELSE 0 END),0)) AS '70', (ISNULL(SUM(CASE WHEN FICHAS1.FicHora=50 THEN (dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) ELSE 0 END),0)) AS '50', (ISNULL(SUM(CASE WHEN FICHAS1.FicHora=2 THEN (dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) ELSE 0 END),0)) AS '2', (ISNULL(SUM(CASE WHEN FICHAS1.FicHora=4 THEN (dbo.fn_STRMinutos(FICHAS1.FicHsAu)) ELSE 0 END),0)) AS '4', (SELECT ISNULL(SUM(dbo.fn_STRMinutos(FICHAS3.FicHoras)),0) FROM FICHAS3 INNER JOIN PERSONAL ON FICHAS3.FicLega=PERSONAL.LegNume INNER JOIN FICHAS ON FICHAS3.FicLega=FICHAS.FicLega AND FICHAS3.FicFech=FICHAS.FicFech AND FICHAS3.FicTurn=FICHAS.FicTurn WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS3.FicLega=FICHAS1.Ficlega AND FICHAS3.FicNove IN(2,5,6,7,9,10,202,205,206,207,209,212,304,307,422,425,435) $FilterEstruct) AS 'nov' FROM FICHAS1 INNER JOIN PERSONAL ON FICHAS1.FicLega=PERSONAL.LegNume INNER JOIN FICHAS ON FICHAS1.FicLega=FICHAS.FicLega AND FICHAS1.FicFech=FICHAS.FicFech AND FICHAS1.FicTurn=FICHAS.FicTurn INNER JOIN SUCURSALES ON FICHAS.FicSucu=SUCURSALES.SucCodi INNER JOIN SECCION ON FICHAS.FicSect=SECCION.SecCodi AND FICHAS.FicSec2=SECCION.Se2Codi INNER JOIN GRUPOS ON FICHAS.FicGrup=GRUPOS.GruCodi WHERE FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND (dbo.fn_STRMinutos(FICHAS1.FicHsAu) + dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) >0 AND FICHAS1.FicHora IN (90,3,80,70,50,2,4) $FilterEstruct GROUP BY FICHAS1.Ficlega, GRUPOS.GruDesc, SECCION.Se2Desc, PERSONAL.LegTipo, SUCURSALES.SucDesc ORDER BY FICHAS1.Ficlega";
// print_r($sql_query);exit;

$queryRecords = sqlsrv_query($link, $sql_query, $param, $options);
$totalRecords = sqlsrv_num_rows($queryRecords);

while ($r = sqlsrv_fetch_array($queryRecords)):
    // $trNorm = ((($r['90']-$r['3'])+($r['4']-($r['80']+$r['70']+$r['50']+$r['2'])))/60);
    $trNorm = ((($r['90'] - $r['3']) + ($r['4'] - ($r['80'] + $r['70'] + $r['50'] + $r['2'])) - $r['nov']) / 60);
    $trNorm = round($trNorm, 2);
    $trExtras = ((($r['80'] + $r['70'] + $r['50'] + $r['2'])) / 60);
    $trExtras = round($trExtras, 2);
    $capExtras = ($r['2'] / 60);
    $capExtras = round($capExtras, 2);
    $subExtras = ((($r['80'] + $r['70'] + $r['50'])) / 60);
    $subExtras = round($subExtras, 2);


    $data[] = array(
        'legajo' => $r['Legajo'],
        'jm' => ($r['JM'] == 1) ? 'J' : 'M',
        'compania' => $r['Compania'],
        'seccion' => $r['Seccion'],
        'sectorLinea' => $r['Sector/Linea'],
        '90' => $r['90'],
        '3' => $r['3'],
        '80' => $r['80'],
        '70' => $r['70'],
        '50' => $r['50'],
        '2' => $r['2'],
        '4' => $r['4'],
        'trNorm' => ($trNorm > 0) ? $trNorm : '',
        'trExtras' => ($trExtras > 0) ? $trExtras : '',
        'capExtras' => ($capExtras > 0) ? $capExtras : '',
        'subExtras' => ($subExtras > 0) ? $subExtras : '',
    );
endwhile;
// echo json_encode($data, JSON_PRETTY_PRINT);
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
