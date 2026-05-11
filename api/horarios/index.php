<?php
require __DIR__ . '/../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();
$request = Flight::request();

$checkMethod('POST');
function intToRgb($colorInt)
{
    // Convertir el entero a hexadecimal y quitar el prefijo '0x'
    $hexColor = strtoupper(dechex($colorInt & 0xFFFFFF));

    // Asegurarse de que el string tenga 6 caracteres (rellenar con ceros si es necesario)
    $hexColor = str_pad($hexColor, 6, '0', STR_PAD_LEFT);

    // Separar los componentes RGB
    $red = hexdec(substr($hexColor, 0, 2));
    $green = hexdec(substr($hexColor, 2, 2));
    $blue = hexdec(substr($hexColor, 4, 2));

    return [$red, $green, $blue];
}

function getBrightness($r, $g, $b)
{
    // Fórmula para calcular el brillo percibido
    return ($r * 299 + $g * 587 + $b * 114) / 1000;
}

function getTextColor($backgroundColor)
{
    list($r, $g, $b) = intToRgb($backgroundColor);
    $brightness = getBrightness($r, $g, $b);

    // Si el brillo es alto, usa texto negro; si es bajo, usa texto blanco
    return ($brightness > 128) ? 'rgb(0, 0, 0)' : 'rgb(255, 255, 255)';
}

$wc = '';

$dp = $request->data;

$start = start();
$length = length();

$LastCodi = $dp->LastCodi ?? 0;
$OrderBy = $dp->OrderBy ?? '';

$mapOrderBy = [
    'Fecha' => 'FechaHora desc',
    'Codi' => 'HorCodi asc',
    'ID' => 'HorID asc',
    'Desc' => 'HorDesc asc'
];

$dp->Codi = ($dp->Codi) ?? [];
$dp->Codi = vp($dp->Codi, 'Codi', 'intArrayM0', 11);

$dp->ID = ($dp->ID) ?? [];
$dp->ID = vp($dp->ID, 'ID', 'strArray', 3);

$dp->Desc = $dp->Desc ?? '';
$dp->Desc = vp($dp->Desc, 'Desc', 'str', 40);

$arrDPHorarios = array(
    'Codi' => $dp->Codi, // Codigo de Horario {int} {array}
    'ID' => $dp->ID, // ID de Horario {int} {array}
);
$arrDPSTR = array(
    'Desc' => $dp->Desc, // Descripcion de Horario {string}
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
                    foreach ($e as $v1) {
                        if ($v1 !== NULL) {
                            $wc .= " AND HORARIOS.$key LIKE '%$v1%'";
                        }
                    }
                }
            }
        }
    } else {
        if ($v) {
            if ($key == 'HorDesc') {
                $wc .= " AND CONCAT(HORARIOS.HorID, HORARIOS.HorCodi, HORARIOS.Hor$key) LIKE '%$v%'";
            } else {
                $wc .= " AND CONCAT(HORARIOS.HorID, HORARIOS.HorCodi, HORARIOS.Hor$key) LIKE '%$v%'";
            }
        }
    }
}
// si LastCodi es mayor a 0, entonces retornar solo el MAX(HorCodi) menor a LastCodi de la tabla HORARIOS
if ($LastCodi > 0) {
    $queryLastCodi = "SELECT MAX(HorCodi) as LastCodi FROM HORARIOS WHERE HORARIOS.HorCodi > 0";
    $stmtLastCodi = $dbApiQuery($queryLastCodi)[0]['LastCodi'] ?? 0;
    $countData = count($stmtLastCodi ?? []);
    http_response_code(200);
    (response($stmtLastCodi ?? [], 1, 'OK', 200, $time_start, $countData, $idCompany));
    exit;
}
$query = "SELECT * FROM HORARIOS WHERE HORARIOS.HorCodi > 0";
$queryCount = "SELECT count(1) as 'count' FROM HORARIOS WHERE HORARIOS.HorCodi > 0";

if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= ($OrderBy && isset($mapOrderBy[$OrderBy])) ? " ORDER BY " . $mapOrderBy[$OrderBy] : " ORDER BY HorCodi";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";

// print_r($query).exit;
$stmt = $dbApiQuery($query) ?? '';

function minutosAHorasSemanales(int $minutos)
{
    $horas = floor($minutos / 60);
    $minutosRestantes = $minutos % 60;
    return sprintf('%02d:%02d', $horas, $minutosRestantes);
}

function arrDia($tipo, $de, $Ha, $Des, $li, $Ho)
{

    switch ($tipo) {
        case '0':
            $tipoStr = 'No Laboral';
            break;
        case '1':
            $tipoStr = 'Laboral';
            break;
        case '2':
            $tipoStr = 'Según día';
            break;
        case '3':
            $tipoStr = 'Según día con Horario';
            break;
        default:
            $tipoStr = 'No definido';
            break;
    }
    $HorasMin = intval($tipo) === 1 ? HoraMin($Ho) : 0;
    return [
        "Laboral" => $tipoStr,
        "LaboralID" => intval($tipo),
        "Desde" => $de,
        "Hasta" => $Ha,
        "Descanso" => $Des,
        "Limite" => intval($li),
        "Horas" => $Ho,
        "HorasMin" => $HorasMin,
    ];
}
foreach ($stmt as $key => $v) {
    $HorasMin = 0;
    $backgroundColorRgb = intToRgb($v['HorColor']);
    $textColor = getTextColor($v['HorColor']);

    $HorLun = arrDia($v['HorLune'], $v['HorLuDe'], $v['HorLuHa'], $v['HorLuRe'], $v['HorLuLi'], $v['HorLuHs']);
    $HorMar = arrDia($v['HorMart'], $v['HorMaDe'], $v['HorMaHa'], $v['HorMaRe'], $v['HorMaLi'], $v['HorMaHs']);
    $HorMie = arrDia($v['HorMier'], $v['HorMiDe'], $v['HorMiHa'], $v['HorMiRe'], $v['HorMiLi'], $v['HorMiHs']);
    $HorJue = arrDia($v['HorJuev'], $v['HorJuDe'], $v['HorJuHa'], $v['HorJuRe'], $v['HorJuLi'], $v['HorJuHs']);
    $HorVie = arrDia($v['HorVier'], $v['HorViDe'], $v['HorViHa'], $v['HorViRe'], $v['HorViLi'], $v['HorViHs']);
    $HorSab = arrDia($v['HorSaba'], $v['HorSaDe'], $v['HorSaHa'], $v['HorSaRe'], $v['HorSaLi'], $v['HorSaHs']);
    $HorDom = arrDia($v['HorDomi'], $v['HorDoDe'], $v['HorDoHa'], $v['HorDoRe'], $v['HorDoLi'], $v['HorDoHs']);
    $HorFer = arrDia($v['HorFeri'], $v['HorFeDe'], $v['HorFeHa'], $v['HorFeRe'], $v['HorFeLi'], $v['HorFeHs']);
    $HorasMin += $HorLun['HorasMin'] + $HorMar['HorasMin'] + $HorMie['HorasMin'] + $HorJue['HorasMin'] + $HorVie['HorasMin'] + $HorSab['HorasMin'] + $HorDom['HorasMin'] + $HorFer['HorasMin'];
    $HorasSemanales = minutosAHorasSemanales($HorasMin);
    $data[] = [
        "Codi" => $v['HorCodi'],
        "Desc" => $v['HorDesc'],
        "ID" => $v['HorID'],
        "ColorInt" => floatval($v['HorColor']),
        "Color" => sprintf('rgb(%d, %d, %d)', $backgroundColorRgb[0], $backgroundColorRgb[1], $backgroundColorRgb[2]),
        "ColorText" => $textColor,
        "FechaHora" => fecha($v['FechaHora'], 'Y-m-d H:i:s'),
        "Lunes" => $HorLun,
        "Martes" => $HorMar,
        "Miércoles" => $HorMie,
        "Jueves" => $HorJue,
        "Viernes" => $HorVie,
        "Sábado" => $HorSab,
        "Domingo" => $HorDom,
        "Feriado" => $HorFer,
        "HorasMin" => $HorasMin,
        "HorasSem" => $HorasSemanales,
    ];
}

if (empty($stmt)) {
    http_response_code(200);
    (response([], 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
$countData = count($data ?? []);
http_response_code(200);
(response($data ?? [], $stmtCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
