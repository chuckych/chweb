<?php
function debug_to_json($data)
{
    Flight::json([
        'debug' => $data
    ]);
    exit;
}
try {
    unset($payload['data']);

    $payload['NovA'] = [];
    $payload['getHor'] = "1";
    $payload['getNov'] = "1";

    foreach (['FechIni', 'FechFin'] as $key) {
        if (!$payload[$key] ?? '') { // Validar que las fechas estén presentes
            throw new Exception("{$key} es requerida", 400);
        }
        $payload[$key] = date('Y-m-d', strtotime($payload[$key])); // Convertir la fecha a formato 'Y-m-d'
    }

    $hashPayload = md5(json_encode($payload));

    // Obtener la lista de legajos según los parámetros especificados en $payload
    // $legajos = v1_api('/fichas/legajos', 'POST', $payload) ?? []; // Obtener legajos
    $legajos = cacheData(
        "legajos-{$hashPayload}",
        $payload,
        'legajos',
        '/fichas/legajos'
    );

    if (!$legajos) { // Si no hay datos, retornar un array vacío
        throw new Exception('No se encontraron legajos', 200);
    }

    // Itera sobre la estructura obtenida con los parámetros especificados
    foreach (get_estructura(['length' => 1000, 'Estruct' => 'Sec', 'flag' => $flag]) ?? [] as $sector) {
        // Asigna al array $sectores el código del sector como clave y la primera palabra de la descripción como valor
        // Si no hay un espacio en la descripción, usa la descripción completa
        $sectores[$sector['Codi']] = strstr($sector['Desc'], ' ', true) ?: $sector['Desc'];
    }

    $payload['length'] = 100000;
    // $payload['LegTipo'] = [];

    array_walk($legajos, function (&$value) use ($sectores) {
        // Asigna a 'FicSectStr' el valor correspondiente del array $sectores
        // Si no existe una clave en $sectores para 'FicSectStr', asigna una cadena vacía
        $value['FicSectStr'] = $sectores[$value['FicSect']] ?? '';
    });

    $colsExcel = colsExcel();

    if (!$colsExcel) { // Si no hay datos, retornar un array vacío
        return Flight::json([]);
    }

    $keysHoras = array_keys($colsExcel);
    $legajosColumn = array_column($legajos, null, 'FicLega');
    $defaultValues = array_fill_keys($keysHoras, 0); // añadir keyHoras a legajosColumn con el valor 0

    array_walk($legajosColumn, function (&$item) use ($defaultValues) { // Iterar sobre legajosColumn
        $item += $defaultValues; // Añadir valores por defecto
    });

    $payload['HsTrAT'] = 1;

    $hashPayload = md5(json_encode($payload));

    // $horas = v1_api('/horas/totales', 'POST', $payload) ?? []; // Obtener tipos de horas
    $horas = cacheData(
        "horas-totales-{$hashPayload}",
        $payload,
        'horas totales',
        '/horas/totales'
    );

    $horasData = $horas['data'] ?? []; // Obtener datos de horas totales por legajo
    $horasColumn = array_column($horasData, null, 'Lega');

    // $payload['Dias'] = [2, 3, 4, 5, 6, 7];
    // $payload['Hora'] = [90];
    $payload['HoraMin'] = "00:01";
    $payload['HoraDia'] = "1";

    $payloadHorasDia = [
        'FechaDesde' => $payload['FechIni'],
        'FechaHasta' => $payload['FechFin'],
        'SinHorarios' => 1,
        'Egreso' => 1,
        'flag' => $payload['flag']
    ];

    $hashPayload = md5(json_encode($payloadHorasDia));

    $sumarMinutosPorClave = static function (array $data): array {
        $out = [];
        foreach ($data as $clave => $filas) {
            $total = 0;
            if (!is_array($filas)) {
                $out[$clave] = 0;
                continue;
            }
            foreach ($filas as $fila) {
                if (!is_array($fila))
                    continue;
                // Excluir los registros de Domingo y Sábado
                if (($fila['Dia'] ?? '') === 'Domingo' || ($fila['Dia'] ?? '') === 'Sábado')
                    continue;

                if (!array_key_exists('HsDelDia', $fila))
                    continue;
                $v = $fila['HsDelDia'];
                if ($v === null || $v === '')
                    continue; // sólo si no es null ni vacío
                $total += to_minutes($v);
            }
            $out[$clave] = $total;
        }
        return $out;
    };

    $sumarMinutosPorClave2 = static function (array $data): array {
        $out = [];
        foreach ($data as $clave => $filas) {
            $total = 0;
            if (!is_array($filas)) {
                $out[$clave] = 0;
                continue;
            }
            foreach ($filas as $fila) {
                if (!is_array($fila))
                    continue;
                // Excluir los registros de Domingo y Sábado
                if (($fila['Dia'] ?? '') !== 'Sábado')
                    continue;

                if (!array_key_exists('HsDelDia', $fila))
                    continue;
                $v = $fila['HsDelDia'];
                if ($v === null || $v === '')
                    continue; // sólo si no es null ni vacío
                $total += to_minutes($v);
            }
            $out[$clave] = $total;
        }
        return $out;
    };

    $sumarMinutosPorClaveSabado = static function (array $data): array {
        $out = [];
        foreach ($data as $clave => $filas) {
            if (!is_array($filas)) {
                $out[$clave] = [];
                continue;
            }
            foreach ($filas as $fila) {
                if (!is_array($fila))
                    continue;
                // Excluir los registros que no sean de Sábado
                if (($fila['Dia'] ?? '') !== 'Sábado')
                    continue;

                // Agregar toda la fila completa al array de resultados
                $out[$clave][] = $fila;
            }
        }
        return $out;
    };

    $datosHorasDia = cacheData(
        "horas-dia-{$hashPayload}",
        $payloadHorasDia,
        'horas dia',
        '/horarios/asignados'
    );

    $idealesHorasDia = $sumarMinutosPorClave($datosHorasDia);
    $idealesHorasDiaSabado = $sumarMinutosPorClaveSabado($datosHorasDia);

    // buscar las horas de los sabados solo si tiene el tipo de hora 23 y 90
    $ficNoveHora = fic_nove_horas([
        'FechIni' => $payload['FechIni'] ?? null,
        'FechFin' => $payload['FechFin'] ?? null,
        'getHor' => 1,
        'Hora' => [23, 90],
        'HoraEx' => 1,
        // 'Lega' => [269],
        'Dias' => [7],
        'length' => 10000
    ]) ?? [];

    $dataficNoveHora = [];
    // Agrupar los registros por legajo
    foreach ($ficNoveHora as $registro) {
        $lega = $registro['Lega'];
        if (!isset($dataficNoveHora[$lega])) {
            $dataficNoveHora[$lega] = [];
        }
        $dataficNoveHora[$lega][] = $registro;
    }
    ksort($dataficNoveHora);

    $idealesHorasDiaSabadoFiltrado = array_filter($idealesHorasDiaSabado, function ($key) use ($dataficNoveHora) {
        return array_key_exists($key, $dataficNoveHora);
    }, ARRAY_FILTER_USE_KEY);

    $fechasDisponibles = [];
    foreach ($dataficNoveHora as $legajo => $registros) {
        foreach ($registros as $registro) {
            $fechasDisponibles[$legajo][] = $registro['Fech'];
        }
    }

    // Filtrar idealesHorasDiaSabadoFiltrado comparando fechas
    $idealesHorasDiaSabadoFinal = [];
    foreach ($idealesHorasDiaSabadoFiltrado as $legajo => $horarios) {
        if (isset($fechasDisponibles[$legajo])) {
            foreach ($horarios as $horario) {
                if (in_array($horario['Fecha'], $fechasDisponibles[$legajo])) {
                    $idealesHorasDiaSabadoFinal[$legajo][] = $horario;
                }
            }
        }
    }

    $idealesSabado = $sumarMinutosPorClave2($idealesHorasDiaSabadoFinal);

    foreach (['Dias', 'Hora', 'HoraMin'] as $value) {
        unset($payload[$value]);
    }

    function sumarHoras23a90(&$array)
    {
        foreach ($array as &$empleado) {
            if (!empty($empleado['Totales'])) {
                $minutosHoras23 = 0;
                $encontrado90 = false;

                foreach ($empleado['Totales'] as &$registro) {
                    if ($registro['HoraCodi'] == 23) {
                        $minutosHoras23 = $registro['EnMinutos2'];
                    }
                    if ($registro['HoraCodi'] == 90) {
                        $registro['EnMinutos2'] += $minutosHoras23;
                        $encontrado90 = true;
                    }
                }

                if (!$encontrado90 && $minutosHoras23 > 0) {
                    // Si no existe HoraCodi 90 pero sí existe 23, crear el registro 90
                    $empleado['Totales'][] = [
                        'HoraCodi' => 90,
                        'THoDesc' => 'HORAS NORMALES',
                        'THoDesc2' => 'NORMAL',
                        'Cantidad' => 0,
                        'EnHoras' => '00:00',
                        'EnHoras1' => '00:00',
                        'EnHoras2' => '00:00',
                        'EnMinutos' => 0,
                        'EnMinutos1' => 0,
                        'EnMinutos2' => $minutosHoras23,
                        'EnHorasDecimal' => 0,
                        'EnHorasDecimal1' => 0,
                        'EnHorasDecimal2' => $minutosHoras23 / 60
                    ];
                }
            }
        }
        unset($empleado, $registro);
    }

    /* === */
    sumarHoras23a90($horasColumn);
    /* === */

    // añadir las horas a legajosColumn con el valor correspondiente según el tipo de hora
    array_walk($horasColumn, function ($value, $Lega) use (&$legajosColumn) {
        if (!empty($value['Totales'])) { // Si hay datos en 'Totales'
            foreach ($value['Totales'] as $total) { // Iterar sobre 'Totales'
                if (isset($total['THoDesc2'], $total['EnMinutos2'])) { // Si existen 'THoDesc2' y 'EnHoras2'
                    $legajosColumn[$Lega][$total['THoDesc2']] = $total['EnMinutos2']; // Asignar 'EnHoras2' a 'THoDesc2'
                }
            }
        }
        $hsTr = $value['HsATyTR'] ?? ''; // Obtener 'HsATyTR'
        if (!empty($hsTr)) { // Si hay datos en 'HsATyTR'
            $legajosColumn[$Lega]['HsATr'] = $hsTr['HsATEnMinutos']; // Asignar 'HsATr' a 'HsATEnHoras'
            $legajosColumn[$Lega]['HsTr'] = $hsTr['HsTrEnMinutos']; // Asignar 'HsTr' a 'HsTrEnHoras'
        }
    });

    // añadir el valor de minutos de idealesHorasDia de cada legajo a legajosColumn
    array_walk($idealesHorasDia, function ($value, $Lega) use (&$legajosColumn) {
        if (isset($legajosColumn[$Lega])) {
            $legajosColumn[$Lega]['Ideales'] = $value;
        }
    });

    $clavesCustom = [
        'Enferm' => 'Enferm',
        'Accid' => 'Accid',
        'Susp. Disc' => 'SuspDisc',
        'Vac' => 'Vac',
        'Susp' => 'Susp',
        'Paro' => 'Paro',
        'Incid' => 'Incid',
    ];

    $valoresCustom = get_params(['cliente' => $cliente, 'descripcion' => '', 'modulo' => 46]) ?? [];
    $params = $valoresCustom;
    $valoresCustom = array_column($valoresCustom, null, 'descripcion');
    
    foreach ($clavesCustom as $key => $clave) {
        
        $valores = $valoresCustom[$key]['valores'] ?? '';
        // error_log(print_r($valores, true));

        // Limpiar y filtrar códigos
        $codNove = array_filter(
            array_map('trim', explode(',', $valores)),
            function ($item) {
                return $item !== '' && $item !== null;
            }
        );

        // Re-indexar para tener índices consecutivos
        $codNove = array_values($codNove);
        
        $payload['Nove'] = $codNove;

        if (count($codNove) > 0) {

            $payload['Nove'] = $codNove;

            $legajosColumn = horas_custom(
                $legajosColumn,
                $payload,
                $key
            );

            unset($payload['Nove']);
        }
    }

    $tipoHora = cacheData(
        "tipo-horas-{$flag}",
        [],
        'tipos horas',
        '/horas/data',
        'GET'
    );

    $horasParams = horasCustom($params);
    array_push($horasParams, ...$tipoHora);
    $detalleTipoHoras = array_column($horasParams, null, 'THoCodi');

    $tipoHora = array_column($tipoHora, null, 'THoCodi');
    $strMerienda = $tipoHora['50']['THoDesc2'] ?? 'MERIENDA'; // Obtener la descripcion 2 de 'HORAS MERIENDA'
    $strMerienda2 = $tipoHora['51']['THoDesc2'] ?? 'MER2'; // Obtener la descripcion 2 de 'MER2'
    $strNormales = $tipoHora['90']['THoDesc2'] ?? 'NORMAL'; // Obtener la descripcion 2 de 'HORAS NORMALES'
    foreach ($legajosColumn as $key => $value) {
        $horasATrabajar = $value['HsATr'] ?? 0;
        $horasIdeales = $value['Ideales'] ?? 0;
        $horasMerienda = $value[$strMerienda] ?? 0;
        $horasMerienda2 = $value[$strMerienda2] ?? 0;
        $horasNormales = $value[$strNormales] ?? 0;

        $idealesSab = $idealesSabado[$key] ?? 0;

        $meriendaTotal = $horasMerienda + $horasMerienda2;

        $legajosColumn[$key]['IdealesSinSabado'] = $horasIdeales - $meriendaTotal;
        $legajosColumn[$key]['IdealesSabado'] = $idealesSab;
        $Ideales = $horasIdeales - $meriendaTotal + $idealesSab;
        $legajosColumn[$key]['Ideales'] = $Ideales;

        $varias = array_sum([$Ideales, $meriendaTotal]);

        $variasARestar = array_sum([
            $horasNormales,
            $value['Enferm'] ?? 0,
            $value['Accid'] ?? 0,
            $value['Susp. Disc'] ?? 0,
            $value['Vac'] ?? 0,
            $value['Susp'] ?? 0,
            $value['Paro'] ?? 0,
            $value['Incid'] ?? 0
        ]);

        $calculoVarias = $varias - $variasARestar;
        $legajosColumn[$key]['Varias'] = $calculoVarias >= 0 ? $calculoVarias : 0;
    }

    // recorrer legajosColumn y todos los valores que sean del tipo integer aplicarle al funcion minutos_a_horas_decimal
    array_walk_recursive($legajosColumn, function (&$item, $key) {
        if (is_int($item)) {
            $item = minutos_a_horas_decimal($item);
        }
    });

    $columnasSorted = $valoresCustom['columnasSorted']['valores'] ?? '';
    $columnasSorted = explode(',', $columnasSorted);
    $columnasSorted = array_map('intval', $columnasSorted);

    $initKeys = [
        'FicSectStr' => [
            'titulo' => 'Sector',
            'tipo' => 'string',
        ],
        'FicLega' => [
            'titulo' => 'Legajo',
            'tipo' => 'string',
        ],
        'FicApNo' => [
            'titulo' => 'Apellido y Nombre',
            'tipo' => 'string',
        ],
    ];

    foreach ($columnasSorted as $key => $value) {
        $clave = $detalleTipoHoras[$value]['THoDesc2'] ?? '';
        $columnKeys[$clave] = [
            'titulo' => $clave,
            'tipo' => 'number',
        ];
    }

    $columnKeys = array_merge($initKeys, $columnKeys);

    $rs = [
        'data' => array_values($legajosColumn),
        'columnKeys' => $columnKeys,
    ];

    if ($tipo == 'view') {
        return Flight::json($rs);
    }

    if ($tipo == 'xls') {
        $Datos['Data'] = $legajosColumn;
        require __DIR__ . '/../fn_spreadsheet.php';
        $colsExcel = colsExcel();
        include __DIR__ . '/../../informes/custom/prysmian/xls2.php';
    }

} catch (\Throwable $th) {
    throw new Exception($th->getMessage(), 400);
}