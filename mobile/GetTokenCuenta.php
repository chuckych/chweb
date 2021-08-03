<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../config/conect_mysql.php';

$q = FusNuloPOST('q','');

$FiltroQ  = (!empty($q)) ? "AND clientes.nombre LIKE '%$q%'":'';

$sql = "SELECT clientes.tkmobile as 'id', clientes.nombre 'text'
FROM clientes 
WHERE clientes.tkmobile !='' $FiltroQ";
// print_r($sql);exit;

$rs = mysqli_query($link, $sql);
$numrows = mysqli_num_rows($rs);
    
if (mysqli_num_rows($rs) > 0) {
    while ($row = mysqli_fetch_assoc($rs)) :

        $id   = $row['id'];
        $text = $row['text'];

        $data[] = array(
            'id'    => $id,
            'text'  => $text,
        );
    endwhile;
}
mysqli_free_result($rs);
mysqli_close($link);
echo json_encode($data);
