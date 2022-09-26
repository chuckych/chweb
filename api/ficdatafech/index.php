<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$iLega2 = $total = $FicCountSelect = $joinFichas4 = $joinFichas3 = $joinFichas2 = $joinFichas1 = $onlyRegCount = '';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(400);
    (response(array(), 0, 'Invalid Request Method: ' . $_SERVER['REQUEST_METHOD'], 400, $time_start, 0, $idCompany));
    exit;
}

require __DIR__ . '../wc.php';

if ($dp['getReg']) {
    $joinFichas4 = " LEFT JOIN REGISTRO ON FICHAS.FicLega = REGISTRO.RegLega AND FICHAS.FicFech = REGISTRO.RegFeAs ";

    if ($dp['onlyReg']) {
        $joinFichas4 = " INNER JOIN REGISTRO ON FICHAS.FicLega = REGISTRO.RegLega AND FICHAS.FicFech = REGISTRO.RegFeAs ";
    }
    
}
if ($dp['getNov']) {
    $joinFichas3 = " INNER JOIN FICHAS3 ON FICHAS.FicLega = FICHAS3.FicLega AND FICHAS.FicFech = FICHAS3.FicFech ";
}
if ($dp['getONov']) {
    $joinFichas2 = " INNER JOIN FICHAS2 ON FICHAS.FicLega = FICHAS2.FicLega AND FICHAS.FicFech = FICHAS2.FicFech ";
}
if ($dp['getHor']) {
    $joinFichas1 = " INNER JOIN FICHAS1 ON FICHAS.FicLega = FICHAS1.FicLega AND FICHAS.FicFech = FICHAS1.FicFech ";
}
// CONVERT(VARCHAR(20),FICHAS.FicFech,120) AS 'Fecha',
$qFic = "SELECT FICHAS.FicFech FROM FICHAS 
$joinFichas3  -- Join Novedades
$joinFichas2  -- Join otras Novedades
$joinFichas1  -- Join Horas 
$joinFichas4  -- Join Registros 
WHERE FICHAS.FicLega > 0 $wcFicFech ";

$qFicCount = "SELECT count(DISTINCT FICHAS.FicFech) as 'count' FROM 
FICHAS $joinFichas3 $joinFichas2 $joinFichas1 $joinFichas4 WHERE FICHAS.FicLega > 0 $wcFicFech";

if ($wc) {
    $qFic .= $wc;
    $qFicCount .= $wc;
}

// print_r($qFicCount).exit;

$stmtFicCount = $dbApiQuery($qFicCount)[0]['count'] ?? '';
$qFic .= " GROUP BY FICHAS.FicFech ORDER BY FICHAS.FicFech";
$qFic .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
// print_r($qFicCount).exit;
// print_r($qFic).exit;
$stmtFic = $dbApiQuery($qFic) ?? '';


if (empty($stmtFic)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}

foreach ($stmtFic as $key => $v) {

    $data[] = array(
        'Fecha'     => $v['FicFech'],
    );
}
$countData    = count($data);
http_response_code(200);
(response($data, $stmtFicCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
