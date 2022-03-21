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
borrarLogs(__DIR__ . '../../../_logs/addChecks/', 30, '.log');
$total = 0;
$params = $_POST;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}

$startScript = microtime(true);
$idCompany = 0;

// echo json_encode($_REQUEST);
// exit;

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
function employeId()
{
    $p = $_POST;
    $p['employeId'] = $p['employeId'] ?? 0;
    $employeId  = empty($p['employeId']) ? 0 : $p['employeId'];
    return urldecode($employeId);
}
function phoneid()
{
    $p = $_POST;
    $p['phoneid'] = $p['phoneid'] ?? '0';
    $phoneid  = empty($p['phoneid']) ? 0 : $p['phoneid'];
    return urldecode($phoneid);
}
function companyCode()
{
    $p = $_POST;
    $p['companyCode'] = $p['companyCode'] ?? 0;
    $companyCode  = empty($p['companyCode']) ? 0 : $p['companyCode'];
    return intval($companyCode);
}
function createdDate()
{
    $p = $_POST;
    $p['createdDate'] = $p['createdDate'] ?? 0;
    $createdDate  = empty($p['createdDate']) ? 0 : $p['createdDate'];
    return urldecode($createdDate);
}
function _fechaHora()
{
    $p = $_POST;
    $p['fechaHora'] = $p['fechaHora'] ?? '';
    $fechaHora  = empty($p['fechaHora']) ? '' : $p['fechaHora'];
    return urldecode($fechaHora);
}
function lat()
{
    $p = $_POST;
    $p['lat'] = $p['lat'] ?? '';
    $lat  = empty($p['lat']) ? '' : $p['lat'];
    return floatval($lat);
}
function lng()
{
    $p = $_POST;
    $p['lng'] = $p['lng'] ?? '';
    $lng  = empty($p['lng']) ? '' : $p['lng'];
    return floatval($lng);
}
function idZone()
{
    $p = $_POST;
    $p['idZone'] = $p['idZone'] ?? 0;
    $idZone  = empty($p['idZone']) ? 0 : $p['idZone'];
    return intval($idZone);
}
function distancia()
{
    $p = $_POST;
    $p['distancia'] = $p['distancia'] ?? '';
    $distancia  = empty($p['distancia']) ? 0 : $p['distancia'];
    return floatval($distancia);
}
function gpsStatus()
{
    $p = $_POST;
    $p['gpsStatus'] = $p['gpsStatus'] ?? '';
    $gpsStatus  = empty($p['gpsStatus']) ? '' : $p['gpsStatus'];
    return intval($gpsStatus);
}
function eventType()
{
    $p = $_POST;
    $p['eventType'] = $p['eventType'] ?? '';
    $eventType  = empty($p['eventType']) ? '' : $p['eventType'];
    return intval($eventType);
}
function operationType()
{
    $p = $_POST;
    $p['operationType'] = $p['operationType'] ?? '';
    $operationType  = empty($p['operationType']) ? '' : $p['operationType'];
    return intval($operationType);
}
function operation()
{
    $p = $_POST;
    $p['operation'] = $p['operation'] ?? '';
    $operation  = empty($p['operation']) ? '' : $p['operation'];
    return urldecode($operation);
}
function _id()
{
    $p = $_POST;
    $p['_id'] = $p['_id'] ?? '';
    $_id  = empty($p['_id']) ? '' : $p['_id'];
    return urldecode($_id);
}
function regid()
{
    $p = $_POST;
    $p['regid'] = $p['regid'] ?? '';
    $regid  = empty($p['regid']) ? '' : $p['regid'];
    return urldecode($regid);
}
function appVersion()
{
    $p = $_POST;
    $p['appVersion'] = $p['appVersion'] ?? '';
    $appVersion  = empty($p['appVersion']) ? '' : $p['appVersion'];
    return urldecode($appVersion);
}
function attphoto()
{
    $p = $_POST;
    $p['attphoto'] = $p['attphoto'] ?? '0';
    $attphoto  = empty($p['attphoto']) ? '0' : $p['attphoto'];
    return ($attphoto);
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
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'employeId' || $key == 'phoneid' || $key == 'companyCode' || $key == 'createdDate' || $key == 'fechaHora' || $key == 'lat' || $key == 'lng' || $key == 'idZone' || $key == 'distancia' || $key == 'gpsStatus' || $key == 'eventType' || $key == 'operationType' || $key == 'operation' || $key == '_id' || $key == 'regid' || $key == 'appVersion' || $key == 'attphoto') {
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

    $pathLog  = __DIR__ . '../../../_logs/addChecks/' . date('Ymd') . '_log_addChecks_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
    /** start text log*/
    $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
    /** end text log*/
    fileLog($TextLog, $pathLog); // Log Api
    /** END LOG API CONFIG */
    exit;
}
function checkEmpty($value, $key)
{
    if (empty($value)) {
        http_response_code(400);
        (response(array(), 0, "$key required", 400, 0, 0, '0'));
        exit;
    }
}
function checkLenght($value, $lenght, $key)
{
    if (strlen($value) > $lenght) {
        http_response_code(400);
        (response(array(), 0, "$key max length $lenght", 400, 0, 0, '0'));
        exit;
    }
}

$queryRecords  = array();
$start         = start();
$length        = length();
$employeId     = employeId();
checkEmpty($employeId, 'employeId');
checkLenght($employeId, 11, 'employeId');
$phoneid       = phoneid();
checkEmpty($phoneid, 'phoneid');
$companyCode   = companyCode();
checkEmpty($companyCode, 'companyCode');
$createdDate   = createdDate();
checkEmpty($createdDate, 'createdDate');
$fechaHora     = _fechaHora();
checkEmpty($fechaHora, 'fechaHora');
$lat           = lat();
$lng           = lng();
$idZone        = idZone();
$distancia     = distancia();
$gpsStatus     = gpsStatus();
$eventType     = eventType();
$operationType = operationType();
$operation     = operation();
$_id           = _id();
$regid         = regid();
$appVersion    = appVersion();
$attphoto    = attphoto();


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


timeZone();
require __DIR__ . '../../../../../../../config/conect_pdo.php';
$connpdo->beginTransaction();

try {
 $sql = "INSERT INTO reg_ ( `reg_uid`, `id_user`, `id_company`, `phoneid`, `fechaHora`, `createdDate`, `lat`, `lng`, `idZone`, `distance`, `gpsStatus`, `eventType`, `operationType`, `operation`, `_id`, `regid`, `appVersion`, `attphoto` ) VALUES ( :reg_uid, :employeId, :companyCode, :phoneid, :fechaHora, :createdDate, :lat, :lng, :idZone, :distancia, :gpsStatus, :eventType, :operationType, :operation, :_id, :regid, :appVersion, :attphoto )";

    $stmt = $connpdo->prepare($sql); // prepara la consulta
    $reg_uid = sha1($createdDate.$employeId);
    $reg_uid = sha1(microtime().$reg_uid);
    // cortrar la cadena a 8 caracteres
    $reg_uid = substr($reg_uid, 0, 8);
    $d = [
        "reg_uid"       => $reg_uid,
        "id_user"       => $employeId,
        "id_company"    => $companyCode,
        "phoneid"       => $phoneid,
        "fechaHora"     => $fechaHora,
        "createdDate"   => $createdDate,
        "lat"           => $lat,
        "lng"           => $lng,
        "idZone"        => $idZone,
        "distance"      => $distancia,
        "gpsStatus"     => $gpsStatus,
        "eventType"     => $eventType,
        "operationType" => $operationType,
        "operation"     => $operation,
        "_id"           => $_id,
        "regid"         => $regid,
        "appVersion"    => $appVersion,
        "attphoto"      => $attphoto
    ];

    $stmt->bindParam(':reg_uid', $d['reg_uid'], PDO::PARAM_STR);
    $stmt->bindParam(':employeId', $d['id_user'], PDO::PARAM_INT);
    $stmt->bindParam(':companyCode', $d['id_company'], PDO::PARAM_INT);
    $stmt->bindParam(':phoneid', $d['phoneid'], PDO::PARAM_INT);
    $stmt->bindParam(':fechaHora', $d['fechaHora'], PDO::PARAM_STR);
    $stmt->bindParam(':createdDate', $d['createdDate'], PDO::PARAM_STR);
    $stmt->bindParam(':lat', $d['lat'], PDO::PARAM_STR);
    $stmt->bindParam(':lng', $d['lng'], PDO::PARAM_STR);
    $stmt->bindParam(':idZone', $d['idZone'], PDO::PARAM_INT);
    $stmt->bindParam(':distancia', $d['distance'], PDO::PARAM_STR);
    $stmt->bindParam(':gpsStatus', $d['gpsStatus'], PDO::PARAM_INT);
    $stmt->bindParam(':eventType', $d['eventType'], PDO::PARAM_INT);
    $stmt->bindParam(':operationType', $d['operationType'], PDO::PARAM_INT);
    $stmt->bindParam(':operation', $d['operation'], PDO::PARAM_STR);
    $stmt->bindParam(':_id', $d['_id'], PDO::PARAM_STR);
    $stmt->bindParam(':regid', $d['regid'], PDO::PARAM_STR);
    $stmt->bindParam(':appVersion', $d['appVersion'], PDO::PARAM_STR);
    $stmt->bindParam(':attphoto', $d['attphoto'], PDO::PARAM_STR);
    $stmt->execute();

    $lastInsertId = $connpdo->lastInsertId();
    $rowCount     = $stmt->rowCount();
    $connpdo->commit(); // si todo salio bien, confirma la transaccion
    $total = $rowCount;
    $count = $rowCount;
    $arrayData = array(
        'total' => $total,
        'count' => $count,
        'data'  => $d
    );
} catch (\Throwable $th) { // si hay error
    $arrayData = array();
    $total = 0;
    $count = 0;
    $MESSAGE = 'ERROR';
    $MESSAGE = $th->getMessage(); // mensaje de error
    // $MESSAGE = $stmt; // mensaje de error
    $connpdo->rollBack(); // revierte la transaccion
    $pathLog  = __DIR__ . '../../../_logs/addChecks/' . date('Ymd') . '_log_error_addChecks.log'; // path Log Api
    fileLog($th->getMessage(), $pathLog); // escribir en el log de errores
}
$connpdo = null; // cierra la conexion

$endScript  = microtime(true);
$timeScript = round($endScript - $startScript, 2);
$countData  = count($arrayData);
(response($arrayData, $total, $MESSAGE, '', $timeScript, $count, $idCompany));
exit;

// $insert = pdoQuery($sql_query);
// if ($insert) {
//     $a = simple_pdoQuery("SELECT * FROM `reg_device_` WHERE `phoneid` = '$devicePhoneID' AND `id_company` = '$idCompany' LIMIT 1");
//     $MESSAGE = 'OK';
//     $arrayData = array(
//         'deviceID'      => $a['id'],
//         'devicePhoneID' => $a['phoneid'],
//         'id_company'    => $a['id_company'],
//         'deviceName'    => $a['nombre'],
//         'deviceEvent'   => $a['evento'],
//     );
//     $text = "Alta Dispositivo \"$a[nombre]\" ID = $a[id] Evento = $a[evento] PhoneID = $a[phoneid]";
//     fileLog($text, __DIR__ . '../../../_logs/addChecks/' . date('Ymd') . '_log_addChecks_' . padLeft($idCompany, 3, 0) . '.log'); // _log_addChecks_
// } else {
//     $MESSAGE = 'ERROR';
// }
// $endScript    = microtime(true);
// $timeScript = round($endScript - $startScript, 2);
// $countData    = count($arrayData);
// (response($arrayData, 1, $MESSAGE, '', $timeScript, 1, $idCompany));
// exit;
