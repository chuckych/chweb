<?php
if (!$dataCompany) {
    http_response_code(400);
    (response([], 0, 'No hay datos de cuenta', 400, timeStart(), 0, ''));
    exit;
}
/** CONNECT DATABASE */
$serverName = $dataCompany['host'];
$db         = $dataCompany['db'];
$user       = $dataCompany['user'];
$pass       = $dataCompany['pass'];
$auth       = $dataCompany['auth'];
/** */
if ((empty($db . $user . $pass . $serverName))) { // Si no hay datos de conexion SQL
    http_response_code(400);
    (response([], 0, 'No hay datos de conexion SQL', 400, timeStart(), 0, ''));
    exit;
}

try {
    $conn = new PDO(
        "sqlsrv:server=$serverName;Database=$db;Encrypt=no",
        $user,
        $pass,
        [
            //PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
} catch (PDOException $e) {
    $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorMSQuery.log'; // ruta del archivo de Log de errores
    writeLog(PHP_EOL . 'Message: ' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . PHP_EOL . 'Source: ' . '"' . $_SERVER['REQUEST_URI'] . '"', $pathLog); // escribir en el log de errores el error
    http_response_code(400);
    (response([], 0, $e->getMessage(), 400, timeStart(), 0, ''));
    exit;
}
