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

$query = "SELECT PERRELO.RelLega AS Legajo ,PERRELO.RelFech AS Desde, PERRELO.RelFech2 AS Vence, PERRELO.RelReMa AS Marca ,PERRELO.RelRelo AS Reloj ,RELOJES.RelDeRe AS Descrip, RELOJES.RelSeri AS Serie FROM PERRELO,RELOJES WHERE PERRELO.RelLega = '$q2' AND PERRELO.RelReMa = RELOJES.RelReMa AND PERRELO.RelRelo = RELOJES.RelRelo ORDER BY PERRELO.RelLega,PERRELO.RelReMa,PERRELO.RelRelo";
//    print_r($query);

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();
$icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 opa4');


if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {

        $Legajo = $fila['Legajo'];
        $Reloj   = $fila['Reloj'];
        $Serie   = $fila['Serie'];
        $Descrip = $fila['Descrip'];
        $Desde   = $fila['Desde']->format('d-m-Y');
        $Vence   = $fila['Vence']->format('d-m-Y');
        $Marca   = $fila['Marca'];
        
        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_perrelo" data="'.$Reloj.'" data2="'.$Marca.'" data3="'.$Legajo.'" data4="true">'.$icon_trash.'</a></div>';

        switch ($Marca) {
            case '0'  : 
               $Marca = 'ASCII';
                break;
            case '1'  : 
               $Marca = 'Macronet';
                break;
            case '10' : 
               $Marca = 'Hand Reader';
                break;
            case '21' : 
               $Marca = 'SB CAuto';
                break;
            case '30' : 
               $Marca = 'ZKTeco';
                break;
            default   : 
                $Marca = $fila['Marca'];
                break;
        }
        $data[] = array(
            "Serie"    => $Serie,
            "Reloj"    => $Reloj,
            "Descrip"  => $Descrip,
            "Desde"    => $Desde,
            "Vence"    => $Vence,
            "Marca"    => $Marca,
            "eliminar" => $eliminar,
            "null"     => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
