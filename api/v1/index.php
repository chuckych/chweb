<?php
require 'vendor/autoload.php';
session_start();

$_SESSION['DataCompany']['idCompany'] = $_SESSION['DataCompany']['idCompany'] ?? 'xxx';
$_SERVER['HTTP_TOKEN'] = $_SERVER['HTTP_TOKEN'] ?? '';

$tools = new Classes\Tools;
define('ID_COMPANY', $tools->padLeft($_SESSION['DataCompany']['idCompany'], 3, 0)); // ID de la empresa

$dataCompany = new Classes\DataCompany;
$dataCompany->checkToken();

$response = new Classes\Response;
$log = new Classes\Log;
$horas = new Classes\Horas;
$novedades = new Classes\Novedades;
$RRHHWebService = new Classes\RRHHWebService;
$ConnectSqlSrv = new Classes\ConnectSqlSrv;
$ParaGene = new Classes\ParaGene;

use flight\Engine;

$api = new Engine();

$log->delete('log', 2); // Elimina los logs de hace 1 día o más

$api->route('PUT /novedades', [$novedades, 'update']);
$api->route('DELETE /novedades', [$novedades, 'delete']);
$api->route('PUT /horas', [$horas, 'update']);
$api->route('POST /horas/estruct/@estruct', [$horas, 'estruct']);
$api->route('POST /novedades/estruct/@estruct', [$novedades, 'estruct']);
$api->route('GET /novedades/data', [$novedades, 'data']);
$api->route('/paragene', [$ParaGene, 'get']);

$api->map('notFound', [$response, 'notFound']);

$api->map('error', function ($ex) {
    $log = new Classes\Log;
    $nameLog = date('Ymd') . '_error_.log'; // path Log Api
    if ($ex instanceof Exception) {
        $log->write($ex->getTraceAsString(), $nameLog);
    } elseif ($ex instanceof Error) {
        $log->write($ex->getTraceAsString(), $nameLog);
    } elseif ($ex instanceof PDOException) {
        $log->write($ex->getTraceAsString(), $nameLog);
    }
});

$api->start();
