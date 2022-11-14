<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

$respuesta = array();
$arrayData = array();

$params = $columns = $totalRecords = '';
$params = $_REQUEST;
$params['status'] = $params['status'] ?? '';
$params['start']  = $params['start'] ?? '';
$params['length'] = $params['length'] ?? '';
$params['key']    = $params['key'] ?? '';

$idCompany = $_SESSION['ID_CLIENTE'];

$paramsApi = array(
    'key'        => $_SESSION["RECID_CLIENTE"],
    'start'      => ($params['start']),
    'length'     => ($params['length']),
    'status'     => ($params['status']),
    'userIDName' => urlencode($params['search']['value']),
);
$parametros = '';
foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}
$api = "api/v1/users/$parametros";
$url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);

$totalRecords = $api['TOTAL'];
if ($api['COUNT'] > 0) {

    foreach ($api['RESPONSE_DATA'] as $r) {

        if ($r['locked'] == '1') {
            $bloqueado = true;
            $tipoBloqueo = '';
        } else if (!empty($r['expiredEnd']) && FechaFormatVar($r['expiredEnd'], 'Ymd') >= date('Ymd')) {
            $bloqueado = true;
            $tipoBloqueo = 'Fecha';
        } else {
            $bloqueado = false;
            $tipoBloqueo = '';
        }

        $arrayData[] = array(
            'userID'       => $r['userID'],
            'userName'     => $r['userName'],
            'userChecks'   => $r['userChecks'],
            'userRegId'    => $r['userRegId'],
            'expiredEnd'   => ($r['expiredEnd']!= '0000-00-00') ? FechaFormatVar($r['expiredEnd'], 'd/m/Y'): null,
            'expiredStart' => ($r['expiredStart'] != '0000-00-00') ? FechaFormatVar($r['expiredStart'], 'd/m/Y') : null,
            'locked'       => $r['locked'],
            'motivo'       => $r['motivo'],
            'bloqueado'    => $bloqueado,
            'tipoBloqueo'  => $tipoBloqueo,
            'trained'  => $r['trained']
        );
    }
}
// foreach ($arrayData as $key => $valor) {
//     $Activar = (strlen($valor['userRegId'] > '100')) ? '<span data-regid="' . $valor['userRegId'] . '" data-userid="' . $valor['userID'] . '" data-titlel="Configurar dispositivo. Envía Legajo y Empresa" class="ml-1 btn btn-outline-custom border sendSettings"><i class="bi bi-phone"></i></span>' : '<span data-titlel="Sin Reg ID" class="ml-1 btn btn-outline-custom disabled border-0"><i class="bi bi-phone"></i></span>';
//     $mensaje = (strlen($valor['userRegId'] > '100')) ? '<span data-nombre="' . $valor['userName'] . '" data-regid="' . $valor['userRegId'] . '"  data-titlel="Enviar Mensaje" class="ml-1 btn btn-outline-custom border bi bi-chat-text sendMensaje"></span>' : '<span data-titlel="Sin Reg ID" data-regid="' . $valor['userRegId'] . '" class="ml-1 btn btn-outline-custom border-0 bi bi-chat-text disabled"></span></span>';

//     $respuesta[] = array(
//         '<div>' . $valor['userID'] . '</div>',
//         '<div>' . $valor['userName'] . '</div>',
//         '<div>' . $valor['userChecks'] . '</div>',
//         '<div class="d-flex justify-content-start">
//         <span data-titlel="Editar" data-iduser="' . $valor['userID'] . '" data-nombre="' . $valor['userName'] . '" class="btn btn-outline-custom border bi bi-pen updateUser"></span>
//         ' . $mensaje . '
//         ' . $Activar . '
//         <span data-titlel="Eliminar" data-iduser="' . $valor['userID'] . '" data-nombre="' . $valor['userName'] . '" class="ml-1 btn btn-outline-custom border bi bi-trash deleteUser"></span></div>'
//     );
// }
// $respuesta = array('mobile' => $respuesta);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $arrayData
);
echo json_encode($json_data);
