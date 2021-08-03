<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

$data = array();
$params = $_REQUEST;
if (isset($_POST['_l']) && !empty($_POST['_l'])) {
    $legajo = test_input(FusNuloPOST('_l', 'vacio'));
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

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query="SELECT FICHAS.FicFech AS 'FicFech', dbo.fn_DiaDeLaSemana(FICHAS.FicFech) AS 'Dia' FROM FICHAS INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas GROUP BY FICHAS.FicFech";

// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if( !empty($params['search']['value']) ) {
    // $where_condition .=	" AND ";
    // $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%".$params['search']['value']."%') ";
}

if(isset($where_condition) && $where_condition != '') {
$sqlTot .= $where_condition;
$sqlRec .= $where_condition;
}
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$sqlRec .=  " ORDER BY FICHAS.FicFech OFFSET ".$params['start']." ROWS FETCH NEXT ".$params['length']." ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec,$param, $options);

// print_r($sqlRec); exit;

while( $row = sqlsrv_fetch_array($queryRecords) ) {

    $Dia        = $row['Dia'];
    $FicFech    = $row['FicFech']->format('d/m/Y');
    $FicFechStr = $row['FicFech']->format('Ymd');

    $data[] = array(
        'FicFech' => '<span class="animate__animated animate__fadeIn">'.$FicFech.'</span><input type="hidden" class="" id="_f" value='.$FicFechStr.'>',
        'Dia'         => '<span class="animate__animated animate__fadeIn">'.$Dia.'</span>',
        'null'        => '',
    );
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
$json_data = array(
"draw"            => intval( $params['draw'] ),   
"recordsTotal"    => intval( $totalRecords ),  
"recordsFiltered" => intval($totalRecords),
"data"            => $data
);
echo json_encode($json_data);
