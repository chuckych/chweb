<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
error_reporting(E_ALL);
ini_set('display_errors', '0');
UnsetGet('q2');
session_start();
require __DIR__ . '../../config/conect_mssql.php';
$q2 = $_GET['q2'];
$query = "SELECT [IDCodigo] ,[IDFichada], [FechaHora], [IDVence], [IDTarjeta] FROM IDENTIFICA WHERE IDLegajo = '$q2' ORDER BY IDCodigo";
//    print_r($query);

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();
$icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 opa5');

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $IDCodigo   = $fila['IDCodigo'];
        $IDTarjeta   = $fila['IDTarjeta'];
        $IDVence = $fila['IDVence']->format('d/m/Y');
        $IDVence    = $IDVence=='01/01/1753' ? '-':$IDVence;
        $IDFichada  = $fila['IDFichada']=='0' ? '-':'&#10003;';
        $FechaHora  = $fila['FechaHora']->format('d/m/Y');
        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_identifica" data="'.$IDCodigo.'" data2="'.$q2.'" data3="true">'.$icon_trash.'</a></div>';
        $data[] = array(
            "IDCodigo"  => $IDCodigo,
            "IDFichada" => $IDFichada,
            "IDTarjeta" => $IDTarjeta,
            "IDVence"   => $IDVence,
            "FechaHora" => $FechaHora,
            "eliminar"  => $eliminar,
            "null"      => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
