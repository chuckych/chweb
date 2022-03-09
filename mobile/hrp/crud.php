<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();
$id_company = $_SESSION["ID_CLIENTE"];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['tipo'] == 'transferir') {
        $_POST['legFech'] = $_POST['legFech'] ?? '';
        $legFech = explode('@', $_POST['legFech']);
        $legajo  = test_input($legFech[0]);
        $fecha   = test_input($legFech[1]);
        $hora    = test_input($legFech[2]);

        if (valida_campo($legajo)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        };
        if (($legajo == '0')) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        };
        if (valida_campo($fecha)) {
            PrintRespuestaJson('error', 'Falta Fecha');
            exit;
        };
        if (valida_campo($hora)) {
            PrintRespuestaJson('error', 'Falta Hora');
            exit;
        };

        $query = "INSERT INTO FICHADAS (RegTarj, RegFech, RegHora, RegRelo, RegLect, RegEsta) VALUES ('$legajo', '$fecha', '$hora', '9999', '9999', '0')";

        if (InsertRegistroMS($query)) {
            $data = array(
                'status' => 'ok',
                'Mensaje' => 'Se tranfirio el registro.<br>ID: ' . $legajo . '<br>Fecha: ' . fechformat($fecha) . ' Hora: ' . $hora,
                'Legajo' => ($legajo),
                'Fecha' => ($fecha),
            );
            echo json_encode($data);
            exit;
        } else {
            PrintRespuestaJson('error', 'Error al transferir');
            exit;
        }
    } else if ($_POST['tipo'] == 'add_device') {

        $post = $_POST;

        $post['formDeviceNombre']  = $post['formDeviceNombre'] ?? '';
        $post['formDeviceEvento']  = $post['formDeviceEvento'] ?? '';
        $post['formDevicePhoneID'] = $post['formDevicePhoneID'] ?? '';

        $formDeviceNombre  = test_input($post['formDeviceNombre']);
        $formDeviceEvento  = test_input($post['formDeviceEvento']);
        $formDevicePhoneID = test_input($post['formDevicePhoneID']);

        if (valida_campo($formDevicePhoneID)) {
            PrintRespuestaJson('error', 'Falta Phone ID');
            exit;
        };
        if (valida_campo($formDeviceNombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'           => $_SESSION["RECID_CLIENTE"],
            'deviceName'    => urlencode($formDeviceNombre),
            'deviceEvent'   => ($formDeviceEvento),
            'devicePhoneID' => ($formDevicePhoneID)
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/devices/add/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'upd_device') {

        $post = $_POST;

        $post['formDeviceNombre']  = $post['formDeviceNombre'] ?? '';
        $post['formDeviceEvento']  = $post['formDeviceEvento'] ?? '';
        $post['formDevicePhoneID'] = $post['formDevicePhoneID'] ?? '';

        $formDeviceNombre  = test_input($post['formDeviceNombre']);
        $formDeviceEvento  = test_input($post['formDeviceEvento']);
        $formDevicePhoneID = test_input($post['formDevicePhoneID']);

        if (valida_campo($formDeviceNombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($formDevicePhoneID)) {
            PrintRespuestaJson('error', 'Falta Phone ID');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'           => $_SESSION["RECID_CLIENTE"],
            'deviceName'    => urlencode($formDeviceNombre),
            'deviceEvent'   => ($formDeviceEvento),
            'devicePhoneID' => ($formDevicePhoneID)
        );
        $api = "api/v1/devices/upd/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'del_device') {

        $post = $_POST;

        $post['formDeviceNombre']  = $post['formDeviceNombre'] ?? '';
        $post['formDeviceEvento']  = $post['formDeviceEvento'] ?? '';
        $post['formDevicePhoneID'] = $post['formDevicePhoneID'] ?? '';

        $formDeviceNombre  = test_input($post['formDeviceNombre']);
        $formDeviceEvento  = test_input($post['formDeviceEvento']);
        $formDevicePhoneID = test_input($post['formDevicePhoneID']);

        if (valida_campo($formDevicePhoneID)) {
            PrintRespuestaJson('error', 'Falta Phone ID');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'           => $_SESSION["RECID_CLIENTE"],
            'deviceName'    => urlencode($formDeviceNombre),
            'deviceEvent'   => ($formDeviceEvento),
            'devicePhoneID' => ($formDevicePhoneID)
        );
        $api = "api/v1/devices/del/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'add_usuario') {

        $post = $_POST;

        $post['formUserName']  = $post['formUserName'] ?? '';
        $post['formUserID']    = $post['formUserID'] ?? '';
        $post['formUserRegid'] = $post['formUserRegid'] ?? '';

        $formUserName  = test_input($post['formUserName']);
        $formUserID    = test_input($post['formUserID']);
        $formUserRegid = test_input($post['formUserRegid']);

        if (valida_campo($formUserName)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($formUserID)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'userName'  => urlencode($formUserName),
            'userID'    => ($formUserID),
            'userRegid' => ($formUserRegid)
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/users/add/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'upd_usuario') {

        $post = $_POST;

        $post['formUserName']  = $post['formUserName'] ?? '';
        $post['formUserID']    = $post['formUserID'] ?? '';
        $post['formUserRegid'] = $post['formUserRegid'] ?? '';

        $formUserName  = test_input($post['formUserName']);
        $formUserID    = test_input($post['formUserID']);
        $formUserRegid = test_input($post['formUserRegid']);

        if (valida_campo($formUserName)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($formUserID)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'userName'  => urlencode($formUserName),
            'userID'    => ($formUserID),
            'userRegid' => ($formUserRegid)
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/users/upd/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'del_usuario') {

        $post = $_POST;

        $post['formUserName']  = $post['formUserName'] ?? '';
        $post['formUserID']    = $post['formUserID'] ?? '';
        $post['formUserRegid'] = $post['formUserRegid'] ?? '';

        $formUserName  = test_input($post['formUserName']);
        $formUserID    = test_input($post['formUserID']);
        $formUserRegid = test_input($post['formUserRegid']);

        if (valida_campo($formUserID)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'userName'  => urlencode($formUserName),
            'userID'    => ($formUserID),
            'userRegid' => ($formUserRegid)
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/users/del/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'send_mensaje') {

        $_POST['modalMsgRegID']   = $_POST['modalMsgRegID'] ?? '';
        $_POST['modalMsgMensaje'] = $_POST['modalMsgMensaje'] ?? '';

        $modalMsgRegID   = test_input($_POST['modalMsgRegID']);
        $modalMsgMensaje = test_input($_POST['modalMsgMensaje']);

        if (valida_campo($modalMsgRegID)) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        };

        if (valida_campo($modalMsgMensaje)) {
            PrintRespuestaJson('error', 'El Mensaje es requerido');
            exit;
        };

        $paramsApi = array(
            'key'     => $_SESSION["RECID_CLIENTE"],
            'regID'   => urlencode($modalMsgRegID),
            'message' => urlencode($modalMsgMensaje),
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/msg/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'send_UserSet') {

        $_POST['regid']  = $_POST['regid'] ?? '';
        $_POST['userid'] = $_POST['userid'] ?? '';
        $userRegID       = test_input($_POST['regid']);
        $userID          = test_input($_POST['userid']);

        if (valida_campo($userRegID)) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        };

        if (valida_campo($userID)) {
            PrintRespuestaJson('error', 'El regid es requerido');
            exit;
        };
        if (strlen($userID) > 11) {
            PrintRespuestaJson('error', 'El userID no puede ser mayor a 11 caracteres');
            exit;
        };

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'userID'    => $userID,
            'regID'     => $userRegID,
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/set/";
        $url   = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status'  => $status,
        );
        echo json_encode($json_data);
        exit;
    } else {
        $json_data = array(
            "Mensaje" => 'Invalid Request Type',
            'status'  => 'Error',
        );
        echo json_encode($json_data);
        exit;
    }
} else {
    $json_data = array(
        "Mensaje" => 'Invalid Request Method',
        'status'  => 'Error',
    );
    echo json_encode($json_data);
    exit;
}
exit;
