<?php
require __DIR__ . '../../../config/index.php';
session_start();
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header("Authorization: 7BB3A26C25687BCD56A9BAF353A78");
header('Access-Control-Allow-Origin: *');

E_ALL();

require __DIR__ . '../../../config/conect_mysql.php';

$respuesta = array();

$query = "SELECT reg_.createdDate as 'createdDate', reg_.phoneid as 'phoneid', reg_user_.nombre as 'nombre', reg_.fechaHora 'fechaHora', reg_.lat as 'lat', reg_.lng as 'lng', reg_.gpsStatus as 'gpsStatus', reg_.eventType as 'eventType', reg_.appVersion as 'appVersion', reg_.attphoto as 'attphoto' FROM reg_ LEFT JOIN reg_user_ ON reg_.phoneid=reg_user_.phoneid ORDER BY reg_.fechaHora DESC ";
$queryRecords = mysqli_query($link, $query);
while ($r = mysqli_fetch_assoc($queryRecords)) {
    $arrayData[] = array(
        'phoneid'     => $r['phoneid'],
        'nombre'      => $r['nombre'],
        'fechaHora'   => $r['fechaHora'],
        'dateTime'    => $r['dateTime'],
        'lat'         => $r['lat'],
        'lng'         => $r['lng'],
        'gpsStatus'   => $r['gpsStatus'],
        'eventType'   => $r['eventType'],
        'appVersion'  => $r['appVersion'],
        'attphoto'    => $r['attphoto'],
        'createdDate' => $r['createdDate'],
    );
}

// print_r(json_encode($arrayData)); exit;

if ((!empty($arrayData))) {
    foreach ($arrayData as $key => $valor) {
        $dia  = Fecha_String($valor['fechaHora']);
        $dia2 = fechformatM($valor['fechaHora']);
        $time = HoraFormat($valor['fechaHora']);
        $LinkMapa        = "https://www.google.com/maps/place/" . $valor['lat'] . "," . $valor['lng'];
        $iconMapa        = ($valor['lat'] != '0') ? '<a href="' . $LinkMapa . '" target="_blank" rel="noopener noreferrer">' . imgIcon('marker', 'Ver Mapa', 'w20') . '</a>' : imgIcon('nomarker', 'Sin GPS', 'w20');
        $gps             = ($valor['gpsStatus'] != '0') ? 'Ok' : 'Sin GPS';
        $imgfoto = '<img loading="lazy" src= "data:image/png;base64,' . ($valor['attphoto']) . '" class="shadow-sm w40 h40 scale radius img-fluid pointer"/>';

        $foto = '<span class="pic" datafoto="'.$valor['attphoto'].'" dataname="' . $valor['nombre'] . '" datauid="' . $valor['phoneid'] . '" datacerteza="" datacerteza2="" datainout="" datazone="" datahora="' . $time . '" datadia="' . DiaSemana4($dia) . '" datagps="' . $gps . '" datatype="' . ($valor['eventType']) . '" datalat="' . ($valor['lat']) . '" datalng="' . ($valor['lng']) . '" >' . ($imgfoto) . '</span>';

        $respuesta[] = array(
            'createdDate' => $valor['createdDate'],
            'Fecha2'      => $dia2,
            'Fecha'       => DiaSemana3($dia),
            'Fecha4'      => DiaSemana4($dia),
            'face_url'    => $foto,
            'mapa'        => $iconMapa,
            'phoneid'        => $valor['phoneid'],
            'eventType'      => $valor['eventType'],
            'uid'         => $valor['phoneid'],
            'name'        => $valor['nombre'],
            'time'        => $time,
            'gps'         => $gps
        );
    }
    $respuesta = array('mobile' => $respuesta);
    echo json_encode($respuesta);
} else {
    $respuesta = array('mobile' => '');
    echo json_encode($respuesta);
}
// }
