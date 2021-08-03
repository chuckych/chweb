<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME,"es_ES");
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
session_start();
require __DIR__ . '../../config/index.php';
require __DIR__ . '../../config/conect_mssql.php';
E_ALL();

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";
$params['start']  = $params['start']??'1';
$params['length'] = $params['length']??'10';
$params['draw']   = $params['draw']??'1';
$sql_query = "SELECT * FROM AUDITOR WHERE AUDITOR.AudModu > '0'";
$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if( !empty($params['search']['value']) ) {
    $where_condition .=	" AND ";
    // $where_condition .= "(AUDITOR.AudDato LIKE '%".$params['search']['value']."%') AND AUDITOR.AudTipo != 'P' ";
    $where_condition .= "(AUDITOR.AudDato LIKE '%".$params['search']['value']."%')";
}
    // $where_condition .=	" ";
    $where_condition .= "AND  AUDITOR.AudModu in (21,8)";

if(isset($where_condition) && $where_condition != '') {
$sqlTot .= $where_condition;
$sqlRec .= $where_condition;
}
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
// $sqlRec .=  "ORDER BY login_logs.id desc";
// $sqlRec .=  "ORDER BY PERSONAL.LegFeEg, PERSONAL.LegApNo";
$sqlRec .=  "ORDER BY AUDITOR.FechaHora desc OFFSET ".$params['start']." ROWS FETCH NEXT ".$params['length']." ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec,$param, $options);

// print_r($sqlRec); exit;

while( $row = sqlsrv_fetch_array($queryRecords) ) {

    $AudFech   = $row['AudFech']->format('d/m/Y');
    $AudHora   = ($row['AudHora']);
    $AudUser   = ($row['AudUser']);
    $AudTerm   = $row['AudTerm'];
    $AudModu   = $row['AudModu'];
    $AudTipo   = $row['AudTipo'];
    $AudDato   = $row['AudDato'];
    $FechaHora = $row['FechaHora']->format('d-m-Y H:i:s');

    switch ($AudModu) {
        case '0':
            $AudModu = 'Mantenimiento';
            break;
        case '1':
            $AudModu = 'Control Horario';
            break;
        case '2':
            $AudModu = 'Sueldos';
            break;
        case '3':
            $AudModu = 'Control Horario Lite';
            break;
        case '4':
            $AudModu = 'Accesos';
            break;
        case '5':
            $AudModu = 'Planificación';
        case '8':
            $AudModu = 'WebService';
            break;
        case '21':
            $AudModu = 'CHWEB';
            break;
        
        default:
        $AudModu = $AudModu;
            break;
    }
    switch ($AudTipo) {
        case 'M':
            $AudTipo = 'Modificación';
            break;
        case 'B':
            $AudTipo = 'Baja';
            break;
        case 'A':
            $AudTipo = 'Alta';
            break;
        case 'P':
            $AudTipo = 'Proceso';
            break;
        case 'T':
            $AudTipo = 'Transferencia';
            break;
        case 'R':
            $AudTipo = 'Informe';
            break;
        case 'E':
            $AudTipo = 'Exportación';
            break;
        case 'I':
            $AudTipo = 'Importación';
            break;
        
        default:
        $AudTipo = $AudTipo;
            break;
    }

$data[] = array(
    'AudFech'   => $AudFech,
    'AudHora'   => $AudHora,
    'AudUser'   => $AudUser,
    'AudTerm'   => $AudTerm,
    'AudModu'   => $AudModu,
    'AudTipo'   => $AudTipo,
    'AudDato'   => $AudDato,
    'FechaHora' => $FechaHora,
    'null'      => '',
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
