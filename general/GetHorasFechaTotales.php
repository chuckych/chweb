<?php
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
ini_set('max_execution_time', 180); //180 seconds = 3 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

E_ALL();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';
$data = array();
$Fecha = test_input(FusNuloPOST('_f', 'vacio'));

if ($Fecha == 'vacio') {

    $json_data = array(
        "draw" => 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => $data
    );

    echo json_encode($json_data);
    exit;
}
require __DIR__ . '../valores.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$params = $columns = $totalRecords = '';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$Calculos = (!$Calculos == 1) ? "AND TIPOHORA.THoColu > 0" : '';

$sql_query = "SELECT FICHAS1.FicHora AS 'FicHora', TIPOHORA.THoDesc AS 'THoDesc',
SUM(dbo.fn_STRMinutos(FICHAS1.FicHsHe)) AS 'FicHsHe', 
SUM(dbo.fn_STRMinutos(FICHAS1.FicHsAu)) AS 'FicHsAu', 
SUM(dbo.fn_STRMinutos(FICHAS1.FicHsAu2)) AS 'FicHsAu2' FROM FICHAS1 INNER JOIN FICHAS ON FICHAS1.FicLega=FICHAS.FicLega AND FICHAS1.FicFech=FICHAS.FicFech AND FICHAS1.FicTurn=FICHAS.FicTurn INNER JOIN PERSONAL ON FICHAS1.FicLega=PERSONAL.LegNume INNER JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi WHERE FICHAS1.FicFech='$Fecha' $Calculos $FilterEstruct $filtros GROUP BY FICHAS1.FicHora, TIPOHORA.THoDesc";

// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%" . $params['search']['value'] . "%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

//$sqlRec .=  " ORDER BY FICHAS1.FicLega, TIPOHORA.THoColu, FICHAS1.FicHora OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);

$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

while ($row = sqlsrv_fetch_array($queryRecords)):

    $FicHsHe = $row['FicHsHe'];
    $FicHora = $row['FicHora'];
    $THoDesc = $row['THoDesc'];
    $FicHsAu = MinHora($row['FicHsAu']);
    $FicHsAu2 = MinHora($row['FicHsAu2']);

    $data[] = array(
        'FicHora' => $FicHora,
        'THoDesc' => $THoDesc,
        'FicHsHe' => $FicHsHe,
        'FicHsAu' => $FicHsAu,
        'FicHsAu2' => $FicHsAu2,
        'null' => '',
    );

endwhile;

sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);

echo json_encode($json_data);
