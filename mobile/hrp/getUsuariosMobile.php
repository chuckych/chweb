<?php
require __DIR__ . '../../../config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

require __DIR__ . '../../../config/conect_mysql.php';
// sleep(2);
$respuesta = array();

$params = $columns = $totalRecords;
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT reg_user_.phoneid as 'phoneid', reg_user_.nombre as 'nombre', (SELECT COUNT(1) FROM reg_ WHERE reg_.phoneid = reg_user_.phoneid) AS 'cant' FROM reg_user_ WHERE reg_user_.ID > 0";

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND ";
    $where_condition .= "reg_user_.nombre LIKE '%" . $params['search']['value'] . "%'";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}

$sqlRec .=  " ORDER BY reg_user_.nombre LIMIT " . $params['start'] . " ," . $params['length'];
$queryTot = mysqli_query($link, $sqlTot);
$totalRecords = mysqli_num_rows($queryTot);
$queryRecords = mysqli_query($link, $sqlRec);

// print_r($sqlRec); exit;

if ($totalRecords > 0) {
    while ($r = mysqli_fetch_assoc($queryRecords)) {
        $arrayData[] = array(
            'phoneid' => $r['phoneid'],
            'nombre'  => $r['nombre'],
            'cant'    => $r['cant'],
        );
    }
}

// print_r(json_encode($arrayData)); exit;

foreach ($arrayData as $key => $valor) {
    $respuesta[] = array(
        '<div>' . $valor['phoneid'] . '</div>',
        '<div>' . $valor['nombre'] . '</div>',
        '<div>' . $valor['cant'] . '</div>',
    );
}
// $respuesta = array('mobile' => $respuesta);
$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $respuesta
);
// sleep(2);
echo json_encode($json_data);
