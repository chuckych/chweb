<?php
require __DIR__ . '../../../../../../../config/index.php';
// require __DIR__ . '../../../../../vendor/autoload.php';
// use Carbon\Carbon;
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();

$iniKeys = (getDataIni(__DIR__ . '../../../../../../../mobileApikey.php'));

$total = 0;
$params = $_POST;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}

$iniScript = microtime(true);
$idCompany = 0;

function start()
{
    $p = $_POST;
    $p['start'] = $p['start'] ?? '0';
    $start  = empty($p['start']) ? 0 : $p['start'];
    return intval($start);
}
function length()
{
    $p = $_POST;
    $p['length'] = $p['length'] ?? '';
    $length = empty($p['length']) ? 0 : $p['length'];
    return intval($length);
}
function devicePhoneID()
{
    $p = $_POST;
    $p['devicePhoneID'] = $p['devicePhoneID'] ?? 0;
    $devicePhoneID  = empty($p['devicePhoneID']) ? 0 : $p['devicePhoneID'];
    return intval($devicePhoneID);
}
function deviceEvent()
{
    $p = $_POST;
    $p['deviceEvent'] = $p['deviceEvent'] ?? '0';
    $deviceEvent  = empty($p['deviceEvent']) ? 0 : $p['deviceEvent'];
    return intval($deviceEvent);
}
function deviceName()
{
    $p = $_POST;
    $p['deviceName'] = $p['deviceName'] ?? '';
    $deviceName = empty($p['deviceName']) ? '' : $p['deviceName'];
    return urldecode($deviceName);
}
function validaKey()
{
    $p = $_POST['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}
if (!isset($_POST['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0, 0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'deviceName' || $key == 'deviceEvent' || $key == 'devicePhoneID') {
        continue;
    } else {
        (response(array(), 0, 'Parameter error', 400, 0, 0, $idCompany));
        exit;
    }
}

function response($data, $total, $msg = 'OK', $code = 200, $tiempoScript = 0, $count = 0, $idCompany)
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
        'TIME'          => $tiempoScript,
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
    $idCompany = $idCompany;

    if ($agent) {
        require_once __DIR__ . '../../../../../../../../control/PhpUserAgent/src/UserAgentParser.php';
        $parsedagent[] = parse_user_agent($agent);
        foreach ($parsedagent as $key => $value) {
            $platform = $value['platform'];
            $browser  = $value['browser'];
            $version  = $value['version'];
        }
        $agent = $platform . ' ' . $browser . ' ' . $version;
    }
    $pathLog  = __DIR__ . '../../../logs/delDevice/' . date('Ymd') . '_log_delDevice_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
    /** start text log*/
    $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
    /** end text log*/
    fileLog($TextLog, $pathLog); // Log Api
    /** END LOG API CONFIG */
    exit;
}

$queryRecords  = array();
$start         = start();
$length        = length();
$deviceName    = deviceName();
$devicePhoneID = devicePhoneID();
$deviceEvent   = deviceEvent();

$validaKey = validaKey();
$vkey = '';

foreach ($iniKeys as $key => $value) {
    if ($value['recidCompany'] == $validaKey) {
        $idCompany = $value['idCompany'];
        $vkey      = $value['recidCompany'];
        break;
    } else {
        $idCompany = 0;
        $vkey      = '';
        continue;
    }
}
if (!$vkey) {
    http_response_code(400);
    (response(array(), 0, 'Invalid Key', 400, 0, 0, $idCompany));
}
if (empty($devicePhoneID)) {
    http_response_code(400);
    (response(array(), 0, 'devicePhoneID es requerido', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

$a = simple_pdoQuery("SELECT * FROM `reg_device_` WHERE `phoneid` = '$devicePhoneID' AND `id_company` = '$idCompany' LIMIT 1");
$MESSAGE = 'OK';

if (!$a) {
    $arrayData = array();
    $MESSAGE = 'El dispositivo no existe';
    $finScript    = microtime(true);
    $tiempoScript = round($finScript - $iniScript, 2);
    $countData    = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, '', $tiempoScript, $countData, $idCompany));
    exit;
} else {
    $arrayData = array(
        'deviceID'      => $a['id'],
        'devicePhoneID' => $a['phoneid'],
        'id_company'    => $a['id_company'],
        'deviceName'    => $a['nombre'],
        'deviceEvent'   => $a['evento'],
    );
}

// delet query 
$sql_query_delete = "DELETE FROM `reg_device_` WHERE `id_company` = '$idCompany' AND `phoneid` = '$devicePhoneID'";

$delete = pdoQuery($sql_query_delete);
if ($delete) {
    $text = "Eliminacion Dispositivo \"$arrayData[deviceName]\" ID = $arrayData[deviceID] Evento = $arrayData[deviceEvent] PhoneID = $arrayData[devicePhoneID]";
    fileLog($text, __DIR__ . '../../../logs/delDevice/' . date('Ymd') . '_log_delDevice_' . padLeft($idCompany, 3, 0) . '.log'); // _log_addDevice_
} else {
    $MESSAGE = 'ERROR';
    $finScript    = microtime(true);
    $tiempoScript = round($finScript - $iniScript, 2);
    $countData    = count($arrayData);
    (response($arrayData, 0, $MESSAGE, '', $tiempoScript, 0, $idCompany));
}
$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($countData), $MESSAGE, '', $tiempoScript, $countData, $idCompany));
exit;
