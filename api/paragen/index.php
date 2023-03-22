<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$checkMethod('POST');

$wc = '';

$dp = ($_REQUEST); // dataPayload
$dp = file_get_contents("php://input");

if (strlen($dp) > 0 && isValidJSON($dp)) {
    $dp = json_decode($dp, true);
} else {
    isValidJSON($dp);
    http_response_code(400);
    (response(array(), 0, 'Invalid json Payload', 400, $time_start, 0, $idCompany));
}

$dp['ParCodi']  = ($dp['ParCodi']) ?? '';
$dp['ParCodi']  = vp($dp['ParCodi'], 'ParCodi', 'int', 2); // Traer Solo Fichadas
// CONVERT(VARCHAR(20),FICHAS.FicFech,120) AS 'Fecha',
$query = "SELECT * FROM PARAGENE WHERE PARAGENE.ParCodi = '$dp[ParCodi]'";

$stmt = $dbApiQuery($query) ?? '';


if (empty($stmt)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
$countData    = count($stmt);
http_response_code(200);
(response($stmt, 0, 'OK', 200, $time_start, $countData, $idCompany));
exit;
