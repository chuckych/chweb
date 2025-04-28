<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
header("Content-Type: application/json");
E_ALL();
$totalRecords = $data = array();
$params = $_REQUEST;
// sleep(1);

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";

if (!empty($params['search']['value'])) {
    $where_condition .= " AND proy_empresas.EmpDesc LIKE '%" . $params['search']['value'] . "%'";
}
$query = "SELECT EmpID, EmpDesc, EmpTel, EmpObs FROM proy_empresas WHERE proy_empresas.EmpID > 0";
$queryCount = "SELECT COUNT(*) as 'count' FROM proy_empresas WHERE proy_empresas.EmpID > 0";

$where_condition .= " AND proy_empresas.Cliente = '$_SESSION[ID_CLIENTE]'";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $queryCount .= $where_condition;
}

$query .= " ORDER BY proy_empresas.EmpDesc LIMIT " . $params['start'] . " ," . $params['length'] . " ";
$totalRecords = simple_pdoQuery($queryCount);
$count = $totalRecords['count'] ?? 0;
$records = array_pdoQuery($query) ?? [];
// print_r($records); exit;
// print_r($query);exit;
foreach ($records as $key => $row) {

    $EmpID = $row['EmpID'];
    $EmpDesc = $row['EmpDesc'];
    $EmpTel = $row['EmpTel'];
    $EmpObs = $row['EmpObs'];

    $data[] = array(
        "EmpID" => $EmpID,
        "EmpDesc" => $EmpDesc,
        "EmpTel" => $EmpTel,
        "EmpObs" => $EmpObs,
    );
}

$tiempo_fin = microtime(true);
$tiempo = ($tiempo_fin - $tiempo_inicio);

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($count),
    "recordsFiltered" => intval($count),
    "data" => $data,
    "tiempo" => round($tiempo, 2)
);
echo json_encode($json_data);
exit;
