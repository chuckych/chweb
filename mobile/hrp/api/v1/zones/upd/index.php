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
borrarLogs(__DIR__ . '../../../_logs/updZones/', 30, '.log');
$total = 0;
$params = $_POST;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}

$startScript = microtime(true);
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
function zoneLat()
{
    $p = $_POST;
    $p['zoneLat'] = $p['zoneLat'] ?? '';
    $zoneLat  = empty($p['zoneLat']) ? '' : test_input($p['zoneLat']);
    return floatval($zoneLat);
}
function zoneLng()
{
    $p = $_POST;
    $p['zoneLng'] = $p['zoneLng'] ?? '';
    $zoneLng  = empty($p['zoneLng']) ? '' : test_input($p['zoneLng']);
    return floatval($zoneLng);
}
function idZone()
{
    $p = $_POST;
    $p['idZone'] = $p['idZone'] ?? '';
    $idZone  = empty($p['idZone']) ? '' : test_input($p['idZone']);
    return intval($idZone);
}
function zoneRadio()
{
    $p = $_POST;
    $p['zoneRadio'] = $p['zoneRadio'] ?? '';
    $zoneRadio  = empty($p['zoneRadio']) ? '' : test_input($p['zoneRadio']);
    return intval($zoneRadio);
}
function zoneName()
{
    $p = $_POST;
    $p['zoneName'] = $p['zoneName'] ?? '';
    $zoneName = empty($p['zoneName']) ? '' : test_input($p['zoneName']);
    return urldecode($zoneName);
}
function zoneEvent()
{
    $p = $_REQUEST;
    $p['zoneEvent'] = $p['zoneEvent'] ?? 0;
    $zoneEvent  = empty($p['zoneEvent']) ? 0 : $p['zoneEvent'];
    return intval($zoneEvent);
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
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'zoneLat' || $key == 'zoneLng' || $key == 'zoneRadio' || $key == 'zoneName' || $key == 'idZone' || $key == 'zoneEvent') {  
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
    $idCompany = $idCompany;

    if ($agent) {
        require_once __DIR__ . '../../../../../../../control/PhpUserAgent/src/UserAgentParser.php';
        $parsedagent[] = parse_user_agent($agent);
        foreach ($parsedagent as $key => $value) {
            $platform = $value['platform'];
            $browser  = $value['browser'];
            $version  = $value['version'];
        }
        $agent = $platform . ' ' . $browser . ' ' . $version;
    }
    $pathLog  = __DIR__ . '../../../_logs/updZones/' . date('Ymd') . '_log_updZones_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
    /** start text log*/
    $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
    /** end text log*/
    fileLog($TextLog, $pathLog); // Log Api
    /** END LOG API CONFIG */
    exit;
}

$queryRecords = array();
$start     = start();
$length    = length();
$zoneName  = (zoneName());
$zoneLat   = (zoneLat());
$zoneLng   = (zoneLng());
$zoneRadio = (zoneRadio());
$idZone    = (idZone());
$zoneEvent = (zoneEvent());

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
        $idCompany    = 0;
        $vkey         = '';
        $nameCompany  = '';
        $urlAppMobile = '';
        continue;
    }
}

if (!$vkey) {
    http_response_code(400);
    (response(array(), 0, 'Invalid Key', 400, 0, 0, $idCompany));
}

if (empty($idZone)) {
    http_response_code(400);
    (response(array(), 0, 'idZone required', 400, 0, 0, $idCompany));
}
if (strlen($idZone) > 11) {
    http_response_code(400);
    (response(array(), 0, 'idZone max length 11', 400, 0, 0, $idCompany));
}
if (empty($zoneLat)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLat required', 400, 0, 0, $idCompany));
}
if (empty($zoneLng)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLng required', 400, 0, 0, $idCompany));
}
if (empty($zoneRadio)) {
    http_response_code(400);
    (response(array(), 0, 'zoneRadio required', 400, 0, 0, $idCompany));
}
if (!is_int($zoneRadio)) {
    http_response_code(400);
    (response(array(), 0, 'zoneRadio invalid format', 400, 0, 0, $idCompany));
}
if (!is_int($zoneEvent)) {
    http_response_code(400);
    (response(array(), 0, 'zoneEvent invalid format', 400, 0, 0, $idCompany));
}
if (strlen($zoneEvent) > 4) {
    http_response_code(400);
    (response(array(), 0, 'zoneEvent max length 4', 400, 0, 0, $idCompany));
}
if (!is_float($zoneLat)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLat invalid format', 400, 0, 0, $idCompany));
}
if (!is_float($zoneLng)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLng invalid format', 400, 0, 0, $idCompany));
}
if (empty($zoneName)) {
    http_response_code(400);
    (response(array(), 0, 'zoneName required', 400, 0, 0, $idCompany));
}
if (strlen($zoneName) > 50) {
    http_response_code(400);
    (response(array(), 0, 'zoneName max length 50', 400, 0, 0, $idCompany));
}
if (strlen($zoneRadio) > 4) {
    http_response_code(400);
    (response(array(), 0, 'zoneRadio max length 4', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();
/** chequeamos unique nombre */
$qUniqueName = "SELECT * FROM `reg_zones` WHERE `id_company` = '$idCompany' AND `nombre` = '$zoneName' AND `id` != '$idZone' LIMIT 1";
$a = count_pdoQuery($qUniqueName);

if ($a > 0) {
    $arrayData  = array();
    $MESSAGE    = 'zoneName already exists';
    $endScript  = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    $countData  = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, '', $timeScript, $countData, $idCompany));
    exit;
}
 /** cheqeuamos unique zone lat y lng */
$qPositionZone = "SELECT * FROM `reg_zones` WHERE `id_company` = '$idCompany' AND `lat` = '$zoneLat' AND `lng` = '$zoneLng' AND `id` != '$idZone' LIMIT 1";
$a = count_pdoQuery($qPositionZone);

if ($a > 0) {
    $arrayData  = array();
    $MESSAGE    = 'Position already exists';
    $endScript  = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    $countData  = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, '', $timeScript, $countData, $idCompany));
    exit;
}

// update query 
$sql_query = "UPDATE `reg_zones` SET `nombre` = '$zoneName', `lat` = '$zoneLat', `lng` = '$zoneLng', `radio` = '$zoneRadio' , `evento` = '$zoneEvent' WHERE `id_company` = '$idCompany' AND `id` = '$idZone' ";

$update = pdoQuery($sql_query);
if ($update) {
    $a = simple_pdoQuery("SELECT * FROM `reg_zones` WHERE `id` = '$idZone' LIMIT 1");
    $MESSAGE = 'OK';
    $text = "Modificacion Zona \"$a[nombre]\" ID = $a[id] Radio = $a[radio] Lat = $a[lat] Lng = $a[lng] Evento = $a[evento]";
    $arrayData = array(
        'id_company' => $a['id_company'],
        'zoneID'     => $a['id'],
        'zoneLat'    => $a['lat'],
        'zoneLng'    => $a['lng'],
        'zoneName'   => $a['nombre'],
        'zoneRadio'  => $a['radio'],
        'zoneEvent'  => $a['evento'],
        'textAud'    => $text,
    );
    fileLog($text, __DIR__ . '../../../_logs/updZones/' . date('Ymd') . '_log_updZones_' . padLeft($idCompany, 3, 0) . '.log'); // 
} else {
    $MESSAGE = 'ERROR';
}
$endScript    = microtime(true);
$timeScript = round($endScript - $startScript, 2);
$countData    = count($arrayData);
(response($arrayData, 1, $MESSAGE, '', $timeScript, 1, $idCompany));
exit;
