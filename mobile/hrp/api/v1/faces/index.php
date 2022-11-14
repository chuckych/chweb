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
borrarLogs(__DIR__ . '../../_logs/getFaces/', 30, '.log');

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
function userID()
{
    $p = $_REQUEST;
    $p['userID'] = $p['userID'] ?? '';
    $userID = empty($p['userID']) ? '' : $p['userID'];
    return intval($userID);
}
function validaKey()
{
    $p = $_REQUEST['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}

$idCompany = 0;

if (!isset($params['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0, 0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    $key = urldecode($key);
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'userID') {
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

    $pathLog  = __DIR__ . '../../_logs/getFaces/' . date('Ymd') . '_getFaces_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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

if (empty($userID)) {
    http_response_code(400);
    (response(array(), 0, 'User ID is required', 400, 0, 0, $idCompany));
}

$MESSAGE = 'OK';
$arrayData = array();

$notIdPunchEvent = '';
$q = "SELECT * FROM `reg_enroll` WHERE `id_company` = '$idCompany' AND `id_user` = '$userID'";
$a = array_pdoQuery($q);
if ($a) {
    foreach ($a as $key => $v) {
        $i[] = $v['idPunchEvent'];
    }
    $notIdPunchEvent = implode(',',$i);
}

$sql_query="SELECT r.id_user AS 'id_user', r.createdDate, r.fechaHora, r.phoneid, r.reg_uid AS 'reg_uid', CONCAT(r.createdDate, '_',r.phoneid) AS 'regPhoto', r.id_api AS 'id_api' FROM reg_ r WHERE r.id_user > 0 AND `r`.`rid` > 7669";

$filtro_query = '';
$filtro_query .= " AND r.id_company = $idCompany";
$filtro_query .= " AND r.id_user = '$userID'";
$filtro_query .= ($notIdPunchEvent) ? " AND r.id_api NOT IN ($notIdPunchEvent)" : '';
$filtro_query .= " AND r.id_api > 0";
$filtro_query .= " AND r.attphoto = '0'";
$sql_query .= $filtro_query;
// echo $filtro_query;exit;
$total = rowCount_pdoQuery($sql_query);
$sql_query .= " ORDER BY r.fechaHora DESC, r.createdDate DESC";
$sql_query .= " LIMIT $start, $length";

$imageTypeArray = array(
    0  => 'UNKNOWN',
    1  => 'GIF',
    2  => 'JPEG',
    3  => 'PNG',
    4  => 'SWF',
    5  => 'PSD',
    6  => 'BMP',
    7  => 'TIFF_II',
    8  => 'TIFF_MM',
    9  => 'JPC',
    10 => 'JP2',
    11 => 'JPX',
    12 => 'JB2',
    13 => 'SWC',
    14 => 'IFF',
    15 => 'WBMP',
    16 => 'XBM',
    17 => 'ICO',
    18 => 'COUNT'
);

$queryRecords = array_pdoQuery($sql_query);

if (($queryRecords)) {
    foreach ($queryRecords as $r) {
        $Fecha      = FechaFormatVar($r['fechaHora'], 'Y-m-d');
        // $regPhoto   = (intval($r['attPhoto']) == 0) ? "$r[regPhoto].png" : '';
        $eplodeFecha = explode('-', $Fecha);
        $PathAnio    = $eplodeFecha[0];
        $PathMes     = $eplodeFecha[1];
        $PathDia     = $eplodeFecha[2];
        $filename = "fotos/$idCompany/$PathAnio/$PathMes/$PathDia/";
        $filenameOld = "fotos/$idCompany/";

        $img = $filename . intval($r['createdDate']) . '_' . $r['phoneid'] . '.jpg';
        $imgOld = $filenameOld . intval($r['createdDate']) . '_' . $r['phoneid'] . '.png';
        $urlImg = (intval($r['createdDate']) > 1651872233773) ? $img : $imgOld;
        $size = getimagesize("../../../" . $urlImg);
        $size[2] = $imageTypeArray[$size[2]];
        list($ancho, $alto, $tipo, $atributos) = $size;

        $arrayData[] = array(
            "id_user"  => $r['id_user'],
            "id_api"   => $r['id_api'],
            'imageData'         => array(
                'ancho'             => $ancho,
                'alto'              => $alto,
                'tipo'              => $size[2],
                'img'               => $urlImg,
            ),
        );
    }
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($total), 'OK', '', $tiempoScript, $countData, $idCompany));
exit;
