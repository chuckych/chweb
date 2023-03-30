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
borrarLogs(__DIR__ . '../../../_logs/getEnroll/', 30, '.log');
$total = 0;
$params = $_GET;

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}

$iniScript = microtime(true);
$idCompany = 0;

function start()
{
    $p = $_GET;
    $p['start'] = $p['start'] ?? '0';
    $start  = empty($p['start']) ? 0 : $p['start'];
    return intval($start);
}
function length()
{
    $p = $_GET;
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

function validaKey()
{
    $p = $_GET['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}
if (!isset($_GET['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0, 0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    if ($key == 'key' || $key == 'start' || $key == 'length'  || $key == 'userID') {
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
    $pathLog  = __DIR__ . '../../../_logs/getEnroll/' . date('Ymd') . '_log_getEnroll_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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

$MESSAGE = 'OK';
$arrayData = array();

/** Validar que el id_user exita */
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

$q = "SELECT `reg_enroll`.`idPunchEvent`, `reg_enroll`.`faceIdAws`, `reg_enroll`.`id_company`, `reg_enroll`.`id_user`, `reg_enroll`.`fechahora`, `reg_`.`createdDate`, `reg_`.`fechaHora`, `reg_`.`phoneid`, `reg_`.`reg_uid`, CONCAT(`reg_`.`createdDate`, '_',`reg_`.`phoneid`) AS 'regPhoto' FROM `reg_enroll` INNER JOIN `reg_` ON `reg_enroll`.`idPunchEvent`=`reg_`.`id_api` WHERE `reg_enroll`.`id_company`='$idCompany' AND `reg_enroll`.`id_user`='$userID' AND `reg_`.`rid` > 7669";
// $a = array_pdoQuery($q);
$q .= " ORDER BY `reg_enroll`.`fechahora` DESC";

// print_r($q).exit;


$imageTypeArray = array( 0=>'UNKNOWN', 1=>'GIF', 2=>'JPEG', 3=>'PNG', 4=>'SWF', 5=>'PSD', 6=>'BMP', 7=>'TIFF_II', 8=>'TIFF_MM', 9=>'JPC', 10=>'JP2', 11=>'JPX', 12=>'JB2', 13=>'SWC', 14=>'IFF', 15=>'WBMP', 16=>'XBM', 17=>'ICO', 18=>'COUNT' );

$queryRecords = array_pdoQuery($q);
// print_r($queryRecords).exit;

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
        $ancho = $alto = $tipo = $atributos = '';
        $img = $filename . intval($r['createdDate']) . '_' . $r['phoneid'] . '.jpg';
        $imgOld = $filenameOld . intval($r['createdDate']) . '_' . $r['phoneid'] . '.png';
        $urlImg = (intval($r['createdDate']) > 1651872233773) ? $img : $imgOld;

        if (file_exists("../../../../" . $urlImg)) {
            $size = getimagesize("../../../../" . $urlImg);
            $size[2] = $imageTypeArray[$size[2]];
            $type = $size[2];
            $filesize = filesize("../../../../" . $urlImg);
            $FileSizeConvert = FileSizeConvert($filesize);
            list($ancho, $alto, $tipo, $atributos) = $size;
        } else {
            $size = '';
            $type = '';
            $filesize = '';
            $FileSizeConvert = '';
            $imageTypeArray['JPEG'] = '';
        }

        if ($size) {
            $size[2] = $imageTypeArray[$size[2]];
            list($ancho, $alto, $tipo, $atributos) = $size;
            $arrayData[] = array(
                "id_user" => $r['id_user'],
                "id_api"  => $r['idPunchEvent'],
                'imageData' => array(
                    'ancho'     => $ancho,
                    'alto'      => $alto,
                    'tipo'      => $type,
                    'img'       => $urlImg,
                    'size'      => $filesize,
                    'humanSize' => $FileSizeConvert
                ),
            );
        }
    }
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, $countData, $MESSAGE, 'OK', $tiempoScript, $countData, $idCompany));
exit;
