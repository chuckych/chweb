<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', '0');

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_zona'] == 'true')) {

   $tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');

   $lat        = $_POST['lat'];
   $lng        = $_POST['lng'];
   $metros     = $_POST['metros'];
   $nombrezona = $_POST['nombre'];
   // $mail       = $_POST['mail'];

   
   $parametros = "?TYPE=INSERT&tk=".$tkcliente."&col=zones&validation_parameters%5Bname%5D=".$nombrezona."&data_parameters%5Bname%5D=".$nombrezona."&data_parameters%5Bmap_size%5D=".$metros."&data_parameters%5Blat%5D=".$lat."&data_parameters%5Blng%5D=".$lng;
   $parametros = str_replace(" ", "%20", $parametros);
   $url        = "https://app.xmartclock.com/xmart/be/xmart_end_point.php".$parametros;
   $json  = file_get_contents($url);
   $array = json_decode($json, TRUE);
   // $array = json_decode(getRemoteFile($url), true);
  
   // exit();
   // print_r($array); exit;
   foreach ($array as $key => $value) {
      $SUCCESS = $array['SUCCESS'];
      $ERROR   = $array['ERROR'];
      $MESSAGE = $array['MESSAGE'];
      # code...
   }
   if($array['SUCCESS']=='YES'){
      $data = array('status' => 'ok', 'zona' => $nombrezona, 'lat'=>$lat, 'lng'=> $lng, 'radio'=>$metros);
      echo json_encode($data);
      exit;
   }else{header('location:index.php?error');}
}