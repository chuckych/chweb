<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

$dataNovedades = array();
$legajo=$valueAgrup['Legajo'];
$fecha=$valueAgrup['Fecha'];

$PorLegajo = ($_Por == 'Fech') ? '': "AND FICHAS3.FicLega = '$legajo'"; /** para filtrar por legajo */
$PorFecha = ($_Por == 'Fech') ? "WHERE FICHAS3.FicFech = '$fecha'": "WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin'"; /** Para filtrra por Fecha desde o desde hasta */

    $sql_query="SELECT FICHAS3.FicLega AS 'Legajo', 
    PERSONAL.LegApNo AS 'Nombre', 
    FICHAS3.FicFech AS 'Fecha', 
    dbo.fn_DiaDeLaSemana(FICHAS3.FicFech) AS 'Dia',
    FICHAS3.FicNove AS 'Codigo', 
    NOVEDAD.NovDesc AS 'Novedad', 
    NOVEDAD.NovTipo AS 'Tipo', 
    FICHAS3.FicHoras AS 'Horas', 
    FICHAS3.FicCaus AS 'CodigoCausa', 
    NOVECAUSA.NovCDesc AS 'Causa',
    'Justificada'= CASE FICHAS3.FicJust WHEN 1 THEN 'Si' ELSE 'No' END, FICHAS3.FicObse AS 'Observacion',
    dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario'
    FROM FICHAS3
    INNER JOIN PERSONAL ON FICHAS3.FicLega = PERSONAL.LegNume
    INNER JOIN FICHAS ON FICHAS3.FicLega = FICHAS.FicLega AND FICHAS3.FicFech = FICHAS.FicFech AND FICHAS3.FicTurn = FICHAS.FicTurn 
    LEFT JOIN NOVEDAD ON FICHAS3.FicNove = NOVEDAD.NovCodi 
    LEFT JOIN NOVECAUSA ON FICHAS3.FicNove = NOVECAUSA.NovCNove AND FICHAS3.FicCaus = NOVECAUSA.NovCCodi 
    $PorFecha $PorLegajo $FilterEstruct $FiltrosFichas ORDER BY FICHAS3.FicLega";

// h4($sql_query);exit;
// print_r($sql_query);

    $queryRecords = sqlsrv_query($link, $sql_query,$param, $options);
    while ($row = sqlsrv_fetch_array($queryRecords)) {

        $Legajo      = $row['Legajo'];
        $Nombre      = $row['Nombre'];
        $Fecha       = $row['Fecha']->format('d/m/Y');
        $Dia         = $row['Dia'];
        $Codigo      = $row['Codigo'];
        $Novedad     = $row['Novedad'];
        $Tipo        = $row['Tipo'];
        $Horas       = $row['Horas'];
        $CodigoCausa = $row['CodigoCausa'];
        $Causa       = $row['Causa'];
        $Justificada = $row['Justificada'];
        $Observacion = $row['Observacion'];
        $Horario     = $row['Horario'];
        
        $dataNovedades[] = array(
            'Legajo'      => $Legajo,
            'Nombre'      => $Nombre,
            'Fecha'       => $Fecha,
            'Dia'         => $Dia,
            'Codigo'      => $Codigo,
            'Novedad'     => $Novedad,
            'TipoN'       => ($Tipo),
            'Tipo'        => TipoNov($Tipo),
            'Horas'       => $Horas,
            'CodigoCausa' => $CodigoCausa,
            'Causa'       => $Causa,
            'Justificada' => $Justificada,
            'Observacion' => $Observacion,
            'Horario'     => $Horario,
        );
    }
sqlsrv_free_stmt($queryRecords);


foreach ($dataNovedades as $key => $ValueData) {
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
}

$todoc = ($dataNovedades['Codigo']);
$todo = HoraMin($dataNovedades['Horas']);

$tc    = (array_count_values($tc));
$ac    = (array_count_values($ac));
$ic    = (array_count_values($ic));
$sc    = (array_count_values($sc));
$vc    = (array_count_values($vc));
$lc    = (array_count_values($lc));
$acc   = (array_count_values($acc));
$suc   = (array_count_values($suc));
$arc   = (array_count_values($arc));

$todoc = (array_count_values($todoc));
