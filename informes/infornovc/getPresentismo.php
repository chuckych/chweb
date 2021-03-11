<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
// header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
// header("Pragma: no-cache"); // HTTP 1.0.
// header("Expires: 0"); // Proxies.

require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';

$data = array();

// error_reporting(E_ALL);
// ini_set('display_errors', '1');


require __DIR__ . '../valores.php';
function calcularMeses($a)
{
    $fecha1 = new DateTime($a[0]);
    $fecha2 = new DateTime($a[1]);
    //calcular con diff
    $fecha = $fecha1->diff($fecha2);

    $fechay = $fecha->y;
    $fecham = $fecha->m;
    $fechad = $fecha->d;
    $fechah = $fecha->h;
    $fechai = $fecha->i;

    $fechameses = $fechay * 12 + $fecham;
    return $fechameses;
}
function calcularFechas($a)
{
    $fecha1 = new DateTime($a[0]);
    $fecha2 = new DateTime($a[1]);
    //calcular con diff
    $fecha = $fecha1->diff($fecha2);

    $fechay = $fecha->y;
    $fecham = $fecha->m;
    $fechad = $fecha->d;

    // $tiempo = printf('%d años, %d meses, %d días', $fecha->y, $fecha->m, $fecha->d);
    // return $tiempo;
}

$TotalMeses = calcularMeses(array($FechaIni, $FechaFin)) + 1;
$TotalFechas = '';

$presentes = ($_SESSION['CONCEPTO_PRESENTES']);
$ausentes  = ($_SESSION['CONCEPTO_AUSENTES']);

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$query = "SELECT DISTINCT FICHAS.FicLega AS 'legajo', PERSONAL.LegApNo AS 'nombre',
(SELECT COUNT(FI.FicLega) FROM FICHAS FI INNER JOIN FICHAS3 ON FI.FicLega = FICHAS3.FicLega AND FI.FicFech = FICHAS3.FicFech AND FI.FicTurn = FICHAS3.FicTurn WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FI.FicHsTr = '00:00' AND  FICHAS.FicLega = FI.FicLega AND FICHAS3.FicNove IN ($presentes)) AS 'TotDiasPre',
(SELECT COUNT(FI2.FicLega) FROM FICHAS FI2 WHERE FI2.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FI2.FicHsTr > '00:00' AND  FICHAS.FicLega = FI2.FicLega) AS 'TotDiasPreHs', 
(SELECT COUNT(FI3.FicLega) FROM FICHAS FI3 INNER JOIN FICHAS3 ON FI3.FicLega = FICHAS3.FicLega AND FI3.FicFech = FICHAS3.FicFech AND FI3.FicTurn = FICHAS3.FicTurn WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FI3.FicHsTr = '00:00' AND  FICHAS.FicLega = FI3.FicLega AND FICHAS3.FicNove IN ($ausentes)) AS 'TotDiasAus' FROM FICHAS INNER JOIN FICHAS3 ON  FICHAS.FicLega = FICHAS3.FicLega AND FICHAS.FicFech = FICHAS3.FicFech AND FICHAS.FicTurn = FICHAS3.FicTurn INNER JOIN PERSONAL ON  FICHAS.FicLega = PERSONAL.LegNume WHERE  FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltrosFichas $FilterEstruct ORDER BY FICHAS.FicLega";
// print_r($query).PHP_EOL; exit;
function ConvMesesPresentes($TotalDiasPresentes, $TotalMeses, $TotalDias)
{
    if ($TotalDiasPresentes > 0) {
        $v = ($TotalDiasPresentes * $TotalMeses) / $TotalDias;
        /** Hacemos el calculo */
        $v = round($v, 2, PHP_ROUND_HALF_UP);
        return $v;
    }
}

    $rs = sqlsrv_query($link, $query, $param, $options);
    if (sqlsrv_num_rows($rs) > 0) {
        while ($r = sqlsrv_fetch_array($rs)) :

            $legajo         = $r['legajo'];
            $nombre         = $r['nombre'];
            $TotDiasPre     = $r['TotDiasPre'];
            $TotDiasPreHs   = $r['TotDiasPreHs'];
            $TotDiasAus     = $r['TotDiasAus'];
            // $TotDiasAus     = 130;
            // $presentes      = 54;
            $presentes      = $TotDiasPre + $TotDiasPreHs;
            $TotalDias      = $presentes + $TotDiasAus;
            $TotalDias      = $presentes + $TotDiasAus;
            $ConvPres       = (ConvMesesPresentes($presentes, $TotalMeses, $TotalDias));
            $ConvAus        = (ConvMesesPresentes($TotDiasAus, $TotalMeses, $TotalDias));
            $TotalMesesConv = ($ConvPres) + ($ConvAus);
            $TotalMesesConv = number_format($TotalMesesConv, 0);
            $ConvAus        = number_format($ConvAus, 2, ',', '.');
            $ConvPres       = number_format($ConvPres, 2, ',', '.');
            $data[] = array(
                'legajo'           => $legajo,
                'nombre'           => $nombre,
                'desde'            => Fech_Format_Var($FechaIni, 'd/m/Y'),
                'hasta'            => Fech_Format_Var($FechaFin, 'd/m/Y'),
                'totDiasPre'       => $TotDiasPre,
                'totDiasPreHs'     => $TotDiasPreHs,
                '_presentes'       => $presentes,
                '_ausentes'        => $TotDiasAus,
                '_totaldias'       => $TotalDias,
                '_totalmesesfecha' => $TotalMeses,
                '_convpres'        => ($ConvPres),
                '_convaus'         => ($ConvAus),
                '_totalmesesconv'  => $TotalMesesConv,
                'n'                => '',
            );
        endwhile;
    }
    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);


$json_data = array(
    "data"          => $data,
    // "a_FechaIni"    => Fech_Format_Var($FechaIni, 'd/m/Y'),
    // "b_FechaFin"    => Fech_Format_Var($FechaFin, 'd/m/Y'),
    // "c_TotalMeses"  => $TotalMeses,
    // "d_TotalTiempo" => $TotalFechas,
);

echo json_encode($json_data);
exit;
