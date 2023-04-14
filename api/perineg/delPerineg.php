<?php

$wc = '';
$dp = ($request->data); // dataPayload

// Flight::json($dp).exit;

$dp['Lega']  = ($dp['Lega']) ?? '';
$dp['Lega']  = vp($dp['Lega'], 'Lega', 'int', 11);

if (empty($dp['Lega'])) {
    http_response_code(400);
    (response("Parámetro 'Lega' es requerido", 0, "Parámetro 'Lega' es requerido", 400, timeStart(), 0, 0));
    exit;
}

$dp['FeIn'] = $dp['FeIn'] ?? '';
$dp['FeIn'] = returnFecha($dp['FeIn'], 'Ymd', false);

if (empty($dp['FeIn'])) {
    http_response_code(400);
    (response("Parámetro 'FeIn' es requerido", 0, "Parámetro 'FeIn' es requerido", 400, timeStart(), 0, 0));
    exit;
}

$query = "DELETE FROM PERINEG WHERE InEgLega = $dp[Lega] AND InEgFeIn = '$dp[FeIn]'";

$stmt = $dbApiQuery2($query);

if ($stmt) {
    http_response_code(200);
    (response('Registro eliminado', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
