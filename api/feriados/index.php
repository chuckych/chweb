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

$dp->Fech  = ($dp->Fech) ?? [];
$dp->Fech  = vp($dp->Fech, 'Fech', 'arrfecha', '');

$dp->Tras  = ($dp->Tras) ?? [];
$dp->Tras  = vp($dp->Tras, 'Tras', 'arrfecha', '');

$dp->TrasIniFin  = ($dp->TrasIniFin) ?? [];
$dp->TrasIniFin  = vp($dp->TrasIniFin, 'TrasIniFin', 'arrfecha', '');

$dp->Desc = $dp->Desc ?? '';
$dp->Desc = vp($dp->Desc, 'Desc', 'str', 40);

$arrDPFeriados = array(
    'Fech' => arrFecha($dp->Fech, 'Ymd'), // Fecha de feriados {str} {array}
    'Tras' => arrFecha($dp->Tras, 'Ymd'), // Fecha de traslado {str} {array}
);

if (($dp->TrasIniFin) && count($dp->TrasIniFin) != 2) {
    http_response_code(400);
    (response(array(), 0, 'Traslado de inicio y fin incorrecto', 400, $time_start, 0, $idCompany));
    exit;
}
$arrDPTrasIniFin = array();

if (count($dp->TrasIniFin) == 2) {
    $arrDPTrasIniFin = array(
        'TrasIni' => fecha($dp->TrasIniFin[0], 'Ymd'),
        'TrasFin' => fecha($dp->TrasIniFin[1], 'Ymd')
    );
    if (($arrDPTrasIniFin)) {
        if (intval($arrDPTrasIniFin['TrasIni']) > intval($arrDPTrasIniFin['TrasFin'])) {
            http_response_code(400);
            (response(array(), 0, 'Fecha de inicio mayor a fecha de fin', 400, $time_start, 0, $idCompany));
            exit;
        }
        $wc .= " AND FERIADOS.FerTras BETWEEN '$arrDPTrasIniFin[TrasIni]' AND '$arrDPTrasIniFin[TrasFin]'";
    }
}

// Flight::json(($wc)) . exit;

$arrDPSTR = array(
    'Desc'  => $dp->Desc, // Descripcion de Horario {string}
);

foreach ($arrDPFeriados as $key => $FERIADOS) {
    $e = array();
    if (is_array($FERIADOS)) {
        $v = '';
        $e = array_filter($FERIADOS, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= " AND FERIADOS.Fer$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= " AND FERIADOS.Fer$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($FERIADOS) {
            $wc .= " AND FERIADOS.Fer$key = '$v'";
        }
    }
}
foreach ($arrDPSTR as $key => $v) {

    if (is_array($v)) {
        if ($e = array_filter($v)) {
            if ($e) {
                if (count($e) > 1) {
                    $e = "'" . implode("','", $e) . "'";
                    $wc .= " AND FERIADOS.Fer$key LIKE '%$e%'";
                } else {
                    foreach ($e as $v) {
                        if ($v !== NULL) {
                            $wc .= " AND FERIADOS.Fer$key LIKE '%$v%'";
                        }
                    }
                }
            }
        }
    } else {
        if ($v) {
            if ($key == 'Desc') {
                $wc .= " AND FERIADOS.Fer$key LIKE '%$v%'";
            } else {
                $wc .= " AND FERIADOS.Fer$key LIKE '%$v%'";
            }
        }
    }
}

$query = "SELECT * FROM FERIADOS WHERE FerTipo >= 0";
$queryCount = "SELECT count(1) as 'count' FROM FERIADOS WHERE FerTipo >= 0";

if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}
// Flight::json($query).exit;

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= " ORDER BY FERIADOS.FerTras";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

// print_r($query).exit;
$stmt = $dbApiQuery($query) ?? '';
function InfoEn($v)
{
    switch ($v) {
        case '0':
            $str = 'Todos los días';
            break;
        case '1':
            $str = 'En Laboral';
            break;
        case '2':
            $str = 'En No Laboral';
            break;
        case '3':
            $str = 'En Hábiles';
            break;
        case '4':
            $str = 'En No Hábiles';
            break;

        default:
            $str = '';
            break;
    }
    return $str;
}
function FerTipo($v)
{
    switch ($v) {
        case '0':
            $TipoStr = 'Nacional';
            break;
        case '1':
            $TipoStr = 'Opcional';
            break;
        case '2':
            $TipoStr = 'Empresarial';
            break;
        default:
            $TipoStr = 'Sin Definir';
            break;
    }
    return $TipoStr;
}
foreach ($stmt  as $key => $v) {
    // $data[] = $v;
    $data[] = array(
        'Descripcion' => $v['FerDesc'],
        'Tipo'        => $v['FerTipo'],
        'TipoStr'     => FerTipo($v['FerTipo']),
        'Fecha' => array(
            'Fecha' => fecha($v['FerFech']),
            'Dia' => diaSemana($v['FerFech']),
        ),
        'Traslado' => array(
            'Fecha' => fecha($v['FerTras']),
            'Dia' => diaSemana($v['FerTras']),
        ),
        'CodigosLiqui' => array(
            'Mensuales' => array(
                'Mens'          => $v['FerCodM'],
                'MensTrab'      => $v['FerCodM2'],
                'Mens3'         => $v['FerCodM3'],
                'MensInfH'      => $v['FerInfM'],
                'MensInfoEn'    => $v['FerInMeNL'],
                'MensInfoEnStr' => InfoEn($v['FerInMeNL']),
            ),
            'Jornales' => array(
                'Jorn'          => $v['FerCodJ'],
                'Jorntrab'      => $v['FerCodJ2'],
                'Jorn3'         => $v['FerCodJ3'],
                'JornInfh'      => $v['FerInfJ'],
                'JornInfoEn'    => $v['FerInJoNL'],
                'JornInfoEnStr' => InfoEn($v['FerInJoNL']),
            ),
        ),
        'InfoFerTrab' => $v['FerInFeTR'],
        'FechaHora'   => fecha($v['FechaHora'], 'Y-m-d H:i:s'),
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
