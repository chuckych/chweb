<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();
// sleep(1);
$totalRecords = $data = array();
$params = $_REQUEST;
$params['_dr']         = $params['_dr'] ?? '';
$params['nombreAud']   = $params['nombreAud'] ?? '';
$params['tipoAud']     = $params['tipoAud'] ?? '';
$params['userAud']     = $params['userAud'] ?? '';
$params['idSesionAud'] = $params['idSesionAud'] ?? '';
$params['horaAud']     = $params['horaAud'] ?? '00:00:00';
$params['horaAud2']    = $params['horaAud2'] ?? '23:59:59';
$params['cuentaAud']   = $params['cuentaAud'] ?? '';

$params['horaAud'] = ($params['horaAud'] == '') ? '00:00:00' : $params['horaAud'];
$params['horaAud2'] = ($params['horaAud2'] == '') ? '23:59:59' : $params['horaAud2'];

$tiempo_inicio = microtime(true);
$objCuentas    = array_pdoQuery("SELECT clientes.id, clientes.nombre FROM clientes");
$objModulo     = array_pdoQuery("SELECT modulos.id, modulos.nombre FROM modulos");

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

$where_condition .= (!empty($params['nombreAud'])) ? " AND auditoria.nombre = '" . $params['nombreAud'] . "'" : "";
$where_condition .= (!empty($params['tipoAud'])) ? " AND auditoria.tipo = '" . $params['tipoAud'] . "'" : "";
$where_condition .= (!empty($params['userAud'])) ? " AND auditoria.usuario = '" . $params['userAud'] . "'" : "";
$where_condition .= (!empty($params['idSesionAud'])) ? " AND auditoria.id_sesion = '" . $params['idSesionAud'] . "'" : "";
// $where_condition .= (!empty($params['horaAud'])) ? " AND auditoria.hora LIKE '%" . $params['horaAud'] . "%'" : "";
$where_condition .= (!empty($params['cuentaAud'])) ? " AND auditoria.audcuenta = '" . $params['cuentaAud'] . "'" : "";
$where_condition .= " AND auditoria.hora BETWEEN  '" . $params['horaAud'] . "'" . " AND '" . $params['horaAud2'] . "'";

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
// print_r($query);exit;
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
    $id               = $row['id'];
    $modulo           = $row['modulo'];
    $modulo_nombre    = filtrarObjeto($objModulo, 'id', $modulo);
    $nombre           = $row['nombre'];
    $tipo             = ($row['tipo']);
    $usuario          = $row['usuario'];

    $data[] = array(
        "id"               => $id,
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
