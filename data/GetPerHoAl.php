<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
E_ALL();
UnsetGet('q2');
session_start();
require __DIR__ . '../../config/conect_mssql.php';
// $q = $_GET['q'];
$q2 = $_GET['q2'];
$query = "SELECT PERHOALT.LeHALega,PERHOALT.LeHAHora, PERHOALT.FechaHora, HORARIOS.HorDesc
FROM PERHOALT
INNER JOIN HORARIOS ON PERHOALT.LeHAHora = HORARIOS.HorCodi
WHERE PERHOALT.LeHALega = '$q2'
ORDER BY PERHOALT.LeHAHora";

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();
$icon_trash=imgIcon('trash3', 'Eliminar registro ' ,'w15 opa5');

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $LeHALega = $fila['LeHALega'];
        $LeHAHora = $fila['LeHAHora'];
        $HorDesc  = $fila['HorDesc'];
        $FechaHora    = $fila['FechaHora']->format('d/m/Y H:i');
        $eliminar     = '<div class="item"><a class="btn btn-light btn-sm delete_perhoalt" data="'.$LeHALega.'" data2="'.$LeHAHora.'" data3="true">'.$icon_trash.'</a></div>';
        $data[] = array(
            "LeHALega"  => $LeHALega,
            "LeHAHora"  => $LeHAHora,
            "HorDesc"   => $HorDesc,
            "FechaHora" => $FechaHora,
            "eliminar"  => $eliminar,
            "null"      => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
