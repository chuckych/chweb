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
$params['_l'] = $params['_l'] ?? '';
(!$params['length']) ? exit : '';
(!$params['_l']) ? exit : '';
if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni  = date('Ymd');
    $FechaFin  = date('Ymd');
}
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

$Empr     = $params['Emp'] ? ($params['Emp']) : array();
$Plan     = $params['Plan'] ? $params['Plan'] : array();
$Sect     = $params['Sect'] ? $params['Sect'] : array();
$Grup     = $params['Grup'] ? $params['Grup'] : array();
$Sucu     = $params['Sucur'] ? $params['Sucur'] : array();
$Sec2     = $params['Sec2'] ? $params['Sec2'] : array();
$FicFalta = $params['FicFalta'] ? array(intval($params['FicFalta'])) : [];
$LegTipo  = $params['Tipo'] ? $params['Tipo'] : array();

$dataParametros = array(
    'Lega'    => array($params['_l']),
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
    'start'   => intval($params['start']),
    'length'  => intval($params['length']),
    'getReg'  => 1,
    'onlyReg'  => $params['onlyReg']
);
$url = gethostCHWeb()."/".HOMEHOST."/api/ficnovhor/";
// file_put_contents('url.log', $url."\n", FILE_APPEND | LOCK_EX);

$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';

$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);
// print_r($dataParametros);
// print_r($dataApi).exit;


if ($dataApi['DATA']) {
    foreach ($dataApi['DATA'] as $v) {
        $ficHorario = $v['Tur']['ent'] . ' a ' . $v['Tur']['sal'];
        $ficHorario = ($v['Labo'] == '0') ? 'Franco' : $ficHorario;
        $ficHorario = ($v['Feri'] == '1') ? 'Feriado' : $ficHorario;

        $data[] = array(
            'Fic_Lega'    => $v['Lega'],
            'Fic_Nombre'  => $v['ApNo'],
            'Fic_Fecha'   => FechaFormatVar($v['Fech'], 'd/m/Y'),
            'Fic_Dia'     => DiaSemana3($v['Fech']),
            'Fic_Horario' => $ficHorario,
            // 'Fic_FichC'   => $v['FichC'],
            'Fic_Labo'   => $v['Labo'],
            // 'Fichadas'    => implodeArrayByKey($v['Fich'], 'HoRe', ','),
            'Fich'        => $v['Fich']
        );
    }
}
// echo json_encode($dataParametros, JSON_PRETTY_PRINT);
// exit;
// sleep(1);
$json_data = array(
    "draw"            => intval($params['draw'] ?? 0),
    "recordsTotal"    => intval($dataApi['TOTAL'] ?? 0),
    "recordsFiltered" => intval($dataApi['TOTAL'] ?? 0),
    "data"            => $data,
    "dataParametros"  => $dataParametros,
    "Mensaje" => $dataApi['MESSAGE'] 
);

echo json_encode($json_data);
