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
            'key'        => $_SESSION["RECID_CLIENTE"],
            'phoneRegId' => urlencode($modalMsgRegID),
            'message'    => urlencode($modalMsgMensaje),
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
        $phoneRegId       = test_input($_POST['regid']);
        $userID          = test_input($_POST['userid']);

        if (valida_campo($phoneRegId)) {
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
            'key'        => $_SESSION["RECID_CLIENTE"],
            'userID'     => $userID,
            'phoneRegId' => $phoneRegId,
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
    } else if ($_POST['tipo'] == 'add_zone') {

        $post = $_POST;

        // PrintRespuestaJson('error', 'Zona');
        // exit;

        $post['formZoneNombre'] = $post['formZoneNombre'] ?? '';
        $post['formZoneRadio']  = $post['formZoneRadio'] ?? '';
        $post['lat']            = $post['lat'] ?? '';
        $post['lng']            = $post['lng'] ?? '';

        $nombre = test_input($post['formZoneNombre']);
        $radio  = test_input($post['formZoneRadio']);
        $lat    = test_input($post['lat']);
        $lng    = test_input($post['lng']);


        if (valida_campo($nombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($radio)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        };
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'zoneName'  => urlencode($nombre),
            'zoneRadio' => ($radio),
            'zoneLat'   => ($lat),
            'zoneLng'   => ($lng)
        );

        $api = "api/v1/zones/add/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
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
    } else if ($_POST['tipo'] == 'create_zone') {

        $post = $_POST;

        // PrintRespuestaJson('error', 'Zona');
        // exit;

        $post['formZoneNombre'] = $post['formZoneNombre'] ?? '';
        $post['formZoneRadio']  = $post['formZoneRadio'] ?? '';
        $post['lat']            = $post['lat'] ?? '';
        $post['lng']            = $post['lng'] ?? '';

        $nombre = test_input($post['formZoneNombre']);
        $radio  = test_input($post['formZoneRadio']);
        $lat    = test_input($post['lat']);
        $lng    = test_input($post['lng']);


        if (valida_campo($nombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($radio)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        };
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'zoneName'  => urlencode($nombre),
            'zoneRadio' => ($radio),
            'zoneLat'   => ($lat),
            'zoneLng'   => ($lng)
        );

        $api = "api/v1/zones/add/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
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
    } else if ($_POST['tipo'] == 'upd_zone') {

        $post = $_POST;

        // PrintRespuestaJson('error', 'Zona');
        // exit;

        $post['formZoneNombre'] = $post['formZoneNombre'] ?? '';
        $post['formZoneRadio']  = $post['formZoneRadio'] ?? '';
        $post['lat']            = $post['lat'] ?? '';
        $post['lng']            = $post['lng'] ?? '';
        $post['idZone']            = $post['idZone'] ?? '';

        $nombre = test_input($post['formZoneNombre']);
        $radio  = test_input($post['formZoneRadio']);
        $lat    = test_input($post['lat']);
        $lng    = test_input($post['lng']);
        $idZone = test_input($post['idZone']);


        if (valida_campo($nombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($radio)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        };
        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        };
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        };
        if (valida_campo($idZone)) {
            PrintRespuestaJson('error', 'Falta ID de Zona');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'zoneName'  => urlencode($nombre),
            'zoneRadio' => ($radio),
            'zoneLat'   => ($lat),
            'zoneLng'   => ($lng),
            'idZone'    => ($idZone)
        );

        $api = "api/v1/zones/upd/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
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
    } else if ($_POST['tipo'] == 'del_zone') {

        $post = $_POST;

        // PrintRespuestaJson('error', 'Del Zona');
        // exit;

        $post['idZone']            = $post['idZone'] ?? '';

        $idZone = test_input($post['idZone']);

        if (valida_campo($idZone)) {
            PrintRespuestaJson('error', 'Falta ID de Zona');
            exit;
        };

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'idZone'    => ($idZone)
        );

        $api = "api/v1/zones/del/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
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
    } else if ($_POST['tipo'] == 'proccesZone') {

        $post = $_POST;

        $post['lat']    = $post['lat'] ?? '';
        $post['lng']    = $post['lng'] ?? '';
        $post['reguid'] = $post['reguid'] ?? '';


        $lat    = test_input($post['lat']);
        $lng    = test_input($post['lng']);
        $reguid = test_input($post['reguid']);

        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        };
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        };
        if (valida_campo($reguid)) {
            PrintRespuestaJson('error', 'Falta RegUID');
            exit;
        };

        $paramsApi = array(
            'key'       => $_SESSION["RECID_CLIENTE"],
            'zoneLat'    => ($lat),
            'zoneLng'    => ($lng),
            'regUID'     => ($reguid)
        );

        $api = "api/v1/zones/process/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
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
