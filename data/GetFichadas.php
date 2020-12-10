<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME,"es_ES");
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../config/index.php';
UnsetGet('tk');
UnsetGet('q');
$respuesta = '';
$respuesta = '';
$token     = token();

    $FechaIni=test_input($_GET['FechaIni']);
    $FechaFin=test_input($_GET['FechaFin']);

    $FechaPag= (isset($_GET['k'])) ? test_input($_GET['k']):'';
    $FechaIni = ((isset($_GET['k']))) ? $FechaPag : $FechaIni;
        
if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {

        require __DIR__ . '../../filtros/filtros.php';
        require __DIR__ . '../../config/conect_mssql.php';

        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        $query="SELECT DISTINCT REGISTRO.RegFeAs AS 'Fecha'
        FROM REGISTRO,PERSONAL,FICHAS
        WHERE REGISTRO.RegFeAs BETWEEN '$_GET[FechaIni]' AND '$_GET[FechaFin]'
        AND PERSONAL.LegFeEg = '17530101' AND REGISTRO.RegFeAs > '17530101'
        AND REGISTRO.RegLega = PERSONAL.LegNume 
        AND REGISTRO.RegLega = FICHAS.FicLega
        AND REGISTRO.RegFeAs = FICHAS.FicFech $filtros
        ORDER BY REGISTRO.RegFeAs";
        // print_r($query); exit;
    
        $result = sqlsrv_query($link, $query, $params, $options); 
        
        while ($row = sqlsrv_fetch_array($result)) :
            $IndiFecha[] = ($row['Fecha']->format('Y-m-d'));
        endwhile;
            $IndiFecha = (isset($IndiFecha)) ? $IndiFecha : array($_GET['FechaIni']);

        sqlsrv_free_stmt($result); 

        /** Query de primer registro de Fecha */
        $query="SELECT  
        MIN(REGISTRO.RegFeAs) AS 'min_Fecha',
        MAX(REGISTRO.RegFeAs) AS 'max_Fecha'
        FROM REGISTRO,PERSONAL
        WHERE PERSONAL.LegFeEg = '17530101' AND REGISTRO.RegFeAs > '17530101'
        AND REGISTRO.RegLega = PERSONAL.LegNume";
        $result = sqlsrv_query($link, $query, $params, $options); 
        // print_r($query); exit;
        while ($row = sqlsrv_fetch_array($result)) :
            $FirstDate = ($row['min_Fecha']!=null) ? $row['min_Fecha']->format('Y/m/d') :'';
            $FirstDate = ($row['min_Fecha']!=null) ? $row['min_Fecha']->format('Y') : '';
            $firstDate = array(
                'firstDate' => $FirstDate,
                'firstYear' => $FirstDate
            );
            $MaxDate = ($row['max_Fecha']!=null) ? $row['max_Fecha']->format('Y/m/d') :'';
            $MaxYear = ($row['max_Fecha']!=null) ? $row['max_Fecha']->format('Y') :'';
            $MaxYear3 = ($row['max_Fecha']!=null) ? $row['max_Fecha']->format('Ymd') :'';
            $maxDate = array(
                'maxDate'=> $MaxDate,
                'maxYear' => $MaxYear
            );
            $maxDate2 = $MaxDate;
            $maxDate3 = $MaxYear3;
            $maxDate4 = DiaSemana3($maxDate3);
        endwhile;
        sqlsrv_free_stmt($result); 

        $primero = (array_key_first($IndiFecha));
        $ultimo  = (array_key_last($IndiFecha));
        $primero = (array_values($IndiFecha)[$primero]);
        $ultimo  = (array_values($IndiFecha)[$ultimo]);
        $k        = (isset($_GET['k'])) ? $_GET['k'] :'0';
        $FechaPag = array_values($IndiFecha)[$k];
        $FechaIni = (isset($_GET['k'])) ? FechaString($FechaPag) : FechaString($primero);
        $FechaFin = FechaString($FechaFin);
        $Count_Per = (isset($_GET['_per'])) ? count($_GET['_per']) :'0'; /** Contamos filtros de legajo. */
        if($Count_Per == '1'){ /** Si la cuenta de filtro de legajo es igual a 1. Filtramos la fecha por el rango. Sino solo por el día. */
            $Filter_fecha = "WHERE REGISTRO.RegFeAs BETWEEN '$FechaIni' AND '$FechaFin'";
        }else{
            $Filter_fecha = "WHERE REGISTRO.RegFeAs = '$FechaIni'";
        }
        $query = "SELECT TOP 1 REGISTRO.RegFeAs AS 'Fic_Asignada', DATEPART(dw, REGISTRO.RegFeAs) AS Fic_Dia_semana FROM REGISTRO,PERSONAL,FICHAS
        $Filter_fecha AND REGISTRO.RegLega = PERSONAL.LegNume AND REGISTRO.RegLega = FICHAS.FicLega AND REGISTRO.RegFeAs = FICHAS.FicFech $filtros
        ORDER BY REGISTRO.RegLega";
        // print_r($query).PHP_EOL; exit;
        $result = sqlsrv_query($link, $query, $params, $options);
        $data  = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result)) :
                $Fic_Asignada  = $row['Fic_Asignada']->format('d-m-Y');
                $Fic_Dia_semana = $row['Fic_Dia_semana'];
              
                $data[] = array(
                    'num_dia'     => nombre_dia($Fic_Dia_semana),
                    'Fecha'       => ($Fic_Asignada),
                );

            endwhile;
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'firstDate'=>$firstDate, 'maxDate'=>$maxDate, 'rango_fecha' => ($IndiFecha),'fichadas' => ($data));
        } else {
            $data[] = array( 'Fecha' => ($maxDate2), 'Fechastr'  => $maxDate3,'num_dia' => ($maxDate4));
            $respuesta = array('success' => 'YES', 'error' => '0', 'firstDate'=>$firstDate, 'maxDate'=>$maxDate, 'rango_fecha' => ($IndiFecha), 'fichadas' => ($data));
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => true, 'fichadas' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => true, 'fichadas' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
