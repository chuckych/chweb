<?php
require __DIR__ . '../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$data = array();

$params['length'] = $params['length'] ?? '';
(!$params['length']) ? exit : '';

$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni = date('Ymd');
    $FechaFin = date('Ymd');
}
$params['_f'] = $params['_f'] ?? '';
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
    'Lega' => $Legajos,
    'Falta' => $FicFalta,
    'Empr' => ($Empr),
    'Plan' => ($Plan),
    'Sect' => ($Sect),
    'Grup' => ($Grup),
    'Sucu' => ($Sucu),
    'Sec2' => ($Sec2),
    'LegTipo' => ($LegTipo),
    'FechIni' => FechaFormatVar($params['_f'], 'Y-m-d'),
    'FechFin' => FechaFormatVar($params['_f'], 'Y-m-d'),
    'start' => intval($params['start']),
    'length' => intval($params['length']),
    'getReg' => 1,
    'onlyReg' => intval($params['onlyReg'])
);

$url = gethostCHWeb() . "/" . HOMEHOST . "/api/ficnovhor/";

$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';

if ($params['_l']) {
    $dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);
}

if ($dataApi['DATA']) {
    foreach ($dataApi['DATA'] as $v) {
        $ficHorario = $v['Tur']['ent'] . ' a ' . $v['Tur']['sal'];
        $ficHorario = ($v['Labo'] == '0') ? 'Franco' : $ficHorario;
        $ficHorario = ($v['Feri'] == '1') ? 'Feriado' : $ficHorario;

        $data[] = array(
            'Fic_Lega' => $v['Lega'],
            'Fic_Nombre' => $v['ApNo'],
            'Fic_Fecha' => FechaFormatVar($v['Fech'], 'd/m/Y'),
            'Fic_Dia' => DiaSemana3($v['Fech']),
            'Fic_Horario' => $ficHorario,
            'Fic_Labo' => $v['Labo'],
            'Fich' => $v['Fich']
        );
    }
}
$json_data = array(
    "draw" => intval($params['draw'] ?? 0),
    "recordsTotal" => intval($dataApi['TOTAL'] ?? 0),
    "recordsFiltered" => intval($dataApi['TOTAL'] ?? 0),
    "data" => $data,
    "dataParametros" => $dataParametros,
    "Mensaje" => $dataApi['MESSAGE']
);

echo json_encode($json_data);
