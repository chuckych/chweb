<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '/../valores.php';

require __DIR__ . '/../../filtros/filtros.php';
require __DIR__ . '/../../config/conect_mssql.php';

$FiltroQ = (!empty($q)) ? "AND CONCAT(FICHAS2.FicONov, OTRASNOV.ONovDesc) LIKE '%$q%'" : '';

$query = "SELECT FICHAS2.FicONov AS 'id',
 OTRASNOV.ONovDesc AS 'text'
FROM FICHAS2
 INNER JOIN OTRASNOV ON FICHAS2.FicONov = OTRASNOV.ONovCodi
 INNER JOIN PERSONAL ON FICHAS2.FicLega = PERSONAL.LegNume
 LEFT JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega AND FICHAS2.FicFech = FICHAS.FicFech
WHERE FICHAS2.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $FilterEstruct2 $FiltrosFichas
GROUP BY FICHAS2.FicONov,
 OTRASNOV.ONovDesc
ORDER BY FICHAS2.FicONov";

// print_r($query); exit;

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result = sqlsrv_query($link, $query, $params, $options);
$data = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)):

        $id = $row['id'];
        $text = $row['text'];

        $data[] = array(
            'id' => $id,
            'text' => $text,
            'title' => $id . ' - ' . $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
