<?php

function procesarCamposLiquidarClienteId(): string
{
    $clienteId = (string) ($_SESSION['ID_CLIENTE'] ?? '');
    $clienteId = trim($clienteId);
    $clienteId = preg_replace('/[^A-Za-z0-9_-]/', '', $clienteId) ?: '';

    if ($clienteId === '') {
        throw new Exception('No se encontro el ID_CLIENTE de la sesion.', 400);
    }

    return $clienteId;
}

function procesarCamposLiquidarNombreArchivo(): string
{
    return procesarCamposLiquidarClienteId() . '_liquidar_custom.json';
}

function procesarCamposLiquidarRutaArchivo(): string
{
    return __DIR__ . '/../json/' . procesarCamposLiquidarNombreArchivo();
}

function procesarCamposLiquidarNormalizarCampo($item): ?array
{
    if (!is_array($item)) {
        return null;
    }

    $posicion = isset($item['posicion']) ? (int) $item['posicion'] : 0;
    $tipo = isset($item['tipo']) ? (string) $item['tipo'] : '';
    $subtipo = isset($item['subtipo']) ? (string) $item['subtipo'] : '';
    $tamano = isset($item['tamano']) ? (int) $item['tamano'] : 0;
    $formato = isset($item['formato']) ? (string) $item['formato'] : '';

    if ($posicion <= 0 || $tipo === '' || $tamano <= 0 || $formato === '') {
        return null;
    }

    return [
        'uid' => isset($item['uid']) ? (int) $item['uid'] : 0,
        'posicion' => $posicion,
        'tipo' => $tipo,
        'tipoLabel' => isset($item['tipoLabel']) ? (string) $item['tipoLabel'] : '',
        'subtipo' => $subtipo,
        'subtipoLabel' => isset($item['subtipoLabel']) ? (string) $item['subtipoLabel'] : '',
        'tamano' => $tamano,
        'formato' => $formato,
        'formatoLabel' => isset($item['formatoLabel']) ? (string) $item['formatoLabel'] : '',
    ];
}

function procesarCamposLiquidarNormalizarCampos($campos): array
{
    if (!is_array($campos)) {
        throw new Exception('El payload de campos no es valido.', 400);
    }

    $normalizados = [];

    foreach ($campos as $item) {
        $campo = procesarCamposLiquidarNormalizarCampo($item);
        if ($campo !== null) {
            $normalizados[] = $campo;
        }
    }

    usort($normalizados, function ($a, $b) {
        return ($a['posicion'] ?? 0) <=> ($b['posicion'] ?? 0);
    });

    return $normalizados;
}

function procesarCamposLiquidarLeerCampos(): array
{
    $rutaArchivo = procesarCamposLiquidarRutaArchivo();

    if (!file_exists($rutaArchivo)) {
        return [];
    }

    $contenido = file_get_contents($rutaArchivo);
    if ($contenido === false || $contenido === '') {
        return [];
    }

    $data = json_decode($contenido, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('El archivo de configuracion de campos no es valido.', 500);
    }

    return procesarCamposLiquidarNormalizarCampos($data);
}

function procesarCamposLiquidarGuardarCampos($campos): array
{
    $normalizados = procesarCamposLiquidarNormalizarCampos($campos);
    $json = json_encode($normalizados, JSON_PRETTY_PRINT);

    if ($json === false) {
        throw new Exception('No se pudo serializar la configuracion de campos.', 500);
    }

    $rutaArchivo = procesarCamposLiquidarRutaArchivo();
    $bytes = file_put_contents($rutaArchivo, $json, LOCK_EX);

    if ($bytes === false) {
        throw new Exception('No se pudo guardar la configuracion de campos.', 500);
    }

    return $normalizados;
}

function procesarCamposLiquidarNormalizarRegistrosFicNovHor($data): array
{
    if (is_array($data) && isset($data['DATA']) && is_array($data['DATA'])) {
        return $data['DATA'];
    }

    if (is_array($data)) {
        return array_values(array_filter($data, 'is_array'));
    }

    return [];
}

function procesarCamposLiquidarFormatearFecha(string $fecha, string $formato): string
{
    $timestamp = strtotime($fecha);
    if ($timestamp === false) {
        return '';
    }

    switch ($formato) {
        case 'MM-DD-YYYY':
            return date('m-d-Y', $timestamp);
        case 'DD-MM-YYYY':
            return date('d-m-Y', $timestamp);
        case 'YYYY-MM-DD':
        default:
            return date('Y-m-d', $timestamp);
    }
}

function procesarCamposLiquidarHoraADecimal(string $hora): string
{
    $hora = trim($hora);
    if ($hora === '') {
        return '0.00';
    }

    if (strpos($hora, ':') !== false) {
        $partes = explode(':', $hora);
        $h = isset($partes[0]) ? (int) $partes[0] : 0;
        $m = isset($partes[1]) ? (int) $partes[1] : 0;
        $decimal = $h + ($m / 60);
        return number_format($decimal, 2, '.', '');
    }

    return number_format((float) $hora, 2, '.', '');
}

function procesarCamposLiquidarPadIzquierda(string $valor, int $tamano): string
{
    if ($tamano <= 0) {
        return $valor;
    }

    if (strlen($valor) >= $tamano) {
        return $valor;
    }

    return str_pad($valor, $tamano, '0', STR_PAD_LEFT);
}

function procesarCamposLiquidarBuscarHoraPorSubtipo(array $registro, string $subtipo): string
{
    $horas = (isset($registro['Horas']) && is_array($registro['Horas'])) ? $registro['Horas'] : [];

    foreach ($horas as $hora) {
        if ((string) ($hora['Hora'] ?? '') === (string) $subtipo) {
            return (string) ($hora['Auto'] ?? '');
        }
    }

    return '';
}

function procesarCamposLiquidarBuscarNovedadPorSubtipo(array $registro, string $subtipo): string
{
    $novedades = (isset($registro['Nove']) && is_array($registro['Nove'])) ? $registro['Nove'] : [];

    foreach ($novedades as $novedad) {
        if ((string) ($novedad['Codi'] ?? '') === (string) $subtipo) {
            return (string) ($novedad['Horas'] ?? '');
        }
    }

    return '';
}

function procesarCamposLiquidarObtenerValorCrudoCampo(array $registro, array $campo): string
{
    $tipo = (string) ($campo['tipo'] ?? '');
    $subtipo = (string) ($campo['subtipo'] ?? '');

    switch ($tipo) {
        case 'legajo':
            return (string) ($registro['Lega'] ?? '');
        case 'fecha':
            return (string) ($registro['Fech'] ?? '');
        case 'horas':
            return procesarCamposLiquidarBuscarHoraPorSubtipo($registro, $subtipo);
        case 'novedades':
            return procesarCamposLiquidarBuscarNovedadPorSubtipo($registro, $subtipo);
        default:
            return '';
    }
}

function procesarCamposLiquidarFormatearValorCampo(string $valorCrudo, array $campo): string
{
    $tipo = (string) ($campo['tipo'] ?? '');
    $formato = (string) ($campo['formato'] ?? '');
    $tamano = (int) ($campo['tamano'] ?? 0);

    if ($valorCrudo === '') {
        return procesarCamposLiquidarPadIzquierda('', $tamano);
    }

    switch ($tipo) {
        case 'legajo':
            $soloDigitos = preg_replace('/\D+/', '', $valorCrudo) ?: '';
            return procesarCamposLiquidarPadIzquierda($soloDigitos, $tamano);
        case 'fecha':
            return procesarCamposLiquidarPadIzquierda(procesarCamposLiquidarFormatearFecha($valorCrudo, $formato), $tamano);
        case 'horas':
        case 'novedades':
            return procesarCamposLiquidarPadIzquierda(procesarCamposLiquidarHoraADecimal($valorCrudo), $tamano);
        default:
            if ($formato === 'decimal') {
                return procesarCamposLiquidarPadIzquierda(number_format((float) $valorCrudo, 2, '.', ''), $tamano);
            }
            return procesarCamposLiquidarPadIzquierda($valorCrudo, $tamano);
    }
}

function procesarCamposLiquidarConstruirLineaRegistro(array $registro, array $campos): string
{
    $valores = [];

    foreach ($campos as $campo) {
        $valorCrudo = procesarCamposLiquidarObtenerValorCrudoCampo($registro, $campo);
        $valores[] = procesarCamposLiquidarFormatearValorCampo($valorCrudo, $campo);
    }

    return implode(',', $valores);
}

function procesarCamposLiquidarGenerarContenidoExport(array $campos, array $registros): string
{
    $lineas = [];

    foreach ($registros as $registro) {
        $lineas[] = procesarCamposLiquidarConstruirLineaRegistro($registro, $campos);
    }

    return implode("\r\n", $lineas);
}

function procesarCamposLiquidarExportarTxt(array $payload): array
{
    $fechaInicio = trim((string) ($payload['FechIni'] ?? ''));
    $fechaFin = trim((string) ($payload['FechFin'] ?? ''));

    if ($fechaInicio === '' || $fechaFin === '') {
        throw new Exception('Debe indicar rango de fechas para exportar.', 400);
    }

    $fechaInicioDt = DateTime::createFromFormat('Y-m-d', $fechaInicio);
    $fechaFinDt = DateTime::createFromFormat('Y-m-d', $fechaFin);
    if (!$fechaInicioDt || !$fechaFinDt) {
        throw new Exception('Formato de fecha invalido. Debe ser YYYY-MM-DD.', 400);
    }

    if ($fechaFinDt < $fechaInicioDt) {
        throw new Exception('La fecha fin no puede ser menor que la fecha inicio.', 400);
    }

    $diasRango = (int) $fechaInicioDt->diff($fechaFinDt)->days + 1;
    if ($diasRango > 31) {
        throw new Exception('El rango de fechas no puede superar 31 dias.', 400);
    }

    $campos = procesarCamposLiquidarLeerCampos();
    if (!$campos) {
        throw new Exception('No hay campos configurados para exportar.', 400);
    }

    $payloadFicNovHor = [
        'FechIni' => $fechaInicio,
        'FechFin' => $fechaFin,
        'getNov' => 1,
        'getHor' => 1,
        'start' => 0,
        'length' => 100000,
    ];

    $ficNovHor = fic_nove_horas($payloadFicNovHor);
    $registros = procesarCamposLiquidarNormalizarRegistrosFicNovHor($ficNovHor);

    if (!$registros) {
        throw new Exception('No hay registros para exportar en el rango seleccionado.', 400);
    }

    $contenido = procesarCamposLiquidarGenerarContenidoExport($campos, $registros);

    $timestamp = date('Ymd_His');
    $nombreArchivo = 'liquidar_' . $timestamp . '.txt';
    $rutaArchivo = __DIR__ . '/../archivos/' . $nombreArchivo;
    $bytes = file_put_contents($rutaArchivo, $contenido, LOCK_EX);

    if ($bytes === false) {
        throw new Exception('No se pudo generar el archivo de exportacion.', 500);
    }

    return [
        'archivo' => 'archivos/' . $nombreArchivo,
        'registros' => count($registros),
    ];
}
