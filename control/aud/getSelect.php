<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$params['d'] = $params['d'] ?? '';
$q = $params['q'];
$data = array();
$where_condition = '';
if (empty($params['_dr'])) {
    $where_condition .= " AND auditoria.fecha = (SELECT MAX(fecha) FROM auditoria)";
} else {
    $DateRange = explode(' al ', $params['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
    $where_condition .= " AND auditoria.fecha BETWEEN '$FechaIni' AND '$FechaFin'";
}
$params['nombreAud']   = test_input($params['nombreAud']);
$params['tipoAud']     = test_input($params['tipoAud']);
$params['userAud']     = test_input($params['userAud']);
$params['idSesionAud'] = test_input($params['idSesionAud']);
$params['horaAud']     = test_input($params['horaAud']);
$params['cuentaAud']   = test_input($params['cuentaAud']);
$params['datosAud']    = test_input($params['datosAud']);

$where_condition .= (!empty($params['nombreAud'])) ? " AND auditoria.nombre = '" . $params['nombreAud'] . "'" : "";
$where_condition .= (!empty($params['tipoAud'])) ? " AND auditoria.tipo = '" . $params['tipoAud'] . "'" : "";
$where_condition .= (!empty($params['userAud'])) ? " AND auditoria.usuario = '" . $params['userAud'] . "'" : "";
$where_condition .= (!empty($params['idSesionAud'])) ? " AND auditoria.id_sesion = '" . $params['idSesionAud'] . "'" : "";
$where_condition .= (!empty($params['horaAud'])) ? " AND auditoria.hora LIKE '%" . $params['horaAud'] . "%'" : "";
$where_condition .= (!empty($params['cuentaAud'])) ? " AND auditoria.audcuenta = '" . $params['cuentaAud'] . "'" : "";
$where_condition .= (!empty($params['datosAud'])) ? " AND auditoria.dato LIKE '%" . $params['datosAud'] . "%'" : "";

switch ($params['d']) {
    case 'nombre':
        $FiltroQ = (!empty($q)) ? " AND auditoria.nombre LIKE '%$q%'" : '';
        $query = "SELECT DISTINCT(auditoria.nombre) as 'nombre' FROM auditoria WHERE auditoria.id > 0";
        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' LIMIT 20';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {
            $data[] = array(
                'id'    => $row['nombre'],
                'text'  => $row['nombre']
            );
        }
        break;
    case 'usuario':
        $FiltroQ = (!empty($q)) ? " AND auditoria.usuario LIKE '%$q%'" : '';
        $query = "SELECT DISTINCT(auditoria.usuario) as 'usuario' FROM auditoria WHERE auditoria.id > 0";
        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' LIMIT 20';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {
            $data[] = array(
                'id'    => $row['usuario'],
                'text'  => $row['usuario']
            );
        }
        break;
    case 'id_sesion':
        $FiltroQ = (!empty($q)) ? " AND auditoria.id_sesion LIKE '%$q%'" : '';
        $query = "SELECT DISTINCT(auditoria.id_sesion) as 'id_sesion' FROM auditoria WHERE auditoria.id > 0";
        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' LIMIT 20';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {
            $data[] = array(
                'id'    => $row['id_sesion'],
                'text'  => $row['id_sesion']
            );
        }
        break;
    case 'tipo':
        $FiltroQ = (!empty($q)) ? " AND auditoria.tipo LIKE '%$q%'" : '';
        $query = "SELECT DISTINCT(auditoria.tipo) as 'tipo' FROM auditoria WHERE auditoria.id > 0";
        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' ORDER BY auditoria.tipo';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {
            $data[] = array(
                'id'    => $row['tipo'],
                'text'  => tipoAud($row['tipo'])
            );
        }
        break;
    case 'audcuenta':
        $FiltroQ = (!empty($q)) ? " AND clientes.nombre LIKE '%$q%'" : '';
        $query = "SELECT DISTINCT(auditoria.audcuenta) as 'audid', clientes.nombre as 'nombre' FROM auditoria INNER JOIN clientes ON auditoria.audcuenta = clientes.id WHERE auditoria.id > 0";
        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' ORDER BY auditoria.audcuenta';

        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {
            $data[] = array(
                'id'    => $row['audid'],
                'text'  => ($row['nombre'])
            );
        }
        break;

    default:
        # code...
        break;
}

echo json_encode($data);
