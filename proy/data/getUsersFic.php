<?php
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
E_ALL();
timeZone();

function filterArr($array, $key, $valor) // Funcion para filtrar un objeto
{
    $a = [];
    if ($array && $key && $valor) {
        foreach ($array as $v) {
            if ($v[$key] === $valor) {
                $a[] = $v;
            }
        }
    }
    return $a;
}

$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);
$data = [];
$data2 = [];

(!$_SERVER['REQUEST_METHOD'] == 'POST') ? PrintRespuestaJson('error', 'Invalid Request Method') . exit : '';
require __DIR__ . '/../data/wcGetUserFic.php'; //  require where_conditions y variables
$qUser = "SELECT usr.id AS 'idUser', usr.nombre AS 'nameUser', usr.legajo AS 'legaUser', usr.usuario AS 'usuarioUsr' FROM usuarios usr INNER JOIN mod_roles mrol ON usr.rol=mrol.id_rol LEFT JOIN proy_tareas ptar ON usr.id = ptar.TareResp WHERE usr.legajo > 0";

$qCount = "SELECT COUNT(*) as 'count' FROM usuarios usr INNER JOIN mod_roles mrol ON usr.rol=mrol.id_rol LEFT JOIN proy_tareas ptar ON usr.id = ptar.TareResp WHERE usr.legajo > 0";

if ($w_c ?? ''): // if where_conditions
    $qUser .= $w_c;
    $qCount .= $w_c;
endif;

$qUser .= " ORDER BY `usr`.`nombre` ASC";
$totalRecords = array_pdoQuery($qCount);
$count = count($totalRecords);
$r = array_pdoQuery($qUser);

$tareas = [];

if (empty($params['sinTar'])) {
    $q = "SELECT TareResp, TareID, TareIni, TareFin, TarePlano, TareProy, TareProc, TareHorHoras, TareEsta, ProyNom  FROM proy_tareas 
        LEFT JOIN proy_tare_horas ON proy_tareas.TareID = proy_tare_horas.TareHorID
        INNER JOIN proy_proyectos ON proy_tareas.TareProy = proy_proyectos.ProyID
        WHERE DATE_FORMAT(TareIni, '%Y-%m-%d') = '$FiltroAsignTarFechas' AND TareEsta = '0' ORDER BY proy_tareas.TareIni ASC";
    $tareas = array_pdoQuery($q);
}
$dataParametros = [
    "getNov" => 0,
    "getHor" => 0,
    'FechIni' => $FiltroAsignTarFechas,
    'FechFin' => $FiltroAsignTarFechas,
    'start' => 0,
    'length' => 9999,
    'getReg' => 1,
    'onlyReg' => 1
];

$url = gethostCHWeb() . "/" . HOMEHOST . "/api/ficnovhor/";

$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);

foreach ($r as $key => $row) {

    $idUser = $row['idUser'];
    $nameUser = $row['nameUser'];
    $legaUser = $row['legaUser'];
    $usuarioUsr = $row['usuarioUsr'];

    $arrUser = [
        "id" => intval($idUser),
        "nombre" => utf8str($nameUser),
        "legajo" => intval($legaUser),
    ];

    $arrFic = filterArr($dataApi['DATA'] ?? [], 'Lega', $row['legaUser']);
    $arrTar = filterArr($tareas, 'TareResp', $row['idUser']);

    $arrFichadas = '';
    if ($params['presentes'] && ($arrFic) && $arrFic[0]) {
        $arrFichadas = $arrFic[0];
        $data[] = [
            'arrFic' => $arrFichadas,
            'arrTar' => $arrTar,
            'arrUser' => $arrUser,
            'date' => $FiltroAsignTarFechas
        ];
    }

    if (!$params['presentes']) {

        if ($arrFic && $arrFic[0]) {
            $arrFichadas = $arrFic[0];
        }

        $data2[] = [
            'arrFic' => $arrFichadas,
            'arrTar' => $arrTar,
            'arrUser' => $arrUser,
            'date' => $FiltroAsignTarFechas
        ];
    }
}

if (!$params['presentes']) {
    foreach ($data2 as $key => $v) {

        if (!$v['arrFic']) {
            $data[] = [
                'arrFic' => $v['arrFic'],
                'arrTar' => $v['arrTar'],
                'arrUser' => $v['arrUser'],
                'date' => $FiltroAsignTarFechas
            ];
        }

    }
}

$tiempo_fin = microtime(true);
$tiempo = $tiempo_fin - $tiempo_inicio;

$json_data = [
    "data" => $data,
    "tiempo" => round($tiempo, 2),
];
echo json_encode($json_data);
exit;
