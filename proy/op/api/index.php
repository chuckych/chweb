<?php
require __DIR__ . '/../../../vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');
// use flight\Engine;
$request = Flight::request();

Flight::route('GET /conf-proy', function () {
    require_once __DIR__ . '/scripts/get-conf-proy.php';
});

Flight::route('POST /conf-proy', function () use ($request) {
    require_once __DIR__ . '/scripts/post-conf-proy.php';
});

Flight::route('GET /import-proy', function () {
    require_once __DIR__ . '/scripts/import-conf-proy.php';
});

Flight::map('conf_proy', function () {
    require_once __DIR__ . '/scripts/get-conf-proy.php';
});

Flight::map('notFound', function () {
    return Flight::json(
        [
            'status' => 404,
            'error' => 'Not found'
        ]
    );
});
Flight::map('Forbidden', function ($mensaje) {
    return Flight::json(
        [
            'status' => 403,
            'error' => $mensaje
        ]
    );
});
Flight::map('Respuesta', function ($mensaje, $code = 200) {
    http_response_code($code);
    return Flight::json(
        [
            'status' => $code,
            'message' => $mensaje
        ]
    );
});

Flight::route('GET /health', function () {
    return Flight::json(
        [
            'status' => 200,
            'message' => 'API is healthy'
        ]
    );
});

Flight::start();