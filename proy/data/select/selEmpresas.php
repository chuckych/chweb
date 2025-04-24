<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../../config/index.php';
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$q = $params['q'];
$data = array();
$where_condition = '';

$where_condition .= (!empty($params['_c'])) ? " AND clientes.recid = '$params[_c]'" : "";
$where_condition .= " AND proy_empresas.Cliente = '$_SESSION[ID_CLIENTE]'";

$FiltroQ = (!empty($q)) ? " AND proy_empresas.EmpDesc LIKE '%$q%'" : '';
$query = "SELECT EmpID, EmpDesc, EmpTel FROM proy_empresas WHERE proy_empresas.EmpID > 0";
$query .= $FiltroQ;
$query .= $where_condition;
$query .= ' ORDER BY proy_empresas.EmpDesc';
$r = array_pdoQuery($query);

function html($EmpDesc, $EmpTel)
{
    $a = "
    <div title='$EmpTel' class='form-selectgroup-label bg-azure text-white' style='border-radius:0px; border:1px solid #ddd;'>
        $EmpDesc
    </div>
    ";
    return $a;
}

foreach ($r as $key => $row) {
    $data[] = array(
        'id' => $row['EmpID'],
        'text' => utf8str($row['EmpDesc']),
        'html' => html($row['EmpDesc'], $row['EmpTel'])
    );
}

echo json_encode($data);
