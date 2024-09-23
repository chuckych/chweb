<?php
require 'vendor/autoload.php';

$_SERVER['HTTP_TOKEN'] ?? ''; // Token de la petición

$inicio = microtime(true); // Tiempo de inicio del proceso

$dataCompany = new Classes\DataCompany; // Instancia de la clase DataCompany

$dataCompany->checkToken(); // Verifica el token
$envData = $dataCompany->get(); // Obtiene la información de la empresa

$idCompany = $envData['idCompany'] ?? 'xxx'; // ID de la empresa

$arrEnv = [
    'DB_HOST' => $envData['DBHost'] ?? '',
    'DB_USER' => $envData['DBUser'] ?? '',
    'DB_PASS' => $envData['DBPass'] ?? '',
    'DB_NAME' => $envData['DBName'] ?? '',
    'DB_AUTH' => $envData['DBAuth'] ?? '',
    'WEBSERVICE' => $envData['WebServiceCH'] ?? '',
    'ID_COMPANY' => $idCompany,
];

foreach ($arrEnv as $key => $value) {
    putenv("$key=$value");
}

$tools = new Classes\Tools; // Instancia de la clase Tools
$response       = new Classes\Response;
$log            = new Classes\Log;
$horas          = new Classes\Horas;
$fichas         = new Classes\Fichas;
$estructuras    = new Classes\Estructuras;
$horarios       = new Classes\Horarios;
$auditor        = new Classes\Auditor;
$novedades      = new Classes\Novedades;
$RRHHWebService = new Classes\RRHHWebService;
$connectSqlSrv  = new Classes\ConnectSqlSrv;
$ParaGene       = new Classes\ParaGene;
$Personal       = new Classes\Personal;

define('ID_COMPANY', $tools->padLeft(getenv('ID_COMPANY'), 3, 0)); // ID de la empresa con formato

use flight\Engine;

$api  = new Engine();

$log->delete('log', 2); // Elimina los logs de hace 1 día o más

$api->route('PUT /novedades', [$novedades, 'update']);
$api->route('DELETE /novedades', [$novedades, 'delete']);
$api->route('POST /novedades', [$novedades, 'add']);
$api->route('POST /novedades/totales', [$novedades, 'totales']);
$api->route('POST /horas/totales', [$horas, 'totales']);
$api->route('PUT /horas', [$horas, 'update']);
$api->route('GET /horas/dateMinMax', [$horas, 'dateMinMax']);
$api->route('GET /fichas/dateMinMax', [$fichas, 'dateMinMax']);
$api->route('POST /horas/estruct/@estruct', [$horas, 'estruct']);
$api->route('POST /novedades/estruct/@estruct', [$novedades, 'estruct']);
$api->route('POST /estructuras/', [$estructuras, 'estructuras']);
$api->route('POST /estructuras/alta', [$estructuras, 'create']);
$api->route('GET /novedades/data', [$novedades, 'data']);
$api->route('/paragene', [$ParaGene, 'get']);
$api->route('GET /parametros/paragene', [$ParaGene, 'get']);
$api->route('GET /parametros/dbdata', [$ParaGene, 'dbData']);
$api->route('GET /horarios/', [$horarios, 'get_horarios']);
$api->route('GET /horarios/rotacion', [$horarios, 'get_rotaciones']);
$api->route('GET /horarios/asign/desde-hasta/(@Legajo)', [$horarios, 'get_horale_2']);
$api->route('GET /horarios/asign/desde/(@Legajo)', [$horarios, 'get_horale_1']);
$api->route('GET /horarios/asign/rotacion/(@Legajo)', [$horarios, 'get_rotaleg']);
$api->route('GET /horarios/asign/citacion/(@Legajo)', [$horarios, 'get_citacion']);
$api->route('GET /horarios/asign/legajo/(@Legajo)', [$horarios, 'get_asign_legajo']);
$api->route('POST /horarios/rotacion', [$horarios, 'set_rotacion']);
$api->route('POST /horarios/legajo-rotacion', [$horarios, 'set_rotacion']);
$api->route('POST /horarios/desde', [$horarios, 'set_horario']);
$api->route('DELETE /horarios/desde', [$horarios, 'delete_horario']);
$api->route('DELETE /horarios/legajo-desde', [$horarios, 'delete_horario']);
$api->route('DELETE /horarios/desde-hasta', [$horarios, 'delete_horario']);
$api->route('DELETE /horarios/legajo-desde-hasta', [$horarios, 'delete_horario']);
$api->route('DELETE /horarios/citacion', [$horarios, 'delete_horario']);
$api->route('DELETE /horarios/legajo-citacion', [$horarios, 'delete_horario']);
$api->route('DELETE /horarios/rotacion', [$horarios, 'delete_horario']);
$api->route('DELETE /horarios/legajo-rotacion', [$horarios, 'delete_horario']);
$api->route('POST /horarios/legajo-desde', [$horarios, 'set_horario']);
$api->route('POST /horarios/desde-hasta', [$horarios, 'set_horario']);
$api->route('POST /horarios/legajo-desde-hasta', [$horarios, 'set_horario']);
$api->route('POST /horarios/citacion', [$horarios, 'set_horario']);
$api->route('POST /horarios/legajo-citacion', [$horarios, 'set_horario']);
$api->route('POST /auditor', [$auditor, 'add']);
$api->route('GET /personal/legajos', [$Personal, 'legajos']);
$api->route('POST /conectar', [$connectSqlSrv, 'test_connect']);
$api->map('notFound', [$response, 'notFound']);
$api->set('flight.log_errors', true);

$api->map('Forbidden', function ($mensaje) use ($response) {
    $inicio = microtime(true);
    $response->respuesta('', 0, $mensaje, 403, $inicio, 0, 0);
    exit;
});

$api->map('error', function ($ex) use ($api, $response, $log) {

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

    if ($ex instanceof Exception) {
        $log->write($error_message, $nameLog);
    } elseif ($ex instanceof Error) {
        $log->write($error_message, $nameLog);
    } elseif ($ex instanceof PDOException) {
        $log->write($error_message, $nameLog);
    }
    $company = getenv('ID_COMPANY') !== false ? getenv('ID_COMPANY') : '';
    $response->respuesta('', 0, $error_message, $code_protected, $inicio, 0, $company);
});

$api->start();
