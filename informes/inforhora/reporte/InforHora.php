<body class="fontq" backtop="5mm" backbottom="10mm">
    chunk
    <?php
    // ============ SISTEMA DE LOGGING DE PERFORMANCE ============
    $log_file = __DIR__ . '/performance_log.txt';
    $time_log = [];
    $time_start_total = microtime(true);
    
    function log_time($label, $start_time = null) {
        global $time_log, $time_start_total;
        $current = microtime(true);
        if ($start_time !== null) {
            $duration = ($current - $start_time) * 1000; // en milisegundos
            $time_log[] = sprintf("[%.2fms desde inicio] %s: %.2f ms", 
                ($current - $time_start_total) * 1000, $label, $duration);
        } else {
            $time_log[] = sprintf("[%.2fms desde inicio] %s", 
                ($current - $time_start_total) * 1000, $label);
        }
        return $current;
    }
    
    log_time("INICIO DEL SCRIPT");
    
    // Cargar datos desde la API
    $t1 = microtime(true);
    require __DIR__ . '/dataApi.php';
    log_time("Carga de dataApi.php (llamadas API)", $t1);
    
    // Detalles de tiempos de API
    if (isset($GLOBALS['api_time_fichas'])) {
        log_time("  └─ API ficnovhor: " . sprintf("%.2f ms", $GLOBALS['api_time_fichas']) . " (" . $GLOBALS['api_count_fichas'] . " registros)");
    }
    if (isset($GLOBALS['api_time_tipohora'])) {
        log_time("  └─ API tipohora: " . sprintf("%.2f ms", $GLOBALS['api_time_tipohora']) . " (" . $GLOBALS['api_count_tipohora'] . " tipos)");
    }
    
    log_time("Total registros recibidos: " . count($fichasData));
    
    // Agrupar datos según $_Por
    $t2 = microtime(true);
    $dataAgrupada = [];
    
    if ($_Por == 'Fech') {
        // Agrupar por FECHA
        foreach ($fichasData as $ficha) {
            $fecha = $ficha['Fech'];
            if (!isset($dataAgrupada[$fecha])) {
                $dataAgrupada[$fecha] = [
                    'tipo' => 'fecha',
                    'clave' => $fecha,
                    'label' => $ficha['FechF'] . ' (' . $ficha['FechD'] . ')',
                    'registros' => []
                ];
            }
            $dataAgrupada[$fecha]['registros'][] = $ficha;
        }
        // Ordenar por fecha
        ksort($dataAgrupada);
    } elseif ($_Por == 'ApNo') {
        // Agrupar por APELLIDO Y NOMBRE (ordenado alfabéticamente)
        foreach ($fichasData as $ficha) {
            $legajo = $ficha['Lega'];
            if (!isset($dataAgrupada[$legajo])) {
                $dataAgrupada[$legajo] = [
                    'tipo' => 'legajo',
                    'clave' => $legajo,
                    'label' => '(' . $legajo . ') ' . $ficha['ApNo'],
                    'nombre' => $ficha['ApNo'],
                    'registros' => []
                ];
            }
            $dataAgrupada[$legajo]['registros'][] = $ficha;
        }
        // Ordenar alfabéticamente por nombre
        uasort($dataAgrupada, function($a, $b) {
            return strcmp($a['nombre'], $b['nombre']);
        });
    } else {
        // Agrupar por LEGAJO (por defecto)
        foreach ($fichasData as $ficha) {
            $legajo = $ficha['Lega'];
            if (!isset($dataAgrupada[$legajo])) {
                $dataAgrupada[$legajo] = [
                    'tipo' => 'legajo',
                    'clave' => $legajo,
                    'label' => '(' . $legajo . ') ' . $ficha['ApNo'],
                    'registros' => []
                ];
            }
            $dataAgrupada[$legajo]['registros'][] = $ficha;
        }
        // Ordenar por legajo
        ksort($dataAgrupada);
    }
    
    log_time("Agrupación de datos ($_Por = $_Por)", $t2);
    log_time("Total grupos creados: " . count($dataAgrupada));
    
    // Preparar encabezados de columnas de horas
    $t3 = microtime(true);
    $columnasHoras = [];
    if ($_agrupar_thcolu === true) {
        // Agrupar columnas por Colu
        $colsAgrupadas = [];
        foreach ($tiposHora as $th) {
            $colu = $th['Colu'];
            if (!isset($colsAgrupadas[$colu])) {
                $colsAgrupadas[$colu] = $th['Desc2'];
            }
        }
        ksort($colsAgrupadas);
        $columnasHoras = $colsAgrupadas;
    } else {
        // Sin agrupar - cada tipo de hora es una columna
        foreach ($tiposHora as $th) {
            $columnasHoras[$th['Codi']] = $th['Desc2'];
        }
    }
    log_time("Preparación de encabezados de columnas", $t3);
    log_time("Total columnas de horas: " . count($columnasHoras));
    
    $t_render_start = microtime(true);
    log_time("INICIO DE RENDERIZADO HTML");
    
    $grupo_count = 0;
    $registro_count = 0;
    ?>
    
    <?php 
    foreach ($dataAgrupada as $grupo): 
        $grupo_count++;
        $t_grupo = microtime(true);
    ?>
        <div style="page-break-inside: avoid">
            <hr>
            <!-- Encabezado del Grupo -->
            <table>
                <tr>
                    <th style="width:5%">
                        <?= ($grupo['tipo'] == 'fecha') ? 'Fecha:' : 'Legajo:' ?>
                    </th>
                    <th style="width:45%">
                        <p class="bold"><?= $grupo['label'] ?></p>
                    </th>
                    <th style="width:50%" class="right"></th>
                </tr>
            </table>
            <hr>
            
            <!-- Tabla de datos -->
            <table>
                <!-- Encabezado principal de columnas -->
                <tr>
                    <th colspan="4"></th>
                    <?php foreach ($columnasHoras as $colLabel): ?>
                        <th class="bold" colspan="2"><?= $colLabel ?></th>
                    <?php endforeach; ?>
                </tr>
                
                <!-- Subencabezados -->
                <tr>
                    <?php if ($grupo['tipo'] == 'fecha'): ?>
                        <th class="bold">Legajo</th>
                        <th class="bold">Nombre</th>
                    <?php else: ?>
                        <th class="bold">Fecha</th>
                        <th class="bold">Día</th>
                    <?php endif; ?>
                    <th class="bold">Horario</th>
                    <th class="bold bg-light">Trab.</th>
                    <?php foreach ($columnasHoras as $colLabel): ?>
                        <th class="bold center w40">Hechas</th>
                        <th class="bold center w40 bg-light">Auto</th>
                    <?php endforeach; ?>
                </tr>
                
                <!-- Datos de registros -->
                <?php 
                foreach ($grupo['registros'] as $registro): 
                    $registro_count++;
                ?>
                    <tr>
                        <?php if ($grupo['tipo'] == 'fecha'): ?>
                            <th class="vtop"><?= $registro['Lega'] ?></th>
                            <th class="vtop"><?= $registro['ApNo'] ?></th>
                        <?php else: ?>
                            <th class="vtop"><?= $registro['FechF'] ?></th>
                            <th class="vtop"><?= $registro['FechD'] ?></th>
                        <?php endif; ?>
                        <td class="vtop"><?= $registro['TurStr'] ?></td>
                        <td class="vtop bg-light"><?= $registro['Trab'] ?></td>
                        
                        <?php
                        // Procesar horas según agrupación
                        if ($_agrupar_thcolu === true) {
                            // Agrupar horas por Colu
                            $horasAgrupadas = [];
                            foreach ($registro['Horas'] as $hora) {
                                $colu = $hora['Colu'];
                                if (!isset($horasAgrupadas[$colu])) {
                                    $horasAgrupadas[$colu] = ['Hechas' => 0, 'Auto' => 0];
                                }
                                $horasAgrupadas[$colu]['Hechas'] += horaMin($hora['Hechas']);
                                $horasAgrupadas[$colu]['Auto'] += horaMin($hora['Auto']);
                            }
                            
                            // Mostrar horas agrupadas ordenadas por Colu
                            ksort($horasAgrupadas);
                            foreach ($columnasHoras as $colu => $label) {
                                if (isset($horasAgrupadas[$colu])) {
                                    echo '<td class="center w40">', MinHora($horasAgrupadas[$colu]['Hechas']), '</td>',
                                         '<td class="center w40 bg-light">', MinHora($horasAgrupadas[$colu]['Auto']), '</td>';
                                } else {
                                    echo '<td class="w40"></td><td class="w40 bg-light"></td>';
                                }
                            }
                        } else {
                            // Sin agrupar - mostrar cada tipo de hora individualmente
                            $horasPorCodigo = [];
                            foreach ($registro['Horas'] as $hora) {
                                $horasPorCodigo[$hora['Hora']] = $hora;
                            }
                            
                            foreach ($columnasHoras as $codigo => $label) {
                                if (isset($horasPorCodigo[$codigo])) {
                                    echo '<td class="center w40">', $horasPorCodigo[$codigo]['Hechas'], '</td>',
                                         '<td class="center w40 bg-light">', $horasPorCodigo[$codigo]['Auto'], '</td>';
                                } else {
                                    echo '<td class="w40"></td><td class="w40 bg-light"></td>';
                                }
                            }
                        }
                        ?>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Fila de Totales -->
                <?php if ($_TotHoras == '1'): ?>
                    <tr>
                        <td class="right bold" colspan="4">TOTALES:</td>
                        <?php
                        // Calcular totales del grupo
                        if ($_agrupar_thcolu === true) {
                            // Totales agrupados por Colu
                            $totalesGrupo = [];
                            foreach ($grupo['registros'] as $registro) {
                                foreach ($registro['Horas'] as $hora) {
                                    $colu = $hora['Colu'];
                                    if (!isset($totalesGrupo[$colu])) {
                                        $totalesGrupo[$colu] = ['Hechas' => 0, 'Auto' => 0];
                                    }
                                    $totalesGrupo[$colu]['Hechas'] += horaMin($hora['Hechas']);
                                    $totalesGrupo[$colu]['Auto'] += horaMin($hora['Auto']);
                                }
                            }
                            
                            ksort($totalesGrupo);
                            foreach ($columnasHoras as $colu => $label) {
                                if (isset($totalesGrupo[$colu])) {
                                    echo '<td class="center w40 bold">', ceronull(MinHora($totalesGrupo[$colu]['Hechas'])), '</td>',
                                         '<td class="center w40 bg-light bold">', ceronull(MinHora($totalesGrupo[$colu]['Auto'])), '</td>';
                                } else {
                                    echo '<td class="w40"></td><td class="w40 bg-light"></td>';
                                }
                            }
                        } else {
                            // Totales sin agrupar
                            $totalesGrupo = [];
                            foreach ($grupo['registros'] as $registro) {
                                foreach ($registro['Horas'] as $hora) {
                                    $codigo = $hora['Hora'];
                                    if (!isset($totalesGrupo[$codigo])) {
                                        $totalesGrupo[$codigo] = ['Hechas' => 0, 'Auto' => 0];
                                    }
                                    $totalesGrupo[$codigo]['Hechas'] += horaMin($hora['Hechas']);
                                    $totalesGrupo[$codigo]['Auto'] += horaMin($hora['Auto']);
                                }
                            }
                            
                            foreach ($columnasHoras as $codigo => $label) {
                                if (isset($totalesGrupo[$codigo])) {
                                    echo '<td class="center w40 bold">', ceronull(MinHora($totalesGrupo[$codigo]['Hechas'])), '</td>',
                                         '<td class="center w40 bg-light bold">', ceronull(MinHora($totalesGrupo[$codigo]['Auto'])), '</td>';
                                } else {
                                    echo '<td class="w40"></td><td class="w40 bg-light"></td>';
                                }
                            }
                        }
                        ?>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
        
        <?php
        log_time("Grupo #$grupo_count renderizado (" . count($grupo['registros']) . " registros)", $t_grupo);
        
        // Salto de página si está activado
        if ($_SaltoPag == '1' && $grupo !== end($dataAgrupada)) {
            echo '<div style="page-break-before: always; clear:both"></div>';
        }
        ?>
    <?php endforeach; ?>
</body>
<?php
// ============ FIN DEL RENDERIZADO - GUARDAR LOG ============
log_time("TOTAL RENDERIZADO HTML (grupos: $grupo_count, registros: $registro_count)", $t_render_start);
log_time("SCRIPT COMPLETO", $time_start_total);

// Guardar log en archivo
$log_content = "========================================\n";
$log_content .= "LOG DE PERFORMANCE - " . date('Y-m-d H:i:s') . "\n";
$log_content .= "========================================\n";
$log_content .= "Parámetros:\n";
$log_content .= "  - _Por: $_Por\n";
$log_content .= "  - _agrupar_thcolu: " . ($_agrupar_thcolu ? 'true' : 'false') . "\n";
$log_content .= "  - _TotHoras: $_TotHoras\n";
$log_content .= "  - Total registros API: " . count($fichasData) . "\n";
$log_content .= "  - Total grupos: " . count($dataAgrupada) . "\n";
$log_content .= "  - Total columnas horas: " . count($columnasHoras) . "\n";
$log_content .= "========================================\n\n";
$log_content .= implode("\n", $time_log) . "\n\n";
$log_content .= "========================================\n";
$log_content .= "TIEMPO TOTAL: " . sprintf("%.2f", (microtime(true) - $time_start_total) * 1000) . " ms\n";
$log_content .= "========================================\n\n\n";

file_put_contents($log_file, $log_content);
?>
