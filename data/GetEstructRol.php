<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
$concat='';
switch ($_GET['e']) {
    case 'sectores':
        $tabla     = 'sect_roles';
        $ColEstr   = 'sector';
        $arrayjson = 'sector';
        break;
    case 'plantas':
        $tabla     = 'plan_roles';
        $ColEstr   = 'planta';
        $arrayjson = 'planta';
        break;    
    case 'grupos':
        $tabla     = 'grup_roles';
        $ColEstr   = 'grupo';
        $arrayjson = 'grupo';
        break;    
    case 'sucursales':
        $tabla     = 'suc_roles';
        $ColEstr   = 'sucursal';
        $arrayjson = 'sucursal';
        break;    
    case 'empresas':
        $tabla     = 'emp_roles';
        $ColEstr   = 'empresa';
        $arrayjson = 'empresa';
        break;    
    case 'convenios':
        $tabla     = 'conv_roles';
        $ColEstr   = 'convenio';
        $arrayjson = 'convenio';
        break;    
    case 'secciones':
        $tabla     = 'secc_roles';
        $ColEstr   = 'seccion';
        $arrayjson = 'seccion';
        $concat = ", CONCAT(secc_roles.sector,secc_roles.seccion) AS 'sect_secc'";
        break;
    case 'personal':
        break;       
    default:
        $concat='';
        $respuesta = array('success' => 'NO', 'error' => '1', 'mensaje' => 'No se especifico parametro de estructura');
        $datos = array($respuesta);
        echo json_encode($datos);
        break;
}
UnsetGet('sect');
switch ($_GET['sect']) {
    case '1':
        $tabla     = 'secc_roles';
        $ColEstr   = 'sector';
        $arrayjson = 'sector';
        break;
    default:
        # code...
        break;
}
$respuesta = '';
$token     = token();
$recidRol  = (isset($_GET['_r'])) ? "AND $tabla.recid_rol = '$_GET[_r]'" : "";
$Sector  = (isset($_GET['sector'])) ? "AND $tabla.sector = '$_GET[sector]'" : "";

if ($_GET['tk'] === token()) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {
        
                require __DIR__ . '../../config/conect_mysql.php';
                $query = "SELECT DISTINCT $tabla.$ColEstr AS id, $tabla.recid_rol AS recid_rol $concat
                FROM $tabla
                WHERE $tabla.id LIKE '%%' $recidRol $Sector";
                $result = mysqli_query($link, $query);
                // print_r($query); exit;
                // $data  = array();
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) :
                        $id = ($_GET['e']=='secciones') ? $row['sect_secc'] : $row['id'];
                        $recid_rol = $row['recid_rol'];
                        $data[] = (
                            $id
                        );
                    endwhile;
                    mysqli_free_result($result);
                    mysqli_close($link);
                    $respuesta = array('success' => 'YES', 'error' => '0', $arrayjson => $data);
                } else {
                    $respuesta = array('success' => 'NO', 'error' => '1', $arrayjson => 'NO HAY DATOS');
                }

    } else {
        $respuesta = array('success' => 'NO', 'error' => '1', $arrayjson => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', $arrayjson => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
