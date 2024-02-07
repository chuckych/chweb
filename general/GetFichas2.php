<?php
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

E_ALL();
require __DIR__ . '../../config/conect_mssql.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$Datos = explode('-', $_GET['Datos']);

$Fecha = test_input($Datos[1]);
$Legajo = test_input($Datos[0]);

$data = array();

/** fichas */
$query = "SELECT TOP 1 FICHAS.FicHsTr, FICHAS.FicHsAT, FICHAS.FicDiaL, FICHAS.FicDiaF,FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicLega, dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS Horario, dbo.fn_STRMinutos(FicHsTr) AS FicHsTrMin, dbo.fn_STRMinutos(FicHsAT) AS FicHsATMin FROM FICHAS WHERE FICHAS.FicLega='$Legajo' AND FICHAS.FicFech='$Fecha'";
// print_r($query);exit;

$result = sqlsrv_query($link, $query, $param, $options);

if (sqlsrv_num_rows($result) > 0) {
    while ($row_Hor = sqlsrv_fetch_array($result)):
        $HorasNeg = ($row_Hor['FicHsTrMin'] < $row_Hor['FicHsATMin']) ? '1' : '0';
        $FicDiaL = ($row_Hor['FicDiaL'] == '0') ? '0' : '1';
        $data = array(
            'status' => "ok",
            'FicHsTr' => $row_Hor['FicHsTr'],
            'FicHsAT' => $row_Hor['FicHsAT'],
            'FicHorE' => $row_Hor['FicHorE'],
            'FicHorS' => $row_Hor['FicHorS'],
            'FicHorario' => $row_Hor['Horario'],
            'FicDiaL' => $row_Hor['FicDiaL'],
            'HorasNeg' => $HorasNeg
        );
    endwhile;
    sqlsrv_free_stmt($result);
    echo json_encode($data);
}
/** Fin HORAS */

// echo json_encode($data);
sqlsrv_close($link);
exit;



