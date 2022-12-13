<?php
require __DIR__ . '../../../../../../config/index.php';
// require __DIR__ . '../../../../../vendor/autoload.php';
// use Carbon\Carbon;
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();
$iniKeys = (getDataIni(__DIR__ . '../../../../../../mobileApikey.php'));
borrarLogs(__DIR__ . '../../_logs/getChecks/', 30, '.log');

$total = 0;
$params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}

$iniScript = microtime(true);
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
function deviceID()
{
    $p = $_REQUEST;
    $p['deviceID'] = $p['deviceID'] ?? '0';
    $deviceID  = empty($p['deviceID']) ? 0 : $p['deviceID'];
    return intval($deviceID);
}
function deviceEvent()
{
    $p = $_REQUEST;
    $p['deviceEvent'] = $p['deviceEvent'] ?? 0;
    $deviceEvent  = empty($p['deviceEvent']) ? 0 : $p['deviceEvent'];
    return intval($deviceEvent);
}
function deviceName()
{
    $p = $_REQUEST;
    $p['deviceName'] = $p['deviceName'] ?? '';
    $deviceName = empty($p['deviceName']) ? '' : $p['deviceName'];
    return urldecode($deviceName);
}
function validaKey()
{
    $p = $_REQUEST['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}
if (!isset($params['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0,0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'deviceID' || $key == 'deviceName' || $key == 'deviceEvent') {
        continue;
    } else {
        (response(array(), 0, 'Parameter error', 400, 0,0, $idCompany));
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
        require_once __DIR__ . '../../../../../../control/PhpUserAgent/src/UserAgentParser.php';
        $parsedagent[] = parse_user_agent($agent);
        foreach ($parsedagent as $key => $value) {
            $platform = $value['platform'];
            $browser  = $value['browser'];
            $version  = $value['version'];
        }
        $agent = $platform . ' ' . $browser . ' ' . $version;
    }

    $pathLog  = __DIR__ . '../../_logs/getDevice/' . date('Ymd') . '_log_getDevice_'.padLeft($idCompany, 3, 0).'.log'; // path Log Api
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
$deviceName   = deviceName();
$deviceID     = deviceID();
$deviceEvent  = deviceEvent();

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

// echo json_encode($iniKeys, JSON_PRETTY_PRINT);exit;

if (!$vkey) {
    http_response_code(400);
    (response(array(), 0, 'Invalid Key', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

$sql_query = "SELECT *, (SELECT COUNT(1) FROM reg_ r WHERE r.phoneid = rd.phoneid AND r.id_company = '$idCompany') AS 'totalChecks'  FROM `reg_device_` `rd` WHERE `rd`.`id` > 0";
$filtro_query = '';
$filtro_query .= ($idCompany) ? " AND `rd`.`id_company` = '$idCompany'" : '';
$filtro_query .= (!empty($deviceID)) ? " AND `rd`.`id` = '$deviceID'" : '';
$filtro_query .= (!empty($deviceEvent)) ? " AND `rd`.`evento` = '$deviceEvent'" : '';
$filtro_query .= (!empty($deviceName)) ? " AND `rd`.`nombre` LIKE '%$deviceName%'" : '';
$sql_query .= $filtro_query;
$sql_query .= " ORDER BY `rd`.`nombre` ASC";
$sql_query .= " LIMIT $start, $length";

// print_r($sql_query);exit;

$queryRecords = array_pdoQuery($sql_query);
if (($queryRecords)) {
    foreach ($queryRecords as $r) {
        // $Fecha = FechaFormatVar($r['fechaHora'], 'Y-m-d');
        $arrayData[] = array(
            'deviceEvent' => $r['evento'],
            'deviceID'    => intval($r['id']),
            'deviceName'  => $r['nombre'],
            'idCompany'   => $r['id_company'],
            'lastUpdate'  => ($r['fechahora']),
            'phoneID'     => $r['phoneid'],
            'totalChecks' => ($r['totalChecks']),
            'appVersion'  => ($r['appVersion']),
            'regid'       => ($r['regid']),
        );
    }
    $q = "SELECT COUNT(*) AS 'count' FROM `reg_device_` `rd` WHERE `rd`.`id` > 0";
    $q .= $filtro_query;
    $total = simple_pdoQuery($q)['count'];
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($total), 'OK', '', $tiempoScript, $countData, $idCompany));
exit;
