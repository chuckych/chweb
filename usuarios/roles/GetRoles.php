<?php
require __DIR__ . '../../../config/index.php';
session_start();
header('Content-type: text/html; charset=utf-8');
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

$data = array();
require __DIR__ . '../../../config/conect_mysql.php';

$params = $columns = $totalRecords='';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

FusNuloPOST('recid_c', '');
$recid_c = test_input($_POST['recid_c']);


$sql_query = "SELECT roles.id AS 'id', roles.recid AS 'recid', roles.nombre AS 'nombre', roles.fecha_alta AS 'fecha_alta', roles.cliente as 'id_cliente', clientes.nombre as 'cliente', clientes.recid as 'recid_cliente', roles.fecha AS 'fecha_mod', (SELECT COUNT(usuarios.rol) FROM usuarios WHERE roles.id = usuarios.rol) AS 'cant_roles',
(SELECT COUNT(mod_roles.id) FROM mod_roles WHERE mod_roles.recid_rol = roles.recid) AS 'cant_modulos',
(SELECT COUNT(sect_roles.id) FROM sect_roles WHERE sect_roles.recid_rol = roles.recid) AS 'cant_sectores',
(SELECT COUNT(grup_roles.id) FROM grup_roles WHERE grup_roles.recid_rol = roles.recid) AS 'cant_grupos',
(SELECT COUNT(plan_roles.id) FROM plan_roles WHERE plan_roles.recid_rol = roles.recid) AS 'cant_plantas',
(SELECT COUNT(emp_roles.id) FROM emp_roles WHERE emp_roles.recid_rol = roles.recid) AS 'cant_empresas',
(SELECT COUNT(conv_roles.id) FROM conv_roles WHERE conv_roles.recid_rol = roles.recid) AS 'cant_convenios',
(SELECT COUNT(suc_roles.id) FROM suc_roles WHERE suc_roles.recid_rol = roles.recid) AS 'cant_sucur'
FROM roles
INNER JOIN clientes ON roles.cliente = clientes.id
WHERE roles.id > '1' AND clientes.recid='$recid_c'";

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND ";
    $where_condition .= " roles.nombre LIKE '%" . $params['search']['value'] . "%' ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .=  " ORDER BY roles.fecha desc LIMIT " . $params['start'] . " ," . $params['length'];
$queryTot = mysqli_query($link, $sqlTot);
$totalRecords = mysqli_num_rows($queryTot);
$queryRecords = mysqli_query($link, $sqlRec);

// print_r($sqlRec).PHP_EOL; exit;
$classButton = 'btn btn-sm btn-outline-custom border mr-1';
$IconEditar  = '<i class="bi bi-pen"></i>';
$IconTrash   = '<i class="bi bi-trash"></i>';

function Total($tabla, $Col){
    require __DIR__ . '../../../config/conect_mssql.php';
    $q = "SELECT COUNT($tabla.$Col) AS total FROM $tabla";
    $rs = sqlsrv_query($link, $q);
    while ($r = sqlsrv_fetch_array($rs)) :
        $t = $r['total'];
    endwhile;
    sqlsrv_free_stmt($rs);
    return $t;
}
$EMPRESAS   = Total('EMPRESAS', 'EmpCodi');
$PLANTAS    = Total('PLANTAS', 'PlaCodi');
$SUCURSALES = Total('SUCURSALES', 'SucCodi');
$CONVENIO   = Total('CONVENIO', 'ConCodi');
$GRUPOS     = Total('GRUPOS', 'GruCodi');
$SECTORES   = Total('SECTORES', 'SecCodi');
$rowcount_mod = CountRegMySql("SELECT modulos.id AS 'id' FROM modulos WHERE modulos.id>'0' AND modulos.estado ='0'");

if ($totalRecords > 0) {
    while ($row = mysqli_fetch_assoc($queryRecords)) :

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

        $sum_cant = array(
            $cant_roles,
            $cant_modulos,
            $cant_sectores,
            $cant_grupos,
            $cant_plantas,
            $cant_sucur,
            $cant_empresas,
            $cant_convenios
        );

        $cant_roles ='<a title="Usuarios del Rol '.$nombre.'" href="/'.HOMEHOST.'/usuarios/?_c='.$recid_cliente.'&_rol='.$nombre.'" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd">
        <span class="contentd">'.$cant_roles.'</span></a>';
        
        $cant_modulos = '<a title="Módulos del Rol '. $nombre .'" href="/'. HOMEHOST .'/usuarios/modulos/?_r='. $recid .'&id='. $id .'&_c='. $recid_cliente .'" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd"><span class="contentd">'. $cant_modulos .' / '. $rowcount_mod . '</span></a>';

        $listas = '<button type="button" data-r="'. $recid .'" data-id="'. $id .'" data-c="'. $recid_cliente .'" title="Listas del Rol '. $nombre .'" class="fw5 border btn btn-outline-custom btn-sm fontp contentd ListaRol"><span class="contentd"><i class="bi bi-list"></i></span></button>';

        $cant_empresas='<a title="Empresas del Rol '.$nombre .'" href="/'.HOMEHOST .'/usuarios/estructura/?_r='.$recid .'&id='.$id .'&_c='.$_GET['_c'] .'&e=empresas" class="w70 border btn btn-outline-custom btn-sm fontp contentd"><span class="contentd">'.$cant_empresas .' / '. $EMPRESAS .'</span></a>';

        $cant_plantas='<a title="Plantas del Rol '.$nombre .'" href="/'.HOMEHOST .'/usuarios/estructura/?_r='.$recid .'&id='.$id .'&_c='.$_GET['_c'] .'&e=plantas" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd"><span class="contentd">'.$cant_plantas .' / '.$PLANTAS .'</span></a>';

        $cant_convenios='<a title="Convenios del Rol '.$nombre .'" href="/'.HOMEHOST .'/usuarios/estructura/?_r='.$recid .'&id='.$id .'&_c='.$_GET['_c'] .'&e=convenios" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd"><span class="contentd">'.$cant_convenios .' / '.$CONVENIO .'</span></a>';
        
        $cant_sectores='<a title="Sectores del Rol '.$nombre .'" href="/'.HOMEHOST .'/usuarios/estructura/?_r='.$recid .'&id='.$id .'&_c='.$_GET['_c'] .'&e=sectores" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd"><span class="contentd">'.$cant_sectores .' / '.$SECTORES .'</span></a>';
        
        $cant_grupos='<a title="Grupos del Rol '.$nombre .'" href="/'.HOMEHOST .'/usuarios/estructura/?_r='.$recid .'&id='.$id .'&_c='.$_GET['_c'] .'&e=grupos" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd"><span class="contentd">'.$cant_grupos .' / '.$GRUPOS .'</span></a>';
        
        $cant_sucur='<a title="Sucursales del Rol '.$nombre .'" href="/'.HOMEHOST .'/usuarios/estructura/?_r='.$recid .'&id='.$id .'&_c='.$_GET['_c'] .'&e=sucursales" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd"><span class="contentd">'.$cant_sucur .' / '.$SUCURSALES .'</span></a>';

        $abm_rol='<button title="Altas, bajas y modificaciones del Rol '.ucwords(strtolower($nombre)) .'" type="button" class="w70 fw5 border btn btn-outline-custom btn-sm fontp contentd" data-toggle="modal" data-target="#ModalABM" data="'.$nombre .'" data1="'.$recid .'" data2="'.$id .'" data3="'.$cliente .'" id="open-modal"><span class="contentd">ABM</span></button>';

        $edit_rol='<button type="button" title="Editar Rol" class="btn btn-sm fontp btn-outline-custom border mr-1 editRol contentd" datarol="'.$nombre .'" dataidrol="'.$id .'" datarecid_c="'.$_GET['_c'] .'" id="Editar_'.$id .'"><i class="bi bi-pencil fontq contentd"></i></button>';

        if (array_sum($sum_cant) <= 0) {
        $delete_rol = '<button type="button" title="Eliminar" class="btn btn-sm fontp btn-outline-custom border deleteRol contentd" datarol="'.$nombre .'" dataidrol="'.$id .'" datarecid_c="'.$_GET['_c'] .'" id="Eliminar_'.$id .'"><i class="bi bi-trash fontq contentd"></i></button>';
        }else{
            $delete_rol = '';
        }

        $data[] = array(
            'id'             => '<span class="contentd">'.$id.'</span>',
            'recid'          => '<span class="contentd">'.$recid.'</span>',
            'recid_cliente'  => '<span class="contentd">'.$recid_cliente.'</span>',
            'nombre'         => '<span class="contentd">'.$nombre.'</span>',
            'id_cliente'     => '<span class="contentd">'.$id_cliente.'</span>',
            'cliente'        => '<span class="contentd">'.$cliente.'</span>',
            'cant_roles'     => '<span class="contentd">'.$cant_roles.'</span>',
            'cant_modulos'   => '<span class="contentd">'.$cant_modulos.'</span>',
            'listas'         => '<span class="contentd">'.$listas.'</span>',
            'abm_rol'        => '<span class="contentd">'.$abm_rol.'</span>',
            'cant_sectores'  => '<span class="contentd">'.$cant_sectores.'</span>',
            'cant_sucur'     => '<span class="contentd">'.$cant_sucur.'</span>',
            'cant_grupos'    => '<span class="contentd">'.$cant_grupos.'</span>',
            'cant_plantas'   => '<span class="contentd">'.$cant_plantas.'</span>',
            'cant_empresas'  => '<span class="contentd">'.$cant_empresas.'</span>',
            'cant_convenios' => '<span class="contentd">'.$cant_convenios.'</span>',
            'fecha_alta'     => '<span class="contentd">'.$fecha_alta.'</span>',
            'fecha_mod'      => '<span class="contentd">'.$fecha_mod.'</span>',
            'edit_rol'       => '<span class="contentd">'.$edit_rol.$delete_rol.'</span>',
        );
    endwhile;
}
// sleep(1);
mysqli_free_result($queryTot);
mysqli_free_result($queryRecords);
mysqli_close($link);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);
// sleep(2);
echo json_encode($json_data);
