<?php
require __DIR__ . '/../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$checkMethod('POST');

$wc = '';

$dp = ($_REQUEST); // dataPayload
$dp = file_get_contents("php://input");
$dp = json_decode($dp, true);


// if (strlen($dp) > 0 && isValidJSON($dp)) {
//     $dp = json_decode($dp, true);
// } else {
//     isValidJSON($dp);
//     http_response_code(400);
//     (response(array(), 0, 'Invalid json Payload', 400, $time_start, 0, $idCompany));
// }

$start = start();
$length = length();

$dp['Codi'] = ($dp['Codi']) ?? [];
$dp['Codi'] = vp($dp['Codi'], 'Codi', 'intArrayM0', 11);
$dp['ID'] = ($dp['ID']) ?? [];
$dp['ID'] = vp($dp['ID'], 'ID', 'strArray', 3);

$dp['Desc'] = $dp['Desc'] ?? '';
$dp['Desc'] = vp($dp['Desc'], 'Desc', 'str', 30);

$dp['Desc2'] = $dp['Desc2'] ?? '';
$dp['Desc2'] = vp($dp['Desc2'], 'Desc2', 'str', 10);

$arrDPTipoHora = array(
    'Codi' => $dp['Codi'],
    // Codigo de tipo de hora {int} {array}
    'ID' => $dp['ID'],
    // ID de tipo de hora {int} {array}
);
$arrDPSTR = array(
    'Desc' => $dp['Desc'],
    // Descripcion de tipo de hora {str}
    'Desc2' => $dp['Desc2'],
    // Descripcion de tipo de hora 2 {str}
);

foreach ($arrDPTipoHora as $key => $horas) {
    $e = array();
    if (is_array($horas)) {
        $v = '';
        $e = array_filter($horas, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= " AND TIPOHORA.THo$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= " AND TIPOHORA.THo$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= " AND TIPOHORA.THo$key = '$v'";
        }
    }
}
foreach ($arrDPSTR as $key => $v) {

    if (is_array($v)) {
        if ($e = array_filter($v)) {
            if ($e) {
                if (count($e) > 1) {
                    $e = "'" . implode("','", $e) . "'";
                    $wc .= " AND TIPOHORA.$key IN ($e)";
                } else {
                    foreach ($e as $v) {
                        if ($v !== NULL) {
                            $wc .= " AND TIPOHORA.$key = '$v'";
                        }
                    }
                }
            }
        }
    } else {
        if ($v) {
            if ($key == 'Desc' || $key == 'Desc2') {
                $wc .= " AND TIPOHORA.THo$key LIKE '%$v%'";
            } else {
                $wc .= " AND TIPOHORA.THo$key = '$v'";
            }
        }
    }
}

$query = "SELECT THoCodi, THoDesc, THoDesc2, THoID, THoColu, FechaHora FROM TIPOHORA WHERE TIPOHORA.THoCodi > 0";
$queryCount = "SELECT count(1) as 'count' FROM TIPOHORA WHERE TIPOHORA.THoCodi > 0";

if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= " ORDER BY TIPOHORA.THoColu";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

$stmt = $dbApiQuery($query) ?? '';

foreach ($stmt as $key => $v) {
    $data[] = array(
        "Codi" => $v['THoCodi'],
        "Desc" => $v['THoDesc'],
        "Desc2" => $v['THoDesc2'],
        "ID" => $v['THoID'],
        "Colu" => $v['THoColu'],
        "FechaHora" => $v['FechaHora']
    );
}

if (empty($stmt)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
$countData = count($data);
http_response_code(200);
(response($data, $stmtCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
