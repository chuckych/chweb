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
borrarLogs(__DIR__ . '../../_logs/nearZones/', 30, '.log');

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
function zoneLat()
{
    $p = $_GET;
    $p['zoneLat'] = $p['zoneLat'] ?? '';
    $zoneLat  = empty($p['zoneLat']) ? '' : test_input($p['zoneLat']);
    return floatval($zoneLat);
}
function zoneLng()
{
    $p = $_GET;
    $p['zoneLng'] = $p['zoneLng'] ?? '';
    $zoneLng  = empty($p['zoneLng']) ? '' : test_input($p['zoneLng']);
    return floatval($zoneLng);
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
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'zoneLat' || $key == 'zoneLng' || $key == 'zoneName') {
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
        require_once __DIR__ . '../../../../../../../control/PhpUserAgent/src/UserAgentParser.php';
        $parsedagent[] = parse_user_agent($agent);
        foreach ($parsedagent as $key => $value) {
            $platform = $value['platform'];
            $browser  = $value['browser'];
            $version  = $value['version'];
        }
        $agent = $platform . ' ' . $browser . ' ' . $version;
    }

    $pathLog  = __DIR__ . '../../../_logs/nearZones/' . date('Ymd') . '_log_nearZones_'.padLeft($idCompany, 3, 0).'.log'; // path Log Api
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
$zoneLat   = zoneLat();
$zoneLng   = zoneLng();

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
if (empty($zoneLat)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLat required', 400, 0, 0, $idCompany));
}
if (empty($zoneLng)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLng required', 400, 0, 0, $idCompany));
}
if (!is_float($zoneLat)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLat invalid format', 400, 0, 0, $idCompany));
}
if (!is_float($zoneLng)) {
    http_response_code(400);
    (response(array(), 0, 'zoneLng invalid format', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

function queryCalcZone($lat, $lng, $idCompany){
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
    ";
    return $query;
}
$sql_query = queryCalcZone($zoneLat, $zoneLng, $idCompany);
$filtro_query = '';
// $filtro_query .= ($idCompany) ? " AND `rg`.`id_company` = '$idCompany'" : '';
$sql_query .= $filtro_query;
$sql_query .= " ORDER BY distancia ASC, rg.id";
$sql_query .= " LIMIT $start, $length";

// print_r($sql_query);exit;

$queryRecords = array_pdoQuery($sql_query);
// print_r($queryRecords);exit;

if (($queryRecords)) {
    foreach ($queryRecords as $r) {
        // $Fecha = FechaFormatVar($r['fechaHora'], 'Y-m-d');
        $arrayData[] = array(
            'zoneID'       => intval($r['id']),
            'zoneName'     => trim($r['nombre']),
            'zoneRadio'    => $r['radio'],
            'zoneLat'      => $r['lat'],
            'zoneLng'      => $r['lng'],
            'idCompany'    => $r['id_company'],
            'zoneDistance' => ($r['distancia']),
        );
    }
    $q = "SELECT COUNT(*) AS 'count' FROM `reg_zones` `rz` WHERE `rz`.`id` > 0 AND `rz`.`id_company` = $idCompany";
    $q .= $filtro_query;
    $total = simple_pdoQuery($q)['count'];
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($total), 'OK', '', $tiempoScript, $countData, $idCompany));
exit;