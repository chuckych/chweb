<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/index.php';
UnsetGet('q');
// session_start();
$respuesta = '';
require_once __DIR__ . '/../config/conect_mssql.php';
$q = $_GET['q'];
$query = "SELECT CONVENIO.ConDesc, CONVENIO.ConCodi FROM CONVENIO WHERE CONVENIO.ConDesc LIKE '%$q%'";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $ConCodi = $fila['ConCodi'];
        $ConDesc = empty($fila['ConDesc']) ? '-' : $fila['ConDesc'];
        $data[] = array(
            'id' => $ConCodi,
            'text' => $ConDesc,
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
