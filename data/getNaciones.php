<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
UnsetGet('q');
$respuesta = '';
require_once __DIR__ . '/../config/conect_mssql.php';
$q = $_GET['q'];

$selected = $q2 = $_GET['q2'] ?? '' ? "AND NACIONES.NacCodi = '$_GET[q2]'" : '';


$query = "SELECT NACIONES.NacDesc, NACIONES.NacCodi FROM NACIONES WHERE NACIONES.NacDesc LIKE '%$q%' AND NACIONES.NacCodi > '0' $selected ";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $NacCodi = $fila['NacCodi'];
        $NacDesc = empty($fila['NacDesc']) ? '-' : $fila['NacDesc'];
        $data[] = array(
            'id' => $NacCodi,
            'text' => $NacDesc,
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
