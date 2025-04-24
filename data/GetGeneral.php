<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");

require __DIR__ . '/../config/index.php';
// require __DIR__ . '/../funciones.php';
E_ALL();
UnsetGet('tk');
// UnsetGet('q');
// UnsetGet('k');
$respuesta = '';
$respuesta = '';
$token = token();

$check_dl = (isset($_GET['_dl'])) ? "AND FICHAS.FicDiaL = '1'" : ''; /** Filtrar Dia Laboral */

$FechaIni = test_input($_GET['FechaIni']);
$FechaFin = test_input($_GET['FechaFin']);

$FechaPag = (isset($_GET['k'])) ? test_input($_GET['k']) : '';
$FechaIni = ((isset($_GET['k']))) ? $FechaPag : $FechaIni;


if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {

        require __DIR__ . '/../filtros/filtros.php';
        require __DIR__ . '/../config/conect_mssql.php';

        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        $query = "SELECT DISTINCT FICHAS.FicFech AS Fecha
FROM.FICHAS
    INNER JOIN.PERSONAL ON.FICHAS.FicLega =.PERSONAL.LegNume
WHERE PERSONAL.LegFeEg = '17530101'
    AND.FICHAS.FicFech BETWEEN '$_GET[FechaIni]' AND '$_GET[FechaFin]' $check_dl $filtros
ORDER BY.FICHAS.FicFech";

        $result = sqlsrv_query($link, $query, $params, $options);
        // print_r($query); exit;
        while ($row = sqlsrv_fetch_array($result)):
            $IndiFecha[] = ($row['Fecha']->format('Y-m-d'));
        endwhile;
        $IndiFecha = (isset($IndiFecha)) ? $IndiFecha : array($_GET['FechaIni']);
        sqlsrv_free_stmt($result);

        /** Query de primer registro de Fecha */
        $query = "SELECT  
        MIN(REGISTRO.RegFeAs) AS 'min_Fecha',
        MAX(REGISTRO.RegFeAs) AS 'max_Fecha'
        FROM REGISTRO,PERSONAL
        WHERE PERSONAL.LegFeEg = '17530101' AND REGISTRO.RegFeAs > '17530101'
        AND REGISTRO.RegLega = PERSONAL.LegNume";
        $result = sqlsrv_query($link, $query, $params, $options);
        // print_r($query);
        // exit;
        while ($row = sqlsrv_fetch_array($result)):

            $FirstDate = ($row['min_Fecha'] != null) ? $row['min_Fecha']->format('Y/m/d') : '';
            $FirstDate = ($row['min_Fecha'] != null) ? $row['min_Fecha']->format('Y') : '';
            $firstDate = array(
                'firstDate' => $FirstDate,
                'firstYear' => $FirstDate
            );
            $MaxDate = ($row['max_Fecha'] != null) ? $row['max_Fecha']->format('Y/m/d') : '';
            $MaxYear = ($row['max_Fecha'] != null) ? $row['max_Fecha']->format('Y') : '';
            $MaxYear3 = ($row['max_Fecha'] != null) ? $row['max_Fecha']->format('Ymd') : '';
            $maxDate = array(
                'maxDate' => $MaxDate,
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
        $ultimo = (array_key_last($IndiFecha));
        $primero = (array_values($IndiFecha)[$primero]);
        $ultimo = (array_values($IndiFecha)[$ultimo]);
        // exit;
        $k = (isset($_GET['k'])) ? $_GET['k'] : '0';
        $FechaPag = array_values($IndiFecha)[$k];
        $FechaIni = (isset($_GET['k'])) ? FechaString($FechaPag) : FechaString($primero);
        $FechaFin = FechaString($FechaFin);

        $query = "SELECT TOP 1 
        .FICHAS.FicFech AS Gen_Fecha, 
        DATEPART(dw, .FICHAS.FicFech) AS Gen_Dia_Semana
        FROM .FICHAS
        INNER JOIN .PERSONAL ON .FICHAS.FicLega = .PERSONAL.LegNume
        WHERE PERSONAL.LegFeEg = '17530101' AND .FICHAS.FicFech = '$FechaIni'
        $check_dl $filtros
        ORDER BY .FICHAS.FicFech";

        // print_r($query).PHP_EOL; exit;

        $result = sqlsrv_query($link, $query, $params, $options);
        $data = array();
        /** BUSCAMOS DENTRO DE FICHAS EL LEGAJO NOMBRE FECHA DIA HORARIO */
        if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result)):

                $Gen_Fecha = $row['Gen_Fecha']->format('d-m-Y');
                $Gen_Fecha2 = $row['Gen_Fecha']->format('Ymd');
                $Gen_Dia_Semana = $row['Gen_Dia_Semana'];

                $data[] = array(
                    'Fecha' => $Gen_Fecha,
                    'Fechastr' => $Gen_Fecha2,
                    'num_dia' => ($Gen_Dia_Semana)
                );
            endwhile;
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'firstDate' => $firstDate, 'maxDate' => $maxDate, 'rango_fecha' => ($IndiFecha), 'general' => ($data));
        } else {
            $data[] = array('Fecha' => ($maxDate2), 'Fechastr' => $maxDate3, 'num_dia' => ($maxDate4));
            $respuesta = array('success' => 'YES', 'error' => '0', 'firstDate' => $firstDate, 'maxDate' => $maxDate, 'rango_fecha' => ($IndiFecha), 'general' => ($data));
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => true, 'general' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => true, 'general' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// var_export($datos);
