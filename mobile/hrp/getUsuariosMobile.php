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
$params['data_array'] = $params['data_array'] ?? '';
$params['status'] = $params['status'] ?? '';
$params['start'] = $params['start'] ?? '';
$params['length'] = $params['length'] ?? '';
$params['key'] = $params['key'] ?? '';

$idCompany = $_SESSION['ID_CLIENTE'];

$paramsApi = array(
    'key' => $_SESSION["RECID_CLIENTE"],
    'start' => ($params['start']),
    'length' => ($params['length']),
    'status' => ($params['status']),
    'userIDName' => urlencode($params['search']['value'] ?? ''),
);
$parametros = '';
foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}
$api = "api/v1/users/$parametros";
$url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);

$totalRecords = $api['TOTAL'] ?? 0;
if ($api['COUNT'] ?? 0 > 0) {

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

        if ($params['data_array'] == 'select') {
            $arrayData[] = array(
                'id' => $r['userID'],
                'text' => $r['userName'],
                'html' => '
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column">
                            <span>' . $r['userName'] . '</span>
                            <span class="fontp">ID: ' . $r['userID'] . '</span>
                        </div>
                        <span class="badge badge-light">' . $r['userChecks'] . '</span>
                    </div>
                ',
            );
        } else {
            $arrayData[] = array(
                'userID' => $r['userID'],
                'userName' => $r['userName'],
                'userChecks' => $r['userChecks'],
                'userRegId' => $r['userRegId'],
                'userArea' => $r['hasArea'],
                'expiredEnd' => ($r['expiredEnd'] != '0000-00-00') ? FechaFormatVar($r['expiredEnd'], 'd/m/Y') : null,
                'expiredStart' => ($r['expiredStart'] != '0000-00-00') ? FechaFormatVar($r['expiredStart'], 'd/m/Y') : null,
                'locked' => $r['locked'],
                'motivo' => $r['motivo'],
                'bloqueado' => $bloqueado,
                'tipoBloqueo' => $tipoBloqueo,
                'trained' => $r['trained']
            );
        }
    }
}
$json_data = array(
    "draw" => intval($params['draw'] ?? ''),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $arrayData
);
if ($params['data_array'] == 'select') {
    echo json_encode($arrayData);
    exit;
}
echo json_encode($json_data);
