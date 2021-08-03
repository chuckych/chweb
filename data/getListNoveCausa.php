<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '../../config/index.php';
session_start();
E_ALL();
require_once __DIR__ . '../../config/conect_mssql.php';

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$_POST['q'] = $_POST['q'] ?? '';
$q = test_input($_POST['q']);

$data = array();

$q = (ValString($_POST['q'])) ? $_POST['q'] : exit;
$NovCNove = (ValNumerico($_POST['NovCNove'])) ? $_POST['NovCNove'] : exit;


$query="SELECT NOVECAUSA.NovCCodi AS Codigo ,NOVECAUSA.NovCDesc AS Descripcion FROM NOVECAUSA WHERE NOVECAUSA.NovCNove='$NovCNove' AND NOVECAUSA.NovCCodi >0 AND NOVECAUSA.NovCDesc LIKE '%$q%' AND NOVECAUSA.NovCCodi > 0 ORDER BY NOVECAUSA.NovCCodi";

$result  = sqlsrv_query($link, $query, $params, $options);
// print_r($query); exit;

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :
        // $cod = str_pad($row['Codigo'], 3, "0", STR_PAD_LEFT);
        $data[] = array(
            'id'       => $row['Codigo'],
            'text'     => $row['Descripcion'],
        );
    endwhile;
    sqlsrv_free_stmt($result);
}

sqlsrv_close($link);
echo json_encode(($data)); 
