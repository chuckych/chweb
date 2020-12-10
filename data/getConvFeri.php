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
$query = "SELECT [CFConv] ,[CFFech] ,[CFDesc], [CFInFeTR], [CFCodM], [CFCodJ], [CFInfM], [CFInfJ] FROM CONVFERI WHERE [CFConv] = '$q' ORDER BY CFConv ,CFFech";
//    print_r($query);

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();
$icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 op5');

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        
        $CFFech   = $fila['CFFech']->format('Y-m-d');
        $CFFech2  = $fila['CFFech']->format('d/m/Y');
        $CFConv   = $fila['CFConv'];
        $CFCodM   = $fila['CFCodM'];
        $CFCodJ   = $fila['CFCodJ'];
        $CFInFeTR = $fila['CFInFeTR']=='0' ? '-':'&#10003;';
        $CFInfM   = $fila['CFInfM']=='0' ? '-':'&#10003;';
        $CFInfJ   = $fila['CFInfJ']=='0' ? '-':'&#10003;';
        $CFDesc   = $fila['CFDesc'];

        // $eliminar = '<input type="hidden" name="CFConv" value="'.$CFConv.'"><input type="hidden" name="CFDesc" value="'.$CFDesc.'"><input type="hidden" name="CFFech" value="'.$CFFech.'"><button name="" value="" type="submit" title="Eliminar" class="btn btn-light btn-sm">'.$icon_trash.'</button>';

        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_convFeri" data="'.$CFConv.'" data2="'.$CFDesc.'" data3="'.$CFFech.'" data4="true">'.$icon_trash.'</a></div>';

        $data[] = array(
            "CFConv"   => $CFConv,
            "CFFech"   => $CFFech2,
            "CFDesc"   => $CFDesc,
            "CFCodM"   => $CFCodM,
            "CFCodJ"   => $CFCodJ,
            "CFInFeTR" => $CFInFeTR,
            "CFInfM"   => $CFInfM,
            "CFInfJ"   => $CFInfJ,
            "eliminar" => $eliminar,
            "null"     => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
