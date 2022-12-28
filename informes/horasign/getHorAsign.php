<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

$params    = $_REQUEST;
$data      = array();
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token     = sha1($_SESSION['RECID_CLIENTE']);
// $token     = 'a2aa1fb35c3028c5c8a179a2ac1968a14a5938ed';
$params['length'] = $params['length'] ?? '';
$params['_l'] = $params['_l'] ?? '';
// (!$params['length']) ? exit : '';
// (!$params['_l']) ? exit : '';

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
$LegTipo  = $params['Tipo'] ? $params['Tipo'] : array();

$dataParametros = array(
    "FechaDesde"     => "2022-12-01",
    "FechaHasta"     => "2022-12-11",
    'Legajos'        => array($params['_l']),
    // "LegajoDesde"    => "1",
    // "LegajoHasta"    => "29988600",
    "TipoDePersonal" => "",
    'Empresa'        => "",
    'Planta'         => "",
    'Sector'         => "",
    'Grupo'          => "",
    'Sucursal'       => "",
    'Seccion'        => "",
);
$url = gethostCHWeb() . "/" . HOMEHOST . "/api/horasign/";
// $url = "http://localhost/".HOMEHOST."/api/horasign/";


$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';
$dataApiPersonal['DATA'] = $dataApiPersonal['DATA'] ?? '';
$dataApiPersonal['MESSAGE'] = $dataApiPersonal['MESSAGE'] ?? '';

$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);

if ($dataApi['DATA']) {
    // $dataParamPerson = array(
    //     "Nume"     => array($params['_l']),
    //     "getDatos" => 1,
    //     "start"    => 0,
    //     "length"   => 9999,
    // );
    // $url = gethostCHWeb() . "/" . HOMEHOST . "/api/personal/";
    // $dataApiPersonal = json_decode(requestApi($url, $token, $authBasic, $dataParamPerson, 10), true);

    // foreach ($dataApiPersonal['DATA'] as $key => $p) {
    //    $person[] = array(
    //         "Lega" => $p['Lega'],
    //         "ApNo" => $p['ApNo'],
    //         "CUIT" => $p['Datos']['CUIT'],
    //         "DNI"  => $p['Datos']['Docu']
    //    );
    // }

    foreach ($dataApi['DATA'] as $v) {
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

        // $filtroPerson = filtrarObjetoArr($person, 'Lega', $v['Legajo']);

        $data[] = array(
            "Codigo"       => $v['Codigo'],
            "Horario"      => $v['Horario'],
            "HorarioID"    => $v['HorarioID'],
            "Fecha"        => $fecha->format('d/m/Y'),
            "Dia"          => $v['Dia'],
            "Feriado"      => $v['Feriado'],
            "Laboral"      => $v['Laboral'],
            "Desde"        => $v['Desde'],
            "Hasta"        => $v['Hasta'],
            "Descanso"     => $v['Descanso'],
            "Legajo"       => $v['Legajo'],
            "TipoAsign"    => $v['TipoAsign'],
            "TipoAsignStr" => $v['TipoAsignStr'],
            "Turno"        => $turno,
            // "InfoLega"     => $filtroPerson[0]
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
exit;
