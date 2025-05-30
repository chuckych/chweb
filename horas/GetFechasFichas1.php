<?php
require __DIR__ . '/../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
// sleep(1);
require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/../config/conect_mssql.php';
E_ALL();
require __DIR__ . '/valores.php';

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT FICHAS1.FicFech as 'FicFech', dbo.fn_DiaDeLaSemana(FICHAS1.FicFech) AS 'Dia' FROM FICHAS1 INNER JOIN PERSONAL ON FICHAS1.FicLega=PERSONAL.LegNume INNER JOIN FICHAS ON FICHAS1.FicLega=FICHAS.FicLega WHERE PERSONAL.LegFeEg='17530101' AND FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas GROUP BY FICHAS1.FicFech";
// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    // $where_condition .=	" AND ";
    // $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%".$params['search']['value']."%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}
$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$sqlRec .= " ORDER BY FICHAS1.FicFech OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r($sqlRec); exit;

while ($row = sqlsrv_fetch_array($queryRecords)) {

    $Dia = $row['Dia'];
    $FicFech = $row['FicFech']->format('d/m/Y');
    $FicFechStr = $row['FicFech']->format('Ymd');

    $data[] = array(
        'FicFech' => '<span class="animate__animated animate__fadeIn">' . $FicFech . '</span><input type="hidden" class="" id="_f" value=' . $FicFechStr . '>',
        'Dia' => '<span class="animate__animated animate__fadeIn">' . $Dia . '</span>',
        'null' => '',
    );
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data" => $data
);
echo json_encode($json_data);
