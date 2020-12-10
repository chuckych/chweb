<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');
require __DIR__ . '../../../config/conect_mysql.php';

FusNuloPOST('q', '');
$q = $_POST['q'];

$query="SELECT clientes.recid as recid, clientes.ident as ident, clientes.id as id, clientes.nombre as nombre, clientes.host as host, clientes.db as db, clientes.user as user, clientes.pass as pass, clientes.auth as auth, clientes.fecha_alta as fecha_alta, clientes.fecha as fecha_mod, clientes.tkmobile as tkmobile, clientes.WebService as WebService, ( SELECT COUNT(usuarios.cliente) FROM usuarios WHERE clientes.id=usuarios.cliente ) AS cant_usuarios, ( SELECT COUNT(roles.id) FROM roles WHERE roles.cliente=clientes.id ) AS cant_roles FROM clientes WHERE clientes.id >'0' $recid $id ORDER BY clientes.fecha DESC";
// print_r($query); exit;
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
                       $auth = ($row['auth']==0)? 'No': 'Sí';
                   $tkmobile = $row['tkmobile'];
                 $WebService = $row['WebService'];
                 $fecha_alta = $row['fecha_alta'];
                      $fecha = $row['fecha_mod'];
              $cant_usuarios = $row['cant_usuarios'];
                 $cant_roles = $row['cant_roles'];

                if ($cant_roles){
                    $btnUsers = '<a title="Agregar usuario" href="/'.HOMEHOST.'/usuarios/?_c='.$recid.'&alta" class="btn border btn-outline-custom w50 fontq"><span class="align-middle ls1"> '.$cant_usuarios.' </span>
                    </a>';
                }else{
                    $btnUsers = '<a title="" href="" class="btn border btn-outline-custom w50 fontq"><span class="align-middle ls1"> '.$cant_usuarios.' </span>
                    </a>';
                }
                $btnRoles = '<a title="Agregar Roles" href="/'.HOMEHOST.'/usuarios/roles/?_c='.$recid.'&alta" class="btn border btn-outline-custom w50 fontq"><span class="align-middle ls1"> '.$cant_roles.' </span>
                    </a>';
                $btnEditar = '<a title="Editar Cuenta: '.$nombre.'" href="index.php?id='.$recid.'&mod" class="btn border btn-outline-custom fontp"><span data-icon="&#xe042;" class="align-middle ls1"></span>
                    </a>';
        $data[] = array(
                  'recid' => $recid,
                  'ident' => $ident,
                     'id' => $id,
                 'nombre' => $nombre,
                   'host' => '<span class="d-inline-block text-truncate" style="max-width: 150px;" title="'.$host .'">'.$host .'</span><br>'.$db,
                     'db' => $db,
                   'user' => $user .'<br>'.$pass,
                   'pass' => $pass,
           'auth_windows' => $auth,
               'tkmobile' => '<span class="d-inline-block text-truncate" style="max-width: 150px;" title="'.$tkmobile .'">'.$tkmobile .'</span>',
             'WebService' => $WebService,
             'fecha_alta' => $fecha_alta,
              'fecha_mod' => $fecha,
          'cant_usuarios' => $btnUsers,
             'cant_roles' => $btnRoles,
                 'Editar' => $btnEditar,
                   'null' => ''
                //    <span data-icon="" class="icon ml-2 align-middle mt-1 text-gris"></span>
        );
    endwhile;
    mysqli_free_result($result);
    mysqli_close($link);
}
echo json_encode(array('data'=>$data));
