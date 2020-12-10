<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
$url   = host() . "/" . HOMEHOST . "/data/GetUser.php?tk=" . token() . "&recid_c=" . $_GET['_c'];
// echo $url;exit;
$json  = file_get_contents($url);
$array = json_decode($json, TRUE);
$data  = $array['users'];
if(!$array['error']){
$_c = $_GET['_c'];
// print_r($data);

if(modulo_cuentas()!='1'){
$data = array_filter($data, function ($e) {
    return $e['nombre'] != 'SISTEMA';
}); 
} /** Filtramos el usuario SISTEMA para que solo sea visible por el rol que tenga acceso a Cuentas */
foreach ($data as $value) {
            $recid = $value['recid'];
              $uid = $value['uid'];
           $nombre = $value['nombre'];
           $legajo = ($value['legajo'])? $value['legajo'] : '-';
          $usuario = $value['usuario'];
           $estado = $value['estado'];
         $estado_n = $value['estado_n'];
    $recid_cliente = $value['recid_cliente'];
    switch ($estado) {
        case '0':
            $title = 'Dar de baja';
            $btn   = 'danger';
            $alert   = 'success';
            $text  = 'text-success';
            $caret = imgIcon('down_rojo', 'Dar de baja' ,'w20');
            break;

        default:
            $title = 'Dar de alta';
            $btn   = 'success';
            $alert   = 'danger';
            $text  = 'text-danger';
            $caret = imgIcon('up_verde', 'Dar de alta' ,'w20');
            break;
    }

    // $estado_n = '';
    $estado_n = "<div title='$estado_n' class='fw3 radius-0 p-2 text-center text-white w80 opa8 m-0 bg-$alert'><p class='d-none'>$estado_n</p></div>";
    $rol = $value['rol_n'];
    $icon_pass=imgIcon('password', 'Restablecer contraseña' ,'w20');
    $icon_edit=imgIcon('editar', 'Editar usuario' ,'w20');
    $editar = "";
    $action = 'index.php?_c='. $recid_cliente;
    if(!principal($value['recid'])){
    $editar = "<a title='Editar usuario' class='btn btn-light btn-sm' href='index.php?id=$recid&mod&_c=$_c'>$icon_edit</a>";
    $reset_c = "<form action='$action' method='post' onsubmit='ShowLoading()'>
    <input type='hidden' name='recid' value='$recid'>
    <input type='hidden' name='usuario' value='$usuario'>
    <input type='hidden' name='nombre' value='$nombre'>
    <button name='submit' value='key' type='submit' title='Restablecer contraseña' class='btn btn-light btn-sm'>$icon_pass</button>
    </form>";
    $dar_baja = "<form action='$action' method='post' onsubmit='ShowLoading()'>
    <input type='hidden' name='cambioestado' value='$estado'>
    <input type='hidden' name='recid_e' value='$recid'>
    <input type='hidden' name='nombre' value='$nombre'>
    <button name='submit' value='modestado' type='submit' title='$title' class='btn btn-light btn-sm' >$caret</button>
    </form>";
    $icon_trash=imgIcon('trash', 'Eliminar usuario' ,'w20');
    $eliminar = "<form action='$action' method='post' onsubmit='ShowLoading()'>
<input type='hidden' name='recid' value='$recid'>
<input type='hidden' name='nombre' value='$nombre'>
<button name='submit' value='trash' type='submit' title='Eliminar usuario' class='btn btn-light btn-sm'>$icon_trash</button>
</form>";
    }else{$eliminar='-';$reset_c='-';$dar_baja='-';$editar = '-';}
    $respuesta[] = array(
             'uid' => $uid,
          'nombre' => $nombre,
          'legajo' => $legajo,
         'usuario' => $usuario,
             'rol' => $rol,
        'estado_n' => $estado_n,
          'editar' => $editar,
         'reset_c' => $reset_c,
        'dar_baja' => $dar_baja,
        'eliminar' => $eliminar
    );
}
$respuesta = array('users' => $respuesta);
echo json_encode($respuesta);
}
else{
    $respuesta= array(
        'uid' => '1',
     'nombre' => '1',
    'usuario' => '1',
        'rol' => '1',
   'estado_n' => '1',
     'editar' => '1',
    'reset_c' => '1',
   'dar_baja' => '1',
   'eliminar' => '1'
);
$respuesta = array('users' => $respuesta);
echo json_encode($respuesta);
}

