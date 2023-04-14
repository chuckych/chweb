<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$FechaHora = date('Ymd H:i:s');
$checkMethod('GET');

if ($method == 'POST') {
    // require __DIR__ . '/postEstruct.php';
    exit;
}
if ($method == 'PUT') {
    // require __DIR__ . '/putEstruct.php';
    exit;
}
if ($method == 'DELETE') {
    // require __DIR__ . '/delEstruct.php';
    exit;
}
// Flight::json($request).exit;

$wc = '';
$dp = ($request->query); // dataPayload
$start  = start();
$length  = length();

$dp['Codi']  = ($dp['Codi']) ?? [];
$dp['Codi']  = vp($dp['Codi'], 'Codi', 'intArrayM0', 11);

$dp['Estruct']  = ($dp['Estruct']) ?? [];
$dp['Estruct']  = vp($dp['Estruct'], 'Estruct', 'str', 11);


if (empty($dp['Estruct'])) {
    http_response_code(400);
    (response("Parámetro 'Estruct' es requerido", 0, "Parámetro 'Estruct' es requerido", 400, timeStart(), 0, 0));
    exit;
}

$arrDP = array(
    'Codi' => $dp['Codi'], // Codigo de tipo de hora {int} {array}
);

switch ($dp['Estruct']) {
    case 'Emp':
        $tabla = 'EMPRESAS';
        $pref = 'Emp';
        break;
    case 'Pla':
        $tabla = 'PLANTAS';
        $pref = 'Pla';
        break;
    case 'Sec':
        $tabla = 'SECTORES';
        $pref = 'Sec';
        break;
    case 'Con':
        $tabla = 'CONVENIO';
        $pref = 'Con';
        break;
    case 'Se2':
        $tabla = 'SECCION';
        $pref = 'Se2';
        break;
    case 'Gru':
        $tabla = 'GRUPOS';
        $pref = 'Gru';
        break;
    case 'Suc':
        $tabla = 'SUCURSALES';
        $pref = 'Suc';
        break;
    default:
        http_response_code(400);
        (response("Parámetro 'Estruct' es inválido", 0, "Parámetro 'Estruct' es inválido", 400, timeStart(), 0, 0));
        exit;
        break;
}

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
                $wc .= " AND $tabla.$pref$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= " AND $tabla.$pref$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= " AND $tabla.$pref$key = '$v'";
        }
    }
}

$Codi = $pref.'Codi';
$Desc = ($dp['Estruct'] == 'Emp') ? $pref.'Razon': $pref.'Desc';

$wc .= ($dp['Desc']) ? " AND CONCAT('', $Desc, $Codi) LIKE '%$dp[Desc]%'" : '';

$query="SELECT * FROM $tabla WHERE $Codi > 0";
$queryCount = "SELECT count(1) as 'count' FROM $tabla WHERE $Codi > 0";

if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= " ORDER BY $Codi";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

$stmt = $dbApiQuery($query) ?? '';

foreach ($stmt  as $key => $v) {

    $data[] = array(
        "Codi"      => $v[$Codi],
        "Desc"      => $v[$Desc],
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
