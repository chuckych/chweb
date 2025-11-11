<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

// Función auxiliar para convertir color negativo a hexadecimal
function convertirColorHex($colorNegativo)
{
    if (empty($colorNegativo) || $colorNegativo == 0) {
        return '#FFFFFF';
    }

    // Convertir el número negativo a positivo y luego a hexadecimal
    $colorPositivo = abs($colorNegativo);
    $hex = str_pad(dechex($colorPositivo), 6, '0', STR_PAD_LEFT);
    return '#' . strtoupper($hex);
}

// Función auxiliar para formatear fecha
function formatearFecha($fecha)
{
    if (empty($fecha))
        return '';
    $date = DateTime::createFromFormat('Y-m-d', $fecha);
    return $date ? $date->format('d/m/Y') : $fecha;
}
function asignacionStr($prioridad, $date) {
    $dateFormat = formatearFecha($date);
    
    $splitDate = function($date) {
        $dates = explode(' - ', $date);
        $d1 = isset($dates[0]) ? formatearFecha($dates[0]) : '';
        $d2 = isset($dates[1]) ? formatearFecha($dates[1]) : '';
        return [$d1, $d2];
    };
    
    $map = [
        "1" => "Citación fecha {$dateFormat}",
        "2" => function() use ($splitDate, $date) {
            $split = $splitDate($date);
            return "Horario desde {$split[0]} hasta {$split[1]}";
        },
        "3" => function() use ($splitDate, $date, $dateFormat) {
            $dates = explode(' - ', $date);
            if (count($dates) > 1) {
                $split = $splitDate($date);
                return "Rotación desde {$split[0]} hasta {$split[1]}";
            } else {
                return "Rotación desde {$dateFormat}";
            }
        },
        "4" => "Horario desde {$dateFormat}"
    ];
    
    if (isset($map[$prioridad])) {
        if (is_callable($map[$prioridad])) {
            return $map[$prioridad]();
        } else {
            return $map[$prioridad];
        }
    }
    
    return '';
}

echo '<body backtop="5mm" backbottom="10mm">';
// error_log(print_r($data, true));
// Verificar que existan datos
if (empty($data) || !is_array($data)) {
    echo '<p style="text-align: center; color: red; font-size: 14pt; margin-top: 50px;">No hay datos para mostrar</p>';
} else {
    $totalEmpleados = count($data);
    $contadorEmpleado = 0;

    // Iterar sobre cada legajo (clave asociativa del array principal)
    foreach ($data as $legajo => $registros) {
        $contadorEmpleado++;

        // Verificar que haya registros para este legajo
        if (empty($registros) || !is_array($registros)) {
            continue;
        }

        // Obtener el nombre del empleado del primer registro
        $primerRegistro = reset($registros);
        $nombreEmpleado = $primerRegistro['Nombre'] ?? 'Sin Nombre';

        // Iniciar tabla para este empleado
        echo '<div style="padding-bottom: 5px;padding-top: 10px;">';
        echo '<hr>';
        echo '<p class="bold" style="padding-top: 5px;">' . $legajo . ' - ' . $nombreEmpleado . '</p>';
        echo '<hr>';
        echo '<table border="0" style="width:100%; border-collapse: collapse; font-size: 8pt;">';

        // Encabezado de la tabla
        echo '<thead>';
        echo '<tr>';
        echo '<th class="bold" style="padding: 5px;">Fecha</th>';
        echo '<th class="bold" style="padding: 5px;">Día</th>';
        echo '<th class="bold" style="padding: 5px;">Horario</th>';
        echo '<th class="bold" style="padding: 5px;">Hs. A Trab.</th>';
        echo '<th class="bold" style="padding: 5px;">ID</th>';
        echo '<th class="bold" style="padding: 5px;">Descripción Horario</th>';
        echo '<th class="bold" style="padding: 5px;">Asignacion</th>';
        // echo '<th class="bold" style="padding: 5px;"></th>';
        echo '</tr>';
        echo '</thead>';

        // Cuerpo de la tabla
        echo '<tbody>';

        $totalHsATrab = 0;
        $totalHsDelDia = 0;

        foreach ($registros as $registro) {
            // Obtener el color de fondo si existe
            // $colorFondo = '#FFFFFF';
            // if (!empty($registro['HorColor']) && $registro['HorColor'] != 0) {
            //     $colorFondo = convertirColorHex($registro['HorColor']);
            // }

            // Determinar si es franco para aplicar estilo especial
            $esFranco = ($registro['Horario']) === 'FRANCO';
            $esFeriado = ($registro['Feriado']) === '1';
            $CodigoHorario = $registro['CodigoHorario'];
            $DescripcionHorario = $CodigoHorario === '0' ? 'Sin horario asignado ' : '(' . $CodigoHorario . ') ' . $registro['DescripcionHorario'];
           
            echo '<tr>';
            echo '<td style="padding: 4px;">' . (formatearFecha($registro['Fecha'])) . '</td>';
            echo '<td style="padding: 4px;">' . ($registro['Dia']) . '</td>';
            if ($CodigoHorario === '0') {
                echo '<td colspan="7" style="padding: 4px;">' . $DescripcionHorario . '</td>';
                continue;
            }
            if ($esFeriado) {
                echo '<td style="padding: 4px;">Feriado</td>';
            } else {
                echo '<td style="padding: 4px;">' . ucfirst(strtolower($registro['Horario'])) . '</td>';
            }
            echo '<td style="padding: 4px;">' . ($registro['HsATrab']) . '</td>';
            echo '<td style="padding: 4px;">' . ($registro['HorID']) . '</td>';
            echo '<td style="padding: 4px;">' . $DescripcionHorario . '</td>';
            echo '<td style="padding: 4px;">' . asignacionStr($registro['Prioridad'], $registro['Referencia']) . '</td>';
            echo '</tr>';

            // Acumular totales (convertir formato HH:MM a minutos para sumar correctamente)
            if (!empty($registro['HsATrab']) && $registro['HsATrab'] !== '00:00') {
                $partes = explode(':', trim($registro['HsATrab']));
                if (count($partes) === 2 && is_numeric($partes[0]) && is_numeric($partes[1])) {
                    $totalHsATrab += intval($partes[0]) * 60 + intval($partes[1]);
                }
            }

            // if (!empty($registro['HsDelDia']) && $registro['HsDelDia'] !== '00:00') {
            //     $partes = explode(':', trim($registro['HsDelDia']));
            //     if (count($partes) === 2 && is_numeric($partes[0]) && is_numeric($partes[1])) {
            //         $totalHsDelDia += (intval($partes[0]) * 60) + intval($partes[1]);
            //     }
            // }
        }

        // Fila de totales
        $horasATrab = intval($totalHsATrab / 60);
        $minutosATrab = intval($totalHsATrab % 60);
        $totalHsATrabFormato = sprintf('%02d:%02d', $horasATrab, $minutosATrab);

        // $horasDelDia = intval($totalHsDelDia / 60);
        // $minutosDelDia = intval($totalHsDelDia % 60);
        // $totalHsDelDiaFormato = sprintf('%02d:%02d', $horasDelDia, $minutosDelDia);
        if ($totalHsATrabFormato != '00:00') {
            echo '<tr>';
            echo '<td colspan="7"><hr></td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td colspan="3"></td>';
            // echo '<td colspan="4" style="padding: 5px; text-align: right;">TOTALES:</td>';
            echo '<td style="padding: 5px;" class="bold">' . $totalHsATrabFormato . '</td>';
            // echo '<td style="padding: 5px; text-align: center;">' . $totalHsDelDiaFormato . '</td>';
            echo '<td colspan="3"></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '<hr>';
        echo '</div>';

        // Salto de página si está configurado y no es el último empleado
        if ($_SaltoPag === '1' && $contadorEmpleado < $totalEmpleados) {
            echo '<div style="page-break-after: always;"></div>';
        }
    }
}

echo '</body>';