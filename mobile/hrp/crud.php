<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

$_POST['tipo'] = $_POST['tipo'] ?? '';

$id_company = $_SESSION["ID_CLIENTE"];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['tipo'] == 'transferir') {
        $_POST['legFech'] = $_POST['legFech'] ?? '';
        $legFech = explode('@', $_POST['legFech']);
        $legajo = test_input($legFech[0]);
        $fecha = test_input($legFech[1]);
        $hora = test_input($legFech[2]);

        if (valida_campo($legajo)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        }
        ;
        if (($legajo == '0')) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        }
        ;
        if (valida_campo($fecha)) {
            PrintRespuestaJson('error', 'Falta Fecha');
            exit;
        }
        ;
        if (valida_campo($hora)) {
            PrintRespuestaJson('error', 'Falta Hora');
            exit;
        }
        ;

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
    } else if ($_POST['tipo'] == 'add_device') { // auditado

        $post = $_POST;

        $post['formDeviceNombre'] = $post['formDeviceNombre'] ?? '';
        $post['formDeviceEvento'] = $post['formDeviceEvento'] ?? '';
        $post['formDevicePhoneID'] = $post['formDevicePhoneID'] ?? '';

        $formDeviceNombre = test_input($post['formDeviceNombre']);
        $formDeviceEvento = test_input($post['formDeviceEvento']);
        $formDevicePhoneID = test_input($post['formDevicePhoneID']);

        if (valida_campo($formDevicePhoneID)) {
            PrintRespuestaJson('error', 'Falta Phone ID');
            exit;
        }
        ;
        if (valida_campo($formDeviceNombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'deviceName' => urlencode($formDeviceNombre),
            'deviceEvent' => ($formDeviceEvento),
            'devicePhoneID' => ($formDevicePhoneID)
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/devices/add/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'A', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'upd_device') { // auditado

        $post = $_POST;

        $post['formDeviceNombre'] = $post['formDeviceNombre'] ?? '';
        $post['formDeviceEvento'] = $post['formDeviceEvento'] ?? '';
        $post['formDevicePhoneID'] = $post['formDevicePhoneID'] ?? '';

        $formDeviceNombre = test_input($post['formDeviceNombre']);
        $formDeviceEvento = test_input($post['formDeviceEvento']);
        $formDevicePhoneID = test_input($post['formDevicePhoneID']);

        if (valida_campo($formDeviceNombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($formDevicePhoneID)) {
            PrintRespuestaJson('error', 'Falta Phone ID');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'deviceName' => urlencode($formDeviceNombre),
            'deviceEvent' => ($formDeviceEvento),
            'devicePhoneID' => ($formDevicePhoneID)
        );
        $api = "api/v1/devices/upd/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'M', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'del_device') { // auditado

        $post = $_POST;

        $post['formDeviceNombre'] = $post['formDeviceNombre'] ?? '';
        $post['formDeviceEvento'] = $post['formDeviceEvento'] ?? '';
        $post['formDevicePhoneID'] = $post['formDevicePhoneID'] ?? '';

        $formDeviceNombre = test_input($post['formDeviceNombre']);
        $formDeviceEvento = test_input($post['formDeviceEvento']);
        $formDevicePhoneID = test_input($post['formDevicePhoneID']);

        if (valida_campo($formDevicePhoneID)) {
            PrintRespuestaJson('error', 'Falta Phone ID');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'deviceName' => urlencode($formDeviceNombre),
            'deviceEvent' => ($formDeviceEvento),
            'devicePhoneID' => ($formDevicePhoneID)
        );
        $api = "api/v1/devices/del/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'B', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'add_usuario') { //auditado

        $post = $_POST;

        $post['formUserName'] = $post['formUserName'] ?? '';
        $post['formUserID'] = $post['formUserID'] ?? '';
        $post['formUserRegid'] = $post['formUserRegid'] ?? '';
        $post['formUserExpired'] = $post['formUserExpired'] ?? '';
        $post['formUserEstado'] = $post['formUserEstado'] ?? '0';
        $post['formUserArea'] = $post['formUserArea'] ?? '0';
        $post['formUserMotivo'] = $post['formUserMotivo'] ?? '';

        $formUserName = test_input($post['formUserName']);
        $formUserID = test_input($post['formUserID']);
        $formUserRegid = test_input($post['formUserRegid']);
        $formUserExpired = test_input($post['formUserExpired']);
        $formUserMotivo = test_input($post['formUserMotivo']);
        $formUserEstado = test_input($post['formUserEstado']);
        $formUserArea = test_input($post['formUserArea']);


        if (valida_campo($formUserName)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($formUserID)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        }
        ;

        if ($formUserExpired) {
            $DateRange = explode(' al ', $formUserExpired);
            $FechaIni = test_input(dr_fecha($DateRange[0]));
            $FechaFin = test_input(dr_fecha($DateRange[1]));
        } else {
            $FechaIni = '';
            $FechaFin = '';
        }

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'userName' => urlencode($formUserName),
            'userID' => ($formUserID),
            'userRegid' => ($formUserRegid),
            'status' => ($formUserEstado),
            'hasArea' => ($formUserArea),
            'expiredStart' => ($FechaIni),
            'expiredEnd' => ($FechaFin),
            'textLock' => ($formUserMotivo),
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/users/add/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'A', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'add_usuarios') {
        $pathTemp = __DIR__ . '/temp';
        $flag = $_POST['flag'] ?? '';

        if (!$flag) {
            PrintRespuestaJson('error', 'Flag no proporcionado');
            exit;
        }

        $fileData = "{$flag}.json";
        $file = "{$pathTemp}/{$fileData}";

        if (!file_exists($file)) {
            PrintRespuestaJson('error', 'Archivo no encontrado');
            exit;
        }

        $data = json_decode(file_get_contents($file), true);

        if ($data === null) {
            PrintRespuestaJson('error', 'Error al leer el archivo de datos.');
            exit;
        }

        $idCompany = $_SESSION['ID_CLIENTE'];

        $payload = [
            'key' => $_SESSION["RECID_CLIENTE"],
            'usuarios' => $data,
        ];

        $api = "api/v1/users/adds/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        // $url = "https://chweb.local" . "/" . HOMEHOST . "/mobile/hrp/" . $api;

        // Aquí estamos  enviando los datos a la api
        $api = sendRemoteDatausuarios($url, $payload, 10);

        print_r($api);

    } else if ($_POST['tipo'] == 'upd_usuario') { //auditado

        $post = $_POST;

        $post['formUserName'] = $post['formUserName'] ?? '';
        $post['formUserID'] = $post['formUserID'] ?? '';
        $post['formUserRegid'] = $post['formUserRegid'] ?? '';
        $post['formUserExpired'] = $post['formUserExpired'] ?? '';
        $post['formUserEstado'] = $post['formUserEstado'] ?? '0';
        $post['formUserArea'] = $post['formUserArea'] ?? '0';
        $post['formUserMotivo'] = $post['formUserMotivo'] ?? '';

        $formUserName = test_input($post['formUserName']);
        $formUserID = test_input($post['formUserID']);
        $formUserRegid = test_input($post['formUserRegid']);
        $formUserExpired = test_input($post['formUserExpired']);
        $formUserMotivo = test_input($post['formUserMotivo']);
        $formUserEstado = test_input($post['formUserEstado']);
        $formUserArea = test_input($post['formUserArea']);

        if (valida_campo($formUserName)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($formUserID)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        }
        ;

        if ($formUserExpired) {
            $DateRange = explode(' al ', $formUserExpired);
            $FechaIni = test_input(dr_fecha($DateRange[0]));
            $FechaFin = test_input(dr_fecha($DateRange[1]));
        } else {
            $FechaIni = '';
            $FechaFin = '';
        }

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'userName' => urlencode($formUserName),
            'userID' => ($formUserID),
            'userRegid' => ($formUserRegid),
            'status' => ($formUserEstado),
            'hasArea' => ($formUserArea),
            'expiredStart' => ($FechaIni),
            'expiredEnd' => ($FechaFin),
            'textLock' => ($formUserMotivo),
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/users/upd/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'M', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'del_usuario') { //auditado

        $post = $_POST;

        $post['formUserName'] = $post['formUserName'] ?? '';
        $post['formUserID'] = $post['formUserID'] ?? '';
        $post['formUserRegid'] = $post['formUserRegid'] ?? '';

        $formUserName = test_input($post['formUserName']);
        $formUserID = test_input($post['formUserID']);
        $formUserRegid = test_input($post['formUserRegid']);

        if (valida_campo($formUserID)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'userName' => urlencode($formUserName),
            'userID' => ($formUserID),
            'userRegid' => ($formUserRegid)
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/users/del/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'B', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'send_mensaje') { // auditado

        $_POST['modalMsgRegID'] = $_POST['modalMsgRegID'] ?? '';
        $_POST['modalMsgMensaje'] = $_POST['modalMsgMensaje'] ?? '';
        $_POST['userID'] = $_POST['userID'] ?? '';

        $modalMsgRegID = test_input($_POST['modalMsgRegID']);
        $modalMsgMensaje = test_input($_POST['modalMsgMensaje']);
        $userID = test_input($_POST['userID']);

        if (valida_campo($modalMsgRegID)) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        }
        ;

        if (valida_campo($modalMsgMensaje)) {
            PrintRespuestaJson('error', 'El Mensaje es requerido');
            exit;
        }
        ;

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'phoneRegId' => urlencode($modalMsgRegID),
            'message' => urlencode($modalMsgMensaje),
        );

        $api = "api/v1/msg/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            $usuario = simple_pdoQuery("SELECT * FROM reg_user_ where reg_user_.id_user  = '$userID' LIMIT 1");
            auditoria("Mensaje Mobile Enviado. Mensaje = $modalMsgMensaje - Usuario = $userID $usuario[nombre]", 'A', $usuario['id_company'], '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'send_UserSet') { // auditado

        $_POST['regid'] = $_POST['regid'] ?? '';
        $_POST['userid'] = $_POST['userid'] ?? '';
        $phoneRegId = test_input($_POST['regid']);
        $userID = test_input($_POST['userid']);

        if (valida_campo($phoneRegId)) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        }
        ;

        if (valida_campo($userID)) {
            PrintRespuestaJson('error', 'El regid es requerido');
            exit;
        }
        ;
        if (strlen($userID) > 11) {
            PrintRespuestaJson('error', 'El userID no puede ser mayor a 11 caracteres');
            exit;
        }
        ;

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'userID' => $userID,
            'phoneRegId' => $phoneRegId,
        );
        // $api = "api/v1/devices/upd/$parametros";
        // $api = "api/v1/devices/del/$parametros";
        $api = "api/v1/set/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);


        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            $usuario = simple_pdoQuery("SELECT * FROM reg_user_ where reg_user_.id_user  = '$userID' LIMIT 1");
            auditoria("Seteo Mobile Enviado. Usuario = $userID $usuario[nombre]", 'A', $usuario['id_company'], '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'send_DeviceSet') { // auditado    

        $params = (Flight::request()->data);

        $params['deviceName'] = $params['deviceName'] ?? '';
        $params['deviceIDCompany'] = $params['deviceIDCompany'] ?? '';
        $params['deviceRememberUser'] = $params['deviceRememberUser'] ?? '';
        $params['deviceTMEF'] = $params['deviceTMEF'] ?? '';
        $params['deviceUser'] = $params['deviceUser'] ?? '';
        $params['devicePhoneID'] = $params['devicePhoneID'] ?? '';
        $params['deviceRegid'] = $params['deviceRegid'] ?? '';
        $params['deviceInitialize'] = $params['deviceInitialize'] ?? '';
        $params['deviceAppVersion'] = $params['deviceAppVersion'] ?? '';

        $deviceUser = intval($params['deviceUser']);
        $deviceTMEF = intval($params['deviceTMEF']);
        $rememberUser = intval($params['deviceRememberUser']);
        $deviceInitialize = intval($params['deviceInitialize']);

        if (valida_campo($params['deviceRegid'])) {
            PrintRespuestaJson('error', 'Falta Reg ID');
            exit;
        }

        if (strlen($deviceUser) > 11) {
            PrintRespuestaJson('error', 'El usuario no puede ser mayor a 11 caracteres');
            exit;
        }
        if ($rememberUser > 1) {
            PrintRespuestaJson('error', 'Recordar usuario inválido');
            exit;
        }

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'rememberUser' => $rememberUser,
            'TMEF' => $deviceTMEF,
            'userID' => $deviceUser,
            'regid' => $params['deviceRegid'],
            'initialize' => $deviceInitialize,
            'AppVersion' => $params['deviceAppVersion'],
        );

        // echo Flight::json($paramsApi) . exit;

        $api = "api/v1/setDevice/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);
        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'] ?? '';

        if (($api['COUNT'] ?? '') > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            $i = ($deviceInitialize) ? 'Sí' : 'No';
            $r = ($rememberUser) ? 'Sí' : 'No';
            $textAud = "Dispositivo ($params[deviceName]) configurado. TMEF: $deviceTMEF; userID: $deviceUser; phoneID: $params[devicePhoneID]; Inhabilitado: $i; RememberUser: $r; Versión App: $params[deviceAppVersion]";
            auditoria($textAud, 'A', $params['deviceIDCompany'], '32');
        } else {
            $status = 'error';
            $arrayData = ($api['MESSAGE'] ?? '');
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'add_zone') { // auditado

        $post = $_POST;

        $post['formZoneNombre'] = $post['formZoneNombre'] ?? '';
        $post['formZoneRadio'] = $post['formZoneRadio'] ?? '';
        $post['formZoneEvento'] = $post['formZoneEvento'] ?? '';
        $post['lat'] = $post['lat'] ?? '';
        $post['lng'] = $post['lng'] ?? '';

        $nombre = test_input($post['formZoneNombre']);
        $radio = test_input($post['formZoneRadio']);
        $evento = test_input($post['formZoneEvento']);
        $lat = test_input($post['lat']);
        $lng = test_input($post['lng']);


        if (valida_campo($nombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($radio)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        }
        ;
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'zoneName' => urlencode($nombre),
            'zoneRadio' => ($radio),
            'zoneLat' => ($lat),
            'zoneLng' => ($lng),
            'zoneEvent' => ($evento)
        );

        $api = "api/v1/zones/add/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'A', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'create_zone') { // auditado

        $post = $_POST;

        $post['formZoneNombre'] = $post['formZoneNombre'] ?? '';
        $post['formZoneRadio'] = $post['formZoneRadio'] ?? '';
        $post['formZoneEvento'] = $post['formZoneEvento'] ?? '';
        $post['lat'] = $post['lat'] ?? '';
        $post['lng'] = $post['lng'] ?? '';
        $post['regUID'] = $post['regUID'] ?? '';
        $post['regLat'] = $post['regLat'] ?? '';
        $post['regLng'] = $post['regLng'] ?? '';

        /** data de lazona a crear */
        $nombre = test_input($post['formZoneNombre']);
        $radio = test_input($post['formZoneRadio']);
        $lat = test_input($post['lat']);
        $lng = test_input($post['lng']);
        $evento = test_input($post['formZoneEvento']);


        if (valida_campo($nombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($radio)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        }
        ;
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        // PrintRespuestaJson('error', 'procesarZonas.php?company='.$idCompany);
        // exit;

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'zoneName' => urlencode($nombre),
            'zoneRadio' => ($radio),
            'zoneLat' => ($lat),
            'zoneLng' => ($lng),
            'zoneEvent' => ($evento),
        );

        $api = "api/v1/zones/add/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {

            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'A', $idCompany, '32');
            /** data del registro */
            $regLat = test_input($post['regLat']);
            $regLng = test_input($post['regLng']);
            $regUID = test_input($post['regUID']);

            $paramsApi = array(
                'key' => $_SESSION["RECID_CLIENTE"],
                'zoneLat' => ($regLat),
                'zoneLng' => ($regLng),
                'regUID' => ($regUID)
            );

            // $u = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/procesarZonas.php?company=".$idCompany;
            // $g = json_decode(file_get_contents("$u"));
            // procear url de la api


            // PrintRespuestaJson('error', $g);
            // exit;

            $api = "api/v1/zones/process/";
            $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
            $api = sendRemoteData($url, $paramsApi, $timeout = 10);

            $api = json_decode($api, true);

            $totalRecords = $api['TOTAL'];

            if ($api['COUNT'] > 0) {
                $status2 = 'ok';
                $arrayData2 = $api['RESPONSE_DATA'];
                auditoria($api['RESPONSE_DATA']['textAud'], 'P', $idCompany, '32');
            } else {
                $status2 = 'error';
                $arrayData2 = $api['MESSAGE'];
            }
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'upd_zone') { // auditado

        $post = $_POST;

        $post['formZoneNombre'] = $post['formZoneNombre'] ?? '';
        $post['formZoneRadio'] = $post['formZoneRadio'] ?? '';
        $post['formZoneEvento'] = $post['formZoneEvento'] ?? '';
        $post['lat'] = $post['lat'] ?? '';
        $post['lng'] = $post['lng'] ?? '';
        $post['idZone'] = $post['idZone'] ?? '';

        $nombre = test_input($post['formZoneNombre']);
        $radio = test_input($post['formZoneRadio']);
        $lat = test_input($post['lat']);
        $lng = test_input($post['lng']);
        $idZone = test_input($post['idZone']);
        $evento = test_input($post['formZoneEvento']);


        if (valida_campo($nombre)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($radio)) {
            PrintRespuestaJson('error', 'Falta Nombre');
            exit;
        }
        ;
        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        }
        ;
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        }
        ;
        if (valida_campo($idZone)) {
            PrintRespuestaJson('error', 'Falta ID de Zona');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'zoneName' => urlencode($nombre),
            'zoneRadio' => ($radio),
            'zoneLat' => ($lat),
            'zoneLng' => ($lng),
            'idZone' => ($idZone),
            'zoneEvent' => ($evento)
        );

        $api = "api/v1/zones/upd/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'M', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'del_zone') { // auditado

        $post = $_POST;

        // PrintRespuestaJson('error', 'Del Zona');
        // exit;

        $post['idZone'] = $post['idZone'] ?? '';

        $idZone = test_input($post['idZone']);

        if (valida_campo($idZone)) {
            PrintRespuestaJson('error', 'Falta ID de Zona');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'idZone' => ($idZone)
        );

        $api = "api/v1/zones/del/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'B', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'proccesZone') { // auditado

        $post = $_POST;

        $post['lat'] = $post['lat'] ?? '';
        $post['lng'] = $post['lng'] ?? '';
        $post['reguid'] = $post['reguid'] ?? '';


        $lat = test_input($post['lat']);
        $lng = test_input($post['lng']);
        $reguid = test_input($post['reguid']);

        if (valida_campo($lat)) {
            PrintRespuestaJson('error', 'Falta Latitud');
            exit;
        }
        ;
        if (valida_campo($lng)) {
            PrintRespuestaJson('error', 'Falta Longitud');
            exit;
        }
        ;
        if (valida_campo($reguid)) {
            PrintRespuestaJson('error', 'Falta RegUID');
            exit;
        }
        ;

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'zoneLat' => ($lat),
            'zoneLng' => ($lng),
            'regUID' => ($reguid)
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
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'proccesRegFace') { //auditado

        $post = $_POST;

        $post['id_api'] = $post['id_api'] ?? '';

        $id_api = test_input($post['id_api']);

        if (valida_campo($id_api)) {
            PrintRespuestaJson('error', 'Falta ID');
            exit;
        }
        ;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $paramsApi = array(
            'key' => $_SESSION["RECID_CLIENTE"],
            'idApi' => ($id_api),
        );
        $api = "api/v1/enroll/process/";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = sendRemoteData($url, $paramsApi, $timeout = 10);

        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'];

        if ($api['COUNT'] > 0) {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA'];
            auditoria($api['RESPONSE_DATA']['textAud'], 'P', $idCompany, '32');
        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'];
        }
        $json_data = array(
            "Mensaje" => $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;
    } else if ($_POST['tipo'] == 'formTrain') { // enrolar fotos

        $post = $_POST;

        $post['selected'] = $post['selected'] ?? '';
        $post['userID'] = $post['userID'] ?? '';
        $post['type'] = $post['type'] ?? '';

        $selected = $post['selected']; // idPunchEvent separados por coma
        $userID = $post['userID'];
        $type = $post['type'];

        if (empty($userID)) {
            PrintRespuestaJson('error', 'Debe seleccionar al menos un usuario');
            exit;
        }
        if (empty($type)) {
            PrintRespuestaJson('error', 'type no especificado');
            exit;
        }
        if (($type != 'update' && $type != 'enroll')) {
            PrintRespuestaJson('error', 'type erroneo');
            exit;
        }

        $idPunchEvent = explode(',', $selected);

        // print_r($post) . exit;

        $idCompany = $_SESSION['ID_CLIENTE'];

        if ($type == 'enroll') {

            $params = array(
                'key' => $_SESSION["RECID_CLIENTE"],
                'userID' => $userID,
                // "idPunchEvent" => implode(',', $post['idPunchEvent']),
                "idPunchEvent" => $selected,
            );

            $api = "api/v1/enroll/enroll/"; //enroll 
            $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
            // $url   = "http://DESKTOP-8FK3BRD/" . HOMEHOST . "/mobile/hrp/" . $api;
            $api = sendApiData($url, $params, 'POST');
            $api = json_decode($api, true);

            $totalRecords = $api['TOTAL'];

            if ($api['MESSAGE'] == 'OK') {
                $status = 'ok';
                $arrayData = $api['RESPONSE_DATA']['responseApi'];
                auditoria($api['RESPONSE_DATA']['textAud'], 'M', $idCompany, '32');
                checkenroll($_SESSION["RECID_CLIENTE"], $userID, $_SESSION["APIMOBILEHRP"], 4);
            } else {
                $status = 'error';
                $arrayData = $api['MESSAGE'];
            }
            $json_data = array(
                "Mensaje" => $arrayData,
                'status' => $status,
            );
            echo json_encode($json_data);
            exit;
        }
        if ($type == 'update') {

            $params = array(
                'key' => $_SESSION["RECID_CLIENTE"],
                'userID' => $userID,
                // "idPunchEvent" => implode(',', $post['idPunchEvent']),
                "idPunchEvent" => $selected,
            );

            $api = "api/v1/enroll/update/";
            $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
            $api = sendApiData($url, $params, 'POST');
            $api = json_decode($api, true);

            $totalRecords = $api['TOTAL'];

            if ($api['MESSAGE'] == 'OK') {
                $status = 'ok';
                $arrayData = $api['RESPONSE_DATA']['responseApi'];
                auditoria($api['RESPONSE_DATA']['textAud'], 'M', $idCompany, '32');
                checkenroll($_SESSION["RECID_CLIENTE"], $userID, $_SESSION["APIMOBILEHRP"], 4);
            } else {
                $status = 'error';
                $arrayData = $api['MESSAGE'];
            }
            $json_data = array(
                "Mensaje" => $arrayData,
                'status' => $status,
            );
            echo json_encode($json_data);
            exit;
        }

    } else if ($_POST['tipo'] == 'getTokenMobile') { // getTokenMobile

        $post = $_POST;

        $idCompany = $_SESSION['ID_CLIENTE'];

        $params = array(
            'key' => $_SESSION["RECID_CLIENTE"],
        );

        $urlParams = http_build_query($params);
        $api = "api/v1/token/?" . $urlParams;
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = getRemoteFile($url, $timeout = 10);
        $api = json_decode($api, true);

        $totalRecords = $api['TOTAL'] ?? 0;

        if (($api['MESSAGE'] ?? '') == 'OK') {
            $status = 'ok';
            $arrayData = $api['RESPONSE_DATA']['payload'];
            foreach ($arrayData as $key => $value) {
                $config = json_decode($value['config'], true);
                $data[] = array(
                    "companyId" => $value['companyId'],
                    "dateDelete" => $value['dateDelete'],
                    "expirationDate" => FechaFormatVar($value['expirationDate'], 'd/m/Y'),
                    "id" => $value['id'],
                    "token" => $value['token'],
                    "updatedDate" => $value['updatedDate'],
                    "rememberEmployeId" => $config['rememberEmployeId'],
                    "tmef" => $config['tmef'],
                );
            }

        } else {
            $status = 'error';
            $arrayData = $api['MESSAGE'] ?? '';
        }
        $json_data = array(
            "data" => $data ?? $arrayData,
            'status' => $status,
        );
        echo json_encode($json_data);
        exit;

    } else {
        $json_data = array(
            "Mensaje" => 'Invalid Request Type',
            'status' => 'Error',
        );
        echo json_encode($json_data);
        exit;
    }
} else {
    $json_data = array(
        "Mensaje" => 'Invalid Request Method',
        'status' => 'Error',
    );
    echo json_encode($json_data);
    exit;
}
exit;
function sendRemoteDatausuarios($url, $payload, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    // Si el payload contiene arrays complejos, convertirlo a JSON
    if (is_array($payload) && array_key_exists('usuarios', $payload)) {
        $jsonPayload = json_encode($payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno) : $curl_error $url " . json_encode($payload); // set error message
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    exit;
}