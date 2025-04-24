<?php
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
E_ALL();

require __DIR__ . '/../config/index.php';
UnsetGet('tk');
UnsetGet('q');
$respuesta = '';
$respuesta = '';
$token = token();

$FechaIni = test_input($_GET['FechaIni']);
$FechaFin = test_input($_GET['FechaFin']);

$FechaPag = (isset($_GET['k'])) ? test_input($_GET['k']) : '';
$FechaIni = ((isset($_GET['k']))) ? $FechaPag : $FechaIni;

$FechaIni = FechaString($FechaIni);
$FechaFin = FechaString($FechaFin);

if ($_GET['tk'] == $token) {
    if (isset($_GET['tk']) && ($_GET['tk'] == $token)) {

        require __DIR__ . '/../filtros/filtros.php';
        require __DIR__ . '/../config/conect_mssql.php';

        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        /** Query de primer registro de Fecha */
        $query = "SELECT MIN(FICHAS3.FicFech) AS 'min_Fecha', MAX(FICHAS3.FicFech) AS 'max_Fecha'
        FROM FICHAS3,PERSONAL
        WHERE PERSONAL.LegFeEg = '17530101' AND FICHAS3.FicLega = PERSONAL.LegNume";
        $result = sqlsrv_query($link, $query, $params, $options);
        // print_r($query); exit;
        while ($row = sqlsrv_fetch_array($result)):
            $firstDate = array(
                'firstDate' => $row['min_Fecha']->format('Y/m/d'),
                'firstYear' => $row['min_Fecha']->format('Y')
            );
            $maxDate = array(
                'maxDate' => $row['max_Fecha']->format('Y/m/d'),
                'maxYear' => $row['max_Fecha']->format('Y')
            );
        endwhile;
        sqlsrv_free_stmt($result);
        $query = "SELECT PERSONAL.LegNume as Legajo,
        PERSONAL.LegApNo as Nombre,
        (Select SUM((LEFT(FICHAS1.FicHsAu,2)*60+RIGHT(FICHAS1.FicHsAu,2)) - (LEFT(FICHAS1.FicHsAu2,2)*60+RIGHT(FICHAS1.FicHsAu2,2))) as Horas From FICHAS1,TIPOHORA Where FICHAS1.FicLega = PERSONAL.LegNume and FICHAS1.FicFech >= '$FechaIni' and FICHAS1.FicFech <= '$FechaFin' and FICHAS1.FicHora = TIPOHORA.THoCodi and TIPOHORA.THoCtaH = 1 and FICHAS1.FicHsAu2 < FICHAS1.FicHsAu) as HorasEx,
        (Select SUM(LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2)) as Horas From FICHAS3,NOVEDAD Where FICHAS3.FicLega = PERSONAL.LegNume and FICHAS3.FicFech >= '$FechaIni' and FICHAS3.FicFech <= '$FechaFin' and FICHAS3.FicNove > 0 and FICHAS3.FicNove = NOVEDAD.NovCodi and NOVEDAD.NovTiCo = 1) as FrancoCompe1 ,
        (Select SUM(LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2)) as Horas From FICHAS3,NOVEDAD Where FICHAS3.FicLega = PERSONAL.LegNume and FICHAS3.FicFech >= '$FechaIni' and FICHAS3.FicFech <= '$FechaFin' and FICHAS3.FicNove > 0 and FICHAS3.FicNove = NOVEDAD.NovCodi and NOVEDAD.NovTiCo = 4) as FrancoCompe2 ,
        (Select SUM(LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2)) as Horas From FICHAS3,NOVEDAD Where FICHAS3.FicLega = PERSONAL.LegNume and FICHAS3.FicFech >= '$FechaIni' and FICHAS3.FicFech <= '$FechaFin' and FICHAS3.FicNove > 0 and FICHAS3.FicNove = NOVEDAD.NovCodi and NOVEDAD.NovTiCo = 2) as JornadaReducida1 ,
        (Select SUM(LEFT(FICHAS3.FicHoras,2)*60+RIGHT(FICHAS3.FicHoras,2)) as Horas From FICHAS3,NOVEDAD Where FICHAS3.FicLega = PERSONAL.LegNume and FICHAS3.FicFech >= '$FechaIni' and FICHAS3.FicFech <= '$FechaFin' and FICHAS3.FicNove > 0 and FICHAS3.FicNove = NOVEDAD.NovCodi and NOVEDAD.NovTiCo = 5) as JornadaReducida2 
        FROM PERSONAL Where PERSONAL.LegNume >= 1 $filtros Order By PERSONAL.LegNume";

        // print_r($query); exit;

        $result = sqlsrv_query($link, $query, $params, $options);
        $data = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result)):

                $Legajo = $row['Legajo'];
                $Nombre = $row['Nombre'];
                $HorasEx = FormatHora($row['HorasEx']);
                $FrancoCompe1 = FormatHora($row['FrancoCompe1']);
                $FrancoCompe2 = FormatHora($row['FrancoCompe2']);
                $JornadaReducida1 = FormatHora($row['JornadaReducida1']);
                // $JornadaReducida2 = FormatHora($row['JornadaReducida2']);
                $ctacteX = $row['HorasEx'] - $row['JornadaReducida1'];
                $ctacte = $ctacteX - $row['FrancoCompe1'];
                if ($ctacte) {
                    $data[] = array(
                        'Legajo' => $Legajo,
                        'Nombre' => $Nombre,
                        'HorasEx' => $HorasEx,
                        'FrancoCompe1' => $FrancoCompe1,
                        // 'FrancoCompe2' => $FrancoCompe2,
                        'JornadaReducida1' => $JornadaReducida1,
                        'ctacte' => ($ctacte),
                    );
                }
                // unset($Fic_Hora);
            endwhile;
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            $respuesta = array('success' => 'YES', 'error' => '0', 'firstDate' => $firstDate, 'maxDate' => $maxDate, 'cta_horas' => ($data));
        } else {
            $respuesta = array('success' => 'NO', 'error' => true, 'firstDate' => $firstDate, 'maxDate' => $maxDate, 'cta_horas' => 'No hay Datos');
        }
    } else {
        $respuesta = array('success' => 'NO', 'error' => true, 'cta_horas' => 'error');
    }
} else {
    $respuesta = array('success' => 'NO', 'error' => true, 'cta_horas' => 'ERROR TOKEN');
}
$datos = array($respuesta);
echo json_encode($datos);
// print_r($datos);
