<?php
header("Content-Type: application/json");
function tz($tz = 'America/Argentina/Buenos_Aires')
{
    return date_default_timezone_set($tz);
}
function dateTimeNow()
{
    tz();
    // $t = date("Y-m-d H:i:s");
    $t = explode(" ", microtime());
    $t = date("Y-m-d H:i:s", $t[1]) . substr((string)$t[0], 1, 4);
    return $t;
}
function writeLog($text, $path, $type = false)
{
    $date   = dateTimeNow();
    $text   = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
    file_put_contents($path, $text, FILE_APPEND | LOCK_EX);
}
function requestApi($url)
{
    // ignore_user_abort(true);
    // set_time_limit(0);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $curl_errno    = curl_errno($ch); // get error code
    $curl_error    = curl_error($ch); // get error information
    $pathLog = __DIR__ .  date('Ymd') . '_prueba--old_errorCurl.log'; // ruta del archivo de Log de errores
    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        writeLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
}

$_SERVER['HTTP_TOKEN'] = $_SERVER['HTTP_TOKEN'] ?? '';
$array = array(
    'RESPONSE_CODE' => '',
    'START'         => 0,
    'LENGTH'        => 0,
    'TOTAL'         => 0,
    'COUNT'         => 0,
    'TIME'          => '',
    'MESSAGE'       => '',
    'RESPONSE_DATA' => '',
);
if ($_SERVER['HTTP_TOKEN'] == "1ec558a60b5dda24597816c924776716018caf8b") {
    requestApi('https://localhost/chweb/mobile/hrp/prueba--old.php');
    http_response_code(200);
    $array['RESPONSE_CODE'] = 200;
    $array['MESSAGE'] = 'ok';

} else {
    http_response_code(400);
    $array['RESPONSE_CODE'] = 400;
    $array['MESSAGE'] = 'Invalid token';
}
echo json_encode($array, JSON_PRETTY_PRINT);
