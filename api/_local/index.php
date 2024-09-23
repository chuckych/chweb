<?php

require __DIR__ . '../../../vendor/autoload.php';

foreach (glob(__DIR__ . '/Classes/*.php') as $filename) {
    require $filename;
}

$_SERVER['HTTP_TOKEN'] ?? ''; // Token de la petición
$inicio = microtime(true); // Tiempo de inicio del proceso

$env = new Classes\Env; // Instancia de la clase Env

foreach ($env->get() as $key => $value) {
    putenv("$key=$value");
}
define('PATH_LOG', __DIR__ . '/logs/'); // Path Log Api
// $host es la primer parte de la url, ejemplo: https://api.chweb.com.ar
$base = Flight::request()->base; // Nombre del directorio actual
$base = explode('/', $base); // Divide el nombre del directorio en un array
define('HOMEHOST', $base[1] ?? 'chweb'); // Nombre del directorio actual
define('PATH_APIKEY', '../../mobileApikey.php'); // Path Apikey

$response = new Classes\Response;
$clientes = new Classes\Clientes; // Instancia de la clase Clientes
use flight\Engine;

$api  = new Engine();

// Flight::json($conectar->getConn());

$api->route('GET /clientes', [$clientes, 'get_clientes']); // Obtiene los clientes
$api->route('POST /clientes', [$clientes, 'alta_cliente']); // Obtiene los clientes
$api->route('PUT /clientes/@IDCliente', [$clientes, 'edita_cliente']); // Obtiene los clientes

$api->map('notFound', [$response, 'notFound']);
$api->map('Forbidden', function ($mensaje) use ($response) {
    $inicio = microtime(true);
    $response->respuesta('', 0, $mensaje, 403, $inicio, 0, 0);
    exit;
});

$api->map('error', function ($ex) use ($api, $response) {

    $code_protected = $ex->getCode() ?? 400;
    $error_message  = $ex->getMessage() ?? 'Error desconocido';

    switch ($code_protected) {
        case 404:
            $api->notFound();
            break;
        case 403:
            $api->Forbidden($ex->getMessage());
            break;
        case 1:
            $error_message = 'Error interno';
            break;
    }

    $inicio  = microtime(true);
    $nameLog = date('Ymd') . '_error_.log'; // path Log Api
    $nameInstance = get_class($ex);

    if ($ex instanceof Exception) {
        // $log->write($error_message, $nameLog);
    } elseif ($ex instanceof Error) {
        // $log->write($error_message, $nameLog);
    } elseif ($ex instanceof PDOException) {
        // $log->write($error_message, $nameLog);
    }

    switch ($nameInstance) {
        case 'PDOException':
            // $error_message = 'Error en la conexión a la base de datos';
            break;
        case 'Exception':
            // $error_message = 'Error interno';
            break;
        case 'Error':
            // $error_message = 'Error en la conexión a la base de datos';
            break;
        case 'Classes\\ValidationException':
            $nameInstance = '';
            break;
    }
    $nameInstance = $nameInstance !== '' ? "{$nameInstance}: " : '';

    $company = getenv('ID_COMPANY') !== false ? getenv('ID_COMPANY') : '';
    $response->respuesta([], 0,  "{$nameInstance}{$error_message}", $code_protected, $inicio, 0, $company);
});

$api->start();
