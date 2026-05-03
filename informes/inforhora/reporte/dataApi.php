<?php
$params = $_REQUEST;
$data = [];
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);

$params['start'] ??= '0';
$params['length'] ??= '99999';
$_POST['_dr'] ??= '';

(!$_POST['_dr']) ? exit : '';

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni = date('Ymd');
    $FechaFin = date('Ymd');
}

$params['Per'] ??= '';
$params['Per2'] ??= '';
$params['Emp'] ??= '';
$params['Plan'] ??= '';
$params['Sect'] ??= '';
$params['Sec2'] ??= '';
$params['Grup'] ??= '';
$params['Sucur'] ??= '';
$params['_l'] ??= [];
$params['draw'] ??= '';
$params['FicFalta'] ??= '';
$params['Tipo'] ??= '';
$params['FicNovT'] ??= '';
$params['FicDiaL'] ??= '';
$params['FicNovI'] ??= '';
$params['FicNovS'] ??= '';
$params['FicNovA'] ??= '';
$params['Filtros'] ??= [];
$params['Fic3Nov'] ??= '';
$params['NovEx'] ??= '';

function arrParams($params)
{
    return trim($params) ? explode(',', $params) : [];
}

$Empr = $params['Emp'] ? arrParams($params['Emp']) : explode(',', $_SESSION['EmprRol']);
$Per = $params['Per'] ? arrParams($params['Per']) : [];
// print_r($Per);
// exit;
$Per2 = $params['Per2'] ? [$params['Per2']] : explode(',', $_SESSION['EstrUser']);
$Plan = $params['Plan'] ? arrParams($params['Plan']) : explode(',', $_SESSION['PlanRol']);
$Sect = $params['Sect'] ? arrParams($params['Sect']) : explode(',', $_SESSION['SectRol']);
$Grup = $params['Grup'] ? arrParams($params['Grup']) : explode(',', $_SESSION['GrupRol']);
$Sucu = $params['Sucur'] ? arrParams($params['Sucur']) : explode(',', $_SESSION['SucuRol']);
$Sec2 = $params['Sec2'] ? arrParams($params['Sec2']) : explode(',', $_SESSION['Sec2Rol']);

$Filtros = is_array($params['Filtros']) ? $params['Filtros'] : (json_decode($params['Filtros'], true) ?: []);

$FicNovT = $params['FicNovT'] ? arrParams($params['FicNovT']) : [];
$FicNovI = $params['FicNovI'] ? arrParams($params['FicNovI']) : [];
$FicNovS = $params['FicNovS'] ? arrParams($params['FicNovS']) : [];
$FicNovA = $params['FicNovA'] ? arrParams($params['FicNovA']) : [];
$FicDiaL = $params['FicDiaL'] ? [intval($params['FicDiaL'])] : [];
$Fic3Nov = $params['Fic3Nov'] ? [intval($params['Fic3Nov'])] : [];
$NovEx = $params['NovEx'] ? intval($params['NovEx']) : '';

$SoloFic = $Filtros['SoloFic'] ?? null;
$LegDe = $Filtros['LegDe'] ?? '';
$LegHa = $Filtros['LegHa'] ?? '';

$FicFalta = $params['FicFalta'] ? [intval($params['FicFalta'])] : [];
$LegTipo = ($params['Tipo'] == '2') ? ["0"] : [];

$dataParametros = [
    'Lega' => $Per,
    'Empr' => $Empr,
    'Plan' => $Plan,
    'Sect' => $Sect,
    'Grup' => $Grup,
    'Sucu' => $Sucu,
    'Sec2' => $Sec2,
    'NovT' => $FicNovT,
    'NovA' => $FicNovA,
    'NovI' => $FicNovI,
    'NovS' => $FicNovS,
    'Nove' => $Fic3Nov,
    'NovEx' => $NovEx,
    'DiaL' => $FicDiaL,
    'LegaD' => $LegDe,
    'LegaH' => $LegHa,
    'Falta' => $FicFalta,
    'LegTipo' => $LegTipo,
    'FechIni' => FechaFormatVar($FechaIni, 'Y-m-d'),
    'FechFin' => FechaFormatVar($FechaFin, 'Y-m-d'),
    'start' => intval($params['start']),
    'length' => 99999999,
    "onlyReg" => 0,
    "getReg" => "0",
    "getNov" => "0",
    "getONov" => "0",
    "getHor" => "1",
];

$t_api1 = microtime(true);
$url = $_SESSION['HOST_CHWEB'] . "/" . HOMEHOST . "/api/ficnovhor/";

$dataApi = requestApi($url, $token, $authBasic, $dataParametros, 20);
$dataApi = parseApiResponse($dataApi);
$dataApi['DATA'] ??= [];

$t_api1_end = microtime(true);

/** Obtenemos los Tipos de Horas */
$t_api2 = microtime(true);
$dataParamThColu = ["Codi" => [], "ID" => [], "Desc" => "", "Desc2" => "", "start" => 0, "length" => 500];
$urlTHColu = $_SESSION['HOST_CHWEB'] . "/" . HOMEHOST . "/api/tipohora/";
$dataApiTHColu = requestApi($urlTHColu, $token, $authBasic, $dataParamThColu, 20);
$dataApiTHColu = parseApiResponse($dataApiTHColu);
$dataApiTHColu['DATA'] ??= [];

$t_api2_end = microtime(true);
// Procesar tipos de horas para usar en el reporte
$tiposHora = [];
if (!empty(($dataApiTHColu['DATA']))) {
    foreach ($dataApiTHColu['DATA'] as $th) {
        $tiposHora[] = [
            'Codi' => $th['Codi'],
            'Desc' => $th['Desc'],
            'Desc2' => $th['Desc2'],
            'Colu' => $th['Colu']
        ];
    }
}

// Procesar datos de fichas
$fichasData = [];
if (!empty($dataApi['DATA'])) {
    $fichasData = $dataApi['DATA'];
}

// Registrar tiempos de API en variables globales para el log
$GLOBALS['api_time_fichas'] = ($t_api1_end - $t_api1) * 1000;
$GLOBALS['api_time_tipohora'] = ($t_api2_end - $t_api2) * 1000;
$GLOBALS['api_count_fichas'] = count($fichasData);
$GLOBALS['api_count_tipohora'] = count($tiposHora);