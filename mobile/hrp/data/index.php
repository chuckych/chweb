<?php
require __DIR__ . '../../../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../../config/index.php';

ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");

if (!$_SESSION) {
    Flight::json(array("error" => "Sesión finalizada."));
    exit;
}
// sleep(1);

$token = sha1($_SESSION['RECID_CLIENTE']);
$idCliente = $_SESSION['ID_CLIENTE'] ?? '';


// borrarLogs('json', 1, 'json');

/**
 * Realiza una llamada a API.
 *
 * @param string $endpoint El endpoint de la API.
 * @param array $payload El payload de la solicitud (opcional).
 * @param string $method El método de la solicitud (opcional, por defecto es GET).
 * @param array $queryParams Los parámetros de la consulta (opcional).
 * @return mixed Los datos devueltos por la API o false si hay un error.
 * @throws Exception Si ocurre un error durante la llamada a la API.
 */
function call_api()
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
            throw new Exception('call_api: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
        }

        $endpoint = ($queryParams) ? $endpoint . "?" . http_build_query($queryParams) : $endpoint; // Si hay parámetros de query, los agrego al endpoint

        // print_r($queryParams) . exit;

        $ch = curl_init(); // Inicializo curl

        curl_setopt($ch, CURLOPT_URL, $endpoint); // Seteo la url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Seteo el retorno de la respuesta
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Seteo el timeout de la conexión
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // Seteo la verificación del host
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Seteo la verificación del peer

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }
        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        if ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }
        if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }

        $headers = array(
            "Accept: application/json, text/plain, */*",
            "Accept-Encoding: gzip, deflate, br",
            "Accept-Language: es-AR,es;q=0.8,en-US;q=0.5,en;q=0.3",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "DNT: 1",
            "Pragma: no-cache",
            "Referer: https://www.google.com/",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3",
            "Sec-Fetch-Dest: empty",
            "Sec-Fetch-Mode: cors",
            "Sec-Fetch-Site: cross-site",
            "Sec-GPC: 1",
            "content-type: application/json; charset=utf-8"
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Seteo los headers

        $file_contents = curl_exec($ch); // Ejecuto curl

        $curl_errno = curl_errno($ch); // get error code
        $curl_error = curl_error($ch); // get error information

        if ($curl_errno > 0) { // si hay error
            $text = "cURL Error ($curl_errno): $curl_error"; // set error message
            throw new Exception($text);
        }
        if (!$file_contents) {
            throw new Exception('call_api: ' . date('Y-m-d H:i:s') . ' Error al obtener datos');
        }
        curl_close($ch);
        $text = 'call_api: ' . date('Y-m-d H:i:s') . ' ' . json_encode($file_contents);
        return $file_contents;
    } catch (\Exception $e) {
        curl_close($ch);
        return false;
    }
}

function get_data($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);

    $output = curl_exec($ch);

    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno) : $curl_error $url"; // set error message
        $pathLog = date('Ymd') . '_get_data.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return $output;
}

Flight::route('GET /position_data/@lat/@lng', function ($lat, $lng) {

    try {
        if (!$lat || !$lng) {
            throw new Exception('No se han recibido los datos necesarios');
        }

        $position_data = array();

        $idCliente = str_pad($_SESSION['ID_CLIENTE'], 2, '0', STR_PAD_LEFT) ?? '';

        $pathFile = __DIR__ . '/archivos/position_data_' . $idCliente . '.json';

        try {
            $position_data = file_get_contents($pathFile);
        } catch (\Throwable $th) {
            // crear el archivo si no existe
            $file = fopen($pathFile, 'w');
            fwrite($file, json_encode($position_data, JSON_PRETTY_PRINT));
            fclose($file);
        }
        $position_data = file_get_contents($pathFile);
        if ($position_data) {
            // buscar en el archivo json si ya existe el registro. Si existe no lo agrega
            $position_data = json_decode($position_data, true);
            $pos = $lat . ',' . $lng;
            foreach ($position_data as $key => $value) {
                if ($value['pos'] == $pos) {
                    Flight::json(
                        array(
                            "status" => "success",
                            "data" => $value['name']
                        )
                    );
                    exit;
                }
            }
        }

        $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=$lat&lon=$lng";

        $data = call_api($url);

        if (!$data) {
            throw new Exception('No se han podido obtener los datos');
        }


        $data = json_decode($data);
        $display_name = ($data->display_name ?? '');

        $arrayDatos = [];

        $arrayDatos['name'] = $display_name;
        $arrayDatos['pos'] = $lat . ',' . $lng;

        $position_data[] = $arrayDatos;

        $file = fopen($pathFile, 'w');
        fwrite($file, json_encode($position_data, JSON_PRETTY_PRINT));
        fclose($file);

        Flight::json(
            array(
                "status" => "success",
                "data" => $display_name
            )
        );
    } catch (\Throwable $th) {
        Flight::json(
            array(
                "status" => "error",
                "error" => $th->getMessage()
            )
        );
    }
});

Flight::route('GET /devices', function () { // Ruta para obtener los dispositivos de la empresa (cache)

    $id = $_SESSION['ID_CLIENTE'] ?? ''; // ID de la empresa
    $recid = $_SESSION['RECID_CLIENTE'] ?? ''; // ID de la empresa
    $baseDevices = $_SESSION["APIMOBILEHRP"] . "/chweb/mobile/hrp/api/v1/devices"; // URL base de la API
    $endpointSetCache = "$baseDevices/cache.php?key=$recid"; // Endpoint para actualizar la cache
    $endpointGetCache = "$baseDevices/cache_$recid.txt"; // Endpoint para obtener la cache
    $output = get_data($endpointGetCache); // Obtener la cache

    if (!$output) { // Si no hay cache
        actualizar_cache($endpointSetCache); // Actualizar la cache
        $output = get_data($endpointGetCache); // Obtener la cache
    }

    $data = ($output) ? (unserialize(stripcslashes($output))) : ''; // Deserializer la cache
    Flight::json(array('data' => $data ?? '')); // Responder con la cache
});

Flight::map('notFound', function () {
    Flight::json(array('status' => 'error', 'message' => 'Not found'), 404);
});
Flight::set('flight.log_errors', true);

Flight::map('error', function ($ex) {
    Flight::json(array('status' => 'error', 'message' => $ex->getMessage()), 400);
});

Flight::start(); // Inicio FlightPHP