<?php
require __DIR__ . '../../../../../../../config/index.php';

header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();

$iniKeys = (getDataIni(__DIR__ . '../../../../../../../mobileApikey.php'));
borrarLogs(__DIR__ . '../../../_logs/enroll/', 30, '.log');
$total = 0;
$params = $_POST;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
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
    $p = $_REQUEST;
    $p['userID'] = $p['userID'] ?? '';
    $userID = empty($p['userID']) ? '' : $p['userID'];
    return intval($userID);
}
function idPunchEvent()
{
    $p = $_REQUEST;
    $p['idPunchEvent'] = $p['idPunchEvent'] ?? '';
    $idPunchEvent = empty($p['idPunchEvent']) ? '' : $p['idPunchEvent'];
    if ($idPunchEvent) {
        return (explode(',', ($idPunchEvent)));
    }
    return array();
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
    if ($key == 'key' || $key == 'start' || $key == 'length'  || $key == 'userID' || $key == 'idPunchEvent') {
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

    $textParams = array();

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
    $pathLog  = __DIR__ . '../../../_logs/enroll/' . date('Ymd') . '_log_update_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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
$validaKey    = validaKey();
$idPunchEvent = idPunchEvent();
$vkey         = '';

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
if (empty($userID)) {
    http_response_code(400);
    (response(array(), 0, 'userID is required', 400, 0, 0, $idCompany));
}
if (empty(idPunchEvent())) {
    http_response_code(400);
    (response(array(), 0, 'idPunchEvent is required', 400, 0, 0, $idCompany));
}

$ping = pingApiMobileHRP($urlAppMobile);

// print_r($ping).exit;

if (empty($ping)) {
    http_response_code(400);
    (response(array(), 0, 'Error conection API', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

/** Validar que el ID exita */
$q = "SELECT id_user FROM `reg_user_` WHERE `id_company` = '$idCompany' AND `id_user` = '$userID' LIMIT 1";
$a = count_pdoQuery($q);

if (empty($a)) {
    $arrayData = array();
    $MESSAGE = 'userID is invalid';
    $finScript    = microtime(true);
    $tiempoScript = round($finScript - $iniScript, 2);
    $countData    = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, 400, $tiempoScript, $countData, $idCompany));
    exit;
}

$MESSAGE = 'OK';

foreach ($idPunchEvent as $index => $i) {
    $idPunch[] = array(
        "idPunchEvent" => $i
    );
}
$users[] = array(
    "idUser" => $userID,
    "photos" => $idPunch
);
$payload[] = array(
    "id" => $idCompany,
    "recid" => $vkey,
    "users" => $users
);
// print_r($payload).exit;
// $deleteEnroll = "DELETE FROM `reg_enroll` WHERE `id_user` = $userID AND `id_company` = $idCompany";

// if (pdoQuery($deleteEnroll)) {
$sendApi = sendApiMobileHRP(json_encode($payload), $urlAppMobile, "attention/api/access/update-enroll-users", $idCompany, true);
$responseApi = (json_decode($sendApi));
// }

// print_r($sendApi).exit;

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = 0;

$a = simple_pdoQuery("SELECT * FROM `reg_user_` WHERE `id_user` = '$userID' AND `id_company` = '$idCompany' LIMIT 1");

$text = "Fotos de usuario $a[nombre]. ID = $a[id_user] entrenadas";

$arrayData = array(
    'textAud'          => $text,
    'responseApi'      => $responseApi->message,
);

(response($arrayData, $countData, 'OK', 200, $tiempoScript, $countData, $idCompany));
exit;
