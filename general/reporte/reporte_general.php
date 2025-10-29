<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
echo '<body backtop="5mm" backbottom="10mm">';
require __DIR__ . '/dataApi.php';

$groupLega = _group_by_keys($dataApi['DATA'], $keys = array('Lega')); // Agrupamos los datos obtenidos de la api por legajo. 

if ($THColu) {
    foreach ($THColu as $cc) {
        $TotalHorasGeneral[$cc['Desc']] = []; // creamos array vacíos de los códigos de Tipos de horas para luego hacer push acumulando los totales y mostrarlos al final de a comlumna.
    }
}

$TotalLegajos = count($groupLega);
foreach ($groupLega as $key => $encabezado) {
    $ResumenNovedades = [];
    if ($THColu) {
        foreach ($THColu as $c) {
            $TotalHoras[$c['Hora']] = []; // creamos array vacíos de los códigos de Tipos de horas para luego hacer push acumulando los totales y mostrarlos al final de a comlumna.
        }
    }
    // Usar array y implode es mucho más rápido que concatenación con .=
    $headerParts = [
        '<div style="page-break-inside: avoid">',
        '<hr>',
        '<table border=0 width=100%>', // encabezado
        '<tr>',
        '<th class="bold" style="width:50px">Legajo: </th>',
        "<th><span class='bold'>({$encabezado['Lega']}) {$encabezado['ApNo']}</span></th>",
        '</tr>',
        '<tr>',
        '<th class="bold">Cuil: </th>',
        "<th class='bold'>{$encabezado['Cuil']}</th>",
        '</tr>',
        '</table>', // Fin Encabezado
        '<hr>',
        '<table border=0 width:100%>',
        '<tr>',
        '<th class="pr-2 bold">Fecha</th>',
        '<th class="px-2 bold">Día</th>',
        '<th class="px-2 bold">Horario</th>',
        '<th class="px-2 bold">Entra</th>',
        '<th></th>',
        '<th class="px-2 bold">Sale</th>',
        '<th></th>',
        '<th class="px-2 bold">Novedades</th>',
        '<th class="bold center"></th>',
    ];

    $cantidadTHColu = count($THColu);
    
    // Generar encabezados de horas
    if ($_agrupar_thcolu === true) {
        // Agrupar encabezados por columna
        $encabezadosAgrupados = [];
        foreach ($THColu as $dataTHoDesc2) {
            $colu = $dataTHoDesc2['Colu'];
            if (!isset($encabezadosAgrupados[$colu])) {
                $encabezadosAgrupados[$colu] = $dataTHoDesc2['Desc2'];
            }
        }
        // Agregar encabezados agrupados al array
        foreach ($encabezadosAgrupados as $colu => $desc2) {
            $headerParts[] = "<th class='px-2 bold center'>$desc2</th>";
        }
    } else {
        // Agregar encabezados sin agrupar
        foreach ($THColu as $dataTHoDesc2) {
            $headerParts[] = "<th class='px-2 bold center'>{$dataTHoDesc2['Desc2']}</th>";
        }
    }
    
    $headerParts[] = '</tr>';
    echo implode('', $headerParts);
    $cuerpoLegajo = filtrarObjetoArr($dataApi['DATA'], 'Lega', $encabezado['Lega']); // Filtramos los datos por legajo
    
    // Inicializar arrays correctamente
    $arrNove = [];
    $arrNoveCant = [];
    foreach ($cuerpoLegajo as $key => $valueLegajo) {

        $ent = $valueLegajo['Tur']['ent'];
        $sal = $valueLegajo['Tur']['sal'];
        $labo = $valueLegajo['Labo'];
        $feri = $valueLegajo['Feri'];
        $horario = horarioApi($ent, $sal, $labo, $feri);
        $fecha = FechaFormatVar($valueLegajo['Fech'], 'd/m/Y');
        $dia = DiaSemana3($valueLegajo['Fech']);
        $FrancoColor = '';

        echo '<tr ' . $FrancoColor . '>';
        echo "<td class='pr-2 vtop'>$fecha</td>";
        echo "<td class='px-2 vtop'>$dia</td>";
        echo "<td class='px-2 vtop'>$horario</td>";

        if ($valueLegajo['Fich']) { // Mostramos Las Fichadas E/S
            $fichadas = $valueLegajo['Fich'];
            $lastKey = count($fichadas) - 1;
            $incons = (($lastKey + 1) % 2 == 0) ? '' : '(I)';
            $masFich = ($lastKey > 1) ? '*' : '';
            $primera = $fichadas[0]['HoRe'];
            $ultima = $fichadas[$lastKey]['HoRe'];
            $ultima = ($primera == $ultima) ? '.' : $ultima;
            
            // Determinar color y tipo de la primera fichada
            $color = "style='color:black'";
            if ($fichadas[0]['Tipo'] === 'Manual') {
                $color = "style='color:blue'";
            } elseif ($fichadas[0]['Esta'] === 'Modificada') {
                $color = "style='color:red'";
            }
            
            // Determinar color y tipo de la última fichada
            $color2 = "style='color:black'";
            if ($fichadas[$lastKey]['Tipo'] === 'Manual') {
                $color2 = "style='color:blue'";
            } elseif ($fichadas[$lastKey]['Esta'] === 'Modificada') {
                $color2 = "style='color:red'";
            }
            
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

            $TotalNovedades = count($valueLegajo['Nove']);
            if ($TotalNovedades > 0) {
                echo "<td  class='px-2 vtop'>";
                // echo '<table border=1 width="100%" autosize="1">';
                foreach ($valueLegajo['Nove'] as $key => $n) {
                    // echo '<tr>';
                    // echo "<td class='vtop'>";
                    // echo '<p style="border:0px solid #333;">';
                    echo "<p>";
                    echo "<span>" . $n['Desc'] . "</span>";
                    // echo "&nbsp;";
                    // echo "</td>";
                    // echo "<td class='vtop' style='text-align:right'>";
                    // echo "<span style='float-right'>" . $n['Horas'] . "</span>";
                    echo "</p>";
                    // echo "</td>";
                    // echo '</tr>';
                    // si la ultima $key es mayor a 0 y la cantidad de novedades es mayor a 1, mostramos un hr
                    // echo '</p>';
                    if ($key < $TotalNovedades - 1 && $TotalNovedades > 1) {
                        echo '<hr style="margin:2px; padding:0px; color:#fff">';
                    }
                    $arrNove[$n['Desc']] += horaMin($n['Horas']);
                    $arrNoveGeneral[$n['Desc']] += horaMin($n['Horas']);
                    $arrNoveCant[$n['Desc']] += 1;
                    $arrNoveCantGeneral[$n['Desc']] += 1;
                }
                // echo '</table>';
                echo '</td>';
            }
            echo "<td  class='vtop center'>";
            foreach ($valueLegajo['Nove'] as $key => $n2) {
                echo "<p>";
                echo "<span>" . $n2['Horas'] . "</span>";
                echo "</p>";
                if ($key < $TotalNovedades - 1 && $TotalNovedades > 1) {
                    echo '<hr style="margin:2px; padding:0px; color:#fff">';
                }
            }
            echo '</td>';
        } else {
            echo "<td class='px-2 vtop'>.</td>";
            echo "<td class='vtop center'></td>";
        }

        if ($THColu) { // Mostramos Horas

            $o[] = [
                'Hora' => $THColu[0]['Hora'],
                'Desc' => $THColu[0]['Desc'],
                'Desc2' => $THColu[0]['Desc2'],
                'Colu' => $THColu[0]['Colu'],
                'Calc' => '',
                'Hechas' => '',
                'Auto' => ''
            ]; // Objeto de horas vacio.

            $objHoras = ($valueLegajo['Horas']) ? $valueLegajo['Horas'] : $o;

            $Horas = mergeArrayIfValue(
                $THColu,
                $objHoras,
                'Hora'
            ); // Se crea un array con el array de tipos de horas y las horas, haciendo un merge entre ambos y combinandolos para luego imprimir las columnas con las horas correspondientes a cada tipo de horas. 
            // error_log(print_r($Horas, true));
            // error_log(print_r($Horas, true));
            
            if ($_agrupar_thcolu === true) {
                // Agrupar horas por columna
                $horasAgrupadas = [];
                
                foreach ($Horas as $h) {
                    $colu = $h['Colu'];
                    if (!isset($horasAgrupadas[$colu])) {
                        $horasAgrupadas[$colu] = [
                            'Colu' => $colu,
                            'Desc' => $h['Desc'],
                            'TotalMinutos' => 0,
                            'Hora' => $h['Hora']
                        ];
                    }
                    // Sumar los minutos de las horas de esta columna
                    if (($h['Auto']) && $h['Auto'] != '00:00') {
                        $horasAgrupadas[$colu]['TotalMinutos'] += horaMin($h['Auto']);
                    }
                }
                
                // Mostrar las horas agrupadas
                foreach ($horasAgrupadas as $colu => $horaAgrupada) {
                    echo "<td class='px-2 vtop' style='text-align:center'>";
                    if ($horaAgrupada['TotalMinutos'] > 0) {
                        $horasFormato = MinHora($horaAgrupada['TotalMinutos']);
                        echo $horasFormato;
                        // Usar sintaxis de array en lugar de array_push (más rápido)
                        $TotalHoras[$horaAgrupada['Hora']][] = $horaAgrupada['TotalMinutos'];
                        $TotalHorasGeneral[$horaAgrupada['Desc']][] = $horaAgrupada['TotalMinutos'];
                    } else {
                        echo '.';
                    }
                    echo "</td>";
                }
            } else {
                // Mostrar horas sin agrupar (comportamiento original)
                foreach ($Horas as $key => $h) {
                    echo "<td class='px-2 vtop' style='text-align:center'>";
                    if (($h['Auto']) && $h['Auto'] != '00:00') {
                        echo $h['Auto'];
                        $TotalHoras[$h['Hora']][] = horaMin($h['Auto']);
                        $TotalHorasGeneral[$h['Desc']][] = horaMin($h['Auto']);
                    } else {
                        echo '.';
                    }
                    echo "</td>";
                }
            }
        }
        echo '</tr>';
    } // fin cuerpoLegajo
    
    // Calcular totales
    foreach ($TotalHoras as $key => $t) {
        $TotalHoras[$key] = array_sum($t);
    }
    
    echo '<tr>';
    echo '<td colspan="8"></td>';
    echo '<td class="bold" style="text-align:right">Totales:</td>';
    
    if ($_agrupar_thcolu === true) {
        // Agrupar totales por columna
        $totalesAgrupados = [];
        foreach ($THColu as $thc) {
            $colu = $thc['Colu'];
            $hora = $thc['Hora'];
            if (!isset($totalesAgrupados[$colu])) {
                $totalesAgrupados[$colu] = 0;
            }
            if (isset($TotalHoras[$hora])) {
                $totalesAgrupados[$colu] += $TotalHoras[$hora];
            }
        }
        // Mostrar totales agrupados
        foreach ($totalesAgrupados as $colu => $totalMinutos) {
            echo "<td class='px-2 vtop center bold' style='text-align:center'>" . MinHora($totalMinutos) . "</td>";
        }
    } else {
        // Mostrar totales sin agrupar (comportamiento original)
        foreach ($TotalHoras as $key => $ColHoras) {
            echo "<td class='px-2 vtop center bold' style='text-align:center'>" . MinHora($ColHoras) . "</td>";
        }
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
        foreach ($chunks as $chunk) {
            echo '<tr>';
            foreach ($chunk as $key => $value) {
                $valueCant = $arrNoveCant[$key] ?? 0;
                echo "<td class='pr-2 vtop'>$key:</td>";
                echo "<td class='pr-2 vtop bold'>" . MinHora($value) . " ($valueCant)</td>";
            }
            echo '</tr>';
        }
        echo '</table>';
        echo '<br>';
    }
    echo '</div>';
    if ($_SaltoPag == '1' && $TotalLegajos > 1) {
        if (($groupLega[$TotalLegajos - 1])) {
            echo '<div style="page-break-before: always; clear:both"></div>'; // Salto de pagina 
        }
    }
}
if ($_SaltoPag != '1' && $TotalLegajos > 1) {
    echo '<div style="page-break-before: always; clear:both"></div>'; // Salto de pagina 
}
if ($TotalLegajos > 1) { // si hay mas de un legajos mostramos los totales generales

    if ($arrNoveGeneral) {
        echo '<hr>';
        echo '<table border=0 width=100%>';
        echo '<tr>';
        echo '<td class="bold py-1"><u>Resumen general de Novedades: </u></td>';
        echo '</tr>';
        echo '</table>';
        echo '<table border=0>';
        foreach ($arrNoveGeneral as $key => $value) {
            $valueCant = $arrNoveCantGeneral[$key] ?? 0;
            echo '<tr>';
            echo "<td class='pr-2 vtop'>$key:</td>";
            echo "<td class='pr-2 vtop bold'>" . MinHora($value) . " ($valueCant)</td>";
            echo '</tr>';
        }
        echo '</table>';
        echo '<br>';
    }

    if ($TotalHorasGeneral) {
        echo '<hr>';
        echo '<table border=0 width=100%>';
        echo '<tr>';
        echo '<td class="bold py-1"><u>Resumen general de Horas: </u></td>';
        echo '</tr>';
        echo '</table>';
        echo '<table border=0>';

        foreach ($TotalHorasGeneral as $key => $tt) {
            $TotalHorasGeneral[$key] = array_sum(($tt));
        }
        foreach ($TotalHorasGeneral as $key => $ColHorasGeneral) {
            if ($ColHorasGeneral) {
                echo '<tr>';
                echo "<td class='pr-2 vtop'>" . ($key) . "</td>";
                echo "<td class='pr-2 vtop bold'>" . MinHora($ColHorasGeneral) . "</td>";
                echo '</tr>';
            }
        }

        echo '</table>';
        echo '<br>';
    }
}

//echo '</body>';
// exit;