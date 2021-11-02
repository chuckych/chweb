<?php
session_start();
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['point'] != '')) {

    $_POST['alias'] = $_POST['alias'] ??'';

    $name = test_input($_POST['point']);
    $alias = test_input($_POST['alias']);

    if(valida_campo($alias)){
        $data = PrintRespuestaJson('Error', "Campo alias es requerido");
        exit;
    }

    $audCuenta = simple_pdoQuery("SELECT clientes.id FROM clientes where clientes.tkmobile = '$_SESSION[TK_MOBILE]'");
    $tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');

    $parametros = ("users_api.php?TYPE=UPDATE_POINT&tk=" . $tkcliente . "&name=" . $name."&alias=".$alias);
    $parametros = str_replace(" ", "%20", $parametros);
    $url = ("https://app.xmartclock.com/xmart/be/" . $parametros);
    // echo $url; exit;
    $json       = file_get_contents($url);
    $array      = json_decode($json, TRUE);

    foreach ($array as $key => $value) {
        $SUCCESS = $array['SUCCESS'];
        $ERROR   = $array['ERROR'];
        $MESSAGE = $array['MESSAGE'];
    }

    if ($array['SUCCESS'] == 'YES') {
        auditoria("Alias Mobile ($name) Nombre: $alias", 'M', $audCuenta['id'], '5');
        $data = PrintRespuestaJson('ok', "Datos Guardados correctamente");
        exit;
    } else {
        $data = PrintRespuestaJson('error', $MESSAGE);
        exit;
    }
}
