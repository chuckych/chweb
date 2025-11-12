<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Función auxiliar para formatear fecha
function formatearFechaPlanilla($fecha)
{
    if (empty($fecha))
        return '';
    $date = DateTime::createFromFormat('Y-m-d', $fecha);
    return $date ? $date->format('d/m') : $fecha;
}

// Función auxiliar para obtener el día de la semana
function obtenerDiaSemana($fecha)
{
    if (empty($fecha))
        return '';
    $date = DateTime::createFromFormat('Y-m-d', $fecha);
    return $date ? $date->format('D') : '';
}

echo '<body backtop="5mm" backbottom="10mm">';

// Verificar que existan datos
if (empty($data) || !is_array($data)) {
    echo '<p style="text-align: center; color: red; font-size: 12pt; margin-top: 50px;">No hay datos para mostrar</p>';
} else {

    // Obtener las fechas de la semana del primer empleado
    $primerEmpleado = reset($data);
    $fechasSemana = [];

    if (!empty($primerEmpleado) && is_array($primerEmpleado)) {
        foreach ($primerEmpleado as $registro) {
            $fechasSemana[] = [
                'fecha' => $registro['Fecha'],
                'dia' => $registro['Dia']
            ];
        }
    }

    // Recopilar todos los horarios únicos para la sección de referencias
    $horariosReferencia = [];

    echo '<div style="margin-bottom: 10px;">';
    // Crear tabla de planilla
    echo '<p style="padding: 3px;"></p>';
    echo '<table class="tabla" border="1" style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-top: 10px;">';

    // Encabezado de la tabla
    echo '<thead>';
    echo '<tr>';
    echo '<th style="padding: 5px;" class="bold">Legajo / Nombre</th>';

    // Columnas de los días de la semana
    foreach ($fechasSemana as $fechaInfo) {
        $fechaFormateada = formatearFechaPlanilla($fechaInfo['fecha']);
        $dia = $fechaInfo['dia'];
        echo '<th style="padding: 5px; text-align: center; width: 80px;" class="bold">' . $dia . '<br>' . $fechaFormateada . '</th>';
    }

    // Columna de Total
    echo '<th style="padding: 5px; text-align: center; width: 40px;" class="bold">Total<br>Hs.</th>';

    echo '</tr>';
    echo '</thead>';

    // Cuerpo de la tabla
    echo '<tbody>';
    $citCount = 0;
    // Iterar sobre cada empleado
    foreach ($data as $legajo => $registros) {

        // Verificar que haya registros para este legajo
        if (empty($registros) || !is_array($registros)) {
            continue;
        }

        // Obtener el nombre del empleado del primer registro
        $primerRegistro = reset($registros);
        $nombreEmpleado = $primerRegistro['Nombre'] ?? 'Sin Nombre';

        echo '<tr>';
        echo '<td style="padding: 5px;">' . htmlspecialchars($legajo) . '<br>' . htmlspecialchars($nombreEmpleado) . '</td>';

        // Variable para acumular el total de horas de este empleado
        $totalMinutosEmpleado = 0;

        // Iterar sobre los días de la semana para este empleado
        foreach ($registros as $registro) {
            $horID = $registro['HorID'] ?? '';
            $prioridad = $registro['Prioridad'] ?? '';
            $entrada = $registro['Entrada'] ?? '';
            $salida = $registro['Salida'] ?? '';
            $horario = $registro['Horario'] ?? '';
            $codigoHorario = $registro['CodigoHorario'] ?? '';
            $descripcionHorario = $registro['DescripcionHorario'] ?? '';
            $hsATrab = $registro['HsATrab'] ?? '00:00';
            $esFranco = strtoupper($horario) === 'FRANCO';
            $esFeriado = ($registro['Feriado'] ?? '0') === '1';
            $citacion = '';

            // Calcular minutos de este día y sumar al total
            if (!empty($hsATrab) && $hsATrab !== '00:00') {
                $partes = explode(':', trim($hsATrab));
                if (count($partes) === 2 && is_numeric($partes[0]) && is_numeric($partes[1])) {
                    $totalMinutosEmpleado += (intval($partes[0]) * 60) + intval($partes[1]);
                }
            }


            // Guardar horario para referencias (solo si no es franco y tiene ID)
            if (!$esFranco && !empty($horID) && $codigoHorario !== '0') {
                $horariosReferencia[$horID] = [
                    'codigo' => $codigoHorario,
                    'descripcion' => $descripcionHorario,
                    'horario' => $horario
                ];
            }

            if ($prioridad === '1') {
                $horario = $entrada . ' a ' . $salida;
                $horID .= ' - CIT';
                $citCount++;
            }

            // Determinar el estilo de la celda
            $estiloFondo = '';
            if ($esFeriado) {
                $estiloFondo = 'background-color: #FFE6E6;'; // Rojo claro para feriados
            } elseif ($codigoHorario === '0') {
                $estiloFondo = 'background-color: #F0F0F0;'; // Gris claro para sin horario
            } elseif ($esFranco) {
                $estiloFondo = 'background-color: #FFF2CC;'; // Amarillo claro para francos
            }

            echo '<td style="padding: 4px; text-align: center; ' . $estiloFondo . '">';

            if ($esFeriado) {

                if ($prioridad === '1') {
                    echo '<div class="bold">Feriado (CIT)</div>';
                    echo '<div>' . ucfirst(strtolower($horario)) . '</div>';
                    continue;
                }
                echo 'Feriado';
            } elseif ($codigoHorario === '0' || empty($horID)) {
                echo 'Sin horario';
            } else {
                // echo '<p style="line-height:1.5">';
                echo '<div class="bold">' . $horID . '</div>';
                echo '<div>' . ucfirst(strtolower($horario)) . '</div>';
                // echo '</p>';
            }

            echo '</td>';
        }

        // Calcular y mostrar el total de horas en formato HH:MM
        $horasTotal = intval($totalMinutosEmpleado / 60);
        $minutosTotal = intval($totalMinutosEmpleado % 60);
        $totalFormateado = sprintf('%02d:%02d', $horasTotal, $minutosTotal);

        // Celda de total con fondo destacado
        echo '<td style="padding: 4px; text-align: center; font-weight: bold;">' . $totalFormateado . '</td>';

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    // Sección de Referencias
    if (!empty($horariosReferencia)) {
        echo '<div style="margin-top: 15px; page-break-inside: avoid;">';
        echo '<p style="padding: 5px; margin: 0; border-bottom: 0.1pt solid #000;">REFERENCIAS DE HORARIOS</p>';
        echo '<table border="0" style="width: 100%; border-collapse: collapse; font-size: 8pt; margin-top: 5px;">';

        echo '<thead>';
        echo '<tr class="bold">';
        echo '<th style="padding: 5px; width: 60px;">ID</th>';
        echo '<th style="padding: 5px; width: 100px;">Código</th>';
        echo '<th style="padding: 5px; width: 120px;">Horario</th>';
        echo '<th style="padding: 5px; text-align: left;">Descripción</th>';
        echo '</tr>';
        echo '</thead>';

        echo '<tbody>';

        // Ordenar las referencias por ID
        ksort($horariosReferencia);

        foreach ($horariosReferencia as $id => $info) {
            echo '<tr>';
            echo '<td style="padding: 4px;"><strong>' . $id . '</strong></td>';
            echo '<td style="padding: 4px;">' . $info['codigo'] . '</td>';
            echo '<td style="padding: 4px;">' . $info['horario'] . '</td>';
            echo '<td style="padding: 4px;">' . $info['descripcion'] . '</td>';
            echo '</tr>';
        }

        if ($citCount > 0) {
            echo '<tr>';
            echo '<td style="padding: 4px;"><strong>CIT</strong></td>';
            echo '<td style="padding: 4px;">-</td>';
            echo '<td style="padding: 4px;">-</td>';
            echo '<td style="padding: 4px;">Citación de horario</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '<p style="padding: 5px; margin: 0; border-bottom: 0.1pt solid #000;"></p>';
        echo '</div>';
    }

    $mapCodigos = [
        'EmpCodi' => 'Empresa',
        'PlaCodi' => 'Planta',
        'ConCodi' => 'Convenio',
        'SecCodi' => 'Sector',
        'GruCodi' => 'Grupo',
        'SucCodi' => 'Sucursal',
        'Se2Codi' => 'Sección',
    ];

    if (!empty($estructuras)) {
        echo '<div style="margin-top: 15px; page-break-inside: avoid;">';
        // echo '<p style="padding: 5px; margin: 0; border-bottom: 0.1pt solid #000;">Estructuras</p>';

        // Iterar sobre cada tipo de estructura (Empresas, Sectores, Secciones)
        foreach ($estructuras as $tipoEstructura => $items) {
            if (empty($items) || !is_array($items)) {
                continue;
            }

            echo '<table border="0" style="border-collapse: collapse; font-size: 8pt; margin-top: 2px;">';

            // Encabezado dinámico basado en las claves del primer elemento
            if (!empty($items[0])) {
                echo '<thead>';
                echo '<tr>';

                foreach (array_keys($items[0]) as $columna) {
                    // Formatear nombres de columnas
                    $nombreColumna = $columna;
                    if (strpos($columna, 'Codi') !== false) {
                        $nombreColumna = $mapCodigos[$columna] ?? 'Código';
                    } elseif (strpos($columna, 'Desc') !== false || strpos($columna, 'Razon') !== false) {
                        $nombreColumna = '';
                    }
                    echo '<th class="bold" style="padding: 5px;">' . $nombreColumna . '</th>';
                }

                echo '</tr>';
                echo '</thead>';
            }

            // Cuerpo de la tabla
            echo '<tbody>';

            foreach ($items as $item) {
                echo '<tr>';

                foreach ($item as $valor) {
                    echo '<td style="padding: 4px;">' . $valor . '</td>';
                }

                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }

        echo '</div>';
    }
    if (!empty($payload['Tipo'])) {

        $tipos = $payload['Tipo'];
        $countTipo = count($tipos);

        echo '<div style="padding: 5px; font-size: 8pt; margin-top: 2px;" class="bold">Tipo de Personal</div>';

        if ($countTipo === 1) {
            $labelTipo = ($tipos[0] == 1) ? 'Jornales' : 'Mensuales';
            echo '<div style="padding-left: 5px; font-size: 8pt;">' . $labelTipo . '</div>';
        } elseif ($countTipo === 2) {
            echo '<div style="padding-left: 5px; font-size: 8pt;">Jornales | Mensuales</div>';
        }
    }
}

echo '</body>';