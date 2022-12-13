<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../../config/index.php';
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$params['notPlano'] = $params['notPlano'] ?? [];
$q = $params['q'];
$data = array();
$where_condition = '';


$where_condition .= (!empty($params['_c'])) ? " AND clientes.recid = '$params[_c]'" : "";
$where_condition .= " AND proy_planos.Cliente = '$_SESSION[ID_CLIENTE]'";
$where_condition .= " AND proy_planos.PlanoEsta = '0'";

if ($params['notPlano']) {
    $selPlano = implode(',',$params['notPlano']);
    $where_condition .= " AND proy_planos.PlanoID NOT IN ($selPlano)";
}

$FiltroQ = (!empty($q)) ? " AND proy_planos.PlanoDesc LIKE '%$q%'" : '';
$query = "SELECT PlanoID, PlanoDesc, PlanoCod, PlanoObs FROM proy_planos WHERE proy_planos.PlanoID > 0 AND proy_planos.Cliente = '$_SESSION[ID_CLIENTE]'";
$query .= $FiltroQ;
$query .= $where_condition;
$query .= ' ORDER BY proy_planos.PlanoID DESC';
$query .= ' LIMIT 50';
$r = array_pdoQuery($query);

foreach ($r as $key => $row) {
    // $html = "<span class='font08'>$row[PlanoDesc]</span><br><span class='text-mutted font08'>$row[PlanoCod]</span>";
    // $html = "<span class='font08'>$row[PlanoDesc]</span>";
    $data[] = array(
        'id'   => $row['PlanoID'],
        'text' => utf8str($row['PlanoDesc']),
        'cod'  => utf8str($row['PlanoCod']),
        'obs'  => utf8str($row['PlanoObs']),
        // 'html'  => $html
    );
}

echo json_encode($data);
exit;
