<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
header("Content-Type: application/json");
E_ALL();
$totalRecords = $data = $count = array();
$params = $_REQUEST;
// sleep(1);
$params['start'] = $params['start'] ?? 0;
$params['length'] = $params['length'] ?? 9999;
$params['draw'] = $params['draw'] ?? 0;
$params['PlanoEsta'] = $params['PlanoEsta'] ?? '';

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND proy_planos.PlanoDesc LIKE '%" . $params['search']['value'] . "%'";
}
if (!empty($_POST['selectPlano'])) {
    if (!empty($_POST['q'])) {
        $where_condition .= (!empty($_POST['q'])) ? " AND CONCAT(PlanoID, PlanoDesc) LIKE '%$_POST[q]%'" : '';
    }
}
if (($params['PlanoEsta'] == '0')) {
    $where_condition .= " AND proy_planos.PlanoEsta = '0'";
}

$query = "SELECT PlanoID, PlanoDesc, PlanoCod, PlanoObs, PlanoEsta FROM proy_planos WHERE proy_planos.PlanoID > 0";
$queryCount = "SELECT COUNT(*) as 'count' FROM proy_planos WHERE proy_planos.PlanoID > 0";

$where_condition .= " AND proy_planos.Cliente = '$_SESSION[ID_CLIENTE]'";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $queryCount .= $where_condition;
}

$query .=  " ORDER BY proy_planos.PlanoEsta, proy_planos.PlanoDesc LIMIT " . $params['start'] . " ," . $params['length'] . " ";
if (empty($_POST['selectPlano'])) { // sino viene de un select
    $totalRecords = simple_pdoQuery($queryCount);
    $count = $totalRecords['count'];
}
$records = array_pdoQuery($query);
foreach ($records as $key => $row) {

    $PlanoID   = $row['PlanoID'];
    $PlanoDesc = utf8str($row['PlanoDesc']);
    $PlanoCod  = $row['PlanoCod'];
    $PlanoObs  = $row['PlanoObs'];
    $PlanoEsta = $row['PlanoEsta'];

    if (!empty($_POST['selectPlano'])) {
        $text = '(' . $PlanoID . ') ' . $PlanoDesc;
        if ($PlanoCod != '') {
            $html = "<span>$PlanoDesc</span><span class='badge bg-indigo-lt'>$PlanoCod</span>";
        } else {
            $html = "<span>$PlanoDesc</span><span class='badge bg-indigo-lt'>-</span>";
        }
        $data[] = array(
            'id'    => $PlanoID,
            'text'  => ($PlanoDesc),
            'title' => utf8str($text),
            'html'  => "<div class='w-100 d-flex justify-content-between'>$html</div>"
        );
    } else {
        $data[] = array(
            "PlanoID"   => $PlanoID,
            "PlanoDesc" => $PlanoDesc,
            "PlanoCod" => $PlanoCod,
            "PlanoObs"  => $PlanoObs,
            "PlanoEsta"  => $PlanoEsta
        );
    }
}

$tiempo_fin = microtime(true);
$tiempo = ($tiempo_fin - $tiempo_inicio);

if (!empty($_POST['selectPlano'])) {
    echo json_encode($data);
    exit;
}
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($count),
    "recordsFiltered" => intval($count),
    "data"            => $data,
    "tiempo"          => round($tiempo, 2)
);
echo json_encode($json_data);
exit;
