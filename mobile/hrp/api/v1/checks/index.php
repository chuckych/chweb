<?php
require __DIR__ . '../../../../../../config/index.php';
// require __DIR__ . '../../../../../vendor/autoload.php';
// use Carbon\Carbon;
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();
borrarLogs(__DIR__ . '../../_logs/getChecks/', 30, '.log');

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
function identified()
{
    $p = $_REQUEST;
    $p['identified'] = $p['identified'] ?? '';
    $identified = empty($p['identified']) ? '' : $p['identified'];
    return intval($identified);
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
function zoneIDName()
{
    $p = $_REQUEST;
    $p['zoneIDName'] = $p['zoneIDName'] ?? '';
    $zoneIDName = empty($p['zoneIDName']) ? '' : $p['zoneIDName'];
    return urldecode($zoneIDName);
}
function deviceIDName()
{
    $p = $_REQUEST;
    $p['deviceIDName'] = $p['deviceIDName'] ?? '';
    $deviceIDName = empty($p['deviceIDName']) ? '' : $p['deviceIDName'];
    return urldecode($deviceIDName);
}
function validaKey()
{
    $p = $_REQUEST['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}
function createdDate()
{
    $p = $_REQUEST;
    $p['createdDate'] = $p['createdDate'] ?? '';
    $createdDate  = empty($p['createdDate']) ? '' : $p['createdDate'];
    return intval($createdDate);
}
function validUser()
{
    $p = $_REQUEST;
    $p['validUser'] = $p['validUser'] ?? '';
    $validUser  = empty($p['validUser']) ? '' : $p['validUser'];
    return intval($validUser);
}

$zones   = $_REQUEST['zones'] ?? array();
$users   = $_REQUEST['users'] ?? array();
$devices = $_REQUEST['devices'] ?? array();
$groupBy = ($_REQUEST['groupBy']) ?? '';

$idCompany = 0;

if (!isset($params['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0, 0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    $key = urldecode($key);
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'checks' || $key == 'startDate' || $key == 'endDate' || $key == 'userID' || $key == 'userName' || $key == 'userIDName' || $key == 'zoneIDName' || $key == 'createdDate' || $key == 'validUser' || $key == 'zones' || $key == 'users' || $key == 'identified' || $key == 'devices' 
    || $key == 'groupBy' || $key == 'deviceIDName') {
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
    // foreach ($_REQUEST as $key => $value) {
    //         $arrRequest = "$key = $value";
    //         array_push($textParams, $arrRequest);
    // }

    $textParams = implode('&', $textParams); // convert to string

    $ipAdress = $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $agent    = $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    // $idCompany    = $idCompany;

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

    $pathLog  = __DIR__ . '../../_logs/getChecks/' . date('Ymd') . '_getChecks_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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
$identified   = identified();
$userName     = userName();
$userIDName   = userIDName();
$zoneIDName   = zoneIDName();
$deviceIDName = deviceIDName();
$FechaIni     = startDate();
$FechaFin     = endDate();
$createdDate  = createdDate();
$validUser    = validUser();
// checkEmpty($createdDate, 'createdDate');

$validaKey = validaKey();
$vkey = '';
foreach ($iniKeys as $key => $value) {
    if ($value['recidCompany'] == $validaKey) {
        $idCompany = $value['idCompany'];
        $vkey      = $value['recidCompany'];
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

if ($identified && $identified != 1 && $identified != 2) {
    http_response_code(400);
    (response(array(), 0, 'Identified invalid', 400, 0, 0, $idCompany));
}
if ( $groupBy && $groupBy !='user' && $groupBy !='zone' && $groupBy !='device' ){
    http_response_code(400);
    (response(array(), 0, 'groupBy invalid', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();
$joinUser = (($validUser == 1)) ? "LEFT JOIN reg_user_ ru ON r.id_user=ru.id_user AND r.id_company = ru.id_company" : 'LEFT JOIN reg_user_ ru ON r.id_user=ru.id_user AND r.id_company = ru.id_company';
//$joinFaces = ($FechaIni >= '2022-09-01') ? "LEFT JOIN reg_faces rf ON r.id_user = rf.id_user AND r.id_company = rf.id_company AND r.createdDate = rf.createdDate" : '';
//$basePhoto = ($FechaIni >= '2022-09-01') ? " rf.photo as 'basePhoto'," : '';

if ($groupBy) {
    
    switch ($groupBy) {
        case 'user':
            $groupBy = 'r.id_user';
            break;
        case 'zone':
            $groupBy = 'r.idZone';
            break;
        case 'device':
            $groupBy = 'r.deviceID';
            break;
    }
}

$countGroup = ($groupBy) ? "COUNT(r.id_user) AS 'countGroup'," : '';

$sql_query = "SELECT 
    r.createdDate AS 'createdDate', 
    $countGroup
    r.id_user AS 'id_user', 
    r.phoneid AS 'phoneid', 
    r.deviceID AS 'deviceID', 
    ru.nombre AS 'name', 
    r.fechaHora 'fechaHora', 
    r.lat AS 'lat', 
    r.lng AS 'lng', 
    r.gpsStatus AS 'gpsStatus', 
    r.eventType AS 'eventType', 
    r.operationType AS 'operationType', 
    r.operation AS 'operation', 
    r.appVersion AS 'appVersion', 
    r.attphoto AS 'attPhoto', 
    r.id_company AS 'id_company',
    rd.nombre AS 'deviceName',
    rd.evento AS 'deviceEvent',
    r.idZone AS 'zoneID',
    rz.nombre AS 'zoneName',
    rz.lat AS 'zoneLat',
    rz.lng AS 'zoneLng',
    rz.radio AS 'zoneRadio',
    r.distance AS 'zoneDistance',
    r.reg_uid AS 'reg_uid',
    CONCAT(r.createdDate, '_',r.phoneid) AS 'regPhoto',
    LEFT(r.regid, 10) AS 'regid',
    r.id_api AS 'id_api',
    r.locked AS 'locked',
    r.error AS 'error',
    r.confidence AS 'confidence', 
    r.threshold AS 'threshold', 
    r.eventZone AS 'eventZone', 
    r.eventDevice AS 'eventDevice',
    r.identified AS 'identified'
    FROM reg_ r
    $joinUser
    LEFT JOIN reg_device_ rd ON r.phoneid=rd.phoneid AND r.id_company = rd.id_company
    LEFT JOIN reg_zones rz ON r.id_company = rz.id_company AND r.idZone = rz.id 
    WHERE r.rid > 0";

$identificado = ($identified == 1) ? true : ''; 
$Noidentificado = ($identified == 2) ? true : ''; 



$filtro_query = '';
$filtro_query .= " AND r.id_user > 0";
$filtro_query .= ($params['checks'] == '1') ? " AND r.eventType = 2" : '';
$filtro_query .= ($idCompany) ? " AND r.id_company = $idCompany" : '';
$filtro_query .= (!empty($userID)) ? " AND r.id_user = $userID" : '';
$filtro_query .= (!empty($userName)) ? " AND ru.nombre LIKE '%$userName%'" : '';
$filtro_query .= (!empty($userIDName))  ? " AND CONCAT(ru.id_user, ru.nombre) LIKE '%$userIDName%'" : '';
$filtro_query .= (!empty($zoneIDName))  ? " AND CONCAT(rz.id, rz.nombre) LIKE '%$zoneIDName%'" : '';
$filtro_query .= (!empty($deviceIDName))  ? " AND CONCAT(rd.id, rd.nombre) LIKE '%$deviceIDName%'" : '';
$filtro_query .= (empty($createdDate)) ? " AND r.fechaHora BETWEEN '$FechaIni' AND '$FechaFin'" : '';
$filtro_query .= (!empty($createdDate)) ? " AND r.createdDate > '$createdDate'" : '';
$filtro_query .= (!empty($zones)) ? " AND r.idZone IN ($zones)" : '';
$filtro_query .= (!empty($users)) ? " AND r.id_user IN ($users)" : '';
$filtro_query .= (!empty($devices)) ? " AND r.deviceID IN ($devices)" : '';
$filtro_query .= ($identificado) ? " AND r.identified = '1'" : '';
$filtro_query .= ($Noidentificado) ? " AND r.identified = '0'" : '';
// $filtro_query .= (($validUser==1)) ? "AND ru.nombre != ''" : '';
// $filtro_query .= ' GROUP BY id_user, fechaHora, phoneid';
$filtro_query .= ($groupBy) ? " GROUP BY ".$groupBy : '';
$sql_query .= $filtro_query;
// echo $filtro_query;exit;
$total = rowCount_pdoQuery($sql_query);
$sql_query .= " ORDER BY r.fechaHora DESC, r.createdDate DESC";
$sql_query .= " LIMIT $start, $length";
$imageTypeArray = array( 0=>'UNKNOWN', 1=>'GIF', 2=>'JPEG', 3=>'PNG', 4=>'SWF', 5=>'PSD', 6=>'BMP', 7=>'TIFF_II', 8=>'TIFF_MM', 9=>'JPC', 10=>'JP2', 11=>'JPX', 12=>'JB2', 13=>'SWC', 14=>'IFF', 15=>'WBMP', 16=>'XBM', 17=>'ICO', 18=>'COUNT');
function human_filesize($bytes, $decimals = 2)
{
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

// print_r($sql_query);exit;
$queryRecords = array_pdoQuery($sql_query);
if (($queryRecords)) {
    foreach ($queryRecords as $r) {
        $Fecha      = FechaFormatVar($r['fechaHora'], 'Y-m-d');
        $appVersion = explode('-', $r['appVersion']);
        $appVersion = trim($appVersion[0] ?? '' . ' ' . $appVersion[1] ?? '');
        $regPhoto   = (intval($r['attPhoto']) == 0) ? "$r[regPhoto].png" : '';

        $eplodeFecha = explode('-', $Fecha);
        $PathAnio    = $eplodeFecha[0];
        $PathMes     = $eplodeFecha[1];
        $PathDia     = $eplodeFecha[2];
        $filename = "fotos/$idCompany/$PathAnio/$PathMes/$PathDia/";
        $filenameOld = "fotos/$idCompany/";

        $img = $filename . intval($r['createdDate']) . '_' . $r['phoneid'] . '.jpg';
        $imgOld = $filenameOld . intval($r['createdDate']) . '_' . $r['phoneid'] . '.png';
        $urlImg = (intval($r['createdDate']) > 1651872233773) ? $img : $imgOld;

        if (file_exists("../../../" . $urlImg)) {
            $size = getimagesize("../../../" . $urlImg);
            $size[2] = $imageTypeArray[$size[2]];
            $type = $size[2];
            $filesize = filesize("../../../" . $urlImg);
            $FileSizeConvert = FileSizeConvert($filesize);
            list($ancho, $alto, $tipo, $atributos) = $size;
        } else {
            $size = '';
            $type = '';
            $filesize = '';
            $FileSizeConvert = '';
        }

        if ($r['id_company'] == '19') {
            $timestamp_19     = substr(intval($r['createdDate']), 0, 10);
            $dates = new DateTime("@" . $timestamp_19);  // will snap to UTC because of the 
            $dates->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
            $r['fechaHora'] = $dates->format('Y-m-d H:i:s');  // Buenos_Aires time
        }

        $arrayData[] = array(
            'appVersion'        => $appVersion,
            'attPhoto'          => intval($r['attPhoto']),
            'createdDate'       => intval($r['createdDate']),
            'deviceName'        => trim($r['deviceName']),
            'deviceEvent'        => trim($r['deviceEvent'] ?? '0'),
            'eventType'         => $r['eventType'],
            'eventZone'         => $r['eventZone'],
            'eventDevice'       => intval($r['eventDevice']),
            'gpsStatus'         => $r['gpsStatus'],
            'operation'         => $r['operation'],
            'operationType'     => $r['operationType'],
            'phoneid'           => ($r['phoneid']),
            'deviceID'           => intval($r['deviceID']),
            'regDate'           => FechaFormatVar($r['fechaHora'], 'Y-m-d'),
            'regDateTime'       => ($r['fechaHora']),
            'regUID'            => ($r['reg_uid']),
            'regDay'            => DiaSemana3(FechaString($r['fechaHora'])),
            'regLat'            => floatval($r['lat']),
            'regLng'            => floatval($r['lng']),
            'regPhoto'          => $regPhoto,
            'regTime'           => (HoraFormat($r['fechaHora'], false)),
            'userCompany'       => $r['id_company'],
            'userID'            => intval($r['id_user']),
            'userName'          => trim($r['name']),
            'phoneRegID'        => trim($r['regid']),
            'zoneID'            => intval($r['zoneID']),
            'zoneName'  => $r['zoneName'],
            'zoneLat'   => $r['zoneLat'],
            'zoneLng'   => $r['zoneLng'],
            'zoneRadio' => $r['zoneRadio'],
            'zoneDistance'      => (intval($r['zoneID'] > 0)) ? floatval($r['zoneDistance']) : '',
            'zoneDistanceStr'   => (intval($r['zoneID'] > 0)) ? round(floatval($r['zoneDistance']) * 1000, 2) . " Mts." : '',
            'locked'            => intval($r['locked']),
            'error'             => $r['error'],
            'confidenceFaceVal' => floatval($r['confidence']),
            'confidenceFaceStr' => (confidenceFaceStr($r['confidence'], $r['id_api'], intval($r['threshold']))),
            'threshold'         => intval($r['threshold']),
            'identified'         => intval($r['identified']),
            'id_api'            => intval($r['id_api']),
            'imageData'         => array(
                'ancho'     => $ancho ??'',
                'alto'      => $alto ??'',
                'tipo'      => $type ??'',
                'img'       => $urlImg ??'',
                'size'      => $filesize ??'',
                'humanSize' => $FileSizeConvert ??''
            ),
            'basePhoto' => $r['basePhoto'] ?? '',
            'countGroup' => ($groupBy) ? $r['countGroup'] : ''
        );
    }
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($total), 'OK', '', $tiempoScript, $countData, $idCompany));
exit;
