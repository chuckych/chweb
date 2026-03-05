<?php
require __DIR__ . '/../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$request = Flight::request();
// Flight::json($dataC['WebServiceCH'].'/RRHHWebService', 200) . exit;
$Horario = '';

// $checkMethod('POST');
$checkMethodMultiple(['POST', 'GET']);

function pingWS($textError, $webService) // Función para validar que el Webservice de Control Horario esta disponible
{
    // CRÍTICO: Cerrar sesión para no bloquear otras peticiones
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $url = rutaWS($webService, "Ping?");

    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor

    // TIMEOUTS CRÍTICOS - Evitar esperas largas
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // Timeout de conexión: 2 segundos
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout total de ejecución: 3 segundos
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 60); // Cache DNS por 60 segundos

    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, false); // Reutilizar conexiones existentes
    curl_setopt($ch, CURLOPT_FORBID_REUSE, false); // Permitir reutilización de conexiones
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // Máximo 3 redirecciones

    $response = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
        writeLog($text, __DIR__ . "../../logs/" . date('Ymd') . "_errorWebService.log", '');
        curl_close($ch); // Cerrar curl antes de salir
        http_response_code(400);
        (response([], 0, "Error Interno. WS", 400, 0, 0, 0));
        exit;
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
    curl_close($ch); // close curl handle

    return ($http_code == 201) ? true : (response([], 0, $textError, 400, 0, 0, 0)) . exit; // escribir en el log
}
function rutaWS($webService, $Comando)
{
    return $webService . "/" . $Comando;
}
/** PARA EL WEBSERVICE CH*/
function respuestaWS($respuesta)
{
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    return $respuesta[0];
}
function EstadoProcesoWS($url)
{
    // CRÍTICO: Cerrar sesión para no bloquear otras peticiones
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $maxRetries = 30; // Máximo 30 intentos (30 segundos aprox)
    $retryCount = 0;

    do {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // TIMEOUTS CRÍTICOS
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // Timeout de conexión: 2 segundos
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout total: 3 segundos

        $respuesta = curl_exec($ch);

        $curl_errno = curl_errno($ch);
        // error_log(print_r($respuesta, true));
        curl_close($ch);

        // Si hay error de curl, retornar error
        if ($curl_errno > 0) {
            return 'Error';
        }

        $retryCount++;

        // Si excede el máximo de reintentos, retornar timeout
        if ($retryCount >= $maxRetries) {
            return 'Timeout';
        }

        if ($respuesta === '{Pendiente}') {
            // error_log("Respuesta Pendiente, reintentando... Intento: $retryCount");
            usleep(300000); // Esperar 0.3 segundos
        }

    } while (respuestaWS($respuesta) == 'Pendiente');

    return respuestaWS($respuesta);
}
function getPremios($FechaDesde, $FechaHasta, $Legajos, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion, $Premio = 0, $PremioMostrar = 0, $webService)
{
    $time_start = microtime(true);
    $Legajos = implode(';', $Legajos);
    $FechaDesde = fecha($FechaDesde, 'd/m/Y');
    $FechaHasta = fecha($FechaHasta, 'd/m/Y');
    $ruta = rutaWS($webService, "Premios");
    $post_data = "{Usuario=SUPERVISOR,Legajos=[$Legajos],TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion,Premio=$Premio, PremioMostrar=$PremioMostrar}";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $ruta); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // Timeout de conexión: 2 segundos
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout total de ejecución: 1 segundo
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 60); // Cache DNS por 60 segundos

    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, false); // Reutilizar conexiones existentes
    curl_setopt($ch, CURLOPT_FORBID_REUSE, false); // Permitir reutilización de conexiones
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // Máximo 3 redirecciones

    // No verificar SSL en desarrollo (comentar en producción si usas HTTPS válido)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $respuesta = curl_exec($ch);

    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $text = "Error al obtener Premios. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
    if ($curl_errno > 0) {
        writeLog($text, __DIR__ . "/../logs/" . date('Ymd') . "_errorGetPremios.log", '');
        http_response_code(400);
        response($text, '0', 'Error', 400, $time_start, 0, '');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        writeLog($text, __DIR__ . '/../logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        $data = ['status' => 'error', 'dato' => $respuesta];
        http_response_code(400);
        response($respuesta, '0', 'Error', 400, $time_start, 0, '');
        exit;
    }
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    $processID = $respuesta[1];
    $url = rutaWS($webService, "Estado?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return ['ProcesoId' => $processID, 'Estado' => EstadoProcesoWS($url)];
    }

    fileLog($text, __DIR__ . '/../logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
}

$dp = $request->data ?? [];
$dq = $request->query ?? [];

if ($method === 'POST') {
    $stmtPremios = $dbApiQuery("SELECT * FROM PREMIOS") ?? [];

    if (empty($stmtPremios)) {
        http_response_code(400);
        (response([], 0, "No se encontraron premios.", 400, timeStart(), 0, 0));
        exit;
    }

    $stmtPremios = array_column($stmtPremios, null, 'PreCodi');
    $codiPremios = ["0" => 0];
    foreach ($stmtPremios as $premio) {
        $codiPremios[$premio['PreCodi']] = $premio['PreCodi'];
    }

    $pingWS = pingWS('Error Interno WS', $dataC['WebServiceCH'] . '/RRHHWebService');

    $start = start();
    $length = length();

    $FechaDesde = $dp->FechaDesde ?? [];
    $FechaDesde = vp($FechaDesde, 'FechaDesde', 'str', 10);

    $FechaHasta = $dp->FechaHasta ?? [];
    $FechaHasta = vp($FechaHasta, 'FechaHasta', 'str', 10);

    $LegajoDesde = $dp->LegajoDesde ?? [];
    $LegajoDesde = vp($LegajoDesde, 'LegajoDesde', 'str', 9);

    $Legajos = $dp->Legajos ?? [];
    $Legajos = vp($Legajos, 'Legajos', 'strArray', 9);

    $LegajoHasta = $dp->LegajoHasta ?? [];
    $LegajoHasta = vp($LegajoHasta, 'LegajoHasta', 'str', 9);

    $TipoDePersonal = $dp->TipoDePersonal ?? [];
    $TipoDePersonal = vp($TipoDePersonal, 'TipoDePersonal', 'int', 1);

    $Empresa = $dp->Empresa ?? [];
    $Empresa = vp($Empresa, 'Empresa', 'int', 4);

    $Planta = $dp->Planta ?? [];
    $Planta = vp($Planta, 'Planta', 'int', 4);

    $Sucursal = $dp->Sucursal ?? [];
    $Sucursal = vp($Sucursal, 'Sucursal', 'int', 4);

    $Grupo = $dp->Grupo ?? [];
    $Grupo = vp($Grupo, 'Grupo', 'int', 4);

    $Sector = $dp->Sector ?? [];
    $Sector = vp($Sector, 'Sector', 'int', 4);

    $Seccion = $dp->Seccion ?? [];
    $Seccion = vp($Seccion, 'Seccion', 'int', 4);

    $Premio = $dp->Premio ?? [];
    $Premio = vp($Premio, 'Premio', 'int', 4);

    if (in_array($Premio, $codiPremios) === false) {
        http_response_code(400);
        unset($codiPremios["0"]); // Eliminar la opción "0" del mensaje de error
        (response([], 0, "Parámetro 'Premio' erróneo. Debe ser uno de los siguientes valores: " . implode(', ', $codiPremios) . ".", 400, timeStart(), 0, 0));
        exit;
    }

    $PremioMostrar = $dp->PremioMostrar ?? [];
    $validPremioMostrar = [0, 1, 2, 3];

    if (in_array($PremioMostrar, $validPremioMostrar) === false) {
        http_response_code(400);
        (response([], 0, "Parámetro 'PremioMostrar' erroneo. Valor '$PremioMostrar'. Debe ser uno de los siguientes valores: 0, 1, 2, 3.", 400, timeStart(), 0, 0));
        exit;
    }

    $agrupar = $dq->agrupar ?? '';
    $validAgrupar = ['premio', 'legajo'];
    if ($agrupar && in_array($agrupar, $validAgrupar) === false) {
        http_response_code(400);
        (response([], 0, "Parámetro 'agrupar' erroneo. Valor '$agrupar'. Debe ser uno de los siguientes valores: 'premio', 'legajo'.", 400, timeStart(), 0, 0));
        exit;
    }

    if (!empty($Legajos)) {
        $sql = "SELECT LegNume FROM PERSONAL WHERE LegNume IN (" . implode(',', $Legajos) . ")";
        $stmtLegajos = $dbApiQuery($sql) ?? [];
        $stmtLegajos = array_column($stmtLegajos, 'LegNume');
        $invalidLegajos = array_diff($Legajos, $stmtLegajos);
        if (!empty($invalidLegajos)) {
            http_response_code(400);
            (response([], 0, "Los siguientes legajos no existen: " . implode(', ', $invalidLegajos) . ".", 400, timeStart(), 0, 0));
            exit;
        }
    }

    $getPremios = getPremios(
        $FechaDesde,
        $FechaHasta,
        $Legajos,
        $LegajoDesde,
        $LegajoHasta,
        $TipoDePersonal,
        $Empresa,
        $Planta,
        $Sucursal,
        $Grupo,
        $Sector,
        $Seccion,
        $Premio,
        $PremioMostrar,
        $dataC['WebServiceCH'] . '/RRHHWebService',
    );

    $data = [];
    if ($getPremios && $getPremios['Estado'] != 'Terminado') {
        $arrPremios = preg_split('/(\r|\n)/', $getPremios['Estado'], -1, 1);

        foreach ($arrPremios as $key => $value) {
            $explode = explode(',', $value);
            $premio = $explode[0] ?? '';
            $descripcion = trim($explode[1] ?? '');
            $legajo = $explode[2] ?? '';
            $nombre = trim($explode[3] ?? '');
            $cuil = trim($explode[4] ?? '');
            $valor = $explode[5] ?? '';

            switch ($agrupar) {
                case 'premio':
                    $premioKey = intval($premio);
                    $data[$premioKey]['Premio'] = $premioKey;
                    $data[$premioKey]['Descripcion'] = $descripcion;
                    $data[$premioKey]['CodigosLiquidacion']['CodM'] = $stmtPremios[$premioKey]['PreCodM'] ?? 0;
                    $data[$premioKey]['CodigosLiquidacion']['CodJ'] = $stmtPremios[$premioKey]['PreCodJ'] ?? 0;
                    $data[$premioKey]['CodigosLiquidacion']['CodM2'] = $stmtPremios[$premioKey]['PreCodM2'] ?? 0;
                    $data[$premioKey]['CodigosLiquidacion']['CodJ2'] = $stmtPremios[$premioKey]['PreCodJ2'] ?? 0;
                    $data[$premioKey]['Valor'] = intval($stmtPremios[$premioKey]['PreValor']) ?? 0;
                    $data[$premioKey]['Legajos'][] = [
                        'Legajo' => intval($legajo),
                        'Nombre' => $nombre,
                        'Cuil' => $cuil,
                        'Valor' => floatval($valor),
                    ];
                    break;
                case 'legajo':
                    $legajoKey = intval($legajo);
                    $data[$legajoKey]['Legajo'] = $legajoKey;
                    $data[$legajoKey]['Nombre'] = $nombre;
                    $data[$legajoKey]['Cuil'] = $cuil;
                    $data[$legajoKey]['Premios'][] = [
                        'Premio' => intval($premio),
                        'Descripcion' => $descripcion,
                        'Valor' => floatval($valor),
                    ];
                    break;
                default:
                    $data[] = [
                        'Premio' => intval($premio),
                        'Descripcion' => $descripcion,
                        'Legajo' => intval($legajo),
                        'Nombre' => $nombre,
                        'Cuil' => $cuil,
                        'Valor' => floatval($valor),
                    ];
                    break;
            }
        }
    }
    $countData = count($data);
    http_response_code(200);
    response($data, $countData, 'OK', 200, $time_start, $countData, $idCompany);
}
if ($method === 'GET') {
    $codigo = $dq->codigo ?? '';

    if ($codigo && !is_numeric($codigo)) {
        http_response_code(400);
        (response([], 0, "Parámetro 'codigo' debe ser numérico.", 400, timeStart(), 0, 0));
        exit;
    }

    $sql = "SELECT * FROM PREMIOS";
    $sql .= $codigo ? " WHERE PreCodi = $codigo" : "";
    $stmt = $dbApiQuery($sql) ?? [];

    if(empty($stmt)) {
        http_response_code(400);
        (response([], 0, "No se encontraron premios.", 400, timeStart(), 0, 0));
        exit;
    }

    $sql1 = "SELECT * FROM PREMIOS1"; // condiciones de premios
    $stmt1 = $dbApiQuery($sql1) ?? [];
    // agrupar condiciones por PreCodi. Puede haber mas de un PreCodi en PREMIOS1, por eso se agrupa en un array
    $stmt1 = array_reduce($stmt1, function ($carry, $item) {
        $carry[$item['PreCodi']][] = $item;
        return $carry;
    }, []);

    $MapTipoCond = [
        0 => 'Cantidad de Novedades',
        1 => 'Horas de Novedades',
        2 => 'Horas de una Novedad',
        3 => 'Horas de dos Novedades',
        4 => 'Minutos de Novedades',
        5 => 'Minutos de una Novedad',
        6 => 'Minutos de dos Novedades',
    ];

    $sql2 = "SELECT PreCodi, PreCond, PreNove FROM PREMIOS2"; // condiciones de premios
    $stmt2 = $dbApiQuery($sql2) ?? [];
    // error_log(print_r($stmt2, true));

    // obtener un array unico con todos los PreNove
    $allPreNove = array_unique(array_column($stmt2, 'PreNove')) ?? [];
    $allPreNoveStr = implode(', ', $allPreNove);

    if (!empty($allPreNove)) {
        $sqlNovedades = "SELECT NovCodi, NovDesc FROM NOVEDAD WHERE NovCodi IN ($allPreNoveStr)";
        $stmtNovedades = $dbApiQuery($sqlNovedades) ?? [];

        $mapNovedades = [];
        foreach ($stmtNovedades as $novedad) {
            $mapNovedades[$novedad['NovCodi']] = [
                'Codi' => $novedad['NovCodi'],
                'Desc' => $novedad['NovDesc'],
            ];
        }
        // reemplazar PreNove por su descripcion en stmt2
        foreach ($stmt2 as &$item) {
            $item['PreNove'] = $mapNovedades[$item['PreNove']] ?? 'Desconocida';
        }
    }

    // agrupar condiciones por PreCodi y PreCond. Puede haber mas de un PreCodi y PreCond en PREMIOS2, por eso se agrupa en un array
    $stmt2 = array_reduce($stmt2, function ($carry, $item) {
        $carry[$item['PreCodi']][$item['PreCond']][] = $item;
        return $carry;
    }, []);

    foreach ($stmt as &$premio) {
        $data[] = [
            'Codi' => intval($premio['PreCodi']),
            'Desc' => $premio['PreDesc'],
            'Valor' => floatval($premio['PreValor']),
            'CodM' => intval($premio['PreCodM']),
            'CodJ' => intval($premio['PreCodJ']),
            'CodM2' => intval($premio['PreCodM2']),
            'CodJ2' => intval($premio['PreCodJ2']),
            'Condiciones' => array_map(function ($condicion) use ($MapTipoCond, $stmt2) {
                return [
                    'Condicion' => intval($condicion['PreCond']),
                    'Tipo' => $MapTipoCond[$condicion['PreTipo']] ?? 'Desconocido',
                    'Cantidad' => $condicion['PreCant'],
                    'Valor a Descontar' => $condicion['PreRest'],
                    'Novedades' => array_column($stmt2[$condicion['PreCodi']][$condicion['PreCond']] ?? [], 'PreNove'),
                ];
            }, $stmt1[$premio['PreCodi']] ?? []),
        ];
    }

    http_response_code(200);
    response($data, '0', 'OK', 200, timeStart(), count($data), $idCompany);
}
