<?php
require __DIR__ . '/../../config/session_start.php';
require_once __DIR__ . '/../../vendor/autoload.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
header("Content-Type: application/json");

use App\Http\ChApiClient;
use App\Http\CurlHttpClient;
use App\Http\ApiTokenGenerator;
use App\Http\UrlBuilder;

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
    Flight::jsonHalt(["error" => "Sesión finalizada."]);
}

// sleep(1);
$token = sha1(($_SESSION['RECID_CLIENTE'] ?? ''));

define('HOSTCHWEB', gethostCHWeb());
// define('URLAPI', HOSTCHWEB . "/" . HOMEHOST);
define('URLAPI', api_internal_base_url() . "/" . HOMEHOST);
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

function normalize_local_api_arg_to_array($value): array
{
    if (is_array($value)) {
        return $value;
    }

    if (is_object($value) && method_exists($value, 'getData')) {
        $data = $value->getData();
        if (is_array($data)) {
            return $data;
        }
    }

    if (is_object($value)) {
        return (array) $value;
    }

    return [];
}

function local_api($endpoint, $payload = [], $method = 'GET', $queryParams = [])
{
    static $client = null;

    if ($client === null) {
        // Mantiene timeout de conexión en 10s como en la implementación previa.
        $client = new ChApiClient(
            new CurlHttpClient(10, 60),
            new ApiTokenGenerator(),
            new UrlBuilder()
        );
    }

    $argumento = func_get_args();
    $endpoint = (string) ($argumento[0] ?? '');
    $payload = normalize_local_api_arg_to_array($argumento[1] ?? []);
    $method = strtoupper((string) ($argumento[2] ?? 'GET'));
    $queryParams = normalize_local_api_arg_to_array($argumento[3] ?? []);

    try {
        if (!$endpoint) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
        }
        return $client->call($endpoint, $payload, $method, $queryParams);
    } catch (\Exception $e) {
        error_log('local_api: ' . $e->getMessage());
        return false;
    }
}

Flight::map('request_get', function ($endpoint) {
    if (!$endpoint) {
        throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
    }
    $url = URLAPI . "/api/_local/{$endpoint}";
    $request = local_api($url, [], 'GET', []);
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
    $request = local_api($url, $data, 'POST', []);
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
    $request = local_api($url, $data, 'POST', []);
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('POST /usuarios', function () use ($requestData) {
    $url = URLAPI . "/api/_local/usuarios";
    $request = local_api($url, $requestData, 'POST', []);
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('GET /usuarios', function () {
    $url = URLAPI . "/api/_local/usuarios";
    // $data = Flight::request()->data->getData();
    $request = local_api($url, [], 'GET', []);
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('PUT /clientes/@id', function ($id) use ($requestData) {
    $url = URLAPI . "/api/_local/clientes/{$id}";
    $request = local_api($url, $requestData, 'PUT', []);
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::route('POST /clientes', function () use ($requestData) {
    $url = URLAPI . "/api/_local/clientes/";
    $request = local_api($url, $requestData, 'POST', []);
    $arrayData = json_decode($request, true);
    Flight::json($arrayData);
});
Flight::map('Forbidden', function ($mensaje) {
    Flight::jsonHalt(['status' => 'error', 'message' => $mensaje], 403);
});
Flight::map('notFound', function () {
    $request = Flight::request();
    $url = $request->url ?? '';
    $method = $request->method ?? '';
    Flight::jsonHalt(['status' => 'error', 'message' => "Not found: ({$method}) {$url}"], 404);
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