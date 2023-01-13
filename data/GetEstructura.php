<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "spanish");
require __DIR__ . '../../config/index.php';
E_ALL();
$count_secciones = '';
switch ($_GET['e']) {
    case 'sectores':
        $tabla       = 'SECTORES';
        $ColCodi     = 'SecCodi';
        $ColDesc     = 'SecDesc';
        $ColPersCodi = 'LegSect';
        $DescSin     = 'Sin Sector';
        $valorarray  = 'sectores';
        $valorarray2  = 'sector';
        $GetValRol  = 'GetEstructRol';
        /** Lista de los valores de la estructura del rol */
        $count_secciones = "(SELECT COUNT(SECCION.SecCodi) FROM SECCION WHERE SECCION.SecCodi = SECTORES.SecCodi AND SECCION.Se2Codi > '0') AS cant_secciones,";
        break;
    case 'plantas':
        $tabla      = 'PLANTAS';
        $ColCodi    = 'PlaCodi';
        $ColDesc     = 'PlaDesc';
        $ColPersCodi = 'LegPlan';
        $DescSin    = 'Sin Planta';
        $valorarray = 'plantas';
        $valorarray2 = 'planta';
        $GetValRol  = 'GetEstructRol';
        break;
    case 'grupos':
        $tabla       = 'GRUPOS';
        $ColCodi     = 'GruCodi';
        $ColDesc     = 'GruDesc';
        $ColPersCodi = 'LegGrup';
        $DescSin     = 'Sin Grupo';
        $valorarray  = 'grupos';
        $valorarray2 = 'grupo';
        $GetValRol   = 'GetEstructRol';
        break;
    case 'sucursales':
        $tabla       = 'SUCURSALES';
        $ColCodi     = 'SucCodi';
        $ColDesc     = 'SucDesc';
        $ColPersCodi = 'LegSucu';
        $DescSin     = 'Sin Sucursal';
        $valorarray  = 'sucursales';
        $valorarray2 = 'sucursal';
        $GetValRol   = 'GetEstructRol';
        break;
    case 'empresas':
        $tabla       = 'EMPRESAS';
        $ColCodi     = 'EmpCodi';
        $ColDesc     = 'EmpRazon';
        $ColPersCodi = 'LegEmpr';
        $DescSin     = 'Sin Empresa';
        $valorarray  = 'empresas';
        $valorarray2 = 'empresa';
        $GetValRol   = 'GetEstructRol';
        break;
    case 'convenios':
        $tabla       = 'CONVENIO';
        $ColCodi     = 'ConCodi';
        $ColDesc     = 'ConDesc';
        $ColPersCodi = 'LegConv';
        $DescSin     = 'Sin Convenio';
        $valorarray  = 'convenios';
        $valorarray2 = 'convenio';
        $GetValRol   = 'GetEstructRol';
        break;
    case 'secciones':
        $tabla       = 'SECCION';
        $ColCodiSec  = 'SecCodi';
        $ColCodi     = 'Se2Codi';
        $ColDesc     = 'Se2Desc';
        $ColPersCodi = 'LegSec2';
        $DescSin     = 'Sin Seccion';
        $valorarray  = 'secciones';
        $valorarray2 = 'seccion';
        $valorarray3 = 'seccion';
        $GetValRol   = 'GetEstructRol';
        break;
    default:
        exit;
        // break;
}
// session_start();
UnsetGet('tk');
UnsetGet('q');
UnsetGet('_r');
UnsetGet('sect');
$respuesta  = '';
/** CONSULTAMOS SECTORES DEL ROL GET PARA LUEGO FILTRARLOS EN LA CONSULTA */
// $roles = sector_rol($_GET['_r']);
$roles = estructura_rol($GetValRol, $_GET['_r'], $_GET['e'], $valorarray2);
$roles2 = ($_GET['sect']) ? estructura_rol($GetValRol, $_GET['_r'], $_GET['e'], $valorarray3) : '';
// echo $roles;
/** -- */
$q=$_GET['q'];
$qu = (isset($_GET['q'])) ? "AND $tabla.$ColDesc LIKE '%$q%'" : "";
// print_r($qu);
// exit;

$token = token();
if ($_GET['tk'] == $token) {
    if (!isset($_GET['count'])) {
        if (isset($_GET['act'])) {
            $Codi = ($roles) ? "AND $tabla.$ColCodi IN ($roles)" : '';
        } else {
            $Codi = ($roles) ? "AND $tabla.$ColCodi NOT IN ($roles)" : '';
        }
        switch ($_GET['sect']) {
            case '1':
                $CodiSect =  ($roles2) ? "AND $tabla.$ColCodiSec NOT IN ($roles2)" : '';
                break;
            case '2':
                $CodiSect =  ($roles2) ? "AND $tabla.$ColCodiSec IN ($roles2)" : '';
                break;
            default:
                $CodiSect = '';
                break;
        }
        require __DIR__ . '../../config/conect_mssql.php';

        $q     = $_GET['q'];
     
        
        $query = "SELECT .$tabla.$ColCodi AS cod, .$tabla.$ColDesc AS 'desc',
        (SELECT COUNT(PERSONAL.LegNume) FROM PERSONAL WHERE PERSONAL.$ColPersCodi = $tabla.$ColCodi AND PERSONAL.LegFeEg = '17530101') AS cant_legajos_act,
        (SELECT COUNT(PERSONAL.LegNume) FROM PERSONAL WHERE PERSONAL.$ColPersCodi = $tabla.$ColCodi AND PERSONAL.LegFeEg != '17530101') AS cant_legajos_baja,
        $count_secciones /** SOLO PARA LOS SECTORES */
        (SELECT COUNT(PERSONAL.LegNume) FROM PERSONAL WHERE PERSONAL.$ColPersCodi = $tabla.$ColCodi) AS cant_legajos
        FROM $tabla WHERE $tabla.$ColCodi >= '0' $Codi $CodiSect $qu
        ORDER BY $tabla.$ColCodi ASC";
        // print_r($query);  exit;

        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $result  = sqlsrv_query($link, $query, $params, $options);
        $data    = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result)) :
                $cod               = $row['cod'];
                $desc              = $row['desc'];
                $desc              = (!$cod) ? $DescSin : $desc;
                $cant_legajos_act  = $row['cant_legajos_act'];
                $cant_legajos_baja = $row['cant_legajos_baja'];
                $cant_legajos      = $row['cant_legajos'];
                $cant_secciones    = ($_GET['e'] == 'sectores') ? $row['cant_secciones'] : '';
                
                $data[] = array(
                    'cod'               => $cod,
                    'desc'              => $desc,
                    'cant_legajos'      => $cant_legajos,
                    'cant_legajos_act'  => $cant_legajos_act,
                    'cant_legajos_baja' => $cant_legajos_baja,
                    'cant_secciones'    => $cant_secciones
                );
            endwhile;
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            // exit;
            $respuesta = array('success' => 'YES', 'error' => 'NO', $valorarray => $data);
        } else {
            $respuesta = array('success' => 'NO', 'error' => '1', $valorarray => 'No hay datos');
        }
    } else {
        $Codi = '';
        require_once __DIR__ . '../../config/conect_mssql.php';
        $query = "SELECT COUNT($tabla.$ColCodi) AS count_cod
        FROM $tabla";
        $result = sqlsrv_query($link, $query);
        while ($row = sqlsrv_fetch_array($result)) :
            $count_cod = $row['count_cod'];
        endwhile;
        sqlsrv_free_stmt($result);
        sqlsrv_close($link);
        $respuesta = array('success' => 'YES', 'error' => 'NO', 'count_cod' => $count_cod);
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'count_cod' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
