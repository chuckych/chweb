<?php
require __DIR__ . '/../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/../config/conect_mssql.php';
E_ALL();

$data = array();

require __DIR__ . '/valores.php';

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = "";

$sql_query = "SELECT FICHAS.FicLega AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas GROUP BY FICHAS.FicLega, PERSONAL.LegApNo";

// print_r($sql_query); exit;

$sqlTot .= $sql_query;
$sqlRec .= $sql_query;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND ";
    $where_condition .= " (CONCAT(FICHAS.FicLega,PERSONAL.LegApNo) LIKE '%" . $params['search']['value'] . "%') ";
}

if (isset($where_condition) && $where_condition != '') {
    $sqlTot .= $where_condition;
    $sqlRec .= $where_condition;
}
$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$sqlRec .= " ORDER BY FICHAS.FicLega OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
$queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
$totalRecords = sqlsrv_num_rows($queryTot);
$queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

// print_r($sqlRec); exit;

while ($row = sqlsrv_fetch_array($queryRecords)) {
    $pers_legajo = $row['pers_legajo'];
    $pers_nombre = empty($row['pers_nombre']) ? 'Sin Nombre' : $row['pers_nombre'];
    $data[] = array(
        'pers_legajo' => '<span class="numlega animate__animated animate__fadeIn btn pointer p-0 fontq text-dark fw4">' . $pers_legajo . '</span><input type="hidden" id="_l" value=' . $pers_legajo . '>',
        'pers_nombre' => '<span class="animate__animated animate__fadeIn">' . $pers_nombre . '</span>',
        'null' => '',
        'ApNo' => $pers_nombre,
    );
}
if (!empty($Per2)) {
    if (
        !CountRegistrosMayorCero("SELECT DISTINCT FICHAS.FicLega FROM FICHAS
    INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume
    WHERE FICHAS.FicLega = $Per3 AND PERSONAL.LegFeEg = '17530101'")
    ) {
        $data[] = array(
            'pers_legajo' => '<span class="numlega animate__animated animate__fadeIn btn pointer p-0 fontq text-dark fw4">' . $Per3 . '</span><input type="hidden" id="_l" value=' . $pers_legajo . '>',
            'pers_nombre' => '<span class="animate__animated animate__fadeIn text-danger fw5">Legajo inválido</span>',
            'null' => '',
            'ApNo' => '',
        );
    }
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
