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
borrarLogs(__DIR__ . '', 1, '.json');

$company = array();
$employe = array();
$arrayData = array();
$insertCH = array();
$insertCH_Fail = array();

$start = microtime(true);

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

require __DIR__ . '../../../config/conect_mysql.php';

$createdDate = simple_pdoQuery("SELECT createdDate FROM reg_ ORDER BY createdDate DESC LIMIT 1");
$createdDate = (empty($createdDate['createdDate'])) ? '1620506140879' : $createdDate['createdDate'];

$url   = "http://190.7.56.83/attention/api/punch-event/" . $createdDate;
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

    foreach ($arrayData as $key => $valor) {
        $timestamp = $valor['dateTime'];
        $timestamp = substr($timestamp, 0, 10);
        $dates     = new \DateTime();
        $dates     = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $dates->setTimestamp($timestamp);
        $fechaHora = $dates->format('Y-m-d H:i');
        $fechaHoraCH = $dates->format('Y-m-d');
        $hora = $dates->format('H:i');
        $__v           = $valor['__v'];
        $_id           = $valor['_id'];
        $accuracy      = $valor['accuracy'];
        $appVersion    = $valor['appVersion'];
        $attphoto      = $valor['attphoto'];
        $batteryLevel  = $valor['batteryLevel'];
        $bearing       = $valor['bearing'];
        $companyCode   = $valor['companyCode'];
        $createdDate   = $valor['createdDate'];
        $dateTime      = $valor['dateTime'];
        $dateTime      = substr($dateTime, 0, 10);
        $employeId     = $valor['employeId'];
        $eventType     = $valor['eventType'];
        $gpsStatus     = $valor['gpsStatus'];
        $lat           = $valor['lat'];
        $lng           = $valor['lng'];
        $phoneid       = $valor['phoneid'];
        $regid         = $valor['regid'];
        $speed         = $valor['speed'];
        $sync          = $valor['sync'];
        $operationType = $valor['operationType'];
        $operation     = $valor['operation'];
        $checkPhoto = ($attphoto) ? '0' : '1';
        /** Guardamos la foto del base64 */

        $company[]      = "$companyCode";
        if (($companyCode == $_SESSION['ID_CLIENTE'])) {
            $totalSession[] = ($companyCode);
        }
        $employe[]      = "$employeId";

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

        /** */
        $query = "INSERT INTO reg_ (phoneid,id_user, id_company,createdDate,fechaHora,lat,lng,gpsStatus,eventType,operationType, operation, _id,regid,appVersion, attphoto) VALUES('$phoneid', '$employeId', '$companyCode','$createdDate', '$fechaHora', '$lat','$lng','$gpsStatus','$eventType', '$operationType', '$operation','$_id', '$regid', '$appVersion', '$checkPhoto')";

        if ((pdoQuery($query))) { // Si se guarda correctamente insertanmos en la tabla fichadas de control horarios
            $query = "INSERT INTO FICHADAS (RegTarj, RegFech, RegHora, RegRelo, RegLect, RegEsta) VALUES ('$employeId', '$fechaHoraCH', '$hora', '9999', '9999', '0')";
            $Legajo = str_pad($employeId, 11, "0", STR_PAD_LEFT);
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
    // $pathLog = date('Ymd') . '_Transfer.json';
    // $dataJson = array(
    //     $data
    // );
    // fileLogJson((($dataJson)), $pathLog);
    exit;
} else {
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
