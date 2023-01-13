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
borrarLogs(__DIR__ . '../../_logs/getZones/', 30, '.log');

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
function zoneID()
{
    $p = $_REQUEST;
    $p['zoneID'] = $p['zoneID'] ?? '0';
    $zoneID  = empty($p['zoneID']) ? 0 : $p['zoneID'];
    return intval($zoneID);
}
function zoneName()
{
    $p = $_REQUEST;
    $p['zoneName'] = $p['zoneName'] ?? '';
    $zoneName = empty($p['zoneName']) ? '' : $p['zoneName'];
    return urldecode($zoneName);
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
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'zoneID' || $key == 'zoneName') {
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

    $pathLog  = __DIR__ . '../../_logs/getZones/' . date('Ymd') . '_log_getZones_'.padLeft($idCompany, 3, 0).'.log'; // path Log Api
    /** start text log*/
    $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
    /** end text log*/
    fileLog($TextLog, $pathLog); // Log Api
    /** END LOG API CONFIG */
    exit;
}
$queryRecords = array();
$start    = start();
$length   = length();
$zoneName = zoneName();
$zoneID   = zoneID();

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

$MESSAGE = 'OK';
$arrayData = array();

$sql_query = "SELECT *, (SELECT COUNT(1) FROM reg_ r WHERE r.idZone = rz.id AND r.id_company = '$idCompany') AS 'totalZones'  FROM `reg_zones` `rz` WHERE `rz`.`id` > 0";
$filtro_query = '';
$filtro_query .= ($idCompany) ? " AND `rz`.`id_company` = '$idCompany'" : '';
$filtro_query .= (!empty($zoneID)) ? " AND `rz`.`id` = '$zoneID'" : '';
$filtro_query .= (!empty($zoneName)) ? " AND `rz`.`nombre` LIKE '%$zoneName%'" : '';
$sql_query .= $filtro_query;
$sql_query .= " ORDER BY `rz`.`nombre` ASC";
$sql_query .= " LIMIT $start, $length";

// print_r($sql_query);exit;

$queryRecords = array_pdoQuery($sql_query);
if (($queryRecords)) {
    foreach ($queryRecords as $r) {
        // $Fecha = FechaFormatVar($r['fechaHora'], 'Y-m-d');
        $arrayData[] = array(
            'zoneID'     => intval($r['id']),
            'zoneName'   => $r['nombre'],
            'zoneRadio'  => $r['radio'],
            'zoneLat'    => $r['lat'],
            'zoneLng'    => $r['lng'],
            'lastUpdate' => ($r['fechahora']),
            'idCompany'  => $r['id_company'],
            'totalZones' => ($r['totalZones']),
            'zoneEvent' => ($r['evento']),
        );
    }
    $q = "SELECT COUNT(*) AS 'count' FROM `reg_zones` `rz` WHERE `rz`.`id` > 0";
    $q .= $filtro_query;
    $total = simple_pdoQuery($q)['count'];
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($total), 'OK', '', $tiempoScript, $countData, $idCompany));
exit;
