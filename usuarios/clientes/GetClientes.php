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

$query = "SELECT clientes.recid as recid, clientes.ident as ident, clientes.id as id, clientes.nombre as nombre, clientes.host as host, clientes.db as db, clientes.user as user, clientes.pass as pass, clientes.auth as auth, clientes.fecha_alta as fecha_alta, clientes.fecha as fecha_mod, clientes.tkmobile as tkmobile, clientes.WebService as WebService, ( SELECT COUNT(usuarios.cliente) FROM usuarios WHERE clientes.id=usuarios.cliente ) AS cant_usuarios, ( SELECT COUNT(roles.id) FROM roles WHERE roles.cliente=clientes.id ) AS cant_roles FROM clientes WHERE clientes.id >'0' $recid $id ORDER BY clientes.fecha DESC";
// print_r($query); exit;
$result = mysqli_query($link, $query);
$data  = array();

$IconEditar   = '<i class="bi bi-pen"></i>';
$IconUsuarios = '<i class="bi bi-people-fill"></i>';
$IconUser = '<i class="bi bi-person-fill"></i>';
$IconKey = '<i class="bi bi-key-fill"></i>';
$IconRoles    = '<i class="bi bi-sliders"></i>';
$IconServer    = '<i class="bi bi-server"></i>';
$IconHDD    = '<i class="bi bi-hdd-stack-fill"></i>';
$IconHash    = '<i class="bi bi-hash"></i>';
$IconCuenta    = '<i class="bi bi-house-fill"></i>';


if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) :
    $recid         = $row['recid'];
    $ident         = $row['ident'];
    $id            = $row['id'];
    $nombre        = $row['nombre'];
    $host          = $row['host'];
    $db            = $row['db'];
    $user          = $row['user'];
    $pass          = $row['pass'];
    $auth          = ($row['auth'] == 0) ? 'No' : 'Sí';
    $auth2         = ($row['auth'] == 0) ? '1' : '2'; /** 1 = no ; 2 = Si */
    $tkmobile      = $row['tkmobile'];
    $WebService    = $row['WebService'];
    $fecha_alta    = fechformat($row['fecha_alta']);
    $fecha         = $row['fecha_mod'];
    $cant_usuarios = $row['cant_usuarios'];
    $cant_roles    = $row['cant_roles'];

    if ($cant_roles) {
      $btnUsers = '<a title="Usuarios" href="/' . HOMEHOST . '/usuarios/?_c=' . $recid . '&alta" class="btn border btn-outline-custom w80 fontq"><span class=""> <span class="mr-2">' . $IconUsuarios . '</span>' . $cant_usuarios . ' </span>
                    </a>';
    } else {
      $btnUsers = '<button title="" href="#" class="btn border btn-outline-custom w80 fontq"><span class=""><span class="mr-2">' . $IconUsuarios . '</span>' . $cant_usuarios . ' </span></button>';
    }
    $btnRoles = '<a title="Roles" href="/' . HOMEHOST . '/usuarios/roles/?_c=' . $recid . '&alta" class="btn border btn-outline-custom w80 fontq ml-1"><span class="mr-2">' . $IconRoles . '</span>' . $cant_roles . ' </span></a>';
    $btnEditar='<button type="button" title="Editar Cuenta:' . $nombre . '" dataNombre="' . $nombre . '" dataIdent="' . $ident . '" dataRecid="' . $recid . '" dataId="' . $id . '" dataHost="' . $host . '" dataDB="' . $db . '" dataUser="' . $user . '" dataPass="' . $pass . '" dataAuth="' . $auth2 . '" dataTkmobile="' . $tkmobile . '" dataWebService="' . $WebService . '" class="btn border btn-outline-custom fontq ml-1 editCuenta">' . $IconEditar . '</span></button>';
    // $btnEditar = '<a title="Editar Cuenta: '.$nombre.'" href="index.php?id='.$recid.'&mod" class="btn border btn-outline-custom fontp">'.$IconEditar.'</span></a>';
    $Botones = $btnUsers . $btnRoles . $btnEditar;
    $data[] = array(
      'recid' => '<span class="contentd">' . $recid . '</span>',
      'ident' => '<span class="contentd" title="Identificador de la cuenta"><span class="mr-2 text-info">' . $IconHash . '</span>' . $ident . '</span>',
      'id' => '<span class="contentd">' . $id . '</span>',
      'nombre' => '<div class="contentd text-nowrap pt-2 text-secondary"><b class="contentd text-secondary mr-2" title="Nombre de la Cuenta">' . '<span class="mr-2 text-info">' . $IconCuenta . '</span>' . $nombre . '</b><div class="float-right">' . $Botones . '</div></div>',
      'host' => '<span class="contentd" title="Servidor de Base de Datos">' . '<span class="mr-2 text-info">' . $IconServer . '</span>' . $host . '</span>',
      'db' => '<span class="contentd" title="Nombre de la Base de Datos">' . '<span class="mr-2 text-info">' . $IconHDD . '</span>' . $db . '</span>',
      'user' => '<span class="contentd" title="Usuario de la Base de Datos">' . '<span class="mr-2 text-info">' . $IconUser . '</span>' . $user . '</span>',
      //  'pass' => '<span class="contentd">'.'<span class="mr-2 text-info">'.$IconKey.'</span>'.$pass.'</span>',
      'pass' => '<span class="contentd">' . '<span class="d-inline-block text-truncate" style="max-width: 70px;" title="' . $pass . '">' . '<span class="mr-2 text-info" title="Contraseña de la Base de Datos">' . $IconKey . '</span>' . $pass . '</span>' . '</span>',
      'auth_windows' => '<span class="contentd" title="Autenticación de Windows"><span class="text-info" >Auth: </span>' . $auth . '</span>',
      'tkmobile' => '<span class="contentd">' . '<span class="d-inline-block text-truncate" style="max-width: 120px;" title="' . $tkmobile . '"><span class="text-info" title="Token Mobile">Token: </span>' . $tkmobile . '</span>' . '</span>',
      'WebService' => '<span class="contentd" title="Web Service CH"><span class="text-info">WS: </span>' . $WebService . '</span>',
      'fecha_alta' => '<span class="contentd ls1" title="Fecha de alta">' . $fecha_alta . '</span>',
      'fecha_mod' => '<span class="contentd">' . $fecha . '</span>',
      'cant_usuarios' => '<span class="contentd">' . $btnUsers . '</span>',
      'cant_roles' => '<span class="contentd">' . $btnRoles . '</span>',
      'Editar' => '<span class="contentd">' . $btnEditar . '</span>',
      'null' => ''
      //    <span data-icon="" class="icon ml-2 align-middle mt-1 text-gris"></span>
    );
  endwhile;
  mysqli_free_result($result);
  mysqli_close($link);
}
echo json_encode(array('data' => $data));
