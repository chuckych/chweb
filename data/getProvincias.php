<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/index.php';
UnsetGet('q');
$respuesta = '';
require_once __DIR__ . '/../config/conect_mssql.php';
$q = $_GET['q'];
$query = "SELECT PROVINCI.ProCodi, PROVINCI.ProDesc FROM PROVINCI WHERE PROVINCI.ProDesc collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%' AND PROVINCI.ProCodi > '0'";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $ProCodi = $fila['ProCodi'];
        $ProDesc = empty($fila['ProDesc']) ? '-' : $fila['ProDesc'];
        $data[] = array(
            'id' => $ProCodi,
            'text' => $ProDesc,
        );
    }
} else {
    $data[] = array(
        'id' => false,
        'text' => false
    );
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
