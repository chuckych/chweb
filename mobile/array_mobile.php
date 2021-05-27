<?php
// session_start();
// ultimoacc();
// secure_auth_ch();
// header("Content-Type: application/json");
// header('Access-Control-Allow-Origin: *');

require __DIR__ . '../../config/index.php';
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

$_datos     = 'mobile';
$token = TokenMobile($_SESSION["TK_MOBILE"], 'token');

require __DIR__ . '../../config/conect_mysql.php';
$query = "SELECT clientes.tkmobile, clientes.nombre FROM clientes where clientes.tkmobile = '$_SESSION[TK_MOBILE]' LIMIT 1";
$rs = mysqli_query($link, $query);
while ($row = mysqli_fetch_assoc($rs)) {
    $tkmobile = TokenMobile($_SESSION["TK_MOBILE"], 'appcode');
    $cuenta = $row['nombre'];
};
mysqli_free_result($rs);
mysqli_close($link);

$DateRange  = explode(' al ', $_POST['_drMob']);
// $start_date = (($DateRange[0]));
// $end_date   = (($DateRange[1]));

$start_date = date("d-m-Y", strtotime((str_replace("/", "-", $DateRange[0]))));
$end_date   = date("d-m-Y", strtotime((str_replace("/", "-", $DateRange[1]))));


$url   = "https://server.xenio.uy/metrics.php?TYPE=GET_CHECKS&tk=" . $token . "&start_date=" . $start_date . "&end_date=" . $end_date;

$json  = file_get_contents($url);
$array = json_decode($json, TRUE);
// $array = json_decode(getRemoteFile($url), true);

// print_r($array);exit;

$respuesta = array();
    
    if ($array['SUCCESS'] == 'YES' && (!empty($array['MESSAGE']))) {
        foreach ($array['MESSAGE'] as $key => $valor) {
            $timestamp = $valor['timestamp'];
            /* CONVERTIMOS TIMESTAMP Y LE DAMOS FORMATO AÑO/DIA/MES */
            $datetimeFormat = 'd/m/Y';
            /** Formato de fecha */
            $datetimeFormat2 = 'Ymd';
            /** Formato de fecha2 */
            $dates          = new \DateTime();
            $dates          = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
            $dates->setTimestamp($timestamp);
            // $fech           = $dates->format($datetimeFormat);
            $dia             = $dates->format($datetimeFormat2);
            $dia2            = $dates->format($datetimeFormat);
            $certeza         = ($valor['similarity'] > 70) ? imgIcon('check', round($valor['similarity'], 0, PHP_ROUND_HALF_UP) . '%', 'w15') : imgIcon('uncheck', round($valor['similarity'], 0, PHP_ROUND_HALF_UP) . '%', 'w15');
            $LinkMapa        = "https://www.google.com/maps/place/" . $valor['lat'] . "," . $valor['lng'];
            $iconMapa        = ($valor['lat'] != '0') ? '<a href="' . $LinkMapa . '" target="_blank" rel="noopener noreferrer">' . imgIcon('marker', 'Ver Mapa', 'w20') . '</a>' : imgIcon('nomarker', 'Sin GPS', 'w20');
            $gps             = ($valor['lat'] != '0') ? '' : 'Sin GPS';
            $zone            = (!empty($valor['zone'])) ? '<span class="text-success fw4">' . $valor['zone'] . '</span>' : '<span class="text-danger fw4">Fuera de Zona</span>';
            $name            = $valor['name'];
            $zone2           = $valor['zone'];
            $valor['IN_OUT'] = $valor['IN_OUT'] ?? '';
            switch ($valor['IN_OUT']) {
                case 'OUT':
                    $inout = 'Salida';
                    break;
                case 'IN':
                    $inout = 'Entrada';
                    break;
                case 'AUTOMATIC':
                    $inout = 'Automático';
                    break;

                default:
                    $inout = $valor['IN_OUT'];
                    break;
            }
            $imgfoto = ($valor['face_url']) ? "https://server.xenio.uy/" . $valor['face_url'] : '../img/user.png';

            $certeza2 = round($valor['similarity'], 0, PHP_ROUND_HALF_UP) . '%';
            // $respuesta = array();
            $respuesta[] = array(
                // '_id'        => $valor['_id'],
                'timestamp'  => $valor['timestamp'],
                'Fecha2'         => $dia2,
                'Fecha'         => DiaSemana3($dia),
                'Fecha4'         => DiaSemana4($dia),
                // 'dia'           => DiaSemana($dia),
                'face_url'      => '<span class="pic" datafoto="' . $valor['face_url'] . '" dataname="' . $name . '" datauid="' . $valor['u_id'] . '" datacerteza="' . $valor['similarity'] . '" datacerteza2="' . $certeza2 . '" datainout="' . $inout . '" datazone="' . $valor['zone'] . '" datahora="' . $valor['time'] . '" datadia="' . DiaSemana4($dia) . '" datagps="' . $gps . '" datatype="' . ucfirst($valor['t_type']) . '" datalat="' . ($valor['lat']) . '" datalng="' . ($valor['lng']) . '" >' . Foto($imgfoto, '', "shadow-sm w40 h40 scale radius img-fluid pointer") . '</span>',
                't_type'        => ucfirst($valor['t_type']),
                // 'check_type' => $valor['check_type'],
                // 'lat'        => $valor['lat'],
                // 'lng'        => $valor['lng'],
                'mapa'          => $iconMapa,
                'u_id'          => $valor['u_id'],
                // 'point'      => $valor['point'],
                'similarity'    => $certeza,
                'IN_OUT'        => $inout,
                'zone'          => $zone,
                'zone2'          => $zone2,
                // 'alias'      => $valor['alias'],
                'uid'           => $valor['u_id'],
                'name'          => $name,
                // 'date'       => $valor['date'],
                'time'          => $valor['time'],
                'gps'           => $gps
                // 'time_o_f'   => $valor['time_o_f']
            );
        }
        $respuesta = array($_datos => $respuesta, 'message' => '', 'success' => $array['SUCCESS'], 'AppCode'=> $tkmobile,'Cuenta'=> $cuenta);
        echo json_encode($respuesta);
    }
    else{
        $respuesta = array($_datos => '', 'message' => $array['MESSAGE'], 'success' => $array['SUCCESS'], 'AppCode'=> $tkmobile,'Cuenta'=> $cuenta);
        echo json_encode($respuesta);
    }
// }
