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
borrarLogs(__DIR__ . '../../../_logs/delZone/', 30, '.log');
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
function idZone()
{
    $p = $_POST;
    $p['idZone'] = $p['idZone'] ?? '';
    $idZone  = empty($p['idZone']) ? '' : test_input($p['idZone']);
    return intval($idZone);
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
    if ($key == 'key' || $key == 'idZone') {
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
    $pathLog  = __DIR__ . '../../../_logs/delZone/' . date('Ymd') . '_log_delZone_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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
$idZone        = idZone();

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
if (empty($idZone)) {
    http_response_code(400);
    (response(array(), 0, 'idZone required', 400, 0, 0, $idCompany));
}
if (strlen($idZone) > 11) {
    http_response_code(400);
    (response(array(), 0, 'idZone max length 11', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

$qDel = "SELECT * FROM `reg_` WHERE `id_company` = '$idCompany' AND `idZone` = '$idZone' LIMIT 1";
$a = count_pdoQuery($qDel);

if ($a) { // si tiene registros en la tbla de reg_
    $arrayData  = array();
    $MESSAGE    = 'This idZone cannot be deleted.';
    $endScript  = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    $countData  = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, '', $timeScript, $countData, $idCompany));
    exit;
}

$a = simple_pdoQuery("SELECT * FROM `reg_zones` WHERE `id` = '$idZone' AND `id_company` = '$idCompany' LIMIT 1");
$MESSAGE = 'OK';

if (!$a) {
    $arrayData  = array();
    $MESSAGE    = 'idZone does not exist';
    $endScript  = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    $countData  = count($arrayData);
    (response($arrayData, intval($countData), $MESSAGE, '', $timeScript, $countData, $idCompany));
    exit;
} else {
    $text = "Eliminacion Zona \"$a[nombre]\" ID = $a[id] Radio = $a[radio] Lat = $a[lat] Lng = $a[lng] Evento = $a[evento]";
    $arrayData = array(
        'id_company' => $a['id_company'],
        'zoneID'     => $a['id'],
        'zoneLat'    => $a['lat'],
        'zoneLng'    => $a['lng'],
        'zoneName'   => $a['nombre'],
        'zoneRadio'  => $a['radio'],
        'textAud'    => $text,
    );
}

$sql_query_delete = "DELETE FROM `reg_zones` WHERE `id_company` = '$idCompany' AND `id` = '$idZone'";
$delete = pdoQuery($sql_query_delete);

if ($delete) {
    $text = "Eliminacion Zona \"$arrayData[zoneName]\" ID = $arrayData[zoneID] Radio = $arrayData[zoneRadio] Lat = $arrayData[zoneLat] Lng = $arrayData[zoneLng]";
    fileLog($text, __DIR__ . '../../../_logs/delZone/' . date('Ymd') . '_log_delZone_' . padLeft($idCompany, 3, 0) . '.log'); // _log_addDevice_
} else {
    $MESSAGE = 'ERROR';
    $endScript    = microtime(true);
    $timeScript = round($endScript - $startScript, 2);
    $countData    = count($arrayData);
    (response($arrayData, 0, $MESSAGE, '', $timeScript, 0, $idCompany));
}
$endScript    = microtime(true);
$timeScript = round($endScript - $startScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($countData), $MESSAGE, '', $timeScript, $countData, $idCompany));
exit;
