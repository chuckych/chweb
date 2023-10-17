<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();
$respuesta = array();
$arrayData = array();
$arraySelect = array();
$error = $start_date = $end_date = '';
$_SESSION["APIMOBILEHRP"] = $_SESSION["APIMOBILEHRP"] ?? '';
function dr_f($ddmmyyyy)
{
    $fecha = date("Ymd", strtotime((str_replace("/", "-", $ddmmyyyy))));
    return $fecha;
}

$DateRange = explode(' al ', $_POST['_drMob2']);
$FechaIni = test_input(dr_f($DateRange[0]));
$FechaIni = fechformat2($FechaIni) . ' 00:00:00';
$FechaFin = test_input(dr_f($DateRange[1]));
$FechaFin = fechformat2($FechaFin) . ' 23:59:59';

$params = $columns = $totalRecords = '';
$params = (Flight::request()->data);

$params['type'] = $params['type'] ?? '';
$params['q'] = $params['q'] ?? '';
$params['qUser'] = $params['qUser'] ?? '';
$params['qZone'] = $params['qZone'] ?? '';
$params['users'] = $params['users'] ?? '';
$params['zones'] = $params['zones'] ?? '';
$params['device'] = $params['device'] ?? '';
$params['identified'] = $params['identified'] ?? '';
// $params['data_array'] = $params['data_array'] ?? '';

FusNuloPOST('SoloFic', '');
FusNuloPOST('typeDownload', '');

$arrayUsers[] = array();
$idCompany = $_SESSION['ID_CLIENTE'];

if ($params['users'] && $params['type'] != 'selectUsers') {
    $users = implode(',', $params['users']);
}
if ($params['zones'] && $params['type'] != 'selectZone') {
    $zones = implode(',', $params['zones']);
}

if ($params['device'] && $params['type'] != 'selectDevice') {
    $device = implode(',', $params['device']);
}

if ($params['type'] == 'selectUsers') {
    $groupBy = 'user';
}

if ($params['type'] == 'selectZone') {
    $groupBy = 'zone';
}

if ($params['type'] == 'selectDevice') {
    $groupBy = 'device';
}

$paramsApi = array(
    'key'        => $_SESSION["RECID_CLIENTE"],
    'start'      => urlencode($params['start']),
    'length'     => urlencode($params['length']),
    'checks'     => urlencode($_POST['SoloFic']),
    'startDate'  => test_input(dr_f($DateRange[0])),
    'endDate'    => test_input(dr_f($DateRange[1])),
    'users'      => $users ?? '',
    'zones'      => $zones ?? '',
    'devices'      => $device ?? '',
    'groupBy'    => $groupBy ?? '',
    'identified' => $params['identified'] ?? '',
    'userIDName' => urlencode($params['search']['value'] ?? $params['qUser']),
    'zoneIDName' => urlencode($params['qZone']),
    'deviceIDName' => urlencode($params['qDevice'])
);

// echo Flight::json($paramsApi).exit;

$parametros = '';
foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}
$api = "api/v1/checks/$parametros";
$url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);

// echo Flight::json($parametros).exit;

$totalRecords = $api['TOTAL'] ?? 0;
$tm = (microtime(true));
$routeFile = __DIR__ . '/archivos/export_' . $idCompany . '_' . $tm . '.txt';
$routeFileXls = __DIR__ . '/archivos/export_' . $idCompany . '_' . $tm . '.xls';
$routeFile2 = 'archivos/export_' . $idCompany . '_' . $tm . '.txt';
$routeFile3 = 'archivos/export_' . $idCompany . '_' . $tm . '.xls';
$startScript = microtime(true);
borrarLogs($routeFile, 1, '.txt');
borrarLogs($routeFile, 1, '.xls');

if ($api['COUNT'] ?? 0 > 0) {
    foreach ($api['RESPONSE_DATA'] as $r) {

        $jsonMarcador = json_encode(
            array(
                'name' => $r['userName'],
                'lat' => $r['regLat'],
                'lng' => $r['regLng'],
                'regDate' => FechaFormatVar($r['regDate'], 'd/m/Y'),
                'regDay' => $r['regDay'],
                'regHora' => $r['regTime'],
                // 'map_size' => $r['map_size'],
            )
        );

        $zoneDistance = round(floatval($r['zoneDistance']) * 1000, 2);

        $hora = "<span class='marcador' marcador='$jsonMarcador'>$r[regTime]</span>";
        if ($params['type'] == 'selectUsers') {
            $name = ($r['userName'] == '') ? 'Usuario Inválido' : $r['userName'];
            if ($r['userName']) {
                $arraySelect[] = array(
                    'id' => $r['userID'],
                    'text' => $name,
                    'html' => '
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex flex-column">
                            <span>' . $name . '</span>
                            <span class="fontp">ID: ' . $r['userID'] . '</span>
                        </div>
                        <span class="badge badge-light p-1">' . $r['countGroup'] . '</span>
                    </div>
                    ',
                );
            }
        }
        if ($params['type'] == 'selectZone') {
            $zoneName = $r['zoneName'] ?? 'Fuera de Zona';
            if ($r['zoneName']) {
                $arraySelect[] = array(
                    'id' => $r['zoneID'],
                    'text' => $zoneName,
                    'html' => '
                    <div class="d-flex align-items-center justify-content-between">
                        <span>' . $zoneName . '</span>
                        <span class="badge badge-light p-1">' . $r['countGroup'] . '</span>
                    </div>
                    ',
                );
            }
        }
        if ($params['type'] == 'selectDevice') {
            if ($r['deviceName']) {
                $arraySelect[] = array(
                    'id' => $r['deviceID'],
                    'text' => $r['deviceName'],
                    'html' => '
                    <div class="d-flex align-items-center justify-content-between">
                        <span>' . $r['deviceName'] . '</span>
                        <span class="badge badge-light p-1">' . $r['countGroup'] . '</span>
                    </div>
                    ',
                );
            }
        }

        $arrayData[] = array(
            'appVersion'        => $r['appVersion'],
            'attPhoto'          => $r['attPhoto'],
            'createdDate'       => $r['createdDate'],
            'deviceName'        => $r['deviceName'],
            'deviceEvent'       => $r['deviceEvent'],
            'eventType'         => $r['eventType'],
            'eventZone'         => $r['eventZone'],
            'gpsStatus'         => $r['gpsStatus'],
            'operation'         => $r['operation'],
            'operationType'     => $r['operationType'],
            'phoneID'           => $r['phoneid'],
            'regDate'           => FechaFormatVar($r['regDate'], 'd/m/Y'),
            'regDateTime'       => $r['regDateTime'],
            'regDay'            => $r['regDay'],
            'regUID'            => ($r['regUID']),
            'regLat'            => $r['regLat'],
            'regLng'            => $r['regLng'],
            'regHora'           => $r['regTime'],
            'regTime'           => $hora,
            'userCompany'       => $r['userCompany'],
            'userID'            => $r['userID'],
            'userName'          => html_entity_decode($r['userName'], ENT_QUOTES, 'UTF-8'),
            'phoneRegId'        => $r['phoneRegID'],
            'zoneID'            => $r['zoneID'],
            'zoneName'          => $r['zoneName'],
            'zoneLat'           => $r['zoneLat'],
            'zoneLng'           => $r['zoneLng'],
            'zoneRadio'         => $r['zoneRadio'],
            'zoneDistance'      => $zoneDistance,
            'locked'            => $r['locked'],
            'error'             => $r['error'],
            'confidenceFaceStr' => $r['confidenceFaceStr'] ?? ($r['confidenceFaceVal']),
            'confidenceFaceVal' => $r['confidenceFaceVal'],
            'id_api'            => $r['id_api'],
            'imageData'         => $r['imageData'],
            'basePhoto'         => $r['basePhoto']
        );
        // print_r($arrayData).exit;
        if (($params['typeDownload'] ?? '') == 'downloadTxt') { //downloadTxt

            $txtData = array(
                'userID'            => (padLeft($r['userID'], 11, ' ')),
                'userName'          => html_entity_decode($r['userName'], ENT_QUOTES, 'UTF-8'),
                'zoneID'            => $r['zoneID'],
                'zoneName'          => ($r['zoneName']) ? trim($r['zoneName']) : 'Fuera de Zona',
                'zoneDistance'      => round(floatval($r['zoneDistance']) * 1000, 2),
                'locked'            => $r['locked'],
                'confidenceFaceStr' => $r['confidenceFaceStr'] ?? ($r['confidenceFaceVal']),
                'regDateTime'       => ($r['regDateTime']),
                'eventZone'         => ($r['eventZone']),
                'appVersion'        => $r['appVersion'],
            );
            // print_r($txtData) . exit;

            if ($txtData['userName']) {
                $zoneDistance = ($txtData['zoneDistance'] > 0) ? $txtData['zoneDistance'] . ' mts' : '0 mts';
                $line = "$txtData[userID],$txtData[regDateTime],$txtData[eventZone],$txtData[userName],$txtData[appVersion],$txtData[zoneName],$zoneDistance,$txtData[locked],$txtData[confidenceFaceStr]";
                fileLog($line, $routeFile, 'export');
            }
        }
        if (($params['typeDownload'] ?? '') == 'downloadXls') { //xls
            if ($r['userName']) {
                $xlsData[] = array(
                    'userID'            => $r['userID'],
                    'userName'          => trim(html_entity_decode($r['userName'], ENT_QUOTES, 'UTF-8')),
                    'zoneID'            => $r['zoneID'],
                    'regDay'            => $r['regDay'],
                    'regHora'           => $r['regTime'],
                    'regDate'           => FechaFormatVar($r['regDateTime'], 'Y-m-d'),
                    'zoneName'          => trim(html_entity_decode($r['zoneName'], ENT_QUOTES, 'UTF-8')),
                    'zoneDistance'      => round(floatval($r['zoneDistance']) * 1000, 2),
                    'locked'            => $r['locked'],
                    'confidenceFaceStr' => $r['confidenceFaceStr'] ?? ($r['confidenceFaceVal']),
                    'regDateTime'       => $r['regDateTime'],
                    'regLat'            => $r['regLat'],
                    'regLng'            => $r['regLng'],
                    'device'            => ($r['deviceName']),
                    'phoneid'           => ($r['phoneid']),
                    'operationType'     => $r['operationType'],
                    'timestamp'         => $r['createdDate'],
                    'eventZone'         => ($r['eventZone']),
                    'eventDevice'       => ($r['eventDevice']),
                    'appVersion'        => $r['appVersion'],
                );
            }
        }
    }
}

if (($params['typeDownload'] ?? '') == 'downloadXls') {
    require __DIR__ . './exportXls.php';
}

// print_r($api['COUNT'] ?? '') . exit;

if (($api['COUNT'] ?? '') == 0) {
    if (($params['typeDownload'] ?? '') == 'downloadTxt') {
        $routeFile2 = '';
    }
    if (($params['typeDownload'] ?? '') == 'downloadXls') {
        $routeFile3 = '';
    }
}



switch ($params['typeDownload'] ?? '') {
    case 'downloadTxt':
        $arrayData = $routeFile2;
        auditoria("Exportar TXT Fichadas Mobile HRP. Desde $start_date a $end_date", 'A', $idCompany, '32');
        break;
    case 'downloadXls':
        $arrayData = $routeFile3;
        auditoria("Exportar Excel Fichadas Mobile HRP. Desde $start_date a $end_date", 'A', $idCompany, '32');
        break;
}
$endScript = microtime(true);
$timeScript = round($endScript - $startScript, 2);
$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $arrayData,
    "e" => $error,
    "timeScript" => $timeScript,
);
// sleep(2);
if ($params['type'] == 'selectUsers' || $params['type'] == 'selectZone' || $params['type'] == 'selectDevice') {
    // echo json_encode(_group_by_keys($arraySelect, $keys = array('id', 'text')));
    echo json_encode($arraySelect);
    exit;
}

echo json_encode($json_data);
exit;
