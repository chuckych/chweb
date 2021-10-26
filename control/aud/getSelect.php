<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$params['d'] = $params['d'] ?? '';
$q = $params['q'];
$data = array();

switch ($params['d']) {
    case 'nombre':
        $FiltroQ = (!empty($q)) ? " AND auditoria.nombre LIKE '%$q%'" : '';
        $query = "SELECT DISTINCT(auditoria.nombre) as 'nombre' FROM auditoria WHERE auditoria.id > 0";
        $query .= $FiltroQ;
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
        $query .= ' ORDER BY auditoria.audcuenta';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {
            $data[] = array(
                'id'    => $row['audid'],
                'text'  => tipoAud($row['nombre'])
            );
        }
        break;

    default:
        # code...
        break;
}

echo json_encode($data);
