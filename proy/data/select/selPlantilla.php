<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../../config/index.php';
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$q = $params['q'];
$data = array();
$where_condition = '';

$where_condition .= (!empty($params['_c'])) ? " AND clientes.recid = '$params[_c]'" : "";
$where_condition .= " AND proy_plantillas.Cliente = '$_SESSION[ID_CLIENTE]'";

$FiltroQ = (!empty($q)) ? " AND proy_plantillas.PlantDesc LIKE '%$q%'" : '';
$query = "SELECT PlantID, PlantDesc FROM proy_plantillas WHERE proy_plantillas.PlantID > 0 AND proy_plantillas.Cliente = '$_SESSION[ID_CLIENTE]'";
$query .= $FiltroQ;
$query .= $where_condition;
$query .= ' ORDER BY proy_plantillas.PlantDesc';
$r = array_pdoQuery($query);

foreach ($r as $key => $row) {

    $data[] = array(
        'id'    => $row['PlantID'],
        'text'  => utf8str($row['PlantDesc']),
        // 'html'  => $html
    );
}

echo json_encode($data);
exit;
