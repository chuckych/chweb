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
$where_condition .= " AND usuarios.cliente = '$_SESSION[ID_CLIENTE]'";
$where_condition .= " AND usuarios.estado = '0'";

$FiltroQ = (!empty($q)) ? " AND usuarios.nombre LIKE '%$q%'" : '';
$query = "SELECT id, nombre FROM usuarios WHERE usuarios.id > 1";
$query .= $FiltroQ;
$query .= $where_condition;
$query .= ' ORDER BY usuarios.nombre';
$r = array_pdoQuery($query);

foreach ($r as $key => $row) {

    $data[] = array(
        'id'    => $row['id'],
        'text'  => utf8str($row['nombre']),
        // 'html'  => $html
    );
}

echo json_encode($data);
exit;
