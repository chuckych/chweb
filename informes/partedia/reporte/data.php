<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// session_start();
// header('Content-type: text/html; charset=utf-8');
// header("Content-Type: application/json");
// require __DIR__ . '../../../../config/index.php';

require __DIR__ . '../../../../filtros/filtros.php';
require __DIR__ . '../../../../config/conect_mssql.php';
require __DIR__ . '../valores.php';

$dataParte = array();

$legajo = test_input(FusNuloPOST('_l', 'vacio'));
if ($_PerSN=='on') {
    $sql_query="SELECT FICHAS.FicDiaL AS 'DiaL', FICHAS.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', FICHAS3.FicNove AS 'Codigo', NOVEDAD.NovDesc AS 'Novedad', NOVEDAD.NovTipo AS 'Tipo', FICHAS3.FicHoras AS 'Horas', FICHAS3.FicCaus AS 'CodigoCausa', NOVECAUSA.NovCDesc AS 'Causa', 'Justificada'=CASE FICHAS3.FicJust WHEN 1 THEN 'Si' ELSE 'No' END, FICHAS3.FicObse AS 'Observacion', dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume LEFT JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech AND FICHAS.FicTurn=FICHAS3.FicTurn LEFT JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi LEFT JOIN NOVECAUSA ON FICHAS3.FicNove=NOVECAUSA.NovCNove AND FICHAS3.FicCaus=NOVECAUSA.NovCCodi WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaIni' $FilterEstruct $FiltrosFichas ORDER BY FICHAS.FicLega ";
}else{
    $sql_query="SELECT FICHAS3.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', FICHAS3.FicNove AS 'Codigo', NOVEDAD.NovDesc AS 'Novedad', NOVEDAD.NovTipo AS 'Tipo', FICHAS3.FicHoras AS 'Horas', FICHAS3.FicCaus AS 'CodigoCausa', NOVECAUSA.NovCDesc AS 'Causa', 'Justificada'=CASE FICHAS3.FicJust WHEN 1 THEN 'Si' ELSE 'No' END, FICHAS3.FicObse AS 'Observacion', dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario' FROM FICHAS3 INNER JOIN PERSONAL ON FICHAS3.FicLega=PERSONAL.LegNume INNER JOIN FICHAS ON FICHAS3.FicLega=FICHAS.FicLega AND FICHAS3.FicFech=FICHAS.FicFech AND FICHAS3.FicTurn=FICHAS.FicTurn LEFT JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi LEFT JOIN NOVECAUSA ON FICHAS3.FicNove=NOVECAUSA.NovCNove AND FICHAS3.FicCaus=NOVECAUSA.NovCCodi WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaIni' $FilterEstruct $FiltrosFichas ORDER BY FICHAS3.FicLega";
}
// h4($sql_query); exit; 

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$queryRecords = sqlsrv_query($link, $sql_query,$param, $options);

while ($row = sqlsrv_fetch_array($queryRecords)) {

    $DiaL      = $row['DiaL'];
    $Legajo      = $row['Legajo'];
    $Nombre      = $row['Nombre'];
    $Codigo      = ($row['Codigo']);
    $Novedad     = (!$row['Novedad'])?'':$row['Novedad'];
    $Tipo        = ($row['Tipo']);
    $Horas       = ($row['Horas']);
    $CodigoCausa = ($row['CodigoCausa']);
    $Causa       = ($row['Causa']);
    $Justificada = $row['Justificada'];
    $Observacion = ($row['Observacion']);
    $Horario     = ($row['Horario']);

    $dataCount[]=1;
    
    $dataParte[] = array(
        'DiaL'      => $DiaL,
        'Legajo'      => $Legajo,
        'Nombre'      => $Nombre,
        'Codigo'      => ceronull($Codigo),
        'Novedad'     => $Novedad,
        'TipoN'       => ($Tipo),
        'Tipo'        => TipoNov($Tipo),
        'Horas'       => ceronull($Horas),
        'CodigoCausa' => $CodigoCausa,
        'Causa'       => ceronull($Causa),
        'Justificada' => $Justificada,
        'Observacion' => ceronull($Observacion),
        'Horario'     => $Horario,
    );
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);

foreach ($dataParte as $key => $ValueData) {
    /** Tardes */
    $tc[]=($ValueData['TipoN']=='0') ? (($ValueData['Tipo'])):'';
    $t[]=($ValueData['TipoN']=='0') ? (HoraMin($ValueData['Horas'])):'';
    /** Incumplimientos */
    $ic[]=($ValueData['TipoN']=='1') ? (($ValueData['Tipo'])):'';
    $i[]=($ValueData['TipoN']=='1') ? (HoraMin($ValueData['Horas'])):'';
    /** Salidas */
    $sc[]=($ValueData['TipoN']=='2') ? (($ValueData['Tipo'])):'';
    $s[]=($ValueData['TipoN']=='2') ? (HoraMin($ValueData['Horas'])):'';
    /** Vacaciones */
    $vc[] = ($ValueData['TipoN']=='6') ? (($ValueData['Tipo'])):'';
    $v[]  = ($ValueData['TipoN']=='6') ? (HoraMin($ValueData['Horas'])):'';
    /** Licencias */
    $lc[] = ($ValueData['TipoN']=='4') ? (($ValueData['Tipo'])):'';
    $l[]  = ($ValueData['TipoN']=='4') ? (HoraMin($ValueData['Horas'])):'';
    /** Accidentes */
    $acc[] = ($ValueData['TipoN']=='5') ? (($ValueData['Tipo'])):'';
    $ac1[]  = ($ValueData['TipoN']=='5') ? (HoraMin($ValueData['Horas'])):'';
    /** Suspensiones */
    $suc[] = ($ValueData['TipoN']=='7') ? (($ValueData['Tipo'])):'';
    $sus[]  = ($ValueData['TipoN']=='7') ? (HoraMin($ValueData['Horas'])):'';
    /** ART */
    $arc[] = ($ValueData['TipoN']=='7') ? (($ValueData['Tipo'])):'';
    $art[]  = ($ValueData['TipoN']=='7') ? (HoraMin($ValueData['Horas'])):'';
    /** Ausencias */
    $ac[] = ($ValueData['TipoN']=='3') ? (($ValueData['Tipo'])):'';
    $a[]  = ($ValueData['TipoN']=='3') ? (HoraMin($ValueData['Horas'])):'';

    $CountSNov[]=(!$ValueData['Novedad']) ? 1:'';
    $CountNov[]=($ValueData['Novedad']) ? 1:'';
}
$tc  = (array_count_values($tc));
$ac  = (array_count_values($ac));
$ic  = (array_count_values($ic));
$sc  = (array_count_values($sc));
$vc  = (array_count_values($vc));
$lc  = (array_count_values($lc));
$acc = (array_count_values($acc));
$suc = (array_count_values($suc));
$arc = (array_count_values($arc));
// $CountNov = ((($CountNov)));
// $CountNov=(count($dataCount));
$CountNov  = (array_sum($CountNov));
$CountSNov = (array_sum($CountSNov));

// echo json_encode($dataParte);
// print_r($dataLegajo);exit;
