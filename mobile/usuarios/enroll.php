<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', '0');

$tkcliente = $_SESSION["TK_MOBILE"];
$u_id      = $_POST['u_id'];
$_id       = $_POST['_id'];
$face_url  = $_POST['face_url'];

$url = "https://server.xenio.uy/save.php?TYPE=ADD_FACES&u_id=".$u_id."&tk=".$tkcliente."&FACE_URL=".$face_url;

// echo $url;exit;

$json = file_get_contents($url);
$array = json_decode($json, TRUE);


foreach ($array as $key => $value) {
    $SUCCESS = $array['SUCCESS'];
    $ERROR   = $array['ERROR'];
    $MESSAGE = $array['MESSAGE'];
    # code...
 }
 if($array['SUCCESS']=='YES'){

    $url2 = "https://server.xenio.uy/save.php?TYPE=SET_ENROLL_STATUS&tk=".$tkcliente."&enroll_id=".$_id."&status=2&u_id=".$u_id;
    $json2 = file_get_contents($url2);
    $array2 = json_decode($json2, TRUE);

    foreach ($array2 as $key => $value2) {

        $SUCCESS = $array['SUCCESS'];
        $ERROR   = $array['ERROR'];
        $MESSAGE = $array['MESSAGE'];

     }
     if($array['SUCCESS']=='YES'){

        $data = array('status' => 'ok', 'MESSAGE' => $MESSAGE);
        echo json_encode($data);
        exit;

     }else{
        $data = array('status' => 'error', 'MESSAGE' => $MESSAGE, 'ERROR' => $ERROR);
        echo json_encode($data);
        exit;
     }

    
 }else{
    $data = array('status' => 'error', 'MESSAGE' => $MESSAGE, 'ERROR' => $ERROR);
    echo json_encode($data);
    exit;
 }
