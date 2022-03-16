<?php
require __DIR__ . '../../../config/index.php';
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

borrarLogs(__DIR__ . '', 30, '.log');

function writeFlags($assoc, $path)
{
    $content = "";
    foreach ($assoc as $key => $elem) {
        $content .= "[" . $key . "]\n";
        foreach ($elem as $key2 => $elem2) {
            if (is_array($elem2)) {
                for ($i = 0; $i < count($elem2); $i++) {
                    $content .= $key2 . "[] =\"" . $elem2[$i] . "\"\n";
                }
            } else if ($elem2 == "") $content .= $key2 . " =\n";
            else $content .= $key2 . " = \"" . $elem2 . "\"\n";
        }
    }
    // $path = __DIR__ . '/flags.ini';
    if (!$handle = fopen($path, 'w')) {
        return false;
    }
    $success = fwrite($handle, $content);
    fclose($handle);
    return $success;
}
function statusFlags($statusFlags, $pathFlags, $createdDate){
    $assoc = array(
        'flags' => array(
            'lastDate' => $createdDate,
            'download' => $statusFlags,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );
    writeFlags($assoc, $pathFlags);
    $text = ($statusFlags == '2') ? "Se marco Bandera de espera": "Se marco Bandera de descarga";
    $pathLog = date('Ymd') . '_FlagsLog_.log';
    fileLog($text, $pathLog);
}
function getEvents($url, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        $pathLog = __DIR__ . '../../../logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }

    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '../../../logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
        return false;
    }
    exit;
}
function queryCalcZone($lat, $lng, $idCompany){
    $query = "
            SELECT
            `rg`.*,
        (
                (
                    (
                        acos(
                            sin(($lat * pi() / 180)) * sin((`rg`.`lat` * pi() / 180)) + cos(($lat * pi() / 180)) * cos((`rg`.`lat` * pi() / 180)) * cos((($lng - `rg`.`lng`) * pi() / 180))
                        )
                    ) * 180 / pi()
                ) * 60 * 1.1515 * 1.609344
            ) as distancia
        FROM
            reg_zones rg WHERE `rg`.`id_company` = $idCompany
        -- HAVING (distancia <= 0.1)
        ORDER BY distancia ASC, rg.id DESC LIMIT 1
    ";
    return $query;
}

$pathFlags = 'flags.ini'; // ruta del archivo de Log de errores
$flags = (getDataIni($pathFlags));

if (!$flags) {
    $assoc = array(
        'flags' => array(
            'lastDate' => '1646871812712',
            'download' => 1,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );
    writeFlags($assoc, $pathFlags);
    $flags = (getDataIni($pathFlags));
    $flags_lastDate = $flags['flags']['lastDate'];
    $flags_download = $flags['flags']['download'];
} else {
    // $flags = (getDataIni($pathFlags));
    $flags_lastDate = $flags['flags']['lastDate'];
    $flags_download = $flags['flags']['download'];
}
$company       = array();
$employe       = array();
$arrayData     = array();
$insertCH      = array();
$insertCH_Fail = array();
$totalSession  = array();

$start = microtime(true);

if($flags_download == 2){
    $data = array(
        'Mensaje' => 'Aguarde. Hay procesos de descarga en ejecucion.',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'no',
        'time'    => '',
        'total'   => 0
    );
    echo json_encode(array('Response' => $data));
    exit;
}
statusFlags(2, $pathFlags, $flags_lastDate); // marcar bandera de espera
$url   = "http://190.7.56.83/attention/api/punch-event/" . $flags_lastDate;
$array = json_decode(getEvents($url), true);
if (!empty($array['payload'])) {

    foreach ($array['payload'] as $key => $v) {
        $operation = $v['operation']['observations'] ?? '';
        $arrayData[] = array(
            '__v'           => $v['__v'],
            '_id'           => $v['_id'],
            'accuracy'      => $v['position']['accuracy'],
            'appVersion'    => $v['appVersion'],
            'attphoto'      => $v['attphoto'],
            'batteryLevel'  => $v['position']['batteryLevel'],
            'bearing'       => $v['position']['bearing'],
            'companyCode'   => $v['companyCode'],
            'createdDate'   => $v['createdDate'],
            'dateTime'      => ($v['dateTime']),
            'employeId'     => $v['employeId'],
            'eventType'     => $v['eventType'],
            'gpsStatus'     => $v['position']['gpsStatus'],
            'lat'           => $v['position']['lat'],
            'lng'           => $v['position']['lng'],
            'phoneid'       => $v['phoneid'],
            'regid'         => $v['regid'],
            'speed'         => $v['position']['speed'],
            'sync'          => $v['sync'],
            'operationType' => $v['operationType'],
            'operation'     => $operation,
        );
    }
}

if (!empty($arrayData)) {

    foreach ($arrayData as $key => $v) {
        $timestamp     = $v['dateTime'];
        $timestamp     = substr($timestamp, 0, 10);
        $dates         = new \DateTime();
        $dates         = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $dates->setTimestamp($timestamp);
        $fechaHora     = $dates->format('Y-m-d H:i');
        $fechaHoraCH   = $dates->format('Y-m-d');
        $hora          = $dates->format('H:i');
        $__v           = $v['__v'];
        $_id           = $v['_id'];
        $accuracy      = $v['accuracy'];
        $appVersion    = $v['appVersion'];
        $attphoto      = $v['attphoto'];
        $batteryLevel  = $v['batteryLevel'];
        $bearing       = $v['bearing'];
        $companyCode   = $v['companyCode'];
        $createdDate   = $v['createdDate'];
        $dateTime      = $v['dateTime'];
        $dateTime      = substr($dateTime, 0, 10);
        $employeId     = $v['employeId'];
        $eventType     = $v['eventType'];
        $gpsStatus     = $v['gpsStatus'];
        $lat           = $v['lat'];
        $lng           = $v['lng'];
        $phoneid       = $v['phoneid'];
        $regid         = $v['regid'];
        $speed         = $v['speed'];
        $sync          = $v['sync'];
        $operationType = $v['operationType'];
        $operation     = $v['operation'];
        $checkPhoto    = ($attphoto) ? '0' : '1';

        $arrayObj[] = array(
            'fechaHora'     => $fechaHora,
            'fechaHoraCH'   => $fechaHoraCH,
            'hora'          => $hora,
            'appVersion'    => $appVersion,
            'attphoto'      => $attphoto,
            'companyCode'   => $companyCode,
            'createdDate'   => $createdDate,
            'dateTime'      => $dateTime,
            'employeId'     => $employeId,
            'eventType'     => $eventType,
            'gpsStatus'     => $gpsStatus,
            'lat'           => $lat,
            'lng'           => $lng,
            'phoneid'       => $phoneid,
            'regid'         => $regid,
            'operationType' => $operationType,
            'operation'     => $operation,
            'checkPhoto'    => $checkPhoto,
        );
    }

    (array_multisort(array_column($arrayObj, 'createdDate'), SORT_DESC, $arrayObj));
    $first_element = reset($arrayObj);

    $assoc = array(
        'flags' => array(
            'lastDate' => $first_element['createdDate'],
            'fechHora' => $first_element['fechaHora'],
            'download' => 2,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );

    $arrGroup = (_group_by_keys($arrayObj, array('employeId', 'fechaHora', 'phoneid')));

    foreach ($arrGroup as $key => $valor) {

        $fechaHora     = $valor['fechaHora'];
        $fechaHoraCH   = $valor['fechaHoraCH'];
        $hora          = $valor['hora'];
        $appVersion    = $valor['appVersion'];
        $attphoto      = $valor['attphoto'];
        $companyCode   = $valor['companyCode'];
        $createdDate   = $valor['createdDate'];
        $dateTime      = $valor['dateTime'];
        $employeId     = $valor['employeId'];
        $eventType     = $valor['eventType'];
        $gpsStatus     = $valor['gpsStatus'];
        $lat           = $valor['lat'];
        $lng           = $valor['lng'];
        $phoneid       = $valor['phoneid'];
        $regid         = $valor['regid'];
        $operationType = $valor['operationType'];
        $operation     = $valor['operation'];
        $checkPhoto    = $valor['checkPhoto'];

        $company[]      = "$companyCode";
        if (($companyCode == $_SESSION['ID_CLIENTE'])) {
            $totalSession[] = ($companyCode);
        }
        $employe[]      = "$employeId";

        /** Guardamos la foto del base64 */
        if ($eventType == '2') {
            $filename = 'fotos/' . $companyCode . '/index.php';
            $dirname = dirname($filename);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755, true);
            }
            $f = fopen('fotos/' . $companyCode . '/' . $createdDate . '_' . $phoneid . '.png', "w") or die("Unable to open file!");
            fwrite($f, base64_decode($attphoto));
            fclose($f);
        }

        /** Calculamos la Zona */
        $query       = queryCalcZone($lat, $lng, $companyCode);
        $zona        = simple_pdoQuery($query);
        if ($zona) {
            $radio       = round(intval($zona['radio'])/1000, 2);
            $distancia = ($zona['distancia']) ? round($zona['distancia'], 2) : 0;
            $distancia2 = ($zona['distancia']) ? ($zona['distancia']) : 0;
            $idZone = ($distancia <= $radio) ? $zona['id'] : '0';
        }else{
            $idZone = '0';
        }
        /** Fin calculo Zona */

        $query = "INSERT INTO reg_ (reg_uid, id_user, phoneid, id_company,createdDate,fechaHora,lat,lng, idZone, distance, gpsStatus,eventType,operationType, operation, _id,regid,appVersion, attphoto) VALUES(UUID(),'$employeId', '$phoneid', '$companyCode','$createdDate', '$fechaHora', '$lat','$lng', '$idZone', '$distancia2','$gpsStatus','$eventType', '$operationType', '$operation','$_id', '$regid', '$appVersion', '$checkPhoto')";

        if ((pdoQuery($query))) { // Si se guarda correctamente insertanmos en la tabla fichadas de control horarios
            $Legajo = str_pad($employeId, 11, "0", STR_PAD_LEFT);
            /** Guardo Log de las Fichadas descargadas */
            $text = "$Legajo $createdDate $fechaHora $lat $lng";
            $pathLog = date('Ymd') . '_DescargasAPI_' . $companyCode . '.log'; // ruta del archivo de Log de errores
            fileLog($text, $pathLog); // escribir en el log de Fichadas insertadas en control horario
            /**  */

            $query = "INSERT INTO FICHADAS (RegTarj, RegFech, RegHora, RegRelo, RegLect, RegEsta) VALUES ('$employeId', '$fechaHoraCH', '$hora', '9999', '9999', '0')";

            if (InsertRegistroCH($query)) {
                $text = "$Legajo $fechaHoraCH $hora 9999 9999 0";
                $pathLog = date('Ymd') . '_FichadasCH_' . $companyCode . '.log'; // ruta del archivo de Log de errores
                $insertCH[] = array(
                    'Estado' => '0',
                    'Fecha'  => $fechaHoraCH,
                    'Hora'   => $hora,
                    'Lector' => '9999',
                    'Legajo' => $Legajo,
                    'Reloj'  => '9999',
                );
                fileLog($text, $pathLog); // escribir en el log de Fichadas insertadas en control horario
            } else {
                $text = "No se pudo insertar el registro en TABLA FICHADAS CH: $Legajo $fechaHoraCH $hora 9999 9999 0";
                $pathLog = date('Ymd') . '_ErrorInsertCH.log'; // ruta del archivo de Log de errores
                fileLog($text, $pathLog); // escribir en el log de errores el error
                $insertCH_Fail[] = array(
                    'Estado' => '0',
                    'Fecha'  => $fechaHoraCH,
                    'Hora'   => $hora,
                    'Lector' => '9999',
                    'Legajo' => $Legajo,
                    'Reloj'  => '9999',
                );
            }
        } else {
            $text = 'No se pudo insertar el registro ' . $employeId . ' ' . $fechaHora;
            $pathLog = date('Ymd') . '_logActualizar.log'; // ruta del archivo de Log de errores
            fileLog($text, $pathLog); // escribir en el log de errores el error
        }
    }
    $totalSession = array_count_values($totalSession);
    $end  = microtime(true);
    $time = round($end - $start, 2);
    header("Content-Type: application/json");
    $data = array(
        'Mensaje'      => 'Se actualizaron registros',
        'company'      => array_count_values($company),
        'date'         => date('Y-m-d H:i:s'),
        'employe'      => array_count_values($employe),
        'iCH_Fail'     => ($insertCH_Fail),
        'iCH_OK'       => ($insertCH),
        'status'       => 'ok',
        'time'         => ($time),
        'total'        => count($arrayData),
        'totalSession' => reset($totalSession),
        // 'data'      => $arrayData,
    );
    echo json_encode(array('Response' => $data));
    statusFlags(1, $pathFlags, $first_element['createdDate']); // marcar bandera de descarga
    exit;
} else {
    $flags = (getDataIni($pathFlags));
    $flags_lastDate = $flags['flags']['lastDate'];
    statusFlags(1, $pathFlags, $flags_lastDate); // marcar bandera de espera
    $end = microtime(true);
    $time = round($end - $start, 2);
    // header("Content-Type: application/json");
    $data = array(
        'Mensaje' => 'No hay registros nuevos',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'no',
        'time'    => ($time),
        'total'   => count($arrayData),
    );
    echo json_encode(array('Response' => $data));
    exit;
}
