<?php
require __DIR__ . '/../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$data = [];
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);
$params['length'] ??= '';
$params['_l'] ??= '';
(!$params['length']) ? exit : '';
(!$params['_l']) ? exit : '';

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni = date('Ymd');
    $FechaFin = date('Ymd');
}

$params['Emp'] ??= '';
$params['Plan'] ??= '';
$params['Sect'] ??= '';
$params['Sec2'] ??= '';
$params['Grup'] ??= '';
$params['Sucur'] ??= '';
$params['draw'] ??= '';
$params['FicFalta'] ??= '';
$params['Tipo'] ??= '';
$params['onlyReg'] ??= '';

$Empr = $params['Emp'] ?: [];
$Plan = $params['Plan'] ?: [];
$Sect = $params['Sect'] ?: [];
$Grup = $params['Grup'] ?: [];
$Sucu = $params['Sucur'] ?: [];
$Sec2 = $params['Sec2'] ?: [];
$FicFalta = $params['FicFalta'] ? [intval($params['FicFalta'])] : [];
$LegTipo = $params['Tipo'] ?: [];

$dataParametros = [
    'Lega' => [$params['_l']],
    'Falta' => $FicFalta,
    'Empr' => $Empr,
    'Plan' => $Plan,
    'Sect' => $Sect,
    'Grup' => $Grup,
    'Sucu' => $Sucu,
    'Sec2' => $Sec2,
    'LegTipo' => $LegTipo,
    'FechIni' => FechaFormatVar($FechaIni, 'Y-m-d'),
    'FechFin' => FechaFormatVar($FechaFin, 'Y-m-d'),
    'start' => intval($params['start']),
    'length' => intval($params['length']),
    'getReg' => 1,
    'onlyReg' => $params['onlyReg']
];
$url = $_SESSION['HOST_CHWEB'] . "/" . HOMEHOST . "/api/ficnovhor/";
$dataApi['DATA'] ??= '';
$dataApi['MESSAGE'] ??= '';

$dataApi = json_decode(
    requestApi(
        $url,
        $token,
        $authBasic,
        $dataParametros, 10),
    true);

if ($dataApi['DATA'] ?? []) {
    foreach ($dataApi['DATA'] as $v) {
        $ficHorario = $v['Tur']['ent'] . ' a ' . $v['Tur']['sal'];
        $ficHorario = ($v['Labo'] == '0') ? 'Franco' : $ficHorario;
        $ficHorario = ($v['Feri'] == '1') ? 'Feriado' : $ficHorario;

        $data[] = [
            'Fic_Lega' => $v['Lega'],
            'Fic_Nombre' => $v['ApNo'],
            'Fic_Fecha' => FechaFormatVar($v['Fech'], 'd/m/Y'),
            'Fic_Dia' => DiaSemana3($v['Fech']),
            'Fic_Horario' => $ficHorario,
            'Fic_Labo' => $v['Labo'],
            'Fich' => $v['Fich']
        ];
    }
}

$json_data = [
    "draw" => intval($params['draw'] ?? 0),
    "recordsTotal" => intval($dataApi['TOTAL'] ?? 0),
    "recordsFiltered" => intval($dataApi['TOTAL'] ?? 0),
    "data" => $data,
    "dataParametros" => $dataParametros,
    "Mensaje" => $dataApi['MESSAGE'] ?? ''
];

echo json_encode($json_data);
