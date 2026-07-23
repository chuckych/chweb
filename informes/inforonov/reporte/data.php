<?php
E_ALL();

require __DIR__ . '/../../../filtros/filtros.php';
require __DIR__ . '/valores.php';

$dataLega = [];
$legajo = test_input(FusNuloPOST('_l', 'vacio'));
$param = [];
$options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
$_Por ??= '';
switch ($_Por) {
    case 'Lega':
        $order = 'FICHAS2.FicLega';
        break;
    case 'ApNo':
        $order = 'PERSONAL.LegApNo';
        break;
    case 'Fech':
        $order = 'FICHAS2.FicFech';
        break;
    default:
        $order = 'FICHAS2.FicLega';
        break;
}
if ($_Por == 'Fech') { /** Si agrupamos por Fecha */
    $query = "SELECT FICHAS2.FicFech AS 'Fecha', dbo.fn_DiaDeLaSemana(FICHAS2.FicFech) AS 'Dia'
    FROM FICHAS2
    INNER JOIN PERSONAL ON FICHAS2.FicLega = PERSONAL.LegNume
    INNER JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega AND FICHAS2.FicFech = FICHAS.FicFech AND FICHAS2.FicTurn = FICHAS.FicTurn
    WHERE FICHAS2.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas
    GROUP BY FICHAS2.FicFech ORDER BY $order";
} else {
    $query = "SELECT DISTINCT FICHAS2.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', PERSONAL.LegCUIT AS 'Cuil'
    FROM FICHAS2
    INNER JOIN PERSONAL ON FICHAS2.FicLega = PERSONAL.LegNume
    INNER JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega AND FICHAS2.FicFech = FICHAS.FicFech AND FICHAS2.FicTurn = FICHAS.FicTurn
    WHERE FICHAS2.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas 
    ORDER BY $order";
}

$result = arrMSQuery($query) ?? [];
$dataAgrup = [];

if (empty($result)) {
    ob_end_clean();
    header('Content-Type: application/json');
    $data = ['status' => 'error', 'dato' => "No se encontraron datos para el rango de fechas seleccionado."];
    echo json_encode($data);
    exit();
}

foreach ($result as $row) {

    $Dia = $row['Dia'] ?? '';
    $Cuil = $row['Cuil'] ?? '';
    $Legajo = $row['Legajo'] ?? '';
    $Nombre = $row['Nombre'] ?? '';
    $Fecha = ($row['Fecha'] ?? '') ? $row['Fecha']->format('Ymd') : '';

    $dataAgrup[] = [
        'Legajo' => $Legajo,
        'Nombre' => $Nombre,
        'Cuil' => $Cuil,
        'Fecha' => $Fecha,
        'Dia' => $Dia,
    ];
}