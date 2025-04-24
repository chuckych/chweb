<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '/../../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis = date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_Fichadas_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();

ExisteModRol('34');

E_ALL();

$_POST['datos'] = $_POST['datos'] ?? '';
$_POST['datos'] = json_decode($_POST['datos'], true);

if (intval($_POST['datos']['agrup']) == 1) {
    require __DIR__ . '/inforExcelLegajo.php';
} else {
    require __DIR__ . '/inforExcelEstruct.php';
}