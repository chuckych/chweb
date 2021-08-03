<?php
E_ALL();

require __DIR__ . '../../../../filtros/filtros.php';
require __DIR__ . '../../../../config/conect_mssql.php';
require __DIR__ . '../valores.php';

$dataLega = array();

$legajo = test_input(FusNuloPOST('_l', 'vacio'));

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

switch ($_Por) {
    case 'Lega':
        $order = 'FICHAS3.FicLega';
        break;
    case 'ApNo':
        $order = 'PERSONAL.LegApNo';
        break;
    case 'Fech':
        $order = 'FICHAS3.FicFech';
        break;
    default:
        $order = 'FICHAS3.FicLega';
        break;
}
if ($_Por=='Fech') { /** Si agrupamos por Fecha */
    $query="SELECT FICHAS3.FicFech AS 'Fecha', dbo.fn_DiaDeLaSemana(FICHAS3.FicFech) AS 'Dia'
    FROM FICHAS3
    INNER JOIN PERSONAL ON FICHAS3.FicLega = PERSONAL.LegNume
    INNER JOIN FICHAS ON FICHAS3.FicLega = FICHAS.FicLega AND FICHAS3.FicFech = FICHAS.FicFech AND FICHAS3.FicTurn = FICHAS.FicTurn
    WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas
    GROUP BY FICHAS3.FicFech ORDER BY $order";
}else{
$query="SELECT DISTINCT FICHAS3.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', PERSONAL.LegCUIT AS 'Cuil'
FROM FICHAS3
INNER JOIN PERSONAL ON FICHAS3.FicLega = PERSONAL.LegNume
INNER JOIN FICHAS ON FICHAS3.FicLega = FICHAS.FicLega AND FICHAS3.FicFech = FICHAS.FicFech AND FICHAS3.FicTurn = FICHAS.FicTurn
WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas 
ORDER BY $order";
}

$result = sqlsrv_query($link, $query,$param, $options);

while ($row = sqlsrv_fetch_array($result)) {

    $Dia    = isset($row['Dia']) ? $row['Dia']:'';
    $Cuil   = isset($row['Cuil']) ? $row['Cuil']:'';
    $Legajo = isset($row['Legajo']) ? $row['Legajo']:'';
    $Nombre = isset($row['Nombre']) ? $row['Nombre']:'';
    $Fecha  = isset($row['Fecha']) ? $row['Fecha']->format('Ymd'):'';

    $dataAgrup[] = array(
        'Legajo' => $Legajo,
        'Nombre' => $Nombre,
        'Cuil'   => $Cuil,
        'Fecha'  => $Fecha,
        'Dia'    => $Dia,
    );
}
// h1($query);
// echo json_encode($dataAgrup); exit;

sqlsrv_free_stmt($result);
// sqlsrv_close($link);
