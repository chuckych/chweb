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

$checkMethod('POST');

$stmtHorarios = $dbApiQuery("SELECT HorCodi, HorDesc, HorID FROM HORARIOS") ?? '';

function pingWebService($textError, $webService) // Función para validar que el Webservice de Control Horario esta disponible
{
    // CRÍTICO: Cerrar sesión para no bloquear otras peticiones
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $url = rutaWebService($webService, "Ping?");

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
pingWebService('Error Interno WS', $dataC['WebServiceCH'] . '/RRHHWebService');

function rutaWebService($webService, $Comando)
{
    return $webService . "/" . $Comando;
}
/** PARA EL WEBSERVICE CH*/
function respuestaWebService($respuesta)
{
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    return $respuesta[0];
}
function EstadoProceso($url)
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

    } while (respuestaWebService($respuesta) == 'Pendiente');

    return respuestaWebService($respuesta);
}

function getHorario($FechaDesde, $FechaHasta, $Legajos, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion, $webService)
{
    $time_start = microtime(true);
    $Legajos = implode(';', $Legajos);
    $FechaDesde = fecha($FechaDesde, 'd/m/Y');
    $FechaHasta = fecha($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService($webService, "Horarios");
    $post_data = "{Usuario=SUPERVISOR,Legajos=[$Legajos],TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion}";

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
    $text = "Error al obtener Horario. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
    if ($curl_errno > 0) {
        writeLog($text, __DIR__ . "/../logs/" . date('Ymd') . "_errorGetHorario.log", '');
        http_response_code(400);
        error_log(print_r($text . " Cod: $curl_errno: $curl_error", true));
        response($text, '0', 'Error', 400, $time_start, 0, '');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        writeLog($text, __DIR__ . '/../logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        $data = ['status' => 'error', 'dato' => $respuesta];
        http_response_code(400);
        error_log(print_r($text . " Cod: $curl_errno: $curl_error", true));
        response($respuesta, '0', 'Error', 400, $time_start, 0, '');
        exit;
    }
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    $processID = $respuesta[1];
    $url = rutaWebService($webService, "Estado?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return ['ProcesoId' => $processID, 'Estado' => EstadoProceso($url)];
    }

    fileLog($text, __DIR__ . '/../logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
    error_log(print_r($text, true));
}

$wc = '';

$dp = $request->data;

$start = start();
$length = length();

$dp->FechaDesde = ($dp->FechaDesde) ?? [];
$dp->FechaDesde = vp($dp->FechaDesde, 'FechaDesde', 'str', 10);

$dp->FechaHasta = ($dp->FechaHasta) ?? [];
$dp->FechaHasta = vp($dp->FechaHasta, 'FechaHasta', 'str', 10);

$dp->LegajoDesde = ($dp->LegajoDesde) ?? [];
$dp->LegajoDesde = vp($dp->LegajoDesde, 'LegajoDesde', 'str', 9);

$dp->Legajos = ($dp->Legajos) ?? [];
$dp->Legajos = vp($dp->Legajos, 'Legajos', 'strArray', 9);

$dp->LegajoHasta = ($dp->LegajoHasta) ?? [];
$dp->LegajoHasta = vp($dp->LegajoHasta, 'LegajoHasta', 'str', 9);

$dp->TipoDePersonal = ($dp->TipoDePersonal) ?? [];
$dp->TipoDePersonal = vp($dp->TipoDePersonal, 'TipoDePersonal', 'int', 1);

$dp->Empresa = ($dp->Empresa) ?? [];
$dp->Empresa = vp($dp->Empresa, 'Empresa', 'int', 4);

$dp->Planta = ($dp->Planta) ?? [];
$dp->Planta = vp($dp->Planta, 'Planta', 'int', 4);

$dp->Sucursal = ($dp->Sucursal) ?? [];
$dp->Sucursal = vp($dp->Sucursal, 'Sucursal', 'int', 4);

$dp->Grupo = ($dp->Grupo) ?? [];
$dp->Grupo = vp($dp->Grupo, 'Grupo', 'int', 4);

$dp->Sector = ($dp->Sector) ?? [];
$dp->Sector = vp($dp->Sector, 'Sector', 'int', 4);

$dp->Seccion = ($dp->Seccion) ?? [];
$dp->Seccion = vp($dp->Seccion, 'Seccion', 'int', 4);

$getHorario = getHorario(
    $dp->FechaDesde,
    $dp->FechaHasta,
    $dp->Legajos,
    $dp->LegajoDesde,
    $dp->LegajoHasta,
    $dp->TipoDePersonal,
    $dp->Empresa,
    $dp->Planta,
    $dp->Sucursal,
    $dp->Grupo,
    $dp->Sector,
    $dp->Seccion,
    $dataC['WebServiceCH'] . '/RRHHWebService',
);

$data = [];
if ($getHorario && $getHorario['Estado'] != 'Terminado') {
    $arrHorario = preg_split('/(\r|\n)/', $getHorario['Estado'], -1, 1);

    foreach ($arrHorario as $key => $value) {
        $explode = explode(',', $value);
        $legajo = $explode[0];
        $fecha = fecha(str_replace('/', '-', $explode[1]));
        $desde = $explode[2];
        $hasta = $explode[3];
        $descanso = $explode[4];
        $laboral = $explode[5];
        $feriado = $explode[6];
        $asignacion = $explode[7];
        $codigo = $explode[8];
        $dia = diaSemana($fecha);

        $horariodesc = '';
        if ($codigo > 0) {
            $filtroHorarios = filtrarObjetoArr($stmtHorarios, 'HorCodi', $codigo);

            foreach ($filtroHorarios as $key => $a) {
                $horario = [
                    'cod' => $a['HorCodi'],
                    'desc' => $a['HorDesc'],
                    'horID' => $a['HorID']
                ];
            }
            $horario['desc'] = str_replace("  ", " ", $horario['desc']);
            $horariodesc = (intval($asignacion) == 0) ? '' : ' ' . $horario['desc'];
            $horariodesc = (intval($codigo) == 0) ? '' : ' ' . $horario['desc'];
        }

        $mapAsignacion = [
            0 => 'Sin Horario Asignado',
            1 => 'Citación',
            3 => 'Desde Hasta por Legajo',
            6 => 'Rotación por Legajo',
            7 => 'Rotación por Sector',
            8 => 'Rotación por Grupo',
            9 => 'Desde por Legajo',
            10 => 'Desde por Sector',
            11 => 'Desde por Grupo'
        ];

        $mapLaboral = [
            0 => 'No',
            1 => 'Sí'
        ];

        $mapFeriado = [
            0 => 'No',
            1 => 'Sí'
        ];

        $siEsferiado = $feriado === '1' ? true : false;
        $siEsLaboral = $laboral === '1' ? true : false;

        $vsHorario = $siEsLaboral ? $desde . ' a ' . $hasta : ($siEsferiado ? 'Feriado' : 'Franco');

        $tipo = $mapAsignacion[intval($asignacion)] ?? '';
        $diaLaboral = $mapLaboral[intval($laboral)] ?? '';
        $diaFeriado = $mapFeriado[intval($feriado)] ?? '';

        $Mensaje = $getHorario['Estado'] ? $tipo . ' (' . $vsHorario . ') ' . $horariodesc : 'Sin datos';
        $Horario = $horario['desc'] ?? 'Sin datos';
        $HorarioID = $horario['horID'] ?? '-';

        $data[] = [
            'Codigo' => intval($codigo),
            'Horario' => $Horario,
            'HorarioID' => $HorarioID,
            'Fecha' => $fecha,
            'Dia' => $dia,
            'Feriado' => $diaFeriado,
            'Laboral' => $diaLaboral,
            'Desde' => $desde,
            'Hasta' => $hasta,
            'Descanso' => $descanso,
            'Legajo' => intval($legajo),
            'TipoAsign' => $tipo,
            'TipoAsignStr' => $Mensaje
        ];
    }
}
$countData = count($data);
http_response_code(200);
response($data, '0', 'OK', 200, $time_start, $countData, $idCompany);