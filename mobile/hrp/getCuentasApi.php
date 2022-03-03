<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

$q = FusNuloPOST('q', '');

$FiltroQ  = (!empty($q)) ? "AND clientes.nombre LIKE '%$q%'" : '';

$sql="SELECT clientes.recid as 'id', clientes.nombre 'text' FROM clientes WHERE clientes.ApiMobileHRP !='' $FiltroQ";

$arrayData = array_pdoQuery($sql);

if (count($arrayData) > 0) {
    foreach ($arrayData as $row) {
        $id   = $row['id'];
        $text = $row['text'];

        $data[] = array(
            'id'    => $id,
            'text'  => $text,
        );
    }
}
echo json_encode($data);
exit;
