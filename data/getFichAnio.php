<?php
session_start();
// header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

FusNuloPOST('q', '');
$q = $_POST['q'];

$query = "SELECT DATEPART(YY, FICHAS.FicFech) AS anio FROM FICHAS WHERE DATEPART(YY, FICHAS.FicFech) LIKE '%$q%'
GROUP BY DATEPART(YY, FICHAS.FicFech) ORDER BY DATEPART(YY, FICHAS.FicFech) Desc";
// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['anio'];
        $text = $row['anio'];

        $data[] = array(
            'id'   => $id,
            'text' => $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
