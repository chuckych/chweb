<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

require __DIR__ . '../../config/index.php';
require __DIR__ . '../../config/conect_mssql.php';

$param   = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$Datos = explode('-', $_GET['Datos']);

$Fecha  = $Datos[1];
$Legajo = $Datos[0];

$data = array();

 /** fichas */
$query="SELECT FICHAS.FicHsTr AS HsTrabajadas, FICHAS.FicHsAT AS HsTrabajar
FROM FICHAS
WHERE FICHAS.FicLega='$Legajo' AND FICHAS.FicFech='$Fecha'";
// print_r($query);exit;

$result = sqlsrv_query($link, $query, $param, $options);

if (sqlsrv_num_rows($result) > 0) {
    while ($row_Hor = sqlsrv_fetch_array($result)) :
            $data[] = array(
                'HsTrabT'  => "Horas Trabajadas: "."<span class='ls1 fw5 HsTrab'>".$row_Hor['HsTrabajadas']."</span>",
                'HsaTrabT' => "Horas a Trabajar: "."<span class='ls1 fw5'>".$row_Hor['HsTrabajar']."</span>",
                'HsTrab'   => $row_Hor['HsTrabajadas'],
                'HsaTrab'  => $row_Hor['HsTrabajar'],
                'null'     => ''
            );
        endwhile;
        sqlsrv_free_stmt($result);
}
/** Fin HORAS */

echo json_encode(array('Horas'=> $data));
sqlsrv_close($link);
exit;



