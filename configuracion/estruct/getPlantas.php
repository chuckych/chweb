<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");

require __DIR__ . '../../../config/conect_mssql.php';

$data = array();

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$query = "SELECT I.PlaDesc AS 'descripcion', I.PlaCodi AS 'codigo', (SELECT COUNT(1) FROM PERSONAL P WHERE P.LegPlan=I.PlaCodi AND P.LegNume > 0) AS 'cant' FROM PLANTAS I";
// print_r($query).PHP_EOL; exit;
$rs = sqlsrv_query($link, $query, $params, $options);
if (sqlsrv_num_rows($rs) > 0) {
    while ($r = sqlsrv_fetch_array($rs)) :
        $cant        = $r['cant'];
        $cant        = '<span class="float-left text-center" data-titlel="Total Personal: '.$cant.'"><span class="w35 badge badge-light border">'.$cant.'</span>';
        $codigo      = $r['codigo'];
        $descripcion = ($codigo=='0')?'Sin Planta':$r['descripcion'];
        $data[] = array($codigo, $descripcion, $cant );
    endwhile;
}
sqlsrv_free_stmt($rs);
sqlsrv_close($link);


$json_data = array(
    "data" => $data,
);

echo json_encode($json_data);
exit;
