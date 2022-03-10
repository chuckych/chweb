<?php
session_start();
require __DIR__ . '../../../config/index.php';
secure_auth_ch();
$Modulo='32';
ExisteModRol($Modulo);
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    borrarLogs(__DIR__ . '/', 1, '.log');
} else {
    borrarLogs(__DIR__ . '/', 30, '.log');
}
// $q = "SELECT id_user, fechaHora, phoneid FROM reg_ WHERE createdDate > '1646860443979' AND createdDate < '1646863286801' ORDER BY fechaHora";

// $arr = array_pdoQuery($q);
// $input = array_map("unserialize", array_unique(array_map("serialize", $arr)));
// echo '<pre>';

// print_r($input);

// exit;

require pagina('mobile.php');