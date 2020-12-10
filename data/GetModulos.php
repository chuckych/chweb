<?php
session_start();
require __DIR__ . '../../config/index.php';
ultimoacc();
// secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', '0');

$respuesta   = $nocuentas = '';
$token = token();

$recid = (isset($_GET['recid'])) ? "AND modulos.recid='$_GET[recid]'" : "";
$idtipo = (isset($_GET['idtipo'])) ? "AND modulos.idtipo='$_GET[idtipo]'" : "";
// if ($_GET['idtipo']=='5') {
//     $nocuentas = (modulo_cuentas()!='1') ? "AND modulos.id != '1'":'';
// }
// if (modulo_cuentas()!='1') {
//     echo 'ok';
// } exit;
$recidRol = (isset($_GET['recidRol'])) ? "AND modulos.id NOT IN (SELECT mod_roles.modulo FROM mod_roles WHERE mod_roles.recid_rol = '$_GET[recidRol]')" : "";

if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        require __DIR__ . '../../config/conect_mysql.php';
        $query = "SELECT modulos.id AS 'id', modulos.recid AS 'recid', modulos.nombre AS 'nombre', modulos.idtipo as 'tipo'
        FROM modulos 
        WHERE modulos.id>'0' $recid $recidRol $idtipo AND modulos.estado ='0'
        ORDER BY modulos.orden";
        $result = mysqli_query($link, $query);
        // print_r(mysqli_error_list($link));
        // print_r($query); exit;
        $data  = array();
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) :
                
                $id     = $row['id'];
                $recid  = $row['recid'];
                $tipo   = $row['tipo'];
                $nombre = $row['nombre'];
                $data[] = array(
                    'id'     => $id,
                    'recid'  => $recid,
                    'nombre' => $nombre,
                    'tipo'   => $tipo
                );
            endwhile;
            mysqli_free_result($result);
            mysqli_close($link);
            $respuesta = array('success' => 'YES', 'error' => 'NO', 'modulos' => $data);
        } else {
            $respuesta = array('success' => 'NO', 'error' => '1', 'modulos' => $data);
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', 'modulos' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'modulos' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
