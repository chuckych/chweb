<?php
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
E_ALL();

$estruct = FusNuloPOST('estruct', '');
$q = FusNuloPOST('q', '');

$request       = Flight::request();
$method        = $request->method;
$params        = $request->data;
$authBasic     = base64_encode('chweb:' . HOMEHOST);
$token         = sha1($_SESSION['RECID_CLIENTE']);
$FilterEstruct = '';
$data          = array();

$params['Per']   = $params['Per'] ?? '';
$params['Emp']   = $params['Emp'] ?? '';
$params['Plan']  = $params['Plan'] ?? '';
$params['Sect']  = $params['Sect'] ?? '';
$params['Sec2']  = $params['Sec2'] ?? '';
$params['Grup']  = $params['Grup'] ?? '';
$params['Sucur'] = $params['Sucur'] ?? '';
$params['Tipo']  = ($params['Tipo']) ?? '';
$params['Regla'] = ($params['Regla']) ?? '';

$userEmpr = explode(',', $_SESSION['EmprRol']) ?? '';
$Empr     = $params['Emp'] ? ($params['Emp']) : $userEmpr;

$Per      = $params['Per'] ? ($params['Per']) : array();
$Per2     = $params['Per2'] ? array($params['Per2']) : explode(',', $_SESSION['EstrUser']);

$userPlan = explode(',', $_SESSION['PlanRol']) ?? '';
$Plan     = $params['Plan'] ? $params['Plan'] : $userPlan;

$userSect = explode(',', $_SESSION['SectRol']) ?? '';
$Sect     = $params['Sect'] ? $params['Sect'] : $userSect;

$userGrup = explode(',', $_SESSION['GrupRol']) ?? '';
$Grup     = $params['Grup'] ? $params['Grup'] : $userGrup;

$userSucu = explode(',', $_SESSION['SucuRol']) ?? '';
$Sucu     = $params['Sucur'] ? $params['Sucur'] : $userSucu;

$userSec2 = explode(',', $_SESSION['Sec2Rol']) ?? '';
$Sec2     = $params['Sec2'] ? $params['Sec2'] : $userSec2;

$userConv = explode(',', $_SESSION['ConvRol']) ?? '';
$Conv     = $params['Conv'] ? $params['Conv'] : $userConv;

$Tare     = $params['Tare'] ? $params['Tare'] : '';

switch ($params['Tipo']) {
    case '2':
        $LegTipo = array('0');
        break;
    case '1':
        $LegTipo = array('1');
        break;
    default:
        $LegTipo = array();
        break;
}

$RegCH    = $params['Regla'] ? $params['Regla'] : '';
$Legajos = ($Per) ? ($Per) : array();

$dataApiPerson['DATA'] = $dataApiPerson['DATA'] ?? '';
$dataApiPerson['MESSAGE'] = $dataApiPerson['MESSAGE'] ?? '';
$qLega = $estruct == 'Lega' ? $q : '';


switch ($estruct) {
    case 'Empr':
        $Empr = ($params['Emp'] && !$userEmpr) ? '' : $userEmpr;
        $FicEstruct    = 'PERSONAL.LegEmpr';
        $ColEstruc     = 'EMPRESAS';
        $ColEstrucDesc = 'EMPRESAS.EmpRazon';
        $ColEstrucCod  = 'EMPRESAS.EmpCodi';
        break;
    case 'Plan':
        $Plan     = ($params['Plan'] && !$userPlan) ? '' : $userPlan;
        $FicEstruct    = 'PERSONAL.LegPlan';
        $ColEstruc     = 'PLANTAS';
        $ColEstrucDesc = 'PLANTAS.PlaDesc';
        $ColEstrucCod  = 'PLANTAS.PlaCodi';
        break;
    case 'Grup':
        $Grup     = ($params['Grup'] && !$userGrup) ? '' : $userGrup;
        $FicEstruct    = 'PERSONAL.LegGrup';
        $ColEstruc     = 'GRUPOS';
        $ColEstrucDesc = 'GRUPOS.GruDesc';
        $ColEstrucCod  = 'GRUPOS.GruCodi';
        break;
    case 'Sect':
        $Sect     = ($params['Sect'] && !$userSect) ? '' : $userSect;
        $FicEstruct    = 'PERSONAL.LegSect';
        $ColEstruc     = 'SECTORES';
        $ColEstrucDesc = 'SECTORES.SecDesc';
        $ColEstrucCod  = 'SECTORES.SecCodi';
        break;
    case 'Sucu':
        $Sucu = ($params['Sucur'] && !$userSucu) ? '' : $userSucu;
        $FicEstruct    = 'PERSONAL.LegSucu';
        $ColEstruc     = 'SUCURSALES';
        $ColEstrucDesc = 'SUCURSALES.SucDesc';
        $ColEstrucCod  = 'SUCURSALES.SucCodi';
        break;
    case 'Tare':
        $Tare     = '';
        $FicEstruct    = 'PERSONAL.LegTareProd';
        $ColEstruc     = 'TAREAS';
        $ColEstrucDesc = 'TAREAS.TareDesc';
        $ColEstrucCod  = 'TAREAS.TareCodi';
        break;
    case 'Conv':
        $Conv = ($params['Conv'] && !$userConv) ? '' : $userConv;
        $FicEstruct    = 'PERSONAL.LegConv';
        $ColEstruc     = 'CONVENIO';
        $ColEstrucDesc = 'CONVENIO.ConDesc';
        $ColEstrucCod  = 'CONVENIO.ConCodi';
        break;
    case 'Regla':
        $RegCH = '';
        $FicEstruct    = 'PERSONAL.LegRegCH';
        $ColEstruc     = 'REGLASCH';
        $ColEstrucDesc = 'REGLASCH.RCDesc';
        $ColEstrucCod  = 'REGLASCH.RCCodi';
        break;
    case 'Lega':
        $FicEstruct    = 'PERSONAL.LegNume';
        $ColEstruc     = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod  = 'PERSONAL.LegNume';
        break;
    case 'Sec2':
        $Sec2 = ($params['Sec2'] && !$userSec2) ? '' : $userSec2;
        $FicEstruct    = 'PERSONAL.LegNume';
        $ColEstruc     = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod  = 'PERSONAL.LegNume';
        break;
    case 'Tipo':
        $LegTipo = '';
        $ColEstruc     = 'PERSONAL';
        $ColEstrucCod  = 'PERSONAL.LegTipo';
        break;

    default:
        # code...
        break;
}


$dataParamPerson = array(
    "Estruct"    => "$estruct",
    "Desc"       => "$q",
    "Sector"     => ($Sect),
    "Nume"       => ($Legajos),
    "ApNoNume"   => "$qLega",
    "getDatos"   => 0,
    "getEstruct" => 0,
    "Baja"       => array("0"),
    "Empr"       => ($Empr),
    "Plan"       => ($Plan),
    "Sect"       => ($Sect),
    "Sec2"       => ($Sec2),
    "Grup"       => ($Grup),
    "Sucu"       => ($Sucu),
    "Conv"       => ($Conv),
    "TareProd"   => ($Tare),
    "RegCH"      => ($RegCH),
    "Tipo"       => ($LegTipo),
    "start"      => 0,
    "length"     => 500,
);

$url = gethostCHWeb() . "/" . HOMEHOST . "/api/personestruct/";

$FiltroQ  = (!empty($q)) ? "AND CONCAT($ColEstrucCod, $ColEstrucDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';

switch ($estruct) {
    case 'Lega':
        $dataApiPerson = json_decode(requestApi($url, $token, $authBasic, $dataParamPerson, 10), true);
        if ($dataApiPerson['RESPONSE_CODE'] == 200) {
            if (is_array($dataApiPerson['DATA'])) {
                foreach ($dataApiPerson['DATA'] as $key => $v) {
                    $data[] = array(
                        'Cod'     => $v['Cod'],
                        'CUIL'    => $v['CUIL'],
                        'Desc'    => $v['Desc'],
                        'Docu'    => $v['Docu'],
                        'Estruct' => $estruct
                    );
                }
            } else {
                $data[] = array(
                    'Cod'     => '',
                    'CUIL'    => '',
                    'Desc'    => '',
                    'Docu'    => '',
                    'Estruct' => '',
                );
            }
        }
        Flight::json($data) . exit;
        break;
    case 'Sec2':
        $dataApiPerson = json_decode(requestApi($url, $token, $authBasic, $dataParamPerson, 10), true);
        if ($dataApiPerson['RESPONSE_CODE'] == 200) {
            if (is_array($dataApiPerson['DATA'])) {
                foreach ($dataApiPerson['DATA'] as $key => $v) {
                    $data[] = array(
                        'Cod'      => $v['Cod'],
                        'Desc'     => $v['Desc'],
                        'Count'    => $v['Count'],
                        'Sect'     => $v['Sect'],
                        'SectDesc' => $v['SectDesc'],
                        'Estruct'  => $estruct
                    );
                }
            } else {
                $data[] = array(
                    'Cod'      => '',
                    'Desc'     => '',
                    'Count'    => '',
                    'Sect'     => '',
                    'SectDesc' => '',
                    'Estruct'  => '',
                );
            }
        }
        Flight::json($data) . exit;
        break;
    default:
        $dataApiPerson = json_decode(requestApi($url, $token, $authBasic, $dataParamPerson, 10), true);
        if ($dataApiPerson['RESPONSE_CODE'] == 200) {
            if (is_array($dataApiPerson['DATA'])) {
                foreach ($dataApiPerson['DATA'] as $key => $v) {
                    $data[] = array(
                        'Cod'     => $v['Cod'],
                        'Desc'    => $v['Desc'],
                        'Count'   => $v['Count'],
                        'Estruct' => $estruct
                    );
                }
            } else {
                $data[] = array(
                    'Cod'     => '',
                    'Desc'    => '',
                    'Count'   => '',
                    'Estruct' => '',
                );
            }
        }
        Flight::json($data) . exit;
        break;
}