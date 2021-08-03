<?php
header("Content-Type: application/json");
// session_start();
require __DIR__ . '../../funciones.php';
E_ALL();
$respuesta   = '';
$token = token();
$recid = (isset($_GET['recid'])) ? "AND clientes.recid='$_GET[recid]'" : "";
$id = (isset($_GET['id'])) ? "AND clientes.id='$_GET[id]'" : "";
if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        require __DIR__ . '../../config/conect_mysql.php';
         $query="SELECT clientes.recid as recid, clientes.ident as ident, clientes.id as id, clientes.nombre as nombre, clientes.host as host, clientes.db as db, clientes.user as user, clientes.pass as pass, clientes.auth as auth, clientes.fecha_alta as fecha_alta, clientes.fecha as fecha_mod, clientes.tkmobile as tkmobile, clientes.WebService as WebService, ( SELECT COUNT(usuarios.cliente) FROM usuarios WHERE clientes.id=usuarios.cliente ) AS cant_usuarios, ( SELECT COUNT(roles.id) FROM roles WHERE roles.cliente=clientes.id ) AS cant_roles FROM clientes WHERE clientes.id >'0' $recid $id ORDER BY clientes.fecha DESC";
        // print_r($query);
        $result = mysqli_query($link, $query);
        $data  = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) :
                              $recid = $row['recid'];
                              $ident = $row['ident'];
                                 $id = $row['id'];
                             $nombre = $row['nombre'];
                               $host = $row['host'];
                                 $db = $row['db'];
                               $user = $row['user'];
                               $pass = $row['pass'];
                               $auth = $row['auth'];
                           $tkmobile = $row['tkmobile'];
                         $WebService = $row['WebService'];
                         $fecha_alta = $row['fecha_alta'];
                              $fecha = $row['fecha_mod'];
                      $cant_usuarios = $row['cant_usuarios'];
                         $cant_roles = $row['cant_roles'];
                $data[] = array(
                          'recid' => $recid,
                          'ident' => $ident,
                             'id' => $id,
                         'nombre' => $nombre,
                           'host' => $host,
                             'db' => $db,
                           'user' => $user,
                           'pass' => $pass,
                   'auth_windows' => $auth,
                       'tkmobile' => $tkmobile,
                     'WebService' => $WebService,
                     'fecha_alta' => $fecha_alta,
                      'fecha_mod' => $fecha,
                  'cant_usuarios' => $cant_usuarios,
                     'cant_roles' => $cant_roles
                );
            endwhile;
            mysqli_free_result($result);
            mysqli_close($link);
            $respuesta = array('success' => 'YES', 'error' => 'NO', 'clientes' => $data);
        } else {
            $respuesta[] = array('success' => 'NO', 'error' => '1', 'clientes' => 'NO');
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', 'clientes' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'clientes' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
