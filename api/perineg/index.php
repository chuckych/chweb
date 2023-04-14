<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();
$FechaHora = date('Ymd H:i:s');
if ($method == 'POST') {
    require __DIR__ . '/postPerineg.php';
    exit;
}
if ($method == 'PUT') {
    require __DIR__ . '/putPerineg.php';
    exit;
}
if ($method == 'DELETE') {
    require __DIR__ . '/delPerineg.php';
    exit;
}

$wc = '';
$dp = ($request->query); // dataPayload
$start  = start();
$length  = length();

$dp['Lega']  = ($dp['Lega']) ?? [];
$dp['Lega']  = vp($dp['Lega'], 'Lega', 'intArrayM0', 11);

$dp['Caus'] = $dp['Caus'] ?? '';
$dp['Caus'] = vp($dp['Caus'], 'Caus', 'str', 30);

$dp['ApNo'] = $dp['ApNo'] ?? '';
$dp['ApNo'] = vp($dp['ApNo'], 'ApNo', 'str', 30);

$dp['FeIn'] = $dp['FeIn'] ?? '';
$dp['FeIn'] = returnFecha($dp['FeIn'], 'Ymd', false);

$dp['FeEg'] = $dp['FeEg'] ?? '';
$dp['FeEg'] = returnFecha($dp['FeEg'], 'Ymd', false);

$arrDP = array(
    'Lega' => $dp['Lega'], // Codigo de tipo de hora {int} {array}
);

foreach ($arrDP as $key => $p) {
    $e = array();
    if (is_array($p)) {
        $v = '';
        $e = array_filter($p, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= " AND PERINEG.InEg$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= " AND PERINEG.InEg$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= " AND PERINEG.InEg$key = '$v'";
        }
    }
}

$wc .= ($dp['FeIn']) ? " AND PERINEG.InEgFeIn = '$dp[FeIn]'" : '';
$wc .= ($dp['FeEg']) ? " AND PERINEG.InEgFeEg = '$dp[FeEg]'" : '';
$wc .= ($dp['Caus']) ? " AND PERINEG.InEgCaus LIKE '%$dp[Caus]%'" : '';

$query="SELECT * FROM PERINEG WHERE PERINEG.InEgLega > 0";
$queryCount = "SELECT count(1) as 'count' FROM PERINEG WHERE PERINEG.InEgLega > 0";

if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= " ORDER BY PERINEG.InEgLega";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

$stmt = $dbApiQuery($query) ?? '';

foreach ($stmt  as $key => $v) {
    $data[] = array(
        "Lega"      => $v['InEgLega'],
        "FeIn"      => fechFormat($v['InEgFeIn'], 'Y-m-d'),
        "FeEg"      => ($v['InEgFeEg'] == '1753-01-01 00:00:00.000') ? '': fechFormat($v['InEgFeEg']),
        "Caus"      => $v['InEgCaus'],
        "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
    );
}

if (empty($data)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
$countData    = count($data);
http_response_code(200);
(response($data, $stmtCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
