<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
error_reporting(E_ALL);
ini_set('display_errors', '0');
UnsetGet('q');
session_start();
require __DIR__ . '../../config/conect_mssql.php';
$q = $_GET['q'];
$query = "SELECT
        [CVConv] ,[CVAnios] ,[CVMeses] ,[CVDias] ,[FechaHora]
       FROM CONVVACA WHERE CVConv = '$q'
       ORDER BY CVConv ,CVAnios ,CVMeses";
//    print_r($query);

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();
$icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 opa5');


if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        //$eliminar = '<input type="hidden" name="del_cod" value="'.$fila['CVConv'].'"><input type="hidden" name="del_anios" value="'.$fila['CVAnios'].'"><input type="hidden" name="del_meses" value="'.$fila['CVMeses'].'"><input type="hidden" name="del_dias" value="'.$fila['CVDias'].'"><button name="" value="" type="submit" title="Eliminar" class="btn btn-light btn-sm">'.$icon_trash.'</button>';
        
        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_convVaca" data="'.$fila['CVConv'].'" data2="'.$fila['CVAnios'].'" data3="'.$fila['CVMeses'].'" data4="'.$fila['CVDias'].'" data5="true">'.$icon_trash.'</a></div>';
    
        $CVConv    = $fila['CVConv'];
        $CVAnios   = $fila['CVAnios'];
        $CVMeses   = $fila['CVMeses'];
        $CVDias    = $fila['CVDias'];
        $FechaHora = $fila['FechaHora']->format('Y-m-d H:i:s');
        $data[] = array(
            "CVConv"    => $CVConv,
            "CVAnios"   => $CVAnios,
            "CVMeses"   => $CVMeses,
            "CVDias"    => $CVDias,
            "FechaHora" => $FechaHora,
            "eliminar"  => $eliminar,
            "null"      => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
