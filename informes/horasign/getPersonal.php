<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
borrarLogs('archivos/', 1, '.json');

$request = Flight::request();
$method = $request->method;
$params = $request->data;
$data = array();
$dataHorarios = array();
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);
$params['length'] = $params['length'] ?? '';
$params['time'] = $params['time'] ?? '';
$params['_drhorarios'] = $params['_drhorarios'] ?? '';

// Flight::json($params) . exit;

($method != 'POST') ? exit : '';

if (isset($params['_drhorarios']) && !empty($params['_drhorarios'])) {
    $DateRange = explode(' al ', $params['_drhorarios']);
    $f1 = new DateTime(str_replace('/', '-', $DateRange[0]));
    $f2 = new DateTime(str_replace('/', '-', $DateRange[1]));
    $FechaIni = $f1->format('Y-m-d');
    $FechaFin = $f2->format('Y-m-d');
} else {
    $FechaIni = date('Y-m-d');
    $FechaFin = date('Y-m-d');
}

$params['Per'] = $params['Per'] ?? '';
$params['Emp'] = $params['Emp'] ?? '';
$params['Plan'] = $params['Plan'] ?? '';
$params['Sect'] = $params['Sect'] ?? '';
$params['Sec2'] = $params['Sec2'] ?? '';
$params['Grup'] = $params['Grup'] ?? '';
$params['Sucur'] = $params['Sucur'] ?? '';
$params['Tipo'] = ($params['Tipo']) ?? '';
$params['Regla'] = ($params['Regla']) ?? '';
$params['toExcelAll'] = ($params['toExcelAll']) ?? '';

$Empr = $params['Emp'] ? ($params['Emp']) : explode(',', $_SESSION['EmprRol']);
$Per = $params['Per'] ? ($params['Per']) : array();
$Per2 = $params['Per2'] ? array($params['Per2']) : explode(',', $_SESSION['EstrUser']);
$Plan = $params['Plan'] ? $params['Plan'] : explode(',', $_SESSION['PlanRol']);
$Sect = $params['Sect'] ? $params['Sect'] : explode(',', $_SESSION['SectRol']);
$Grup = $params['Grup'] ? $params['Grup'] : explode(',', $_SESSION['GrupRol']);
$Sucu = $params['Sucur'] ? $params['Sucur'] : explode(',', $_SESSION['SucuRol']);
$Sec2 = $params['Sec2'] ? $params['Sec2'] : explode(',', $_SESSION['Sec2Rol']);
$Conv = $params['Conv'] ? $params['Conv'] : explode(',', $_SESSION['ConvRol']);
$Tare = $params['Tare'] ? $params['Tare'] : '';

switch ($params['Tipo']) {
    case '0':
        $LegTipo = array('0');
        break;
    case '1':
        $LegTipo = array('1');
        break;
    default:
        $LegTipo = array();
        break;
}
$RegCH = $params['Regla'] ? $params['Regla'] : '';

// $Legajos = ($Per2) ? ($Per2) : $Per;
$Legajos = ($Per) ? ($Per) : explode(',', $_SESSION['EstrUser']);

$dataApiPerson['DATA'] = $dataApiPerson['DATA'] ?? '';
$dataApiPerson['MESSAGE'] = $dataApiPerson['MESSAGE'] ?? '';

if ($params['toExcelAll'] == '1') {
    $params['length'] = 9999;
}

$dataParamPerson = array(
    "Nume" => ($Legajos),
    "getDatos" => 1,
    "Baja" => array("0"),
    "Empr" => ($Empr),
    "Plan" => ($Plan),
    "Sect" => ($Sect),
    "Sec2" => ($Sec2),
    "Grup" => ($Grup),
    "Sucu" => ($Sucu),
    "Conv" => ($Conv),
    "TareProd" => ($Tare),
    "RegCH" => ($RegCH),
    "Tipo" => ($LegTipo),
    "start" => $params['start'],
    "length" => $params['length'],
);
$url = gethostCHWeb() . "/" . HOMEHOST . "/api/personal/";
$dataApiPerson = json_decode(requestApi($url, $token, $authBasic, $dataParamPerson, 10), true);

if (is_array($dataApiPerson['DATA'])) {
    foreach ($dataApiPerson['DATA'] as $key => $p) {
        $pers_legajo = $p['Lega'];
        $pers_nombre = empty($p['ApNo']) ? 'Sin Nombre' : $p['ApNo'];
        $data[] = array(
            "pers_legajo" => $pers_legajo,
            "pers_nombre" => $pers_nombre,
            "CUIT" => $p['Datos']['CUIT'],
            "DNI" => $p['Datos']['Docu']
        );
    }
}
$LegajoDesde = '';
$LegajoHasta = '';

if ($params['toExcelAll'] == '1') {
    $Legajos = array_column($data, 'pers_legajo');
    $LegajoDesde = 1;
    $LegajoHasta = 99999999;
} else {
    $Legajos = array($data[0]['pers_legajo']);
}
$dataParametros = array(
    "FechaDesde" => "$FechaIni",
    "FechaHasta" => "$FechaFin",
    'Legajos' => ($Legajos),
    "LegajoDesde" => $LegajoDesde,
    "LegajoHasta" => $LegajoHasta,
    "TipoDePersonal" => '',
    'Empresa' => '',
    'Planta' => '',
    'Sector' => '',
    'Grupo' => '',
    'Sucursal' => '',
    'Seccion' => '',
);
$url = gethostCHWeb() . "/" . HOMEHOST . "/api/horasign/";

$dataApiHorarios['DATA'] = $dataApiHorarios['DATA'] ?? '';
$dataApiHorarios['MESSAGE'] = $dataApiHorarios['MESSAGE'] ?? '';

$dataApiHorarios = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);

$dataPer = '';

if (is_array($dataApiHorarios['DATA'])) {

    foreach ($dataApiHorarios['DATA'] as $v) {
        $fecha = new DateTime($v['Fecha']);
        $desdeInt = (intval(str_replace(':', '', $v['Desde'])));

        switch ($desdeInt) {
            case $desdeInt < 1359:
                $turno = 'Mañana';
                break;
            case $desdeInt < 1959:
                $turno = 'Tarde';
                break;
            case $desdeInt < 2359:
                $turno = 'Noche';
                break;

            default:
                $turno = '';
                break;
        }

        if ($params['toExcelAll'] == '1') {
            $filtroPersonal = filtrarObjetoArr($data, 'pers_legajo', $v['Legajo']);
            $dataPer = array(
                "Legajo" => $filtroPersonal[0]['pers_legajo'],
                "Nombre" => $filtroPersonal[0]['pers_nombre'],
                "Cuit" => $filtroPersonal[0]['CUIT'],
                "DNI" => $filtroPersonal[0]['DNI']
            );
        }
        $dataHorarios[] = array(
            "Codigo" => $v['Codigo'],
            "Horario" => $v['Horario'],
            "HorarioID" => $v['HorarioID'],
            "Fecha" => $fecha->format('d/m/Y'),
            "Dia" => $v['Dia'],
            "Feriado" => $v['Feriado'],
            "Laboral" => $v['Laboral'],
            "Desde" => $v['Desde'],
            "Hasta" => $v['Hasta'],
            "Descanso" => $v['Descanso'],
            "Legajo" => $v['Legajo'],
            "TipoAsign" => $v['TipoAsign'],
            "TipoAsignStr" => $v['TipoAsignStr'],
            "Turno" => $turno,
            "InfoLega" => $dataPer
        );
    }
} else {
    $dataApiHorarios['MESSAGE'] = $dataApiHorarios['DATA'];
}

if ($params['toPdfAll'] == '1') {
    file_put_contents("archivos/full_$params[time].json", json_encode($dataHorarios, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
    require 'reporte/index2.php';
    $params['toExcelAll'] == '';
    exit;
}
if ($params['toExcelAll'] == '1') {
    file_put_contents("archivos/full_$params[time].json", json_encode($dataHorarios, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);
    require 'toExcelAll.php';
    exit;
}


$json_dataHorarios = array(
    "recordsTotal" => count($dataHorarios ?? 0),
    "data" => $dataHorarios,
    "dataParametros" => $dataParametros,
    "Mensaje" => $dataApiHorarios['MESSAGE']
);

$json_data = array(
    "draw" => intval($params['draw'] ?? 0),
    "recordsTotal" => intval($dataApiPerson['TOTAL'] ?? 0),
    "recordsFiltered" => intval($dataApiPerson['TOTAL'] ?? 0),
    "CountData" => count($data),
    "data" => $data,
    "data2" => $json_dataHorarios,
    "dataParametros" => $dataParamPerson,
    "Mensaje" => $dataApiPerson['MESSAGE']
);

file_put_contents("archivos/$params[time].json", json_encode($json_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), LOCK_EX);

echo json_encode($json_data);
