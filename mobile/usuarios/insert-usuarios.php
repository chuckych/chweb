<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta'] == 'true')) {

      $tkcliente = $_SESSION["TK_MOBILE"];

      $_id       = FusNuloPOST('_id',''); /** DNI */
      $_name     = FusNuloPOST('_name',''); /** Nombre */
      $_email    = FusNuloPOST('_email',''); /** Email */
      $_enable   = FusNuloPOST('_enable','false'); /** Estado: Activo o Inactivo. True o False */

      $_id       = test_input($_id);
      $_name     = test_input($_name);
      $_email    = test_input($_email);
      $_email    = filter_var($_email, FILTER_SANITIZE_EMAIL);
      $_enable   = test_input($_enable);
      $_enable   = ($_enable=='on')?'true':'false';
      $cel       = '';
      $tel       = '';
      $p_type    = ''; /** cargo */
      $gender    = '';
   
      $startdate = date('d-m-Y'); /** Fecha Actual */

   $parametros=("persons.php?TYPE=ADD_PERSON&childs=0&start_date=".$startdate."&role=&address=&cell_phone=".$cel."&phone=".$tel."&email=".$_email."&pic=&tk=".$tkcliente."&pin=&p_type=".$p_type."&name=".$_name."&departament&place=&birth_date=&gender=".$gender."&enable=".$_enable."&id=".$_id);

   $parametros = str_replace(" ", "%20", $parametros);

   $url=("https://server.xenio.uy/".$parametros);
   // echo $url; exit;

   $json  = file_get_contents($url);
   $array = json_decode($json, TRUE);
  
 
   foreach ($array as $key => $value) {
      $SUCCESS = $array['SUCCESS'];
      $ERROR   = $array['ERROR'];
      $MESSAGE = $array['MESSAGE'];
      # code...
   }
   if($array['SUCCESS']=='YES'){
      $data = array('status' => 'ok', '_name' => $_name, 'MESSAGE' => 'Usuario creado.');
      echo json_encode($data);
      exit;
   }else{
      $data = array('status' => 'error', 'MESSAGE' => $MESSAGE, 'ERROR' => $ERROR);
      echo json_encode($data);
      exit;
   }
} elseif(($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta'] == 'update')) {
   
      $tkcliente = $_SESSION["TK_MOBILE"];

      $_id       = FusNuloPOST('_id',''); /** DNI */
      $_name     = FusNuloPOST('_name',''); /** Nombre */
      $_email    = FusNuloPOST('_email',''); /** Email */
      $_enable   = FusNuloPOST('_enable','false'); /** Estado: Activo o Inactivo. True o False */

      $_id       = test_input($_id);
      $_name     = test_input($_name);
      $_email    = test_input($_email);
      $_email    = filter_var($_email, FILTER_SANITIZE_EMAIL);
      $_enable   = test_input($_enable);
      $_enable   = ($_enable=='on')?'true':'false';
      $cel       = '';
      $tel       = '';
      $p_type    = ''; /** cargo */
      $gender    = '';
   
      $startdate = ''; /** Fecha Actual */

   $parametros = ("persons.php?TYPE=EDIT_PERSON&childs=0&start_date=&role=&address=&cell_phone=".$cel."&phone=".$tel."&email=".$_email."&pic=&tk=".$tkcliente."&pin=&p_type=".$p_type."&name=".$_name."&departament&place=&birth_date=&gender=".$gender."&enable=".$_enable."&id=".$_id);
   $parametros = str_replace(" ", "%20", $parametros);

   $url=("https://server.xenio.uy/".$parametros);
   // echo $url; exit;

   $json  = file_get_contents($url);
   $array = json_decode($json, TRUE);
  
 
   foreach ($array as $key => $value) {
      $SUCCESS = $array['SUCCESS'];
      $ERROR   = $array['ERROR'];
      $MESSAGE = $array['MESSAGE'];
      # code...
   }
   if($array['SUCCESS']=='YES'){
      $data = array('status' => 'ok', '_name' => $_name, '_id'=>$_id, 'MESSAGE' => 'Datos Guardados.');
      echo json_encode($data);
      exit;
   }else{
      $data = array('status' => 'error', 'MESSAGE' => $MESSAGE, 'ERROR' => $ERROR);
      echo json_encode($data);
      exit;
   }

}