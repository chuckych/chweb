<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
E_ALL();
UnsetGet('q2');
require __DIR__ . '../../config/conect_mssql.php';
// $q = $_GET['q'];
$q2 = $_GET['q2'];
$query = "SELECT OTROCONLEG.OTROConLega,OTROCONLEG.OTROConCodi, OTROConValor, OTROCONLEG.FechaHora, OTROCONCEP.OTROConDesc
FROM OTROCONLEG
INNER JOIN OTROCONCEP ON OTROCONLEG.OTROConCodi = OTROCONCEP.OTROConCodi
WHERE OTROCONLEG.OTROConLega = '$q2'
ORDER BY OTROCONLEG.OTROConCodi";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
$icon_trash = imgIcon('trash3', 'Eliminar registro ', 'w15 opa5');

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $OTROConLega = $fila['OTROConLega'];
        $OTROConCodi = $fila['OTROConCodi'];
        $OTROConValor = $fila['OTROConValor'];
        $OTROConDesc = $fila['OTROConDesc'];
        $FechaHora = $fila['FechaHora']->format('d/m/Y H:i');
        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_otrosconleg" data="' . $OTROConLega . '" data2="' . $OTROConCodi . '" data3="true">' . $icon_trash . '</a></div>';
        $OTROConValor = ($OTROConValor == '0') ? '0' : $OTROConValor;
        $data[] = array(
            "OTROConLega" => $OTROConLega,
            "OTROConCodi" => $OTROConCodi,
            "OTROConValor" => $OTROConValor,
            "OTROConDesc" => $OTROConDesc,
            "FechaHora" => $FechaHora,
            "eliminar" => $eliminar,
            "null" => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));
