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

FusNuloPOST('q', '');
$q = test_input($_POST['q']);

$FiltroQ  = (!empty($q)) ? "AND CONCAT(NOVEDAD.NovCodi, NOVEDAD.NovDesc) LIKE '%$q%'":'';

  $query="SELECT NOVEDAD.NovCodi AS 'id', NOVEDAD.NovDesc AS 'text' FROM NOVEDAD WHERE NOVEDAD.NovCodi > 0 $FiltroQ ORDER BY NOVEDAD.NovCodi";

// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['id'];
        $text = $row['text'];

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
