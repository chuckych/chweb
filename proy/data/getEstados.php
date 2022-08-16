<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
header("Content-Type: application/json");
E_ALL();
$totalRecords = $data = array();
$params = $_REQUEST;
// sleep(3);

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND proy_estados.EstDesc LIKE '%" . $params['search']['value'] . "%'";
}

$query = "SELECT EstID, EstDesc, EstColor, EstTipo FROM proy_estados WHERE proy_estados.EstID > 0";
$queryCount = "SELECT COUNT(*) as 'count' FROM proy_estados WHERE proy_estados.EstID > 0";

$where_condition .= " AND proy_estados.Cliente = '$_SESSION[ID_CLIENTE]'";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $queryCount .= $where_condition;
}

$query .=  " ORDER BY proy_estados.EstDesc LIMIT " . $params['start'] . " ," . $params['length'] . " ";
$totalRecords = simple_pdoQuery($queryCount);
$count = $totalRecords['count'];
$records = array_pdoQuery($query);
foreach ($records as $key => $row) {

    $EstID    = $row['EstID'];
    $EstDesc  = $row['EstDesc'];
    $EstColor = $row['EstColor'];
    $EstTipo  = $row['EstTipo'];

    $data[] = array(
        "EstID"    => $EstID,
        "EstDesc"  => $EstDesc,
        "EstColor" => $EstColor,
        "EstTipo"  => $EstTipo
    );
}

$tiempo_fin = microtime(true);
$tiempo = ($tiempo_fin - $tiempo_inicio);

$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($count),
    "recordsFiltered" => intval($count),
    "data"            => $data,
    "tiempo"          => round($tiempo, 2)
);
echo json_encode($json_data);
exit;
