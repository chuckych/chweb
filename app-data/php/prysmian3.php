<?php
/**
 * Obtiene el sufijo basado en el tipo y estado de la fichada
 * @param array $fichada - Elemento del array Fich
 * @return string - Sufijo a concatenar (N, NM, M, MM)
 */
function obtenerEstadoFichada($fichada)
{
    $tipo = $fichada['Tipo'] ?? '';
    $estado = $fichada['Esta'] ?? '';

    if ($tipo === 'Normal') {
        return ($estado === 'Modificada') ? 'NM' : 'N';
    } elseif ($tipo === 'Manual') {
        return ($estado === 'Modificada') ? 'MM' : 'M';
    }

    return ''; // Por defecto, sin sufijo
}
/**
 * Obtiene el valor Auto de una hora específica y lo convierte a decimal
 * @param array $horas - Array de horas
 * @param string $codigoHora - Código de la hora a buscar (ej: "90")
 * @return float - Valor Auto en formato decimal (ej: 8.0) o 0 si no se encuentra
 */
function obtenerHoraAutoDecimal($horas, $codigoHora)
{
    // Crear array asociativo para búsqueda rápida
    $horasIndexadas = array_column($horas, null, 'Hora');

    // Buscar la hora específica
    if (isset($horasIndexadas[$codigoHora])) {
        $valorAuto = $horasIndexadas[$codigoHora]['Auto'] ?? '00:00';
        // Convertir HH:MM a decimal usando la función existente
        return round(minutos_a_horas_decimal(to_minutes($valorAuto)), 2);
    }

    return 0.0;
}
/**
 * Obtiene la suma de horas de novedades según códigos específicos
 * @param array $novedades - Array de novedades
 * @param array $codigosNovedad - Array de códigos de novedad a buscar (ej: ["4", "204"])
 * @return float - Suma total de horas en formato decimal o 0 si no se encuentran
 */
function obtenerSumaHorasNovedades($novedades, $codigosNovedad)
{
    // Validar que el array de novedades no esté vacío
    if (empty($novedades) || empty($codigosNovedad)) {
        return 0.0;
    }

    // Crear array asociativo para búsqueda rápida
    $novedadesIndexadas = array_column($novedades, null, 'Codi');

    $horasEncontradas = [];

    // Buscar cada código y obtener sus horas
    foreach ($codigosNovedad as $codigo) {
        if (isset($novedadesIndexadas[$codigo])) {
            $horas = $novedadesIndexadas[$codigo]['Horas'] ?? '00:00';
            // Convertir HH:MM a decimal
            $horasEncontradas[] = minutos_a_horas_decimal(to_minutes($horas));
        }
    }

    // Sumar todas las horas encontradas y redondear
    return round(array_sum($horasEncontradas), 2);
}
function cacheDataFicNoveHoras($nameFile, $payload, $nameCache = '')
{
    $inicio = microtime(true);
    $filePath = __DIR__ . "/../json/{$nameFile}.json";
    $r = [];

    if (is_file($filePath)) {
        $json = @file_get_contents($filePath);
        if ($json !== false) {
            $r = json_decode($json, true) ?? [];
            $fin = microtime(true);
            debugLog("Cache hit {$nameCache} - Tiempo: " . round($fin - $inicio, 2));
        }
    } else {
        $api = fic_nove_horas($payload) ?? [];
        if (@file_put_contents($filePath, json_encode($api, JSON_PRETTY_PRINT)) === false) {
            debugLog("Error al guardar cache en {$filePath} - {$nameCache}");
        }
        $r = $api;
        $fin = microtime(true);
        debugLog("Cache miss {$nameCache} - Tiempo: " . round($fin - $inicio, 2));
    }
    return $r;
}
function horasADecimal($hora)
{
    return round(minutos_a_horas_decimal(to_minutes($hora)));
}
try {
    $result = [];
    $inicioScript = microtime(true);

    foreach (['FechIni', 'FechFin'] as $key) {
        if (!$payload[$key] ?? '') { // Validar que las fechas estén presentes
            throw new Exception("{$key} es requerida", 400);
        }
        $payload[$key] = date('Y-m-d', strtotime($payload[$key])); // Convertir la fecha a formato 'Y-m-d'
    }
    $payload['getHor'] = "1";
    $payload['getNov'] = "1";
    $payload['getEstruct'] = "1";
    $payload['getReg'] = "1";
    // $payload['Lega'] = [30];
    unset($payload['data']);
    unset($payload['NovA']);
    $hashPayload = md5(json_encode($payload));
    $sectores = get_estructura(['length' => 1000, 'Estruct' => 'Sec', 'flag' => $hashPayload]) ?? [];
    $sectores = array_column($sectores, 'Desc', 'Codi');

    $inicioFicNoveHoras = microtime(true);
    $ficNoveHora = cacheDataFicNoveHoras("fic_nove_horas-{$hashPayload}", $payload, "fic_nove_horas") ?? [];
    $totalEjecutionNoveHoras = totalEjecution($inicioFicNoveHoras);

    foreach ($ficNoveHora as $key => $value) {

        $fichadas = $value['Fich'] ?? [];
        $totalFichadas = count($fichadas) ?? 0;
        $horas = $value['Horas'] ?? [];
        $novedades = $value['Nove'] ?? [];

        $primerFichada = $ultimaFichada = $estadoInicial = $estadoFinal = '';

        // Primera fichada con sufijo
        if ($totalFichadas > 0) {
            // Primera fichada
            $fichadaInicial = $fichadas[0]['HoRe'] ?? '';
            $estadoInicial = obtenerEstadoFichada($fichadas[0]);
            // $primerFichada = "$fichadaInicial ($estadoInicial)";
            $primerFichada = $fichadaInicial;

            // Última fichada si hay mas de 1
            if ($totalFichadas > 1) {
                $ultimaFichadaData = end($fichadas);
                $fichadaFinal = $ultimaFichadaData['HoRe'] ?? '';
                $estadoFinal = obtenerEstadoFichada($ultimaFichadaData);
                // $ultimaFichada = "$fichadaFinal ($estadoFinal)";
                $ultimaFichada = $fichadaFinal;
            }

        }

        $normalE = obtenerSumaHorasNovedades($novedades, [4, 6, 204, 206, 410]);
        $enf = obtenerSumaHorasNovedades($novedades, [3, 104, 203, 401]);
        $bl = obtenerSumaHorasNovedades($novedades, [7, 107, 207, 417]);
        $acc = obtenerSumaHorasNovedades($novedades, [210, 501]);
        $art = obtenerSumaHorasNovedades($novedades, [801]);
        $descomp = obtenerSumaHorasNovedades($novedades, [309]);
        $hsInj = obtenerSumaHorasNovedades($novedades, [1, 2, 8, 101, 103, 201, 202, 208, 301, 302, 305, 308, 310]);
        $paro = obtenerSumaHorasNovedades($novedades, [303]);
        $susp = obtenerSumaHorasNovedades($novedades, [701, 702]);
        $vac = obtenerSumaHorasNovedades($novedades, [601]);

        $Trab = horasADecimal($value['Trab'] ?? '00:00');

        $result[] = [
            'Legajo' => $value['Lega'],
            'Apellido y Nombre' => $value['ApNo'],
            // 'Fecha' => date('d/m/Y', strtotime($value['Fech'])),
            'Fecha' => $value['Fech'],
            'Día' => $value['FechD'],
            'Entrada' => $value['Tur']['ent'],
            'Salida' => $value['Tur']['sal'],
            'Laboral' => $value['Labo'] == '1' ? 'Sí' : 'No',
            'Feriado' => $value['Feri'] == '1' ? 'Sí' : 'No',
            'Hs. a Trab.' => horasADecimal($value['ATra']),
            'Ingreso' => $primerFichada,
            'EstFic' => $estadoInicial,
            'Egreso' => $ultimaFichada,
            'EstUltFic' => $estadoFinal,
            'Hs. Trab.' => $Trab,
            'Hs. Fer.' => ($value['Feri'] === '0') ? '0' : $Trab,
            '(90) Nor.' => obtenerHoraAutoDecimal($horas, 90),
            'Normal E' => $normalE,
            '(23) Hs. Sab.' => obtenerHoraAutoDecimal($horas, 23),
            '(80) Noct.' => obtenerHoraAutoDecimal($horas, 80),
            '(20) 50%' => obtenerHoraAutoDecimal($horas, 20),
            '(21) Sab. 50%' => obtenerHoraAutoDecimal($horas, 21),
            '(10) 100%' => obtenerHoraAutoDecimal($horas, 10),
            'Enf.' => $enf,
            'Bl.' => $bl,
            'Acc.' => $acc,
            'Art.' => $art,
            'Descomp.' => $descomp,
            'Hs. Inj.' => $hsInj,
            'Paro' => $paro,
            'Susp.' => $susp,
            '(22) Ad Tur. 50%' => obtenerHoraAutoDecimal($horas, 22),
            '(12) Ad Tur. 100%' => obtenerHoraAutoDecimal($horas, 12),
            '(45) Ad Ext.' => obtenerHoraAutoDecimal($horas, 45),
            '(35) 3 Tur. Sem.' => obtenerHoraAutoDecimal($horas, 35),
            '(15) Dia Tra.' => obtenerHoraAutoDecimal($horas, 15),
            'Vac' => $vac,
            'Sector' => $sectores[$value['Estruct']['Sect']] ?? 'N/A',
        ];
    }

    usort($result, function ($a, $b) {
        return $a['Legajo'] <=> $b['Legajo'] ?: strtotime($a['Fecha']) <=> strtotime($b['Fecha']);
    });

    $rs = [
        'data' => $result,
        'hash' => $hashPayload,
        'totalEjecutionNoveHoras' => $totalEjecutionNoveHoras,
        'totalEjecutionScript' => totalEjecution($inicioScript),
    ];

    if ($tipo == 'view') {
        return Flight::json($rs);
    }

    if ($tipo == 'xls') {
        $Datos['Data'] = $result;
        require __DIR__ . '/../fn_spreadsheet.php';
        $colsExcel = array_keys($result[0] ?? []);
        require __DIR__ . '/../../informes/custom/prysmian/xls3.php';
    }
    if ($tipo == 'pdf') {
        require __DIR__ . '/../../informes/custom/prysmian/pdf3.php';
    }

} catch (Exception $th) {
    throw $th;
}