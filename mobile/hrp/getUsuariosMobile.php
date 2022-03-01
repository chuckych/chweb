<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

// require __DIR__ . '../../../config/conect_mysql.php';
// sleep(2);
$respuesta = array();
$arrayData = array();

$params = $columns = $totalRecords = '';
$params = $_REQUEST;
$params['status'] = $params['status'] ?? '';
$params['start']  = $params['start'] ?? '';
$params['length'] = $params['length'] ?? '';
$params['key']    = $params['key'] ?? '';

$idCuenta = $_SESSION['ID_CLIENTE'];

$paramsApi = array(
    'key'        => sha1('mobileHRP'),
    'start'      => ($params['start']),
    'length'     => ($params['length']),
    'status'     => ($params['status']),
    'userIDName' => urlencode($params['search']['value']),
    'idCuenta'   => $idCuenta,
);
$parametros = '';
foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}
$api = "api/v1/users/$parametros";
// echo $api; exit;
$url   = host() . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$json = file_get_contents($url);
$api  = json_decode($json, true);
// $api = getRemoteFile($url, $timeout = 10);
// $api = json_decode($api, true);

$totalRecords = $api['TOTAL'];

// print_r($totalRecords); exit;

if ($api['COUNT'] > 0) {
    foreach ($api['RESPONSE_DATA'] as $r) {
        $arrayData[] = array(
            'userID'     => $r['userID'],
            'userName'   => $r['userName'],
            'userChecks' => $r['userChecks'],
            'userRegId'  => $r['userRegId'],
        );
    }
}

// print_r(json_encode($arrayData)); exit;

foreach ($arrayData as $key => $valor) {
    $Activar = (strlen($valor['userRegId'] > '100')) ? '<span data-regid="' . $valor['userRegId'] . '" data-userid="' . $valor['userID'] . '" data-titlel="Configurar dispositivo. Envía Legajo y Empresa" class="ml-1 btn btn-outline-custom border sendSettings"><i class="bi bi-phone"></i></span>' : '<span data-titlel="Sin Reg ID" class="ml-1 btn btn-outline-custom disabled border-0"><i class="bi bi-phone"></i></span>';
    $mensaje = (strlen($valor['userRegId'] > '100')) ? '<span data-nombre="' . $valor['userName'] . '" data-regid="' . $valor['userRegId'] . '"  data-titlel="Enviar Mensaje" class="ml-1 btn btn-outline-custom border bi bi-chat-text sendMensaje"></span>' : '<span data-titlel="Sin Reg ID" data-regid="' . $valor['userRegId'] . '" class="ml-1 btn btn-outline-custom border-0 bi bi-chat-text disabled"></span></span>';

    $respuesta[] = array(
        '<div>' . $valor['userID'] . '</div>',
        '<div>' . $valor['userName'] . '</div>',
        '<div>' . $valor['userChecks'] . '</div>',
        '<div class="d-flex justify-content-start">
        <span data-titlel="Editar" data-iduser="' . $valor['userID'] . '" data-nombre="' . $valor['userName'] . '" class="btn btn-outline-custom border bi bi-pen updateUser"></span>
        ' . $mensaje . '
        ' . $Activar . '
        <span data-titlel="Eliminar" data-iduser="' . $valor['userID'] . '" data-nombre="' . $valor['userName'] . '" class="ml-1 btn btn-outline-custom border bi bi-trash deleteUser"></span></div>'
    );
}
// $respuesta = array('mobile' => $respuesta);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $arrayData
);
// sleep(2);
echo json_encode($json_data);
