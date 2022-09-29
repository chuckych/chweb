<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(400);
    (response(array(), 0, 'Invalid Request Method: ' . $_SERVER['REQUEST_METHOD'], 400, $time_start, 0, $idCompany));
    exit;
}

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

$start  = start();
$length = length();

$dp['Codi']  = ($dp['Codi']) ?? [];
$dp['Codi']  = vp($dp['Codi'], 'Codi', 'intArrayM0', 11);
$dp['ID']  = ($dp['ID']) ?? [];
$dp['ID']  = vp($dp['ID'], 'ID', 'strArray', 3);

$dp['Desc'] = $dp['Desc'] ?? '';
$dp['Desc'] = vp($dp['Desc'], 'Desc', 'str', 40);

$arrDPHorarios = array(
    'Codi' => $dp['Codi'], // Codigo de Horario {int} {array}
    'ID'   => $dp['ID'], // ID de Horario {int} {array}
);
$arrDPSTR = array(
    'Desc'  => $dp['Desc'], // Descripcion de Horario {int} {array}
);

foreach ($arrDPHorarios as $key => $Horarios) {
    $e = array();
    if (is_array($Horarios)) {
        $v = '';
        $e = array_filter($Horarios, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= " AND HORARIOS.Hor$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= " AND HORARIOS.Hor$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= " AND HORARIOS.Hor$key = '$v'";
        }
    }
}
foreach ($arrDPSTR as $key => $v) {

    if (is_array($v)) {
        if ($e = array_filter($v)) {
            if ($e) {
                if (count($e) > 1) {
                    $e = "'" . implode("','", $e) . "'";
                    $wc .= " AND HORARIOS.$key IN ($e)";
                } else {
                    foreach ($e as $v) {
                        if ($v !== NULL) {
                            $wc .= " AND HORARIOS.$key = '$v'";
                        }
                    }
                }
            }
        }
    } else {
        if ($v) {
            if ($key == 'HorDesc') {
                $wc .= " AND HORARIOS.Hor$key LIKE '%$v%'";
            } else {
                $wc .= " AND HORARIOS.Hor$key = '$v'";
            }
        }
    }
}

$query = "SELECT * FROM HORARIOS WHERE HORARIOS.HorCodi >= 0";
$queryCount = "SELECT count(1) as 'count' FROM HORARIOS WHERE HORARIOS.HorCodi >= 0";

if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= " ORDER BY HORARIOS.HorCodi";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

$stmt = $dbApiQuery($query) ?? '';

foreach ($stmt  as $key => $v) {
    $data[] = array(
        "Codi"      => $v['HorCodi'],
        "Desc"      => $v['HorDesc'],
        "ID"        => $v['HorID'],
        "Color"     => $v['HorColor'],
        "Domi"      => $v['HorDomi'],
        "Lune"      => $v['HorLune'],
        "Mart"      => $v['HorMart'],
        "Mier"      => $v['HorMier'],
        "Juev"      => $v['HorJuev'],
        "Vier"      => $v['HorVier'],
        "Saba"      => $v['HorSaba'],
        "Feri"      => $v['HorFeri'],
        "DoDe"      => $v['HorDoDe'],
        "LuDe"      => $v['HorLuDe'],
        "MaDe"      => $v['HorMaDe'],
        "MiDe"      => $v['HorMiDe'],
        "JuDe"      => $v['HorJuDe'],
        "ViDe"      => $v['HorViDe'],
        "SaDe"      => $v['HorSaDe'],
        "FeDe"      => $v['HorFeDe'],
        "DoHa"      => $v['HorDoHa'],
        "LuHa"      => $v['HorLuHa'],
        "MaHa"      => $v['HorMaHa'],
        "MiHa"      => $v['HorMiHa'],
        "JuHa"      => $v['HorJuHa'],
        "ViHa"      => $v['HorViHa'],
        "SaHa"      => $v['HorSaHa'],
        "FeHa"      => $v['HorFeHa'],
        "DoRe"      => $v['HorDoRe'],
        "LuRe"      => $v['HorLuRe'],
        "MaRe"      => $v['HorMaRe'],
        "MiRe"      => $v['HorMiRe'],
        "JuRe"      => $v['HorJuRe'],
        "ViRe"      => $v['HorViRe'],
        "SaRe"      => $v['HorSaRe'],
        "FeRe"      => $v['HorFeRe'],
        "DoLi"      => $v['HorDoLi'],
        "LuLi"      => $v['HorLuLi'],
        "MaLi"      => $v['HorMaLi'],
        "MiLi"      => $v['HorMiLi'],
        "JuLi"      => $v['HorJuLi'],
        "ViLi"      => $v['HorViLi'],
        "SaLi"      => $v['HorSaLi'],
        "FeLi"      => $v['HorFeLi'],
        "DoHs"      => $v['HorDoHs'],
        "LuHs"      => $v['HorLuHs'],
        "MaHs"      => $v['HorMaHs'],
        "MiHs"      => $v['HorMiHs'],
        "JuHs"      => $v['HorJuHs'],
        "ViHs"      => $v['HorViHs'],
        "SaHs"      => $v['HorSaHs'],
        "FeHs"      => $v['HorFeHs'],
        "FechaHora" => $v['FechaHora']
    );
}

if (empty($stmt)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
$countData    = count($data);
http_response_code(200);
(response($data, $stmtCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
