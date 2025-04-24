<?php

require __DIR__ . '/../../../filtros/filtros.php';
require __DIR__ . '/../../../config/conect_mssql.php';
//require __DIR__ . '/../valores.php';

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni = test_input(dr_fecha($DateRange[0]));
$FechaFin = test_input(dr_fecha($DateRange[1]));

if (empty($DateRange)) {
    $json_data = array(
        "data" => 'No hay Fecha'
    );

    echo json_encode($json_data);
    exit;
}

FusNuloPOST("Per", '');
FusNuloPOST("Emp", '');
FusNuloPOST("Plan", '');
FusNuloPOST("Sect", '');
FusNuloPOST("Sec2", '');
FusNuloPOST("Grup", '');
FusNuloPOST("Sucur", '');
FusNuloPOST("Tipo", '');
FusNuloPOST("FicFalta", 0);

$Per = test_input($_POST['Per']);
$Emp = test_input($_POST['Emp']);
$Plan = test_input($_POST['Plan']);
$Sect = test_input($_POST['Sect']);
$Grup = test_input($_POST['Grup']);
$Sucur = test_input($_POST['Sucur']);
$Sec2 = test_input($_POST['Sec2']);
$FicFalta = test_input($_POST['FicFalta']);
$Tipo = test_input($_POST['Tipo']);



switch ($Tipo) {
    case '1':
        $Tipo = "AND PERSONAL.LegTipo = '1'";
        break;
    case '2':
        $Tipo = "AND PERSONAL.LegTipo = '0'";
        break;
    case 'null':
        $Tipo = "";
        break;
    default:
        $Tipo = "";
        break;
}

$Emp = ($Emp && $Emp != 'null') ? " AND FICHAS.FicEmpr IN ($Emp)" : "";/** Empresa */
$Plan = ($Plan && $Plan != 'null') ? " AND FICHAS.FicPlan IN ($Plan)" : "";/** Plantas */
$Sect = ($Sect && $Sect != 'null') ? " AND FICHAS.FicSect IN ($Sect)" : '';/** Sectores */
$Sec2 = ($Sec2 && $Sec2 != 'null') ? " AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($Sec2)" : '';/** Secciones */
$Grup = ($Grup && $Grup != 'null') ? " AND FICHAS.FicGrup IN ($Grup)" : '';/** Grupos */
$Sucur = ($Sucur && $Sucur != 'null') ? " AND FICHAS.FicSucu IN ($Sucur)" : '';/** Sucursal */
$FicFalta = ($FicFalta && $FicFalta != 'null') ? " AND FICHAS.FicFalta = '$FicFalta'" : "";/** Fichadas Inconsistentes */
$Per = (!empty($Per) && $Per != 'null') ? "AND FICHAS.FicLega IN ($Per)" : ''; /** Legajos */

$FilterEstruct = $FicFalta;
$FilterEstruct .= $Emp;
$FilterEstruct .= $Plan;
$FilterEstruct .= $Sect;
$FilterEstruct .= $Sec2;
$FilterEstruct .= $Grup;
$FilterEstruct .= $Sucur;
$FilterEstruct .= $Tipo;
$FilterEstruct .= $Per;

$dataLega = array();

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

switch ($_Por) {
    case 'Lega':
        $order = 'FICHAS.FicLega';
        break;
    case 'ApNo':
        $order = 'PERSONAL.LegApNo';
        break;
    case 'Fech':
        $order = 'FICHAS.FicFech';
        break;
    default:
        $order = 'FICHAS.FicLega';
        break;
}
if ($_Por == 'Fech') { /** Si agrupamos por Fecha */
    $query = "SELECT FICHAS.FicFech AS 'Fecha', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Dia'
    FROM FICHAS
    INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume
    INNER JOIN REGISTRO ON FICHAS.FicFech = REGISTRO.RegFeAs AND FICHAS.FicLega = REGISTRO.RegLega
    WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas
    GROUP BY FICHAS.FicFech ORDER BY $order";
} else {
    $query = "SELECT DISTINCT FICHAS.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', PERSONAL.LegCUIT AS 'Cuil'
FROM FICHAS
INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume
INNER JOIN REGISTRO ON FICHAS.FicFech = REGISTRO.RegFeAs AND FICHAS.FicLega = REGISTRO.RegLega
WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas 
ORDER BY $order";
}
// h4($query);
$result = sqlsrv_query($link, $query, $param, $options);

while ($row = sqlsrv_fetch_array($result)) {

    $Dia = isset($row['Dia']) ? $row['Dia'] : '';
    $Cuil = isset($row['Cuil']) ? $row['Cuil'] : '';
    $Legajo = isset($row['Legajo']) ? $row['Legajo'] : '';
    $Nombre = isset($row['Nombre']) ? $row['Nombre'] : '';
    $Fecha = isset($row['Fecha']) ? $row['Fecha']->format('Ymd') : '';

    $dataAgrup[] = array(
        'Legajo' => $Legajo,
        'Nombre' => $Nombre,
        'Cuil' => $Cuil,
        'Fecha' => $Fecha,
        'Dia' => $Dia,
    );
}
// h1($query);
// echo json_encode($dataAgrup); exit;

sqlsrv_free_stmt($result);
// sqlsrv_close($link);
