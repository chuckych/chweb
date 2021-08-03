<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();


require __DIR__ . '../../config/conect_mysql.php';

FusNuloPOST('recid_c', '');
$recid_c = test_input($_POST['recid_c']);

 $query="SELECT roles.id AS 'id', roles.nombre AS 'nombre'
 FROM roles
 INNER JOIN clientes ON roles.cliente = clientes.id
 WHERE roles.id > 1 AND clientes.recid ='$recid_c' ";
// print_r($query); exit;


$result  = mysqli_query($link, $query);
$data    = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) :

        $id   = $row['id'];
        $text = $row['nombre'];

        $data[] = array(
            'id'    => $id,
            'text'  => $text,
            'title' => $id.' - '.$text,
        );
    endwhile;
}
mysqli_free_result($result);
mysqli_close($link);
echo json_encode($data);
