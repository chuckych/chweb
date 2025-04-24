<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");

require __DIR__ . '/../../filtros/filtros.php';
require __DIR__ . '/../../config/conect_mssql.php';

$data = array();

E_ALL();


require __DIR__ . '/valores.php';
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
$ausentes = ($_SESSION['CONCEPTO_AUSENTES']);

$dias_franco = $_SESSION["DIAS_FRANCO"];
$dias_feriado = $_SESSION["DIAS_FERIADOS"];
$queryDL = $queryDF = '';
$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
if ($dias_franco) {
    $queryDL = "(SELECT COUNT(FicDL.FicLega) FROM FICHAS FicDL WHERE FicDL.FicFech BETWEEN '20200701' AND '20201231' AND FICHAS.FicLega = FicDl.FicLega AND FicDL.FicDiaL = 0 AND FicDL.FicDiaF = 0 ) AS 'TotalDiasFrancos',";
}
if ($dias_feriado) {
    $queryDF = "(SELECT COUNT(FicDF.FicLega) FROM FICHAS FicDF WHERE FicDF.FicFech BETWEEN '20200701' AND '20201231' AND FICHAS.FicLega = FicDF.FicLega AND FicDF.FicDiaF = 1 ) AS 'TotalDiasFeriados',";
}

$query = "SELECT DISTINCT FICHAS.FicLega AS 'legajo', PERSONAL.LegApNo AS 'nombre', $queryDL $queryDF 
(SELECT COUNT(FicAus.FicLega) FROM FICHAS FicAus INNER JOIN FICHAS3 ON FicAus.FicLega = FICHAS3.FicLega AND FicAus.FicFech = FICHAS3.FicFech WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND  FICHAS.FicLega = FicAus.FicLega AND FICHAS3.FicNove IN ($ausentes)) AS 'TotDiasAus',
(SELECT COUNT(Fic.FicLega) FROM FICHAS Fic WHERE Fic.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FICHAS.FicLega = Fic.FicLega) AS 'TotalDiasFichas' 
FROM FICHAS INNER JOIN FICHAS3 ON FICHAS.FicLega = FICHAS3.FicLega AND FICHAS.FicFech = FICHAS3.FicFech AND FICHAS.FicTurn = FICHAS3.FicTurn INNER JOIN PERSONAL ON  FICHAS.FicLega = PERSONAL.LegNume WHERE  FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltrosFichas $FilterEstruct ORDER BY FICHAS.FicLega";
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
    while ($r = sqlsrv_fetch_array($rs)):

        $legajo = $r['legajo'];
        $nombre = $r['nombre'];
        $TotalDiasFrancos = $r['TotalDiasFrancos'] ?? '';
        $TotalDiasFeriados = $r['TotalDiasFeriados'] ?? '';
        $TotalDiasFichas = $r['TotalDiasFichas'];
        $TotDiasAus = $r['TotDiasAus'];
        $TotDiasAus = $r['TotDiasAus'];
        $presentes = $TotalDiasFichas - $TotDiasAus;

        if ($dias_franco) {
            $presentes = $TotalDiasFichas - $TotDiasAus;
            $presentes = $presentes - $TotalDiasFrancos;
            $TotDiasAus = $TotDiasAus + $TotalDiasFrancos;
        }
        if ($dias_feriado) {
            $presentes = $TotalDiasFichas - $TotDiasAus;
            $presentes = $presentes - $TotalDiasFeriados;
            $TotDiasAus = $TotDiasAus + $TotalDiasFeriados;
        }

        $TotalDias = $presentes + $TotDiasAus;
        $ConvPres = (ConvMesesPresentes($presentes, $TotalMeses, $TotalDias));
        $ConvAus = (ConvMesesPresentes($TotDiasAus, $TotalMeses, $TotalDias));
        $TotalMesesConv = ($ConvPres) + ($ConvAus);
        $TotalMesesConv = number_format($TotalMesesConv, 0);
        $ConvAus = number_format($ConvAus, 2, ',', '.');
        $ConvPres = number_format($ConvPres, 2, ',', '.');
        $data[] = array(
            'legajo' => $legajo,
            'nombre' => $nombre,
            'desde' => Fech_Format_Var($FechaIni, 'd/m/Y'),
            'hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'),
            '_presentes' => $presentes,
            '_ausentes' => $TotDiasAus,
            '_totaldias' => $TotalDias,
            '_totalmesesfecha' => $TotalMeses,
            '_convpres' => ($ConvPres),
            '_convaus' => ($ConvAus),
            '_totalmesesconv' => $TotalMesesConv,
            'n' => '',
        );
    endwhile;
}
sqlsrv_free_stmt($rs);
sqlsrv_close($link);


$json_data = array(
    "data" => $data,
);

echo json_encode($json_data);
exit;
