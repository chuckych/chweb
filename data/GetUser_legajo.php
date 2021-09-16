<?php
header("Content-Type: application/json");
// session_start();
// require __DIR__ . '../../index.php';
require __DIR__ . '../../funciones.php';
// secure_auth_ch_json();
E_ALL();
$respuesta   = '';
$token = token();
$recid_c = (isset($_GET['recid_c'])) ? "AND clientes.recid='$_GET[recid_c]'" : "";
$recid   = (isset($_GET['recid'])) ? "AND usuarios.recid='$_GET[recid]'" : "";
$uid     = (isset($_GET['uid'])) ? "AND usuarios.id='$_GET[uid]'" : "";
$rol_id  = (isset($_GET['rol_id'])) ? "AND roles.id='$_GET[rol_id]'" : "";
$data  = array();
if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        require __DIR__ . '../../config/conect_mysql.php';
        $query="SELECT usuarios.legajo AS legajo FROM usuarios LEFT JOIN roles ON usuarios.rol=roles.id INNER JOIN clientes ON usuarios.cliente=clientes.id WHERE usuarios.id>'0' $recid_c $uid $recid $rol_id AND usuarios.legajo >0 ORDER BY usuarios.estado, usuarios.fecha desc";
        $result = mysqli_query($link, $query);
        // print_r(($query));exit;
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) :
                $legajo = $row['legajo'];
                $data[] = (
                    $legajo
                );
            endwhile;
            mysqli_free_result($result);
            mysqli_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'legajos' =>$data);
        } else {
            $respuesta = array('success' => 'NO', 'error' => '0', 'legajos' => $data);
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', 'legajos' =>  $data);
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'legajos' =>  $data);
}
// $datos = array($respuesta);
echo json_encode($respuesta);
// print_r($datos);
