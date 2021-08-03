<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

E_ALL();

$tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');
$url = "https://server.xenio.uy/persons.php?TYPE=LIST_PERSONS&tk=" . $tkcliente;
// $url = "https://app.xmartclock.com/xmart/be/xmart_end_point.php?TYPE=LIST&tk=" . $tkcliente."&if_exists=&if_not_exists=&col=persons&validation_parameters=&data_parameters=";
// echo $url; exit;
$json = file_get_contents($url);
$array = json_decode($json, TRUE);
// $array = json_decode(getRemoteFile($url), true);

$data = array();
if (is_array($array['MESSAGE']) || $array instanceof Traversable) {

foreach ($array['MESSAGE'] as $key => $valor) {

    $_id              = $valor['_id'];
    $enable2           = ($valor['enable']=='true')? 'Activos':'Inactivos';
    $enable           = ($valor['enable']=='true')? 'Activo':'Inactivo';
    $colorText        = ($valor['enable']=='true')? 'text-success':'text-danger';
    $date             = $valor['date'];
    $timestamp        = $valor['timestamp'];
    $name             = $valor['name'];
    $id               = $valor['id'];
    $c_name           = $valor['c_name'];
    $c_id             = $valor['c_id'];
    $trained          = ($valor['trained']=='true')?'Enrolado':'No Enrolado';
    $colorText2       = ($valor['trained']=='true')? 'text-success':'text-danger';
    $disabledEntrenar = ($valor['trained']=='true')?'disabled':'';

    $eliminar= '<button type="button" class="icon btn btn-sm btn-light border-0 text-decoration-none EliminaUsuario " data-toggle="modal" data-target="#EliminaUsuario" data="'.$id.'" data1="'.$name.'" data2="'.$tkcliente.'"><span data-icon="&#xe03d;" class="align-middle pt-1"></span></button>';
    $modificar= '<button type="button" class="btn btn-sm btn-light border-0 text-decoration-none ModificarUsuario " data="'.$id.'" data1="'.$name.'" data2="'.$enable.'" data3="'.$trained.'"><span data-icon="&#xe042;" class=""></span></button>';
    $entrenar= '<button href="?p=entrenar.php&u_id='.$id.'" class="btn btn-sm btn-light border-0 EntrenarUsuario fontq" data="'.$id.'" data1="'.$name.'" data2="'.$tkcliente.'" data3="'.$trained.'">Enrolar</button>';

    $date = new DateTime($date);
    $fecha = $date->format('d/m/Y');
    $datestr = $date->format('Ymd');
   
            $data[] = array(
                // "_id"    => "",
                "del"       => $eliminar,
                "mod"       => $modificar,
                "enable2"    => $enable2,
                "enable"    => $enable,
                "date"      => $fecha,
                "datestr"   => $datestr,
                "timestamp" => $timestamp,
                "name"      => $name,
                "id"        => $id,
                "usuario"   => '<span id="nombre_'.$id.'">'.$name.'</span><br/>'.$id,
                "estado"    => '<span class="'.$colorText2.' fw5" id="Train_'.$id.'">'.$trained.'</span><br/><span class="'.$colorText.' fw5">'.$enable.'</span>',
                "entrenar"  => $entrenar,
                // "c_name" => "",
                // "c_id"   => "",
                "trained"   => $trained,
                "null"      => ''
            );   
            usort($data, function ($a, $b) {
                if ($a['datestr'] == $b['datestr']) {
                    return 0;
                }
                return ($a['datestr'] > $b['datestr']) ? -1 : 1;
            });  
        }
    }
        
$data = array('usuarios'=>$data);
echo json_encode($data);
