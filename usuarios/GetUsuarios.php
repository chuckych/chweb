<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

$data = array();
require __DIR__ . '../../config/conect_mysql.php';

$params = $columns = $totalRecords;
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

FusNuloPOST('recid_c', '');
$recid_c = test_input($_POST['recid_c']);

// print_r($recid_c).PHP_EOL; exit;
// $recid_c = 'Hh9tzrQZ';

$sql_query = "SELECT usuarios.id AS 'uid', usuarios.recid AS 'recid', usuarios.nombre AS 'nombre', usuarios.usuario AS 'usuario', usuarios.legajo AS 'legajo', usuarios.rol AS 'rol', roles.nombre AS 'rol_n', usuarios.estado AS 'estado', clientes.nombre as 'cliente', clientes.id as 'id_cliente', clientes.recid as 'recid_cliente', usuarios.fecha_alta AS 'fecha_alta', usuarios.fecha AS 'fecha_mod' 
FROM usuarios 
LEFT JOIN roles ON usuarios.rol = roles.id
INNER JOIN clientes ON usuarios.cliente = clientes.id
WHERE usuarios.id>'1' AND clientes.recid='$recid_c'";

// print_r($sql_query).PHP_EOL; exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND ";
    $where_condition .= " usuarios.nombre LIKE '%" . $params['search']['value'] . "%' ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .=  " ORDER BY usuarios.estado, usuarios.fecha desc LIMIT " . $params['start'] . " ," . $params['length'];
$queryTot = mysqli_query($link, $sqlTot);
$totalRecords = mysqli_num_rows($queryTot);
$queryRecords = mysqli_query($link, $sqlRec);

// print_r($sqlRec).PHP_EOL; exit;
$classButton = 'btn btn-sm btn-outline-custom border mr-1';
$IconEditar  = '<svg class="bi" width="17" height="17" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#pen" /></svg>';
$IconEditar  = '<i class="bi bi-pen"></i>';
$IconPerson  = '<i class="bi-person-fill mb-1 mr-2"></i>';
//$IconClave   = '<svg class="bi" width="17" height="17" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#shield-lock" /></svg>';
$IconClave   = '<i class="bi-shield-lock"></i>';
$IconBaja    = '<svg class="bi" width="14" height="14" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#arrow-down-circle" /></svg>';
//$IconBaja    = '<i class="bi bi-arrow-down-circle"></i>';
$IconAlta    = '<svg class="bi" width="14" height="14" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#arrow-up-circle" /></svg>';
//$IconAlta    = '<i class="bi bi-arrow-up-circle"></i>';
//$IconTrash   = '<svg class="bi" width="17" height="17" fill="currentColor"><use xlink:href="../img/bootstrap-icons.svg#trash" /></svg>';
$IconTrash   = '<i class="bi bi-trash"></i>';

if ($totalRecords > 0) {
    while ($row = mysqli_fetch_assoc($queryRecords)) :
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
        $estado_n      = ($estado) ? 'Inactivo' : 'Activo';
        $fecha_alta    = $row['fecha_alta'];
        $fecha_mod     = $row['fecha_mod'];

        $IconEstado = ($estado) ? $IconAlta : $IconBaja;
        $TitleEstado = ($estado) ? 'Dar de alta' : 'Dar de baja';
        $TitleEstado2 = ($estado) ? 'alta' : 'baja';
        $ColorEstado = ($estado) ? 'text-danger' : 'text-secondary';

        $ButtonEditar='<button data_uid="'.$uid .'" data_nombre="'.$nombre.'" data_usuario="'.$usuario.'" data_rol_n="'.$rol_n.'" data_rol="'.$rol.'" data_legajo="'.$legajo.'" data_estado_n="'.$estado_n.'" data_estado="'.$estado.'" data_fecha_alta="'.$fecha_alta .'" data_fecha_mod="'.$fecha_mod .'" data_cliente="'.$cliente .'" title="Editar ' . $nombre . '" class="editar ' . $classButton . '" data-toggle="modal">' . $IconEditar . '</button>';
        $ButtonClave='<button data_uid="'.$uid .'" data_nombre="'.$nombre.'" data_usuario="'.$usuario.'" title="Restablecer Contraseña ' . $nombre . '" class="' . $classButton . ' resetKey" id="reset_'.$uid.'">' . $IconClave . '</button>';
        $ButtonBaja='<button data_uid="'.$uid .'" data_nombre="'.$nombre.'" data_estado="'.$estado.'" title="'.$TitleEstado.': ' . $nombre . '" data_title="'.$TitleEstado2.'" class="' . $classButton . ' estado" id="estado_'.$uid.'">' . $IconEstado . '</button>';
        $ButtonTrash='<button data_uid="'.$uid .'" data_nombre="'.$nombre.'" title="Eliminar ' . $nombre . '" class="' . $classButton . ' delete" id="delete_'.$uid.'">' . $IconTrash . '</button>';

        $data[] = array(
            'uid'           => '<span class="contentd '.$ColorEstado.'">' . $uid . '</span>',
            'recid'         => '<span class="contentd '.$ColorEstado.'">' . $recid . '</span>',
            'nombre'        => '<div class="contentd text-nowrap pt-2 text-secondary"'.$ColorEstado.'"><b class="contentd" '.$ColorEstado.'><span>' . $nombre . '</span></b><span class="mx-2"></span><span class="botones">' . $ButtonEditar . $ButtonClave . $ButtonBaja . $ButtonTrash . '</span></div>',
            'usuario'       => '<span class="contentd '.$ColorEstado.'">'.$IconPerson  . $usuario . '</span>',
            'legajo'        => '<span class="contentd ls1 '.$ColorEstado.'">' . $legajo . '</span>',
            'rol_n'         => '<span class="contentd '.$ColorEstado.'">' . $rol_n . '</span>',
            'estado'        => '<span class="contentd '.$ColorEstado.'">' . $estado . '</span>',
            'estado_n'      => '<span class="contentd '.$ColorEstado.'">' . $estado_n . '</span>',
            'id_cliente'    => '<span class="contentd '.$ColorEstado.'">' . $id_cliente . '</span>',
            'recid_cliente' => '<span class="contentd '.$ColorEstado.'">' . $recid_cliente . '</span>',
            'cliente'       => '<span class="contentd '.$ColorEstado.'">' . $cliente . '</span>',
            'rol'           => '<span class="contentd '.$ColorEstado.'">' . $rol . '</span>',
            'fecha_alta'    => '<span class="contentd ls1 '.$ColorEstado.'">' . FechaFormatH($fecha_alta) . '</span>',
            'fecha_mod'     => '<span class="contentd ls1 '.$ColorEstado.'">' . FechaFormatH($fecha_mod) . '</span>',
            'ButtonEditar'  => '<span class="contentd '.$ColorEstado.'">' . $ButtonEditar . '</span>',
            'Buttons'=>'<span class="contentd '.$ColorEstado.'">' . $ButtonEditar . $ButtonClave . $ButtonBaja . $ButtonTrash . '</span>',
        );
    endwhile;
}
mysqli_free_result($result);
mysqli_close($link);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);
// sleep(2);
echo json_encode($json_data);
