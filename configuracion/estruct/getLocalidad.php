<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");

// require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';

$data = array();

error_reporting(E_ALL);
ini_set('display_errors', '1');

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$query = "SELECT LOCALIDA.LocDesc AS 'descripcion', LOCALIDA.LocCodi AS 'codigo' FROM LOCALIDA WHERE LOCALIDA.LocCodi > 0";
// print_r($query).PHP_EOL; exit;
$rs = sqlsrv_query($link, $query, $params, $options);
if (sqlsrv_num_rows($rs) > 0) {
    while ($r = sqlsrv_fetch_array($rs)) :

        $descripcion = $r['descripcion'];
        $codigo      = $r['codigo'];

        $data[] = array($codigo, $descripcion);
    endwhile;
}
sqlsrv_free_stmt($rs);
sqlsrv_close($link);


$json_data = array(
    "data" => $data,
);

echo json_encode($json_data);
exit;
