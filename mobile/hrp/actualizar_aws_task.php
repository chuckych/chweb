<?php
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
// session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
// ultimoacc();
// secure_auth_ch_json();
$_SERVER["argv"][1] = $_SERVER["argv"][1] ?? '';

if ($_SERVER["argv"][1] != "1ec558a60b5dda24597816c924776716018caf8b") {
    $data = array(
        'Mensaje' => 'Parametro no valido',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'error',
    );
    print_r($data);
    exit;
}

require __DIR__ . '../../../vendor/autoload.php';

use Carbon\Carbon;
function sendEmailTask($subjet, $body)
{
    $url = 'https://ht-api.helpticket.com.ar/sendMail/';

    $data = array(
        "subjet"  => $subjet,
        "to"      => "task-aws-mobile",
        "replyTo" => "task-aws-mobile",
        "body"    => $body
    );

    $timeout = 10;
    $ch = curl_init(); // initialize curl handle
    curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // The number of seconds to wait while trying to connect
    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $headers = array(
        'Content-Type:application/json',
        // Le enviamos JSON al servidor con los datos
        'Token:e47c43594cf22b42588c687c7f7e9871a52245ac' // token
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add headers
    $data_content = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        fileLog($text, __DIR__ . "/logs/" . date('Ymd') . "_sendEmail.log"); // escribimos 
        exit; // salimos del script
    }
    curl_close($ch); // close curl handle
    // return ($data_content);
}
function diffStartEnd($start, $end)
{
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'es_ES.UTF-8');
    $f      = Carbon::parse($start);
    $f2     = Carbon::parse($end);
    $d2 = $f->diffForHumans(null, false, false, 2);
    $diffInSeconds = $f2->diffInSeconds($f);
    $diffInMinutes = $f2->diffInMinutes($f);
    $totalDuration = $diffInSeconds;
    $totalDuration = gmdate("H:i:s", $totalDuration);
    $totalDuration = ($end == '0000-00-00 00:00:00') ? '' : $totalDuration;
    $tareDiff = trim(str_replace('hace', '', $d2));
    $t = array(
        'diffIni' => $tareDiff,
        'duration' => $totalDuration,
        'diffInSeconds' => $diffInSeconds,
        'diffInMinutes' => $diffInMinutes
    );
    return $t;
}
function E_ALL()
{
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}
E_ALL();
function sendMessaje($url, $payload, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $headers = [
        'Accept' => '*/*',
        'Accept-Encoding' => 'gzip, deflate, br',
        'Content-Type: application/json',
        'Authorization:key=AAAALZBjrKc:APA91bH2dmW3epeVB9UFRVNPCXoKc27HMvh6Y6m7e4oWEToMSBDEc4U7OUJhm2yCkcRKGDYPqrP3J2fktNkkTJj3mUGQBIT2mOLGEbwXfGSPAHg_haryv3grT91GkKUxqehYZx_0_kX8',
        'Connection' => 'keep-alive'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return ($file_contents) ? $file_contents : false;
    // exit;
}
function MSQuery($query)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '../../../config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
            }
        }
        sqlsrv_free_stmt($stmt);
        echo json_encode($data[0]);
        sqlsrv_close($link);
        return false;
        // exit;
    }
}
function filtrarObjeto($array, $key, $valor) // Funcion para filtrar un objeto
{
    $r = array_filter($array, function ($e) use ($key, $valor) {
        return $e[$key] === $valor;
    });
    foreach ($r as $key => $value) {
        return ($value);
    }
}
function pdoQuery($sql)
{
    require __DIR__ . '../../../config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        return ($stmt->execute()) ? true : false;
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '../../../logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function simple_pdoQuery($sql)
{
    require __DIR__ . '../../../config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        $stmt->execute();
        // $result = $stmt->fetch(PDO::FETCH_ASSOC);
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            return $row;
        }
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '../../../logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function _group_by_keys($array, $keys = array())
{
    if (($array)) {
        $return = array();
        $append = (count($keys) > 1 ? "_" : null);
        foreach ($array as $val) {
            $final_key = "";
            foreach ($keys as $theKey) {
                $final_key .= $val[$theKey] . $append;
            }
            $return[$final_key][] = $val;
        }
        // return $return;
        foreach ($return as $key => $value) {
            $arrGroup2[] = array_map("unserialize", array_unique(array_map("serialize", $value)));
        }

        foreach ($arrGroup2 as $key => $value2) {
            $arrGroup3[] = $value2[0];
        }
    } else {
        $arrGroup3[] = array();
    }
    return $arrGroup3;
}
function pingApiMobileHRP($urlAppMobile)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlAppMobile . '/attention/api/test/ping');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78',
        'Connection' => 'keep-alive'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return ($file_contents) ? $file_contents : false;
    // exit;
}
function sendApiMobileHRP($payload, $urlApp, $paramsUrl, $idCompany, $post = true)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlApp . '/' . $paramsUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78',
        'Connection' => 'keep-alive'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($payload));
    }
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $MESSAGE = 'Error al enviar al servidor. (' . $curl_error . ')';
        (response(0, 1, $MESSAGE, '', 0, 1, $idCompany));
        exit; // salimos del script
    }
    curl_close($ch);
    return ($file_contents) ? $file_contents : false;
}
function confidenceFaceStr($confidence, $id_api, $threshold)
{
    $i = intval($id_api) ?? 0;
    if ($i > 1) {
        switch ($i) {
            case $confidence == -99:
                $c = 'Registro Sin Foto';
                break;
            case $confidence == -3:
                $c = 'Foto Inválida';
                break;
            case $confidence == -2:
                $c = 'No Enrolado';
                break;
            case $confidence == -1:
                $c = 'Entrenamiento Inválido';
                break;
            case $confidence == 0:
                $c = 'No Identificado';
                break;
            case $confidence >= $threshold:
                $c = 'Identificado';
                break;
            case $confidence < $threshold:
                $c = 'No Identificado';
                break;
            default:
                $c = 'No Disponible';
                break;
        }
    }
    return $c ?? 'No Disponible';
}
function dateDifference($date_1, $date_2, $differenceFormat = '%a') // diferencia en días entre dos fechas
{
    $datetime1 = date_create($date_1); // creo la fecha 1
    $datetime2 = date_create($date_2); // creo la fecha 2
    $interval = date_diff($datetime1, $datetime2); // obtengo la diferencia de fechas
    return $interval->format($differenceFormat); // devuelvo el número de días
}
function borrarLogs($path, $dias, $ext) // borra los logs a partir de una cantidad de días
{
    $files = glob($path . '*' . $ext); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) { // recorremos todos los ficheros.
        $lastModifiedTime = filemtime($file); // obtenemos la fecha de modificación del fichero
        $currentTime      = time(); // obtenemos la fecha actual
        $dateDiff         = dateDifference(date('Ymd', $lastModifiedTime), date('Ymd', $currentTime)); // obtenemos la diferencia de fechas
        ($dateDiff >= $dias) ? unlink($file) : ''; //elimino el fichero
    }
}
function timeZone()
{
    return date_default_timezone_set('America/Argentina/Buenos_Aires');
}
function fechaHora2()
{
    timeZone();
    $t = date("Y-m-d H:i:s");
    return $t;
}
function fileLog($text, $ruta_archivo, $type = false)
{
    $log    = fopen($ruta_archivo, 'a');
    $date   = fechaHora2();
    $text   = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
    fwrite($log, $text);
    fclose($log);
}
function fileLogJson($text, $ruta_archivo, $date = true)
{
    if ($date) {
        $log    = fopen(date('YmdHis') . '_' . $ruta_archivo, 'w');
    } else {
        $log    = fopen($ruta_archivo, 'w');
    }
    $text   = json_encode($text, JSON_PRETTY_PRINT) . "\n";
    fwrite($log, $text);
    fclose($log);
}
function createDir($path, $gitignore = true)
{
    $dirname = dirname($path . '/index.php');
    if (!is_dir($dirname)) {
        mkdir($dirname, 0755, true);

        if ($gitignore) {
            $git = dirname($path . '/.gitignore');
            if (!file_exists($git)) {
                mkdir($path, 0700);
                mkdir($git, 0755, true);
                $logGit    = fopen($git . '/.gitignore', 'a');
                $textGit   = '*';
                fwrite($logGit, $textGit);
                fclose($logGit);
            }
        }

        $log    = fopen($dirname . '/index.php', 'a');
        $text   = '<?php exit;';
        fwrite($log, $text);
        fclose($log);
    }
}
function getDataIni($url) // obtiene el json de la url
{
    if (file_exists($url)) { // si existe el archivo
        $data = file_get_contents($url); // obtenemos el contenido del archivo
        if ($data) { // si el contenido no está vacío
            $data = parse_ini_file($url, true); // Obtenemos los datos del data.php
            return $data; // devolvemos el json
        } else { // si el contenido está vacío
            fileLog("No hay informacion en el archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
        }
    } else { // si no existe el archivo
        fileLog("No existe archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
        return false; // devolvemos false
    }
}
function writeFlags($assoc, $path)
{
    $content = "; <?php exit; ?> <-- ¡No eliminar esta línea! -->\n";
    foreach ($assoc as $key => $elem) {
        $content .= "[" . $key . "]\n";
        foreach ($elem as $key2 => $elem2) {
            if (is_array($elem2)) {
                for ($i = 0; $i < count($elem2); $i++) {
                    $content .= $key2 . "[] =\"" . $elem2[$i] . "\"\n";
                }
            } else if ($elem2 == "") $content .= $key2 . " =\n";
            else $content .= $key2 . " = \"" . $elem2 . "\"\n";
        }
    }
    // $path = __DIR__ . '/flags.ini';
    if (!$handle = fopen($path, 'w')) {
        return false;
    }
    $success = fwrite($handle, $content);
    fclose($handle);
    return $success;
}
function statusFlags($statusFlags, $pathFlags, $createdDate)
{
    $assoc = array(
        'flags' => array(
            'lastDate' => $createdDate,
            'download' => $statusFlags,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );
    writeFlags($assoc, $pathFlags);
    $text = ($statusFlags == '2') ? "Se marco Bandera de espera" : "Se marco Bandera de descarga";
    $pathLog = __DIR__ . '/logs/flagsLog';
    createDir($pathLog);
    fileLog($text, $pathLog . '/flagsLog_aws.log');
    borrarLogs($pathLog . '/', 1, '.log');
}
function getEvents($url, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        $pathLog = __DIR__ . '../../../logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        sendEmailTask("Error al conectar con AWS ".date('Y-m-d H:i:s'), $text);
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '../../../logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
        return false;
    }
    // exit;
}
function queryCalcZone($lat, $lng, $idCompany)
{
    $query = "
            SELECT
            `rg`.*,
        (
                (
                    (
                        acos(
                            sin(($lat * pi() / 180)) * sin((`rg`.`lat` * pi() / 180)) + cos(($lat * pi() / 180)) * cos((`rg`.`lat` * pi() / 180)) * cos((($lng - `rg`.`lng`) * pi() / 180))
                        )
                    ) * 180 / pi()
                ) * 60 * 1.1515 * 1.609344
            ) as distancia
        FROM
            reg_zones rg WHERE `rg`.`id_company` = $idCompany
        -- HAVING (distancia <= 0.1)
        ORDER BY distancia ASC, rg.id DESC LIMIT 1
    ";
    return $query;
}
timeZone();

$iniKeys = (getDataIni(__DIR__ . '../../../mobileApikey.php'));

$pathFlags = __DIR__ . '/flags_aws.php'; // ruta del archivo de Log de errores
$flags = (getDataIni($pathFlags));

if (!$flags) {
    $assoc = array(
        'flags' => array(
            'lastDate' => '1646871812711',
            'download' => 1,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );
    writeFlags($assoc, $pathFlags);
    $flags = (getDataIni($pathFlags));
    $flags_lastDate = $flags['flags']['lastDate'];
    $flags_download = $flags['flags']['download'];
} else {
    $flags_lastDate = $flags['flags']['lastDate'];
    $flags_download = $flags['flags']['download'];
}
// fileLog($flags_lastDate, __DIR__ . '/logs/' . date('Ymd') . '_aws_task_prueba.log').exit;

$company       = array();
$employe       = array();
$data          = array();
$arrayData     = array();
$insertCH      = array();
$insertCH_Fail = array();
$totalSession  = array();
$distancia2 = '';

$start = microtime(true);
$fechaHora = '';
$phoneid = '';
if ($flags_download == 2) {
    $data = array(
        'Mensaje' => 'Aguarde. Hay procesos de descarga en ejecucion.',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'no',
        'time'    => '',
        'total'   => 0
    );
    echo json_encode(array('Response' => $data));
    exit;
}
statusFlags(2, $pathFlags, $flags_lastDate); // marcar bandera de espera
$url   = "http://awsapi.chweb.ar:7575/attention/api/punch-event/" . $flags_lastDate;
// $url   = "http://207.191.165.3:7500/attention/api/punch-event/" . $flags_lastDate;
// $url   = "http://207.191.165.3:7500/attention/api/punch-event/light/" . $flags_lastDate; // SIN FOTO
$array = json_decode(getEvents($url), true);
if (!empty($array['payload'])) {
    foreach ($array['payload'] as $key => $v) {
        $operation = $v['operation']['observations'] ?? '';
        $arrayData[] = array(
            '__v'           => $v['__v'] ?? '',
            '_id'           => $v['_id'] ?? '',
            'accuracy'      => $v['position']['accuracy'],
            'appVersion'    => $v['appVersion'],
            'attphoto'      => $v['attphoto'],
            'batteryLevel'  => $v['position']['batteryLevel'],
            'bearing'       => $v['position']['bearing'],
            'companyCode'   => $v['companyCode'],
            'createdDate'   => $v['createdDate'],
            'dateTime'      => ($v['dateTime']),
            'employeId'     => $v['employeId'],
            'eventType'     => $v['eventType'],
            'gpsStatus'     => $v['position']['gpsStatus'],
            'lat'           => $v['position']['lat'],
            'lng'           => $v['position']['lng'],
            'phoneid'       => $v['phoneid'],
            'regid'         => $v['regid'],
            'speed'         => $v['position']['speed'],
            'sync'          => $v['sync'],
            'operationType' => $v['operationType'],
            'operation'     => $operation,
            'confidence'    => $v['confidence'] ?? '',
            'locked'        => $v['locked'] ?? '0',
            'error'         => $v['error'] ?? '',
            'id_api'        => $v['id'] ?? ''
        );
    }
}
if (!empty($arrayData)) {

    foreach ($arrayData as $key => $v) {

        // $LastStart = ($fechaHora) ? $fechaHora : '';
        // $LastPhoneID = ($phoneid) ? $phoneid : '';
               
        $timestamp     = $v['dateTime'] ?? 0;
        $timestamp     = substr($timestamp, 0, 10);

        // $dates         = new \DateTime();
        // $dates         = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        // $dates->setTimestamp($timestamp);

        $dates = new DateTime("@" . $timestamp);  // will snap to UTC because of the 
        $dates->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
        echo $dates->format('Y-m-d H:i:s') . PHP_EOL;  // Buenos_Aires time    


        $fechaHora     = $dates->format('Y-m-d H:i:s');
        $fechaHoraCH   = $dates->format('Ymd');
        $hora          = $dates->format('H:i');
        $__v           = $v['__v'];
        $_id           = $v['_id'];
        $accuracy      = $v['accuracy'];
        $appVersion    = $v['appVersion'];
        $attphoto      = $v['attphoto'];
        $batteryLevel  = $v['batteryLevel'];
        $bearing       = $v['bearing'];
        $companyCode   = $v['companyCode'];
        $createdDate   = $v['createdDate'];
        $dateTime      = $v['dateTime'];
        $dateTime      = substr($dateTime, 0, 10);
        $employeId     = $v['employeId'];
        $eventType     = $v['eventType'];
        $gpsStatus     = $v['gpsStatus'];
        $lat           = $v['lat'];
        $lng           = $v['lng'];
        $phoneid       = $v['phoneid'];
        $regid         = $v['regid'];
        $speed         = $v['speed'];
        $sync          = $v['sync'];
        $operationType = $v['operationType'];
        $operation     = $v['operation'];
        $checkPhoto    = ($attphoto) ? '0' : '1';
        $confidence    = $v['confidence'] ?? '';
        $locked        = $v['locked'];
        $error         = $v['error'];
        $id_api        = $v['id_api'];

        

        if ($companyCode == '-1') {
            $go = true;
            // $tmef = simple_pdoQuery("SELECT MAX(`fechaHora`) AS 'fechaHora'  FROM `reg_` WHERE `phoneid` = '$phoneid'");

            // if ($LastStart = $fechaHora && $LastPhoneID = $phoneid) {
            //     // $LastStart = $tmef['fechaHora']; // Fecha Hora de la ultima fichada
            //     $end   = $fechaHora; // Fecha Hora de la fichada entrante
            //     $go    = (diffStartEnd($LastStart, $end)['diffInMinutes'] > 1) ? true : false; // si la diferencia es mayor a 1 minutos go true
            //     $pathDiff = __DIR__ . '/logs/' . date('Ymd') . '_tmef.log';
            //     ($go) ? fileLog("TMEF = ".diffStartEnd($LastStart, $end)['diffInMinutes'], $pathDiff) : fileLog("TMEF = ".diffStartEnd($LastStart, $end)['diffInMinutes'], $pathDiff);
            // }
            if ($go) {
                // do {
                    $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_setting_auto_mobile.log'; // ruta del archivo de Log de errores
                    // Si el ID Company es -1 actualizamos el telefono si el phone id tiene un alias asociado
                    $q = "SELECT regid, id_company FROM reg_device_ WHERE phoneid = $phoneid and regid != '' LIMIT 1";
                    $aq = simple_pdoQuery($q); //Buscamos el regis en los dispositivos
                    $aqjson = json_encode($aq);
                    ($aq) ? fileLog("Se encontro phoneID en Dispositivos. Usuario = $employeId. phoneID = $phoneid. IDCompany $aq[id_company]\n$aqjson", $pathLog) : '';
                    if (!$aq) {
                        fileLog("No se encontro phoneID en dispositivos. Usuario = $employeId. phoneID = $phoneid. IDCompany $aq[id_company]\n$aqjson", $pathLog);
                        //  Si no hay regid en dispositivos busvamos en la ultima fichada del phoneid
                        $q = "SELECT regid, id_company FROM reg_ WHERE phoneid = '$phoneid' AND id_company > 0 ORDER BY rid DESC LIMIT 1";
                        $aq = simple_pdoQuery($q);
                        $aqjson = json_encode($aq);
                        fileLog("Se encontro phoneID en fichadas. Usuario = $employeId. phoneID = $phoneid. IDCompany $aq[id_company]\n$aqjson", $pathLog);
                    }
                    if ($aq) {
                        $q2 = "SELECT nombre, recid FROM clientes WHERE id = $aq[id_company] LIMIT 1";
                        $aq2 = simple_pdoQuery($q2);
                        $aq2json = json_encode($aq2);
                        $cancellationReasons[] = '';
                        $operations[] = '';
                        $dataSend = array(
                            'eventType'           => 0,
                            'apiKey'              => '7BB3A26C25687BCD56A9BAF353A78',
                            'companyCode'         => $aq['id_company'],
                            'employeId'           => $employeId,
                            "recid"               => $aq2['recid'],
                            'notificationId'      => 195,
                            'locationIp'          => "http://190.7.56.83",
                            'serverIp'            => "http://awsapi.chweb.ar:7575",
                            'updateInterval'      => 90,
                            'fastestInterval'     => 60,
                            'cancellationReasons' => $cancellationReasons,
                            'operations'          => $operations
                        );
                        $data2 = array(
                            'to' => $aq['regid'],
                            'data' => array('data' => $dataSend)
                        );
                        $payload = json_encode($data2);
                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $sendSettings = sendMessaje($url, $payload, 10);
                        $responseSettings = json_decode($sendSettings)->results;
                        $responseSettings = json_encode($responseSettings);
                        if (json_decode($sendSettings)->success == 1) {
                            fileLog("Setting enviado. Usuario = $employeId. phoneID = $phoneid\n$responseSettings\n$aq2json", $pathLog); // escribir en el log
                            /** Enviamos un mensaje al usuario  * */

                            $mensaje[] = "Dispositivo configurado correctamente. \nFiche nuevamente.\nGracias\n" . date('d/m/Y H:m');

                            $dataMsg = array(
                                'notificationId' => 1,
                                'description' => $mensaje,
                                'eventType' => 1
                            );

                            $dataMsg = array(
                                'data' => array('data' => $dataMsg),
                                'to' => $aq['regid']
                            );

                            $payloadMsg = json_encode($dataMsg);
                            sleep(2);
                            $sendMensaje = sendMessaje($url, $payloadMsg, 10);
                            $responseMsg = json_decode($sendMensaje)->results;
                            $responseMsg = json_encode($responseMsg);
                            if (json_decode($sendMensaje)->success == 1) {
                                fileLog("Mensaje enviado. Usuario = $employeId. phoneID = $phoneid\n$responseMsg", $pathLog);
                            } else {
                                fileLog("Mensaje NO enviado. Usuario = $employeId. phoneID = $phoneid\n$responseMsg", $pathLog);
                            }
                            /**
                             *  Fin de mensaje
                             */
                        } else {
                            fileLog("Setting no enviado. Usuario = $employeId. phoneID = $phoneid\n$responseMsg\n$aq2json", $pathLog); // escribir en el log de errores
                        }
                    } else {
                        fileLog("No existe phone ID en la base de datos. Usuario = $employeId. phoneID = $phoneid\n", $pathLog); // escribir en el log
                    }
                    fileLog("--------------------------------------------------------", $pathLog);
                    $go = false;
                // } while ($go);
            }
        }

        pdoQuery("UPDATE reg_user_ SET regid = '$regid' WHERE id_user = $employeId AND id_company = $companyCode");
        pdoQuery("UPDATE reg_device_ SET appVersion = '$appVersion', regid = '$regid' WHERE phoneid = $phoneid AND id_company = $companyCode");

        $arrayObj[] = array(
            'fechaHora'     => $fechaHora,
            'fechaHoraCH'   => $fechaHoraCH,
            'hora'          => $hora,
            'appVersion'    => $appVersion,
            'attphoto'      => $attphoto,
            'companyCode'   => $companyCode,
            'createdDate'   => $createdDate,
            'dateTime'      => $dateTime,
            'employeId'     => $employeId,
            'eventType'     => $eventType,
            'gpsStatus'     => $gpsStatus,
            'lat'           => $lat,
            'lng'           => $lng,
            'phoneid'       => $phoneid,
            'regid'         => $regid,
            'operationType' => $operationType,
            'operation'     => $operation,
            'checkPhoto'    => $checkPhoto,
            'confidence'    => $confidence,
            'locked'        => $locked,
            'error'         => $error,
            'id_api'        => $id_api
        );
    }
    (array_multisort(array_column($arrayObj, 'createdDate'), SORT_DESC, $arrayObj));
    $first_element = reset($arrayObj);

    $assoc = array(
        'flags' => array(
            'lastDate' => $first_element['createdDate'],
            'fechHora' => $first_element['fechaHora'],
            'download' => 2,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );

    $arrGroup = (_group_by_keys($arrayObj, array('employeId', 'fechaHora', 'phoneid')));

    foreach ($arrGroup as $key => $valor) {

        $fechaHora     = $valor['fechaHora'];
        $fechaHoraCH   = $valor['fechaHoraCH'];
        $hora          = $valor['hora'];
        $appVersion    = addslashes($valor['appVersion']);
        $attphoto      = addslashes($valor['attphoto']);
        $companyCode   = $valor['companyCode'];
        $createdDate   = $valor['createdDate'];
        $dateTime      = $valor['dateTime'];
        $employeId     = $valor['employeId'];
        $eventType     = $valor['eventType'];
        $gpsStatus     = $valor['gpsStatus'];
        $lat           = $valor['lat'];
        $lng           = $valor['lng'];
        $phoneid       = $valor['phoneid'];
        $regid         = addslashes($valor['regid']);
        $operationType = $valor['operationType'];
        $operation     = $valor['operation'];
        $checkPhoto    = $valor['checkPhoto'];
        $confidence    = $valor['confidence'];
        $locked        = empty($valor['locked']) ? '0' : $valor['locked'];
        $error         = addslashes($valor['error']);
        $id_api        = ($valor['id_api']);

        $company[]      = "$companyCode";
        // if (($companyCode == $_SESSION['ID_CLIENTE'])) {
        $totalSession[] = ($companyCode);
        // }
        $employe[]      = "$employeId";

        $eplodeFechaHora = explode(' ', $fechaHora);
        $eplodeFecha = explode('-', $eplodeFechaHora[0]);
        $PathAnio    = $eplodeFecha[0];
        $PathMes     = $eplodeFecha[1];
        $PathDia     = $eplodeFecha[2];
        /** Guardamos la foto del base64 */
        if ($eventType == '2') {
            $filename = "fotos/$companyCode/$PathAnio/$PathMes/$PathDia/index.php";
            $dirname = dirname($filename);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755, true);
                $log    = fopen($dirname . '/index.php', 'a');
                $text   = '<?php exit;';
                fwrite($log, $text);
                fclose($log);
            }
            $f = fopen($dirname . '/' . $createdDate . '_' . $phoneid . '.jpg', "w") or die("Unable to open file!");
            fwrite($f, base64_decode($attphoto));
            fclose($f);
            $rutaImagenOriginal = $dirname . '/' . $createdDate . '_' . $phoneid . '.jpg';
            $imagenOriginal = imagecreatefromjpeg($rutaImagenOriginal); //Abrimos la imagen de origen
            $rutaImagenComprimida = $dirname . '/' . $createdDate . '_' . $phoneid . '.jpg'; //Ruta de la imagen a comprimir
            $calidad = 20; // Valor entre 0 y 100. Mayor calidad, mayor peso
            imagejpeg($imagenOriginal, $rutaImagenComprimida, $calidad); //Guardamos la imagen comprimida
            $contenidoBinario = file_get_contents($rutaImagenComprimida);
            $imagenComoBase64 = base64_encode($contenidoBinario);
        }

        /** Calculamos la Zona */
        $query       = queryCalcZone($lat, $lng, $companyCode);
        $zona        = simple_pdoQuery($query);
        if ($zona) {
            $radio      = round(intval($zona['radio']) / 1000, 2);
            $distancia  = ($zona['distancia']) ? round($zona['distancia'], 2) : 0;
            $distancia2 = ($zona['distancia']) ? ($zona['distancia']) : 0;
            $idZone     = ($distancia <= $radio) ? $zona['id'] : '0';
        } else {
            $idZone = '0';
        }
        /** Fin calculo Zona */
        $eventZone = '0';
        if ($idZone != '0') { // Si la Zona es diferente a 0 entonces se calcula el evento consultando la tabla de zona y evento
            $a = simple_pdoQuery("SELECT evento FROM reg_zones WHERE id_company = '$companyCode' AND evento != '0' AND id = '$idZone' LIMIT 1");
            $eventZone = $a['evento']; // Evento de la zona
        }
        $b = simple_pdoQuery("SELECT evento FROM reg_device_ WHERE id_company = '$companyCode' AND evento != '0' AND phoneid = '$phoneid' LIMIT 1");
        $eventDevice = $b['evento'] ?? ''; // Evento del dispositivo

        $query = "INSERT INTO reg_ (reg_uid, id_user, phoneid, id_company,createdDate,fechaHora,lat,lng, idZone, distance, eventZone, eventDevice, gpsStatus,eventType,operationType, operation, _id,regid,appVersion, attphoto, confidence, locked, error, id_api) VALUES(UUID(),'$employeId', '$phoneid', '$companyCode','$createdDate', '$fechaHora', '$lat','$lng', '$idZone', '$distancia2', '$eventZone', '$eventDevice', '$gpsStatus','$eventType', '$operationType', '$operation','$_id', '$regid', '$appVersion', '$checkPhoto', '$confidence', '$locked', '$error', '$id_api')";

        if ((pdoQuery($query))) { // Si se guarda correctamente insertanmos en la tabla fichadas de control horarios
            // if (!empty($attphoto)) {
            //     $query = "INSERT INTO `reg_faces`(`createdDate`, `id_user`, `id_company`, `photo`) VALUES('$createdDate', '$employeId', '$companyCode', '$imagenComoBase64')";
            //     pdoQuery($query);
            // }

            $Legajo = str_pad($employeId, 11, "0", STR_PAD_LEFT);

            /** Guardo Log de las Fichadas descargadas */
            $iniKeys     = (getDataIni(__DIR__ . '../../../mobileApikey.php'));
            $obj         = filtrarObjeto($iniKeys, 'idCompany', $companyCode);
            $nameCompany = (str_replace(' ', '_', $obj['nameCompany']));
            $recidCompany = $obj['recidCompany'];

            $pathLog = __DIR__ . '/logs/descargas/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
            createDir($pathLog);
            $text = "$Legajo $createdDate $fechaHora $lat $lng api-aws";
            fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log'); // Guardo Log de las Fichadas descargadas
            /**  */

            $localCH = filtrarObjeto($iniKeys, 'idCompany', $companyCode); // Buscamos si la empresa tiene local CH
            $nameCompany = str_replace(" ", "-", $localCH['nameCompany']);

            if ($localCH['localCH'] == '1') {
                $text = "$Legajo $fechaHoraCH $hora";
                // $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_logRegExternalCH_' . $nameCompany . '.log';
                //fileLog($text, $pathLog); // escribir en el log de errores el error
                $pathLog = __DIR__ . '/logs/reg_external_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
                createDir($pathLog);
                fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log'); // Guardo Log de las Fichadas descargadas

            }
            if ($locked == '1') {
                $text = "Usuario Bloqueado $Legajo $fechaHoraCH $hora";
                //$pathLog = __DIR__ . '/logs/' . date('Ymd') . '_logRegLocked_' . $nameCompany . '.log';
                //fileLog($text, $pathLog); // escribir en el log de errores el error

                $pathLog = __DIR__ . '/logs/reg_locked_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
                createDir($pathLog);
                fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log');
            }
            if ($locked != '1' && $localCH['localCH'] == '0') { // Si no esta bloqueado y tiene local CH
                $eventZone = $eventZone ?? '0';
                $query = "INSERT INTO FICHADAS (RegTarj, RegFech, RegHora, RegRelo, RegLect, RegEsta) VALUES ('$employeId', '$fechaHoraCH', '$hora', '9999', '$eventZone', '0')"; // Insertamos en la tabla Fichadas de control horario

                $_GET['_c'] = $recidCompany;

                if (MSQuery($query)) {
                    $eventZone = str_pad($eventZone, 4, "0", STR_PAD_LEFT);
                    $text = "$Legajo $fechaHoraCH $hora 9999 $eventZone 0";
                    //$pathLog = __DIR__ . '/logs/' . date('Ymd') . '_FichadasCH_' . $companyCode . '.log';
                    $insertCH[] = array(
                        'Estado' => '0',
                        'Fecha'  => $fechaHoraCH,
                        'Hora'   => $hora,
                        'Lector' => $eventZone,
                        'Legajo' => $Legajo,
                        'Reloj'  => '9999',
                    );
                    //fileLog($text, $pathLog); // escribir en el log de Fichadas insertadas en control horario

                    $pathLog = __DIR__ . '/logs/insert_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
                    createDir($pathLog);
                    fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log');
                } else {
                    $text = "No se pudo insertar el registro en TABLA FICHADAS CH: $Legajo $fechaHoraCH $hora 9999 9999 0";
                    //$pathLog = __DIR__ . '/logs/' . date('Ymd') . '_ErrorInsertCH.log'; // ruta del archivo de Log de errores
                    //fileLog($text, $pathLog); // escribir en el log de errores el error

                    $pathLog = __DIR__ . '/logs/error_insert_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
                    createDir($pathLog);
                    fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log');

                    $insertCH_Fail[] = array(
                        'Estado' => '0',
                        'Fecha'  => $fechaHoraCH,
                        'Hora'   => $hora,
                        'Lector' => $eventZone,
                        'Legajo' => $Legajo,
                        'Reloj'  => '9999',
                    );
                }
            } else {
                // $text = "$Legajo $fechaHoraCH $hora 9999 9999 0";
                // $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_FichadasCH_' . $companyCode . '.log';
                // fileLog($text, $pathLog); // escribir en el log de Fichadas insertadas en control horario
            }
        }
    }

    // $totalSession = array_count_values($totalSession);
    $end  = microtime(true);
    $time = round($end - $start, 2);
    header("Content-Type: application/json");
    $data = array(
        'Mensaje'      => 'Se actualizaron registros',
        'company'      => array_count_values($company),
        'date'         => date('Y-m-d H:i:s'),
        'employe'      => array_count_values($employe),
        'iCH_Fail'     => ($insertCH_Fail),
        'iCH_OK'       => ($insertCH),
        'time'         => ($time),
        'total'        => count($arrayData),
        // 'data'      => $arrayData,
    );
    $respuesta = json_encode(array($data), JSON_PRETTY_PRINT);
    // sendEmailTask("Se actualizaron registros Mobile", $respuesta);
    fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_aws_task.log');
    statusFlags(1, $pathFlags, $first_element['createdDate']); // marcar bandera de descarga
    fileLogJson($first_element['createdDate'], 'createdDate.json', false); // un json con la fecha de la ultima descarga
    exit;
} else {
    $flags = (getDataIni($pathFlags));
    $flags_lastDate = $flags['flags']['lastDate'];
    statusFlags(1, $pathFlags, $flags_lastDate); // marcar bandera de espera
    $end = microtime(true);
    $time = round($end - $start, 2);
    // header("Content-Type: application/json");
    $data = array(
        'Mensaje' => 'No hay registros nuevos',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'no',
        'time'    => ($time),
        'total'   => count($arrayData),
    );
    // $respuesta = 'No hay registros nuevos';
    // fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_aws_task.log');
    exit;
}
