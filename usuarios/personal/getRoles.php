<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['_c'] = $params['_c'] ?? '';
$params['q'] = $params['q'] ?? '';
$q = $params['q'];
$data = array();
$where_condition = '';

$params['_c']   = test_input($params['_c']);

$where_condition .= (!empty($params['_c'])) ? " AND clientes.recid = '$params[_c]'" : "";

$FiltroQ = (!empty($q)) ? " AND roles.nombre LIKE '%$q%'" : '';
$query = "SELECT roles.nombre, roles.id FROM roles INNER JOIN clientes ON roles.cliente = clientes.id WHERE roles.id > 0";
$query .= $FiltroQ;
$query .= $where_condition;
$query .= ' ORDER BY roles.nombre';
$r = array_pdoQuery($query);

foreach ($r as $key => $row) {
    $data[] = array(
        'id'    => $row['id'],
        'text'  => $row['nombre'],
        // 'html'  => '<div style="color:green">'.$row['nombre'].'</div>'
    );
}

echo json_encode($data);
