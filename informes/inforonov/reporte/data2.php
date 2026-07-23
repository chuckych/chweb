<?php
E_ALL();
$_Por ??= '';
$FechaIni ??= '';
$FechaFin ??= '';
$FilterEstruct ??= '';
$FiltrosFichas ??= '';
$valueAgrup ??= [];
$dataNovedades = [];
$legajo = $valueAgrup['Legajo'] ?? '';
$fecha = $valueAgrup['Fecha'] ?? '';

$PorLegajo = ($_Por == 'Fech') ? '' : "AND FICHAS2.FicLega = '$legajo'"; /** para filtrar por legajo */
$PorFecha = ($_Por == 'Fech') ? "WHERE FICHAS2.FicFech = '$fecha'" : "WHERE FICHAS2.FicFech BETWEEN '$FechaIni' AND '$FechaFin'"; /** Para filtrra por Fecha desde o desde hasta */

$sql_query = "SELECT FICHAS2.FicLega AS 'Legajo', 
    PERSONAL.LegApNo AS 'Nombre', 
    FICHAS2.FicFech AS 'Fecha', 
    dbo.fn_DiaDeLaSemana(FICHAS2.FicFech) AS 'Dia',
    FICHAS2.FicONov AS 'Codigo', 
    OTRASNOV.ONovDesc AS 'Novedad', 
    FICHAS2.FicValor AS 'Valor',
    CASE OTRASNOV.ONovTipo WHEN 1 THEN 'Horas' ELSE 'Valor' END AS 'TipoOnov', 
    FICHAS2.FicObsN AS 'Observacion',
    dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario'
    FROM FICHAS2
    INNER JOIN PERSONAL ON FICHAS2.FicLega = PERSONAL.LegNume
    INNER JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega AND FICHAS2.FicFech = FICHAS.FicFech AND FICHAS2.FicTurn = FICHAS.FicTurn 
    LEFT JOIN OTRASNOV ON FICHAS2.FicONov = OTRASNOV.ONovCodi 
    $PorFecha $PorLegajo $FilterEstruct $FiltrosFichas ORDER BY FICHAS2.FicLega";

$result = arrMSQuery($sql_query);
$dataNovedades = [];
foreach ($result as $row) {

    $Legajo = $row['Legajo'];
    $Nombre = $row['Nombre'];
    $Fecha = $row['Fecha']->format('d/m/Y');
    $Dia = $row['Dia'];
    $Codigo = $row['Codigo'];
    $Novedad = $row['Novedad'];
    $Valor = $row['Valor'];
    $CodigoCausa = $row['CodigoCausa'] ?? '';
    $Causa = $row['Causa'] ?? '';
    $TipoOnov = $row['TipoOnov'] ?? '';
    $Observacion = $row['Observacion'];
    $Horario = $row['Horario'];

    $dataNovedades[] = array(
        'Legajo' => $Legajo,
        'Nombre' => $Nombre,
        'Fecha' => $Fecha,
        'Dia' => $Dia,
        'Codigo' => $Codigo,
        'Novedad' => $Novedad,
        'Valor' => $Valor,
        'CodigoCausa' => $CodigoCausa,
        'Causa' => $Causa,
        'TipoOnov' => $TipoOnov,
        'Observacion' => $Observacion,
        'Horario' => $Horario,
    );
}