<?php
$diasLaborales = [0];
$diasNoLaborales = [0];
$diasFrancos = [0];
$diasFeriados = [0];
$diasTotales = [0];
$getFeriados = false;

$groupLega = _group_by_keys($dataHorarios, array('Legajo'));
$count = count($groupLega);

echo '<body class="fontq" backtop="5mm" backbottom="10mm">';
foreach ($groupLega as $k => $r) {

    $legajo = $r['InfoLega']['Legajo'];
    $ApNo = $r['InfoLega']['Nombre'];
    $Cuil = $r['InfoLega']['Cuit'];

    echo '<hr>';
    echo '<table width=100%>'; // encabezado
    echo '<tr>';
    echo '<th class="bold" style="width:50px">Legajo: </th>';
    echo "<th><span class='bold'>($legajo) $ApNo</span></th>";
    echo '</tr>';
    echo '<tr>';
    echo '<th class="bold">Cuil: </th>';
    echo "<th class='bold'>$Cuil</th>";
    echo '</tr>';
    echo '</table>'; // Fin Encabezado
    echo '<hr>';

    $cuerpoLegajo = filtrarObjetoArr($dataHorarios, 'Legajo', $r['Legajo']); // Filtramos los datos por legajo

    echo '<table border=0>';
    echo '<tr>';
    echo '<th class="pr-2 bold">Fecha</th>';
    echo '<th class="px-2 bold">Día</th>';
    echo '<th class="px-2 bold">Horario</th>';
    echo '<th class="px-2 bold">Descripción</th>';
    echo '<th class="px-2 bold">ID</th>';
    echo '<th class="px-2 bold">Asignación</th>';
    echo '<th class="px-2 bold">Turno</th>';
    echo '<th class="px-2 bold"></th>';
    echo '</tr>';

    foreach ($cuerpoLegajo as $row) {

        $diasTotales[] = ($legajo) ? 1 : '';
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
        echo '</tr>';
    }

    echo '</table>';
    $sumFeriados = array_sum($diasFeriados);

    echo '<hr>';
    echo '<table border=0>';
    echo '<tr>';
    echo '<th class="px-2 bold center">Laborales</th>';
    echo '<th class="px-2 bold center">No Laborales</th>';
    echo '<th class="px-2 bold center">Feriados</th>';
    echo '</tr>';
    echo '<tr>';
    echo '<td class="px-2 center">' . array_sum($diasLaborales) . '</td>';
    echo '<td class="px-2 center">' . array_sum($diasNoLaborales) . '</td>';
    echo '<td class="px-2 center">' . ($sumFeriados) . '</td>';
    echo '</tr>';
    echo '</table>';

    /** Si la suma de feriados es mayor a 0 llamammos a la API de Feriados e imprimimos el listado */
    if ($sumFeriados > 0) {
        /** Esto es ára llamar una vez sola a la API de Feriados. Asi no lo volvemos a hacer en cada iteracción. */
        if ($getFeriados == false) {
            $FechaIni = FechaFormatVar($FechaIni, 'Y-m-d');
            $FechaFin = FechaFormatVar($FechaFin, 'Y-m-d');

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
            $getFeriados = true; /** Declaramos la variable en true para no volver a llamar a los feriados */
        }

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
            }
        }
        echo '</table>';
    }
    /** Insertamos salto de pagina luego de imprimir el listado por legajos. Excluimos el salto en la ultima hoja. */
    if ($k < ($count - 1)) {
        echo '<div style="page-break-before: always; clear:both"></div>'; // Salto de pagina 
    }
    $diasLaborales = [0];
    $diasNoLaborales = [0];
    $diasFrancos = [0];
    $diasFeriados = [0];
    $diasTotales = [0];
}
echo '</body>';
// exit;