<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
$urln   = host() . "/" . HOMEHOST . "/data/GetUser_legajo.php?tk=" . token() . "&recid_c=" . $_GET['_c'];
//  echo $urln; br(); exit;
// $json  = file_get_contents($urln);
// $array = json_decode($json, TRUE);
$array = json_decode(getRemoteFile($urln), TRUE);
$legajos = ($array['error']) ? "&ln%5B%5D=" . implode("&ln%5B%5D=", $array['legajos']) : '';

$url   = host() . "/" . HOMEHOST . "/data/GetPersonal.php?tk=" . token() . "&_r=" . $_SESSION["RECID_ROL"] . "&_c=" . $_GET['_c'] . $legajos;
// echo $url;
// $json  = file_get_contents($url);
// $array = json_decode($json, TRUE);
$array = json_decode(getRemoteFile($url), true);
$data  = $array[0]['personal'];
if (!$array[0]['error']) {
    $_c = $_GET['_c'];
    // print_r($data);
    foreach ($data as $value) {
        $pers_legajo  = $value['pers_legajo'];
        $pers_nombre  = $value['pers_nombre'];
        $pers_empresa = $value['pers_empresa'];
        $pers_planta  = $value['pers_planta'];
        $pers_sector  = $value['pers_sector'];
        $pers_grupo   = $value['pers_grupo'];
        $pers_sucur   = $value['pers_sucur'];
        $pers_dni     = $value['pers_dni'];
        if (test_input($_GET['LegaPass']) == 'true') {
            if ($pers_dni > '0') {
                $check = "<div style='margin-top:10px;' class='custom-control custom-checkbox custom-control-inline'>
            <input type='checkbox' name='_l[]' class='LegaCheck custom-control-input' id='$pers_legajo' value='$pers_legajo'>
            <label class='custom-control-label ml-2' for='$pers_legajo'>
            <p class='fontq' style='margin-top:3px;'>$pers_legajo</p>
            </label>
            </div>";
            } else {
                $check = "<div style='margin-top:10px;' class='custom-control custom-checkbox custom-control-inline'>
            <input type='text' disabled class='custom-control-input' value='$pers_legajo'>
            <span class='custom-control-label ml-2'>
            <p class='fontq' style='margin-top:3px;'>$pers_legajo</p>
            </span>
            </div>";
            }
        }else{
            $check = "<div style='margin-top:10px;' class='custom-control custom-checkbox custom-control-inline'>
            <input type='checkbox' name='_l[]' class='LegaCheck custom-control-input' id='$pers_legajo' value='$pers_legajo'>
            <label class='custom-control-label ml-2' for='$pers_legajo'>
            <p class='fontq' style='margin-top:3px;'>$pers_legajo</p>
            </label>
            </div>";
        }

        $respuesta[] = array(
            'check'        => $check,
            'pers_legajo'  => $pers_legajo,
            'pers_dni'     => ceronull($pers_dni),
            'pers_nombre'  => ceronull($pers_nombre),
            'pers_empresa' => ceronull($pers_empresa),
            'pers_planta'  => ceronull($pers_planta),
            'pers_sector'  => ceronull($pers_sector),
            'pers_grupo'   => ceronull($pers_grupo),
            'pers_sucur'   => ceronull($pers_sucur)
        );
    }
    $respuesta = array('personal' => $respuesta);
    echo json_encode($respuesta);
} else {
    $respuesta = array(
        'pers_legajo'  => '1',
        'pers_nombre'  => '1',
        'pers_empresa' => '1',
        'pers_planta'  => '1',
        'pers_sector'  => '1',
        'pers_grupo'   => '1',
        'pers_sucur'   => '1'
    );
    $respuesta = array('personal' => $respuesta);
    echo json_encode($respuesta);
}
