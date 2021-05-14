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

// sleep(1); PrintRespuestaJson('ok', '<div class="animate__animated animate__fadeInDown">Se actualizaron registros.<br/>Total de registros nuevos: 10</div>');exit;
$query = "SELECT createdDate FROM reg_ ORDER BY createdDate DESC LIMIT 1";
$rs = mysqli_query($link, $query);
$createdDate = mysqli_fetch_assoc($rs);
$createdDate = (empty($createdDate['createdDate'])) ? '1620506140879' : $createdDate['createdDate'];
mysqli_free_result($rs);

// PrintRespuestaJson('error', $createdDate);exit;

$url   = "http://190.7.56.83/attention/api/punch-event/" . $createdDate;
$array = json_decode(getEvents($url), true);
foreach ($array['payload'] as $key => $v) {
    $arrayData[] = array(
        'phoneid'      => $v['phoneid'],
        'dateTime'     => ($v['dateTime']),
        'createdDate'  => $v['createdDate'],
        'lat'          => $v['position']['lat'],
        'lng'          => $v['position']['lng'],
        'accuracy'     => $v['position']['accuracy'],
        'gpsStatus'    => $v['position']['gpsStatus'],
        'batteryLevel' => $v['position']['batteryLevel'],
        'speed'        => $v['position']['speed'],
        'bearing'      => $v['position']['bearing'],
        'eventType'    => $v['eventType'],
        '__v'          => $v['__v'],
        'sync'         => $v['sync'],
        '_id'          => $v['_id'],
        'regid'        => $v['regid'],
        'appVersion'   => $v['appVersion'],
        'attphoto'     => $v['attphoto'],
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

        $phoneid      = $valor['phoneid'];
        $dateTime     = $valor['dateTime'];
        $dateTime     = substr($dateTime, 0, 10);
        $createdDate  = $valor['createdDate'];
        $lat          = $valor['lat'];
        $lng          = $valor['lng'];
        $accuracy     = $valor['accuracy'];
        $gpsStatus    = $valor['gpsStatus'];
        $batteryLevel = $valor['batteryLevel'];
        $speed        = $valor['speed'];
        $bearing      = $valor['bearing'];
        $eventType    = $valor['eventType'];
        $__v          = $valor['__v'];
        $sync         = $valor['sync'];
        $_id          = $valor['_id'];
        $regid        = $valor['regid'];
        $appVersion   = $valor['appVersion'];
        $attphoto     = $valor['attphoto'];


        $query = "INSERT INTO reg_ (phoneid,createdDate,fechaHora,lat,lng,gpsStatus,eventType,_id,regid,appVersion,attphoto) VALUES('$phoneid', '$createdDate', '$fechaHora', '$lat','$lng','$gpsStatus','$eventType', '$_id', '$regid', '$appVersion', '$attphoto')";
        (mysqli_query($link, $query));
    }
    PrintRespuestaJson('ok', 'Se actualizaron registros<br/>Cantidad de registros nuevos: '.count($arrayData));
    mysqli_close($link);
    exit;
}else{
    PrintRespuestaJson('no', 'No hay registros nuevos');
}
