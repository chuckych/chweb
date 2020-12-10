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

$id       = 'FICHAS.FicSec2';
$id2       = 'CONCAT(FICHAS.FicSect,FICHAS.FicSec2)';
$Desc     = 'SECCION.Se2Desc';
$DescCodi = 'SECCION.Se2Codi';
$Col      = 'SECCION';
$ColData  = 'FICHAS1';
$FiltroQ  = (!empty($q)) ? "AND CONCAT($id, $Desc) LIKE '%$q%'":'';

 $query="SELECT $id AS 'id', $id2 AS 'id2', $Desc AS 'Desc' FROM $ColData INNER JOIN FICHAS ON $ColData.FicLega=FICHAS.FicLega AND $ColData.FicFech = FICHAS.FicFech AND $ColData.FicTurn=FICHAS.FicTurn INNER JOIN $Col ON $id = $DescCodi AND FICHAS.FicSect=SECCION.SecCodi INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE $ColData.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND $id > 0 $FiltroQ $FilterEstruct $FiltrosFichas GROUP BY $id, $id2, $Desc ORDER BY $Desc";
// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['id'];
        $id2  = $row['id2'];
        $text = $row['Desc'];

        $data[] = array(
            'id'    => $id2,
            'text'  => $text,
            'title' => $id.' - '.$text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
