<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$checkMethod('POST');

function validarHora($hora)
{
    if (is_string($hora)) {
        $f = explode(':', $hora);

        if (count($f) != 2) return false;

        if (!is_numeric($f[0])) return false;
        if (!is_numeric($f[1])) return false;

        if ($f[0] > 23 || $f[0] < 0) return false;
        if ($f[1] > 59 || $f[1] < 0) return false;

        if (strlen($f[0]) == 1) return false;
        if (strlen($f[1]) == 1) return false;

        return true;
    }
    return false;
}

$FicEstruct = '';
$ColEstruc = '';
$ColEstrucDesc = '';
$ColEstrucCod = '';
$wc = '';

$start  = start();
$length = length();

$validEstruct = array('Empr', 'Plan', 'Grup', 'Sect', 'Sucu', 'Tare', 'Conv', 'Regla', 'Sec2', 'Tipo', 'Lega', 'THora', 'HoraMin', 'HoraMax');
$dp['estruct'] = ($dp['Estruct']) ?? '';
$dp['Estruct'] = vp($dp['Estruct'], 'Estruct', 'strValid', '', $validEstruct); // str de estructura 

$dp['Desc'] = ($dp['Desc']) ?? '00:00';
$dp['Desc'] = vp($dp['Desc'], 'Desc', 'str', 20); // str de estructura

$dp['Baja'] = ($dp['Baja']) ?? [];
$dp['Baja'] = vp($dp['Baja'], 'Baja', 'numArray01', 1);
$dp['Sector'] = ($dp['Sector']) ?? '';

if ($dp['estruct'] == 'Sec2' && empty($dp['Sector'])) {
    http_response_code(400);
    (response(array(), 0, "Parámetro Sector es requerido.", 400, $time_start, 0, $idCompany));
    exit;
}

if ($dp['Sector'] && $dp['estruct'] == 'Sec2') {
    $stmtSect = $dbApiQuery("SELECT SecCodi FROM SECTORES WHERE SecCodi > 0") ?? '';
    $validEstruct = (array_column($stmtSect, 'SecCodi'));
    $dp['Sector'] = vp($dp['Sector'], 'Sector', 'intArray', 11, $validEstruct);
}

$dp['Lega'] = ($dp['Lega']) ?? [];
$dp['Lega'] = vp($dp['Lega'], 'Lega', 'intArray', 11);
$dp['Docu'] = ($dp['Docu']) ?? [];
$dp['Docu'] = vp($dp['Docu'], 'Docu', 'intArray', 11);
$dp['IntExt'] = ($dp['IntExt']) ?? [];
$dp['IntExt'] = vp($dp['IntExt'], 'IntExt', 'numArray01', 1);
$dp['ApNo'] = $dp['ApNo'] ?? '';
$dp['ApNo'] = vp($dp['ApNo'], 'ApNo', 'str', 40);
$dp['ApNoNume'] = $dp['ApNoNume'] ?? '';
$dp['ApNoNume'] = vp($dp['ApNoNume'], 'ApNoNume', 'str', 40);

$dp['Empr'] = ($dp['Empr']) ?? [];
$dp['Empr'] = vp($dp['Empr'], 'Empr', 'intArray', 5);
$dp['Plan'] = ($dp['Plan']) ?? [];
$dp['Plan'] = vp($dp['Plan'], 'Plan', 'intArray', 5);
$dp['Conv'] = ($dp['Conv']) ?? [];
$dp['Conv'] = vp($dp['Conv'], 'Conv', 'intArray', 5);
$dp['Sec2'] = ($dp['Sec2']) ?? [];
$dp['Sec2'] = vp($dp['Sec2'], 'Sec2', 'intArray', 5);
$dp['Sect'] = ($dp['Sect']) ?? [];
$dp['Sect'] = vp($dp['Sect'], 'Sect', 'intArray', 5);
$dp['Grup'] = ($dp['Grup']) ?? [];
$dp['Grup'] = vp($dp['Grup'], 'Grup', 'intArray', 5);
$dp['Sucu'] = ($dp['Sucu']) ?? [];
$dp['Sucu'] = vp($dp['Sucu'], 'Sucu', 'intArray', 5);
$dp['TareProd'] = ($dp['TareProd']) ?? [];
$dp['TareProd'] = vp($dp['TareProd'], 'TareProd', 'intArray', 5);
$dp['RegCH'] = ($dp['RegCH']) ?? [];
$dp['RegCH'] = vp($dp['RegCH'], 'RegCH', 'intArray', 5);
$dp['Tipo'] = ($dp['Tipo']) ?? [];
$dp['Tipo'] = vp($dp['Tipo'], 'Tipo', 'numArray01', 1);

$dp['THora'] = ($dp['THora']) ?? [];
$dp['THora'] = vp($dp['THora'], 'THora', 'intArray', 5);

$dp['FechaIni'] = ($dp['FechaIni']) ?? '';
$dp['FechaFin'] = ($dp['FechaFin']) ?? '';

$dp['HoraMin'] = ($dp['HoraMin']) ?? '';
$dp['HoraMin'] = vp($dp['HoraMin'], 'HoraMin', 'str', 5); // str de horas minimo

$dp['HoraMax'] = ($dp['HoraMax']) ?? '';
$dp['HoraMax'] = vp($dp['HoraMax'], 'HoraMax', 'str', 5); // str de horas maximo

if (empty($dp['FechaIni']) || empty($dp['FechaFin'])) {
    $dp['FechaIni'] = date('Ymd');
    $dp['FechaFin'] = date('Ymd');
}
// Si la fecha de Ini es mayor a la fecha de Fin, error
if ($dp['FechaIni'] > $dp['FechaFin']) {
    http_response_code(400);
    (response(array(), 0, "FechaIni no puede ser mayor a FechaFin.", 400, $time_start, 0, $idCompany));
    exit;
}
if (!validarHora($dp['HoraMin'])) {
    http_response_code(400);
    (response(array(), 0, "Formato de HoraMin Incorrecto", 400, $time_start, 0, $idCompany));
    exit;
}
if (!validarHora($dp['HoraMax'])) {
    http_response_code(400);
    (response(array(), 0, "Formato de HoraMax Incorrecto", 400, $time_start, 0, $idCompany));
    exit;
}

$fechaIni = fechFormat($dp['FechaIni'], 'Ymd');
$fechaFin = fechFormat($dp['FechaFin'], 'Ymd');

// Flight::json($dp['FechaIni']);
// exit;

if (validarHora($dp['HoraMin']) && validarHora($dp['HoraMax'])) {

    $wc .= " AND (dbo.fn_STRMinutos(FICHAS1.FicHsAu) >= dbo.fn_STRMinutos('" . $dp['HoraMin'] . "') AND  dbo.fn_STRMinutos(FICHAS1.FicHsAu) <= dbo.fn_STRMinutos('" . $dp['HoraMax'] . "'))";
}

$wc .= " AND (FICHAS.FicFech BETWEEN '" . $fechaIni . "' AND '" . $fechaFin . "')";

$arrDP = array(
    'Lega' => $dp['Lega'],
    // Codigo de Horario {int} {array}
    'ApNo' => $dp['ApNo'],
    // Nombre y apellido {string}
    'Docu' => $dp['Docu'],
    // Documento {string}
    'ApNoNume' => $dp['ApNoNume'],
    // Nombre y apellido y Legajo {string}
    'IntExt' => $dp['IntExt'],
    // Tipo de legajo. Interno, Externo {int} {array}
    'Empr' => $dp['Empr'],
    // Empresa {int} {array}
    'Plan' => $dp['Plan'],
    // Planta {int} {array}
    'Conv' => $dp['Conv'],
    // Convenio {int} {array}
    'Sect' => $dp['Sect'],
    // Sector {int} {array}
    'Sec2' => $dp['Sec2'],
    // Seccion {int} {array}
    'Grup' => $dp['Grup'],
    // Grupos {int} {array}
    'Sucu' => $dp['Sucu'],
    // Sucursales {int} {array}
    'TareProd' => $dp['TareProd'],
    // Tareas de produccion {int} {array}
    'RegCH' => $dp['RegCH'],
    // Regla de control horario {int} {array}
    'Tipo' => $dp['Tipo'], // Tipo de personal {int} {array}
    'THora' => $dp['THora'], // Tipo de Hora {int} {array}
);

foreach ($arrDP as $key => $filtro) {
    $e = array();
    if (is_array($filtro)) {
        $v = '';
        $e = array_filter($filtro, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";

                if ($key == 'Sec2') { // Si viene Seccion hacemos explode de sector seccion
                    foreach ($dp['Sec2'] as $se2) {
                        $dataSec2[] = $se2;
                    }
                    $dataSec2 = implode(',', $dataSec2);
                    $wc .= " AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($dataSec2)";
                } else if ($key == 'THora') {  // Si viene Tipo de Hora
                    foreach ($dp['THora'] as $THora) {
                        $dataTHora[] = $THora;
                    }
                    $dataTHora = implode(',', $dataTHora);
                    $wc .= " AND FICHAS1.FicHora IN ($dataTHora)";
                } else {
                    $wc .= " AND FICHAS.Fic$key IN ($e)";
                }
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        // $wc .= " AND PERSONAL.Leg$key = '$v'";
                        if ($key == 'Sec2') { // Si viene Seccion hacemos explode de sector seccion
                            // $secSec2 = explode('-', $dp['Sec2'][0]);
                            $dataSec2 = implode(',', $dp['Sec2']);
                            $wc .= " AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($dataSec2)";
                        } else if ($key == 'THora') {  // Si viene Tipo de Hora
                            $dataTHora = implode(',', $dp['THora']);
                            $wc .= " AND FICHAS1.FicHora = '$dataTHora'";
                        } else {
                            $wc .= " AND FICHAS.Fic$key = '$v'";
                        }
                    }
                }
            }
        }
    } else {
        if ($filtro) {
            if ($key == 'ApNoNume') {
                $wc .= " AND CONCAT(PERSONAL.LegApNo, PERSONAL.LegNume) LIKE '%$filtro%'";
            } else if ($key == 'ApNo') {
                $wc .= " AND PERSONAL.LegApNo LIKE '%$filtro%'";
            } else {
                $wc .= " AND FICHAS.Fic$key = '$filtro'";
            }
        }
    }
}

$JoinFichas1 = '';
switch ($dp['Estruct']) {
    case 'Empr':
        $FicEstruct = 'FICHAS.FicEmpr';
        $ColEstruc = 'EMPRESAS';
        $ColEstrucDesc = 'EMPRESAS.EmpRazon';
        $ColEstrucCod = 'EMPRESAS.EmpCodi';
        break;
    case 'Plan':
        $FicEstruct = 'FICHAS.FicPlan';
        $ColEstruc = 'PLANTAS';
        $ColEstrucDesc = 'PLANTAS.PlaDesc';
        $ColEstrucCod = 'PLANTAS.PlaCodi';
        break;
    case 'Grup':
        $FicEstruct = 'FICHAS.FicGrup';
        $ColEstruc = 'GRUPOS';
        $ColEstrucDesc = 'GRUPOS.GruDesc';
        $ColEstrucCod = 'GRUPOS.GruCodi';
        break;
    case 'Sect':
        $FicEstruct = 'FICHAS.FicSect';
        $ColEstruc = 'SECTORES';
        $ColEstrucDesc = 'SECTORES.SecDesc';
        $ColEstrucCod = 'SECTORES.SecCodi';
        break;
    case 'Sucu':
        $FicEstruct = 'FICHAS.FicSucu';
        $ColEstruc = 'SUCURSALES';
        $ColEstrucDesc = 'SUCURSALES.SucDesc';
        $ColEstrucCod = 'SUCURSALES.SucCodi';
        break;
    case 'Tare':
        $FicEstruct = 'FICHAS.FicTareProd';
        $ColEstruc = 'TAREAS';
        $ColEstrucDesc = 'TAREAS.TareDesc';
        $ColEstrucCod = 'TAREAS.TareCodi';
        break;
    case 'Conv':
        $FicEstruct = 'FICHAS.FicConv';
        $ColEstruc = 'CONVENIO';
        $ColEstrucDesc = 'CONVENIO.ConDesc';
        $ColEstrucCod = 'CONVENIO.ConCodi';
        break;
    case 'Regla':
        $FicEstruct = 'FICHAS.FicRegCH';
        $ColEstruc = 'REGLASCH';
        $ColEstrucDesc = 'REGLASCH.RCDesc';
        $ColEstrucCod = 'REGLASCH.RCCodi';
        break;
    case 'THora':
        $FicEstruct = 'FICHAS1.FicHora';
        $ColEstruc = 'TIPOHORA';
        $ColEstrucDesc = 'TIPOHORA.THoDesc';
        $ColEstrucCod = 'TIPOHORA.THoCodi';
        break;
    case 'Lega':
        $FicEstruct = 'FICHAS.FicLega';
        $ColEstruc = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod = 'PERSONAL.LegNume';
        break;
    case 'Tipo':
        $ColEstruc = 'PERSONAL';
        $ColEstrucCod = 'PERSONAL.LegTipo';
        break;
}
$JoinFichas1 = "INNER JOIN FICHAS1 ON FICHAS.FicLega = FICHAS1.FicLega AND FICHAS.FicFech = FICHAS1.FicFech AND FICHAS.FicTurn = FICHAS1.FicTurn";
$FiltroQ = (!empty($dp['Desc'])) ? "AND CONCAT($ColEstrucCod, $ColEstrucDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$dp[Desc]%'" : '';

switch ($dp['Estruct']) {
    case 'Tipo':
        $query = "SELECT $ColEstrucCod AS 'Cod', COUNT(*) AS 'Count' FROM $ColEstruc INNER JOIN FICHAS ON PERSONAL.LegNume = FICHAS.FicLega $JoinFichas1 WHERE PERSONAL.LegNume > 0 $wc GROUP BY $ColEstrucCod ORDER BY $ColEstrucCod OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
        break;
    case 'Lega__old':

        $dataApiPerson['DATA'] = $dataApiPerson['DATA'] ?? '';
        $dataApiPerson['MESSAGE'] = $dataApiPerson['MESSAGE'] ?? '';

        $dataParamPerson = array(
            "Nume" => $dp['Nume'],
            "ApNoNume" => $dp['Desc'],
            "getDatos" => 1,
            "Baja" => ($dp['Baja']),
            "Empr" => ($dp['Empr']),
            "Plan" => ($dp['Plan']),
            "Sect" => ($dp['Sect']),
            "Sec2" => ($dp['Sec2']),
            "Grup" => ($dp['Grup']),
            "Sucu" => ($dp['Sucu']),
            "Conv" => ($dp['Conv']),
            "TareProd" => ($dp['Tare']),
            "RegCH" => ($dp['RegCH']),
            "Tipo" => ($dp['Tipo']),
            "start" => $start,
            "length" => $length,
        );
        $url = "$dataC[hostCHWeb]/$dataC[homeHost]/api/personal/";

        $dataApiPerson = json_decode($requestApi($url, $dataParamPerson, 10), true);
        // Flight::json($dataApiPerson) . exit;

        foreach ($dataApiPerson['DATA'] as $key => $v) {
            $id = $v['Lega'];
            $text = $v['ApNo'];
            $data[] = array(
                'Cod' => $id,
                'Desc' => $text,
                'Docu' => $v['Datos']['Docu'],
                'CUIL' => $v['Datos']['CUIT'],
            );
        }
        http_response_code(200);
        (response($data, $dataApiPerson['TOTAL'], $dataApiPerson['MESSAGE'], 200, $time_start, $dataApiPerson['COUNT'], $idCompany));
        // exit;
        break;
    case 'Sec2':
        $sectorSecc = implode(',', $dp['Sector']);
        $FiltroQ = (!empty($dp['Desc'])) ? "AND CONCAT(SECCION.SecCodi, SECCION.Se2Desc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$dp[Desc]%'" : '';
        $query = "SELECT FICHAS.FicSec2 AS 'Cod', SECCION.Se2Desc AS 'Desc', SECCION.SecCodi AS 'SecCodi', SECTORES.SecDesc, COUNT(*) AS 'Count' FROM FICHAS 
        $JoinFichas1
        INNER JOIN SECCION ON FICHAS.FicSec2=SECCION.Se2Codi AND FICHAS.FicSect=SECCION.SecCodi 
        INNER JOIN SECTORES ON SECCION.SecCodi = SECTORES.SecCodi WHERE FICHAS.FicSec2 > 0  AND FICHAS.FicSect IN ($sectorSecc) $wc $FiltroQ GROUP BY FICHAS.FicSec2, SECCION.Se2Desc, SECCION.SecCodi, SECTORES.SecDesc ORDER BY FICHAS.FicSec2 OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
        break;
    default:
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc', COUNT(*) AS 'Count' FROM FICHAS $JoinFichas1 INNER JOIN $ColEstruc ON $FicEstruct = $ColEstrucCod WHERE FICHAS.FicLega > 0 $wc $FiltroQ GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct OFFSET $start ROWS FETCH NEXT $length ROWS ONLY ";
        break;
}
// Flight::json($query) . exit;

$stmt = $dbApiQuery($query) ?? '';

if (empty($stmt)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}

foreach ($stmt as $key => $row) :

    switch ($dp['Estruct']) {
        case 'Tipo':
            $Cod = $row['Cod'];
            $Count = $row['Count'];
            $id2 = ($Cod == 0) ? 0 : 1;
            $Desc = ($Cod == '0') ? 'Mensuales' : 'Jornales';
            $data[] = array(
                'Cod' => $id2,
                'Desc' => $Desc,
                'Count' => $Count,
            );
            break;
        case 'Sec2':
            $Cod = $row['Cod'];
            $Count = $row['Count'];
            $Desc = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

            $data[] = array(
                'Cod' => $Cod,
                'Desc' => $Desc,
                'Count' => $Count,
                'Sect' => $row['SecCodi'],
                'SectDesc' => $row['SecDesc'],
            );
            break;
        default:
            $Cod = $row['id'];
            $Count = $row['Count'];
            $Desc = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

            $data[] = array(
                'Cod' => $Cod,
                'Desc' => $Desc,
                'Count' => $Count,
            );
            break;
    }

endforeach;

$countData = count($data);
http_response_code(200);
(response($data, $countData, 'OK', 200, $time_start, $countData, $idCompany));
exit;
