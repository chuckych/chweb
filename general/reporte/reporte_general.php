<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
require __DIR__ . '/dataApi.php';
echo '<body backtop="5mm" backbottom="10mm">';

// ── Pre-computar días de semana una sola vez (evita timeZone()+setlocale() por cada fila) ──
timeZone();
setlocale(LC_TIME, 'spanish');
$_diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

// ── Agrupamiento O(n): un único recorrido reemplaza _group_by_keys() + filtrarObjetoArr() ──
// Antes: O(n²) con serialize/unserialize por grupo + re-filtrado completo por cada legajo.
$groupLega = []; // primer registro por legajo (encabezado)
$cuerpoMap  = []; // todos los registros por legajo (cuerpo)
foreach ($dataApi['DATA'] as $_row) {
    $_lega = $_row['Lega'];
    if (!isset($groupLega[$_lega])) {
        $groupLega[$_lega] = $_row;
    }
    $cuerpoMap[$_lega][] = $_row;
}

// ── Acumuladores generales ──────────────────────────────────────────────────────
$arrNoveGeneral     = [];
$arrNoveCantGeneral = [];

if ($THColu) {
    foreach ($THColu as $cc) {
        $TotalHorasGeneral[$cc['Desc']] = [];
    }
    // ── Pre-índice de THColu por Hora: elimina mergeArrayIfValue() en cada fila ──
    $THColuMap = [];
    foreach ($THColu as $_th) {
        $THColuMap[$_th['Hora']] = $_th;
    }
    // ── Pre-cache de fechas y días: strtotime() se ejecuta 1 vez por fecha única en lugar de 1 vez por legajo×día ──
    $_dateCache = [];
    // ── Cache de horario: combinación (ent,sal,labo,feri) se repite para todos los legajos del mismo turno ──
    $_horarioCache = [];
    foreach ($dataApi['DATA'] as $_dr) {
        $_f = $_dr['Fech'];
        if (!isset($_dateCache[$_f])) {
            $_dateCache[$_f] = [
                substr($_f, 8, 2) . '/' . substr($_f, 5, 2) . '/' . substr($_f, 0, 4),
                $_diasSemana[date('w', strtotime($_f))]
            ];
        }
        // Pre-cache de horario con clave compuesta (los 4 campos definen el resultado unívocamente)
        $_hk = $_dr['Tur']['ent'] . '|' . $_dr['Tur']['sal'] . '|' . $_dr['Labo'] . '|' . $_dr['Feri'];
        if (!isset($_horarioCache[$_hk])) {
            $_horarioCache[$_hk] = horarioApi($_dr['Tur']['ent'], $_dr['Tur']['sal'], $_dr['Labo'], $_dr['Feri']);
        }
    }
}

$TotalLegajos = count($groupLega);
foreach ($groupLega as $encabezado) {
    $ResumenNovedades = [];
    if ($THColu) {
        foreach ($THColu as $c) {
            $TotalHoras[$c['Hora']] = []; // creamos array vacíos de los códigos de Tipos de horas para luego hacer push acumulando los totales y mostrarlos al final de a comlumna.
        }
    }
    // Usar array y implode es mucho más rápido que concatenación con .=
    // page-break-inside:avoid en el div del legajo completo fuerza a mPDF a
    // guardar en RAM el layout de TODAS las filas antes de renderizar.
    // Se elimina del wrapper externo; la paginación se controla con page-break-before en los saltos.
    // ── Header con divs en lugar de tabla: elimina 182 inicializaciones de motor de tabla en mPDF ──
    $headerParts = [
        '<div>',
        '<hr>',
        "<div style='font-size:8pt'><b>Legajo:</b> ({$encabezado['Lega']}) {$encabezado['ApNo']}</div>",
        "<div style='font-size:8pt'><b>Cuil:</b> {$encabezado['Cuil']}</div>",
        '<hr>',
        '<table class="trep" border=0 width=100%>',
        '<tr>',
        '<th>Fecha</th>',
        '<th>Día</th>',
        '<th>Horario</th>',
        '<th>Entra</th>',
        '<th></th>',
        '<th>Sale</th>',
        '<th></th>',
        '<th>Novedades</th>',
        '<th></th>',
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
        foreach ($encabezadosAgrupados as $colu => $desc2) {
            $headerParts[] = "<th class='c'>$desc2</th>";
        }
    } else {
        // Agregar encabezados sin agrupar
        foreach ($THColu as $dataTHoDesc2) {
            $headerParts[] = "<th class='c'>{$dataTHoDesc2['Desc2']}</th>";
        }
    }

    $headerParts[] = '</tr>';
    echo implode('', $headerParts);
    // O(1): acceso directo al grupo de filas del legajo (sin re-filtrar el dataset completo)
    $cuerpoLegajo = $cuerpoMap[$encabezado['Lega']];

    // Inicializar arrays correctamente
    $arrNove = [];
    $arrNoveCant = [];
    foreach ($cuerpoLegajo as $valueLegajo) {

        $ent = $valueLegajo['Tur']['ent'];
        $sal = $valueLegajo['Tur']['sal'];
        $labo = $valueLegajo['Labo'];
        $feri = $valueLegajo['Feri'];
        $_hk = $ent . '|' . $sal . '|' . $labo . '|' . $feri;
        $horario = $_horarioCache[$_hk] ?? horarioApi($ent, $sal, $labo, $feri);
        [$fecha, $dia] = $_dateCache[$valueLegajo['Fech']];

        echo '<tr>';
        echo "<td>$fecha</td>";
        echo "<td>$dia</td>";
        echo "<td>$horario</td>";

        if ($valueLegajo['Fich']) { // Mostramos Las Fichadas E/S
            $fichadas = $valueLegajo['Fich'];
            $lastKey = count($fichadas) - 1;
            $incons = (($lastKey + 1) % 2 == 0) ? '' : '(I)';
            $masFich = ($lastKey > 1) ? '*' : '';
            $primera = $fichadas[0]['HoRe'];
            $ultima = $fichadas[$lastKey]['HoRe'];
            $ultima = ($primera == $ultima) ? '.' : $ultima;
            
            // color:black es el default — solo asignamos estilo cuando difiere
            $color  = '';
            if ($fichadas[0]['Tipo'] === 'Manual') {
                $color = "style='color:blue'";
            } elseif ($fichadas[0]['Esta'] === 'Modificada') {
                $color = "style='color:red'";
            }

            $color2 = '';
            if ($fichadas[$lastKey]['Tipo'] === 'Manual') {
                $color2 = "style='color:blue'";
            } elseif ($fichadas[$lastKey]['Esta'] === 'Modificada') {
                $color2 = "style='color:red'";
            }
            
            echo "<td $color>$primera</td>";
            echo '<td>' . $masFich . '</td>';
            echo "<td $color2>$ultima</td>";
            echo '<td>' . $incons . '</td>';
        } else {
            echo '<td>.</td>';
            echo '<td></td>';
            echo '<td>.</td>';
            echo '<td></td>';
        }
        if ($valueLegajo['Nove']) { // Mostramos novedades

            $TotalNovedades = count($valueLegajo['Nove']);
            if ($TotalNovedades > 0) {
                echo '<td>';
                $noveDescs = [];
                foreach ($valueLegajo['Nove'] as $key => $n) {
                    $noveDescs[] = $n['Desc'];
                    $arrNove[$n['Desc']] = ($arrNove[$n['Desc']] ?? 0) + horaMin($n['Horas']);
                    $arrNoveGeneral[$n['Desc']] = ($arrNoveGeneral[$n['Desc']] ?? 0) + horaMin($n['Horas']);
                    $arrNoveCant[$n['Desc']] = ($arrNoveCant[$n['Desc']] ?? 0) + 1;
                    $arrNoveCantGeneral[$n['Desc']] = ($arrNoveCantGeneral[$n['Desc']] ?? 0) + 1;
                }
                echo implode('<br>', $noveDescs);
                echo '</td>';
            }
            echo "<td class='c'>";
            $noveHoras = [];
            foreach ($valueLegajo['Nove'] as $n2) {
                $noveHoras[] = $n2['Horas'];
            }
            echo implode('<br>', $noveHoras);
            echo '</td>';
        } else {
            echo '<td>.</td>';
            echo "<td class='c'></td>";
        }

        if ($THColu) { // Mostramos Horas

            // Lookup liviano de valores reales: evita copiar THColuMap completo por cada fila
            $_horaAuto = [];
            if ($valueLegajo['Horas']) {
                foreach ($valueLegajo['Horas'] as $_h) {
                    $_horaAuto[$_h['Hora']] = $_h['Auto'];
                }
            }

            if ($_agrupar_thcolu === true) {
                // Agrupar horas por columna
                $horasAgrupadas = [];
                foreach ($THColuMap as $_hora => $h) {
                    $colu = $h['Colu'];
                    if (!isset($horasAgrupadas[$colu])) {
                        $horasAgrupadas[$colu] = ['Desc' => $h['Desc'], 'TotalMinutos' => 0, 'Hora' => $_hora];
                    }
                    $auto = $_horaAuto[$_hora] ?? '';
                    if ($auto && $auto !== '00:00') {
                        $horasAgrupadas[$colu]['TotalMinutos'] += horaMin($auto);
                    }
                }
                // Mostrar las horas agrupadas
                foreach ($horasAgrupadas as $horaAgrupada) {
                    if ($horaAgrupada['TotalMinutos'] > 0) {
                        $horasFormato = MinHora($horaAgrupada['TotalMinutos']);
                        $TotalHoras[$horaAgrupada['Hora']][] = $horaAgrupada['TotalMinutos'];
                        $TotalHorasGeneral[$horaAgrupada['Desc']][] = $horaAgrupada['TotalMinutos'];
                        echo "<td class='c'>$horasFormato</td>";
                    } else {
                        echo "<td class='c'>.</td>";
                    }
                }
            } else {
                // Mostrar horas sin agrupar (comportamiento original)
                foreach ($THColuMap as $_hora => $h) {
                    $auto = $_horaAuto[$_hora] ?? '';
                    if ($auto && $auto !== '00:00') {
                        $TotalHoras[$_hora][] = horaMin($auto);
                        $TotalHorasGeneral[$h['Desc']][] = horaMin($auto);
                        echo "<td class='c'>$auto</td>";
                    } else {
                        echo "<td class='c'>.</td>";
                    }
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
    echo '<td class="rb">Totales:</td>';
    
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
        foreach ($totalesAgrupados as $totalMinutos) {
            echo "<td class='cb'>" . MinHora($totalMinutos) . "</td>";
        }
    } else {
        // Mostrar totales sin agrupar (comportamiento original)
        foreach ($TotalHoras as $ColHoras) {
            echo "<td class='cb'>" . MinHora($ColHoras) . "</td>";
        }
    }
    
    echo '</tr>';
    echo '</table>';
    // echo ($_SaltoPag == '1') ? '' : '';
    if ($arrNove) {
        echo ($_SaltoPag == '1') ? '' : '';
        echo "<div style='font-size:8pt'><b>Resumen de novedades: ({$valueLegajo['Lega']}) {$valueLegajo['ApNo']}</b><br>";
        $chunks = array_chunk($arrNove, 5, true);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $key => $value) {
                $valueCant = $arrNoveCant[$key] ?? 0;
                echo "$key: <b>" . MinHora($value) . " ($valueCant)</b> &nbsp; ";
            }
            echo '<br>';
        }
        echo '</div>';
    }
    echo '</div>';
    if ($_SaltoPag == '1' && $TotalLegajos > 1) {
        echo '<div style="page-break-before: always; clear:both"></div>'; // Salto de pagina
    }
}
if ($_SaltoPag != '1' && $TotalLegajos > 1) {
    echo '<div style="page-break-before: always; clear:both"></div>'; // Salto de pagina 
}
if ($TotalLegajos > 1) { // si hay mas de un legajos mostramos los totales generales

    if ($arrNoveGeneral) {
        echo '<hr>';
        echo "<div style='font-size:8pt'><b><u>Resumen general de Novedades:</u></b><br>";
        foreach ($arrNoveGeneral as $key => $value) {
            $valueCant = $arrNoveCantGeneral[$key] ?? 0;
            echo "$key: <b>" . MinHora($value) . " ($valueCant)</b><br>";
        }
        echo '</div>';
    }

    if ($TotalHorasGeneral) {
        echo '<hr>';
        echo "<div style='font-size:8pt'><b><u>Resumen general de Horas:</u></b><br>";

        foreach ($TotalHorasGeneral as $key => $tt) {
            $TotalHorasGeneral[$key] = array_sum(($tt));
        }
        foreach ($TotalHorasGeneral as $key => $ColHorasGeneral) {
            if ($ColHorasGeneral) {
                echo ($key) . ": <b>" . MinHora($ColHorasGeneral) . "</b><br>";
            }
        }
        echo '</div>';
    }
}