<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

if ($method != 'POST') {
    http_response_code(400);
    (response(array(), 0, 'Invalid Request Method: ' . $method, 400, $time_start, 0, $idCompany));
    exit;
}

// Flight::json($dataC).exit;

$FicEstruct    = '';
$ColEstruc     = '';
$ColEstrucDesc = '';
$ColEstrucCod  = '';
$wc            = '';

$validEstruct = array('Empr', 'Plan', 'Grup', 'Sect', 'Sucu', 'Tare', 'Conv', 'Regla', 'Sec2', 'Tipo', 'Lega');
$dp['estruct']    = ($dp['Estruct']) ?? '';
$dp['Estruct']    = vp($dp['Estruct'], 'Estruct', 'strValid', '', $validEstruct); // str de estructura 

$dp['Desc'] = ($dp['Desc']) ?? '';
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

$dp['Nume']     = ($dp['Nume']) ?? [];
$dp['Nume']     = vp($dp['Nume'], 'Nume', 'intArray', 11);
$dp['Docu']     = ($dp['Docu']) ?? [];
$dp['Docu']     = vp($dp['Docu'], 'Docu', 'intArray', 11);
$dp['IntExt']   = ($dp['IntExt']) ?? [];
$dp['IntExt']   = vp($dp['IntExt'], 'IntExt', 'numArray01', 1);
$dp['ApNo']     = $dp['ApNo'] ?? '';
$dp['ApNo']     = vp($dp['ApNo'], 'ApNo', 'str', 40);
$dp['ApNoNume'] = $dp['ApNoNume'] ?? '';
$dp['ApNoNume'] = vp($dp['ApNoNume'], 'ApNoNume', 'str', 40);

$dp['Empr']     = ($dp['Empr']) ?? [];
$dp['Empr']     = vp($dp['Empr'], 'Empr', 'intArray', 5);
$dp['Plan']     = ($dp['Plan']) ?? [];
$dp['Plan']     = vp($dp['Plan'], 'Plan', 'intArray', 5);
$dp['Conv']     = ($dp['Conv']) ?? [];
$dp['Conv']     = vp($dp['Conv'], 'Conv', 'intArray', 5);
$dp['Sec2']     = ($dp['Sec2']) ?? [];
$dp['Sec2']     = vp($dp['Sec2'], 'Sec2', 'intArray', 5);
$dp['Sect']     = ($dp['Sect']) ?? [];
$dp['Sect']     = vp($dp['Sect'], 'Sect', 'intArray', 5);
$dp['Grup']     = ($dp['Grup']) ?? [];
$dp['Grup']     = vp($dp['Grup'], 'Grup', 'intArray', 5);
$dp['Sucu']     = ($dp['Sucu']) ?? [];
$dp['Sucu']     = vp($dp['Sucu'], 'Sucu', 'intArray', 5);
$dp['TareProd'] = ($dp['TareProd']) ?? [];
$dp['TareProd'] = vp($dp['TareProd'], 'TareProd', 'intArray', 5);
$dp['RegCH'] = ($dp['RegCH']) ?? [];
$dp['RegCH'] = vp($dp['RegCH'], 'RegCH', 'intArray', 5);
$dp['Tipo'] = ($dp['Tipo']) ?? [];
$dp['Tipo'] = vp($dp['Tipo'], 'Tipo', 'numArray01', 1);

$arrDPPersonal = array(
    'Nume'     => $dp['Nume'], // Codigo de Horario {int} {array}
    'ApNo'     => $dp['ApNo'], // Nombre y apellido {string}
    'Docu'     => $dp['Docu'], // Documento {string}
    'ApNoNume' => $dp['ApNoNume'], // Nombre y apellido y Legajo {string}
    'IntExt'   => $dp['IntExt'], // Tipo de legajo. Interno, Externo {int} {array}
    'Empr'     => $dp['Empr'], // Empresa {int} {array}
    'Plan'     => $dp['Plan'], // Planta {int} {array}
    'Conv'     => $dp['Conv'], // Convenio {int} {array}
    'Sect'     => $dp['Sect'], // Sector {int} {array}
    'Sec2'     => $dp['Sec2'], // Seccion {int} {array}
    'Grup'     => $dp['Grup'], // Grupos {int} {array}
    'Sucu'     => $dp['Sucu'], // Sucursales {int} {array}
    'TareProd' => $dp['TareProd'], // Tareas de produccion {int} {array}
    'RegCH'    => $dp['RegCH'], // Regla de control horario {int} {array}
    'Tipo'     => $dp['Tipo'], // Tipo de personal {int} {array}
);

foreach ($arrDPPersonal as $key => $per) {
    $e = array();
    if (is_array($per)) {
        $v = '';
        $e = array_filter($per, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                // $wc .= " AND PERSONAL.Leg$key IN ($e)";

                if ($key == 'Sec2') { // Si viene Seccion hacemos explode de sector seccion
                    foreach ($dp['Sec2'] as $se2) {
                        // $secSec2 = explode('-',$se2);
                        // $dataSec2[] = $secSec2[0].$secSec2[1];
                        $dataSec2[] = $se2;
                    }
                    $dataSec2 = implode(',', $dataSec2);
                    // print_r($dataSec2).exit;
                    $wc .= " AND CONCAT(PERSONAL.LegSect, PERSONAL.LegSec2) IN ($dataSec2)"; 
                } else {
                    $wc .= " AND PERSONAL.Leg$key IN ($e)";
                }

            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        // $wc .= " AND PERSONAL.Leg$key = '$v'";
                        if ($key == 'Sec2') { // Si viene Seccion hacemos explode de sector seccion
                            // $secSec2 = explode('-', $dp['Sec2'][0]);
                            $dataSec2 = implode(',', $dp['Sec2']);
                            // Flight::json($dataSec2).exit;
                            $wc .= " AND CONCAT(PERSONAL.LegSect, PERSONAL.LegSec2) IN ($dataSec2)"; 
                        } else {
                            $wc .= " AND PERSONAL.Leg$key = '$v'";
                        }
                    }
                }
            }
        }
    } else {
        if ($per) {
            if ($key == 'ApNoNume') {
                $wc .= " AND CONCAT(PERSONAL.LegApNo, PERSONAL.LegNume) LIKE '%$per%'";
            } else if ($key == 'ApNo') {
                $wc .= " AND PERSONAL.Leg$key LIKE '%$per%'";
            } else {
                $wc .= " AND PERSONAL.Leg$key = '$per'";
            }
        }
    }
}

$arrDPPersonalBaja = array(
    'Baja' => $dp['Baja'], // Codigo de Horario {int} {array}
);
foreach ($arrDPPersonalBaja as $key => $baja) {
    $e = array();
    if (is_array($baja)) {
        $v = '';
        $e = array_filter($baja, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= "";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= ($v == 0) ? " AND PERSONAL.LegFeEg = '17530101'" : '';
                        $wc .= ($v == 1) ? " AND PERSONAL.LegFeEg != '17530101'" : '';
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= " AND PERSONAL.LegFeEg = '$v'";
        }
    }
}

switch ($dp['Estruct']) {
    case 'Empr':
        $FicEstruct    = 'PERSONAL.LegEmpr';
        $ColEstruc     = 'EMPRESAS';
        $ColEstrucDesc = 'EMPRESAS.EmpRazon';
        $ColEstrucCod  = 'EMPRESAS.EmpCodi';
        break;
    case 'Plan':
        $FicEstruct    = 'PERSONAL.LegPlan';
        $ColEstruc     = 'PLANTAS';
        $ColEstrucDesc = 'PLANTAS.PlaDesc';
        $ColEstrucCod  = 'PLANTAS.PlaCodi';
        break;
    case 'Grup':
        $FicEstruct    = 'PERSONAL.LegGrup';
        $ColEstruc     = 'GRUPOS';
        $ColEstrucDesc = 'GRUPOS.GruDesc';
        $ColEstrucCod  = 'GRUPOS.GruCodi';
        break;
    case 'Sect':
        $FicEstruct    = 'PERSONAL.LegSect';
        $ColEstruc     = 'SECTORES';
        $ColEstrucDesc = 'SECTORES.SecDesc';
        $ColEstrucCod  = 'SECTORES.SecCodi';
        break;
    case 'Sucu':
        $FicEstruct    = 'PERSONAL.LegSucu';
        $ColEstruc     = 'SUCURSALES';
        $ColEstrucDesc = 'SUCURSALES.SucDesc';
        $ColEstrucCod  = 'SUCURSALES.SucCodi';
        break;
    case 'Tare':
        $FicEstruct    = 'PERSONAL.LegTareProd';
        $ColEstruc     = 'TAREAS';
        $ColEstrucDesc = 'TAREAS.TareDesc';
        $ColEstrucCod  = 'TAREAS.TareCodi';
        break;
    case 'Conv':
        $FicEstruct    = 'PERSONAL.LegConv';
        $ColEstruc     = 'CONVENIO';
        $ColEstrucDesc = 'CONVENIO.ConDesc';
        $ColEstrucCod  = 'CONVENIO.ConCodi';
        break;
    case 'Regla':
        $FicEstruct    = 'PERSONAL.LegRegCH';
        $ColEstruc     = 'REGLASCH';
        $ColEstrucDesc = 'REGLASCH.RCDesc';
        $ColEstrucCod  = 'REGLASCH.RCCodi';
        break;
    case 'Tipo':
        $ColEstruc     = 'PERSONAL';
        $ColEstrucCod  = 'PERSONAL.LegTipo';
        break;
}
$FiltroQ  = (!empty($dp['Desc'])) ? "AND CONCAT($ColEstrucCod, $ColEstrucDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$dp[Desc]%'" : '';

switch ($dp['Estruct']) {
    case 'Tipo':
        $query = "SELECT $ColEstrucCod AS 'Cod', COUNT(*) AS 'Count' FROM $ColEstruc WHERE PERSONAL.LegNume > 0 $wc GROUP BY $ColEstrucCod ORDER BY $ColEstrucCod";
        break;
    case 'Lega':

        $dataApiPerson['DATA'] = $dataApiPerson['DATA'] ?? '';
        $dataApiPerson['MESSAGE'] = $dataApiPerson['MESSAGE'] ?? '';

        $dataParamPerson = array(
            "Nume"     => $dp['Nume'],
            "ApNoNume" => $dp['Desc'],
            "getDatos" => 1,
            "Baja"     => ($dp['Baja']),
            "Empr"     => ($dp['Empr']),
            "Plan"     => ($dp['Plan']),
            "Sect"     => ($dp['Sect']),
            "Sec2"     => ($dp['Sec2']),
            "Grup"     => ($dp['Grup']),
            "Sucu"     => ($dp['Sucu']),
            "Conv"     => ($dp['Conv']),
            "TareProd" => ($dp['Tare']),
            "RegCH"    => ($dp['RegCH']),
            "Tipo"     => ($dp['Tipo']),
            "start"    => $start,
            "length"   => $length,
        );
        $url = "$dataC[hostCHWeb]/$dataC[homeHost]/api/personal/";
        // Flight::json($url) . exit;

        $dataApiPerson = json_decode($requestApi($url, $dataParamPerson, 10), true);

        foreach ($dataApiPerson['DATA'] as $key => $v) {
            $id   = $v['Lega'];
            $text = $v['ApNo'];
            $data[] = array(
                'Cod'    => $id,
                'Desc'  => $text,
                'Docu' => $v['Datos']['Docu'],
                'CUIL' => $v['Datos']['CUIT'],
            );
        }
        http_response_code(200);
        (response($data, $dataApiPerson['TOTAL'], $dataApiPerson['MESSAGE'], 200, $time_start, $dataApiPerson['COUNT'], $idCompany));
        // exit;
        break;
    case 'Sec2':
        $sectorSecc = implode(',',$dp['Sector']);
        $FiltroQ  = (!empty($dp['Desc'])) ? "AND CONCAT(SECCION.SecCodi, SECCION.Se2Desc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$dp[Desc]%'" : '';
        $query = "SELECT PERSONAL.LegSec2 AS 'Cod', SECCION.Se2Desc AS 'Desc', SECCION.SecCodi AS 'SecCodi', SECTORES.SecDesc, COUNT(*) AS 'Count' FROM PERSONAL INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND PERSONAL.LegSect=SECCION.SecCodi INNER JOIN SECTORES ON SECCION.SecCodi = SECTORES.SecCodi WHERE PERSONAL.LegSec2 > 0 AND PERSONAL.LegSect IN ($sectorSecc) $wc $FiltroQ GROUP BY PERSONAL.LegSec2, SECCION.Se2Desc, SECCION.SecCodi, SECTORES.SecDesc ORDER BY PERSONAL.LegSec2";
        break;
    default:
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc', COUNT(*) AS 'Count' FROM PERSONAL INNER JOIN $ColEstruc ON $FicEstruct=$ColEstrucCod WHERE PERSONAL.LegNume > 0 $wc $FiltroQ GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
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
            $Cod   = $row['Cod'];
            $Count = $row['Count'];
            $id2   = ($Cod == 0) ? 0 : 1;
            $Desc  = ($Cod == '0') ? 'Mensuales' : 'Jornales';
            $data[] = array(
                'Cod'   => $id2,
                'Desc'  => $Desc,
                'Count' => $Count,
            );
            break;
        case 'Sec2':
            $Cod   = $row['Cod'];
            $Count = $row['Count'];
            $Desc  = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

            $data[] = array(
                'Cod'   => $Cod,
                'Desc'  => $Desc,
                'Count' => $Count,
                'Sect' => $row['SecCodi'],
                'SectDesc' => $row['SecDesc'],
            );
            break;
        default:
            $Cod   = $row['id'];
            $Count   = $row['Count'];
            $Desc = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

            $data[] = array(
                'Cod'   => $Cod,
                'Desc'  => $Desc,
                'Count' => $Count,
            );
            break;
    }

endforeach;

$countData    = count($data);
http_response_code(200);
(response($data, $countData, 'OK', 200, $time_start, $countData, $idCompany));
exit;
