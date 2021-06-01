<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../../config/conect_mysql.php';

$id_company = $_SESSION["ID_CLIENTE"];

if (($_POST['tipo'] == 'c_usuario')) {

    $_POST['id_user'] = $_POST['id_user'] ?? '';
    $_POST['nombre']  = $_POST['nombre'] ?? '';
    $_POST['regid']   = $_POST['regid'] ?? '';

    $id_user    = test_input($_POST['id_user']);
    $nombre     = test_input($_POST['nombre']);
    $regid      = test_input($_POST['regid']);

    if (valida_campo($id_user)) {
        PrintRespuestaJson('error', 'Campo Legajo requerido');
        exit;
    };
    if (valida_campo($nombre)) {
        PrintRespuestaJson('error', 'Campo Nombre requerido');
        exit;
    };
    if (strlen($nombre) < '3') {
        PrintRespuestaJson('error', 'El nombre de tener contener mínimo 3 caracteres');
        exit;
    };
    $CheckID_user = CountRegMayorCeroMySql("SELECT 1 FROM reg_user_ WHERE id_user = '$id_user' and id_company = '$id_company' LIMIT 1");
    $CheckRegid = CountRegMayorCeroMySql("SELECT 1 FROM reg_user_ WHERE regid = '$regid' AND regid !=''");

    if ($CheckID_user) {
        PrintRespuestaJson('error', 'Ya existe el Legajo');
        exit;
    }
    if ($CheckRegid) {
        PrintRespuestaJson('error', 'Ya existe el Regid');
        exit;
    }

    $query = "INSERT INTO reg_user_(nombre,id_user,id_company,regid)VALUES('$nombre','$id_user',$id_company,'$regid')";

    $result  = mysqli_query($link, $query);
    if ($result) {
        // audito_ch('A', $Dato);
        PrintRespuestaJson('ok', 'Usuario creado correctamente.');
        mysqli_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', 'Error: ' . mysqli_error($link));
        exit;
    }
} else if ($_POST['tipo'] == 'r_usuario') {

    $_POST['id_user'] = $_POST['id_user'] ?? '';
    $id_user          = test_input($_POST['id_user']);

    $query = "SELECT id_user, nombre, id_company, regid FROM reg_user_ where id_user = '$id_user' LIMIT 1";

    $rs = mysqli_query($link, $query);

    while ($r = mysqli_fetch_assoc($rs)) {
        $array = array(
            'id_company' => $r['id_company'],
            'id_user'    => $r['id_user'],
            'nombre'     => $r['nombre'],
            'regid'      => $r['regid'],
        );
    }
    mysqli_free_result($rs);
    mysqli_close($link);

    echo json_encode($array);
    exit;
} else if ($_POST['tipo'] == 'u_usuario') {

    $_POST['id_user'] = $_POST['id_user'] ?? '';
    $_POST['nombre']  = $_POST['nombre'] ?? '';
    $_POST['regid']   = $_POST['regid'] ?? '';

    $id_user    = test_input($_POST['id_user']);
    $nombre     = test_input($_POST['nombre']);
    $regid      = test_input($_POST['regid']);

    if (valida_campo($id_user)) {
        PrintRespuestaJson('error', 'Campo Legajo requerido');
        exit;
    };
    if (valida_campo($nombre)) {
        PrintRespuestaJson('error', 'Campo Nombre requerido');
        exit;
    };
    if (strlen($nombre) < '3') {
        PrintRespuestaJson('error', 'El nombre de tener contener mínimo 3 caracteres');
        exit;
    };
    $CheckRegid = CountRegMayorCeroMySql("SELECT 1 FROM reg_user_ WHERE regid = '$regid' and id_user != '$id_user' AND regid !=''");

    if ($CheckRegid) {
        PrintRespuestaJson('error', 'Ya existe el Reg ID');
        exit;
    }

    $query = "UPDATE reg_user_ SET nombre = '$nombre', regid = '$regid' WHERE id_user = '$id_user' LIMIT 1";

    $result  = mysqli_query($link, $query);
    if ($result) {
        // audito_ch('A', $Dato);
        PrintRespuestaJson('ok', 'Usuario editado correctamente.');
        mysqli_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', 'Error: ' . mysqli_errno($link));
        exit;
    }
} else if ($_POST['tipo'] == 'd_usuario') {

    $_POST['id_user'] = $_POST['id_user'] ?? '';
    $_POST['nombre']  = $_POST['nombre'] ?? '';
    $_POST['regid']   = $_POST['regid'] ?? '';

    $id_user    = test_input($_POST['id_user']);
    $nombre     = test_input($_POST['nombre']);
    $regid      = test_input($_POST['regid']);

    if (valida_campo($id_user)) {
        PrintRespuestaJson('error', 'Campo Legajo requerido');
        exit;
    };
    $CheckReg = CountRegMayorCeroMySql("SELECT 1 FROM reg_ WHERE id_user = '$id_user' LIMIT 1");

    if ($CheckReg) {
        PrintRespuestaJson('error', 'No se puede elimnar. Existe información en registros');
        exit;
    }

    $query = "DELETE FROM reg_user_ WHERE id_user = '$id_user' LIMIT 1";

    $result  = mysqli_query($link, $query);
    if ($result) {
        // audito_ch('A', $Dato);
        PrintRespuestaJson('ok', 'Usuario eliminado correctamente.');
        mysqli_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', 'Error: ' . mysqli_errno($link));
        exit;
    }
} else if ($_POST['tipo'] == 'c_mensaje') {

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
            'Access-Control-Allow-Origin: *',
            'Accept: */*',
            'Connection: keep-alive',
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
        PrintRespuestaJson('ok', 'Mensaje enviado correctamente');
        exit;
    } else {
        $data = array('status' => 'error', 'Mensaje' => 'No se pudo enviar el mensaje', 'respuesta' => json_decode($sendMensaje), 'payload'=>json_decode($payload));
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
}
mysqli_close($link);
exit;
