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

$id       = 'FICHAS.FicPlan';
$Desc     = 'PLANTAS.PlaDesc';
$DescCodi = 'PLANTAS.PlaCodi';
$Col      = 'PLANTAS';
$ColData  = 'FICHAS';
$FiltroQ  = (!empty($q)) ? "AND CONCAT($id, $Desc) LIKE '%$q%'":'';

 $query="SELECT $id AS 'id', $Desc AS 'Desc' FROM $ColData INNER JOIN FICHAS2 ON FICHAS.FicLega=FICHAS2.FicLega AND FICHAS.FicFech=FICHAS2.FicFech INNER JOIN $Col ON $id=$DescCodi INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume WHERE $ColData.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND $id >0 $FiltroQ $FilterEstruct $FilterEstruct2 $FiltrosFichas GROUP BY $id, $Desc ORDER BY $Desc";
// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['id'];
        $text = $row['Desc'];

        $data[] = array(
            'id'    => $id,
            'text'  => $text,
            'title' => $id.' - '.$text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
