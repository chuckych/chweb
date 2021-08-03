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


$data = array();
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
require __DIR__ . '../valores.php';
    $periodo2 = $periodo+1;

    $query = "SELECT NOVEDAD.NovCtaD AS NovCtaD, NOVEDAD.NovCtaH AS NovCtaH FROM NOVEDAD WHERE NOVEDAD.NovCodi = '$novedad'";
    $res   = sqlsrv_query($link, $query, $param, $options);
    while ($fila = sqlsrv_fetch_array($res)) {
        $NovCtaD = $fila['NovCtaD']; 
        $NovCtaH = $fila['NovCtaH']; 
    }
    sqlsrv_free_stmt($res);
    // print_r($query).PHP_EOL;   exit;

    $query = "SELECT TOP 1 CTANOVE.CTA2Lega FROM CTANOVE WHERE CTANOVE.CTA2Peri='$periodo' ORDER BY CTANOVE.CTA2Lega";
    $res   = sqlsrv_query($link, $query, $param, $options);
    while ($fila = sqlsrv_fetch_array($res)) {
        $CTA2Lega = $fila['CTA2Lega']; 
    }
    sqlsrv_free_stmt($res);
    $Lega  = ((isset($_GET['_per'])) && (!empty($_GET['_per']))) ? implode(",", $_GET['_per']) : $CTA2Lega;

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";


// echo $Novedad; exit;

$sql_query = "SELECT CTANOVE.CTA2Peri AS Periodo,
    CTANOVE.CTA2Lega AS Legajo,
    CTANOVE.CTA2Nove AS CodNov,
    NOVEDAD.NovDesc AS 'Novedad',
    PERSONAL.LegApNo AS Nombre,
    NOVEDAD.NovCtaD AS Desde,
    NOVEDAD.NovCtaH AS Hasta,
    CTANOVE.CTA2Sald AS Saldo,
    CTANOVE.CTA2Cant AS Cantidad,
    (
        Select COUNT(FICHAS3.FicNove) as Novedad
        From FICHAS3,
            NOVEDAD
        Where FICHAS3.FicLega = CTANOVE.CTA2Lega
            and FICHAS3.FicFech >= '"._data_first_month_day($periodo,$NovCtaD)."'
            and FICHAS3.FicFech <= '"._data_last_month_day($periodo2,$NovCtaH)."'
            and FICHAS3.FicNove = CTANOVE.CTA2Nove
            and FICHAS3.FicNoTi >= 3
            and FICHAS3.FicNove = NOVEDAD.NovCodi
            and NOVEDAD.NovTiCo = 3
    ) AS Consumidos
FROM CTANOVE,
    PERSONAL,
    NOVEDAD
WHERE CTANOVE.CTA2Peri = '$periodo'
    AND CTANOVE.CTA2Lega IN ($legajo)
    AND CTANOVE.CTA2Lega <= 999999999
    AND CTANOVE.CTA2Lega = PERSONAL.LegNume
    AND CTANOVE.CTA2Nove = NOVEDAD.NovCodi $FilterEstruct $filtros ";

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if( !empty($params['search']['value']) ) {
$where_condition .=	" AND ";
$where_condition .= " (dbo.fn_Concatenar(PERSONAL.LegNume,PERSONAL.LegApNo) LIKE '%".$params['search']['value']."%')";
}

if(isset($where_condition) && $where_condition != '') {
$sqlTot .= $where_condition;
$sqlRec .= $where_condition;
}

$sqlRec .=  "ORDER BY CTANOVE.CTA2Peri,CTANOVE.CTA2Nove,CTANOVE.CTA2Lega OFFSET ".$params['start']." ROWS FETCH NEXT ".$params['length']." ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec,$param, $options);

// print_r($sqlRec).PHP_EOL;   exit;

    while ($row = sqlsrv_fetch_array($queryRecords)) :
        $Periodo    = $row['Periodo'];
        $Legajo     = $row['Legajo'];
        $CodNov     = $row['CodNov'];
        $Novedad    = $row['Novedad'];
        $Nombre     = $row['Nombre'];
        $Desde      = $row['Desde'];
        $Hasta      = $row['Hasta'];
        $Saldo      = $row['Saldo'];
        $Cantidad   = $row['Cantidad'];
        $Consumidos = $row['Consumidos'];
        $fecha1     = Fech_Format_Var(_data_first_month_day($Periodo,$Desde),'M');
        $fecha2     = Fech_Format_Var(_data_last_month_day($Periodo+1,$Hasta),'M');

        $data[] = array(
            'Periodo'    => $Periodo,
            'Legajo'     => $Legajo,
            'CodNov'     => $CodNov,
            'Novedad'    => $Novedad,
            'Nombre'     => $Nombre,
            'Desde'      => $Desde.'/'.$Periodo,
            'Hasta'      => $Hasta.'/'.($Periodo+1),
            'Rango'      => $fecha1 . ' - '. $fecha2,
            'Saldo'      => $Saldo,
            'Total'      => $Saldo+$Cantidad,
            'Cantidad'   => $Cantidad,
            'Consumidos' => $Consumidos,
            'Disponible' => ($Saldo+$Cantidad)-$Consumidos
        );
         
    endwhile;
    sqlsrv_free_stmt($queryRecords);
    sqlsrv_close($link);
    $json_data = array(
        "draw"            => intval( $params['draw'] ),   
        "recordsTotal"    => intval( $totalRecords ),  
        "recordsFiltered" => intval($totalRecords),
        "data"            => $data
        );

echo json_encode($json_data);