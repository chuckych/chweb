<?php
require 'vendor/autoload.php';

$horas       = new Classes\Horas;
$response    = new Classes\Response;
$log         = new Classes\Log;
$dataCompany = new Classes\DataCompany;
$RRHHWebService = new Classes\RRHHWebService;
// $ConnectSqlSrv = new Classes\ConnectSqlSrv;
// $dataCompany->get(); // Obtiene los datos de la empresa y valida el token

$log->delete('log', 1); // Elimina los logs de hace 1 día o más

// Flight::route('GET /info', [$dataCompany, 'get']);
// Flight::route('GET /conn', [$ConnectSqlSrv, 'conn']);
Flight::route('PUT /horas', [$horas, 'update']);
Flight::route('/RRHHWebService', [$RRHHWebService, 'procesar_legajos']);

Flight::map('notFound', [$response, 'notFound']);

Flight::map('error', function ($ex) {
    $log = new Classes\Log;
    $nameLog = date('Ymd') . '_error_.log'; // path Log Api
    if ($ex instanceof Exception) {
        $log->write($ex->getTraceAsString(), $nameLog);
    } elseif ($ex instanceof Error) {
        $log->write($ex->getTraceAsString(), $nameLog);
    }
});

Flight::start();
