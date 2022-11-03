<?php
$params    = $_REQUEST;
$data      = array();
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token     = sha1($_SESSION['RECID_CLIENTE']);
$params['start'] = $params['start'] ?? '0';
$params['length'] = $params['length'] ?? '99999';
$_POST['_dr'] = $_POST['_dr'] ?? '';
(!$_POST['_dr']) ? exit : '';

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni  = date('Ymd');
    $FechaFin  = date('Ymd');
}
$params['Per']      = $params['Per'] ?? '';
$params['Per2']     = $params['Per2'] ?? '';
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
$params['FicNovT']  = ($params['FicNovT']) ?? '';
$params['FicDiaL']  = ($params['FicDiaL']) ?? '';
$params['FicFalta'] = ($params['FicFalta']) ?? '';
$params['FicNovI']  = ($params['FicNovI']) ?? '';
$params['FicNovS']  = ($params['FicNovS']) ?? '';
$params['FicNovA']  = ($params['FicNovA']) ?? '';
$params['Filtros']  = ($params['Filtros']) ?? array();
$params['Fic3Nov']  = ($params['Fic3Nov']) ?? '';
$params['NovEx']  = ($params['NovEx']) ?? '';


function arrParams($params){
    if ($params) {
        return explode(',',$params); 
    }
    return '';
}

$Empr     = $params['Emp'] ? (arrParams($params['Emp'])) : explode(',', $_SESSION['EmprRol']);
$Per      = $params['Per'] ? arrParams($params['Per']) : array();
$Per2     = $params['Per2'] ? array($params['Per2']) : explode(',', $_SESSION['EstrUser']);
$Plan     = $params['Plan'] ? arrParams($params['Plan']) : explode(',', $_SESSION['PlanRol']);
$Sect     = $params['Sect'] ? arrParams($params['Sect']) : explode(',', $_SESSION['SectRol']);
$Grup     = $params['Grup'] ? arrParams($params['Grup']) : explode(',', $_SESSION['GrupRol']);
$Sucu     = $params['Sucur'] ? arrParams($params['Sucur']) : explode(',', $_SESSION['SucuRol']);
$Sec2     = $params['Sec2'] ? arrParams($params['Sec2']) : explode(',', $_SESSION['Sec2Rol']);

$Filtros = json_decode($params['Filtros'], true);

$FicNovT = $params['FicNovT'] ? arrParams($params['FicNovT']) : array();
$FicNovI = $params['FicNovI'] ? arrParams($params['FicNovI']) : array();
$FicNovS = $params['FicNovS'] ? arrParams($params['FicNovS']) : array();
$FicNovA = $params['FicNovA'] ? arrParams($params['FicNovA']) : array();
$FicDiaL = ($params['FicDiaL']) ? array(intval($params['FicDiaL'])) : [];
$Fic3Nov = ($params['Fic3Nov']) ? array(intval($params['Fic3Nov'])) : [];
$NovEx   = ($params['NovEx']) ? (intval($params['NovEx'])) : '';

$SoloFic = $Filtros['SoloFic'];
$LegDe   = $Filtros['LegDe'] ? ($Filtros['LegDe']) : '';
$LegHa   = $Filtros['LegHa'] ? ($Filtros['LegHa']) : '';

$FicFalta = $params['FicFalta'] ? array(intval($params['FicFalta'])) : [];
$LegTipo  = ($params['Tipo'] == '2') ? array("0") : array();

$Legajos = ($Per2) ? ($Per2) : $Per;
$Legajos = ($Per) ? ($Per) : $Legajos;

$dataParametros = array(
    // 'Lega' => array("30366320", "17653753", "20891138", "45416221", "29988600", "29408391"),
    'Lega'    => ($Legajos),
    // 'Lega' => array("20891138"),
    'Falta'   => '',
    'Empr'    => ($Empr),
    'Plan'    => ($Plan),
    'Sect'    => ($Sect),
    'Grup'    => ($Grup),
    'Sucu'    => ($Sucu),
    'Sec2'    => ($Sec2),
    'NovT'    => $FicNovT,
    'NovA'    => $FicNovA,
    'NovI'    => $FicNovI,
    'NovS'    => $FicNovS,
    'Nove'    => $Fic3Nov, // Filtrar por Novedad
    'NovEx'    => $NovEx, // Filtrar por Novedad
    'DiaL'    => ($FicDiaL),
    'LegaD'   => $LegDe,
    'LegaH'   => $LegHa,
    'Falta'   => $FicFalta,// Fichadas Inconsitentes
    'LegTipo' => $LegTipo,
    'FechIni' => FechaFormatVar($FechaIni, 'Y-m-d'),
    'FechFin' => FechaFormatVar($FechaFin, 'Y-m-d'),
    'start'   => intval($params['start']),
    'length'  => intval($params['length']),
    "onlyReg" => $SoloFic,
    "getReg"  => "1",
    "getNov"  => "1",
    "getONov" => "0",
    "getHor"  => "1",
);


$url = gethostCHWeb() . "/" . HOMEHOST . "/api/ficnovhor/";
$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';
$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);

// echo '<pre>';
// print_r(($dataApi)).exit;

/** Obtenemos los Tipos de Horas */
$dataParamThColu = array("Codi" => [], "ID" => [], "Desc" => "", "Desc2" => "", "start" => 0, "length" => 500); // Parametros
$urlTHColu = gethostCHWeb() . "/" . HOMEHOST . "/api/tipohora/"; // Url del endpoint de la api
$dataApiTHColu['DATA'] = $dataApiTHColu['DATA'] ?? '';
$dataApiTHColu['MESSAGE'] = $dataApiTHColu['MESSAGE'] ?? '';
$dataApiTHColu = json_decode(requestApi($urlTHColu, $token, $authBasic, $dataParamThColu, 10), true); // request

foreach ($dataApiTHColu['DATA'] as $key => $th) { // Creamos una array con los tipos de horas mas algunas claves vacios que serviran despues para hacer un merge con las horas calculadas.
    $THColu[] = array(
        'Hora'   => $th['Codi'],
        'Desc'   => $th['Desc'],
        'Desc2'  => $th['Desc2'],
        'Calc'   => '',
        'Hechas' => '',
        'Auto'   => ''
    );
}
// echo '<pre>';
// print_r($THColu).exit;
