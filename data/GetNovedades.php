<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME,"es_ES");
require __DIR__ . '../../config/index.php';
error_reporting(E_ALL);
ini_set('display_errors', '0');
UnsetGet('tk');
// UnsetGet('k');
$respuesta = '';
$respuesta = '';
$token = token();
    /** VALORES POR DEFECTO DE FECHA */
    $FechaIni=test_input($_GET['FechaIni']);
    $FechaFin=test_input($_GET['FechaFin']);

    $FechaPag= (isset($_GET['k'])) ? test_input($_GET['k']) : '';
    $FechaIni = ((isset($_GET['k']))) ? $FechaPag : $FechaIni;
            
if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {

        require __DIR__ . '../../filtros/filtros.php';

        require __DIR__ . '../../config/conect_mssql.php';

        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        /** Query de registro de Fecha */
        $query="SELECT 
        FICHAS3.FicFech AS 'Fecha' /** FECHA */
        FROM FICHAS3,PERSONAL WHERE PERSONAL.LegFeEg = '17530101' AND FICHAS3.FicFech  BETWEEN '$_GET[FechaIni]' AND '$_GET[FechaFin]' AND FICHAS3.FicLega = PERSONAL.LegNume  AND FICHAS3.FicNove > 0 $filtros GROUP BY FICHAS3.FicFech ORDER BY FICHAS3.FicFech asc";
        $result = sqlsrv_query($link, $query, $params, $options); 
        // print_r($query); exit;
        while ($row = sqlsrv_fetch_array($result)) :
            $IndiFecha[] = ($row['Fecha']->format('Y-m-d'));
        endwhile;
            $IndiFecha = (isset($IndiFecha)) ? $IndiFecha : array($_GET['FechaIni']);
        sqlsrv_free_stmt($result); 
        /** Query de primer registro de Fecha */
        $query="SELECT  
        MIN(FICHAS3.FicFech) AS 'min_Fecha',
        MAX(FICHAS3.FicFech) AS 'max_Fecha'
        FROM FICHAS3,PERSONAL
        WHERE PERSONAL.LegFeEg = '17530101'
        AND FICHAS3.FicLega = PERSONAL.LegNume 
        AND FICHAS3.FicNove > 0 $filtros";
        $result = sqlsrv_query($link, $query, $params, $options); 
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
            
            // $firstDate = array(
            //     'firstDate'=> $row['min_Fecha']->format('Y/m/d'),
            //     'firstYear' => $row['min_Fecha']->format('Y')
            // );
            // $maxDate = array(
            //     'maxDate'=> $row['max_Fecha']->format('Y/m/d'),
            //     'maxYear' => $row['max_Fecha']->format('Y')
            // );
            // $maxDate2 = $row['max_Fecha']->format('d-m-Y');
            // $maxDate3 = $row['max_Fecha']->format('Ymd');
            // $maxDate4 = DiaSemana_Numero($maxDate3);
        endwhile;
        sqlsrv_free_stmt($result); 

        $primero = (array_key_first($IndiFecha));
        $ultimo  = (array_key_last($IndiFecha));
        $primero = (array_values($IndiFecha)[$primero]);
        $ultimo  = (array_values($IndiFecha)[$ultimo]);
        // exit;
        $k        = (isset($_GET['k'])) ? $_GET['k'] :'0';
        $FechaPag = array_values($IndiFecha)[$k];
        $FechaIni = (isset($_GET['k'])) ? FechaString($FechaPag) : FechaString($primero);
        $FechaFin = FechaString($FechaFin);

        $Count_Per = (isset($_GET['_per'])) ? count($_GET['_per']) :'0'; /** Contamos filtros de legajo. */
        if($Count_Per == '1'){ /** Si la cuenta de filtro de legajo es igual a 1. Filtramos la fecha por el rango. Sino solo por el día. */
            $Filter_fecha = "AND FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin'";
        }else{
            $Filter_fecha = "AND FICHAS3.FicFech = '$FechaIni'";
        }
        $sql = "SELECT TOP 1 FICHAS3.FicFech AS 'nov_Fecha', DATEPART(dw,FICHAS3.FicFech) AS 'nov_dia_semana' FROM FICHAS3,PERSONAL WHERE PERSONAL.LegFeEg = '17530101' $Filter_fecha AND FICHAS3.FicLega = PERSONAL.LegNume AND FICHAS3.FicNove > 0 AND FICHAS3.FicNoTi >= 0  $filtros ORDER BY FICHAS3.FicFech ASC";
        // print_r($sql).PHP_EOL; exit;

        $rs = sqlsrv_query($link, $sql, $params, $options);

        $data  = array();
        if (sqlsrv_num_rows($rs) > 0) {
            while ($row = sqlsrv_fetch_array($rs)) :
                $nov_Fecha       = ($row['nov_Fecha']->format('d-m-Y'));
                $nov_Fecha2       = ($row['nov_Fecha']->format('Ymd'));
                $nov_dia_semana   = $row['nov_dia_semana'];
             
                $data[] = array(
                    'Fecha'           => ($nov_Fecha),
                    'Fechastr'         => ($nov_Fecha2),
                    'num_dia'     => ($nov_dia_semana),
                );
                    
            endwhile;
            
            sqlsrv_free_stmt($rs);
            sqlsrv_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'Fecha'=>$FechaIni,'firstDate'=>$firstDate, 'maxDate'=>$maxDate, 'rango_fecha' => ($IndiFecha), /**'legajos' => array_unique($IndiLega),*/  'novedades' => ($data));
        } else {
            $data[] = array( 'Fecha' => ($maxDate2), 'Fechastr'  => $maxDate3,'num_dia' => ($maxDate4));
            $respuesta = array('success' => 'YES', 'error' => '0', 'Fecha'=>$FechaIni,'firstDate'=>$firstDate, 'maxDate'=>$maxDate, 'rango_fecha' => ($IndiFecha), /**'legajos' => array_unique($IndiLega),*/ 'novedades' => ($data));
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => true, 'novedades' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => true, 'novedades' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
