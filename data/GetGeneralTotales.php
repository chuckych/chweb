<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
// setlocale(LC_TIME, "spanish");
setlocale(LC_TIME, "es_ES");
require __DIR__ . '../../config/index.php';
// require __DIR__ . '../../funciones.php';
E_ALL();
UnsetGet('tk');
UnsetGet('q');
// UnsetGet('k');
$respuesta = '';
$token     = token();
$data      = array();

$FechaIni = test_input($_GET['FechaIni']);
$FechaFin = test_input($_GET['FechaFin']);
$FechaDia = test_input($_GET['dia']);

$FiltroFecha = (isset($_GET['dia'])&&(!empty($_GET['dia']))) ? "= '$FechaDia'" : "BETWEEN '$FechaIni' AND '$FechaFin'";
$check_dl = (isset($_GET['_dl'])) ? "AND FICHAS.FicDiaL = '1'": ''; /** Filtrar Dia Laboral */

// echo($FiltroFecha);
// exit;

if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {

        require __DIR__ . '../../filtros/filtros.php';

        require __DIR__ . '../../config/conect_mssql.php';

        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        $query_Horas="SELECT FICHAS1.FicHora AS Hora, MAX(TIPOHORA.THoDesc) AS HoraDesc,MAX(TIPOHORA.THoDesc2) AS HoraDesc2, SUM(LEFT(FICHAS1.FicHsHe,2)*60+RIGHT(FICHAS1.FicHsHe,2)) AS HsHechas, SUM(LEFT(FICHAS1.FicHsAu,2)*60+RIGHT(FICHAS1.FicHsAu,2)) AS HsCalculadas, SUM(LEFT(FICHAS1.FicHsAu2,2)*60+RIGHT(FICHAS1.FicHsAu2,2)) AS HsAutorizadas FROM FICHAS1,FICHAS,TIPOHORA, PERSONAL WHERE TIPOHORA.THoColu >'0' AND FICHAS.FicLega = PERSONAL.LegNume AND FICHAS1.FicFech $FiltroFecha AND FICHAS1.FicTurn=1 AND FICHAS1.FicLega=FICHAS.FicLega AND FICHAS1.FicFech=FICHAS.FicFech AND FICHAS1.FicHora=TIPOHORA.THoCodi $filtros $check_dl GROUP BY TIPOHORA.THoDesc, FICHAS1.FicHora ORDER by FICHAS1.FicHora";
        $result_Hor = sqlsrv_query($link, $query_Horas, $params, $options);

        // print_r($query_Horas); exit;
        
        if (sqlsrv_num_rows($result_Hor) > 0) {
            while ($row = sqlsrv_fetch_array($result_Hor)) :
                $Horas[] = array(
                    'Cod'          => $row['Hora'],
                    'Descripcion'  => $row['HoraDesc'],
                    'Descripcion2' => $row['HoraDesc2'],
                    'HsHechas'     => FormatHora($row['HsHechas']),
                    'HsCalc'       => FormatHora($row['HsCalculadas']),
                    'HsAuto'       => FormatHora($row['HsAutorizadas'])
                );
            endwhile;
            sqlsrv_free_stmt($result_Hor);
            
        } else {
            $Horas[]=array( 'Cod'=>'-', 'Descripcion'=>'-', 'Descripcion2'=>'-', 'HsHechas'=>'-', 'HsCalc'=>'-', 'HsAuto'=>'-' );
        }
        $query_Novedades="SELECT FICHAS3.FicNove AS Novedad, MAX(NOVEDAD.NovDesc) AS Descrip, MAX(NOVEDAD.NovTipo) AS Tipo, SUM(LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2)) AS Horas, SUM((LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2))*FICHAS3.FicJust) AS HorasJust, SUM(ABS((LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2))*(FICHAS3.FicJust-1))) AS HorasNoJust, COUNT(FICHAS3.FicNove) AS Dias, SUM(FICHAS3.FicJust) AS DiasJust, SUM(ABS((FICHAS3.FicJust-1))) AS DiasNoJust FROM FICHAS3,NOVEDAD,FICHAS,PERSONAL WHERE FICHAS3.FicFech $FiltroFecha AND FICHAS.FicLega = PERSONAL.LegNume AND FICHAS3.FicTurn=1 AND FICHAS3.FicNove > 0 AND FICHAS3.FicNove=NOVEDAD.NovCodi 
        AND FICHAS3.FicLega = FICHAS.FicLega AND FICHAS3.FicFech = FICHAS.FicFech AND FICHAS3.FicTurn = FICHAS.FicTurn $filtros $check_dl
        GROUP BY FICHAS3.FicNove ORDER BY FICHAS3.FicNove";
        $result_nov = sqlsrv_query($link, $query_Novedades, $params, $options);
        
        // print_r($query_Novedades); exit;
        

        if (sqlsrv_num_rows($result_nov) > 0) {
            while ($row = sqlsrv_fetch_array($result_nov)) :
                $Novedades[] = array(
                    'Cod'         => $row['Novedad'],
                    'Descripcion' => $row['Descrip'],
                    'Tipo'        => TipoNov($row['Tipo']),
                    'Horas'       => FormatHora($row['Horas']),
                    'HorasJust'   => FormatHora($row['HorasJust']),
                    'HorasNoJust' => FormatHora($row['HorasNoJust']),
                    'Dias'        => $row['Dias'],
                    'DiasJust'    => $row['DiasJust'],
                    'DiasNoJust'  => $row['DiasNoJust']
                );
            endwhile;
            sqlsrv_free_stmt($result_nov);
            
        } else {
            $Novedades[]=array( 'Cod'=>'-', 'Descripcion'=>'-', 'Tipo'=>'-', 'Horas'=>'-', 'HorasJust'=>'-', 'HorasNoJust'=>'-', 'Dias'=>'-', 'DiasJust'=>'-', 'DiasNoJust'=>'-', );
        }
        $query_NovTipo="SELECT MAX(NOVEDAD.NovTipo) AS Tipo, SUM(LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2)) AS Horas, SUM((LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2))*FICHAS3.FicJust) AS HorasJust, SUM(ABS((LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2))*(FICHAS3.FicJust-1))) AS HorasNoJust, COUNT(FICHAS3.FicNove) AS Dias, SUM(FICHAS3.FicJust) AS DiasJust, SUM(ABS((FICHAS3.FicJust-1))) AS DiasNoJust FROM FICHAS3,NOVEDAD,FICHAS,PERSONAL WHERE FICHAS3.FicFech $FiltroFecha AND FICHAS.FicLega=PERSONAL.LegNume AND FICHAS3.FicTurn=1 AND FICHAS3.FicNove >0 AND FICHAS3.FicNove=NOVEDAD.NovCodi AND FICHAS3.FicLega=FICHAS.FicLega AND FICHAS3.FicFech=FICHAS.FicFech AND FICHAS3.FicTurn=FICHAS.FicTurn $filtros $check_dl GROUP BY NOVEDAD.NovTipo ORDER BY NOVEDAD.NovTipo";
        $result_NovTipo = sqlsrv_query($link, $query_NovTipo, $params, $options);
        
        // print_r($query_NovTipo); exit;

        if (sqlsrv_num_rows($result_NovTipo) > 0) {
            while ($row = sqlsrv_fetch_array($result_NovTipo)) :
                $NovTipo[] = array(
                    'Tipo'        => $row['Tipo'],
                    'Descripcion' => TipoNov($row['Tipo']),
                    'Horas'       => FormatHora($row['Horas']),
                    'HorasJust'   => FormatHora($row['HorasJust']),
                    'HorasNoJust' => FormatHora($row['HorasNoJust']),
                    'Dias'        => $row['Dias'],
                    'DiasJust'    => $row['DiasJust'],
                    'DiasNoJust'  => $row['DiasNoJust']
                );
            endwhile;
            sqlsrv_free_stmt($result_NovTipo);
            
        } else {
            $NovTipo[]=array( 'Tipo'=>'-', 'Descripcion'=>'-', 'Horas'=>'-', 'HorasJust'=>'-', 'HorasNoJust'=>'-', 'Dias'=>'-', 'DiasJust'=>'-', 'DiasNoJust'=>'-' );
        }
        
        $data = array(
            'Horas'     => $Horas,
            'Novedades' => $Novedades,
            'NovTipo'   => $NovTipo
        );

        unset($Horas);
        unset($Novedades);
        unset($NovTipo);
        sqlsrv_close($link);
            
            $respuesta = $data;
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => true);
}
echo json_encode($respuesta);
// var_export($respuesta);
