<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

require __DIR__ . '../../../config/conect_mysql.php';
// sleep(2);
$respuesta = array();

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

$params = $columns = $totalRecords;
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT reg_.createdDate as 'createdDate', reg_.phoneid as 'phoneid', reg_user_.nombre as 'nombre', reg_.fechaHora 'fechaHora', reg_.lat as 'lat', reg_.lng as 'lng', reg_.gpsStatus as 'gpsStatus', reg_.eventType as 'eventType', reg_.appVersion as 'appVersion', reg_.attphoto as 'attphoto' FROM reg_ LEFT JOIN reg_user_ ON reg_.phoneid=reg_user_.phoneid WHERE reg_.fechaHora BETWEEN '$FechaIni' AND '$FechaFin'";

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND ";
    $where_condition .= "reg_user_.nombre LIKE '%" . $params['search']['value'] . "%'";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .=  " ORDER BY reg_.fechaHora DESC LIMIT " . $params['start'] . " ," . $params['length'];
$queryTot = mysqli_query($link, $sqlTot);
$totalRecords = mysqli_num_rows($queryTot);
$queryRecords = mysqli_query($link, $sqlRec);

// print_r($sqlRec); exit;

if ($totalRecords > 0) {
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
}

// print_r(json_encode($arrayData)); exit;
$imgfoto = ($valor['face_url']) ? "https://server.xenio.uy/" . $valor['face_url'] : '../img/user.png';

foreach ($arrayData as $key => $valor) {
    $dia  = Fecha_String($valor['fechaHora']);
    $dia2 = fechformat($valor['fechaHora']);
    $time = HoraFormat($valor['fechaHora'], false);
    $time_second = HoraFormat($valor['fechaHora'], true);
    $LinkMapa        = "https://www.google.com/maps/place/" . $valor['lat'] . "," . $valor['lng'];
    $iconMapa        = ($valor['lat'] != '0') ? '<a href="' . $LinkMapa . '" target="_blank" rel="noopener noreferrer">' . imgIcon('markermaps', 'Ver Mapa', '') . '</a>' : imgIcon('nomarker', 'Sin GPS', 'w20');
    // $iconMapa        = ($valor['lat'] != '0') ? '<a href="' . $LinkMapa . '" target="_blank" rel="noopener noreferrer">' . imgIcon('marker', 'Ver Mapa', 'w20') . '</a>' : imgIcon('nomarker', 'Sin GPS', 'w20');
    $gps             = ($valor['gpsStatus'] != '0') ? 'Ok' : 'Sin GPS';

    $imgfoto = "fotos/" . $valor['createdDate'] . '_' . $valor['phoneid'] . '.png';
    //$imgfoto = '<img loading="lazy" src= "data:image/png;base64,' . ($valor['attphoto']) . '" class="shadow-sm w40 h40 scale radius img-fluid pointer"/>';

    $foto = '<span class="pic" datafoto="' . $imgfoto . '" dataname="' . $valor['nombre'] . '" datauid="' . $valor['phoneid'] . '" datacerteza="" datacerteza2="" datainout="" datazone="" datahora="' . $time . '" datadia="' . DiaSemana4($dia) . '" datagps="' . $gps . '" datatype="' . ($valor['eventType']) . '" datalat="' . ($valor['lat']) . '" datalng="' . ($valor['lng']) . '" >' . Foto($imgfoto, '', "shadow-sm w40 h40 scale radius img-fluid pointer") . '</span>';

    $respuesta[] = array(
        'createdDate' => '<div>' . $valor['createdDate'] . '</div>',
        'Fecha2'      => '<div>' . $dia2 . '</div>',
        'Fecha'       => '<div>' . DiaSemana3($dia) . '</div>',
        'Fecha4'      => '<div>' . DiaSemana4($dia) . '</div>',
        'face_url'    => '<div class="w40">' . $foto . '</div>',
        'mapa'        => '<div>' . $iconMapa . '</div>',
        'phoneid'     => '<div>' . $valor['phoneid'],
        'eventType'   => '<div>' . $valor['eventType'],
        'uid'         => '<div>' . $valor['phoneid'] . '</div>',
        'name'        => '<div>' . $valor['nombre'] . '</div>',
        'time'        => '<div data-titler="'.$time_second.'">' . $time . '</div>',
        'gps'         => '<div>' . $gps . '</div>'
    );
}
// $respuesta = array('mobile' => $respuesta);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $respuesta
);
// sleep(2);
echo json_encode($json_data);
