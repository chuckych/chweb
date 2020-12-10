<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
require __DIR__ . '../../../config/conect_mssql.php';
$dataTotHorLeg = array();
$sql_query="SELECT TIPOHORA.THoCodi AS 'Hora', TIPOHORA.THoDesc2 AS 'HoraDesc2', TIPOHORA.THoDesc AS 'HoraDesc', (
    SELECT dbo.fn_MinutosSTR(SUM(dbo.fn_STRMinutos(FICHAS1.FicHsAu2)))
    FROM FICHAS1
    INNER JOIN FICHAS ON FICHAS1.FicLega = FICHAS.FicLega AND FICHAS1.FicFech = FICHAS.FicFech
    WHERE FICHAS1.FicLega = '$legajo' AND FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct AND FICHAS1.FicHora = TIPOHORA.THoCodi) AS 'HsAutorizadas'
    FROM TIPOHORA
    WHERE TIPOHORA.THoColu > 0
    ORDER BY TIPOHORA.THoColu";
    // print_r($sql_query); exit;

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$queryRecords = sqlsrv_query($link, $sql_query,$param, $options);
while ($row = sqlsrv_fetch_array($queryRecords)) {

    $Hora          = $row['Hora'];
    $HoraDesc      = $row['HoraDesc'];
    $HsAutorizadas = $row['HsAutorizadas'];
    // if(MinHora($HsAutorizadas)>'0'){
    $dataTotHorLeg[] = array(
        'Hora'          => $Hora,
        'HoraDesc'      => $HoraDesc,
        'HsAutorizadas' => $HsAutorizadas,
    );
// }
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
