<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

if (empty($_POST['_dr'])) {

    $json_data = array(
        "draw"            => 0,
        "recordsTotal"    => 0,
        "recordsFiltered" => 0,
        "data"            => $data
    );

    echo json_encode($json_data);
    exit;
}

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

$legajo = test_input(FusNuloPOST('_l', 'vacio'));

require __DIR__ . '../valores.php';

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT FICHAS.FicFech as 'FicFech', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Dia' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas GROUP BY FICHAS.FicFech";
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
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$sqlRec .=  " ORDER BY FICHAS.FicFech OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
// print_r($totalRecords); exit;
if ($totalRecords > 0) {
    $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);
    while ($row = sqlsrv_fetch_array($queryRecords)) {

        $Dia        = $row['Dia'];
        $FicFech    = $row['FicFech']->format('d/m/Y');
        $FicFechStr = $row['FicFech']->format('Ymd');

        $data[] = array(
            'FicFech' => '<span class="animate__animated animate__fadeIn">' . $FicFech . '</span><input type="hidden" class="" id="_f" value=' . $FicFechStr . '>',
            'Dia'         => '<span class="animate__animated animate__fadeIn">' . $Dia . '</span>',
            'null'        => '',
        );
    }
    sqlsrv_free_stmt($queryRecords);
}
sqlsrv_close($link);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);
echo json_encode($json_data);
