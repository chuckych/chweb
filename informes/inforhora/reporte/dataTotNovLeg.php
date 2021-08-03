<?php
E_ALL();
require __DIR__ . '../../../../config/conect_mssql.php';
$dataTotNovLeg = array();
 $sql_query="SELECT FICHAS3.FicNove AS 'Cod', MAX(NOVEDAD.NovDesc) AS 'Novedad', MAX(NOVEDAD.NovTipo) AS 'Tipo', dbo.fn_MinutosSTR(SUM(dbo.fn_STRMinutos(FICHAS3.FicHoras))) AS 'Horas', COUNT(FICHAS3.FicNove) AS 'Dias' FROM FICHAS3,NOVEDAD,FICHAS,PERSONAL WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS3.FicLega='$legajo' AND FICHAS.FicLega=PERSONAL.LegNume AND FICHAS3.FicTurn=1 AND FICHAS3.FicNove >0 $FilterEstruct2 AND FICHAS3.FicNove=NOVEDAD.NovCodi AND FICHAS3.FicLega=FICHAS.FicLega AND FICHAS3.FicFech=FICHAS.FicFech AND FICHAS3.FicTurn=FICHAS.FicTurn GROUP BY FICHAS3.FicNove ORDER BY FICHAS3.FicNove";
    // print_r($sql_query); exit;

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$queryRecords = sqlsrv_query($link, $sql_query,$param, $options);
while ($row = sqlsrv_fetch_array($queryRecords)) {

    $Cod     = $row['Cod'];
    $Novedad = $row['Novedad'];
    $Tipo    = $row['Tipo'];
    $Horas   = $row['Horas'];
    $Dias    = $row['Dias'];
    // if(MinHora($HsAutorizadas)>'0'){
    $dataTotNovLeg[] = array(
        'Cod'     => $Cod,
        'Novedad' => $Novedad,
        'Tipo'    => TipoNov($Tipo),
        'Horas'   => $Horas,
        'Dias'    => $Dias,
    );
// }
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
