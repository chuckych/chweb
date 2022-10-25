<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

$params    = $_REQUEST;
$data      = array();
$authBasic = base64_encode('chweb:'.HOMEHOST);
$token     = sha1($_SESSION['RECID_CLIENTE']);
$params['length'] = $params['length'] ?? '';
$_POST['_dr'] = $_POST['_dr'] ?? '';
(!$_POST['_dr']) ? exit : '';

// print_r($_SESSION['EmprRol']).exit;

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni  = date('Ymd');
    $FechaFin  = date('Ymd');
}
$params['Per']      = $params['Per'] ?? '';
$params['Per2']      = $params['Per2'] ?? '';
$params['Emp']      = $params['Emp'] ?? '';
$params['Plan']     = $params['Plan'] ?? '';
$params['Sect']     = $params['Sect'] ?? '';
$params['Sec2']     = $params['Sec2'] ?? '';
$params['Grup']     = $params['Grup'] ?? '';
$params['Sucur']    = $params['Sucur'] ?? '';
$params['_l']       = $params['_l'] ?? $data = array();
$params['draw']     = $params['draw'] ?? '';
$params['FicFalta'] = $params['FicFalta'] ?? '';
$params['Tipo']     = ($params['Tipo']) ?? '';
$params['onlyReg']  = ($params['onlyReg']) ?? '';

$Empr     = $params['Emp'] ? ($params['Emp']) : explode(',', $_SESSION['EmprRol']);
$Per      = $params['Per'] ? ($params['Per']) : array();
$Per2     = $params['Per2'] ? array($params['Per2']) : explode(',', $_SESSION['EstrUser']);
$Plan     = $params['Plan'] ? $params['Plan'] : explode(',', $_SESSION['PlanRol']);
$Sect     = $params['Sect'] ? $params['Sect'] : explode(',', $_SESSION['SectRol']);
$Grup     = $params['Grup'] ? $params['Grup'] : explode(',', $_SESSION['GrupRol']);
$Sucu     = $params['Sucur'] ? $params['Sucur'] : explode(',', $_SESSION['SucuRol']);
$Sec2     = $params['Sec2'] ? $params['Sec2'] : explode(',', $_SESSION['Sec2Rol']);
$FicFalta = $params['FicFalta'] ? array(intval($params['FicFalta'])) : [];
$LegTipo  = $params['Tipo'] ? $params['Tipo'] : array();

$Legajos = ($Per2) ? ($Per2) : $Per;
$Legajos = ($Per) ? ($Per) : $Legajos;

// print_r($Legajos); exit;

$dataParametros = array(
    'Lega'    => ($Legajos),
    'Falta'   => $FicFalta,
    'Empr'    => ($Empr),
    'Plan'    => ($Plan),
    'Sect'    => ($Sect),
    'Grup'    => ($Grup),
    'Sucu'    => ($Sucu),
    'Sec2'    => ($Sec2),
    'LegTipo' => ($LegTipo),
    'FechIni' => FechaFormatVar($FechaIni, 'Y-m-d'),
    'FechFin' => FechaFormatVar($FechaFin, 'Y-m-d'),
    // 'start'   => 0,
    // 'length'  => 9999,
    'start'   => intval($params['start']),
    'length'  => intval($params['length']),
    'getReg'  => 1,
    'onlyReg'  => $params['onlyReg']
);

// $parametros = http_build_query($dataParametros, '', '&');
// $url = "http://localhost/chweb/api/ficdata/";
// $url = "$_SERVER[HTTP_ORIGIN]/".HOMEHOST."/api/ficdata/";
$url = gethostCHWeb()."/".HOMEHOST."/api/ficdata/";
// print_r($url).exit;

$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';

// if ($params['_l']) {
$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);
// }
// print_r($dataParametros);
// print_r($dataApi).exit;

if ($dataApi['DATA']) {
    foreach ($dataApi['DATA'] as $row) {
        $pers_legajo   = $row['Lega'];
        $pers_nombre   = empty($row['ApNo']) ? 'Sin Nombre' : $row['ApNo'];
        $data[] = array(
            'pers_legajo' =>  $pers_legajo,
            'pers_nombre' => $pers_nombre,
        );
    }
}
$json_data = array(
    "draw"            => intval($params['draw'] ?? 0),
    "recordsTotal"    => intval($dataApi['TOTAL'] ?? 0),
    "recordsFiltered" => intval($dataApi['TOTAL'] ?? 0),
    "data"            => $data,
    "dataParametros"  => $dataParametros,
    "Mensaje" => $dataApi['MESSAGE'] 
);

echo json_encode($json_data);
