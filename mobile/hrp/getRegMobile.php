<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

$respuesta = array();
$arrayData = array();

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

$idCuenta = $_SESSION['ID_CLIENTE'];

$paramsApi = array(
    'key'        => sha1('mobileHRP'),
    'start'      => urlencode($params['start']),
    'length'     => urlencode($params['length']),
    'checks'     => urlencode($_POST['SoloFic']),
    'startDate'  => test_input(dr_f($DateRange[0])),
    'endDate'    => test_input(dr_f($DateRange[1])),
    'userIDName' => urlencode($params['search']['value']),
    'idCuenta'   => $idCuenta,
);
$parametros = '';
foreach ($paramsApi as $key => $value) {
    $parametros .= ($key == 'key') ? "?$key=$value" : "&$key=$value";
}
$api = "api/v1/checks/$parametros";
// echo $api; exit;
$url   = host() . "/" . HOMEHOST . "/mobile/hrp/" . $api;
$api = getRemoteFile($url, $timeout = 10);
$api = json_decode($api, true);

$totalRecords = $api['TOTAL'];

// print_r($totalRecords); exit;

if ($api['COUNT'] > 0) {
    foreach ($api['RESPONSE_DATA'] as $r) {
        $arrayData[] = array(
            'appVersion'    => $r['appVersion'],
            'attPhoto'      => $r['attPhoto'],
            'createdDate'   => $r['createdDate'],
            'eventType'     => $r['eventType'],
            'gpsStatus'     => $r['gpsStatus'],
            'operation'     => $r['operation'],
            'operationType' => $r['operationType'],
            'phoneid'       => $r['phoneid'],
            'regDate'       => FechaFormatVar($r['regDate'], 'd/m/Y'),
            'regDateTime'   => $r['regDateTime'],
            'regDay'        => $r['regDay'],
            'regPhoto'      => (is_file('fotos/'.$r['regPhoto'])) ? $r['regPhoto'] : '',
            'regLat'        => $r['regLat'],
            'regLng'        => $r['regLng'],
            'regTime'       => $r['regTime'],
            'userID'        => $r['userID'],
            'userName'      => $r['userName'],
            'userRegId'     => $r['userRegId'],
        );
    }
}
    // foreach ($arrayData as $key => $valor) {
    //     $dia  = Fecha_String($valor['fechaHora']);
    //     $dia2 = fechformat($valor['fechaHora']);
    //     $time = HoraFormat($valor['fechaHora'], false);
    //     $time_second = HoraFormat($valor['fechaHora'], true);
    //     $LinkMapa        = "https://www.google.com/maps/place/" . $valor['lat'] . "," . $valor['lng'];
    //     $iconMapa        = ($valor['lat'] != '0') ? '<a href="' . $LinkMapa . '" target="_blank" rel="noopener noreferrer" data-titlel="Ver Mapa"><i class="bi bi-geo-alt-fill btn btn-sm btn-outline-custom border-0 fontt"></i></a>' : '<i data-titlel="Sin datos GPS" class="bi bi-x-lg btn btn-sm btn-outline-custom border-0"></i>';

    //     $gps             = ($valor['gpsStatus'] != '0') ? 'Ok' : 'Sin GPS';

    //     $valor['operationType'] = ($valor['operationType'] <= '0') ? '' : '/' . $valor['operationType'];
    //     $valor['operation']     = ($valor['operation'] <= '0') ? '' : '/' . $valor['operation'];

    //     $evento = $valor['eventType'] . $valor['operationType'] . $valor['operation'];

    //     if ($valor['eventType'] == '2') {
    //         $imgfoto = "fotos/" . $valor['createdDate'] . '_' . $valor['phoneid'] . '.png';
    //         $foto = '<span class="pic" datafoto="' . $imgfoto . '" data-iduser="' . $valor['id_user'] . '" dataname="' . $valor['nombre'] . '" datauid="' . $valor['phoneid'] . '" datacerteza="" datacerteza2="" datainout="" datazone="" datahora="' . $time . '" datadia="' . DiaSemana4($dia) . '" datagps="' . $gps . '" datatype="' . ($evento) . '" datalat="' . ($valor['lat']) . '" datalng="' . ($valor['lng']) . '" >' . Foto($imgfoto, '', "shadow-sm w40 h40 scale radius img-fluid pointer") . '</span>';
    //     } else {
    //         $imgfoto = '/' . HOMEHOST . '/img/tarea.png?v=' . vjs();
    //         $imgfoto2 = '/' . HOMEHOST . '/img/tarea2.png?v=' . vjs();
    //         $foto = '<span class="pic" data-iduser="' . $valor['id_user'] . '" dataname="' . $valor['nombre'] . '" datauid="' . $valor['phoneid'] . '" datacerteza="" datacerteza2="" datainout="" datazone="" datahora="' . $time . '" datadia="' . DiaSemana4($dia) . '" datagps="' . $gps . '" datatype="' . ($evento) . '" datalat="' . ($valor['lat']) . '" datalng="' . ($valor['lng']) . '" ><i class="bi bi-check2-square pointer fontt text-secondary"></i></span>';
    //     }
    //     //$imgfoto = '<img loading="lazy" src= "data:image/png;base64,' . ($valor['attphoto']) . '" class="shadow-sm w40 h40 scale radius img-fluid pointer"/>';

    //     if ($evento == '2') {
    //         $datarecid = 'a' . recid() . 'a';
    //         $sendCH = '<div data-titlel="Transferir a CH"><button data-legFech="' . $valor['id_user'] . '@' . $dia . '@' . $time . '" data-recid="' . $datarecid . '"id="' . $datarecid . '" class="sendCH btn btn-sm btn-outline-custom border-0 pointer"><i class="bi bi-forward fontt"></i></button></div>';
    //     } else {
    //         $sendCH = '<div><button disabled class="btn btn-sm btn-outline-custom border-0"><i class="bi bi-forward fontt"></i></button></div>';
    //     }

    //     $respuesta[] = array(
    //         'Fecha'       => '<div>' . DiaSemana3($dia) . '</div>',
    //         'Fecha2'      => '<div>' . $dia2 . '</div>',
    //         'Fecha4'      => '<div>' . DiaSemana4($dia) . '</div>',
    //         'createdDate' => '<div>' . $valor['createdDate'] . '</div>',
    //         'eventType'   => '<div>' . $evento . '</div>',
    //         'face_url'    => '<div class="w40">' . $foto . '</div>',
    //         'gps'         => '<div>' . $gps . '</div>',
    //         'id_user'     => '<div>' . $valor['id_user'] . '</div>',
    //         'mapa'        => '<div>' . $iconMapa . '</div>',
    //         'name'        => '<div>' . $valor['nombre'] . '</div>',
    //         'phoneid'     => '<div>' . $valor['phoneid'],
    //         'time'        => '<div data-titler="' . $time_second . '">' . $time . '</div>',
    //         'uid'         => '<div>' . $valor['phoneid'] . '</div>',
    //         'sendch'         => $sendCH,
    //         'regid'         => '<div data-titlel="Copiar Reg ID"><i data-clipboard-text="' . $valor['regid'] . '" class="copyRegig btn btn-sm btn-outline-custom border-0 pointer bi bi-clipboard"></i></div>',
    //     );
    // }
// }
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $arrayData,
);
// sleep(2);
echo json_encode($json_data);
