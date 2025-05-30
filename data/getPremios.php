<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
UnsetGet('q');
$respuesta = '';
require_once __DIR__ . '/../config/conect_mssql.php';
$q = $_GET['q'];
$query = "SELECT PREMIOS.PreDesc, PREMIOS.PreCodi FROM PREMIOS WHERE PREMIOS.PreDesc LIKE '%$q%' AND PREMIOS.PreCodi > '0'";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $PreCodi = $fila['PreCodi'];
        $PreDesc = empty($fila['PreDesc']) ? '-' : $fila['PreDesc'];
        $data[] = array(
            'id' => $PreCodi,
            'text' => $PreDesc,
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
