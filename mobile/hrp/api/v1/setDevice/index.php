<?php
require __DIR__ . '../../../../../../config/index.php';
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();

$iniKeys = (getDataIni(__DIR__ . '../../../../../../mobileApikey.php'));
borrarLogs(__DIR__ . '../../_logs/sendSet/', 30, '.log');
$total = 0;
$params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}

$startScript = microtime(true);
$idCompany = 0;

function start()
{
    $p = $_REQUEST;
    $p['start'] = $p['start'] ?? '0';
    $start  = empty($p['start']) ? 0 : $p['start'];
    return intval($start);
}
function length()
{
    $p = $_REQUEST;
    $p['length'] = $p['length'] ?? '';
    $length = empty($p['length']) ? 10 : $p['length'];
    return intval($length);
}
function rememberUser()
{
    $p = $_REQUEST;
    $p['rememberUser'] = $p['rememberUser'] ?? '';
    $rememberUser = empty($p['rememberUser']) ? '' : $p['rememberUser'];
    return intval($rememberUser);
}
function initialize()
{
    $p = $_REQUEST;
    $p['initialize'] = $p['initialize'] ?? '';
    $initialize = empty($p['initialize']) ? '' : $p['initialize'];
    return intval($initialize);
}
function TMEF()
{
    $p = $_REQUEST;
    $p['TMEF'] = $p['TMEF'] ?? '';
    $TMEF = empty($p['TMEF']) ? '' : $p['TMEF'];
    return intval($TMEF);
}
function userID()
{
    $p = $_REQUEST;
    $p['userID'] = $p['userID'] ?? '';
    $userID = empty($p['userID']) ? '' : $p['userID'];
    return intval($userID);
}
function regid()
{
    $p = $_REQUEST;
    $p['regid'] = $p['regid'] ?? '';
    $regid = empty($p['regid']) ? '' : $p['regid'];
    return urldecode($regid);
}
function phoneID()
{
    $p = $_REQUEST;
    $p['phoneID'] = $p['phoneID'] ?? '';
    $phoneID = empty($p['phoneID']) ? '' : $p['phoneID'];
    return ($phoneID);
}
function validaKey()
{
    $p = $_REQUEST['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}
if (!isset($params['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0, 0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    if ($key=='key' || $key=='rememberUser' || $key=='TMEF' || $key=='userID' || $key=='regid' || $key=='initialize') {
        continue;
    } else {
        (response(array(), 0, 'Parameter error', 400, 0, 0, $idCompany));
        exit;
    }
}

function response($data, $total, $msg = 'OK', $code = 200, $timeScript = 0, $count = 0, $idCompany)
{
    $start  = ($code != '400') ? start() : 0;
    $length  = ($code != '400') ? length() : 0;
    $array = array(
        'RESPONSE_CODE' => http_response_code(intval($code)),
        'START'         => intval($start),
        'LENGTH'        => intval($length),
        'TOTAL'         => intval($total),
        'COUNT'         => intval($count),
        'MESSAGE'       => $msg,
        'TIME'          => $timeScript,
        'RESPONSE_DATA' => $data,
    );

    echo json_encode($array, JSON_PRETTY_PRINT);

    /** LOG API CONFIG */
    $textParams = array();
    foreach ($_REQUEST as $key => $value) {
        $arrRequest = "$key=$value";
        array_push($textParams, $arrRequest);
    }

    $textParams = implode('&', $textParams); // convert to string

    $ipAdress = $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $agent    = $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    // $idCompany = $idCompany;

    if ($agent) {
        require_once __DIR__ . '../../../../../../control/PhpUserAgent/src/UserAgentParser.php';
        $parsedagent[] = parse_user_agent($agent);
        foreach ($parsedagent as $key => $value) {
            $platform = $value['platform'];
            $browser  = $value['browser'];
            $version  = $value['version'];
        }
        $agent = $platform . ' ' . $browser . ' ' . $version;
    }
    $pathLog  = __DIR__ . '../../_logs/sendSet/' . date('Ymd') . '_setConf_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
    /** start text log*/
    $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
    /** end text log*/
    fileLog($TextLog, $pathLog); // Log Api
    /** END LOG API CONFIG */
    exit;
}
$queryRecords = array();
$start        = start();
$length       = length();
$rememberUser = rememberUser();
$initialize   = initialize();
$TMEF         = TMEF();
$userID       = userID();
$regid        = regid();
$phoneID      = phoneID();

$validaKey = validaKey();
$vkey = '';
foreach ($iniKeys as $key => $value) {
    if ($value['recidCompany'] == $validaKey) {
        $idCompany    = $value['idCompany'];
        $vkey         = $value['recidCompany'];
        $nameCompany  = $value['nameCompany'];
        $urlAppMobile = $value['urlAppMobile'];
        break;
    } else {
        $idCompany = 0;
        $vkey      = '';
        $nameCompany = '';
        $urlAppMobile = '';
        continue;
    }
}
if (!$vkey) {
    http_response_code(400);
    (response(array(), 0, 'Invalid Key', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

if (empty($regid)) {
    http_response_code(400);
    (response(array(), 0, 'phoneRegID required', 400, 0, 0, $idCompany));
}
if (strlen($userID)> 11 ) {
    http_response_code(400);
    (response(array(), 0, 'userID max length 11', 400, 0, 0, $idCompany));
}
// $mensaje[] = $message;

$cancellationReasons[] = '';
$operations[] = '';
$dataSend = array(
    'alwaysSavePosition'  => false,
    'notificationId'      => 195,
    'apiKey'              => '7BB3A26C25687BCD56A9BAF353A78',
    'locationIp'          => "http://190.7.56.83",
    'serverIp'            => $urlAppMobile,
    'companyCode'         => ($initialize === 1) ? '-1' : $idCompany,
    "recid"               => $vkey,
    'employeId'           => $userID,
    'updateInterval'      => 90,
    'fastestInterval'     => 60,
    'eventType'           => 0,
    'cancellationReasons' => [],
    'operations'          => [],
    'tmef'                => $TMEF,
    'rememberEmployeId'   => ($rememberUser) ? true : false,
);

$data2 = array(
    'data' => array('data' => $dataSend),
    'to' => $regid
);

$payload = json_encode($data2);

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
}


$url = 'https://fcm.googleapis.com/fcm/send';
$sendMensaje = sendMessaje($url, $payload, 10);

// print_r($sendMensaje).exit;

if (json_decode($sendMensaje)->success === 1) {
    // $data = array('status' => 'ok', 'Mensaje' => 'Mensaje enviado correctamente', 'respuesta' => json_decode($sendMensaje), 'payload' => json_decode($payload));
    http_response_code(200);
    $MESSAGE = 'Config sent successfully';
    $endScript    = microtime(true);
    $timeScript   = round($endScript - $startScript, 2);
    $countData    = count($arrayData);
    (response(json_decode($sendMensaje), 1, $MESSAGE, 200, $timeScript, 1, $idCompany));
    exit;
} else {
    // $data = array('status' => 'error', 'Mensaje' => 'The message could not be sent', 'respuesta' => json_decode($sendMensaje), 'payload' => json_decode($payload));
    http_response_code(400);
    $MESSAGE = 'The config could not be sent';
    $endScript    = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    (response( json_decode($sendMensaje)->results[0]->error, 0, $MESSAGE, 400, $timeScript, 0, $idCompany ));
    exit;
}
