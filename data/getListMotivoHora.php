<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '../../config/index.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');


require_once __DIR__ . '../../config/conect_mssql.php';

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$_POST['Fic1Hora'] = $_POST['Fic1Hora'] ?? '';

$_POST['q'] = $_POST['q'] ?? '';
$q = test_input($_POST['q']);

$data = array();

$q = (ValString($_POST['q'])) ? $_POST['q'] : exit;
$Fic1Hora = (ValNumerico($_POST['Fic1Hora'])) ? $_POST['Fic1Hora'] : exit;

$query="SELECT THoCCodi,THoCDesc FROM TIPOHORACAUSA WHERE THoCHora = '$Fic1Hora' AND THoCDesc LIKE '%$q%' AND THoCCodi > 0 ORDER BY THoCCodi";
$result  = sqlsrv_query($link, $query, $params, $options);
// print_r($query); exit;

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :
        // $cod = str_pad($row['Codigo'], 3, "0", STR_PAD_LEFT);
        $data[] = array(
            'id'       => $row['THoCCodi'],
            'text'     => $row['THoCDesc'],
        );
    endwhile;
    sqlsrv_free_stmt($result);
}

sqlsrv_close($link);
echo json_encode(($data)); 
