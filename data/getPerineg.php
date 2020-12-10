<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
error_reporting(E_ALL);
ini_set('display_errors', '0');
UnsetGet('q');
session_start();
require __DIR__ . '../../config/conect_mssql.php';
$q2 = $_GET['q2'];
$query = "SELECT [InEgLega] ,[InEgFeIn] ,[InEgFeEg] ,[InEgCaus] ,[FechaHora] FROM PERINEG WHERE InEgLega = '$q2' ORDER BY InEgLega ,InEgFeIn";
//    print_r($query);

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();
$icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 opa5');

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $InEgLega  = $fila['InEgLega'];
        $InEgFeIn  = $fila['InEgFeIn']->format('d/m/Y');
        $InEgFeIn2  = $fila['InEgFeIn']->format('Ymd');
        $InEgFeEg  = $fila['InEgFeEg']->format('d/m/Y');
        $InEgFeEg = ($InEgFeEg == '01/01/1753') ? '-' : $InEgFeEg;
        $InEgCaus  = $fila['InEgCaus'];
        $FechaHora = $fila['FechaHora']->format('d/m/Y');
        $eliminar = '<div id="item-'.$InEgFeIn2.'"><div class="item">
        <a class="btn btn-light btn-sm delete_perineg" data="'.$InEgFeIn2.'" data2="'.$InEgLega.'" data3="true">'.$icon_trash.'</a></div></div>';
        // $eliminar ='';
        $data[] = array(
            "InEgLega"  => $InEgLega,
            "InEgFeIn"  => $InEgFeIn,
            "InEgFeEg"  => $InEgFeEg,
            "InEgCaus"  => $InEgCaus,
            "FechaHora" => $FechaHora,
            "eliminar"  => $eliminar,
            "null"      => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
