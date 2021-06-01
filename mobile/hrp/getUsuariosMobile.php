<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

require __DIR__ . '../../../config/conect_mysql.php';
// sleep(2);
$respuesta = array();

$params = $columns = $totalRecords;
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT reg_user_.id_user as 'id_user', reg_user_.nombre as 'nombre', reg_user_.regid as 'regid', (SELECT COUNT(1) FROM reg_ WHERE reg_.id_user = reg_user_.id_user AND reg_.eventType=2) AS 'cant' FROM reg_user_ WHERE reg_user_.UID > 0";

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND ";
    $where_condition .= "reg_user_.nombre LIKE '%" . $params['search']['value'] . "%'";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .=  " ORDER BY reg_user_.nombre LIMIT " . $params['start'] . " ," . $params['length'];
$queryTot = mysqli_query($link, $sqlTot);
$totalRecords = mysqli_num_rows($queryTot);
$queryRecords = mysqli_query($link, $sqlRec);

// print_r($sqlRec); exit;

if ($totalRecords > 0) {
    while ($r = mysqli_fetch_assoc($queryRecords)) {
        $arrayData[] = array(
            'id_user' => $r['id_user'],
            'nombre'  => $r['nombre'],
            'cant'    => $r['cant'],
            'regid'   => $r['regid'],
        );
    }
}

// print_r(json_encode($arrayData)); exit;

foreach ($arrayData as $key => $valor) {
    $Activar = (strlen($valor['regid'] > '100')) ? '<span data-regid="' . $valor['regid'] . '" data-titlel="Configurar dispositivo sin operaciones" class="ml-1 btn btn-outline-custom border sendSettings"><i class="bi bi-phone"></i></span>' : '<span data-titlel="Sin Reg ID" class="ml-1 btn btn-outline-custom disabled border-0"><i class="bi bi-phone"></i></span>';
    $mensaje = (strlen($valor['regid'] > '100')) ? '<span data-nombre="' . $valor['nombre'] . '" data-regid="' . $valor['regid'] . '"  data-titlel="Enviar Mensaje" class="ml-1 btn btn-outline-custom border bi bi-chat-text sendMensaje"></span>' : '<span data-titlel="Sin Reg ID" data-regid="' . $valor['regid'] . '" class="ml-1 btn btn-outline-custom border-0 bi bi-chat-text disabled"></span></span>';

    $respuesta[] = array(
        '<div>' . $valor['id_user'] . '</div>',
        '<div>' . $valor['nombre'] . '</div>',
        '<div>' . $valor['cant'] . '</div>',
        '<div class="d-flex justify-content-start">
        <span data-titlel="Editar" data-iduser="' . $valor['id_user'] . '" data-nombre="' . $valor['nombre'] . '" class="btn btn-outline-custom border bi bi-pen updateUser"></span>
        '.$mensaje.'
        ' . $Activar.'
        <span data-titlel="Eliminar" data-iduser="' . $valor['id_user'] . '" data-nombre="' . $valor['nombre'] . '" class="ml-1 btn btn-outline-custom border bi bi-trash deleteUser"></span></div>'
    );
}
// $respuesta = array('mobile' => $respuesta);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $respuesta
);
// sleep(2);
echo json_encode($json_data);
