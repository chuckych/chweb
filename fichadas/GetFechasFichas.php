<?php
require __DIR__ . '../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$data = [];
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);

$params['length'] = $params['length'] ?? '';
$_POST['_dr'] = $_POST['_dr'] ?? '';
(!$_POST['_dr']) ? exit : '';

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni = date('Ymd');
    $FechaFin = date('Ymd');
}
$params['Per'] = $params['Per'] ?? '';
$params['Per2'] = $params['Per2'] ?? '';
$params['Emp'] = $params['Emp'] ?? '';
$params['Plan'] = $params['Plan'] ?? '';
$params['Sect'] = $params['Sect'] ?? '';
$params['Sec2'] = $params['Sec2'] ?? '';
$params['Grup'] = $params['Grup'] ?? '';
$params['Sucur'] = $params['Sucur'] ?? '';
$params['_l'] = $params['_l'] ?? $data = array();
$params['draw'] = $params['draw'] ?? '';
$params['FicFalta'] = $params['FicFalta'] ?? '';
$params['Tipo'] = ($params['Tipo']) ?? '';
$params['onlyReg'] = ($params['onlyReg']) ?? '';

$Empr = setParamsOrSession($params['Emp'], $_SESSION['EmprRol']);
$Per = $params['Per'] ? ($params['Per']) : array();
$Per2 = $params['Per2'] ? array($params['Per2']) : explodeSession($_SESSION['EstrUser']);
$Plan = setParamsOrSession($params['Plan'], $_SESSION['PlanRol']);
$Sect = setParamsOrSession($params['Sect'], $_SESSION['SectRol']);
$Grup = setParamsOrSession($params['Grup'], $_SESSION['GrupRol']);
$Sucu = setParamsOrSession($params['Sucur'], $_SESSION['SucuRol']);
$Sec2 = setParamsOrSession($params['Sec2'], $_SESSION['Sec2Rol']);
$FicFalta = $params['FicFalta'] ? array(intval($params['FicFalta'])) : [];
$LegTipo = $params['Tipo'] ? $params['Tipo'] : array();

$Legajos = ($Per2) ? ($Per2) : $Per;
$Legajos = ($Per) ? ($Per) : $Legajos;

$dataParametros = array(
    'Lega' => ($Legajos),
    'Falta' => $FicFalta,
    'Empr' => ($Empr),
    'Plan' => ($Plan),
    'Sect' => ($Sect),
    'Grup' => ($Grup),
    'Sucu' => ($Sucu),
    'Sec2' => ($Sec2),
    'LegTipo' => ($LegTipo),
    'FechIni' => FechaFormatVar($FechaIni, 'Y-m-d'),
    'FechFin' => FechaFormatVar($FechaFin, 'Y-m-d'),
    // 'start'   => 0,
    // 'length'  => 9999,
    'start' => intval($params['start']),
    'length' => intval($params['length']),
    'getReg' => 1,
    'onlyReg' => ($params['onlyReg'])
);

$url = gethostCHWeb() . "/" . HOMEHOST . "/api/ficdatafech/";
$DATA = $dataApi['DATA'] ?? [];
$MESSAGE = $dataApi['MESSAGE'] ?? '';

$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);

if ($DATA) {
    foreach ($DATA as $row) {
        $data[] = array(
            'FechaFormat' => FechaFormatVar($row['Fecha'], 'd/m/Y'),
            'Dia' => DiaSemana3($row['Fecha']),
            'Fecha' => FechaFormatVar($row['Fecha'], 'Y-m-d'),
        );
    }
}
$json_data = array(
    "draw" => intval($params['draw'] ?? 0),
    "recordsTotal" => intval($dataApi['TOTAL'] ?? 0),
    "recordsFiltered" => intval($dataApi['TOTAL'] ?? 0),
    "data" => $data,
    "dataParametros" => $dataParametros,
    "Mensaje" => $MESSAGE
);

echo json_encode($json_data);
