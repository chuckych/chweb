<?php
require __DIR__ . '/../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/../config/conect_mssql.php';

FusNuloPOST('q', '');
$q = test_input($_POST['q']);

$filtroNov = '';
$ListaNov = $_SESSION['ListaNov'];
if ($ListaNov != "-") {
    $filtroNov = " AND NOVEDAD.NovCodi IN ($ListaNov)";
}


$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$FiltroQ = (!empty($q)) ? "AND CONCAT(NOVEDAD.NovCodi, NOVEDAD.NovDesc) LIKE '%$q%'" : '';

$query = "SELECT NOVEDAD.NovCodi AS 'NovCodi', NOVEDAD.NovDesc AS 'NovDesc', NOVEDAD.NovTipo AS 'NovTipo' FROM NOVEDAD 
WHERE NOVEDAD.NovCodi > 0 $filtroNov $FiltroQ GROUP BY NOVEDAD.NovCodi, NOVEDAD.NovDesc, NOVEDAD.NovTipo ORDER BY NOVEDAD.NovCodi";

$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
$NumRows = sqlsrv_num_rows($result);
if ($NumRows > 0) {
    while ($row = sqlsrv_fetch_array($result)) {
        $NovCodi = $row['NovCodi'];
        $NovTipo = TipoNov($row['NovTipo']);
        $NovDesc = $NovCodi . ' - ' . ($row['NovDesc']);

        $data[] = array(
            'id' => intval($NovCodi),
            'NovTipo' => utf8str($NovTipo),
            'text' => utf8str($NovDesc),
        );
    }
} else {
    $data[] = array(
        'id' => '',
        'NovTipo' => '',
        'text' => 'No hay Novedades',
    );
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);

function groupAssoc($input, $sortkey)
{
    foreach ($input as $val)
        $output[$val[$sortkey]][] = $val;
    return $output;
}
$myArray = groupAssoc($data, 'NovTipo');

foreach ($myArray as $key => $value) {
    $data_group[] = array(
        'text' => strtoupper(utf8str($key)),
        'children' => $value
    );
}
echo json_encode($data_group);
exit;
