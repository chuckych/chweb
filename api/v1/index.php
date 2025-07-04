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
$response = new Classes\Response;
$log = new Classes\Log;
$horas = new Classes\Horas;
$fichas = new Classes\Fichas;
$estructuras = new Classes\Estructuras;
$horarios = new Classes\Horarios;
$auditor = new Classes\Auditor;
$novedades = new Classes\Novedades;
$RRHHWebService = new Classes\RRHHWebService;
$connectSqlSrv = new Classes\ConnectSqlSrv;
$ParaGene = new Classes\ParaGene;
$Personal = new Classes\Personal;
$Fichadas = new Classes\Fichadas;

define('ID_COMPANY', $tools->padLeft(getenv('ID_COMPANY'), 3, 0)); // ID de la empresa con formato

$log->delete('log', 2); // Elimina los logs de hace 1 día o más

Flight::route('GET /system/data', function () use ($envData, $response, $ParaGene) {
    $inicio = microtime(true);

    $dbData = $ParaGene->dbData(true);

    $data = [
        'cuenta' => $envData['nameCompany'] ?? '',
        'dbname' => $envData['DBName'] ?? '',
        'dbver' => $dbData['BDVersion'] ?? '',
        'chver' => $dbData['SystemVer'] ?? '',
        'token' => $envData['Token'] ?? '',
    ];
    $response->respuesta($data, 0, '', 200, $inicio, 0, ID_COMPANY);
});


Flight::route('PUT /novedades', [$novedades, 'update']);
Flight::route('DELETE /novedades', [$novedades, 'delete']);
Flight::route('POST /novedades', [$novedades, 'add']);
Flight::route('POST /novedades/totales', [$novedades, 'totales']);
Flight::route('POST /horas/totales', [$horas, 'totales']);
Flight::route('PUT /horas', [$horas, 'update']);
Flight::route('GET /horas/dateMinMax', [$horas, 'dateMinMax']);
Flight::route('GET /horas/data', [$horas, 'data']);
Flight::route('GET /fichas/dateMinMax', [$fichas, 'dateMinMax']);
Flight::route('POST /fichas/legajos', [$fichas, 'legajos']);
Flight::route('POST /horas/estruct/@estruct', [$horas, 'estruct']);
Flight::route('POST /novedades/estruct/@estruct', [$novedades, 'estruct']);
Flight::route('POST /estructuras/', [$estructuras, 'estructuras']);
Flight::route('POST /estructuras/alta', [$estructuras, 'create']);
Flight::route('GET /novedades/data', [$novedades, 'data']);
Flight::route('/paragene', [$ParaGene, 'get']);
Flight::route('GET /parametros/paragene', [$ParaGene, 'get']);
Flight::route('GET /parametros/dbdata', [$ParaGene, 'dbData']);
Flight::route('GET /parametros/liquid', [$ParaGene, 'liquid']);
Flight::route('GET /horarios/', [$horarios, 'get_horarios']);
Flight::route('POST /proyectar', function () use ($response, $RRHHWebService) {
    $request = Flight::request();
    $data = $request->data->getData();
    $inicio = microtime(true);

    $proyectar = $RRHHWebService->proyectar_horas(
        $data['Legajos'],
        $data['FechaDesde'],
        $data['FechaHasta']
    );

    $response->respuesta($proyectar, 0, '', 200, $inicio, 0, ID_COMPANY);
});
Flight::route('DELETE /proyectar', [$horas, 'eliminar_proyeccion']);

Flight::route('GET /horarios/rotacion', [$horarios, 'get_rotaciones']);
Flight::route('GET /horarios/asign/desde-hasta/(@Legajo)', [$horarios, 'get_horale_2']);
Flight::route('GET /horarios/asign/desde/(@Legajo)', [$horarios, 'get_horale_1']);
Flight::route('GET /horarios/asign/rotacion/(@Legajo)', [$horarios, 'get_rotaleg']);
Flight::route('GET /horarios/asign/citacion/(@Legajo)', [$horarios, 'get_citacion']);
Flight::route('GET /horarios/asign/legajo/(@Legajo)', [$horarios, 'get_asign_legajo']);
Flight::route('POST /horarios/rotacion', [$horarios, 'set_rotacion']);
Flight::route('POST /horarios/legajo-rotacion', [$horarios, 'set_rotacion']);
Flight::route('POST /horarios/desde', [$horarios, 'set_horario']);
Flight::route('POST /horarios/asignados', [$horarios, 'obtener_horarios']); // para horarios asignados
// Flight::route('POST /horarios/sp', [$horarios, 'create_sp_horarios']); // para horarios asignados
// Flight::route('DELETE /horarios/sp', [$horarios, 'drop_sp_horarios']); // para horarios asignados
Flight::route('DELETE /horarios/desde', [$horarios, 'delete_horario']);
Flight::route('DELETE /horarios/legajo-desde', [$horarios, 'delete_horario']);
Flight::route('DELETE /horarios/desde-hasta', [$horarios, 'delete_horario']);
Flight::route('DELETE /horarios/legajo-desde-hasta', [$horarios, 'delete_horario']);
Flight::route('DELETE /horarios/citacion', [$horarios, 'delete_horario']);
Flight::route('DELETE /horarios/legajo-citacion', [$horarios, 'delete_horario']);
Flight::route('DELETE /horarios/rotacion', [$horarios, 'delete_horario']);
Flight::route('DELETE /horarios/legajo-rotacion', [$horarios, 'delete_horario']);
Flight::route('POST /horarios/legajo-desde', [$horarios, 'set_horario']);
Flight::route('POST /horarios/desde-hasta', [$horarios, 'set_horario']);
Flight::route('POST /horarios/legajo-desde-hasta', [$horarios, 'set_horario']);
Flight::route('POST /horarios/citacion', [$horarios, 'set_horario']);
Flight::route('POST /horarios/legajo-citacion', [$horarios, 'set_horario']);
Flight::route('POST /auditor', [$auditor, 'add']);
Flight::route('GET /personal/legajos', [$Personal, 'legajos']);
Flight::route('POST /personal/filtros', [$Personal, 'filtros_estructura']);
Flight::route('POST /fichadas', [$Fichadas, 'create']);
// Flight::route('POST /conectar', [$connectSqlSrv, 'test_connect']);
Flight::route('POST /conectar', function () {
    $connectSqlSrv = new Classes\ConnectSqlSrv;
    return $connectSqlSrv->test_connect();
});
Flight::map('notFound', [$response, 'notFound']);
Flight::set('flight.log_errors', true);

Flight::map('Forbidden', function ($mensaje) use ($response) {
    $inicio = microtime(true);
    $response->respuesta('', 0, $mensaje, 403, $inicio, 0, 0);
    exit;
});

Flight::map('error', function ($ex) use ($response, $log) {

    $code_protected = $ex->getCode() ?? 400;
    $error_message = $ex->getMessage() ?? 'Error desconocido';

    switch ($code_protected) {
        case 404:
            Flight::notFound();
            break;
        case 403:
            Flight::Forbidden($ex->getMessage());
            break;
        case 1:
            $error_message = 'Error interno';
            break;
    }

    $inicio = microtime(true);
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

Flight::start();
