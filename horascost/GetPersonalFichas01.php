<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '1');

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';


$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

$data = array();

if(empty($DateRange)){
    $json_data = array(
        "draw"            => '',
        "recordsTotal"    => '',
        "recordsFiltered" => '',
        "data"            => $data
    );
    
    echo json_encode($json_data);
    exit;
}

require __DIR__ . '../valores.php';

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

 $sql_query="SELECT PERSONAL.LegNume AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre' FROM FICHAS01 INNER JOIN PERSONAL ON FICHAS01.FicLega=PERSONAL.LegNume INNER JOIN FICHAS ON FICHAS01.FicLega=FICHAS.FicLega WHERE PERSONAL.LegFeEg='17530101' and FICHAS01.FicTurn=1 and FICHAS01.FicLega >0 AND FICHAS01.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltrosFichas $FilterEstruct GROUP BY PERSONAL.LegNume, PERSONAL.LegApNo";
// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if( !empty($params['search']['value']) ) {
    $where_condition .=	" AND ";
    $where_condition .= " (CONCAT(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%".$params['search']['value']."%') ";
}

if(isset($where_condition) && $where_condition != '') {
$sqlTot .= $where_condition;
$sqlRec .= $where_condition;
}
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
// $sqlRec .=  "ORDER BY login_logs.id desc";
// $sqlRec .=  "ORDER BY PERSONAL.LegFeEg, PERSONAL.LegApNo";
// $sqlRec .=  "GROUP BY PERSONAL.LegNume, PERSONAL.LegApNo ORDER BY PERSONAL.LegNume";
$sqlRec .=  " ORDER BY PERSONAL.LegNume OFFSET ".$params['start']." ROWS FETCH NEXT ".$params['length']." ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec,$param, $options);

// print_r($sqlRec); exit;

while( $row = sqlsrv_fetch_array($queryRecords) ) {

        $pers_legajo   = $row['pers_legajo'];
        $pers_nombre   = empty($row['pers_nombre'])?'Sin Nombre':$row['pers_nombre'];
        $data[] = array(
            'pers_legajo' => '<span class="animate__animated animate__fadeIn">'.$pers_legajo.'</span><input type="hidden" id="_l" value='.$pers_legajo.'>',
            'pers_nombre' => '<span class="animate__animated animate__fadeIn">'.$pers_nombre.'</span>',
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
