<?php

function procesarCamposLiquidarClienteId(): string
{
    $clienteId = (string) ($_SESSION['ID_CLIENTE'] ?? '');
    $clienteId = trim($clienteId);
    $clienteId = preg_replace('/[^A-Za-z0-9_-]/', '', $clienteId) ?: '';

    if ($clienteId === '') {
        throw new Exception('No se encontró el ID_CLIENTE de la sesion.', 400);
    }

    return $clienteId;
}

function procesarCamposLiquidarNombreArchivo(): string
{
    return procesarCamposLiquidarClienteId() . '_liquidar_custom.json';
}

function procesarCamposLiquidarRutaDirectorioConfig(): string
{
    return __DIR__ . '/../json/config/';
}

function procesarCamposLiquidarRutaArchivoLegacy(): string
{
    return __DIR__ . '/../json/' . procesarCamposLiquidarNombreArchivo();
}

function procesarCamposLiquidarRutaArchivo(): string
{
    return procesarCamposLiquidarRutaDirectorioConfig() . procesarCamposLiquidarNombreArchivo();
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

    $tamanoValido = ($formato === 'texto' || $formato === 'horas')
        ? ($tamano === 0)
        : (procesarCamposLiquidarEsFormatoFecha($formato) ? ($tamano >= 0) : ($tamano > 0));

    if ($posicion <= 0 || $tipo === '' || !$tamanoValido || $formato === '') {
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

function procesarCamposLiquidarNormalizarSeparador($separador): string
{
    if ($separador === null) {
        return ',';
    }

    $separador = (string) $separador;

    if ($separador === '') {
        return ',';
    }

    return substr($separador, 0, 1);
}

function procesarCamposLiquidarNormalizarEncabezados($encabezados): int
{
    return ((int) $encabezados) === 1 ? 1 : 0;
}

function procesarCamposLiquidarNormalizarConfiguracion($data): array
{
    $separador = ',';
    $encabezados = 0;
    $campos = [];

    if (is_array($data) && array_key_exists('campos', $data)) {
        $campos = $data['campos'];
        $separador = procesarCamposLiquidarNormalizarSeparador($data['separador'] ?? ',');
        $encabezados = procesarCamposLiquidarNormalizarEncabezados($data['encabezados'] ?? 0);
    } else {
        $campos = $data;
        if (is_array($data) && array_key_exists('separador', $data)) {
            $separador = procesarCamposLiquidarNormalizarSeparador($data['separador']);
        }
        if (is_array($data) && array_key_exists('encabezados', $data)) {
            $encabezados = procesarCamposLiquidarNormalizarEncabezados($data['encabezados']);
        }
    }

    return [
        'separador' => $separador,
        'encabezados' => $encabezados,
        'campos' => procesarCamposLiquidarNormalizarCampos($campos),
    ];
}

function procesarCamposLiquidarLeerConfiguracion(): array
{
    $rutaArchivo = procesarCamposLiquidarRutaArchivo();
    $rutaArchivoLegacy = procesarCamposLiquidarRutaArchivoLegacy();

    if (!file_exists($rutaArchivo) && file_exists($rutaArchivoLegacy)) {
        $rutaArchivo = $rutaArchivoLegacy;
    }

    if (!file_exists($rutaArchivo)) {
        return ['separador' => ',', 'encabezados' => 0, 'campos' => []];
    }

    $contenido = file_get_contents($rutaArchivo);
    if ($contenido === false || $contenido === '') {
        return ['separador' => ',', 'encabezados' => 0, 'campos' => []];
    }

    $data = json_decode($contenido, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('El archivo de configuración de campos no es valido.', 500);
    }

    $config = procesarCamposLiquidarNormalizarConfiguracion($data);

    // Si se leyó desde la ruta legacy, intenta migrar a la nueva carpeta de config.
    if ($rutaArchivo === $rutaArchivoLegacy) {
        try {
            procesarCamposLiquidarGuardarConfiguracion($config['campos'] ?? [], $config['separador'] ?? ',');
        } catch (Throwable $th) {
            // Mantener lectura funcional aunque falle migración automática.
        }
    }

    return $config;
}

function procesarCamposLiquidarLeerCampos(): array
{
    $config = procesarCamposLiquidarLeerConfiguracion();
    return $config['campos'] ?? [];
}

function procesarCamposLiquidarGuardarConfiguracion($campos, $separador = ',', $encabezados = 0): array
{
    $normalizados = procesarCamposLiquidarNormalizarCampos($campos);
    $config = [
        'separador' => procesarCamposLiquidarNormalizarSeparador($separador),
        'encabezados' => procesarCamposLiquidarNormalizarEncabezados($encabezados),
        'campos' => $normalizados,
    ];
    $json = json_encode($config, JSON_PRETTY_PRINT);

    if ($json === false) {
        throw new Exception('No se pudo serializar la configuración de campos.', 500);
    }

    $rutaArchivo = procesarCamposLiquidarRutaArchivo();
    $directorio = procesarCamposLiquidarRutaDirectorioConfig();

    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0777, true) && !is_dir($directorio)) {
            throw new Exception('No se pudo crear la carpeta de configuración.', 500);
        }
    }

    $bytes = file_put_contents($rutaArchivo, $json, LOCK_EX);

    if ($bytes === false) {
        throw new Exception('No se pudo guardar la configuración de campos.', 500);
    }

    return $config;
}

function procesarCamposLiquidarGuardarCampos($campos, $separador = ',', $encabezados = 0): array
{
    $config = procesarCamposLiquidarGuardarConfiguracion($campos, $separador, $encabezados);
    return $config['campos'] ?? [];
}

function procesarCamposLiquidarConstruirEncabezados(array $campos, string $separador): string
{
    $headers = [];

    foreach ($campos as $campo) {
        $tipo = (string) ($campo['tipo'] ?? '');

        if ($tipo === 'horas' || $tipo === 'novedades') {
            $headers[] = (string) ($campo['subtipoLabel'] ?? '');
            continue;
        }

        $headers[] = (string) ($campo['tipoLabel'] ?? '');
    }

    return implode($separador, $headers);
}

function procesarCamposLiquidarLeerSeparador(): string
{
    $config = procesarCamposLiquidarLeerConfiguracion();
    return procesarCamposLiquidarNormalizarSeparador($config['separador'] ?? ',');
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

function procesarCamposLiquidarEsFormatoFecha(string $formato): bool
{
    static $formatosFecha = [
        'YYYY-MM-DD',
        'YYYYMMDD',
        'YYYY/MM/DD',
        'MM-DD-YYYY',
        'MMDDYYYY',
        'MM/DD/YYYY',
        'DD-MM-YYYY',
        'DDMMYYYY',
        'DD/MM/YYYY',
    ];

    return in_array($formato, $formatosFecha, true);
}

function procesarCamposLiquidarFormatearFecha(string $fecha, string $formato): string
{
    $timestamp = strtotime($fecha);
    if ($timestamp === false) {
        return '';
    }

    switch ($formato) {
        case 'YYYYMMDD':
            return date('Ymd', $timestamp);
        case 'YYYY/MM/DD':
            return date('Y/m/d', $timestamp);
        case 'MM-DD-YYYY':
            return date('m-d-Y', $timestamp);
        case 'MMDDYYYY':
            return date('mdY', $timestamp);
        case 'MM/DD/YYYY':
            return date('m/d/Y', $timestamp);
        case 'DD-MM-YYYY':
            return date('d-m-Y', $timestamp);
        case 'DDMMYYYY':
            return date('dmY', $timestamp);
        case 'DD/MM/YYYY':
            return date('d/m/Y', $timestamp);
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

function procesarCamposLiquidarPadDerechaEspacios(string $valor, int $tamano): string
{
    if ($tamano <= 0) {
        return $valor;
    }

    if (strlen($valor) >= $tamano) {
        return substr($valor, 0, $tamano);
    }

    return str_pad($valor, $tamano, ' ', STR_PAD_RIGHT);
}

function procesarCamposLiquidarObtenerCodigoEstructura(array $registro, string $clave): string
{
    $estruct = (isset($registro['Estruct']) && is_array($registro['Estruct'])) ? $registro['Estruct'] : [];
    return (string) ($estruct[$clave] ?? '');
}

function procesarCamposLiquidarSanitizarTextoSeparador(string $valor, string $separador): string
{
    if ($separador === '') {
        return $valor;
    }

    return str_replace($separador, ' ', $valor);
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

function procesarCamposLiquidarObtenerFichadas(array $registro): array
{
    $fichadas = (isset($registro['Fich']) && is_array($registro['Fich'])) ? $registro['Fich'] : [];
    return array_values(array_filter($fichadas, 'is_array'));
}

function procesarCamposLiquidarObtenerPrimerFichada(array $registro): string
{
    $fichadas = procesarCamposLiquidarObtenerFichadas($registro);
    if (empty($fichadas)) {
        return '';
    }

    foreach ($fichadas as $fichada) {
        $hora = trim((string) ($fichada['Hora'] ?? ''));
        if ($hora !== '') {
            return $hora;
        }
    }

    return '';
}

function procesarCamposLiquidarObtenerUltimaFichada(array $registro): string
{
    $fichadas = procesarCamposLiquidarObtenerFichadas($registro);
    if (count($fichadas) <= 1) {
        return '';
    }

    $ultima = $fichadas[count($fichadas) - 1];
    return (string) ($ultima['Hora'] ?? '');
}

function procesarCamposLiquidarObtenerTodasLasFichadas(array $registro): string
{
    $fichadas = procesarCamposLiquidarObtenerFichadas($registro);
    if (empty($fichadas)) {
        return '';
    }

    $horas = [];
    foreach ($fichadas as $fichada) {
        $hora = trim((string) ($fichada['Hora'] ?? ''));
        if ($hora !== '') {
            $horas[] = $hora;
        }
    }

    return implode(' ', $horas);
}

function procesarCamposLiquidarObtenerValorCrudoCampo(array $registro, array $campo): string
{
    $tipo = (string) ($campo['tipo'] ?? '');
    $subtipo = (string) ($campo['subtipo'] ?? '');

    switch ($tipo) {
        case 'legajo':
            return (string) ($registro['Lega'] ?? '');
        case 'apno':
            return (string) ($registro['ApNo'] ?? '');
        case 'dni_legajo':
            return (string) ($registro['Docu'] ?? '');
        case 'cuil_legajo':
            return (string) ($registro['Cuil'] ?? '');
        case 'cod_empresa':
            return procesarCamposLiquidarObtenerCodigoEstructura($registro, 'Empr');
        case 'cod_planta':
            return procesarCamposLiquidarObtenerCodigoEstructura($registro, 'Plan');
        case 'cod_convenio':
            return procesarCamposLiquidarObtenerCodigoEstructura($registro, 'Conv');
        case 'cod_sector':
            return procesarCamposLiquidarObtenerCodigoEstructura($registro, 'Sect');
        case 'cod_seccion':
            return procesarCamposLiquidarObtenerCodigoEstructura($registro, 'Sec2');
        case 'cod_grupo':
            return procesarCamposLiquidarObtenerCodigoEstructura($registro, 'Grup');
        case 'cod_sucursal':
            return procesarCamposLiquidarObtenerCodigoEstructura($registro, 'Sucu');
        case 'fecha':
            return (string) ($registro['Fech'] ?? '');
        case 'horas':
            return procesarCamposLiquidarBuscarHoraPorSubtipo($registro, $subtipo);
        case 'novedades':
            return procesarCamposLiquidarBuscarNovedadPorSubtipo($registro, $subtipo);
        case 'atra':
            return (string) ($registro['ATra'] ?? '');
        case 'trab':
            return (string) ($registro['Trab'] ?? '');
        case 'primer_fichada':
            return procesarCamposLiquidarObtenerPrimerFichada($registro);
        case 'ultima_fichada':
            return procesarCamposLiquidarObtenerUltimaFichada($registro);
        case 'todas_fichadas':
            return procesarCamposLiquidarObtenerTodasLasFichadas($registro);
        case 'turstr':
            return (string) ($registro['TurStr'] ?? '');
        case 'labo':
            return (string) ($registro['Labo'] ?? '');
        case 'feri':
            return (string) ($registro['Feri'] ?? '');
        default:
            return '';
    }
}

function procesarCamposLiquidarFormatearValorCampo(string $valorCrudo, array $campo, string $separador): string
{
    $tipo = (string) ($campo['tipo'] ?? '');
    $formato = (string) ($campo['formato'] ?? '');
    $tamano = (int) ($campo['tamano'] ?? 0);

    if ($formato === 'texto') {
        return procesarCamposLiquidarSanitizarTextoSeparador($valorCrudo, $separador);
    }

    if ($formato === 'horas') {
        $valorCrudo = trim($valorCrudo);

        if (in_array($tipo, ['primer_fichada', 'ultima_fichada', 'todas_fichadas'], true)) {
            return $valorCrudo;
        }

        return $valorCrudo === '' ? '00:00' : $valorCrudo;
    }

    if ($valorCrudo === '') {
        if ($tipo === 'fecha') {
            return '';
        }

        if ($tipo === 'cuil_legajo') {
            return procesarCamposLiquidarPadDerechaEspacios('', $tamano);
        }
        return procesarCamposLiquidarPadIzquierda('', $tamano);
    }

    switch ($tipo) {
        case 'legajo':
        case 'dni_legajo':
        case 'cod_empresa':
        case 'cod_planta':
        case 'cod_convenio':
        case 'cod_sector':
        case 'cod_seccion':
        case 'cod_grupo':
        case 'cod_sucursal':
        case 'labo':
        case 'feri':
            $soloDigitos = preg_replace('/\D+/', '', $valorCrudo) ?: '';
            return procesarCamposLiquidarPadIzquierda($soloDigitos, $tamano);
        case 'apno':
            $sanitizado = procesarCamposLiquidarSanitizarTextoSeparador($valorCrudo, $separador);
            return procesarCamposLiquidarPadDerechaEspacios($sanitizado, $tamano);
        case 'cuil_legajo':
            return procesarCamposLiquidarPadDerechaEspacios($valorCrudo, $tamano);
        case 'fecha':
            return procesarCamposLiquidarFormatearFecha($valorCrudo, $formato);
        case 'horas':
        case 'novedades':
        case 'atra':
        case 'trab':
            return procesarCamposLiquidarPadIzquierda(procesarCamposLiquidarHoraADecimal($valorCrudo), $tamano);
        case 'turstr':
            return procesarCamposLiquidarPadDerechaEspacios($valorCrudo, $tamano);
        default:
            if ($formato === 'decimal') {
                return procesarCamposLiquidarPadIzquierda(number_format((float) $valorCrudo, 2, '.', ''), $tamano);
            }
            return procesarCamposLiquidarPadIzquierda($valorCrudo, $tamano);
    }
}

function procesarCamposLiquidarConstruirLineaRegistro(array $registro, array $campos, string $separador): string
{
    $valores = [];

    foreach ($campos as $campo) {
        $valorCrudo = procesarCamposLiquidarObtenerValorCrudoCampo($registro, $campo);
        $valores[] = procesarCamposLiquidarFormatearValorCampo($valorCrudo, $campo, $separador);
    }

    return implode($separador, $valores);
}

function procesarCamposLiquidarGenerarContenidoExport(array $campos, array $registros, string $separador): string
{
    $lineas = [];

    foreach ($registros as $registro) {
        $lineas[] = procesarCamposLiquidarConstruirLineaRegistro($registro, $campos, $separador);
    }

    return implode("\r\n", $lineas);
}

function procesarCamposLiquidarExportarTxt(array $payload): array
{
    $fechaInicio = trim((string) ($payload['FechIni'] ?? ''));
    $fechaFin = trim((string) ($payload['FechFin'] ?? ''));
    $config = procesarCamposLiquidarLeerConfiguracion();
    $separador = procesarCamposLiquidarNormalizarSeparador($config['separador'] ?? ',');
    $encabezados = procesarCamposLiquidarNormalizarEncabezados($config['encabezados'] ?? 0);

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
        throw new Exception('El rango de fechas no puede superar 31 días.', 400);
    }

    $campos = $config['campos'] ?? [];
    if (!$campos) {
        throw new Exception('No hay campos configurados para exportar.', 400);
    }
    
    $hayCamposFichadas = false;
    $hayCamposHoras = false;
    $hayCamposNovedades = false;
    $hayCamposEstructura = false;

    $mapCampoFichadas = ['primer_fichada', 'ultima_fichada', 'todas_fichadas'];
    $mapCampoHoras = ['horas', 'atra', 'trab'];
    $mapCampoNovedades = ['novedades'];
    $mapCampoEstructura = ['cod_empresa', 'cod_planta', 'cod_convenio', 'cod_sector', 'cod_seccion', 'cod_grupo', 'cod_sucursal'];

    foreach ($campos as $campo) {

        if (in_array($campo['tipo'] ?? '', $mapCampoHoras, true)) {
            $hayCamposHoras = true;
        }
        if (in_array($campo['tipo'] ?? '', $mapCampoFichadas, true)) {
            $hayCamposFichadas = true;
        }
        if (in_array($campo['tipo'] ?? '', $mapCampoNovedades, true)) {
            $hayCamposNovedades = true;
        }
        if (in_array($campo['tipo'] ?? '', $mapCampoEstructura, true)) {
            $hayCamposEstructura = true;
        }
    }

    $payloadFicNovHor = [
        'FechIni' => $fechaInicio,
        'FechFin' => $fechaFin,
        'getNov' => $hayCamposNovedades ? 1 : 0,
        'getHor' => $hayCamposHoras ? 1 : 0,
        'getFic' => $hayCamposFichadas ? 1 : 0,
        'getReg' => $hayCamposFichadas ? 1 : 0,
        'getEstruct' => $hayCamposEstructura ? 1 : 0,
        'start' => 0,
        'length' => 1000000,
    ];

    $ficNovHor = fic_nove_horas($payloadFicNovHor);
    $registros = procesarCamposLiquidarNormalizarRegistrosFicNovHor($ficNovHor);

    if (!$registros) {
        throw new Exception('No hay registros para exportar en el rango seleccionado.', 400);
    }

    $contenido = procesarCamposLiquidarGenerarContenidoExport($campos, $registros, $separador);

    if ($encabezados === 1) {
        $lineaEncabezados = procesarCamposLiquidarConstruirEncabezados($campos, $separador);
        $contenido = $lineaEncabezados . "\r\n" . $contenido;
    }

    $timestamp = date('Ymd_His');
    $nombreArchivo = 'liquidar_' . $timestamp . '.txt';
    $rutaArchivo = __DIR__ . '/../archivos/' . $nombreArchivo;
    $bytes = file_put_contents($rutaArchivo, $contenido, LOCK_EX);

    if ($bytes === false) {
        throw new Exception('No se pudo generar el archivo de exportación.', 500);
    }

    return [
        'archivo' => 'archivos/' . $nombreArchivo,
        'registros' => count($registros),
    ];
}
