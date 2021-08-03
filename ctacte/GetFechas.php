<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';
E_ALL();
$data = array();
if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
}else{
    $FechaIni  = date('Ymd');
    $FechaFin  = date('Ymd');
}

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

 $sql_query="SELECT CTANOVE.CTA2Nove, NOVEDAD.NovDesc FROM CTANOVE
 INNER JOIN NOVEDAD ON CTANOVE.CTA2Nove = NOVEDAD.NovCodi
 WHERE CTANOVE.CTA2Peri = '$periodo'
 GROUP BY CTANOVE.CTA2Nove, NOVEDAD.NovDesc";

// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if( !empty($params['search']['value']) ) {
    // $where_condition .=	" AND ";
    // $where_condition .= " (dbo.fn_Concatenar(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%".$params['search']['value']."%') ";
}

if(isset($where_condition) && $where_condition != '') {
$sqlTot .= $where_condition;
$sqlRec .= $where_condition;
}
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$sqlRec .=  " ORDER BY CTANOVE.CTA2Nove OFFSET ".$params['start']." ROWS FETCH NEXT ".$params['length']." ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec,$param, $options);

// print_r($sqlRec); exit;

while( $row = sqlsrv_fetch_array($queryRecords) ) {

    $NovDesc  = $row['NovDesc'];
    $CTA2Nove = $row['CTA2Nove'];

    $data[] = array(
        'FicFech' => '<span class="animate__animated animate__fadeIn">'.$CTA2Nove.'</span><input type="hidden" class="" id="_f" value='.$CTA2Nove.'>',
        'Dia'         => '<span class="animate__animated animate__fadeIn">'.$NovDesc.'</span>',
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
