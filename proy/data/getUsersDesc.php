<?php
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
E_ALL();
timeZone();
$tiempo_inicio = microtime(true);

$authBasic = base64_encode('chweb:' . HOMEHOST);
$token     = sha1($_SESSION['RECID_CLIENTE']);
// sleep(1);
$data = array();
$data2 = array();

(!$_SERVER['REQUEST_METHOD'] == 'POST') ? PrintRespuestaJson('error', 'Invalid Request Method') . exit : '';

$qUser = "SELECT usr.id AS 'idUser', usr.nombre AS 'nameUser', usr.legajo AS 'legaUser', usr.usuario AS 'usuarioUsr', TarDesIni, TarDesFin, TarDesEsta FROM usuarios usr INNER JOIN mod_roles mrol ON usr.rol=mrol.id_rol LEFT JOIN proy_tareas_desc ON usr.id = proy_tareas_desc.TarDesUsr";

$w_c = $sqlTot = $sqlRec = "";
$w_c .= " WHERE usr.cliente='$_SESSION[ID_CLIENTE]' AND mrol.modulo=43 AND usr.estado='0'";
$w_c .= " GROUP BY usr.id";


if ($w_c ?? '') : // if where_conditions
    $qUser .= $w_c;
endif;

// if (!$params['tarTotales']) {
$qUser .=  " ORDER BY `usr`.`nombre` ASC";
$r = array_pdoQuery($qUser);
$count = count($r);

$q  = "SELECT p.TarDesIni,p.TarDesFin,TarDesEsta FROM proy_tareas_desc p WHERE p.TarDesUsr IS NULL LIMIT 1";
$d = simple_pdoQuery($q);
$TarDesIni = (explode(':', $d['TarDesIni']));
$TarDesFin = (explode(':', $d['TarDesFin']));
$TarDesIni  = ($d['TarDesIni'] == null) ? '00:00' : "$TarDesIni[0]:$TarDesIni[1]";
$TarDesFin  = ($d['TarDesFin'] == null) ? '00:00' : "$TarDesFin[0]:$TarDesFin[1]";
$TarDesEsta = $d['TarDesEsta'];

$data[] = array(
    'TarDesUsr'  => null,
    'TarDesNom'  => '',
    'TarDesLeg'  => '',
    'TarDesEsta'  => $TarDesEsta,
    'usuarioUsr' => '',
    'TarDesIni'  => $TarDesIni,
    'TarDesFin'  => $TarDesFin
);

foreach ($r as $key => $row) {

    $TarDesIni = (explode(':', $row['TarDesIni']));
    $TarDesFin = (explode(':', $row['TarDesFin']));

    $idUser     = $row['idUser'];
    $nameUser   = $row['nameUser'];
    $legaUser   = $row['legaUser'];
    $usuarioUsr = $row['usuarioUsr'];
    $TarDesEsta = $row['TarDesEsta'];

    $TarDesIni  = ($row['TarDesIni'] == null) ? '00:00' : "$TarDesIni[0]:$TarDesIni[1]";
    $TarDesFin  = ($row['TarDesFin'] == null) ? '00:00' : "$TarDesFin[0]:$TarDesFin[1]";

    $data[] = array(
        'TarDesUsr'  => $idUser,
        'TarDesNom'  => $nameUser,
        'TarDesLeg'  => $legaUser,
        'usuarioUsr' => $usuarioUsr,
        'TarDesIni'  => $TarDesIni,
        'TarDesFin'  => $TarDesFin,
        'TarDesEsta' => $TarDesEsta,
    );
}

$tiempo_fin = microtime(true);
$tiempo = ($tiempo_fin - $tiempo_inicio);

$json_data = array(
    "data"   => $data,
    "tiempo" => round($tiempo, 2),
);
echo json_encode($json_data);
exit;
