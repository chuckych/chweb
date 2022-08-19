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
$_SESSION["APIMOBILEHRP"] = $_SESSION["APIMOBILEHRP"] ?? '';
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
FusNuloPOST('typeDownload', '');

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
$url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);

$totalRecords = $api['TOTAL'];
$tm = (microtime(true));
$routeFile = __DIR__ . '/archivos/export_' . $idCompany . '_' . $tm . '.txt';
$routeFileXls = __DIR__ . '/archivos/export_' . $idCompany . '_' . $tm . '.xls';
$routeFile2 = 'archivos/export_' . $idCompany . '_' . $tm . '.txt';
$routeFile3 = 'archivos/export_' . $idCompany . '_' . $tm . '.xls';
$startScript = microtime(true);
borrarLogs($routeFile, 1, '.txt');
borrarLogs($routeFile, 1, '.xls');
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
        $pathPhoto = "$r[img]";
        $img = $r['img'];
        $arrayData[] = array(
            'appVersion'        => $r['appVersion'],
            'attPhoto'          => $r['attPhoto'],
            'createdDate'       => $r['createdDate'],
            'deviceName'        => $r['deviceName'],
            'eventType'         => $r['eventType'],
            'gpsStatus'         => $r['gpsStatus'],
            'operation'         => $r['operation'],
            'operationType'     => $r['operationType'],
            'phoneid'           => $r['phoneid'],
            'regDate'           => FechaFormatVar($r['regDate'], 'd/m/Y'),
            'regDateTime'       => $r['regDateTime'],
            'regDay'            => $r['regDay'],
            'regUID'            => ($r['regUID']),
            'regLat'            => $r['regLat'],
            'regLng'            => $r['regLng'],
            // 'regPhoto'          => (is_file('fotos/'.$r['userCompany'].'/' . $r['regPhoto'])) ? $r['regPhoto'] : '',
            'regPhoto'          => (is_file($img)) ? $img : '',
            // 'regPhoto'       => (is_file($pathPhoto)) ? $pathPhoto : '',
            // 'regPhoto'       => $pathPhoto,
            'pathPhoto'         => $pathPhoto,
            'regHora'           => $r['regTime'],
            'regTime'           => $hora,
            'userCompany'       => $r['userCompany'],
            'userID'            => $r['userID'],
            'userName'          => $r['userName'],
            'phoneRegId'        => $r['phoneRegID'],
            'zoneID'            => $r['zoneID'],
            'zoneName'          => $r['zoneName'],
            'zoneDistance'      => round(floatval($r['zoneDistance']) * 1000, 2),
            'locked'            => $r['locked'],
            'error'             => $r['error'],
            'confidenceFaceStr' => $r['confidenceFaceStr'] ?? ($r['confidenceFaceVal']),
            'confidenceFaceVal' => $r['confidenceFaceVal'],
            'id_api'            => $r['id_api'],
            // 'img'               => $img,
            'img'               => $img,
            // 'img'               => $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" .$img,
            'imageData' => $r['imageData'],
        );
        if ($params['typeDownload'] ?? '' == 'downloadTxt') { //downloadTxt
            $txtData = array(
                'userID'            => (padLeft($r['userID'], 11, '0')),
                'userName'          => trim($r['userName']),
                'zoneID'            => $r['zoneID'],
                'zoneName'          => trim($r['zoneName']),
                'zoneDistance'      => round(floatval($r['zoneDistance']) * 1000, 2),
                'locked'            => $r['locked'],
                'confidenceFaceStr' => $r['confidenceFaceStr'] ?? ($r['confidenceFaceVal']),
                'regDateTime'       => FechaFormatVar($r['regDateTime'], 'Y-m-d H:i'),
            );
            if ($txtData['userName']) {
                $line = "$txtData[userID],$txtData[regDateTime],$txtData[userName]";
                fileLog($line, $routeFile, 'export');
            }
        }
        if ($params['typeDownload'] ?? '' == 'downloadXls') { //xls
            if ($r['userName']) {
                $xlsData[] = array(
                    'userID'            => (($r['userID'])),
                    'userName'          => trim($r['userName']),
                    'zoneID'            => $r['zoneID'],
                    'regDay'            => $r['regDay'],
                    'regHora'           => $r['regTime'],
                    'regDate'           => FechaFormatVar($r['regDateTime'], 'Y-m-d'),
                    'zoneName'          => trim($r['zoneName']),
                    'zoneDistance'      => round(floatval($r['zoneDistance']) * 1000, 2),
                    'locked'            => $r['locked'],
                    'confidenceFaceStr' => $r['confidenceFaceStr'] ?? ($r['confidenceFaceVal']),
                    'regDateTime'       => $r['regDateTime'],
                    'regLat'            => $r['regLat'],
                    'regLng'            => $r['regLng'],
                    'device'            => ($r['deviceName']),
                    'phoneid'            => ($r['phoneid']),
                    'operationType'     => $r['operationType'],
                    'timestamp'         => $r['createdDate'],
                );
            }
        }
    }
}
if ($params['typeDownload'] ?? '' == 'downloadXls') {
    require __DIR__ . './exportXls.php';
}
if ($api['COUNT'] == 0) {
    if ($params['typeDownload'] ?? '' == 'downloadTxt') {
        $routeFile2 = '';
    }
    if ($params['typeDownload'] ?? '' == 'downloadXls') {
        $routeFile3 = '';
    }
}

switch ($params['typeDownload'] ?? '') {
    case 'downloadTxt':
        $arrayData = $routeFile2;
        break;
    case 'downloadXls':
        $arrayData = $routeFile3;
        break;
    default:
        $arrayData = $arrayData;
        break;
}
$endScript = microtime(true);
$timeScript = round($endScript - $startScript, 2);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $arrayData,
    "e"               => $error,
    "timeScript"      => $timeScript,
);
// sleep(2);
echo json_encode($json_data);
exit;
