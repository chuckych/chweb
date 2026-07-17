<?php
if (session_status() == PHP_SESSION_NONE) {
    require __DIR__ . '/../../config/session_start.php';
}
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '/../valores.php';
require __DIR__ . '/../../filtros/filtros.php';

$FiltroQ = (!empty($q)) ? "AND CONCAT(NOVEDAD.NovCodi, NOVEDAD.NovDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';

$query = "SELECT NOVEDAD.NovCodi AS 'NovCodi', NOVEDAD.NovDesc AS 'NovDesc', NOVEDAD.NovTipo AS 'NovTipo' FROM FICHAS INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech AND FICHAS.FicTurn=FICHAS3.FicTurn INNER JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume $joinRegistros WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas $FiltroQ $filtroTipo GROUP BY NOVEDAD.NovCodi, NOVEDAD.NovDesc, NOVEDAD.NovTipo ORDER BY NOVEDAD.NovCodi";

$result = arrMSQuery($query);

$data = [];
foreach ($result as $row) {
    $NovCodi = $row['NovCodi'];
    $NovTipo = TipoNov($row['NovTipo']);
    $NovDesc = utf8str($row['NovDesc']);

    $data[] = [
        'id' => intval($NovCodi),
        'NovTipo' => utf8str($NovTipo),
        'text' => utf8str($NovDesc),
        'title' => "$NovCodi - $NovDesc",
        'html' => "<label class='m-0 Mw40 font08'>$NovCodi</label><label class='fontq m-0'>$NovDesc</label>",
    ];
}

function groupAssoc(array $input, string $sortkey)
{
    foreach ($input as $val)
        $output[$val[$sortkey]][] = $val;
    return $output ?? [];
}
$myArray = groupAssoc($data, 'NovTipo');

foreach ($myArray as $key => $value) {
    $data_group[] = [
        'text' => strtoupper(utf8str($key)),
        'children' => $value
    ];
}
echo json_encode($data_group ?? []);