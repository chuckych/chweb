<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
require __DIR__ . '../../config/index.php';
// require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

$query_Fic="SELECT REGISTRO.RegLega, REGISTRO.RegHoRe, REGISTRO.RegFeAs FROM REGISTRO WHERE REGISTRO.RegLega = '121' AND REGISTRO.RegFeAs BETWEEN '20200801' AND '20200824' ORDER BY REGISTRO.RegFeAs,REGISTRO.RegLega,REGISTRO.RegFeRe,REGISTRO.RegHoRe";

// print_r($query_Fic); exit;

$stmt = sqlsrv_query($link, $query_Fic);
while ($row = sqlsrv_fetch_array($stmt)) {
    $array_Fic[]= array(
        'Legajo'  => $row['RegLega'],
        'RegHoRe' => $row['RegHoRe'],
        'Fecha'   => $row['RegFeAs']->format('d/m/Y'),
    );
}
sqlsrv_free_stmt($stmt);

$query = "SELECT FICHAS.FicLega AS 'Gen_Lega', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Gen_dia', PERSONAL.LegApNo AS 'Gen_Nombre', FICHAS.FicFech AS 'Gen_Fecha', DATEPART(dw,.FICHAS.FicFech) AS 'Gen_Dia_Semana', dbo.fn_HorarioAsignado( FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF ) AS 'Gen_Horario' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume AND FICHAS.FicLega = '121' AND FICHAS.FicFech BETWEEN '20200801' AND '20200824'";

$stmt = sqlsrv_query($link, $query);

while ($row = sqlsrv_fetch_array($stmt)) {

    foreach ($array_Fic as $key => $value) {
        if($row['Gen_Fecha']->format('d/m/Y')==$value['Fecha']){
            $array_users[]= array(
                'Legajo'  => $row['Gen_Lega'],
                'Nombre'  => $row['Gen_Nombre'],
                'Fecha'   => $row['Gen_Fecha']->format('d/m/Y'),
                'Dia'     => $row['Gen_dia'],
                'Horario' => $row['Gen_Horario'],
                'Fichadas'=> $array_Fic
            );
         }
        }
        
    }
    unset($array_Fic);
// print_r($query); exit;
sqlsrv_free_stmt($stmt);


echo json_encode($array_users);



sqlsrv_close($link);