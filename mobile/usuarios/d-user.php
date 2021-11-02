<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

E_ALL();
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['d_user'] == 'true')) {
  $audCuenta = simple_pdoQuery("SELECT clientes.id FROM clientes where clientes.tkmobile = '$_SESSION[TK_MOBILE]'");
  $tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');
  $nombre = $_POST['_nombre'];

  $id   = test_input($_POST['_id']);
  $parametros= ("persons.php?TYPE=DELETE_PERSON&tk=".$tkcliente."&id=".$id);
  $parametros = str_replace(" ", "%20", $parametros);
  $url= ("https://server.xenio.uy/".$parametros);
  // echo $url; exit;

  $json       = file_get_contents($url);
  $array      = json_decode($json, TRUE);

  foreach ($array as $key => $value) {
    $SUCCESS = $array['SUCCESS'];
    $ERROR   = $array['ERROR'];
    $MESSAGE = $array['MESSAGE'];
 }
 if($array['SUCCESS']=='YES'){
    auditoria("Usuario Mobile ($id) $nombre", 'B', $audCuenta['id'], '26');
    $data = array('status' => 'ok', 'usuario' => $nombre, 'MESSAGE'=> $MESSAGE);
    echo json_encode($data);
    exit;
  } else {
    $data = array('status' => 'error', 'usuario' => $nombre, 'MESSAGE'=> $MESSAGE);
    echo json_encode($data);
    exit;
  }
}
