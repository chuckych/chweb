<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
// sleep(1);
$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$tiempo_inicio = microtime(true);
$objCuentas = array_pdoQuery("SELECT clientes.id, clientes.nombre FROM clientes");
$objModulo = array_pdoQuery("SELECT modulos.id, modulos.nombre FROM modulos");
// print_r($cuentas); exit;

$where_condition = $sqlTot = $sqlRec = "";
if (!empty($params['search']['value'])) {
    $where_condition .=    " WHERE ";
}
$query = "SELECT * FROM auditoria";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $query .= $where_condition;
}
$query .=  " ORDER BY auditoria.id desc LIMIT " . $params['start'] . " ," . $params['length'] . " ";

$totalRecords = simple_pdoQuery("SELECT count(*) as 'count' FROM auditoria");
$totalRecords = $totalRecords['count'];
$records = array_pdoQuery($query);
// print_r($records); exit;

function nombreTipoProceso($tipo)
{
    switch ($tipo) {
        case 'P':
            $tipo = 'Proceso';
            break;
        case 'M':
            $tipo = 'Modificación';
            break;
        case 'A':
            $tipo = 'Alta';
            break;
        case 'B':
            $tipo = 'Baja';
            break;
        default:
            $tipo = $tipo;
            break;
    }
    return $tipo;
}

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
    "draw"               => intval($params['draw']),
    "recordsTotal"       => intval($totalRecords),
    "recordsFiltered"    => intval($totalRecords),
    "data"               => $data,
    "tiempo"             => round($tiempo, 2)
);
echo json_encode($json_data);
