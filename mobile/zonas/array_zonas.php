<?php
require __DIR__ . '../../../config/index.php';
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

$tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');

$url = "https://app.xmartclock.com/xmart/be/xmart_end_point.php?TYPE=LIST&col=zones&tk=" . $tkcliente;
$json = file_get_contents($url);
$array = json_decode($json, TRUE);
//$array = json_decode(getRemoteFile($url), true);
// echo $url; exit;

$data = array();

// foreach ($array['DATA'] as $key => $v) {
    
//     $data_parameters="data_parameters[name]=".urlencode($v['name']);
//     $data_parameters.="&data_parameters[map_size]=100";
//     $data_parameters.="&data_parameters[lat]=".urlencode($v['lat']);
//     $data_parameters.="&data_parameters[lng]=".urlencode($v['lng']);
//     $urlUpdate = "https://app.xmartclock.com/xmart/be/xmart_end_point.php?TYPE=UPDATE&col=zones&tk=" . $tkcliente."&".($data_parameters)."&validation_parameters[name]=".urlencode($v['name']);
//     if ($v['map_size']!='100') {
//         $array = file_get_contents($urlUpdate);
//     }
//     // print_r(($urlUpdate));
//     // break;
   
// }
// exit;
foreach ($array['DATA'] as $key => $valor) {
    $name       = $valor['name'];
    $map_size   = $valor['map_size'];
    $lat        = $valor['lat'];
    $lng        = $valor['lng'];
    $updated_on = $valor['updated_on'];

    $eliminar = '<button type="button" class="icon btn btn-sm btn-link text-decoration-none EliminaZona" data-toggle="modal" data-target="#EliminaZona" data="'.$lat.'" data4="'.$tkcliente.'" data5="'.$name.'"><span data-icon="&#xe03d;" class="align-middle text-gris pt-1"></span></button>';
    $modificar = '<button type="button" class="btn btn-sm rounded-circle btn-outline-success border ModificarZona" data="'.$lat.'" data1="'.$lng.'" data2="'.$name.'" data3="'.$map_size.'"><span data-icon="&#xe042;" class=""></span></button>';
    $ver ='<button data="'.$lat.'" data1="'.$lng.'" data2="'.$name.'" data3="'.$map_size.'" type="button" class="verZone btn btn-outline-custom px-2 btn-sm fontq border-0">Ver</button>';
    
    $marker = "<div class='p-3 shadow-sm bg-white'><label class='w40 fontq'>Zona: </label> <span class='font-weight-bold'>$name</span><br><label class='w40 fontq'>Radio: </label> <span class='font-weight-bold'>$map_size</span></div>";
    
    $jsonMarcador = json_encode(array(
        'name'     => $name,
        'lat'      => $valor['lat'],
        'lng'      => $valor['lng'],
        'map_size' => $valor['map_size'],
    ));
    $marcador = "<span class='marcador' marcador='$jsonMarcador'>$map_size</span>";

            $data[] = array(
                'updated_on' => $updated_on,
                'ver'        => $ver,
                'name'       => $name,
                'map_size'   => $marcador,
                'lat'        => ($lat),
                'lng'        => ($lng),
                'eliminar'   => $eliminar,
                'modificar'  => $modificar,
                'null'       => ''
            );   
            $data2[] = array(
                'LATITUD'  => floatval($lat),
                'LONGITUD' => floatval($lng),
                'NOMBRE'   => $name,
            );   
            usort($data, function ($a, $b) {
                if ($a['name'] == $b['name']) {
                    return 0;
                }
                return ($a['name'] < $b['name']) ? -1 : 1;
            });  
        }
        

$data = array('zonas'=>$data, 'zonas2'=>$data2);
echo json_encode($data);
