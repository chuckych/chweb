<?php
require __DIR__ . '/../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '/../valores.php';

require __DIR__ . '/../../filtros/filtros.php';
require __DIR__ . '/../../config/conect_mssql.php';

$id = 'FICHAS.FicLega';
$Desc = 'PERSONAL.LegApNo';
$DescCodi = 'PERSONAL.LegNume';
$Col = 'PERSONAL';
$ColData = 'FICHAS1';
$FiltroQ = (!empty($q)) ? "AND CONCAT($id, $Desc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';

$query = "SELECT TOP 100 $id AS 'id', $Desc AS 'Desc' FROM $ColData INNER JOIN FICHAS ON $ColData.FicLega=FICHAS.FicLega AND $ColData.FicFech=FICHAS.FicFech AND $ColData.FicTurn=FICHAS.FicTurn INNER JOIN $Col ON $id=$DescCodi WHERE $ColData.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND $id >0 $FiltroQ $FilterEstruct $FiltrosFichas GROUP BY $id, $Desc ORDER BY $Desc";

// print_r($query); exit;

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result = sqlsrv_query($link, $query, $params, $options);
$data = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)):

        $id = $row['id'];
        $text = $row['Desc'];

        $data[] = array(
            'id' => $id,
            'text' => $id . ' - ' . $text,
            'title' => $id . ' - ' . $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
