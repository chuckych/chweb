<?php
session_start();
require __DIR__ . '/../../config/index.php';
// ultimoacc();
// secure_auth_ch();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');

E_ALL();

FusNuloPOST('tipo', false);
FusNuloPOST('modulos', false);

require __DIR__ . '/../../config/conect_mysql.php';
$respuesta = '';
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'true')) {
    $query = "SELECT tipo_modulo.id AS 'id', tipo_modulo.descripcion AS 'descripcion', (SELECT COUNT(modulos.id) FROM modulos WHERE modulos.idtipo = tipo_modulo.id AND modulos.estado='0') AS 'CantMod' FROM tipo_modulo WHERE estado = '0' ORDER BY CantMod desc";
    // h1($query);exit;
    $result = mysqli_query($link, $query);
    $data = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)):
            $id = $row['id'];
            $descripcion = $row['descripcion'];
            $CantMod = $row['CantMod'];
            $data[] = array(
                'id' => $id,
                'TipoModulo' => $descripcion,
                'CantMod' => $CantMod
            );
        endwhile;
        mysqli_free_result($result);
        mysqli_close($link);

        $data = array('status' => 'ok', 'datos' => $data);
        echo json_encode($data);
        exit;
    } else {
        $data = array('status' => 'error', 'datos' => $data);
        echo json_encode($data);
        exit;
    }

    // $datos = array($respuesta);
    // echo json_encode($datos);
}
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['modulos'] == 'true')) {

    FusNuloPOST('recidRol', false);
    $recidRol = $_POST['recidRol'];
    $tipo = $_POST['tipo'];

    if (empty($recidRol)) {
        $data = array('status' => 'error', 'datos' => $data, 'error' => 'No hay Recid');
        echo json_encode($data);
        exit;
    }

    $respuesta = array();
    $nocuentas = '';
    $idtipo = (isset($tipo)) ? "AND modulos.idtipo='$tipo'" : "";
    if ($tipo == '5') {
        $nocuentas = (modulo_cuentas() != '1') ? "AND modulos.id != '1'" : '';
    }

    $query = "SELECT modulos.id AS 'id', modulos.recid AS 'recid', modulos.nombre AS 'nombre', modulos.idtipo AS 'tipo'
    FROM modulos 
    WHERE modulos.id>'0' $idtipo $nocuentas AND modulos.estado ='0'
    ORDER BY modulos.orden";
    $result = mysqli_query($link, $query);
    // print_r(mysqli_error_list($link));
    // print_r($query); exit;
    $data = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)):

            $id = $row['id'];
            $recid = $row['recid'];
            $tipo = $row['tipo'];
            $nombre = $row['nombre'];

            $data[] = array(
                'id' => $id,
                'recid' => $recid,
                'nombre' => $nombre,
                'tipo' => $tipo
            );

        endwhile;
        mysqli_free_result($result);
        mysqli_close($link);
        $respuesta = array('status' => 'ok', 'datos' => $data, 'modulos' => 'true');
    }
    echo json_encode($respuesta);
    exit;


    // $data = array();
    // $url  = host() . "/" . HOMEHOST . "/data/GetModulos2.php?tk=" . token() . "&idtipo=" . $tipo."&recidRol=" . $recidRol;
    // $json = file_get_contents($url);
    // $data = json_decode($json, TRUE);
    // print_r($data); exit;
    // echo $url; exit;
    if (is_array($data)) {
        if ($data[0]['success'] == 'YES') {
            $data = array('status' => 'ok', 'datos' => $data, 'modulos' => 'true');
            echo json_encode($data);
            exit;
        } else {
            $data = array('status' => 'errors', 'datos' => $data, 'modulos' => 'true');
            echo json_encode($data);
            exit;
        }
    } else {
        $data = array('status' => 'errorArray', 'datos' => $data, 'modulos' => 'true');
        echo json_encode($data);
        exit;
    }
}

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['activos'] == 'true')) {

    FusNuloPOST('recidRol', false);
    $recidRol = $_POST['recidRol'];

    if (empty($recidRol)) {
        $data = array('status' => 'error', 'datos' => $data, 'error' => 'No hay Recid');
        echo json_encode($data);
        exit;
    }
    // $data = array();
    // $url  = host() . "/" . HOMEHOST . "/data/GetModRol.php?tk=" . token() . "&recidRol=" . $recidRol;
    // $json = file_get_contents($url);
    // $data = json_decode($json, TRUE);

    $query = "SELECT mod_roles.id AS 'id', mod_roles.recid_rol AS 'recid_rol', modulos.nombre AS 'nombre', modulos.id AS 'id_mod', modulos.idtipo AS 'idtipo'
        FROM mod_roles
        INNER JOIN modulos ON mod_roles.modulo = modulos.id
        WHERE mod_roles.id>'0' AND modulos.estado ='0' AND mod_roles.recid_rol = '$recidRol'
        ORDER BY modulos.orden";
    $result = mysqli_query($link, $query);
    // print_r(mysqli_error_list($link));
    // print_r($query); exit;
    $data = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)):

            $id = $row['id'];
            $id_mod = $row['id_mod'];
            $recid_rol = $row['recid_rol'];
            $nombre = $row['nombre'];
            $idtipo = $row['idtipo'];
            $data[] = array(
                'id' => $id,
                'recid_rol' => $recid_rol,
                'id_mod' => $id_mod,
                'modulo' => $nombre,
                'idtipo' => $idtipo
            );
        endwhile;
        mysqli_free_result($result);
        mysqli_close($link);
        $respuesta = array('success' => 'YES', 'error' => '0', 'mod_roles' => $data);
    }
    // echo json_encode($respuesta);
    // exit;
    // print_r($data);

    if (is_array($respuesta)) {
        if ($respuesta['success'] == 'YES') {
            $respuesta = array('status' => 'ok', 'datos' => $data, 'activos' => 'true');
            echo json_encode($respuesta);
            exit;
        } else {
            $respuesta = array('status' => 'errors', 'datos' => $respuesta, 'activos' => 'true');
            echo json_encode($respuesta);
            exit;
        }
    } else {
        $respuesta = array('status' => 'error', 'datos' => $data, 'activos' => 'true');
        echo json_encode($respuesta);
        exit;
    }

    // $datos = array($respuesta);
    // echo json_encode($datos);
}
