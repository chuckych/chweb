<?php
function clean_files($path, $days, $ext)
{
    $files = glob($path . '*' . $ext); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) { // recorremos todos los ficheros.
        $lastModifiedTime = filemtime($file); // obtenemos la fecha de modificación del fichero
        $currentTime = time(); // obtenemos la fecha actual
        $dateDiff = dateDifference(date('Ymd', $lastModifiedTime), date('Ymd', $currentTime)); // obtenemos la diferencia de fechas
        ($dateDiff >= $days) ? unlink($file) : ''; //elimino el fichero
    }
}
/**
 * Realiza una llamada a la API CH.
 *
 * @param string $endpoint El endpoint de la API.
 * @param array $payload El payload de la solicitud (opcional).
 * @param string $method El método de la solicitud (opcional, por defecto es GET).
 * @param array $queryParams Los parámetros de la consulta (opcional).
 * @return mixed Los datos devueltos por la API o false si hay un error.
 * @throws Exception Si ocurre un error durante la llamada a la API.
 */
function ch_api()
{
    timeZone();
    timeZone_lang();

    $argumento = func_get_args(); // Obtengo los argumentos de la función en un array   
    $endpoint = $argumento[0] ?? ''; // Obtengo el endpoint
    $payload = $argumento[1] ?? array(); // Obtengo el payload
    $method = $argumento[2] ?? 'GET'; // Obtengo el método
    $queryParams = $argumento[3] ?? array(); // Obtengo los parámetro de la query
    $method = strtoupper($method); // Convierto el método a mayúsculas
    try {

        if (!$endpoint) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
        }
        //file_put_contents('params.log', print_r(http_build_query($queryParams), true) . PHP_EOL, FILE_APPEND); // log error
        $endpoint = $queryParams ? $endpoint . "?" . http_build_query($queryParams) : $endpoint; // Si hay parámetros de query, los agrego al endpoint
        $ch = curl_init(); // Inicializo curl

        curl_setopt($ch, CURLOPT_URL, $endpoint); // Seteo la url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Seteo el retorno de la respuesta
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Seteo el timeout de la conexión
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Seteo la verificación del host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Seteo la verificación del peer
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Permitir seguir redirecciones
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Máximo de redirecciones
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            $payload ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }
        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        if ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            $payload ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }
        if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            $payload ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }

        $token = sha1($_SESSION['RECID_CLIENTE']);
        $AGENT = $_SERVER['HTTP_USER_AGENT'];
        $headers = [
            "Accept: */*",
            'Content-Type: application/json',
            "Token: {$token}",
            "User-Agent: {$AGENT}",
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Seteo los headers
        $file_contents = curl_exec($ch); // Ejecuto curl
        //file_put_contents(__DIR__ . '/logs/api.log', print_r($file_contents, true) . PHP_EOL, FILE_APPEND); // log error

        $curl_errno = curl_errno($ch); // get error code
        $curl_error = curl_error($ch); // get error information

        if ($curl_errno > 0) { // si hay error
            $text = "cURL Error ($curl_errno): $curl_error"; // set error message
            // file_put_contents(__DIR__ . '/logs/api.log', $text . PHP_EOL, FILE_APPEND); // log error
            throw new Exception($text);
        }
        if (!$file_contents) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Error al obtener datos');
        }
        curl_close($ch);
        $text = 'API CH: ' . date('Y-m-d H:i:s') . ' ' . json_encode($file_contents);
        return $file_contents;
    } catch (\Exception $e) {
        curl_close($ch);
        return false;
    }
}
/**
 * Obtiene las causas de una novedad específica.
 *
 * @param string $novedad El código de la novedad.
 * @return array Un array con las causas de la novedad o un array vacío si no se encuentran causas.
 */
function getNoveCausas($novedad)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $method = 'GET';
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "NovC"
    );

    $data = ch_api($endpoint, '', $method, $queryParams);

    $arrayData = json_decode($data, true);

    $rs = array_filter($arrayData['DATA'], function ($element) use ($novedad) {
        return $element['CodiNov'] == $novedad;
    });

    return array_values($rs) ?? array();
}
function getParamLiquid()
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/parametros/liquid";
    $method = 'GET';
    $data = ch_api($endpoint, '', $method, []);
    $arrayData = json_decode($data, true);
    return $arrayData['DATA'] ?? [];
}
function getFichasMinMax()
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/fichas/dateMinMax";
    $method = 'GET';
    $data = ch_api($endpoint, '', $method, []);
    $arrayData = json_decode($data, true);
    return $arrayData['DATA'] ?? [];
}
function estructuras($estructuras)
{
    // estructuras
    // {
    //     "Empr": "100,1,2,300,200",
    //     "Plan": "1,2,4,5,8,19,3,9,18,10,7,13",
    //     "Conv": "2,4",
    //     "Sect": "100,2,200,3,1",
    //     "Secc": "21,12",
    //     "Grup": "1,2",
    //     "Sucu": "1,6,3,4"
    // }
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/estructuras";

    $data = ch_api($endpoint, $estructuras, 'POST', '');

    $arrayData = json_decode($data, true);

    $arrayData['arrayData'] = $arrayData['DATA'] ?? '';

    if (empty($arrayData['DATA'])) {
        return [];
    }

    return $arrayData['DATA'] ?? array();
}
/**
 * Obtiene los datos de una ficha de novedades y horarios de forma simplificada.
 *
 * @param string $legajo El legajo del empleado.
 * @param string $fecha La fecha para la cual se obtendrán los datos.
 * @param array $opt Opciones adicionales para filtrar los datos.
 * @return array|false Los datos de la ficha de novedades y horarios, o false si no se proporciona el legajo o la fecha.
 */
function getFicNovHorSimple($legajo, $fecha, $opt)
{
    if (!$legajo)
        return false;
    if (!$fecha)
        return false;
    $payload = [
        "FechIni" => $fecha,
        "FechFin" => $fecha,
        "onlyReg" => "0",
        "getReg" => $opt['getFic'] ?? 0,
        "getNov" => $opt['getNov'] ?? 0,
        "getONov" => $opt['getONov'] ?? 0,
        "getHor" => $opt['getHor'] ?? 0,
        "getEstruct" => $opt['getEstruct'] ?? 0,
        "getCierre" => $opt['getCierre'] ?? 1,
        "NovEx" => "",
        "ONovEx" => "",
        "HoraEx" => "",
        "LegApNo" => "",
        "LegDocu" => [],
        "LegRegCH" => [],
        "LegTipo" => [],
        "LegaD" => "",
        "LegaH" => "",
        "Lega" => [$legajo],
        "Empr" => [],
        "Plan" => [],
        "Conv" => [],
        "Sec2" => [],
        "Sect" => [],
        "Grup" => [],
        "Sucu" => [],
        "NovT" => [],
        "NovS" => [],
        "NovA" => [],
        "NovI" => [],
        "DiaL" => [],
        "DiaF" => [],
        "HsAT" => [],
        "HsTr" => [],
        "HorE" => [],
        "HorS" => [],
        "HorD" => [],
        "Falta" => [],
        "Nove" => [],
        "NoTi" => [],
        "ONov" => [],
        "Hora" => [],
        "Esta" => [],
        "EstaNov" => $opt['EstaNov'] ?? [],
        "start" => 0,
        "length" => $opt['length'] ?? 1
    ];
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/ficnovhor/";
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    $DATA = $arrayData['DATA'] ?? [];
    if (empty($DATA)) {
        return [];
    }
    return [$DATA[0]];
}
function fic_nove_horas($payload)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/ficnovhor/";
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    $DATA = $arrayData['DATA'] ?? [];
    if (empty($DATA)) {
        return [];
    }
    return $DATA;
}
function v1_api($endpoint, $method, $payload)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1" . $endpoint;
    $data = ch_api($endpoint, $payload, $method, '');
    $arrayData = json_decode($data, true);
    $DATA = $arrayData['DATA'] ?? [];
    if (empty($DATA)) {
        return [];
    }
    return $DATA;
}

function get_estructura($query)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct";
    $data = ch_api($endpoint, [], 'GET', $query);
    $arrayData = json_decode($data, true);
    $DATA = $arrayData['DATA'] ?? [];
    if (empty($DATA)) {
        return [];
    }
    return $DATA;
}
function getHorasTotales($payload)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horas/totales/";
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    // print_r($payload) . exit;
    $arrayData['DATA'] = $arrayData['DATA'] ?? '';
    if (empty($arrayData['DATA'])) {
        return [];
    }
    return ($arrayData['DATA']);
}
function getNovedadesTotales($payload)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/novedades/totales/";
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    // print_r($payload) . exit;
    $Data = $arrayData['DATA'] ?? '';
    if (empty($Data)) {
        return [];
    }
    return $Data;
}
function getHorasTotalesDT($payload)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horas/totales/";
    // print_r($payload) . exit;
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    $arrayData['DATA'] = $arrayData['DATA'] ?? [];
    // if (empty($arrayData['DATA'])) {
    //     return [];
    // }
    $dt_data = array(
        "recordsTotal" => intval($arrayData['TOTAL']) ?? 0,
        "recordsFiltered" => intval($arrayData['COUNT']) ?? 0,
        "data" => $arrayData['DATA']['data'] ?? [],
        "totales" => $arrayData['DATA']['totales'] ?? [],
        "totalesTryAT" => $arrayData['DATA']['totalesTryAT'] ?? '',
        "tiposHoras" => $arrayData['DATA']['tiposHoras'] ?? [],
    );
    return ($dt_data);
}
function getNovedadesTotalesDT($payload)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/novedades/totales/";
    // print_r($payload) . exit;
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    $arrayData['DATA'] = $arrayData['DATA'] ?? [];

    $dt_data = array(
        "recordsTotal" => intval($arrayData['TOTAL']) ?? 0,
        "recordsFiltered" => intval($arrayData['COUNT']) ?? 0,
        "data" => $arrayData['DATA']['data'] ?? [],
        "totales" => $arrayData['DATA']['totales'] ?? [],
        "novedades" => $arrayData['DATA']['novedades'] ?? [],
    );
    return ($dt_data);
}
/**
 * Obtiene el cierre de ficha para un legajo y fecha específicos.
 *
 * @param string $legajo El legajo del empleado.
 * @param string $fecha La fecha para la cual se desea obtener el cierre de ficha.
 * @return array El cierre de ficha para el legajo y fecha especificados.
 */
function getCierreFicha($legajo, $fecha)
{
    if (!$legajo || !$fecha) {
        return array();
    }

    $opt = array("getNov" => "0", "getONov" => "0", "getHor" => "0", "getFic" => "0");
    $data = getFicNovHorSimple($legajo, $fecha, $opt);
    $cierre = $data[0]['Cierre'] ?? array();
    return $cierre;
}
/**
 * Retrieves a specific novedad from the API.
 *
 * @param string | array $novedad The code of the novedad to retrieve.
 * @return array The data of the novedad, or an empty array if not found.
 */
function getNovedad($novedad)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $method = 'GET';
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "Nov",
        "Codi" => (is_array($novedad)) ? $novedad : [$novedad]
    );

    $data = ch_api($endpoint, '', $method, $queryParams);

    $arrayData = json_decode($data, true);
    return ($arrayData['DATA']) ?? array();
}
function getPersonal($payload)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/personal/";
    $personal = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($personal, true);
    if ($arrayData['RESPONSE_CODE'] == '200') {
        $arrayData = $arrayData['DATA'] ?? [];
    } else {
        $arrayData = [];
    }
    return $arrayData;
}
function get_horario_actual($Legajos)
{
    if (!$Legajos) {
        return [];
    }

    $payload = [
        "FechaDesde" => date('Y-m-d'),
        "FechaHasta" => date('Y-m-d'),
        "LegajoDesde" => 1,
        "LegajoHasta" => 99999999,
        "TipoDePersonal" => 0,
        'Legajos' => $Legajos,
        "Empresa" => 0,
        "Planta" => 0,
        "Sector" => 0,
        "Seccion" => 0,
        "Sucursal" => 0,
        "Grupo" => 0,
    ];

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/horasign/";
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    $data = $arrayData['DATA'] ?? '';
    if (empty($data)) {
        return [];
    }
    return $data;
}

Flight::map('personal', function ($payload) {
    $endpoint = URLAPI . "/api/personal/";
    $personal = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($personal, true);
    $result = (($arrayData['RESPONSE_CODE'] ?? '') == '200') ? $arrayData['DATA'] : [];
    return $result;
});

function novedadesRol()
{
    $novedadesRol = $_SESSION['ListaNov'] ?? '';
    $novedadesRol = ($novedadesRol && $novedadesRol != '-') ? explode(',', $novedadesRol) : [];
    return $novedadesRol;
}
/**
 * Combina dos arrays eliminando duplicados.
 *
 * @param array $arr1 El primer array.
 * @param array $arr2 El segundo array.
 * @return array El array resultante de combinar los dos arrays sin duplicados.
 */
function mergeArray($arr1, $arr2)
{
    if (!is_array($arr1)) {
        $arr1 = [];
    }
    return $arr2 ? array_unique(array_merge($arr1, $arr2)) : $arr1;
}

function dateCustomDay($day)
{
    return date('Y-m-d', strtotime(date('Y-m-') . $day));
}
;
function procesar_por_intervalos($data, $payload)
{
    $intervals = [];
    $currentIntervals = [];
    $payloadData = $payload['data'] ?? []; // Datos del payload
    $LegTipo = $payload['LegTipo'][0] ?? 0; // Tipo de legajo

    $Jor1Desde = $payloadData['Jor1Desde'] ?? 1; // Día de inicio de la quincena 1
    $Jor1Hasta = $payloadData['Jor1Hasta'] ?? 15; // Día de fin de la quincena 1
    $Jor2Desde = $payloadData['Jor2Desde'] ?? 16; // Día de inicio de la quincena 2
    $Jor2Hasta = $payloadData['Jor2Hasta'] ?? 30; // Día de fin de la quincena 2
    $MensDesde = $payloadData['MensDesde'] ?? 1; // Día de inicio del mes
    $MensHasta = $payloadData['MensHasta'] ?? 31; // Día de fin del mes
    $jornal = $payloadData['jornal'] ?? 1; // Tipo de jornal

    $desdeVariable = $LegTipo == 1 ? ($jornal == 1 ? $Jor1Desde : $Jor2Desde) : $MensDesde; // Variable para el día de inicio
    $hastaVariable = $LegTipo == 1 ? ($jornal == 1 ? $Jor1Hasta : $Jor2Hasta) : $MensHasta; // Variable para el día de fin

    // $Desde = date('Y-m-d', strtotime(date('Y-m-') . $desdeVariable));
    $Desde = dateCustomDay($desdeVariable); // Día de inicio
    // $Hasta = date('Y-m-d', strtotime(date('Y-m-') . $hastaVariable));
    $Hasta = dateCustomDay($hastaVariable); // Día de fin
    try {
        foreach ($data as $record) {

            if (empty($record['Nove'])) {
                continue;
            }

            $fecha = $record['Fech']; // Fecha de la novedad
            $lega = $record['Lega']; // Legajo

            foreach ($record['Nove'] as $novedad) {
                $codigo = $novedad['Codi']; // Código de la novedad
                $key = "{$codigo}-{$lega}"; // Clave para agrupar intervalos

                if (!isset($currentIntervals[$key])) { // Si no existe el intervalo, crear uno nuevo
                    $currentIntervals[$key] = [
                        'Action' => '',
                        'Company' => 1256,
                        'Employee' => $lega,
                        'EmployeeStr' => $record['ApNo'],
                        'Digit' => '',
                        'Cod Inasistencia' => $codigo,
                        'Novedad' => $novedad['Codi'],
                        'NovedadStr' => $novedad['Desc'],
                        'Fecha inicio' => $fecha,
                        'Fecha fin' => $fecha,
                        'codigo' => $codigo,
                        'lega' => $lega,
                        'descripcion' => $novedad['Desc']
                    ];
                } else { // Si ya existe el intervalo, actualizar la fecha final
                    $prevDate = date('Y-m-d', strtotime($currentIntervals[$key]['Fecha fin']));
                    $nextDate = date('Y-m-d', strtotime("{$prevDate} +1 day"));

                    if ($fecha == $nextDate) { // Si es un día consecutivo, actualizar la fecha final
                        // Es un día consecutivo, actualizar la fecha final
                        $currentIntervals[$key]['Fecha fin'] = $fecha;
                    } else { // No es consecutivo, guardar el intervalo actual y crear uno nuevo
                        // No es consecutivo, guardar el intervalo actual y crear uno nuevo
                        $intervals[] = $currentIntervals[$key]; // Guardar el intervalo actual
                        $currentIntervals[$key] = [
                            'Action' => '',
                            'Company' => 1256,
                            'Employee' => $lega,
                            'EmployeeStr' => $record['ApNo'],
                            'Digit' => '',
                            'Cod Inasistencia' => $codigo,
                            'Novedad' => $novedad['Codi'],
                            'NovedadStr' => $novedad['Desc'],
                            'Fecha inicio' => $fecha,
                            'Fecha fin' => $fecha,
                            'codigo' => $codigo,
                            'lega' => $lega,
                            'descripcion' => $novedad['Desc']
                        ];
                    }
                }
                // En Action se coloca 2 si el primer día de la licencia es dentro de la quincena actual y 3 si es de una quincena anterior.
                $action = $fecha >= $Desde && $fecha <= $Hasta ? 2 : 3;
                $currentIntervals[$key]['Action'] = $action;
            }
        }

        // Agregar los últimos intervalos pendientes
        foreach ($currentIntervals as $interval) {
            $intervals[] = $interval;
        }
        return [
            'Data' => $intervals,
            'LegTipo' => $LegTipo,
        ];
    } catch (\Throwable $th) {
        file_put_contents(__DIR__ . '/logs/api.log', print_r($th, true) . PHP_EOL, FILE_APPEND); // log error
        throw new Exception($th);
    }
}

function get_tipo_hora()
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horas/data";
    $data = ch_api($endpoint, '', 'GET', ['start' => 0, 'length' => 9000]);

    $arrayData = json_decode($data, true);
    return $arrayData['DATA'] ?? [];
}
function get_novedades()
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/novedades/data";
    $data = ch_api($endpoint, '', 'GET', ['start' => 0, 'length' => 9000]);

    $arrayData = json_decode($data, true);
    return $arrayData['DATA'] ?? [];
}
function get_params($queryParams = [])
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/_local/params";
    $data = ch_api($endpoint, '', 'GET', $queryParams);
    $arrayData = json_decode($data, true);
    return $arrayData['DATA'] ?? [];
}
function add_params($payloadParams = [])
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/_local/params";
    $data = ch_api($endpoint, $payloadParams, 'POST', '');
    $arrayData = json_decode($data, true);
    return $arrayData['DATA'] ?? [];
}
function horasCustom($params)
{
    if (!$params) {
        return [];
    }
    foreach ($params as $key => $valueParams) {
        if (in_array($valueParams['descripcion'], ['columnas', 'columnasSorted'])) {
            continue;
        }
        $horasParams[] = [
            'THoCodi' => "3300{$key}",
            'THoDesc' => $valueParams['descripcion'],
            'THoDesc2' => $valueParams['descripcion'],
            'THoID' => '',
            'THoColu' => 0,
            'ThoValores' => $valueParams['valores'],
            'FechaHora' => '',
        ];
    }
    return $horasParams ?? [];
}
function colsExcel(): array
{
    $horas = get_tipo_hora();
    $queryParams = ['cliente' => $_SESSION['ID_CLIENTE'] ?? '', 'descripcion' => '', 'modulo' => 46, 'valores' => ''];
    $params = get_params($queryParams) ?? [];
    $horasParams = horasCustom($params);


    array_push($horas, ...$horasParams);

    $queryColumnSorted = ['cliente' => $_SESSION['ID_CLIENTE'] ?? '', 'descripcion' => 'columnasSorted', 'modulo' => 46];
    $columnas = get_params($queryColumnSorted) ?? [];
    $columnasValores = $columnas[0]['valores'];
    if (!$columnasValores) {
        throw new Exception('No se encontraron columnas', 400);
    }

    if ($columnasValores) {
        // Convertir $columnasValores en un array de enteros
        $columnasValores = array_map('intval', explode(',', $columnasValores));
        // Crear un array asociativo con THoCodi como clave y el array como valor
        $horas = array_column($horas, null, 'THoCodi');
        // Crear un nuevo array ordenado según $valoresSorted
        $horasOrdenadas = [];
        foreach ($columnasValores as $THoCodi) {
            if (isset($horas[$THoCodi])) {
                $horasOrdenadas[$THoCodi] = $horas[$THoCodi];
            }
        }
    }
    $colsExcel = [];
    foreach ($horasOrdenadas as $key => $value) {
        $colsExcel[$value['THoDesc2']] = [
            'ancho' => 15,
            'key' => $value['THoDesc2'],
            'align' => 'HORIZONTAL_CENTER',
            'format' => '0',
            'type' => 'string',
        ];
    }
    return $colsExcel ?? [];
}
function minutos_a_horas($min)
{
    if (!is_int($min) || $min < 0) {
        return false;
    }
    if ($min === 0) {
        return '00:00';
    }

    $horas = floor($min / 60);
    $minutos = $min % 60;
    return sprintf('%02d:%02d', $horas, $minutos);
}
function horas_custom($legajosColumn, $payload, $claveCustom)
{
    $lc = $legajosColumn;
    $tot = v1_api('/novedades/totales', 'POST', $payload) ?? []; // Obtener tipos de horas
    $totData = $tot['data'] ?? []; // Obtener datos de horas totales por legajo
    $totColumn = array_column($totData ?? [], null, 'Lega');
    // añadir las horas a legajosColumn con el valor correspondiente según el tipo de hora
    array_walk($totColumn, function ($value, $Lega) use (&$lc, $claveCustom) {
        if (!empty($value['Totales'])) { // Si hay datos en 'Totales'
            foreach ($value['Totales'] as $total) { // Iterar sobre 'Totales'
                $clave = $lc[$Lega][$claveCustom] ?? ''; // Obtener el valor de $claveCustom
                $totResult = $total['EnMinutos'] ?? 0; // Obtener 'EnMinutos'
                if ($clave) { // Si $clave no está vacío
                    $clave += $totResult; // Asignar 'EnHoras' a $claveCustom
                }
            }
            // $lc[$Lega][$claveCustom] = minutos_a_horas($lc[$Lega][$claveCustom]);
        }
    });
    return $lc;
}
