<?php
header("Content-Type: application/json");

// header('Access-Control-Allow-Origin: *');
// header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
// header("Allow: GET, POST, OPTIONS, PUT, DELETE");


require __DIR__ . '/../funciones.php';
E_ALL();
$respuesta = '';
$token = token();
$id = (isset($_GET['id'])) ? "AND mod_roles.id='$_GET[id]'" : "";
$recidRol = (isset($_GET['recidRol'])) ? "AND mod_roles.recid_rol = '$_GET[recidRol]'" : "";
$id_mod = (isset($_GET['id_mod'])) ? "AND mod_roles.modulo = '$_GET[id_mod]'" : "";

// echo token();
if ($_GET['tk'] === token()) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        require __DIR__ . '/../config/conect_mysql.php';
        $query = "SELECT mod_roles.id AS 'id', mod_roles.recid_rol AS 'recid_rol', modulos.nombre AS 'nombre', modulos.id AS 'id_mod', modulos.idtipo AS 'idtipo'
        FROM mod_roles
        INNER JOIN modulos ON mod_roles.modulo = modulos.id
        WHERE mod_roles.id>'0' AND modulos.estado ='0' $id $recidRol $id_mod
        ORDER BY modulos.orden";
        $result = mysqli_query($link, $query);
        // print_r(mysqli_error_list($link));
        // print_r($query);
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
        } else {
            $respuesta = array('success' => 'NO', 'error' => '1', 'mod_roles' => 'NO HAY DATOS');
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', 'mod_roles' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'mod_roles' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
