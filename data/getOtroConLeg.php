<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
UnsetGet('q');
require_once __DIR__ . '/../config/conect_mssql.php';
$q = $_GET['q'];
$query = "SELECT OTROCONCEP.OTROConDesc, OTROCONCEP.OTROConCodi FROM OTROCONCEP WHERE OTROCONCEP.OTROConDesc LIKE '%$q%' AND OTROCONCEP.OTROConCodi > '0'";

$params = [];
$options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
$result = sqlsrv_query($link, $query, $params, $options);
$data = [];
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $OTROConCodi = $fila['OTROConCodi'];
        $OTROConDesc = empty($fila['OTROConDesc']) ? '-' : $fila['OTROConDesc'];
        $data[] = [
            'id' => $OTROConCodi,
            'text' => $OTROConDesc,
        ];
    }
} else {
    $data[] = [
        'id' => false,
        'text' => false
    ];
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
