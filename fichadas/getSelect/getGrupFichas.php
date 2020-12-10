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

$id       = 'PERSONAL.LegGrup';
$Desc     = 'GRUPOS.GruDesc';
$DescCodi = 'GRUPOS.GruCodi';
$Col      = 'GRUPOS';
$ColData  = 'REGISTRO';
$FiltroQ  = (!empty($q)) ? "AND CONCAT($id, $Desc) LIKE '%$q%'":'';

$query="SELECT $id AS 'id', $Desc AS 'Desc' FROM $ColData INNER JOIN PERSONAL ON $ColData.RegLega=PERSONAL.LegNume INNER JOIN $Col ON $id=$DescCodi WHERE $ColData.RegFeAs BETWEEN '$FechaIni' AND '$FechaFin' AND $DescCodi >0 $FiltroQ $FilterEstruct $filtros GROUP BY $id, $Desc ORDER BY $Desc";

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
