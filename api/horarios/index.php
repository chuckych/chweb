<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();
$request = Flight::request();

$checkMethod('POST');

$wc = '';

$dp = $request->data;

$start  = start();
$length = length();

$dp->Codi  = ($dp->Codi) ?? [];
$dp->Codi  = vp($dp->Codi, 'Codi', 'intArrayM0', 11);

$dp->ID  = ($dp->ID) ?? [];
$dp->ID  = vp($dp->ID, 'ID', 'strArray', 3);

$dp->Desc = $dp->Desc ?? '';
$dp->Desc = vp($dp->Desc, 'Desc', 'str', 40);

$arrDPHorarios = array(
    'Codi' => $dp->Codi, // Codigo de Horario {int} {array}
    'ID'   => $dp->ID, // ID de Horario {int} {array}
);
$arrDPSTR = array(
    'Desc'  => $dp->Desc, // Descripcion de Horario {string}
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
                    $wc .= " AND HORARIOS.$key LIKE '%$e%'";
                } else {
                    foreach ($e as $v) {
                        if ($v !== NULL) {
                            $wc .= " AND HORARIOS.$key LIKE '%$v%'";
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
                $wc .= " AND HORARIOS.Hor$key LIKE '%$v%'";
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

// print_r($query).exit;
$stmt = $dbApiQuery($query) ?? '';

function arrDia($tipo, $de, $Ha, $Des, $li, $Ho){
    
    switch ($tipo) {
        case '0':
            $tipo = 'No Laboral';
            break;
        case '1':
            $tipo = 'No Laboral';
            break;
        case '2':
            $tipo = 'Según día';
            break;
        default:
            $tipo = 'No definido';
            break;
    }
        return array(
            "Laboral"  => $tipo,
            "Desde"    => $de,
            "Hasta"    => $Ha,
            "Descanso" => $Des,
            "Limite"   => intval($li),
            "Horas"    => $Ho,
        );
}
foreach ($stmt  as $key => $v) {
    $data[] = array(
        "Codi"       => $v['HorCodi'],
        "Desc"       => $v['HorDesc'],
        "ID"         => $v['HorID'],
        "Color"      => floatval($v['HorColor']),
        "FechaHora" => fecha($v['FechaHora'],'Y-m-d H:i:s'),
        "Lunes"      => arrDia($v['HorLune'], $v['HorLuDe'], $v['HorLuHa'], $v['HorLuRe'], $v['HorLuLi'], $v['HorLuHs']),
        "Martes"     => arrDia($v['HorMart'], $v['HorMaDe'], $v['HorMaHa'], $v['HorMaRe'], $v['HorMaLi'], $v['HorMaHs']),
        "Miercoles"  => arrDia($v['HorMier'], $v['HorMiDe'], $v['HorMiHa'], $v['HorMiRe'], $v['HorMiLi'], $v['HorMiHs']),
        "Jueves"     => arrDia($v['HorJuev'], $v['HorJuDe'], $v['HorJuHa'], $v['HorJuRe'], $v['HorJuLi'], $v['HorJuHs']),
        "Viernes"    => arrDia($v['HorVier'], $v['HorViDe'], $v['HorViHa'], $v['HorViRe'], $v['HorViLi'], $v['HorViHs']),
        "Sabado"     => arrDia($v['HorSaba'], $v['HorSaDe'], $v['HorSaHa'], $v['HorSaRe'], $v['HorSaLi'], $v['HorSaHs']),
        "Domingo"    => arrDia($v['HorDomi'], $v['HorDoDe'], $v['HorDoHa'], $v['HorDoRe'], $v['HorDoLi'], $v['HorDoHs']),
        "Feriado"    => arrDia($v['HorFeri'], $v['HorFeDe'], $v['HorFeHa'], $v['HorFeRe'], $v['HorFeLi'], $v['HorFeHs']),
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
