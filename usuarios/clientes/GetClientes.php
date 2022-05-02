<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

FusNuloPOST('q', '');
$q = $_POST['q'];
$recid = $recid ?? '';
$id = $id ?? '';

$query = "SELECT clientes.recid as 'recid', clientes.ident as 'ident', clientes.id as 'id', clientes.nombre as 'nombre', clientes.host as 'host', clientes.db as 'db', clientes.user as 'user', clientes.pass as 'pass', clientes.auth as 'auth', clientes.fecha_alta as 'fecha_alta', clientes.fecha as 'fecha_mod', clientes.tkmobile as 'tkmobile', clientes.WebService as 'WebService', clientes.ApiMobileHRP as 'ApiMobileHRP', clientes.UrlAppMobile as 'UrlAppMobile', clientes.localCH as 'localCH', ( SELECT COUNT(usuarios.cliente) FROM usuarios WHERE clientes.id=usuarios.cliente ) AS cant_usuarios, ( SELECT COUNT(roles.id) FROM roles WHERE roles.cliente=clientes.id ) AS 'cant_roles' FROM clientes WHERE clientes.id >'0' $recid $id ORDER BY clientes.fecha DESC";
// print_r($query); exit;
$datos = array_pdoQuery($query);
$data  = array();

$IconEditar   = '<i class="bi bi-pen"></i>';
$IconUsuarios = '<i class="bi bi-people-fill"></i>';
$IconUser     = '<i class="bi bi-person-fill"></i>';
$IconKey      = '<i class="bi bi-key-fill"></i>';
$IconRoles    = '<i class="bi bi-sliders"></i>';
$IconServer   = '<i class="bi bi-server"></i>';
$IconHDD      = '<i class="bi bi-hdd-stack-fill"></i>';
$IconHash     = '<i class="bi bi-hash"></i>';
$IconCuenta   = '<i class="bi bi-house-fill"></i>';

foreach ($datos as $row) {
  $recid         = $row['recid'];
  $ident         = $row['ident'];
  $id            = $row['id'];
  $nombre        = $row['nombre'];
  $host          = $row['host'];
  $db            = $row['db'];
  $user          = $row['user'];
  $pass          = $row['pass'];
  $auth          = ($row['auth'] == 0) ? 'No' : 'Sí';
  $auth2         = ($row['auth'] == 0) ? '1' : '2';
  /** 1          = no ; 2 = Si */
  $tkmobile      = $row['tkmobile'];
  $WebService    = $row['WebService'];
  $ApiMobileHRP  = $row['ApiMobileHRP'];
  $UrlAppMobile  = $row['UrlAppMobile'];
  $localCH       = $row['localCH'];
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
  $btnEditar = '<button type="button" title="Editar Cuenta:' . $nombre . '" dataNombre="' . $nombre . '" dataIdent="' . $ident . '" dataRecid="' . $recid . '" dataId="' . $id . '" dataHost="' . $host . '" dataDB="' . $db . '" dataUser="' . $user . '" dataPass="' . $pass . '" dataAuth="' . $auth2 . '" dataTkmobile="' . $tkmobile . '" dataWebService="' . $WebService . '" dataApiMobileHRP="' . $ApiMobileHRP . '" dataMobileHRPApp="' . $UrlAppMobile . '" dataLocalCH="'.$localCH.'" class="btn border btn-outline-custom fontq ml-1 editCuenta">' . $IconEditar . '</span></button>';
  // $btnEditar = '<a title="Editar Cuenta: '.$nombre.'" href="index.php?id='.$recid.'&mod" class="btn border btn-outline-custom fontp">'.$IconEditar.'</span></a>';
  $Botones = $btnUsers . $btnRoles . $btnEditar;
  $data[] = array(
    'recid'         => '<div class="">' . $recid . '</div>',
    'ident'         => '<div class="" title="Identificador de la cuenta"><span class="mr-2 text-info">' . $IconHash . '</span><span class="user-select-all">' . $ident . '</span></span>',
    'id'            => '<div class="">' . $id . '</div>',
    'nombre'        => '<div class=" text-nowrap pt-2 text-secondary"><b class=" text-secondary mr-2" title="Nombre de la Cuenta">' . '<span class="mr-2 text-info">' . $IconCuenta . '</span><span class="user-select-all">' . $nombre . '</span></b><button class="btn btn-sm fontp btn-link testConnect text-secondary" dataRecid="' . $recid . '">Test Conexión</button><div class="float-right">' . $Botones . '</div></div>',
    'host'          => '<div class="" title="Servidor de Base de Datos">' . '<span class="mr-2 text-info">' . $IconServer . '</span><span class="user-select-all">' . $host . '</span></div><div class="" title="Nombre de la Base de Datos">' . '<span class="mr-2 text-info">' . $IconHDD . '</span><span class="user-select-all">' . $db . '</span></div>',
    'user'          => '<div class="" title="Usuario de la Base de Datos">' . '<span class="mr-2 text-info">' . $IconUser . '</span><span class="user-select-all">' . $user . '</span></div><div class="">' . '<span class="d-inline-block text-truncate" style="max-width: 70px;" title="' . $pass . '">' . '<span class="mr-2 text-info" title="Contraseña de la Base de Datos">' . $IconKey . '</span><span class="user-select-all">' . $pass . '</span></span>' . '</div>',
    //  'pass'      => '<span class="">'.'<span class="mr-2 text-info">'.$IconKey.'</span>'.$pass.'</span>',
    'WebService'    => '<div class="" title="Web Service CH"><span class="text-info">WS CH: </span><span class="user-select-all">' . $WebService . '</span></div><div class="">' . '<span class="d-inline-block text-truncate" style="max-width: 180px;" title="' . $tkmobile . '"><span class="text-info" title="Token Mobile">Xclock API: </span><span class="user-select-all">' . $tkmobile . '</span></span>' . '</div>',
    'MobileHRP'    => '<div class="" title=""><span class="text-info">App Mobile: </span><span class="user-select-all">' . $UrlAppMobile . '</span></div><div class="">' . '<span class="d-inline-block" title=""><span class="text-info" title="Token Mobile">Mobile HRP: </span><span class="user-select-all">' . $ApiMobileHRP . '</span></span>' . '</div>',
    'fecha_alta'    => '<div class=" ls1" title="Fecha de alta">' . $fecha_alta . '</div>',
    'fecha_mod'     => '<div class="">' . $fecha . '</div>',
    'cant_usuarios' => '<div class="">' . $btnUsers . '</div>',
    'cant_roles'    => '<div class="">' . $btnRoles . '</div>',
    'Editar'        => '<div class="">' . $btnEditar . '</div>',
    'null'          => ''
    //    <span data-icon="" class="icon ml-2 align-middle mt-1 text-gris"></span>
  );
}
echo json_encode(array('data' => $data));
exit;
