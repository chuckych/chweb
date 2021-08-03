<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';
E_ALL();
$data = array();
$params = $_REQUEST;
if (isset($_POST['_f']) && !empty($_POST['_f'])) {
    $Fecha = test_input(FusNuloPOST('_f', 'vacio'));
}else{
    $json_data = array(
        "draw"            => intval($params['draw']),
        "recordsTotal"    => 0,
        "recordsFiltered" => 0,
        "data"            => $data
    );
    echo json_encode($json_data);
    exit;
}
require __DIR__ . '../valores.php';

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$params = $columns = $totalRecords ='';
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$Calculos = (!$Calculos==1) ? "AND TIPOHORA.THoColu > 0" : '';

 $sql_query="SELECT FICHAS01.FicHora AS 'FicHora', TIPOHORA.THoDesc AS 'THoDesc', SUM(dbo.fn_STRMinutos(FICHAS01.FicHsHeC)) AS 'FicHsHeC', SUM(dbo.fn_STRMinutos(FICHAS01.FicHsAuC)) AS 'FicHsAuC' FROM FICHAS01 INNER JOIN FICHAS ON FICHAS01.FicLega=FICHAS.FicLega AND FICHAS01.FicFech=FICHAS.FicFech AND FICHAS01.FicTurn=FICHAS.FicTurn INNER JOIN PERSONAL ON FICHAS01.FicLega=PERSONAL.LegNume INNER JOIN TIPOHORA ON FICHAS01.FicHora=TIPOHORA.THoCodi WHERE FICHAS01.FicFech='$Fecha' $Calculos $FilterEstruct $FiltrosFichas GROUP BY FICHAS01.FicHora, TIPOHORA.THoDesc";

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

//$sqlRec .=  " ORDER BY FICHAS01.FicLega, TIPOHORA.THoColu, FICHAS01.FicHora OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);

$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

while ($row = sqlsrv_fetch_array($queryRecords)) :

    $FicHsHeC  = MinHora($row['FicHsHeC']);
    $FicHora  = $row['FicHora'];
    $THoDesc  = $row['THoDesc'];
    $FicHsAuC  = MinHora($row['FicHsAuC']);

    $data[] = array(
        'FicHora'  => $FicHora,
        'THoDesc'  => $THoDesc.'<br><span class="fw3">Autorizadas</span>',
        'FicHsHeC'  => $FicHsHeC,
        'FicHsAuC'  => $FicHsAuC,
        'CalcHoras'   => $FicHsHeC.'<br><b class="text-secondary">'.$FicHsAuC.'</b>',
        'null'     => '',
    );

endwhile;

sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);

$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data
);

echo json_encode($json_data);
