<?php
$fileJson = (file_get_contents("../archivos/" . $request->data->time . ".json"));
$dataLega = (json_decode($fileJson, true));
$dataLega = $dataLega['data'][0];
$dataHorarios = (json_decode($fileJson, true));
$dataHorarios = $dataHorarios['data2']['data'];

echo '<body class="fontq" backtop="5mm" backbottom="10mm">';
$f1 = '<div style="page-break-inside: avoid">';
$f1 .= '<hr>';
$f1 .= '<table width=100%>'; // encabezado
$f1 .= '<tr>';
$f1 .= '<th class="bold" style="width:50px">Legajo: </th>';
$f1 .= "<th><span class='bold'>($dataLega[pers_legajo]) $dataLega[pers_nombre]</span></th>";
$f1 .= '</tr>';
$f1 .= '<tr>';
$f1 .= '<th class="bold">Cuil: </th>';
$f1 .= "<th class='bold'>$dataLega[CUIT]</th>";
$f1 .= '</tr>';
$f1 .= '</table>'; // Fin Encabezado

$f1 .= '<hr>';
$f1 .= '<table border=0>';
$f1 .= '<tr>';
$f1 .= '<th class="pr-2 bold">Fecha</th>';
$f1 .= '<th class="px-2 bold">Día</th>';
$f1 .= '<th class="px-2 bold">Horario</th>';
$f1 .= '<th class="px-2 bold">Descripción</th>';
$f1 .= '<th class="px-2 bold">ID</th>';
$f1 .= '<th class="px-2 bold">Asignación</th>';
$f1 .= '<th class="px-2 bold">Turno</th>';
$f1 .= '<th class="px-2 bold"></th>';
// $f1 .= '<th class="px-2"></th>';
$f1 .= '</tr>';
echo $f1;
$diasLaborales = [0];
$diasNoLaborales = [0];
$diasFrancos = [0];
$diasFeriados = [0];
$diasTotales = [0];
foreach ($dataHorarios as $key => $row) {

    $diasTotales[] = ($row['Legajo']) ? 1 : '';
    $diasLaborales[] = ($row['Laboral'] == 'Sí') ? 1 : '';
    $diasNoLaborales[] = ($row['Laboral'] == 'No') ? 1 : '';

    $Horario = "$row[Desde] a $row[Hasta]";
    $Horario = ($row['Laboral'] == 'No') ? 'Franco' : $Horario;
    $Horario = ($row['Feriado'] == 'Sí' && $row['Laboral'] == 'No') ? 'Feriado' : $Horario;
    $bg = ($row['Feriado'] == 'Sí' && $row['Laboral'] == 'No') ? 'bg-light' : '';
    $bg = ($row['Laboral'] == 'No') ? 'bg-light' : '';
    $FeriadoLaboral = ($row['Feriado'] == 'Sí' && $row['Laboral'] == 'Sí') ? 'Feriado Laboral' : '';

    // $diasFrancos[] = ($Horario == 'Franco') ? 1 : '';
    $diasFeriados[] = ($row['Feriado'] == 'Sí') ? 1 : '';

    echo '<tr>';
    echo "<td class='pr-2 $bg'>$row[Fecha]</td>";
    echo "<td class='px-2 $bg'>$row[Dia]</td>";
    echo "<td class='px-2 $bg'>$Horario</td>";
    echo "<td class='px-2 $bg'>$row[Horario]</td>";
    echo "<td class='px-2 $bg'>$row[HorarioID]</td>";
    echo "<td class='px-2 $bg'>$row[TipoAsign]</td>";
    echo "<td class='px-2 $bg'>$row[Turno]</td>";
    echo "<td class='px-2'>$FeriadoLaboral</td>";
    // echo "<td class='px-2'>$Feriado</td>";
    echo '</tr>';
}

$sumFeriados = array_sum($diasFeriados);
echo '</table>';
echo '<hr>';
$f3 = '';
$f3 .= '<table border=0>';
$f3 .= '<tr>';
$f3 .= '<th class="px-2 bold center">Laborales</th>';
$f3 .= '<th class="px-2 bold center">No Laborales</th>';
$f3 .= '<th class="px-2 bold center">Feriados</th>';
$f3 .= '</tr>';
$f3 .= '<tr>';
$f3 .= '<td class="px-2 center">' . array_sum($diasLaborales) . '</td>';
$f3 .= '<td class="px-2 center">' . array_sum($diasNoLaborales) . '</td>';
$f3 .= '<td class="px-2 center">' . ($sumFeriados) . '</td>';
$f3 .= '</tr>';
$f3 .= '</table>';
echo $f3;
$FechaIni = FechaFormatVar($FechaIni, 'Y-m-d');
$FechaFin = FechaFormatVar($FechaFin, 'Y-m-d');
if ($sumFeriados > 0) {
    $dataParams = array(
        "Fech" => array(),
        "Tras" => array(),
        "TrasIniFin" => array("$FechaIni", "$FechaFin"),
        "Desc" => "",
        "start" => "",
        "length" => $sumFeriados,
    );
    $url = gethostCHWeb() . "/" . HOMEHOST . "/api/feriados/";
    $dataferiados = json_decode(requestApi($url, $token, $authBasic, $dataParams, 10), true);
    // Flight::json($dataferiados['DATA']) . exit;

    echo '<br/>';
    echo '<table border=0>';
    echo '<tr>';
    echo '<th class="px-2 bold">Feriado</th>';
    echo '<th class="px-2 bold">Fecha</th>';
    echo '<th class="px-2 bold">Tipo</th>';
    echo '</tr>';
    if ($dataferiados['DATA']) {
        foreach ($dataferiados['DATA'] as $key => $value) {
            $f = new DateTime($value['Traslado']['Fecha']);
            $f = $f->format('d/m/Y');
            $f = DateTime::createFromFormat("d/m/Y", "$f");
            $f = strftime("%A, %d de %B de %Y", $f->getTimestamp());
            echo '<tr>';
            echo '<td class="px-2">' . ($value['Descripcion']) . '</td>';
            echo '<td class="px-2">' . ucfirst($f) . '</td>';
            echo '<td class="px-2">' . ($value['TipoStr']) . '</td>';
            echo '</tr>';
            unset($f);
        }
    }
    echo '</table>';
}
echo '</div>';
echo '</body>';
// exit;