<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
E_ALL();
UnsetGet('q');
session_start();
$data = array();
$token = sha1($_SESSION['RECID_CLIENTE']);
$pathApiCH = gethostCHWeb()."/".HOMEHOST."/api";

$q2 = $_GET['q2'];

$payload = array();

$sendApi['DATA'] = $sendApi['DATA'] ?? '';
$sendApi['MESSAGE'] = $sendApi['MESSAGE'] ?? '';

$sendApi = curlAPI("$pathApiCH/perineg/?Lega[]=$q2", $payload, 'GET', $token);
$sendApi = json_decode($sendApi, true);

// Flight::json($sendApi).exit;
// $icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 opa5');
$icon_trash='<span class="bi bi-trash fontq"></span>';
$icon_edit='<span class="bi bi-pen fontq"></span>';

if ($sendApi['MESSAGE'] == 'OK') {
    if ($sendApi['DATA']) {
        foreach ($sendApi['DATA'] as $key => $fila) {
            $InEgLega  = $fila['Lega'];
            $Diff  = $fila['Diff'];
            $InEgFeIn  = FechaFormatVar($fila['FeIn'], 'd/m/Y');
            $InEgFeIn2  = FechaFormatVar($fila['FeIn'],'Ymd');
            $InEgFeEg  = FechaFormatVar($fila['FeEg'],'d/m/Y');
            $InEgFeEg = ($fila['FeEg'] == '') ? '' : $InEgFeEg;
            $InEgCaus  = $fila['Caus'];
            $FechaHora = FechaFormatVar($fila['FechaHora'],'d/m/Y');
            $eliminar = '<div data-titlet="Eliminar registro"  id="item-'.$InEgFeIn2.'"><div class="item">
            <a class="btn btn-light btn-sm delete_perineg" data="'.$InEgFeIn2.'" data2="'.$InEgLega.'" data3="true">'.$icon_trash.'</a></div></div>';
            $editar = '<div data-titlet="Editar registro" id="item-'.$InEgFeIn2.'"><div class="item">
            <a class="btn btn-light btn-sm edita_perineg" data="'.$InEgFeIn.'" data2="'.$InEgLega.'" data3="true" data4="'.$InEgFeEg.'" data5="'.$InEgCaus.'">'.$icon_edit.'</a></div></div>';
            // $eliminar ='';
            $data[] = array(
                "InEgLega"  => $InEgLega,
                "InEgFeIn"  => $InEgFeIn,
                "InEgFeEg"  => $InEgFeEg,
                "InEgCaus"  => $InEgCaus,
                "FechaHora" => $FechaHora,
                "eliminar"  => $eliminar,
                "editar"    => $editar,
                "Diff"      => $Diff
            );
        }
    }
}


// require __DIR__ . '../../config/conect_mssql.php';
// $q2 = $_GET['q2'];
// $query = "SELECT [InEgLega] ,[InEgFeIn] ,[InEgFeEg] ,[InEgCaus] ,[FechaHora] FROM PERINEG WHERE InEgLega = '$q2' ORDER BY InEgLega ,InEgFeIn";
// //    print_r($query);

// $params  = array();
// $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
// $result  = sqlsrv_query($link, $query, $params, $options);
// $data    = array();
// $icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 opa5');

// if (sqlsrv_num_rows($result) > 0) {
//     while ($fila = sqlsrv_fetch_array($result)) {
//         $InEgLega  = $fila['InEgLega'];
//         $InEgFeIn  = $fila['InEgFeIn']->format('d/m/Y');
//         $InEgFeIn2  = $fila['InEgFeIn']->format('Ymd');
//         $InEgFeEg  = $fila['InEgFeEg']->format('d/m/Y');
//         $InEgFeEg = ($InEgFeEg == '01/01/1753') ? '-' : $InEgFeEg;
//         $InEgCaus  = $fila['InEgCaus'];
//         $FechaHora = $fila['FechaHora']->format('d/m/Y');
//         $eliminar = '<div id="item-'.$InEgFeIn2.'"><div class="item">
//         <a class="btn btn-light btn-sm delete_perineg" data="'.$InEgFeIn2.'" data2="'.$InEgLega.'" data3="true">'.$icon_trash.'</a></div></div>';
//         // $eliminar ='';
//         $data[] = array(
//             "InEgLega"  => $InEgLega,
//             "InEgFeIn"  => $InEgFeIn,
//             "InEgFeEg"  => $InEgFeEg,
//             "InEgCaus"  => $InEgCaus,
//             "FechaHora" => $FechaHora,
//             "eliminar"  => $eliminar,
//             "null"      => ''
//         );
//     }
// }
// sqlsrv_free_stmt($result);
// sqlsrv_close($link);
Flight::json(array("data" => $data));
exit;
