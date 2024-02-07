<?php
require __DIR__ . '../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

$json_data = array();
$data = array();

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni = test_input(dr_fecha($DateRange[0]));
$FechaFin = test_input(dr_fecha($DateRange[1]));

$sql_query = "SELECT COUNT(NOVEDAD.NovTipo) AS 'Cantidad', NOVEDAD.NovTipo AS 'Tipo' FROM FICHAS3 INNER JOIN FICHAS ON FICHAS3.FicLega=FICHAS.FicLega AND FICHAS3.FicFech=FICHAS.FicFech INNER JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS3.FicTurn=1 AND FICHAS3.FicNove > 0 GROUP BY NOVEDAD.NovTipo ORDER BY NOVEDAD.NovTipo";

$sql_query = "SELECT COUNT(NOVEDAD.NovCodi) AS 'Cantidad', NOVEDAD.NovDesc AS 'Tipo'
 FROM FICHAS3
 INNER JOIN FICHAS ON FICHAS3.FicLega=FICHAS.FicLega AND FICHAS3.FicFech=FICHAS.FicFech
 INNER JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi
 WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS3.FicTurn=1 AND FICHAS3.FicNove > 0
 GROUP BY NOVEDAD.NovCodi, NOVEDAD.NovDesc
 ORDER BY NOVEDAD.NovCodi";
//  print_r($sql_query); exit;

$queryRecords = sqlsrv_query($link, $sql_query, $param, $options);
$totalRecords = sqlsrv_num_rows($queryRecords);

if ($totalRecords > 0) {
    while ($row = sqlsrv_fetch_array($queryRecords)):
        $data[] = array(
            'Cantidad' => $row['Cantidad'],
            'Tipo' => TipoNov($row['Tipo']),
        );
    endwhile;
    sqlsrv_free_stmt($queryRecords);
    sqlsrv_close($link);
}
$Cantidad = implode(',', array_column($data, 'Cantidad'));
$Tipo = implode(', ', array_column($data, 'Tipo'));
$TipoArr = array(array_column($data, 'Tipo'));
$CantArr = array(array_column($data, 'Cantidad'));

$rgbColor = array();
for ($i = 1; $i <= intval($totalRecords); $i++) {
    foreach (array('r', 'g', 'b') as $color) {
        $rgbColor[$color] = mt_rand(0, 255);
    }
    $colorArr[] = array('color' => 'rgb(' . (implode(",", $rgbColor)) . ',0.9)');
}
$colorArr = array(array_column($colorArr, 'color'));



$json_data = array(
    "recordsTotal" => intval($totalRecords),
    "CantArr" => $CantArr,
    "TipoArr" => $TipoArr,
    "colorArr" => $colorArr
);

echo json_encode($json_data);
// print_r($datos);
