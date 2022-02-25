<?php
require __DIR__ . '../../../../../config/index.php';
require __DIR__ . '../../../../../vendor/autoload.php';

use Carbon\Carbon;


// echo $now;                               // 2021-10-07 14:01:58
// echo "<br>";
// $today = Carbon::today();
// echo $today;                             // 2021-10-07 00:00:00
// echo "<br>";
// $tomorrow = Carbon::tomorrow();
// echo $tomorrow;                          // 2021-10-08 00:00:00
// echo "<br>";
// $yesterday = Carbon::yesterday();
// echo $yesterday;                         // 2021-10-06 00:00:00

// exit;

header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
timeZone();
timeZone_lang();

function response($data, $msg = 'OK', $code = 200)
{
    $array = array(
        'RESPONSE_CODE' => http_response_code(intval($code)),
        'COUNT'         => count($data),
        'MESSAGE'       => $msg,
        'RESPONSE_DATA' => $data
    );
    echo json_encode($array, JSON_PRETTY_PRINT);
    exit;
}

// switch ($_SERVER['REQUEST_METHOD']) {
//     case 'GET':
//         $params = $_GET;
//         break;
//     case 'POST':
//         $params = $_POST;
//         break;
//     case 'PUT':
//         parse_str(file_get_contents('php://input'), $params);
//         break;
//     case 'DELETE':
//         parse_str(file_get_contents('php://input'), $params);
//         break;
// }

// auth token
$key = $_GET['key'] ?? false;

if (!$key) {
    http_response_code(400);
    (response(array(), 'The key is empty', 400));
}
$vkey = ($key == '12345') ? true : false;
$vkey .= ($key == '123485') ? true : false;

if (!$vkey) {
    http_response_code(400);
    (response(array(), 'Invalid Key', 400));
}

$MESSAGE = 'OK';
$arrayData = array();

$params = $columns = $totalRecords = '';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT reg_.createdDate as 'createdDate', reg_.id_user as 'id_user', reg_.phoneid as 'phoneid', reg_user_.nombre as 'nombre', reg_.fechaHora 'fechaHora', reg_.lat as 'lat', reg_.lng as 'lng', reg_.gpsStatus as 'gpsStatus', reg_.eventType as 'eventType', reg_.operationType as 'operationType', reg_.operation as 'operation', reg_.appVersion as 'appVersion', reg_.attphoto as 'attphoto', reg_.regid as 'regid' FROM reg_ LEFT JOIN reg_user_ ON reg_.id_user=reg_user_.id_user";

$sql_query .=  " ORDER BY reg_.fechaHora DESC LIMIT 10";

$queryRecords = array_pdoQuery($sql_query);
// print_r($sqlRec); exit;
$ahora      = Carbon::now();
$hoy        = Carbon::today();
$ayer       = Carbon::yesterday();
$fechaPers  = Carbon::createFromDate(2022, 2, 18);
$fechaPers2 = Carbon::createFromDate(2022, 2, 18, 'Europe/Madrid');

if (count($queryRecords) > 0) {
    foreach ($queryRecords as $r) {
        $Fecha = FechaFormatVar($r['fechaHora'], 'Y-m-d');
        $arrayData[] = array(
            'createdDate'    => intval($r['createdDate']),
            'eventType'      => $r['eventType'],
            'gpsStatus'      => $r['gpsStatus'],
            'operation'      => $r['operation'],
            'operation_Type' => $r['operationType'],
            'phone_Id'       => $r['phoneid'],
            'reg_Date'       => FechaFormatVar($r['fechaHora'], 'Y-m-d'),
            'reg_Day'        => DiaSemana3(FechaString($r['fechaHora'])),
            'reg_Foto'       => "$r[createdDate]_$r[phoneid].png",
            'reg_Lat'        => floatval($r['lat']),
            'reg_Lng'        => floatval($r['lng']),
            'reg_Time'       => (HoraFormat($r['fechaHora'], false)),
            'user_ID'        => intval($r['id_user']),
            'user_Name'      => $r['nombre'],
            'reg_DateTime'     => Carbon::createFromFormat('Y-m-d', $Fecha),
        );
    }
}
(response($arrayData));
exit;
