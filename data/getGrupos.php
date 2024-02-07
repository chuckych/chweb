<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
UnsetGet('q');
require __DIR__ . '../../config/conect_mssql.php';
$q = $_GET['q'];
$query = "SELECT GRUPOS.GruDesc, GRUPOS.GruCodi FROM GRUPOS WHERE GRUPOS.GruDesc LIKE '%$q%'";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $GruCodi = $fila['GruCodi'];
        $GruDesc = empty($fila['GruDesc']) ? 'Sin Grupo' : $fila['GruDesc'];
        $data[] = array(
            'id' => $GruCodi,
            'text' => $GruDesc,
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
