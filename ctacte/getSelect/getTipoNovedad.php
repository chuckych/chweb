<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../valores.php';

require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';

$query="SELECT FICHAS3.FicNoTi AS 'id' FROM FICHAS3 INNER JOIN PERSONAL ON FICHAS3.FicLega=PERSONAL.LegNume INNER JOIN FICHAS ON FICHAS3.FicFech = FICHAS.FicFech AND FICHAS3.FicLega = FICHAS.FicLega WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FilterEstruct2 $FiltrosFichas AND FICHAS3.FicTurn=1 GROUP BY FICHAS3.FicNoTi ORDER BY FICHAS3.FicNoTi";

// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['id'];
        $text = TipoNov($row['id']);

        $data[] = array(
            'id'    => $id,
            'text'  => $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
