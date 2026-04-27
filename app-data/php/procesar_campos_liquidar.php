<?php
function ClienteId(): string
{
    $clienteId = (string) ($_SESSION['ID_CLIENTE'] ?? '');
    $clienteId = trim($clienteId);
    $clienteId = preg_replace('/[^A-Za-z0-9_-]/', '', $clienteId) ?: '';

    if ($clienteId === '') {
        throw new Exception('No se encontró el ID_CLIENTE de la sesión.', 400);
    }

    return $clienteId;
}

function NombreArchivo(): string
{
    return ClienteId() . '_liquidar_custom.json';
}

function RutaDirectorioConfig(): string
{
    return __DIR__ . '/../json/config/';
}

function RutaArchivoLegacy(): string
{
    return __DIR__ . '/../json/' . NombreArchivo();
}

function RutaArchivo(): string
{
    return RutaDirectorioConfig() . NombreArchivo();
}

function NormalizarCampo($item): ?array
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
        : (EsFormatoFecha($formato) ? ($tamano >= 0) : ($tamano > 0));

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

function NormalizarCampos($campos): array
{
    if (!is_array($campos)) {
        throw new Exception('El payload de campos no es valido.', 400);
    }

    $normalizados = [];

    foreach ($campos as $item) {
        $campo = NormalizarCampo($item);
        if ($campo !== null) {
            $normalizados[] = $campo;
        }
    }

    usort($normalizados, function ($a, $b) {
        return ($a['posicion'] ?? 0) <=> ($b['posicion'] ?? 0);
    });

    return $normalizados;
}

function NormalizarSeparador($separador): string
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

function NormalizarEncabezados($encabezados): int
{
    return ((int) $encabezados) === 1 ? 1 : 0;
}

function NormalizarPlantillaSlug($slug): string
{
    $slug = trim((string) $slug);

    if (function_exists('iconv')) {
        $slugTransliterado = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        if ($slugTransliterado !== false) {
            $slug = $slugTransliterado;
        }
    }

    $slug = strtolower($slug);
    $slug = preg_replace('/\s+/', '_', $slug) ?: '';
    $slug = preg_replace('/[^a-z0-9_]/', '', $slug) ?: '';
    $slug = preg_replace('/_+/', '_', $slug) ?: '';
    $slug = trim($slug, '_');

    if ($slug === '') {
        return '';
    }

    return substr($slug, 0, 50);
}

function NormalizarNombrePlantilla($nombre, $fallbackSlug = ''): string
{
    $nombre = trim((string) $nombre);

    if ($nombre !== '' && preg_match('/^(?=.*[\p{L}\p{N}])[\p{L}\p{N} ]{1,50}$/u', $nombre)) {
        return $nombre;
    }

    $fallbackSlug = NormalizarPlantillaSlug($fallbackSlug);
    if ($fallbackSlug === '') {
        return '';
    }

    return $fallbackSlug;
}

function PlantillaSlugDesdeNombre($nombre): string
{
    $nombre = trim((string) $nombre);
    if ($nombre === '') {
        throw new Exception('Debe indicar un nombre de plantilla.', 400);
    }

    if (!preg_match('/^(?=.*[\p{L}\p{N}])[\p{L}\p{N} ]{1,50}$/u', $nombre)) {
        throw new Exception('El nombre de la plantilla solo puede contener letras, números, acentos y espacios (máximo 50).', 400);
    }

    return NormalizarPlantillaSlug($nombre);
}

function ExistePlantillaPorSlug($plantillaSlug): bool
{
    $slug = NormalizarPlantillaSlug($plantillaSlug);
    if ($slug === '') {
        return false;
    }

    $queryParams = [
        'cliente' => $_SESSION['ID_CLIENTE'] ?? '',
        'descripcion' => '',
        'modulo' => 48,
        'valores' => ''
    ];

    $rows = get_params($queryParams) ?? [];

    foreach ($rows as $row) {
        $actual = NormalizarPlantillaSlug($row['descripcion'] ?? '');
        if ($actual === $slug) {
            return true;
        }
    }

    return false;
}

function ListarPlantillas(): array
{
    $queryParams = [
        'cliente' => $_SESSION['ID_CLIENTE'] ?? '',
        'descripcion' => '',
        'modulo' => 48,
        'valores' => ''
    ];

    $rows = get_params($queryParams) ?? [];
    $plantillas = [];

    foreach ($rows as $row) {
        $slug = NormalizarPlantillaSlug($row['descripcion'] ?? '');
        $valores = (string) ($row['valores'] ?? '');

        if ($slug === '' || $valores === '') {
            continue;
        }

        $json = json_decode($valores, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json) || !array_key_exists('campos', $json)) {
            continue;
        }

        $nombreReal = NormalizarNombrePlantilla($json['nombre'] ?? ($json['nombrePlantilla'] ?? ''), $slug);

        $plantillas[$slug] = [
            'slug' => $slug,
            'nombre' => $nombreReal !== '' ? $nombreReal : $slug,
            'descripcion' => $slug,
        ];
    }

    // if (!isset($plantillas['liquidar_custom'])) {
    //     $plantillas['liquidar_custom'] = [
    //         'slug' => 'liquidar_custom',
    //         'nombre' => 'liquidar_custom',
    //         'descripcion' => 'liquidar_custom',
    //     ];
    // }

    ksort($plantillas);
    return array_values($plantillas);
}

function NormalizarConfiguracion($data): array
{
    $separador = ',';
    $encabezados = 0;
    $nombre = '';
    $campos = [];

    if (is_array($data) && array_key_exists('campos', $data)) {
        $campos = $data['campos'];
        $separador = NormalizarSeparador($data['separador'] ?? ',');
        $encabezados = NormalizarEncabezados($data['encabezados'] ?? 0);
        $nombre = (string) ($data['nombre'] ?? ($data['nombrePlantilla'] ?? ''));
    } else {
        $campos = $data;
        if (is_array($data) && array_key_exists('separador', $data)) {
            $separador = NormalizarSeparador($data['separador']);
        }
        if (is_array($data) && array_key_exists('encabezados', $data)) {
            $encabezados = NormalizarEncabezados($data['encabezados']);
        }
        if (is_array($data) && array_key_exists('nombre', $data)) {
            $nombre = (string) $data['nombre'];
        } elseif (is_array($data) && array_key_exists('nombrePlantilla', $data)) {
            $nombre = (string) $data['nombrePlantilla'];
        }
    }

    return [
        'separador' => $separador,
        'encabezados' => $encabezados,
        'nombre' => $nombre,
        'campos' => NormalizarCampos($campos),
    ];
}

function LeerConfiguracion($plantillaSlug = ''): array
{
    // $rutaArchivo = RutaArchivo();
    // $rutaArchivoLegacy = RutaArchivoLegacy();

    // if (!file_exists($rutaArchivo) && file_exists($rutaArchivoLegacy)) {
    //     $rutaArchivo = $rutaArchivoLegacy;
    // }

    // if (!file_exists($rutaArchivo)) {
    //     return ['separador' => ',', 'encabezados' => 0, 'campos' => []];
    // }

    // $contenido = file_get_contents($rutaArchivo);

    $plantillaSlug = NormalizarPlantillaSlug($plantillaSlug);

    $queryParams = [
        'cliente' => $_SESSION['ID_CLIENTE'] ?? '',
        'descripcion' => $plantillaSlug,
        'modulo' => 48,
        'valores' => ''
    ];

    $get_params = get_params($queryParams) ?? [];

    $contenido =  $get_params[0]['valores'] ?? [];

    if ($contenido === false || $contenido === '') {
        return ['separador' => ',', 'encabezados' => 0, 'nombre' => $plantillaSlug, 'campos' => []];
    }

    $data = json_decode($contenido, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('El archivo de configuración de campos no es valido.', 500);
    }

    $config = NormalizarConfiguracion($data);

    // // Si se leyó desde la ruta legacy, intenta migrar a la nueva carpeta de config.
    // if ($rutaArchivo === $rutaArchivoLegacy) {
    //     try {
    //         GuardarConfiguracion($config['campos'] ?? [], $config['separador'] ?? ',');
    //     } catch (Throwable $th) {
    //         // Mantener lectura funcional aunque falle migración automática.
    //     }
    // }

    return $config;
}

function LeerCampos($plantillaSlug = ''): array
{
    $config = LeerConfiguracion($plantillaSlug);
    return $config['campos'] ?? [];
}

function GuardarConfiguracion($campos, $separador = ',', $encabezados = 0, $plantillaSlug = ''): array
{
    $plantillaSlug = NormalizarPlantillaSlug($plantillaSlug);

    $nombrePlantilla = '';
    try {
        $configExistente = LeerConfiguracion($plantillaSlug);
        $nombrePlantilla = NormalizarNombrePlantilla($configExistente['nombre'] ?? '', $plantillaSlug);
    } catch (Throwable $th) {
        $nombrePlantilla = NormalizarNombrePlantilla('', $plantillaSlug);
    }

    $normalizados = NormalizarCampos($campos);
    $config = [
        'separador' => NormalizarSeparador($separador),
        'encabezados' => NormalizarEncabezados($encabezados),
        'nombre' => $nombrePlantilla,
        'campos' => $normalizados,
    ];
    $json = json_encode($config);

    if ($json === false) {
        throw new Exception('No se pudo serializar la configuración de campos.', 500);
    }

    $rutaArchivo = RutaArchivo();
    $directorio = RutaDirectorioConfig();

    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0777, true) && !is_dir($directorio)) {
            throw new Exception('No se pudo crear la carpeta de configuración.', 500);
        }
    }

    $bytes = file_put_contents($rutaArchivo, $json, LOCK_EX);

    if ($bytes === false) {
        throw new Exception('No se pudo guardar la configuración de campos.', 500);
    }

    $bodyAdd[] = [
        'descripcion' => $plantillaSlug,
        'valores' => $json,
        'modulo' => 48,
        'cliente' => $_SESSION['ID_CLIENTE'] ?? '',
    ];

    add_params($bodyAdd);

    return $config;
}

function GuardarCampos($campos, $separador = ',', $encabezados = 0, $plantillaSlug = ''): array
{
    $config = GuardarConfiguracion($campos, $separador, $encabezados, $plantillaSlug);
    return $config['campos'] ?? [];
}

function GuardarPlantilla($nombrePlantilla, $campos, $separador = ',', $encabezados = 0, $plantillaSlug = ''): array
{
    $plantillaSlug = NormalizarPlantillaSlug($plantillaSlug);
    $nombrePlantilla = NormalizarNombrePlantilla($nombrePlantilla, $plantillaSlug);

    $normalizados = NormalizarCampos($campos);
    $config = [
        'separador' => NormalizarSeparador($separador),
        'encabezados' => NormalizarEncabezados($encabezados),
        'nombre' => $nombrePlantilla,
        'campos' => $normalizados,
    ];
    $json = json_encode($config);

    if ($json === false) {
        throw new Exception('No se pudo serializar la configuración de campos.', 500);
    }

    $rutaArchivo = RutaArchivo();
    $directorio = RutaDirectorioConfig();

    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0777, true) && !is_dir($directorio)) {
            throw new Exception('No se pudo crear la carpeta de configuración.', 500);
        }
    }

    $bytes = file_put_contents($rutaArchivo, $json, LOCK_EX);

    if ($bytes === false) {
        throw new Exception('No se pudo guardar la configuración de campos.', 500);
    }

    $bodyAdd[] = [
        'descripcion' => $plantillaSlug,
        'valores' => $json,
        'modulo' => 48,
        'cliente' => $_SESSION['ID_CLIENTE'] ?? '',
    ];

    add_params($bodyAdd);

    return $config;
}

function ConstruirEncabezados(array $campos, string $separador): string
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

function LeerSeparador($plantillaSlug = ''): string
{
    $config = LeerConfiguracion($plantillaSlug);
    return NormalizarSeparador($config['separador'] ?? ',');
}

function NormalizarRegistrosFicNovHor($data): array
{
    if (is_array($data) && isset($data['DATA']) && is_array($data['DATA'])) {
        return $data['DATA'];
    }

    if (is_array($data)) {
        return array_values(array_filter($data, 'is_array'));
    }

    return [];
}

function EsFormatoFecha(string $formato): bool
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

function FormatearFecha(string $fecha, string $formato): string
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

function HoraADecimal(string $hora): string
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

function PadIzquierda(string $valor, int $tamano): string
{
    if ($tamano <= 0) {
        return $valor;
    }

    if (strlen($valor) >= $tamano) {
        return $valor;
    }

    return str_pad($valor, $tamano, '0', STR_PAD_LEFT);
}

function PadDerechaEspacios(string $valor, int $tamano): string
{
    if ($tamano <= 0) {
        return $valor;
    }

    if (strlen($valor) >= $tamano) {
        return substr($valor, 0, $tamano);
    }

    return str_pad($valor, $tamano, ' ', STR_PAD_RIGHT);
}

function ObtenerCodigoEstructura(array $registro, string $clave): string
{
    $estruct = (isset($registro['Estruct']) && is_array($registro['Estruct'])) ? $registro['Estruct'] : [];
    return (string) ($estruct[$clave] ?? '');
}

function SanitizarTextoSeparador(string $valor, string $separador): string
{
    if ($separador === '') {
        return $valor;
    }

    return str_replace($separador, ' ', $valor);
}

function BuscarHoraPorSubtipo(array $registro, string $subtipo): string
{
    $horas = (isset($registro['Horas']) && is_array($registro['Horas'])) ? $registro['Horas'] : [];

    foreach ($horas as $hora) {
        if ((string) ($hora['Hora'] ?? '') === (string) $subtipo) {
            return (string) ($hora['Auto'] ?? '');
        }
    }

    return '';
}

function BuscarNovedadPorSubtipo(array $registro, string $subtipo): string
{
    $novedades = (isset($registro['Nove']) && is_array($registro['Nove'])) ? $registro['Nove'] : [];

    foreach ($novedades as $novedad) {
        if ((string) ($novedad['Codi'] ?? '') === (string) $subtipo) {
            return (string) ($novedad['Horas'] ?? '');
        }
    }

    return '';
}

function ObtenerFichadas(array $registro): array
{
    $fichadas = (isset($registro['Fich']) && is_array($registro['Fich'])) ? $registro['Fich'] : [];
    return array_values(array_filter($fichadas, 'is_array'));
}

function ObtenerPrimerFichada(array $registro): string
{
    $fichadas = ObtenerFichadas($registro);
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

function ObtenerUltimaFichada(array $registro): string
{
    $fichadas = ObtenerFichadas($registro);
    if (count($fichadas) <= 1) {
        return '';
    }

    $ultima = $fichadas[count($fichadas) - 1];
    return (string) ($ultima['Hora'] ?? '');
}

function ObtenerTodasLasFichadas(array $registro): string
{
    $fichadas = ObtenerFichadas($registro);
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

function ObtenerValorCrudoCampo(array $registro, array $campo): string
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
            return ObtenerCodigoEstructura($registro, 'Empr');
        case 'cod_planta':
            return ObtenerCodigoEstructura($registro, 'Plan');
        case 'cod_convenio':
            return ObtenerCodigoEstructura($registro, 'Conv');
        case 'cod_sector':
            return ObtenerCodigoEstructura($registro, 'Sect');
        case 'cod_seccion':
            return ObtenerCodigoEstructura($registro, 'Sec2');
        case 'cod_grupo':
            return ObtenerCodigoEstructura($registro, 'Grup');
        case 'cod_sucursal':
            return ObtenerCodigoEstructura($registro, 'Sucu');
        case 'fecha':
            return (string) ($registro['Fech'] ?? '');
        case 'horas':
            return BuscarHoraPorSubtipo($registro, $subtipo);
        case 'novedades':
            return BuscarNovedadPorSubtipo($registro, $subtipo);
        case 'atra':
            return (string) ($registro['ATra'] ?? '');
        case 'trab':
            return (string) ($registro['Trab'] ?? '');
        case 'primer_fichada':
            return ObtenerPrimerFichada($registro);
        case 'ultima_fichada':
            return ObtenerUltimaFichada($registro);
        case 'todas_fichadas':
            return ObtenerTodasLasFichadas($registro);
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

function FormatearValorCampo(string $valorCrudo, array $campo, string $separador): string
{
    $tipo = (string) ($campo['tipo'] ?? '');
    $formato = (string) ($campo['formato'] ?? '');
    $tamano = (int) ($campo['tamano'] ?? 0);

    if ($formato === 'texto') {
        return SanitizarTextoSeparador($valorCrudo, $separador);
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
            return PadDerechaEspacios('', $tamano);
        }
        return PadIzquierda('', $tamano);
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
            return PadIzquierda($soloDigitos, $tamano);
        case 'apno':
            $sanitizado = SanitizarTextoSeparador($valorCrudo, $separador);
            return PadDerechaEspacios($sanitizado, $tamano);
        case 'cuil_legajo':
            return PadDerechaEspacios($valorCrudo, $tamano);
        case 'fecha':
            return FormatearFecha($valorCrudo, $formato);
        case 'horas':
        case 'novedades':
        case 'atra':
        case 'trab':
            return PadIzquierda(HoraADecimal($valorCrudo), $tamano);
        case 'turstr':
            return PadDerechaEspacios($valorCrudo, $tamano);
        default:
            if ($formato === 'decimal') {
                return PadIzquierda(number_format((float) $valorCrudo, 2, '.', ''), $tamano);
            }
            return PadIzquierda($valorCrudo, $tamano);
    }
}

function ConstruirLineaRegistro(array $registro, array $campos, string $separador): string
{
    $valores = [];

    foreach ($campos as $campo) {
        $valorCrudo = ObtenerValorCrudoCampo($registro, $campo);
        $valores[] = FormatearValorCampo($valorCrudo, $campo, $separador);
    }

    return implode($separador, $valores);
}

function GenerarContenidoExport(array $campos, array $registros, string $separador): string
{
    $lineas = [];

    foreach ($registros as $registro) {
        $lineas[] = ConstruirLineaRegistro($registro, $campos, $separador);
    }

    return implode("\r\n", $lineas);
}

function ExportarTxt(array $payload): array
{
    $fechaInicio = trim((string) ($payload['FechIni'] ?? ''));
    $fechaFin = trim((string) ($payload['FechFin'] ?? ''));
    $plantillaSlug = NormalizarPlantillaSlug($payload['plantilla'] ?? '');
    $config = LeerConfiguracion($plantillaSlug);
    $separador = NormalizarSeparador($config['separador'] ?? ',');
    $encabezados = NormalizarEncabezados($config['encabezados'] ?? 0);

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
    $registros = NormalizarRegistrosFicNovHor($ficNovHor);

    if (!$registros) {
        throw new Exception('No hay registros para exportar en el rango seleccionado.', 400);
    }

    $contenido = GenerarContenidoExport($campos, $registros, $separador);

    if ($encabezados === 1) {
        $lineaEncabezados = ConstruirEncabezados($campos, $separador);
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
