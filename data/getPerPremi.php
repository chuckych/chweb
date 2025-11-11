<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
E_ALL();
require __DIR__ . '/../config/conect_mssql.php';
$legajo = $_GET['q2'] ?? '';

$data = [];
try {

    if ($legajo == '' || $legajo === null || !is_numeric($legajo)) {
        throw new Exception('Legajo es obligatorio', 1);
    }

    $query = "SELECT PERPREMI.LPreLega,PERPREMI.LPreCodi, PERPREMI.FechaHora, PREMIOS.PreDesc
        FROM PERPREMI
        INNER JOIN PREMIOS ON PERPREMI.LPreCodi = PREMIOS.PreCodi
        WHERE PERPREMI.LPreLega = '$legajo'
        ORDER BY PERPREMI.LPreCodi";

    $params = [];
    $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
    $result = sqlsrv_query($link, $query, $params, $options) ?? [];
    $icon_trash = imgIcon('trash3', 'Eliminar registro ', 'w15 opa5');

    if (!$result) {
        throw new Exception('Error en la consulta SQL', 1);
    }
    
    if (sqlsrv_num_rows($result) === 0) {
        throw new Exception('No se encontraron registros', 1);
    }

    while ($fila = sqlsrv_fetch_array($result)) {
        $LPreLega = $fila['LPreLega'];
        $LPreCodi = $fila['LPreCodi'];
        $PreDesc = $fila['PreDesc'];
        $FechaHora = $fila['FechaHora']->format('d/m/Y H:i');
        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_perpremi" data="' . $LPreLega . '" data2="' . $LPreCodi . '" data3="true">' . $icon_trash . '</a></div>';

        $data[] = [
            "LPreLega" => $LPreLega,
            "LPreCodi" => $LPreCodi,
            "PreDesc" => $PreDesc,
            "FechaHora" => $FechaHora,
            "eliminar" => $eliminar,
            "null" => ''
        ];
    }
    sqlsrv_free_stmt($result);
    echo json_encode(["data" => $data]);
} catch (\Throwable $th) {
    $data['error'] = $th->getMessage();
    echo json_encode(["data" => $data]);
}
