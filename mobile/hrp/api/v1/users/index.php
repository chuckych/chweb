<?php
require __DIR__ . '../../../../../../config/index.php';
// require __DIR__ . '../../../../../vendor/autoload.php';
// use Carbon\Carbon;
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();

$iniKeys = (getDataIni(__DIR__ . '../../../../../../mobileApikey.php'));
borrarLogs(__DIR__ . '../../_logs/getUsers/', 30, '.log');
$total = 0;
$params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    (response(array(), 0, 'Invalid Request Method', 400, 0, 0, 0));
    exit;
}

$iniScript = microtime(true);
$idCompany = 0;

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
function status()
{
    $p = $_REQUEST;
    $p['status'] = $p['status'] ?? '0';
    $status = empty($p['status']) ? 0 : ($p['status']);
    return intval($status);
}
function validaKey()
{
    $p = $_REQUEST['key'];
    $validaKey = empty($p) ? '' : $p;
    return ($validaKey);
}
if (!isset($params['key'])) {
    http_response_code(400);
    (response(array(), 0, 'The Key is required', 400, 0, 0, $idCompany));
}
$textParams = '';

foreach ($params as $key => $value) {
    if ($key == 'key' || $key == 'start' || $key == 'length' || $key == 'checks' || $key == 'userID' || $key == 'userName' || $key == 'status' || $key == 'userIDName') {
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
        require_once __DIR__ . '../../../../../../control/PhpUserAgent/src/UserAgentParser.php';
        $parsedagent[] = parse_user_agent($agent);
        foreach ($parsedagent as $key => $value) {
            $platform = $value['platform'];
            $browser  = $value['browser'];
            $version  = $value['version'];
        }
        $agent = $platform . ' ' . $browser . ' ' . $version;
    }
    $pathLog  = __DIR__ . '../../_logs/getUsers/' . date('Ymd') . '_getUsers_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
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

$validaKey = validaKey();
$vkey = '';
foreach ($iniKeys as $key => $value) {
    if ($value['recidCompany'] == $validaKey) {
        $idCompany = $value['idCompany'];
        $vkey      = $value['recidCompany'];
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

$status = status();
($status > 1) ? (response(array(), 0, 'Parameter status invalid', 400, 0, 0, $idCompany)) . exit  : '';

$MESSAGE = 'OK';
$arrayData = array();

$sql_query = "SELECT 
ru.id_user AS 'id_user', 
ru.nombre AS 'nombre', 
ru.regid AS 'regid', 
ru.fechahora AS 'fechaHora', 
ru.expiredStart AS 'expiredStart', 
ru.expiredEnd AS 'expiredEnd',
ru.estado AS 'estado',
ru.motivo AS 'motivo',
ru.hasArea AS 'hasArea',
(SELECT COUNT(1) FROM reg_ r WHERE r.id_user = ru.id_user AND r.eventType=2 AND r.id_company = '$idCompany') AS 'cant', 
re.idPunchEvent AS 'idPunchEvent'
FROM reg_user_ ru 
LEFT JOIN reg_enroll re ON ru.id_user = re.id_user
WHERE ru.uid > 0";
$filtro_query = '';
$filtro_query .= ($idCompany) ? " AND ru.id_company = $idCompany" : '';
$filtro_query .= (!empty($status)) ? " AND ru.estado = '$status'" : "";
$filtro_query .= (!empty($userID)) ? " AND ru.id_user = $userID" : '';
$filtro_query .= (!empty($userName)) ? " AND ru.nombre LIKE '%$userName%'" : '';
$filtro_query .= (!empty($userIDName)) ? " AND CONCAT(ru.nombre, ru.id_user) LIKE '%$userIDName%'" : '';
$sql_query .= $filtro_query;
$sql_query .= " GROUP BY ru.id_user ORDER BY ru.nombre ASC";
$sql_query .= " LIMIT $start, $length";

// print_r($sql_query);exit;
$queryRecords = array_pdoQuery($sql_query);
if (($queryRecords)) {
    foreach ($queryRecords as $r) {
        // $Fecha = FechaFormatVar($r['fechaHora'], 'Y-m-d');

        if ($r['expiredEnd'] != '0000-00-00') {

            if (intval(FechaString($r['expiredEnd'])) < intval(date('Ymd'))) { // si la fecha de expired date es menor a la fecha actual. enviamos a la api la actualización de los datos y actualizamos el usuario local
                $user[] = array(
                    'idUser'           => $r['id_user'],
                    'name'             => $r['nombre'],
                    'status'           => 'OK',
                    'comments'         => '',
                    'locked'           => false,
                    'expiredDateStart' => null,
                    'expiredDateEnd'   => null
                );
                $loked =  ($r['estado'] == '0') ? false : true;
                $body = [
                    array(
                        "id"               => intval($idCompany),
                        "recid"            => "$vkey",
                        "name"            => "$nameCompany",
                        "locked"           => $loked,
                        "expiredDateStart" => null,
                        "expiredDateEnd"   => null,
                        "users"            => $user
                    )
                ];
                $payload = json_encode($body);
                $sendApi = sendApiMobileHRP($payload, $urlAppMobile, 'attention/api/access/update-users', $idCompany);
                $responseApi = (json_decode($sendApi));

                if ($responseApi->type == 'success') {
                    if (pdoQuery("UPDATE reg_user_ SET expiredStart = '0000-00-00', expiredEnd = '0000-00-00', motivo = '' WHERE id_user = $r[id_user] AND id_Company = $idCompany")) {
                        $r['expiredEnd'] = '0000-00-00';
                        $r['expiredStart'] = '0000-00-00';
                        $r['motivo'] = '';
                        $pathLog  = __DIR__ . '../../_logs/getUsers/' . date('Ymd') . '_getUsers_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
                        fileLog("Se actualizo usuario ($r[id_user]) $r[nombre]. Company = $idCompany", $pathLog); // Log Api
                    }
                }
            }
        }

        $arrayData[] = array(
            'lastUpdate'   => ($r['fechaHora']),
            'userID'       => intval($r['id_user']),
            'userName'     => $r['nombre'],
            'userRegId'    => $r['regid'],
            'userChecks'   => intval($r['cant']),
            'hasArea'   =>  ($r['hasArea']),
            'expiredStart' => ($r['expiredStart']),
            'expiredEnd'   => ($r['expiredEnd']),
            'locked'       => ($r['estado']),
            'motivo'       => ($r['motivo']),
            'trained' => ($r['idPunchEvent']) ? true : false
        );
    }
    $q = "SELECT COUNT(*) AS 'count' FROM reg_user_ ru WHERE ru.uid > 0";
    $q .= $filtro_query;
    $total = simple_pdoQuery($q)['count'];
}

$finScript    = microtime(true);
$tiempoScript = round($finScript - $iniScript, 2);
$countData    = count($arrayData);
(response($arrayData, intval($total), 'OK', '', $tiempoScript, $countData, $idCompany));
exit;
