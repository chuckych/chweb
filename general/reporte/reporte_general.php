<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
echo '<body class="fontq" backtop="5mm" backbottom="10mm">';
require __DIR__ . '/dataApi.php';
$groupLega = _group_by_keys($dataApi['DATA'], $keys = array('Lega')); // Agrupamos los datos obtenidos de la api por legajo. 
foreach ($groupLega as $key => $encabezado) {
    $ResumenNovedades = array();
    if ($THColu) {
        foreach ($THColu as $c) {
            $TotalHoras[$c['Hora']] = array(); // creamos array vacios de los codigos de Tipos de horas para luego hacer push acumulando los totales y mostrarlos al final de a comlumna.
        }
    }
    $f1 = '<div style="page-break-inside: avoid">';
    $f1 .= '<hr>';
    $f1 .= '<table width=100%>'; // encabezado
    $f1 .= '<tr>';
    $f1 .= '<th class="bold" style="width:50px">Legajo: </th>';
    $f1 .= "<th><span class='bold'>($encabezado[Lega]) $encabezado[ApNo]</span></th>";
    $f1 .= '</tr>';
    $f1 .= '<tr>';
    $f1 .= '<th class="bold">Cuil: </th>';
    $f1 .= "<th class='bold'>$encabezado[Cuil]</th>";
    $f1 .= '</tr>';
    $f1 .= '</table>'; // Fin Encabezado
    $f1 .= '<hr>';
    $f1 .= '<table border=0>';
    $f1 .= '<tr>';
    $f1 .= '<th class="pr-2 bold">Fecha</th>';
    $f1 .= '<th class="px-2 bold">Día</th>';
    $f1 .= '<th class="px-2 bold">Horario</th>';
    // if ($_VerFic == '1') { // Mostramos Las columnas de Fichadas E/S
        $f1 .= '<th class="px-2 bold">Entra</th>';
        $f1 .= '<th></th>';
        $f1 .= '<th class="px-2 bold">Sale</th>';
        $f1 .= '<th></th>';
    // }
    // if ($_VerNove == '1') { // Mostramos Las columnas Novedades
        $f1 .= '<th class="px-2 bold">Novedades</th>';
    // }
    // if ($_VerHoras == '1') { // Mostramos Las columnas Horas
        foreach ($THColu as $dataTHoDesc2) {
            $f1 .= "<th class='px-2 bold center'>$dataTHoDesc2[Desc2]</th>";
        }
    // }
    $f1 .= '</tr>';
    echo $f1;
    $cuerpoLegajo  = filtrarObjetoArr($dataApi['DATA'], 'Lega', $encabezado['Lega']); // Filtramos los datos por legajo
    // print_r($cuerpoLegajo).exit;
    $arrNove = array();
    foreach ($cuerpoLegajo as $key => $valueLegajo) {

        $ent     = $valueLegajo['Tur']['ent'];
        $sal     = $valueLegajo['Tur']['sal'];
        $labo    = $valueLegajo['Labo'];
        $feri    = $valueLegajo['Feri'];
        $horario = horarioApi($ent, $sal, $labo, $feri);
        $fecha   = FechaFormatVar($valueLegajo['Fech'], 'd/m/Y');
        $dia     = DiaSemana3($valueLegajo['Fech']);
        echo '<tr>';
        echo "<td class='pr-2 vtop'>$fecha</td>";
        echo "<td class='px-2 vtop'>$dia</td>";
        echo "<td class='px-2 vtop'>$horario</td>";

        if ($valueLegajo['Fich']) { // Mostramos Las Fichadas E/S
            $lastKey = count($valueLegajo['Fich']);
            $incons  = ($lastKey % 2 == 0) ? '' : '(I)';
            $masFich = ($lastKey > 2) ? '*' : '';
            $primera   = ($valueLegajo['Fich'][0]['HoRe']);
            switch ($valueLegajo['Fich'][0]['Tipo']) {
                case 'Manual':
                    $color = "style='color:blue'";
                    $tipo = "(M)";
                    break;
                default:
                    $color = "style='color:black'";
                    $tipo = "(N)";
                    break;
            }
            switch ($valueLegajo['Fich'][0]['Esta']) {
                case 'Modificada':
                    $color = "style='color:red'";
                    $tipo = "(E)";
                    break;
            }
            switch ($valueLegajo['Fich'][$lastKey - 1]['Tipo']) {
                case 'Manual':
                    $color2 = "style='color:blue'";
                    $tipo2 = "(M)";
                    break;
                default:
                    $color2 = "style='color:black'";
                    $tipo2 = "(N)";
                    break;
            }
            switch ($valueLegajo['Fich'][$lastKey - 1]['Esta']) {
                case 'Modificada':
                    $color2 = "style='color:red'";
                    $tipo2 = "(E)";
                    break;
            }
            $ultima = ($valueLegajo['Fich'][$lastKey - 1]['HoRe']);
            $ultima = ($primera == $ultima) ? '.' : $ultima;
            echo "<td class='px-2 vtop' $color>$primera</td>";
            echo "<td class='vtop'>$masFich</td>";
            echo "<td class='px-2 vtop' $color2>$ultima</td>";
            echo "<td class='vtop'>$incons</td>";
        } else {
            echo "<td class='px-2 vtop'>.</td>";
            echo "<td class='vtop'></td>";
            echo "<td class='px-2 vtop'>.</td>";
            echo "<td class='vtop'></td>";
        }
        if ($valueLegajo['Nove']) { // Mostramos novedades
            // $iNove = 0;
            $TotalNovedades = count($valueLegajo['Nove']);
            if ($TotalNovedades > 0) {
                echo '<td>';
                echo '<table border=0 width="100%">';
                foreach ($valueLegajo['Nove'] as $key => $n) {
                    echo '<tr style="padding-top:0px;">';
                    echo "<td class='px-2 vtop' style='padding-top:0px;'>";
                    echo "$n[Desc]";
                    echo "</td>";
                    echo "<td class='px-2 vtop' style='text-align:right;padding-top:0px;'>";
                    echo "$n[Horas]";
                    echo "</td>";
                    echo '</tr>';
                    $arrNove[$n['Desc']] += horaMin($n['Horas']);
                }
                echo '</table>';
                echo '</td>';
            }
        } else {
            echo "<td class='px-2 vtop'>.</td>";
        }

        if ($THColu) { // Mostramos Horas

            $o[] = array('Hora' => $THColu[0]['Hora'], 'Desc' => $THColu[0]['Desc'], 'Desc2' => $THColu[0]['Desc2'], 'Calc' => '', 'Hechas' => '', 'Auto' => ''); // Objeto de horas vacio.

            $objHoras = ($valueLegajo['Horas']) ? $valueLegajo['Horas'] : $o;

            $Horas = (mergeArrayIfValue($THColu, $objHoras, 'Hora')); // Se crea un array con el array de tipos de horas y las horas, haciendo un merge entre ambos y combinandolos para luego imprimir las columnas con las horas correspondientes a cada tipo de horas. 
            foreach ($Horas as $key => $h) {
                echo "<td class='px-2 vtop' style='text-align:center'>";
                if (($h['Auto']) && $h['Auto'] != '00:00') {
                    echo $h['Auto'];
                    array_push($TotalHoras[$h['Hora']], horaMin($h['Auto']));
                } else {
                    echo '.';
                }
                echo "</td>";
            }
        }
        echo '</tr>';
    } // fin cuerpoLegajo
    foreach ($TotalHoras as $key => $t) {
        $TotalHoras[$key] = array_sum(($t));
    }
    echo '<tr>';
    echo '<td colspan="7"></td>';
    echo '<td class="bold" style="text-align:right">Totales:</td>';
    foreach ($TotalHoras as $key => $ColHoras) {
        echo "<td class='px-2 vtop center bold' style='text-align:center'>" . MinHora($ColHoras) . "</td>";
    }
    echo '</tr>';
    echo '</table>';
    echo ($_SaltoPag == '1') ? '<hr>' : '';
    if ($arrNove) {
        echo ($_SaltoPag == '1') ? '' : '<hr>';
        echo '<table border=0 width=100%>';
        echo '<tr>';
        echo '<td class="bold py-1"><u>Resumen de novedades: (' . $valueLegajo['Lega'] . ') ' . $valueLegajo['ApNo'] . '</u></td>';
        echo '</tr>';
        echo '</table>';
        echo '<table border=0>';
        $chunks = array_chunk($arrNove, 5, true);
        foreach ($chunks as $index => $chunk) {
            echo '<tr>';
            foreach ($chunk as $key => $value) {
                echo "<td class='pr-2 vtop'>$key:</td>";
                echo "<td class='pr-2 vtop bold'>" . MinHora($value) . "</td>";
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '<br>';
    }
    echo '</div>';
    if ($_SaltoPag == '1') {
        /** Si se activa el salto de pagina por legajo */
        if ($valueLegajo != end($valueLegajo)) {
        // Este código se ejecutará para todos menos el último
        echo '<div style="page-break-before: always; clear:both"></div>';
        }
    }
}
echo '</body>';
// exit;