<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
// date_default_timezone_set('America/Argentina/Buenos_Aires');
// setlocale(LC_TIME, "spanish");
require __DIR__ . '../../funciones.php';
// session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');
UnsetGet('tk');
UnsetGet('q');
$respuesta  = '';
$getFicLega = (isset($_GET['_l'])) ? implode(",",$_GET['_l']) : '';
$FicLega    = (isset($_GET['_l'])) ? "AND PERSONAL.LegNume IN ($getFicLega)" : "";
$token = token();
    if ($_GET['tk'] == $token) {
        require_once __DIR__ . '../../config/conect_mssql.php';
        $q = $_GET['q'];
        $query = "SELECT DISTINCT
        PERSONAL.LegNume AS 'pers_legajo', 
        PERSONAL.LegApNo AS 'pers_nombre', 
        PERSONAL.LegDocu AS 'pers_dni'
        FROM personal
        WHERE PERSONAL.LegNume > '0' AND PERSONAL.LegFeEg = '1753-01-01 00:00:00.000'
        $FicLega
        ORDER BY pers_nombre ASC";
        // print_r($query);
        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $result  = sqlsrv_query($link, $query, $params, $options);
        $data    = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result)) :
            $pers_legajo = $row['pers_legajo'];
            $pers_nombre = $row['pers_nombre'];
            $pers_dni    = $row['pers_dni'];
                $data[] = array(
                    'l' => $pers_legajo,
                    'n' => $pers_nombre,
                    'd' => $pers_dni
                );
            // $data[]=array('id'=>"$pers_legajo",'text'=>$pers_nombre);
            endwhile;
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            $respuesta = array('success' => 'YES', 'error' => 'NO', 'impo_personal' => $data);
        } else {
            // $respuesta[] = array('success' => 'NO', 'error' => '1', 'DATOS' => 'NO');
            $data[] = array('text' => 'Empleado no encontrado');
        }
    } else {
    $respuesta = array('success' => 'NO', 'error' => '1', 'impo_personal' => 'ERROR TOKEN');
}
// $datos = array($respuesta);
echo json_encode($respuesta);
// print_r($datos);
