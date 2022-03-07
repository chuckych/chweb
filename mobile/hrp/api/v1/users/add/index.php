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
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'userRegid' || $key == 'userName' || $key == 'userID') {
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
    $pathLog  = __DIR__ . '../../../logs/addUser/' . date('Ymd') . '_log_addUser_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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
$userName     = userName();
$userID       = userID();
$userRegid    = userRegid();

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
if (empty($userID)) {
    http_response_code(400);
    (response(array(), 0, 'userID required', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

$q = "SELECT * FROM `reg_user_` WHERE `id_company` = '$idCompany' AND `nombre` = '$userName'";
$a = count_pdoQuery($q);

if ($a > 0) {
    $arrayData = array();
    $MESSAGE = 'El nombre de usuario ya existe';
    $finScript    = microtime(true);
    $tiempoScript = round($finScript - $iniScript, 2);
    $countData    = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, 400, $tiempoScript, $countData, $idCompany));
    exit;
}

$sql_query = "INSERT INTO `reg_user_` (`id_user`, `id_company`, `nombre`, `regid`) VALUES ('$userID', '$idCompany', '$userName', '$userRegid')";
$insert = pdoQuery($sql_query);
if ($insert) {
    $a = simple_pdoQuery("SELECT * FROM `reg_user_` WHERE `id_user` = '$userID' AND `id_company` = '$idCompany' LIMIT 1");
    $MESSAGE = 'OK';
    $arrayData = array(
        'userID'     => $a['id_user'],
        'id_company' => $a['id_company'],
        'userName'   => $a['nombre'],
        'userRegid'  => $a['regid'],
    );
    $text = "Alta Usuario \"$a[nombre]\" ID = $a[id] Evento = $a[evento] PhoneID = $a[phoneid]";
    fileLog($text, __DIR__ . '../../../logs/addUser/' . date('Ymd') . '_log_addUser_' . padLeft($idCompany, 3, 0) . '.log'); // _log_addUser_
} else {
    $MESSAGE = 'ERROR';
}
$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($countData), $MESSAGE, '', $tiempoScript, $countData, $idCompany));
exit;
