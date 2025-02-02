<?php

require __DIR__ . '../../../vendor/autoload.php';

foreach (glob(__DIR__ . '/Classes/*.php') as $filename) { // Incluye las clases
    require $filename;
}

$HTTP_TOKEN = $_SERVER['HTTP_TOKEN'] ?? ''; // Token de la petición
$inicio = microtime(true); // Tiempo de inicio del proceso

$env = new Classes\Env; // Instancia de la clase Env

function getIni($url) // obtiene el json de la url
{
    if (!file_exists($url)) { // Si no existe el archivo
        writeLog("No existe archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getIni.log", ''); // escribimos en el log
        return false; // devolvemos false
    }
    $data = file_get_contents($url); // obtenemos el contenido del archivo
    if (!$data) { // si el contenido está vacío
        writeLog("No hay informacion en el archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getIni.log", ''); // escribimos en el log
        return false; // devolvemos false
    }
    $data = parse_ini_file($url, true); // Obtenemos los datos del data.php
    return $data ?? []; // devolvemos el json
}
foreach ($env->get() as $key => $value) {
    putenv("$key=$value");
}
define('PATH_LOG', __DIR__ . '/logs/'); // Path Log Api
// $host es la primer parte de la url, ejemplo: https://api.chweb.com.ar
$base = Flight::request()->base; // Nombre del directorio actual
$base = explode('/', $base); // Divide el nombre del directorio en un array
define('HOMEHOST', $base[1] ?? 'chweb'); // Nombre del directorio actual
define('PATH_APIKEY', '../../mobileApikey.php'); // Path Apikey

use flight\Engine;
$api = new Engine();
$response = new Classes\Response;

$api->map('notFound', [$response, 'notFound']);
$api->map('Forbidden', function ($mensaje) use ($response) {
    $inicio = microtime(true);
    $response->respuesta('', 0, $mensaje, 403, $inicio, 0, 0);
    exit;
});

$api->map('error', function ($ex) use ($api, $response) {

    $code_protected = $ex->getCode() ?? 400;
    $error_message = $ex->getMessage() ?? 'Error desconocido';

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

    $inicio = microtime(true);
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
    $response->respuesta([], 0, "{$nameInstance}{$error_message}", $code_protected, $inicio, 0, $company);
});

$iniData = getIni(PATH_APIKEY) ?? []; // Obtiene los datos del archivo de configuración de la api

if (isset($iniData[0]['Token'])) { // Si el token está definido
    $iniData = array_column($iniData, null, 'Token'); // indexa el array por el token
    $validToken = $iniData[$HTTP_TOKEN] ?? $api->Forbidden('Token inválido'); // Si el token no es válido, devuelve un error
} else {
    $api->Forbidden('Token inválido');
}

$clientes = new Classes\Clientes; // Instancia de la clase Clientes
$params = new Classes\Params; // Instancia de la clase Params

$api->route('GET /clientes', [$clientes, 'get_clientes']); // Obtiene los clientes
$api->route('POST /clientes', [$clientes, 'alta_cliente']); // Obtiene los clientes
$api->route('PUT /clientes/@IDCliente', [$clientes, 'edita_cliente']); // Obtiene los clientes
$api->route('GET /params', [$params, 'get']); // Obtiene los params
$api->route('POST /params', [$params, 'alta_multiple']); // alta de params
$api->route('DELETE /params', [$params, 'delete']); // Eliminar params

$api->start();
