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
    if ($_POST['tipo'] == 'c_mensaje-old') {

        $_POST['regid']   = $_POST['regid'] ?? '';
        $_POST['mensaje'] = $_POST['mensaje'] ?? '';

        $regid   = test_input($_POST['regid']);
        $mensaje = test_input($_POST['mensaje']);

        if (valida_campo($regid)) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        };

        if (valida_campo($mensaje)) {
            PrintRespuestaJson('error', 'El Mensaje es requerido');
            exit;
        };

        //setup request to send json via POST
        $message[] = $mensaje;

        $data = array(
            'notificationId' => 1,
            'description' => $message,
            'eventType' => 1
        );
        $data = array(
            'data' => array('data' => $data),
            'to' => $regid
        );

        $payload = json_encode($data);

        function sendMessaje($url, $payload, $timeout = 10)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $headers = [
                'Content-Type: application/json',
                'Authorization:key=AAAALZBjrKc:APA91bH2dmW3epeVB9UFRVNPCXoKc27HMvh6Y6m7e4oWEToMSBDEc4U7OUJhm2yCkcRKGDYPqrP3J2fktNkkTJj3mUGQBIT2mOLGEbwXfGSPAHg_haryv3grT91GkKUxqehYZx_0_kX8'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $file_contents = curl_exec($ch);
            curl_close($ch);
            return ($file_contents) ? $file_contents : false;
            exit;
        }

        $url = 'https://fcm.googleapis.com/fcm/send';
        $sendMensaje = sendMessaje($url, $payload, 10);

        if (json_decode($sendMensaje)->success == '1') {
            $data = array('status' => 'ok', 'Mensaje' => 'Mensaje enviado correctamente', 'respuesta' => json_decode($sendMensaje), 'payload' => json_decode($payload));
            echo json_encode($data);
            exit;
        } else {
            $data = array('status' => 'error', 'Mensaje' => 'No se pudo enviar el mensaje', 'respuesta' => json_decode($sendMensaje), 'payload' => json_decode($payload));
            echo json_encode($data);
            exit;
        }
    } else if ($_POST['tipo'] == 'c_settings') {

        $_POST['regid']   = $_POST['regid'] ?? '';
        $regid   = test_input($_POST['regid']);

        if (valida_campo($regid)) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        };

        $cancellationReasons[] = '';
        $operations[] = '';

        $data = array(
            'notificationId'      => 195,
            'eventType'           => 0,
            'apiKey'              => '7BB3A26C25687BCD56A9BAF353A78',
            'locationIp'          => 'http://190.7.56.83',
            'serverIp'            => 'http://190.7.56.83',
            'updateInterval'      => 90,
            'fastestInterval'     => 60,
            'cancellationReasons' => $cancellationReasons,
            'operations'          => $operations,
        );

        $data = array(
            'to' => $regid,
            'data' => array('data' => $data)
        );
        $payload = json_encode($data);

        function sendMessaje($url, $payload, $timeout = 10)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $headers = [
                'Content-Type: application/json',
                'Authorization:key=AAAALZBjrKc:APA91bH2dmW3epeVB9UFRVNPCXoKc27HMvh6Y6m7e4oWEToMSBDEc4U7OUJhm2yCkcRKGDYPqrP3J2fktNkkTJj3mUGQBIT2mOLGEbwXfGSPAHg_haryv3grT91GkKUxqehYZx_0_kX8'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $file_contents = curl_exec($ch);
            curl_close($ch);
            return ($file_contents) ? $file_contents : false;
            exit;
        }

        $url = 'https://fcm.googleapis.com/fcm/send';
        $sendMensaje = sendMessaje($url, $payload, 10);

        if (json_decode($sendMensaje)->success == '1') {
            $data = array('status' => 'ok', 'Mensaje' => 'Dispositivo configurado correctamente', 'respuesta' => json_decode($sendMensaje));
            echo json_encode($data);
            exit;
        } else {
            $data = array('status' => 'error', 'Mensaje' => 'No se puedo configurar el dispositivo', 'respuesta' => json_decode($sendMensaje));
            echo json_encode($data);
            exit;
        }
    } else if ($_POST['tipo'] == 'transferir') {

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
    } else if ($_POST['tipo'] == 'c_setUserEmp') {

        $_POST['regid']  = $_POST['regid'] ?? '';
        $_POST['userid'] = $_POST['userid'] ?? '';
        $regid           = test_input($_POST['regid']);
        $userid          = test_input($_POST['userid']);

        if (valida_campo($regid)) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        };
        if (valida_campo($userid)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        };

        $cancellationReasons[] = '';
        $operations[] = '';

        $data = array(
            'eventType'           => 101,
            'apiKey'              => '7BB3A26C25687BCD56A9BAF353A78',
            'companyCode'          => $id_company,
            'employeId'            => $userid,
        );
        $data = array(
            'to' => $regid,
            'data' => array('data' => $data)
        );

        $payload = json_encode($data);

        function sendMessaje($url, $payload, $timeout = 10)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $headers = [
                'Content-Type: application/json',
                'Authorization:key=AAAALZBjrKc:APA91bH2dmW3epeVB9UFRVNPCXoKc27HMvh6Y6m7e4oWEToMSBDEc4U7OUJhm2yCkcRKGDYPqrP3J2fktNkkTJj3mUGQBIT2mOLGEbwXfGSPAHg_haryv3grT91GkKUxqehYZx_0_kX8'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $file_contents = curl_exec($ch);
            curl_close($ch);
            return ($file_contents) ? $file_contents : false;
            exit;
        }

        $url = 'https://fcm.googleapis.com/fcm/send';
        $sendMensaje = sendMessaje($url, $payload, 10);

        if (json_decode($sendMensaje)->success == '1') {
            $data = array('status' => 'ok', 'Mensaje' => 'Dispositivo configurado correctamente', 'respuesta' => json_decode($sendMensaje));
            echo json_encode($data);
            exit;
        } else {
            $data = array('status' => 'error', 'Mensaje' => 'No se puedo configurar el dispositivo', 'respuesta' => json_decode($sendMensaje));
            echo json_encode($data);
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
