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

FusNuloPOST('SoloFic', '');

$Filtros = ($_POST['SoloFic'] == '1') ? 'AND reg_.eventType=2' : '';

$sql_query = "SELECT reg_.createdDate as 'createdDate', reg_.id_user as 'id_user', reg_.phoneid as 'phoneid', reg_user_.nombre as 'nombre', reg_.fechaHora 'fechaHora', reg_.lat as 'lat', reg_.lng as 'lng', reg_.gpsStatus as 'gpsStatus', reg_.eventType as 'eventType', reg_.operationType as 'operationType', reg_.operation as 'operation', reg_.appVersion as 'appVersion', reg_.attphoto as 'attphoto', reg_.regid as 'regid' FROM reg_ LEFT JOIN reg_user_ ON reg_.id_user=reg_user_.id_user WHERE reg_.fechaHora BETWEEN '$FechaIni' AND '$FechaFin' $Filtros";

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
            'appVersion'    => $r['appVersion'],
            'attphoto'      => $r['attphoto'],
            'createdDate'   => $r['createdDate'],
            'dateTime'      => $r['dateTime'],
            'eventType'     => $r['eventType'],
            'fechaHora'     => $r['fechaHora'],
            'gpsStatus'     => $r['gpsStatus'],
            'id_user'       => $r['id_user'],
            'lat'           => $r['lat'],
            'lng'           => $r['lng'],
            'nombre'        => $r['nombre'],
            'operation'     => $r['operation'],
            'operationType' => $r['operationType'],
            'phoneid'       => $r['phoneid'],
            'regid'         => $r['regid'],
        );
    }
}

// print_r(json_encode($arrayData)); exit;
// $imgfoto = ($valor['face_url']) ? "https://server.xenio.uy/" . $valor['face_url'] : '../img/user.png';

foreach ($arrayData as $key => $valor) {
    $dia  = Fecha_String($valor['fechaHora']);
    $dia2 = fechformat($valor['fechaHora']);
    $time = HoraFormat($valor['fechaHora'], false);
    $time_second = HoraFormat($valor['fechaHora'], true);
    $LinkMapa        = "https://www.google.com/maps/place/" . $valor['lat'] . "," . $valor['lng'];
    $iconMapa        = ($valor['lat'] != '0') ? '<a href="' . $LinkMapa . '" target="_blank" rel="noopener noreferrer" data-titlel="Ver Mapa"><i class="bi bi-geo-alt-fill btn btn-sm btn-outline-custom border-0 fontt"></i></a>' : '<i data-titlel="Sin datos GPS" class="bi bi-x-lg btn btn-sm btn-outline-custom border-0"></i>';
    // $iconMapa        = ($valor['lat'] != '0') ? '<a href="' . $LinkMapa . '" target="_blank" rel="noopener noreferrer">' . imgIcon('marker', 'Ver Mapa', 'w20') . '</a>' : imgIcon('nomarker', 'Sin GPS', 'w20');
    $gps             = ($valor['gpsStatus'] != '0') ? 'Ok' : 'Sin GPS';

    $valor['operationType'] = ($valor['operationType'] == '0') ? '' : '/' . $valor['operationType'];
    $valor['operation']     = ($valor['operation'] == '0') ? '' : '/' . $valor['operation'];

    $evento = $valor['eventType'] . $valor['operationType'] . $valor['operation'];

    if ($valor['eventType'] == '2') {
        $imgfoto = "fotos/" . $valor['createdDate'] . '_' . $valor['phoneid'] . '.png';
        $foto = '<span class="pic" datafoto="' . $imgfoto . '" data-iduser="' . $valor['id_user'] . '" dataname="' . $valor['nombre'] . '" datauid="' . $valor['phoneid'] . '" datacerteza="" datacerteza2="" datainout="" datazone="" datahora="' . $time . '" datadia="' . DiaSemana4($dia) . '" datagps="' . $gps . '" datatype="' . ($evento) . '" datalat="' . ($valor['lat']) . '" datalng="' . ($valor['lng']) . '" >' . Foto($imgfoto, '', "shadow-sm w40 h40 scale radius img-fluid pointer") . '</span>';
    } else {
        $imgfoto = '/' . HOMEHOST . '/img/tarea.png?v='.vjs();
        $imgfoto2 = '/' . HOMEHOST . '/img/tarea2.png?v='.vjs();
        $foto = '<span class="pic" data-iduser="' . $valor['id_user'] . '" dataname="' . $valor['nombre'] . '" datauid="' . $valor['phoneid'] . '" datacerteza="" datacerteza2="" datainout="" datazone="" datahora="' . $time . '" datadia="' . DiaSemana4($dia) . '" datagps="' . $gps . '" datatype="' . ($evento) . '" datalat="' . ($valor['lat']) . '" datalng="' . ($valor['lng']) . '" ><i class="bi bi-check2-square pointer fontt text-secondary"></i></span>';
    }
    //$imgfoto = '<img loading="lazy" src= "data:image/png;base64,' . ($valor['attphoto']) . '" class="shadow-sm w40 h40 scale radius img-fluid pointer"/>';

if ($evento=='2') {
    $sendCH ='<div data-titlel="Transferir a CH"><button data-legFech="' . $valor['id_user'] . '@' . $dia . '@' . $time . '" class="sendCH btn btn-sm btn-outline-custom border-0 pointer"><i class="bi bi-forward fontt"></i></button></div>';
}else{
    $sendCH = '<div><button disabled class="btn btn-sm btn-outline-custom border-0"><i class="bi bi-forward fontt"></i></button></div>';
}

    $respuesta[] = array(
        'Fecha'       => '<div>' . DiaSemana3($dia) . '</div>',
        'Fecha2'      => '<div>' . $dia2 . '</div>',
        'Fecha4'      => '<div>' . DiaSemana4($dia) . '</div>',
        'createdDate' => '<div>' . $valor['createdDate'] . '</div>',
        'eventType'   => '<div>'.$evento.'</div>',
        'face_url'    => '<div class="w40">' . $foto . '</div>',
        'gps'         => '<div>' . $gps . '</div>',
        'id_user'     => '<div>' . $valor['id_user'] . '</div>',
        'mapa'        => '<div>' . $iconMapa . '</div>',
        'name'        => '<div>' . $valor['nombre'] . '</div>',
        'phoneid'     => '<div>' . $valor['phoneid'],
        'time'        => '<div data-titler="' . $time_second . '">' . $time . '</div>',
        'uid'         => '<div>' . $valor['phoneid'] . '</div>',
        'sendch'         => $sendCH,
        'regid'         => '<div data-titlel="Copiar Reg ID"><i data-clipboard-text="' . $valor['regid'] . '" class="copyRegig btn btn-sm btn-outline-custom border-0 pointer bi bi-clipboard"></i></div>',
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
