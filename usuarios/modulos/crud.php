<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', '1');

FusNuloPOST('NombreMod', '');

$EstadoMod = test_input($_POST['EstadoMod']);
$IdMod     = test_input($_POST['IdMod']);
$NombreMod = test_input($_POST['NombreMod']);
$OrdenMod  = test_input($_POST['OrdenMod']);
$TipoMod   = test_input($_POST['TipoMod']);
$accion    = test_input($_POST['accion']);
$recid     = recid();
   
function LastID(){
    require __DIR__ . '../../../config/conect_mysql.php';
    $queryMaxMod = "SELECT MAX(modulos.id) as 'max' FROM modulos";
    $stmt        = mysqli_query($link, $queryMaxMod);
    while ($row= mysqli_fetch_assoc($stmt)) {
        $id = $row['max']+1;
    }
    return $id;
    mysqli_free_result($stmt);
    mysqli_close($link);
}
function CheckNombre($dato){
    require __DIR__ . '../../../config/conect_mysql.php';
    $queryMaxMod = "SELECT MAX(modulos.nombre) as 'nombre' FROM modulos where modulos.nombre = '$dato'";
    $stmt = mysqli_query($link, $queryMaxMod);
    while ($row= mysqli_fetch_assoc($stmt)) {
        $nombre = $row['nombre'];
    }
    return $nombre;
    mysqli_free_result($stmt);
    mysqli_close($link);
}
// sleep(3);
/** ALTA DE MODULO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['accion'] === '1')) {

    if (empty($NombreMod)) {
        statusData('ErrorPost', 'Descripción requerida');
    }
    if ($NombreMod == CheckNombre($NombreMod)) {
        statusData('Error', 'El nombre '.$NombreMod.' ya existe');
    }
    $id = LastID();
    $query = "INSERT INTO modulos (id, recid, nombre, orden, estado, idtipo) VALUES ( '$id', '$recid', '$NombreMod', '$OrdenMod', '$EstadoMod', '$TipoMod')";

    if (InsertRegistroMySql($query)) {
        statusData('ok', 'Modulo Cargado Correctamente');
    }else{
        statusData('error', 'Error al cargar el Modulo');
    }
    

} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['accion'] === '2')) {
    
    $query = "UPDATE modulos SET orden = '$OrdenMod', estado = '$EstadoMod', idtipo = '$TipoMod' WHERE id = $IdMod";
    if ( UpdateRegistroMySql($query) ) {
        statusData('ok', 'Modulo Editado Correctamente');
    }else{
        statusData('error', 'Error al editar el Modulo');
    }
    
}else{
    $data = array('status' => 'error', 'dato' => 'no accion');
    echo json_encode($data);
    exit;
}
/** FIN ALTA DE USUARIO */
