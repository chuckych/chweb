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

$dp['Caus'] = $dp['Caus'] ?? '';
$dp['Caus'] = vp($dp['Caus'], 'Caus', 'str', 30);

$dp['FeIn'] = $dp['FeIn'] ?? '';
$dp['FeIn'] = returnFecha($dp['FeIn'], 'Ymd', false);

if (empty($dp['FeIn'])) {
    http_response_code(400);
    (response("Parámetro 'FeIn' es requerido", 0, "Parámetro 'FeIn' es requerido", 400, timeStart(), 0, 0));
    exit;
}

$dp['FeEg'] = $dp['FeEg'] ?? '';
$dp['FeEg'] = returnFecha($dp['FeEg'], 'Ymd', false);

if (($dp['FeIn'] > date('Ymd'))) {
    http_response_code(400);
    (response('La fecha de ingreso no puede mayor a la actual', 0, "La fecha de ingreso no puede mayor a la actual", 400, timeStart(), 0, 0));
    exit;
}
if (($dp['FeEg'])) {
    if ($dp['FeEg'] < $dp['FeIn']) {
        http_response_code(400);
        (response('La fecha de egreso no puede ser menor a la fecha de ingreso', 0, "La fecha de egreso no puede ser menor a la fecha de ingreso", 400, timeStart(), 0, 0));
        exit;
    }
}


$dp['FeEg'] = $dp['FeEg'] ?? '17530101';

require __DIR__ . '/checkPeriodo.php';

$query = "INSERT INTO PERINEG (InEgLega, InEgFeIn, InEgFeEg, InEgCaus, FechaHora) 
VALUES ($dp[Lega], '$dp[FeIn]', '$dp[FeEg]', '$dp[Caus]', '$FechaHora')";
// print_r($query).exit;
$stmt = $dbApiQuery2($query);

if ($stmt) {
    http_response_code(200);
    (response('Registro creado', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
