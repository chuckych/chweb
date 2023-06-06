<?php
require __DIR__ . '../../fn.php';

Flight::route('POST /interperson', function () {
    echo 'I received a POST request.';
});
Flight::route('GET /get', function () {
    echo 'I received a GET request.';
});

Flight::map('notFound', function () {
    $response = response("asas", 0, "Error", 404, 0, 0, 0);
    Response::json($response, 404);
    exit;
}); // Sends an HTTP 404 response.

Flight::map('error', function (Exception $ex) {
    file_put_contents('error.log', date('Y-m-d H:i:s') . ' ' . $ex->getTraceAsString() . "\n", FILE_APPEND);
}); // Handle error
// Flight::set('flight.log_errors', true);

Flight::start();