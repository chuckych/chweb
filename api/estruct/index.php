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
$start = start();
$length = length();

$dp['Codi'] = ($dp['Codi']) ?? [];
$dp['Codi'] = vp($dp['Codi'], 'Codi', 'intArrayM0', 11);

$dp['Estruct'] = ($dp['Estruct']) ?? [];
$dp['Estruct'] = vp($dp['Estruct'], 'Estruct', 'str', 11);


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
        $pref = $dp['Estruct'];
        break;
    case 'Pla':
        $tabla = 'PLANTAS';
        $pref = $dp['Estruct'];
        break;
    case 'Sec':
        $tabla = 'SECTORES';
        $pref = $dp['Estruct'];
        break;
    case 'Con':
        $tabla = 'CONVENIO';
        $pref = $dp['Estruct'];
        break;
    case 'Se2':
        $tabla = 'SECCION';
        $pref = $dp['Estruct'];
        break;
    case 'Gru':
        $tabla = 'GRUPOS';
        $pref = $dp['Estruct'];
        break;
    case 'Suc':
        $tabla = 'SUCURSALES';
        $pref = $dp['Estruct'];
        break;
    case 'Nov':
        $tabla = 'NOVEDAD';
        $pref = $dp['Estruct'];
        break;
    case 'ONov':
        $tabla = 'OTRASNOV';
        $pref = $dp['Estruct'];
        break;
    case 'THo':
        $tabla = 'TIPOHORA';
        $pref = $dp['Estruct'];
        break;
    case 'THoC':
        $tabla = 'TIPOHORACAUSA';
        $pref = $dp['Estruct'];
        break;
    case 'NovC':
        $tabla = 'NOVECAUSA';
        $pref = $dp['Estruct'];
        break;
    case 'RC':
        $tabla = 'REGLASCH';
        $pref = $dp['Estruct'];
        break;
    case 'Hor':
        $tabla = 'HORARIOS';
        $pref = $dp['Estruct'];
        break;
    case 'Loc':
        $tabla = 'LOCALIDA';
        $pref = $dp['Estruct'];
        break;
    case 'Nac':
        $tabla = 'NACIONES';
        $pref = $dp['Estruct'];
        break;
    case 'Pro':
        $tabla = 'PROVINCI';
        $pref = $dp['Estruct'];
        break;
    case 'Tare':
        $tabla = 'TAREAS';
        $pref = $dp['Estruct'];
        break;
    case 'Gua':
        $tabla = 'GUARDIAS';
        $pref = $dp['Estruct'];
    case 'Lega':
        $tabla = 'PERSONAL';
        $pref = $dp['Estruct'];
        break;
    default:
        http_response_code(400);
        (response("Parámetro 'Estruct' es inválido", 0, "Parámetro 'Estruct' es inválido", 400, timeStart(), 0, 0));
        exit;
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
                $e = "'" . join("','", $e) . "'";
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

$Codi = $pref . 'Codi';
$Desc = ($dp['Estruct'] == 'Emp') ? $pref . 'Razon' : $pref . 'Desc';
$Sec = ($dp['Estruct'] == 'Se2') ? true : '';
$THoC = ($dp['Estruct'] == 'THoC') ? true : '';
$Lega = ($dp['Estruct'] == 'Lega') ? true : '';
$NovC = ($dp['Estruct'] == 'NovC') ? true : '';
$Nov = ($dp['Estruct'] == 'Nov') ? true : '';
$JoinSe2 = ($dp['Estruct'] == 'Se2') ? "INNER JOIN SECTORES ON SECCION.SecCodi = SECTORES.SecCodi" : '';
$SecDesc = ($dp['Estruct'] == 'Se2') ? ",SECTORES.SecDesc" : '';

$JoinTHora = ($dp['Estruct'] == 'THoC') ? "INNER JOIN TIPOHORA ON TIPOHORACAUSA.THoCHora = TIPOHORA.THoCodi" : '';
// $THoraDesc = ($dp['Estruct'] == 'THoC') ? ",TIPOHORA.THoDesc" : '';
$JoinNovC = ($dp['Estruct'] == 'NovC') ? "INNER JOIN NOVEDAD ON NOVECAUSA.NovCNove = NOVEDAD.NovCodi" : '';
$TipoNovedad = ($dp['Estruct'] == 'Nov') ? ", NOVEDAD.NovTipo, dbo.fn_TipoNovedad(NOVEDAD.NovTipo) as 'NovTipoDesc' " : '';

if ($Lega) {
    $Codi = 'LegNume';
    $Desc = 'LegApNo';
}

$wc .= ($dp['Desc']) ? " AND CONCAT('', $Desc, $Codi) LIKE '%$dp[Desc]%'" : '';

$query = "SELECT * $SecDesc $TipoNovedad FROM $tabla $JoinSe2 $JoinTHora $JoinNovC WHERE $Codi > 0";
// print_r($query) . exit;

$queryCount = "SELECT count(1) as 'count' FROM $tabla WHERE $Codi > 0";

if ($dp['Estruct'] == 'Con') {
    $query = "SELECT * $SecDesc $TipoNovedad FROM $tabla $JoinSe2 $JoinTHora $JoinNovC";
    $queryCount = "SELECT count(1) as 'count' FROM $tabla";
}

if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= " ORDER BY $Codi";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

$stmt = $dbApiQuery($query) ?? '';

foreach ($stmt as $key => $v) {

    if ($Sec) {
        $data[] = array(
            "Codi" => $v[$Codi],
            "Desc" => $v[$Desc],
            "Sector" => array(
                "Codi" => $v['SecCodi'],
                "Desc" => $v['SecDesc'],
            ),
            "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
        );
    } else if ($THoC) {
        $data[] = array(
            "Codi" => $v[$Codi],
            "Desc" => $v[$Desc],
            "CodiHora" => $v['THoCodi'],
            "DescHora" => $v['THoDesc'],
            "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
        );
    } else if ($NovC) {
        $data[] = array(
            "Codi" => $v[$Codi],
            "Desc" => $v[$Desc],
            "CodiNov" => $v['NovCodi'],
            "DescNov" => $v['NovDesc'],
            "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
        );
    } else if ($Lega) {
        $data[] = [
            "Codi" => $v['LegNume'],
            "Desc" => $v['LegApNo'],
            "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
        ];
    } else if ($Nov) {
        $data[] = array(
            "Codi" => $v[$Codi],
            "Desc" => $v[$Desc],
            "Tipo" => $v['NovTipo'],
            "TipoDesc" => $v['NovTipoDesc'],
            "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
        );
    } else {
        $data[] = array(
            "Codi" => $v[$Codi],
            "Desc" => $v[$Desc],
            "FechaHora" => fechFormat($v['FechaHora'], 'Y-m-d H:i:s')
        );
    }
}

if (empty($data)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
$countData = count($data);
http_response_code(200);
(response($data, $stmtCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
