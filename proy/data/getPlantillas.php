<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
header("Content-Type: application/json");
E_ALL();
$totalRecords = $data = array();
$params = $_REQUEST;
// sleep(1);

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND proy_plantillas.PlantDesc LIKE '%" . $params['search']['value'] . "%'";
}

$where_condition .= " AND proy_plantillas.Cliente = '$_SESSION[ID_CLIENTE]'";

$query = "SELECT PlantID, PlantDesc, PlaProcesos  FROM proy_plantillas 
LEFT JOIN proy_plantilla_proc ON proy_plantillas.PlantID = proy_plantilla_proc.PlaProPlan
WHERE proy_plantillas.PlantID > 0";
$queryCount = "SELECT COUNT(*) as 'count' FROM proy_plantillas WHERE proy_plantillas.PlantID > 0";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $queryCount .= $where_condition;
}

$query .=  " ORDER BY proy_plantillas.PlantDesc LIMIT " . $params['start'] . " ," . $params['length'] . " ";
$totalRecords = simple_pdoQuery($queryCount);
$count = $totalRecords['count'];
$records = array_pdoQuery($query);

foreach ($records as $key => $row) {

    $PlantID      = $row['PlantID'];
    $PlantDesc    = $row['PlantDesc'];
    $PlaProcesos  = $row['PlaProcesos'];
    $countProc    = explode(",", $PlaProcesos);
    $PlaCountProc = (!empty($PlaProcesos)) ? count($countProc): 0;

    $data[] = array(
        "PlantID"      => $PlantID,
        "PlantDesc"    => $PlantDesc,
        "PlaProcesos"  => $PlaProcesos,
        "PlaCountProc" => $PlaCountProc,
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
