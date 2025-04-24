<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
E_ALL();
UnsetGet('q2');
require __DIR__ . '/../config/conect_mssql.php';
// $q = $_GET['q'];
$q2 = $_GET['q2'];
$query = "SELECT PERPREMI.LPreLega,PERPREMI.LPreCodi, PERPREMI.FechaHora, PREMIOS.PreDesc
FROM PERPREMI
INNER JOIN PREMIOS ON PERPREMI.LPreCodi = PREMIOS.PreCodi
WHERE PERPREMI.LPreLega = '$q2'
ORDER BY PERPREMI.LPreCodi";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
$icon_trash = imgIcon('trash3', 'Eliminar registro ', 'w15 opa5');

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $LPreLega = $fila['LPreLega'];
        $LPreCodi = $fila['LPreCodi'];
        $PreDesc = $fila['PreDesc'];
        $FechaHora = $fila['FechaHora']->format('d/m/Y H:i');
        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_perpremi" data="' . $LPreLega . '" data2="' . $LPreCodi . '" data3="true">' . $icon_trash . '</a></div>';
        // $eliminar ='';
        $data[] = array(
            "LPreLega" => $LPreLega,
            "LPreCodi" => $LPreCodi,
            "PreDesc" => $PreDesc,
            "FechaHora" => $FechaHora,
            "eliminar" => $eliminar,
            "null" => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
