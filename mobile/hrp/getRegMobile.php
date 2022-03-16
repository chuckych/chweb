<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

$respuesta = array();
$arrayData = array();
$error = '';

function dr_f($ddmmyyyy)
{
    $fecha = date("Ymd", strtotime((str_replace("/", "-", $ddmmyyyy))));
    return $fecha;
}

$DateRange = explode(' al ', $_POST['_drMob2']);
$FechaIni  = test_input(dr_f($DateRange[0]));
$FechaIni  = fechformat2($FechaIni) . ' 00:00:00';
$FechaFin  = test_input(dr_f($DateRange[1]));
$FechaFin  = fechformat2($FechaFin) . ' 23:59:59';

$params = $columns = $totalRecords = '';
$params = ($_REQUEST);

FusNuloPOST('SoloFic', '');

$idCompany = $_SESSION['ID_CLIENTE'];

$paramsApi = array(
    'key'        => $_SESSION["RECID_CLIENTE"],
    'start'      => urlencode($params['start']),
    'length'     => urlencode($params['length']),
    'checks'     => urlencode($_POST['SoloFic']),
    'startDate'  => test_input(dr_f($DateRange[0])),
    'endDate'    => test_input(dr_f($DateRange[1])),
    'userIDName' => urlencode($params['search']['value']),
);
$parametros = '';
foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}
$api = "api/v1/checks/$parametros";
$url  = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);
$totalRecords = $api['TOTAL'];

if ($api['COUNT'] > 0) {
    foreach ($api['RESPONSE_DATA'] as $r) {

        $jsonMarcador = json_encode(array(
            'name'    => $r['userName'],
            'lat'     => $r['regLat'],
            'lng'     => $r['regLng'],
            'regDate' => FechaFormatVar($r['regDate'], 'd/m/Y'),
            'regDay'  => $r['regDay'],
            'regHora' => $r['regTime'],
            // 'map_size' => $r['map_size'],
        ));
        $hora = "<span class='marcador' marcador='$jsonMarcador'>$r[regTime]</span>";
        $pathPhoto = "$_SESSION[APIMOBILEHRP]/chweb/mobile/hrp/fotos/$r[userCompany]/$r[regPhoto]";
        $arrayData[] = array(
            'appVersion'    => $r['appVersion'],
            'attPhoto'      => $r['attPhoto'],
            'createdDate'   => $r['createdDate'],
            'deviceName'    => $r['deviceName'],
            'eventType'     => $r['eventType'],
            'gpsStatus'     => $r['gpsStatus'],
            'operation'     => $r['operation'],
            'operationType' => $r['operationType'],
            'phoneid'       => $r['phoneid'],
            'regDate'       => FechaFormatVar($r['regDate'], 'd/m/Y'),
            'regDateTime'   => $r['regDateTime'],
            'regDay'        => $r['regDay'],
            'regUID'        => ($r['regUID']),
            'regLat'        => $r['regLat'],
            'regLng'        => $r['regLng'],
            'regPhoto'      => (is_file('fotos/'.$r['userCompany'].'/' . $r['regPhoto'])) ? $r['regPhoto'] : '',
            // 'regPhoto'   => (is_file($pathPhoto)) ? $pathPhoto : '',
            // 'regPhoto'   => $pathPhoto,
            'pathPhoto'     => $pathPhoto,
            'regHora'       => $r['regTime'],
            'regTime'       => $hora,
            'userCompany'   => $r['userCompany'],
            'userID'        => $r['userID'],
            'userName'      => $r['userName'],
            'phoneRegId'    => $r['phoneRegID'],
            'zoneID'        => $r['zoneID'],
            'zoneName'      => $r['zoneName'],
            'zoneDistance'  => round(floatval($r['zoneDistance'])*1000,2)
        );
    }
}
// restore_error_handler();
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $arrayData,
    "e"           => $error,
);
// sleep(2);
echo json_encode($json_data);
exit;
