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
    // En login_ad no hay sesión completa; recalculamos host con el recid recibido.
    $_SESSION['HOST_CHWEB'] = gethostCHWeb();
}


if (!$_SESSION && !$noValidate) {
    secure_auth_ch_json();
    Flight::jsonHalt(["error" => "Sesión finalizada."]);
}

// sleep(1);
$token = sha1(($_SESSION['RECID_CLIENTE'] ?? ''));

$hostCHWeb = (string) ($_SESSION['HOST_CHWEB'] ?? gethostCHWeb() ?? '');
if ($hostCHWeb === '') {
    $hostCHWeb = host();
}

define('HOSTCHWEB', $hostCHWeb);
$_SESSION['HOST_CHWEB'] = $_SESSION['HOST_CHWEB'] ?? HOSTCHWEB;
define('URLAPI', rtrim(HOSTCHWEB, '/') . "/" . ltrim(HOMEHOST, '/'));

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

function normalize_local_api_arg_to_string($value): string
{
    if (is_string($value) || is_numeric($value)) {
        return trim((string) $value);
    }

    return '';
}

function extract_etiquetas_paragene_from_payload(array $payload): array
{
    $keys = ['EmprSin', 'EmprPlu', 'PlanSin', 'PlanPlu', 'SucuSin', 'SucuPlu', 'GrupSin', 'GrupPlu', 'SectSin', 'SectPlu', 'SeccSin', 'SeccPlu'];
    $source = (isset($payload['Etiquetas']) && is_array($payload['Etiquetas'])) ? $payload['Etiquetas'] : $payload;

    $etiquetas = [];
    $missing = [];
    $filled = 0;

    foreach ($keys as $key) {
        $value = normalize_local_api_arg_to_string($source[$key] ?? '');
        $etiquetas[$key] = $value;

        if ($value === '') {
            $missing[] = $key;
        } else {
            $filled++;
        }
    }

    return [
        'etiquetas' => $etiquetas,
        'missing' => $missing,
        'filled' => $filled,
        'total' => count($keys),
    ];
}

function local_api(string $endpoint, $payload = [], $method = 'GET', $queryParams = [])
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
    $recid = normalize_local_api_arg_to_string($argumento[4] ?? '');

    try {
        if (!$endpoint) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
        }
        if (!preg_match('#^https?://#i', $endpoint)) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . " Endpoint inválido: {$endpoint}");
        }
        return $client->call($endpoint, $payload, $method, $queryParams, $recid);
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
Flight::route('GET /clientes/@id/paragene', function ($id) {
    $queryParams = Flight::request()->query->getData() ?? [];
    $recidCliente = normalize_local_api_arg_to_string($queryParams['recid'] ?? '');

    if ($recidCliente === '') {
        $urlCliente = URLAPI . "/api/_local/clientes?id={$id}";
        $requestCliente = local_api($urlCliente, [], 'GET', []);
        $arrayCliente = is_string($requestCliente) ? json_decode($requestCliente, true) : [];
        $recidCliente = normalize_local_api_arg_to_string($arrayCliente['DATA'][0]['recid'] ?? '');
    }

    if ($recidCliente === '') {
        Flight::json([
            'RESPONSE_CODE' => '400 Bad Request',
            'MESSAGE' => 'No se pudo resolver la cuenta para obtener Etiquetas CH',
            'DATA' => [],
        ], 400);
        return;
    }

    $urlParagene = URLAPI . '/api/v1/parametros/paragene';
    $requestParagene = local_api($urlParagene, [], 'GET', [], $recidCliente);
    $arrayParagene = is_string($requestParagene) ? json_decode($requestParagene, true) : [];

    if (!is_array($arrayParagene)) {
        Flight::json([
            'RESPONSE_CODE' => '500 Internal Server Error',
            'MESSAGE' => 'No se pudo obtener respuesta de Etiquetas CH',
            'DATA' => [],
        ], 500);
        return;
    }

    Flight::json($arrayParagene);
});
Flight::route('PUT /clientes/@id', function ($id) use ($requestData) {
    $url = URLAPI . "/api/_local/clientes/{$id}";
    $request = local_api($url, $requestData, 'PUT', []);
    $arrayData = json_decode($request, true);

    if (!is_array($arrayData)) {
        Flight::json($arrayData);
        return;
    }

    $warningEtiquetas = '';

    if (($arrayData['RESPONSE_CODE'] ?? '') === '200 OK') {
        $etiquetasData = extract_etiquetas_paragene_from_payload($requestData);
        $recidCliente = normalize_local_api_arg_to_string($requestData['Recid'] ?? ($requestData['AppCode'] ?? ''));

        // Solo intentamos sincronizar si se cargaron campos de etiquetas en la edición.
        if (($etiquetasData['filled'] ?? 0) > 0) {
            if ($recidCliente === '') {
                $warningEtiquetas = 'Cuenta actualizada. No se pudo sincronizar Etiquetas CH: recid de cuenta no disponible.';
            } elseif (!empty($etiquetasData['missing'])) {
                $warningEtiquetas = 'Cuenta actualizada. No se sincronizaron Etiquetas CH: faltan campos requeridos.';
            } else {
                $urlParagene = URLAPI . '/api/v1/parametros/paragene';
                $payloadParagene = ['Etiquetas' => $etiquetasData['etiquetas']];
                $requestParagene = local_api($urlParagene, $payloadParagene, 'PUT', [], $recidCliente);
                $arrayParagene = is_string($requestParagene) ? json_decode($requestParagene, true) : [];

                if (($arrayParagene['RESPONSE_CODE'] ?? '') !== '200 OK') {
                    $msgParagene = $arrayParagene['MESSAGE'] ?? 'Error desconocido';
                    $warningEtiquetas = 'Cuenta actualizada. No se pudieron sincronizar Etiquetas CH: ' . $msgParagene;
                }
            }
        }
    }

    if ($warningEtiquetas !== '') {
        $arrayData['WARNING_ETIQUETAS'] = $warningEtiquetas;
    }

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