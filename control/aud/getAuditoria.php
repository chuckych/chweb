<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
// sleep(1);
$totalRecords = $data = array();
$params = $_REQUEST;
$params['_dr'] = $params['_dr'] ?? '';


$tiempo_inicio = microtime(true);
$objCuentas = array_pdoQuery("SELECT clientes.id, clientes.nombre FROM clientes");
$objModulo = array_pdoQuery("SELECT modulos.id, modulos.nombre FROM modulos");
// print_r($cuentas); exit;

$where_condition = $sqlTot = $sqlRec = "";
if (!empty($params['search']['value'])) {
    $where_condition .=    " AND auditoria.dato LIKE '%" . $params['search']['value'] . "%'";
}
if (empty($params['_dr'])) {
    $where_condition .= " AND auditoria.fecha = (SELECT MAX(fecha) FROM auditoria)";
}else{
    $DateRange = explode(' al ', $params['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
    $where_condition .= " AND auditoria.fecha BETWEEN '$FechaIni' AND '$FechaFin'";
}

$query = "SELECT * FROM auditoria WHERE auditoria.id > 0";
$queryCount = "SELECT COUNT(*) as 'count' FROM auditoria WHERE auditoria.id > 0";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $queryCount .= $where_condition;
}

$query .=  " ORDER BY auditoria.id desc LIMIT " . $params['start'] . " ," . $params['length'] . " ";
$totalRecords = simple_pdoQuery($queryCount);
$count = $totalRecords['count'];
$records = array_pdoQuery($query);
// print_r($records); exit;

foreach ($records as $key => $row) {

    $audcuenta        = $row['audcuenta'];
    $audcuenta_nombre = filtrarObjeto($objCuentas, 'id', $audcuenta);
    $cuenta           = $row['cuenta'];
    $cuenta_nombre    = filtrarObjeto($objCuentas, 'id', $cuenta);
    $dato             = $row['dato'];
    $fecha            = $row['fecha'];
    $fechahora        = $row['fechahora'];
    $hora             = $row['hora'];
    $id_sesion        = $row['id_sesion'];
    $modulo           = $row['modulo'];
    $modulo_nombre    = filtrarObjeto($objModulo, 'id', $modulo);
    $nombre           = $row['nombre'];
    $tipo             = ($row['tipo']);
    $usuario          = $row['usuario'];

    $data[] = array(
        "id_sesion"        => $id_sesion,
        "usuario"          => $usuario,
        "nombre"           => $nombre,
        "cuenta"           => $cuenta,
        "cuenta_nombre"    => $cuenta_nombre['nombre'],
        "audcuenta"        => $audcuenta,
        "audcuenta_nombre" => $audcuenta_nombre['nombre'],
        "fecha"            => $fecha,
        "hora"             => $hora,
        "tipo"             => $tipo,
        "dato"             => $dato,
        "modulo"           => $modulo,
        "modulo_nombre"    => $modulo_nombre['nombre'],
    );
}

$tiempo_fin = microtime(true);
$tiempo = ($tiempo_fin - $tiempo_inicio);

$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($count),
    "recordsFiltered" => intval($count),
    "data"            => $data,
    "tiempo"          => round($tiempo, 2),
    "_dr" => $params['_dr']
);
echo json_encode($json_data);
