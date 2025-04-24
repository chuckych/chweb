<?php
E_ALL();
require __DIR__ . '/../../../config/conect_mssql.php';
$dataTotHorLeg = array();

$legajo = $valueAgrup['Legajo'];
$fecha = $valueAgrup['Fecha'];

//require __DIR__ . '/../valores.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$PorLegajo = ($_Por == 'Fech') ? '' : "AND FICHAS1.FicLega = '$legajo'"; /** para filtrar por legajo */
$PorFecha = ($_Por == 'Fech') ? "WHERE FICHAS1.FicFech = '$fecha'" : "WHERE FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin'"; /** Para filtrra por Fecha desde o desde hasta */

if ($_Por == 'Fech') { /** Si agrupamos por Fecha */
    $sql_query = "SELECT TIPOHORA.THoCodi AS 'Hora', TIPOHORA.THoDesc2 AS 'HoraDesc2', TIPOHORA.THoDesc AS 'HoraDesc', (
        SELECT dbo.fn_MinutosSTR(SUM(dbo.fn_STRMinutos(FICHAS1.FicHsAu2)))
        FROM FICHAS1
        INNER JOIN FICHAS ON FICHAS1.FicLega = FICHAS.FicLega AND FICHAS1.FicFech = FICHAS.FicFech
        $PorFecha $PorLegajo $FilterEstruct AND FICHAS1.FicHora = TIPOHORA.THoCodi) AS 'HsAutorizadas',
        (
        SELECT dbo.fn_MinutosSTR(SUM(dbo.fn_STRMinutos(FICHAS1.FicHsAu)))
        FROM FICHAS1
        INNER JOIN FICHAS ON FICHAS1.FicLega = FICHAS.FicLega AND FICHAS1.FicFech = FICHAS.FicFech
        $PorFecha $PorLegajo $FilterEstruct AND FICHAS1.FicHora = TIPOHORA.THoCodi) AS 'HsHechas'
        FROM TIPOHORA
        WHERE TIPOHORA.THoColu > 0
        ORDER BY TIPOHORA.THoColu";
} else {
    $sql_query = "SELECT TIPOHORA.THoCodi AS 'Hora', TIPOHORA.THoDesc2 AS 'HoraDesc2', TIPOHORA.THoDesc AS 'HoraDesc', (
    SELECT dbo.fn_MinutosSTR(SUM(dbo.fn_STRMinutos(FICHAS1.FicHsAu2)))
    FROM FICHAS1
    INNER JOIN FICHAS ON FICHAS1.FicLega = FICHAS.FicLega AND FICHAS1.FicFech = FICHAS.FicFech
    $PorFecha $PorLegajo $FilterEstruct AND FICHAS1.FicHora = TIPOHORA.THoCodi) AS 'HsAutorizadas',
    (
    SELECT dbo.fn_MinutosSTR(SUM(dbo.fn_STRMinutos(FICHAS1.FicHsAu)))
    FROM FICHAS1
    INNER JOIN FICHAS ON FICHAS1.FicLega = FICHAS.FicLega AND FICHAS1.FicFech = FICHAS.FicFech
    $PorFecha $PorLegajo $FilterEstruct AND FICHAS1.FicHora = TIPOHORA.THoCodi) AS 'HsHechas'
    FROM TIPOHORA
    WHERE TIPOHORA.THoColu > 0
    ORDER BY TIPOHORA.THoColu";
}
// print_r($sql_query); exit;

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$queryRecords = sqlsrv_query($link, $sql_query, $param, $options);
while ($row = sqlsrv_fetch_array($queryRecords)) {

    $Hora = $row['Hora'];
    $HoraDesc = $row['HoraDesc'];
    $HsAutorizadas = $row['HsAutorizadas'];
    $HsHechas = $row['HsHechas'];
    // if(MinHora($HsAutorizadas)>'0'){
    $dataTotHorLeg[] = array(
        'Hora' => $Hora,
        'HoraDesc' => $HoraDesc,
        'HsAutorizadas' => $HsAutorizadas,
        'HsHechas' => $HsHechas,
    );
    // }
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
