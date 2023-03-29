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
borrarLogs(__DIR__ . '../../../_logs/updUser/', 30, '.log');
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
function userID()
{
    $p = $_POST;
    $p['userID'] = $p['userID'] ?? '';
    $userID  = empty($p['userID']) ? '' : $p['userID'];
    return intval($userID);
}
function userName()
{
    $p = $_POST;
    $p['userName'] = $p['userName'] ?? '';
    $userName = empty($p['userName']) ? '' : $p['userName'];
    return urldecode($userName);
}
function userRegid()
{
    $p = $_POST;
    $p['userRegid'] = $p['userRegid'] ?? '';
    $userRegid = empty($p['userRegid']) ? '' : $p['userRegid'];
    return urldecode($userRegid);
}
function expiredStart()
{
    $p = $_POST;
    $p['expiredStart'] = $p['expiredStart'] ?? '';
    $expiredStart = empty($p['expiredStart']) ? '' : $p['expiredStart'];
    return ($expiredStart);
}
function expiredEnd()
{
    $p = $_POST;
    $p['expiredEnd'] = $p['expiredEnd'] ?? '';
    $expiredEnd = empty($p['expiredEnd']) ? '' : $p['expiredEnd'];
    return ($expiredEnd);
}
function textLock()
{
    $p = $_POST;
    $p['textLock'] = $p['textLock'] ?? '';
    $textLock = empty($p['textLock']) ? '' : $p['textLock'];
    return urldecode($textLock);
}
function status()
{
    $p = $_POST;
    $p['status'] = $p['status'] ?? '';
    $status = empty($p['status']) ? '0' : $p['status'];
    return ($status);
}
function hasArea()
{
    $p = $_POST;
    $p['hasArea'] = $p['hasArea'] ?? '';
    $hasArea = empty($p['hasArea']) ? '0' : $p['hasArea'];
    return ($hasArea);
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
    if ($key == 'key' || $key == 'userName' || $key == 'userID' || $key == 'userRegid' || $key == 'expiredStart' || $key == 'expiredEnd' || $key == 'textLock' || $key == 'status' || $key == 'hasArea') {
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
    $_SERVER['HTTP_USER_AGENT'] = ($_SERVER['HTTP_USER_AGENT']) ?? '';
    $agent    = urldecode($_SERVER['HTTP_USER_AGENT']);
    // $idCompany = $idCompany;

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
    $pathLog  = __DIR__ . '../../../_logs/updUser/' . date('Ymd') . '_log_updUser_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
    /** start text log*/
    $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
    /** end text log*/
    fileLog($TextLog, $pathLog); // Log Api
    /** END LOG API CONFIG */
    exit;
}

$queryRecords = array();
$userName     = userName();
$userRegid    = userRegid();
$userID       = userID();
$expiredStart = expiredStart();
$expiredEnd   = expiredEnd();
$textLock     = textLock();
$status       = status();
$hasArea       = hasArea();

$validaKey = validaKey();
$vkey = '';

foreach ($iniKeys as $key => $value) {
    if ($value['recidCompany'] == $validaKey) {
        $idCompany   = $value['idCompany'];
        $vkey        = $value['recidCompany'];
        $nameCompany = $value['nameCompany'];
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
$ping = pingApiMobileHRP($urlAppMobile);

if (empty($urlAppMobile)) {
    http_response_code(400);
    (response(array(), 0, 'Empty URL API', 400, 0, 0, $idCompany));
}
if (empty($ping)) {
    http_response_code(400);
    (response(array(), 0, 'Error conection API', 400, 0, 0, $idCompany));
}

if (empty($userID)) {
    http_response_code(400);
    (response(array(), 0, 'userID required', 400, 0, 0, $idCompany));
}
if (strlen($userID) > 11) {
    http_response_code(400);
    (response(array(), 0, 'userID max length 11', 400, 0, 0, $idCompany));
}
if (strlen($status) > 1) {
    http_response_code(400);
    (response(array(), 0, 'status max length 1', 400, 0, 0, $idCompany));
}
if (($status) != 1 && ($status) != 0) {
    http_response_code(400);
    (response(array(), 0, 'status format Error', 400, 0, 0, $idCompany));
}
if (strlen($hasArea) > 1) {
    http_response_code(400);
    (response(array(), 0, 'hasArea max length 1', 400, 0, 0, $idCompany));
}
if (($hasArea) != 1 && ($hasArea) != 0) {
    http_response_code(400);
    (response(array(), 0, 'hasArea format Error', 400, 0, 0, $idCompany));
}
if (intval($expiredStart) > intval($expiredEnd)) {
    http_response_code(400);
    (response(array(), 0, 'date error', 400, 0, 0, $idCompany));
}
if (strlen($userName) < 1) {
    http_response_code(400);
    (response(array(), 0, 'userName required', 400, 0, 0, $idCompany));
}
if (strlen($userName) > 50) {
    http_response_code(400);
    (response(array(), 0, 'userName max length 50', 400, 0, 0, $idCompany));
}

$q = "SELECT * FROM `reg_user_` WHERE `id_user` = '$userID' AND `id_company` = '$idCompany' LIMIT 1";
$a = simple_pdoQuery($q);
$MESSAGE = 'OK';

if (!$a) {
    $arrayData  = array();
    $MESSAGE    = 'userID does not exist';
    $endScript  = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    $countData  = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, '', $timeScript, $countData, $idCompany));
    exit;
}

$MESSAGE = 'OK';
$arrayData = array();

$q = "SELECT 1 FROM `reg_user_` WHERE `id_company` = '$idCompany' AND `nombre` = '$userName' AND `id_user` != '$userID'";
$a = count_pdoQuery($q);


if ($a > 0) {
    $arrayData = array();
    $MESSAGE = 'userName already exists';
    $endScript    = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    $countData    = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, '', $timeScript, $countData, $idCompany));
    exit;
}

// update query 
$sql_query = "UPDATE `reg_user_` SET `nombre` = '$userName', `regid` = '$userRegid', `estado` = '$status',  `hasArea` = '$hasArea',`expiredStart` = '$expiredStart', `expiredEnd` = '$expiredEnd', `motivo` = '$textLock' WHERE `id_user` = '$userID' AND `id_company` = '$idCompany'";

$update = pdoQuery($sql_query);
if ($update) {
    $a = simple_pdoQuery("SELECT * FROM `reg_user_` WHERE `id_user` = '$userID' AND `id_company` = '$idCompany' LIMIT 1");
    $MESSAGE = 'OK';

    $audData = array(
        'Usuario'        => $a['id_user'],
        'Nombre'         => $a['nombre'],
        'RegID'          => $a['regid'],
        'Motivo Bloqueo' => $a['motivo'],
        'Estado'         => $a['estado'],
        'hasArea'         => ($a['hasArea']),
        'Bloqueo Inicio' => ($a['expiredStart'] == '0000-00-00') ? null : $a['expiredStart'],
        'Bloqueo Fin'    => ($a['expiredEnd'] == '0000-00-00') ? null : $a['expiredEnd'],
    );

    $text = "Modificacion Usuario $a[nombre]. ID = $a[id_user]";
    fileLog($text, __DIR__ . '../../../_logs/updUser/' . date('Ymd') . '_log_updUser_' . padLeft($idCompany, 3, 0) . '.log'); // 

    $user[] = array(
        'idUser'           => $a['id_user'],
        'name'             => $a['nombre'],
        'status'           => 'OK',
        'comments'         => $a['motivo'],
        'locked'           => ($a['estado'] == 1) ? true : false,
        'expiredDateStart' => ($a['expiredStart'] == '0000-00-00') ? null : $a['expiredStart'],
        'expiredDateEnd'   => ($a['expiredEnd'] == '0000-00-00') ? null : $a['expiredEnd'],
        'hasArea' => ($a['hasArea'] == '1') ? true : false,
    );
    $body = [
        array(
            "id"               => intval($idCompany),
            "recid"            => "$vkey",
            "name"            => "$nameCompany",
            "locked"           => false,
            "expiredDateStart" => null,
            "expiredDateEnd"   => null,
            "users"            => $user
        )
    ];
    $payload = json_encode($body);
    
    $sendApi = sendApiMobileHRP($payload, $urlAppMobile, 'attention/api/access/update-users', $idCompany);
    $responseApi = (json_decode($sendApi));

    $arrayData = array(
        'userID'           => $a['id_user'],
        'id_company'       => $a['id_company'],
        'userName'         => $a['nombre'],
        'userRegid'        => $a['regid'],
        'textLock'         => $a['motivo'],
        'status'           => $a['estado'],
        'expiredDateStart' => ($a['expiredStart'] == '0000-00-00') ? null : $a['expiredStart'],
        'expiredDateEnd'   => ($a['expiredEnd'] == '0000-00-00') ? null : $a['expiredEnd'],
        'textAud'          => $text,
        'responseApi'      => $responseApi
    );
} else {
    $MESSAGE = 'ERROR';
}
$endScript    = microtime(true);
$timeScript = round($endScript - $startScript, 2);
$countData    = count($arrayData);
(response($arrayData, 1, ($MESSAGE), '', $timeScript, 1, $idCompany));
exit;
