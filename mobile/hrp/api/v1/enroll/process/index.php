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
borrarLogs(__DIR__ . '../../../_logs/enroll/', 30, '.log');
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
function idApi()
{
    $p = $_POST;
    $p['idApi'] = $p['idApi'] ?? '';
    $idApi  = empty($p['idApi']) ? '' : $p['idApi'];
    return intval($idApi);
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

// print_r($_POST);
// exit;
foreach ($params as $key => $value) {
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'idApi') {
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
    $pathLog  = __DIR__ . '../../../_logs/enroll/' . date('Ymd') . '_log_process_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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
$idApi        = idApi();
$validaKey    = validaKey();
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
if (empty($urlAppMobile)) {
    http_response_code(400);
    (response(array(), 0, 'Empty URL API', 400, 0, 0, $idCompany));
}
$ping = pingApiMobileHRP($urlAppMobile);

if (empty($ping)) {
    http_response_code(400);
    (response(array(), 0, 'Error conection API', 400, 0, 0, $idCompany));
}

if (empty($idApi)) {
    http_response_code(400);
    (response(array(), 0, 'idApi required', 400, 0, 0, $idCompany));
}
if (strlen($idApi) <= 0) {
    http_response_code(400);
    (response(array(), 0, 'idApi required', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

/** Validar que el ID exita */
$q = "SELECT id_api FROM `reg_` WHERE `id_company` = '$idCompany' AND `id_api` = '$idApi' LIMIT 1";
$a = count_pdoQuery($q);
// print_r($a);
// exit;
if (empty($a)) {
    $arrayData = array();
    $MESSAGE = 'idApi is invalid';
    $finScript    = microtime(true);
    $tiempoScript = round($finScript - $iniScript, 2);
    $countData    = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, 400, $tiempoScript, $countData, $idCompany));
    exit;
}

$a = simple_pdoQuery("SELECT reg_.fechaHora as 'fechaHora',  reg_user_.nombre as 'nombre', reg_.id_user as 'id_user', reg_.id_company as 'id_company', reg_.id_api as 'idApi' FROM `reg_` 
LEFT JOIN reg_user_ ON reg_.id_user = reg_user_.id_user
WHERE reg_.id_company = '$idCompany' AND reg_.id_api = '$idApi' LIMIT 1");

$MESSAGE = 'OK';

$sendApi = sendApiMobileHRP('', $urlAppMobile, 'attention/api/punch-event/reprocess-facial-recognition/' . $idApi, $idCompany, false);
$responseApi = (json_decode($sendApi));

// echo $urlAppMobile.'/attention/api/punch-event/reprocess-facial-recognition/' . $idApi; exit;

$f = FechaFormatVar($a['fechaHora'], 'd/m/Y H:i');
$text = "Rostro registro fecha $f - $a[nombre]. ID = $a[id_user] reprocesado";
fileLog($text, __DIR__ . '../../../_logs/addUser/' . date('Ymd') . '_log_addUser_' . padLeft($idCompany, 3, 0) . '.log'); // _log_addUser_
$arrayData = array(
    // 'idApi'       => $a['id_user'],
    'id_company'  => $a['id_company'],
    'userName'    => $a['nombre'],
    'idApi'       => $a['idApi'],
    'fechaHora'   => $f,
    'textAud'     => $text,
    'responseApi' => $responseApi,
);
if($responseApi->payload){
    $confidence = $responseApi->payload->confidence;
    $q = "UPDATE `reg_` SET `confidence` = '$confidence' WHERE `id_company` = '$idCompany' AND `id_user` = $a[id_user] AND `id_api` = '$idApi'";
    simple_pdoQuery($q);
}
$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, 1, $MESSAGE, 'OK', $tiempoScript, 1, $idCompany));
exit;
