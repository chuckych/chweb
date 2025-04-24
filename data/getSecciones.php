<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/index.php';
UnsetGet('q');
UnsetGet('sect');
// session_start();
$respuesta = '';
require_once __DIR__ . '/../config/conect_mssql.php';
$q = $_GET['q'];
$sector = $_GET['sect'];
$query = "SELECT SECCION.Se2Desc, SECCION.Se2Codi FROM SECCION WHERE SECCION.SecCodi = '$sector' AND SECCION.Se2Desc LIKE '%$q%' AND SECCION.Se2Codi > '0'";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $Se2Codi = $fila['Se2Codi'];
        $Se2Desc = empty($fila['Se2Desc']) ? '-' : $fila['Se2Desc'];
        $data[] = array(
            'id' => $Se2Codi,
            'text' => $Se2Desc,
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
