<?php
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', '0');
$_SERVER["argv"][1] = $_SERVER["argv"][1] ?? '';

if ($_SERVER["argv"][1] != "1ec558a60b5dda24597816c924776716018caf8b") {
    $data = array(
        'Mensaje' => 'Parametro no valido',
        'date' => date('Y-m-d H:i:s'),
        'status' => 'error',
    );
    file_put_contents($GeneralLogsPath, json_encode($data)."\n", FILE_APPEND );
    exit;
}
require __DIR__ . '../../../vendor/autoload.php';

function sendEmailTask($subjet, $body)
{
    $url = 'https://ht-api.helpticket.com.ar/sendMail/';

    $data = array(
        "subjet" => $subjet,
        "to" => "task-aws-mobile",
        "replyTo" => "task-aws-mobile",
        "body" => $body
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
function timeZone()
{
    return date_default_timezone_set('America/Argentina/Buenos_Aires');
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
        sendEmailTask("Error al conectar con AWS " . date('Y-m-d H:i:s'), $text);
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
timeZone();
$array = array();
$GeneralLogsPath = __DIR__ . '/logs/' . date('Ymd') . '_deleted_aws_fotos.log';
$last_aws_photo = __DIR__ . '/api/v1/faces/last_aws_photo.txt';
$start = microtime(true);
$url = "http://awsapi.chweb.ar:7575/attention/api/test/attphotos/delete?days=30";
$array = json_decode(getEvents($url), true);

$array['type'] = $array['type'] ?? '';
$array['message'] = $array['message'] ?? '';

if($array['type'] == "success") {
    $end = microtime(true);
    $time = round($end - $start, 2);
    $lastPunchEvent = explode(':', $array['message']); // Last punchevent
    file_put_contents($last_aws_photo, intval($lastPunchEvent[1])); // escribimos el Last punchevent en el archivo /logs/api/v1/last_aws_photo.txt
    $fechaHora = date('Y-m-d H:i:s');
    sendEmailTask("Deleted Fotos: $array[type]", "<pre>$array[message]<pre>");
    file_put_contents($GeneralLogsPath, "$fechaHora $array[message] Dur: $time\n", FILE_APPEND );
}