<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "spanish");
error_reporting(E_ALL);
ini_set('display_errors', '0');
require __DIR__ . '../../config/index.php';

switch ($_GET['e']) {
    case 'secciones':
        $tabla       = 'SECCION';
        $ColCodiSec  = 'SecCodi';
        $ColCodi     = 'Se2Codi';
        $ColDesc     = 'Se2Desc';
        $ColPersCodi = 'LegSec2';
        $DescSin     = 'Sin Seccion';
        $valorarray  = 'sectores';
        $valorarray2 = 'seccion';
        $valorarray3 = 'seccion';
        $GetValRol   = 'GetEstructRol';
        break;
    default:
        exit;
        break;
}
// session_start();
UnsetGet('tk');
UnsetGet('q');
UnsetGet('_r');
UnsetGet('sect');
$respuesta  = '';
/** CONSULTAMOS SECTORES DEL ROL GET PARA LUEGO FILTRARLOS EN LA CONSULTA */
// $roles = sector_rol($_GET['_r']);

$url   = host() . "/" . HOMEHOST . "/data/GetEstructRol.php?tk=" . token() . "&_r=" . $_GET['_r'] . "&e=" . $_GET['e'] . "&sect=1";
// echo $url; br();
// $json  = file_get_contents($url);
// $array = json_decode($json, TRUE);
$array = json_decode(getRemoteFile($url), true);
$val_roles = (!$array[0]['error']) ? implode(",", $array[0]['sector']) : '';
$roles2 = (!$array[0]['error']) ? "$val_roles" : "";

$roles = estructura_rol($GetValRol, $_GET['_r'], $_GET['e'], $valorarray3);
// print_r($roles);
// $roles2 = ($_GET['sect']) ? estructura_rol($GetValRol, $_GET['_r'],$_GET['e'], $valorarray3) : '';
// echo $roles;
/** -- */
$FiltroSector = ($_GET['sector']) ? "AND SECCION.SecCodi = '$_GET[sector]'" : '';

$token = token();
if ($_GET['tk'] == $token) {
    if (!isset($_GET['count'])) {
        if (isset($_GET['act'])) {
            $Codi = ($roles) ? "AND CONCAT(SECCION.SecCodi,SECCION.Se2Codi) IN ($roles)" : '';
        } else {
            $Codi = ($roles) ? "AND CONCAT(SECCION.SecCodi,SECCION.Se2Codi) NOT IN ($roles)" : "";
            // $Codi = ($roles) ? "AND SECCION.Se2Codi NOT IN ($roles)" : "";
        }
        require __DIR__ . '../../config/conect_mssql.php';
        $q     = $_GET['q'];
   
        $query = "SELECT SECCION.Se2Codi AS cod, SECCION.Se2Desc AS 'desc',SECCION.SecCodi AS cod_sector,
        (SELECT COUNT(PERSONAL.LegNume) FROM PERSONAL WHERE PERSONAL.LegSec2 = SECCION.Se2Codi AND PERSONAL.LegFeEg = '17530101' AND PERSONAL.LegSect = SECCION.SecCodi) AS cant_legajos_act,
        (SELECT COUNT(PERSONAL.LegNume) FROM PERSONAL WHERE PERSONAL.LegSec2 = SECCION.Se2Codi AND PERSONAL.LegFeEg != '17530101' AND PERSONAL.LegSect = SECCION.SecCodi) AS cant_legajos_baja,
        (SELECT COUNT(PERSONAL.LegNume) FROM PERSONAL WHERE PERSONAL.LegSec2 = SECCION.Se2Codi AND PERSONAL.LegSect = SECCION.SecCodi) AS cant_legajos
        FROM SECCION WHERE SECCION.Se2Codi >= '0' $FiltroSector $Codi
        ORDER BY SECCION.Se2Codi";

        // print_r($query);
        // exit;

        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $result  = sqlsrv_query($link, $query, $params, $options);
        $data    = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result)) :
                $cod               = $row['cod'];
                $desc              = $row['desc'];
                $desc              = (!$cod) ? 'Sin Sección' : $desc;
                $cant_legajos_act  = $row['cant_legajos_act'];
                $cant_legajos_baja = $row['cant_legajos_baja'];
                $cant_legajos      = $row['cant_legajos'];
                $cod_sector        = $row['cod_sector'];
                $data[] = array(
                    'cod'               => $cod,
                    'desc'              => $desc,
                    'cant_legajos_act'  => $cant_legajos_act,
                    'cant_legajos_baja' => $cant_legajos_baja,
                    'cant_legajos'      => $cant_legajos,
                    'cod_sector'        => $cod_sector
                );
            endwhile;
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'secciones' => $data);
        } else {
            $respuesta = array('success' => 'NO', 'error' => '1', 'secciones' => 'No hay datos');
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
        $respuesta = array('success' => 'YES', 'error' => '0', 'count_cod' => $count_cod);
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'count_cod' => 'ERROR TOKEN');
}
$datos = ($respuesta);
echo json_encode($datos);
// print_r($datos);
