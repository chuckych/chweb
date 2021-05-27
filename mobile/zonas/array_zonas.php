<?php
require __DIR__ . '../../../config/index.php';
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

foreach ($array['DATA'] as $key => $valor) {

    $name       = (($valor['name']));
    $map_size   = $valor['map_size'];
    $lat        = $valor['lat'];
    $lng        = $valor['lng'];
    $updated_on = $valor['updated_on'];
    $eliminar= '<button type="button" class="icon btn btn-sm btn-link text-decoration-none EliminaZona" data-toggle="modal" data-target="#EliminaZona" data="'.$lat.'" data4="'.$tkcliente.'" data5="'.$name.'"><span data-icon="&#xe03d;" class="align-middle text-gris pt-1"></span></button>';
    $modificar= '<button type="button" class="btn btn-sm rounded-circle btn-outline-success border ModificarZona" data="'.$lat.'" data1="'.$lng.'" data2="'.$name.'" data3="'.$map_size.'"><span data-icon="&#xe042;" class=""></span></button>';
    $ver ='<button data="'.$lat.'" data1="'.$lng.'" data2="'.$name.'" data3="'.$map_size.'" type="button" class="verZone btn btn-outline-custom px-2 btn-sm fontq border-0">Ver</button>';

            $data[] = array(
                'updated_on' => $updated_on,
                'ver'        => $ver,
                'name'       => $name,
                'map_size'   => $map_size,
                'lat'        => ($lat),
                'lng'        => ($lng),
                'eliminar'   => $eliminar,
                'modificar'  => $modificar,
                'null'       => ''
            );   
            usort($data, function ($a, $b) {
                if ($a['name'] == $b['name']) {
                    return 0;
                }
                return ($a['name'] < $b['name']) ? -1 : 1;
            });  
        }
        

$data = array('zonas'=>$data);
echo json_encode($data);
