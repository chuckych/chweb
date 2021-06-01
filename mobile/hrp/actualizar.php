<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

// $foto = file_get_contents("https://server.xenio.uy/bucket_1/5c991ff84b5d89b23de9caa6/M_20891138_120520211425_3290.png");
// echo'<img src= "data:image/png;base64,' . base64_encode($foto) . '" />';
// exit;

function getEvents($url, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return ($file_contents) ? $file_contents : false;
    exit;
}

require __DIR__ . '../../../config/conect_mysql.php';

// sleep(6); 
// header("Content-Type: application/json");
// PrintRespuestaJson('ok', 'Se actualizaron registros');
// exit;


$query = "SELECT createdDate FROM reg_ ORDER BY createdDate DESC LIMIT 1";
$rs = mysqli_query($link, $query);
$createdDate = mysqli_fetch_assoc($rs);
$createdDate = (empty($createdDate['createdDate'])) ? '1620506140879' : $createdDate['createdDate'];
mysqli_free_result($rs);

// PrintRespuestaJson('error', $createdDate); exit;

$url   = "http://190.7.56.83/attention/api/punch-event/" . $createdDate;
$array = json_decode(getEvents($url), true);
foreach ($array['payload'] as $key => $v) {
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
        'operation'     => $v['operation']['observations'],
    );
}
// print_r(json_encode($arrayData));exit;
if (!empty($arrayData)) {
    foreach ($arrayData as $key => $valor) {
        $timestamp = $valor['dateTime'];
        $timestamp = substr($timestamp, 0, 10);
        $dates     = new \DateTime();
        $dates     = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $dates->setTimestamp($timestamp);
        $fechaHora = $dates->format('Y-m-d H:i:s');

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
        $operationType = $valor['operationType'] ?? '';
        $operation     = $valor['operation'] ?? '';

        /** Guardamos la foto del base64 */
        if ($eventType == '2') {
            $f = fopen('fotos/' . $createdDate . '_' . $phoneid . '.png', "w") or die("Unable to open file!");
            fwrite($f, base64_decode($attphoto));
            fclose($f);
        }

        /** */
        $query = "INSERT INTO reg_ (phoneid,id_user, id_company,createdDate,fechaHora,lat,lng,gpsStatus,eventType,operationType, operation, _id,regid,appVersion) VALUES('$phoneid', '$employeId', '$companyCode','$createdDate', '$fechaHora', '$lat','$lng','$gpsStatus','$eventType', '$operationType', '$operation','$_id', '$regid', '$appVersion')";
        (mysqli_query($link, $query));
    }
    header("Content-Type: application/json");
    PrintRespuestaJson('ok', 'Se actualizaron registros<br/>Cantidad de registros nuevos: ' . count($arrayData));
    mysqli_close($link);
    exit;
} else {
    header("Content-Type: application/json");
    PrintRespuestaJson('no', 'No hay registros nuevos');
}
