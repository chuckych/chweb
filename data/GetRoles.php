<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME,"es_ES");
require __DIR__ . '../../funciones.php';
E_ALL();
$respuesta   = '';
$token = token();
$recid = (isset($_GET['recid'])) ? "AND roles.recid='$_GET[recid]'" : "";
$recid_c = (isset($_GET['recid_c'])) ? "AND clientes.recid='$_GET[recid_c]'" : "";
if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        require __DIR__ . '../../config/conect_mysql.php';
        $query = "SELECT roles.id AS 'id', roles.recid AS 'recid', roles.nombre AS 'nombre', roles.fecha_alta AS 'fecha_alta', roles.cliente as 'id_cliente', clientes.nombre as 'cliente', clientes.recid as 'recid_cliente', roles.fecha AS 'fecha_mod', (SELECT COUNT(usuarios.rol) FROM usuarios WHERE roles.id = usuarios.rol) AS 'cant_roles',
        (SELECT COUNT(mod_roles.id) FROM mod_roles WHERE mod_roles.recid_rol = roles.recid) AS 'cant_modulos',
        (SELECT COUNT(sect_roles.id) FROM sect_roles WHERE sect_roles.recid_rol = roles.recid) AS 'cant_sectores',
        (SELECT COUNT(grup_roles.id) FROM grup_roles WHERE grup_roles.recid_rol = roles.recid) AS 'cant_grupos',
        (SELECT COUNT(plan_roles.id) FROM plan_roles WHERE plan_roles.recid_rol = roles.recid) AS 'cant_plantas',
        (SELECT COUNT(emp_roles.id) FROM emp_roles WHERE emp_roles.recid_rol = roles.recid) AS 'cant_empresas',
        (SELECT COUNT(conv_roles.id) FROM conv_roles WHERE conv_roles.recid_rol = roles.recid) AS 'cant_convenios',
        (SELECT COUNT(suc_roles.id) FROM suc_roles WHERE suc_roles.recid_rol = roles.recid) AS 'cant_sucur'
        FROM roles
        INNER JOIN clientes ON roles.cliente = clientes.id
        WHERE roles.id > '0' $recid $recid_c
        ORDER BY roles.fecha desc";
        // print_r($query); exit;
        $result = mysqli_query($link, $query);
        $data  = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) :
                $id             = $row['id'];
                $recid          = $row['recid'];
                $recid_cliente  = $row['recid_cliente'];
                $nombre         = $row['nombre'];
                $id_cliente     = $row['id_cliente'];
                $cliente        = $row['cliente'];
                $fecha_alta     = $row['fecha_alta'];
                $fecha_mod      = $row['fecha_mod'];
                $cant_roles     = $row['cant_roles'];
                $cant_modulos   = $row['cant_modulos'];
                $cant_sectores  = $row['cant_sectores'];
                $cant_grupos    = $row['cant_grupos'];
                $cant_sucur     = $row['cant_sucur'];
                $cant_plantas   = $row['cant_plantas'];
                $cant_convenios = $row['cant_convenios'];
                $cant_empresas  = $row['cant_empresas'];
                $data[] = array(
                'id'             => $id,
                'recid'          => $recid,
                'recid_cliente'  => $recid_cliente,
                'nombre'         => $nombre,
                'id_cliente'     => $id_cliente,
                'cliente'        => $cliente,
                'cant_roles'     => $cant_roles,
                'cant_modulos'   => $cant_modulos,
                'cant_sectores'  => $cant_sectores,
                'cant_sucur'     => $cant_sucur,
                'cant_grupos'    => $cant_grupos,
                'cant_plantas'   => $cant_plantas,
                'cant_empresas'  => $cant_empresas,
                'cant_convenios' => $cant_convenios,
                'fecha_alta'     => $fecha_alta,
                'fecha_mod'      => $fecha_mod
                );
            endwhile;
            mysqli_free_result($result);
            mysqli_close($link);
            $respuesta = array('success' => 'YES', 'error' => 'NO', 'roles' => $data);
        } else {
            $respuesta[] = array('success' => 'NO', 'error' => '1', 'roles' => 'NO');
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', 'roles' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'roles' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
