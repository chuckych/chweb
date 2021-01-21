<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', '0');

$tkcliente = TokenMobile($_SESSION["TK_MOBILE"], 'token');
$u_id      = $_POST['u_id'];

$url     = "https://server.xenio.uy/save.php?u_id=" . $u_id . "&tk=" . $tkcliente . "&TYPE=LIST_FACES";
/** Listamos los rostros */
// echo $url; exit;
$json    = file_get_contents($url);
$array   = json_decode($json, TRUE);
$MESSAGE = $array['MESSAGE'];
if ($array['SUCCESS'] == 'YES') {
   /** recorremos los rostros */
   foreach ($MESSAGE as $key => $valor) {

      if ((!empty($valor['FaceId']))) {
         /** Si el valor FaceId existe lo borramos en un bucle*/
         $url  = "https://server.xenio.uy/save.php?u_id=" . $valor['ExternalImageId'] . "&FaceId=" . $valor['FaceId'] . "&tk=" . $tkcliente . "&TYPE=DELETE_FACE";
         // echo $url; exit;
         $json = file_get_contents($url);
         $array = json_decode($json, TRUE);
         foreach ($array as $key => $value) {
            $SUCCESS = $array['SUCCESS'];
            $ERROR   = $array['ERROR'];
            $MESSAGE = $array['MESSAGE'];
         }
         if ($array['SUCCESS'] != 'YES') {
            $data = array('status' => 'error', 'DELETE_FACE' => $MESSAGE);
            echo json_encode($data);
            exit;
         }
      } else {
         /** Luego si ya no existe el FaceId hacemos un RESET_DB_ENROLL_PICTURES */
      }
   }

   $url  = "https://server.xenio.uy/save.php?u_id=" . $valor['ExternalImageId'] . "&tk=" . $tkcliente . "&TYPE=RESET_DB_ENROLL_PICTURES";
   // echo $url; exit;
   $json = file_get_contents($url);
   $array   = json_decode($json, TRUE);
   foreach ($array as $key => $value) {
      $SUCCESS = $array['SUCCESS'];
      $ERROR   = $array['ERROR'];
      $MESSAGE = $array['MESSAGE'];
   }
   if ($array['MESSAGE'] == 'true') {
      $data = array('status' => 'ok', 'RESET_DB_ENROLL_PICTURES' => $MESSAGE);
      echo json_encode($data);
      exit;
   } else {
      $data = array('status' => 'error', 'RESET_DB_ENROLL_PICTURES' => $MESSAGE);
      echo json_encode($data);
      exit;
   }
   
   $data = array('status' => 'ok', 'LIST_FACES' => $MESSAGE);
   echo json_encode($data);
   exit;
} else {
   $data = array('status' => 'error', 'LIST_FACES' => $MESSAGE);
   echo json_encode($data);
   exit;
}
