<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

$json_data = array();
$data      = array();

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

$sql_query = "SELECT 
 FICHAS1.FicHora AS 'Hora', 
 MAX(TIPOHORA.THoDesc) AS 'HoraDesc', 
 MAX(TIPOHORA.THoDesc2) AS 'HoraDesc2', 
 SUM(LEFT(FICHAS1.FicHsHe, 2) * 60 + RIGHT(FICHAS1.FicHsHe, 2) ) AS 'HsHechas', 
 SUM(LEFT(FICHAS1.FicHsAu, 2) * 60 + RIGHT(FICHAS1.FicHsAu, 2) ) AS 'HsCalculadas', 
 SUM(LEFT(FICHAS1.FicHsAu2, 2) * 60 + RIGHT(FICHAS1.FicHsAu2, 2) ) AS 'HsAutorizadas' 
 FROM FICHAS1, FICHAS, TIPOHORA, PERSONAL WHERE TIPOHORA.THoColu >='0' AND FICHAS1.FicHora != '90' AND FICHAS.FicLega=PERSONAL.LegNume AND FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS1.FicTurn = 1 AND FICHAS1.FicLega=FICHAS.FicLega AND FICHAS1.FicFech=FICHAS.FicFech AND FICHAS1.FicHora=TIPOHORA.THoCodi GROUP BY TIPOHORA.THoDesc, FICHAS1.FicHora ORDER by FICHAS1.FicHora";

//  print_r($sql_query); exit;

$queryRecords = sqlsrv_query($link, $sql_query, $param, $options);
$totalRecords = sqlsrv_num_rows($queryRecords);

if ($totalRecords > 0) {
    while ($row = sqlsrv_fetch_array($queryRecords)) :
        $HorasAuto2 = ($row['HsAutorizadas']);
        $HorasAuto = MinHora($row['HsAutorizadas']);
        // $HorasAuto = date("H:i",strtotime($HorasAuto));
        if ($row['HsAutorizadas'] > 0) {
            $data[] = array(
                'HsAutorizadas' => $HorasAuto,
                'HoraDesc2'     => ($row['HoraDesc2']),
            );
            $data2[] = array(
                'name' => ($row['HoraDesc2']),
                'y'    => ($HorasAuto2),
                'horas'    => ($HorasAuto),
            );
        }
    endwhile;
    sqlsrv_free_stmt($queryRecords);
    sqlsrv_close($link);
}
$HsAutorizadas = implode(',', array_column($data, 'HsAutorizadas'));
$HoraDesc2     = implode(', ', array_column($data, 'HoraDesc2'));
$HoraDesc2Arr  = array(array_column($data, 'HoraDesc2'));
$CantArr       = array(array_column($data, 'HsAutorizadas'));
$dataArray = ($data2);

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
    "CantArr"      => ($CantArr),
    "dataArray"    => ($dataArray),
    "HoraDesc2Arr" => $HoraDesc2Arr,
    "colorArr"     => $colorArr
);

echo json_encode($json_data);
// print_r($datos);
