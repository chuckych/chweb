<?php
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

E_ALL();
$Datos = explode('-', $_GET['Datos']);
$Fecha = test_input($Datos[1]);
$Legajo = test_input($Datos[0]);
$data = array();
/** FICHADAS */
$queryFic = "SELECT REGISTRO.RegHoRe FROM REGISTRO WHERE REGISTRO.RegFeAs='$Fecha' AND REGISTRO.RegLega='$Legajo'";
/** NOVEDADES */
$queryNov = "SELECT FICHAS3.FicNove FROM FICHAS3 WHERE FICHAS3.FicLega='$Legajo' AND FICHAS3.FicFech='$Fecha' AND FICHAS3.FicTurn = 1 AND FICHAS3.FicNove > 0 AND FICHAS3.FicNoTi >=0 ";
/** HORAS */
$queryHora = "SELECT FICHAS1.FicHora AS Hora FROM FICHAS1 WHERE FICHAS1.FicLega='$Legajo' AND FICHAS1.FicFech='$Fecha' AND FICHAS1.FicTurn = 1";
/** OTRAS NOVEDADES */
$queryONov = "SELECT FICHAS2.FicONov AS FicONov FROM FICHAS2 WHERE FICHAS2.FicLega = '$Legajo' AND FICHAS2.FicFech = '$Fecha' AND FICHAS2.FicTurn = 1 AND FICHAS2.FicONov > 0";

// $Fic  = NumRowsQueryMSQL($queryFic);
// $Nov  = NumRowsQueryMSQL($queryNov);
// $ONov = NumRowsQueryMSQL($queryONov);
// $Hor  = NumRowsQueryMSQL($queryHora);

$data = array(
    // 'Fic'  => $Fic,
    // 'Nov'  => $Nov,
    // 'Hor'  => $Hor,
    // 'Onov' => $ONov,
);

echo json_encode($data);
sqlsrv_close($link);
exit;



