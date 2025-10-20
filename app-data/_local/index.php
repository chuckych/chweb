<?php
require __DIR__ . '/../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
header("Content-Type: application/json");
ultimoacc();
$noValidate = false;
$request = Flight::request();
$noValidateSession = ['login_ad'];
$requestedEndpoint = explode('/', trim($request->url, '/')) ?? [];
$requestedEndpoint = $requestedEndpoint[0] ?? '';

if (in_array($requestedEndpoint, $noValidateSession)) {
    $noValidate = true;
    $requestData = $request->data->getData() ?? [];
    $_SESSION['RECID_CLIENTE'] = $requestData['recid_cliente'] ?? '';
}


if (!$_SESSION && !$noValidate) {
    secure_auth_ch_json();
    Flight::json(["error" => "Sesión finalizada."]);
    exit;
}

// sleep(1);
$token = sha1(($_SESSION['RECID_CLIENTE'] ?? ''));

define('HOSTCHWEB', gethostCHWeb());
define('URLAPI', HOSTCHWEB . "/" . HOMEHOST);

function dataSession()
{
    return [
        'session' => [
            'id' => $_SESSION['ID_SESION'] ?? '',
            'recid_c' => $_SESSION['RECID_CLIENTE'] ?? '',
            'usuario' => $_SESSION["user"] ?? 'Sin usuario',
            'usuario_nombre' => $_SESSION['NOMBRE_SESION'] ?? 'Sin nombre',
            'cliente_id' => $_SESSION['ID_CLIENTE'] ?? '',
        ]
    ];
}

$requestData = Flight::request()->data->getData() ?? [];
$requestData = array_merge($requestData, dataSession());

// error_log(print_r(HOSTCHWEB, true));
// error_log(print_r(URLAPI, true));

borrarLogs('json', 1, 'json');
borrarLogs('archivos', 1, 'xls');

function local_api($endpoint, $payload = [], $method = 'GET', $queryParams = [])
{
    timeZone();
    timeZone_lang();


    $argumento = func_get_args(); // Obtengo los argumentos de la función en un array   
    $endpoint = $argumento[0] ?? ''; // Obtengo el endpoint
    $payload = $argumento[1] ?? []; // Obtengo el payload
    $method = $argumento[2] ?? 'GET'; // Obtengo el método
    $queryParams = $argumento[3] ?? []; // Obtengo los parámetro de la query
    $method = strtoupper($method); // Convierto el método a mayúsculas

    try {

        if (!$endpoint) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
        }

        $endpoint = $queryParams ? $endpoint . "?" . http_build_query($queryParams) : $endpoint; // Si hay parámetros de query, los agrego al endpoint

        $ch = curl_init(); // Inicializo curl

        curl_setopt($ch, CURLOPT_URL, $endpoint); // Seteo la url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Seteo el retorno de la respuesta
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Seteo el timeout de la conexión
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seteo el seguimiento de la ubicación
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Seteo la verificación del host (0 = no verificar, 2 = verificar)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Seteo la verificación del peer
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
        $AGENT = $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';

        $headers = [
            "Accept: */*",
            'Content-Type: application/json',
            "Token: {$token}",
            "User-Agent: {$AGENT}",
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Seteo los headers
        $file_contents = curl_exec($ch); // Ejecuto curl
        // file_put_contents(__DIR__ . '/logs/api.log', print_r($file_contents, true) . PHP_EOL, FILE_APPEND); // log error

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

Flight::map('request_get', function ($endpoint) {
    if (!$endpoint) {
        throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
    }
    $url = URLAPI . "/api/_local/{$endpoint}";
    $request = local_api($url, '', 'GET', '');
    $arrayData = json_decode($request, true);
    $result = (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') ? $arrayData['DATA'] : [];
    return $result;
});
Flight::map('request_test_ad', function ($endpoint) {
    if (!$endpoint) {
        throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
    }
    $url = URLAPI . "/api/_local/{$endpoint}";
    $data = Flight::request()->data->getData();
    $request = local_api($url, $data, 'POST', '');
    $arrayData = json_decode($request, true);
    return $arrayData;
});

Flight::route('GET /clientes', function () {
    $queryParams = Flight::request()->query->getData() ?? [];
    $endpoint = $queryParams ? 'clientes?' . http_build_query($queryParams) : 'clientes';
    $clientes = Flight::request_get($endpoint);
    Flight::json($clientes);
});
Flight::route('POST /test_ad', function () {
    $clientes = Flight::request_test_ad('test_ad');
    Flight::json($clientes);
});
Flight::route('POST /login_ad', function () {
    $url = URLAPI . "/api/_local/login_ad";
    $data = Flight::request()->data->getData();
    $request = local_api($url, $data, 'POST', '');
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('POST /usuarios', function () use ($requestData) {
    $url = URLAPI . "/api/_local/usuarios";
    $request = local_api($url, $requestData, 'POST', '');
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('GET /usuarios', function () {
    $url = URLAPI . "/api/_local/usuarios";
    // $data = Flight::request()->data->getData();
    $request = local_api($url, [], 'GET', '');
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('PUT /clientes/@id', function ($id) use ($requestData) {
    $url = URLAPI . "/api/_local/clientes/{$id}";
    $request = local_api($url, $requestData, 'PUT', '');
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('POST /clientes', function () use ($requestData) {
    $url = URLAPI . "/api/_local/clientes/";
    $request = local_api($url, $requestData, 'POST', '');
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::map('Forbidden', function ($mensaje) {
    Flight::json(['status' => 'error', 'message' => $mensaje], 403);
    exit;
});
Flight::map('notFound', function () {
    $request = Flight::request();
    $url = $request->url ?? '';
    $method = $request->method ?? '';
    Flight::json(['status' => 'error', 'message' => "Not found: ({$method}) {$url}"], 404);
    exit;
});
Flight::set('flight.log_errors', true);
Flight::map('error', function ($ex) {
    $code_protected = $ex->getCode() ?? 400;

    switch ($code_protected) {
        case 404:
            Flight::notFound();
            break;
        case 403:
            Flight::Forbidden($ex->getMessage());
            break;
    }

    if ($code_protected == 404) {
        Flight::notFound();
    }
    $text = $ex->getMessage();
    Flight::json(['status' => 'error', 'message' => $text], $code_protected);
});

Flight::start(); // Inicio FlightPHP