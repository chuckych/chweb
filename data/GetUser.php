<?php
header("Content-Type: application/json");
// session_start();
require __DIR__ . '../../funciones.php';
E_ALL();
$respuesta   = '';
$token = token();
$recid_c = (isset($_GET['recid_c'])) ? "AND clientes.recid='$_GET[recid_c]'" : "";
$recid   = (isset($_GET['recid'])) ? "AND usuarios.recid='$_GET[recid]'" : "";
$uid     = (isset($_GET['uid'])) ? "AND usuarios.id='$_GET[uid]'" : "";
$rol_id  = (isset($_GET['rol_id'])) ? "AND roles.id='$_GET[rol_id]'" : "";
if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        require __DIR__ . '../../config/conect_mysql.php';
        $query = "SELECT usuarios.id AS uid, usuarios.recid AS recid, usuarios.nombre AS nombre, usuarios.usuario AS usuario, usuarios.legajo AS legajo, usuarios.rol AS rol, roles.nombre AS rol_n, usuarios.estado AS estado, clientes.nombre as cliente, clientes.id as id_cliente, clientes.recid as recid_cliente, usuarios.fecha_alta AS fecha_alta, usuarios.fecha AS fecha_mod 
        FROM usuarios 
        LEFT JOIN roles ON usuarios.rol = roles.id
        INNER JOIN clientes ON usuarios.cliente = clientes.id
        WHERE usuarios.id>'0' $recid_c $uid $recid $rol_id
        ORDER BY usuarios.estado, usuarios.fecha desc";
        $result = mysqli_query($link, $query);
        // print_r(($query));
        $data  = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) :
                $uid           = $row['uid'];
                $recid         = $row['recid'];
                $nombre        = $row['nombre'];
                $usuario       = $row['usuario'];
                $rol           = $row['rol'];
                $rol_n         = $row['rol_n'];
                $legajo        = $row['legajo'];
                $estado        = $row['estado'];
                $cliente       = $row['cliente'];
                $id_cliente    = $row['id_cliente'];
                $recid_cliente = $row['recid_cliente'];
                $estado_n      = ($estado) ? 'Inactivo':'Activo';
                $fecha_alta    = $row['fecha_alta'];
                $fecha_mod     = $row['fecha_mod'];
                $data[] = array(
                    'uid'           => $uid,
                    'recid'         => $recid,
                    'nombre'        => $nombre,
                    'usuario'       => $usuario,
                    'legajo'        => $legajo,
                    'rol_n'         => $rol_n,
                    'estado'        => $estado,
                    'estado_n'      => $estado_n,
                    'id_cliente'    => $id_cliente,
                    'recid_cliente' => $recid_cliente,
                    'cliente'       => $cliente,
                    'rol'           => $rol,
                    'fecha_alta'    => FechaFormatH($fecha_alta),
                    'fecha_mod'     => FechaFormatH($fecha_mod)
                );
            endwhile;
            mysqli_free_result($result);
            mysqli_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'users' => $data);
        } else {
            $respuesta = array('success' => 'NO', 'error' => '1', 'users' => 'NO');
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', 'users' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'users' => 'ERROR TOKEN');
}
// $datos = array($respuesta);
echo json_encode($respuesta);
// print_r($datos);
