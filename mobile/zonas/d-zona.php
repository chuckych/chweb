<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

E_ALL();

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['d_zona'] == 'true')) {
  $audCuenta = simple_pdoQuery("SELECT clientes.id FROM clientes where clientes.tkmobile = '$_SESSION[TK_MOBILE]'");
  $tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');
  $nombrezona = $_POST['_nombre'];

  $parametros = "?TYPE=DELETE&tk=" . $tkcliente . "&col=zones&validation_parameters%5Bname%5D=" . $nombrezona;
  $parametros = str_replace(" ", "%20", $parametros);
  $url        = "https://app.xmartclock.com/xmart/be/xmart_end_point.php" . $parametros;
  $json       = file_get_contents($url);
  $array      = json_decode($json, TRUE);
  // $array = json_decode(getRemoteFile($url), true);

  foreach ($array as $key => $value) {
    $SUCCESS = $array['SUCCESS'];
    $ERROR   = $array['ERROR'];
    $MESSAGE = $array['MESSAGE'];
    # code...
  }
  if ($array['SUCCESS'] == 'YES') {
    auditoria("Zona Mobile $nombrezona", 'B', $audCuenta['id'], '25');
    $data = array('status' => 'ok', 'Zona' => $nombrezona);
    echo json_encode($data);
    exit;
  } else {
    header('location:index.php?error');
  }
}
