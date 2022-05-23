<?php
require __DIR__ . '../../../../../../config/index.php';
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();
borrarLogs(__DIR__ . '../../_logs/infoChecks/', 30, '.log');

$iniKeys = (getDataIni(__DIR__ . '../../../../../../mobileApikey.php'));
// echo json_encode($iniKeys);
// exit;
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}
$total = 0;
// $params = $_REQUEST;
$params = ($_REQUEST);
$params['checks'] = $params['checks'] ?? '';

$iniScript = microtime(true);

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
function startDate()
{
    $p = $_REQUEST;
    $p['startDate'] = $p['startDate'] ?? date('Ymd');
    $startDate  = empty($p['startDate']) ? date('Ymd') : intval($p['startDate']);
    return (FechaFormatVar($startDate, 'Y-m-d 00:00:00'));
}
function endDate()
{
    $p = $_REQUEST;
    $p['endDate'] = $p['endDate'] ?? date('Ymd');
    $endDate  = empty($p['endDate']) ? date('Ymd') : intval($p['endDate']);
    return (FechaFormatVar($endDate, 'Y-m-d 23:59:59'));
}
function userID()
{
    $p = $_REQUEST;
    $p['userID'] = $p['userID'] ?? '';
    $userID = empty($p['userID']) ? '' : $p['userID'];
    return intval($userID);
}
function userName()
{
    $p = $_REQUEST;
    $p['userName'] = $p['userName'] ?? '';
    $userName = empty($p['userName']) ? '' : $p['userName'];
    return urldecode($userName);
}
function userIDName()
{
    $p = $_REQUEST;
    $p['userIDName'] = $p['userIDName'] ?? '';
    $userIDName = empty($p['userIDName']) ? '' : $p['userIDName'];
    return urldecode($userIDName);
}
function validaKey()
{
    $p = $_REQUEST['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}
function validUser()
{
    $p = $_REQUEST;
    $p['validUser'] = $p['validUser'] ?? '';
    $validUser  = empty($p['validUser']) ? '' : $p['validUser'];
    return intval($validUser);
}

$idCompany = 0;

if (!isset($params['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0, 0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    $key = urldecode($key);
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'checks' || $key == 'startDate' || $key == 'endDate' || $key == 'userID' || $key == 'userName' || $key == 'userIDName' || $key == 'validUser') {
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
        'START'         => intval(0),
        'LENGTH'        => intval(0),
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
    $idCompany    = $idCompany;

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

    $pathLog  = __DIR__ . '../../_logs/infoChecks/' . date('Ymd') . '_infoChecks_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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
$userID       = userID();
$userName     = userName();
$userIDName   = userIDName();
$FechaIni     = startDate();
$FechaFin     = endDate();
$validUser   = validUser();
// checkEmpty($createdDate, 'createdDate');

$validaKey = validaKey();
$vkey = '';
foreach ($iniKeys as $key => $value) {
    if ($value['recidCompany'] == $validaKey) {
        $idCompany    = $value['idCompany'];
        $vkey         = $value['recidCompany'];
        $nameCompany  = $value['nameCompany'];
        $urlAppMobile = $value['urlAppMobile'];
        break;
    }
}
if (!$vkey) {
    http_response_code(400);
    (response(array(), 0, 'Invalid Key', 400, 0, 0, $idCompany));
}

if ($FechaIni > $FechaFin) {
    http_response_code(400);
    (response(array(), 0, 'The start date is greater than the end date', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

$joinUser = (($validUser==1)) ? "INNER JOIN reg_user_ ru ON r.id_user=ru.id_user AND r.id_company = ru.id_company" : 'LEFT JOIN reg_user_ ru ON r.id_user=ru.id_user AND r.id_company = ru.id_company';

$sql_query = "SELECT r.id_user as 'ID', ru.nombre AS 'Usuario', (CASE WHEN ru.estado='0' THEN 'Activo' WHEN ru.estado='1' THEN 'Inactivo' ELSE 'No determinado' END) as 'Estado', 
(SELECT COUNT(1) FROM reg_ r WHERE ru.id_user=r.id_user AND r.eventType=2 AND r.id_company = $idCompany AND r.fechaHora BETWEEN '$FechaIni' AND '$FechaFin') AS 'Fichadas' FROM reg_ r $joinUser WHERE r.rid > 0 AND r.eventType = 2";

$filtro_query = '';
$filtro_query .= " AND r.id_user > 0";
// $filtro_query .= ($params['checks'] == '1') ? " AND r.eventType = 2" : '';
$filtro_query .= ($idCompany) ? " AND r.id_company = $idCompany" : '';
$filtro_query .= (!empty($userID)) ? " AND r.id_user = $userID" : '';
$filtro_query .= (!empty($userName)) ? " AND ru.nombre LIKE '%$userName%'" : '';
$filtro_query .= (empty($createdDate)) ? " AND r.fechaHora BETWEEN '$FechaIni' AND '$FechaFin'" : '';
$filtro_query .= (!empty($userIDName))  ? " AND CONCAT(ru.id_user, ru.nombre) LIKE '%$userIDName%'" : '';
$sql_query .= $filtro_query;
$sql_query .= " GROUP BY r.id_user ORDER BY ru.nombre";
$total = rowCount_pdoQuery($sql_query);
// $sql_query .= " LIMIT $start, $length";
// echo $sql_query;exit;

$queryRecords = array_pdoQuery($sql_query);
if (($queryRecords)) {
    foreach ($queryRecords as $r) {
        $arrayData[] = array(
            'ID'       => $r['ID'],
            'Usuario'  => $r['Usuario'],
            'Estado'   => $r['Estado'],
            'Fichadas' => $r['Fichadas'],
        );
    }
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($total), 'OK', '', $tiempoScript, $countData, $idCompany));
exit;
