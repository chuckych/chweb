<?php
require __DIR__ . '../../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../valores.php';

require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';

$id = 'PERSONAL.LegTipo';
$ColData = 'FICHAS01';

$query = "SELECT $id AS 'id' FROM FICHAS01 INNER JOIN FICHAS ON FICHAS01.FicLega=FICHAS.FicLega AND FICHAS01.FicFech=FICHAS.FicFech AND FICHAS01.FicTurn=FICHAS.FicTurn INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE $ColData.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas GROUP BY $id ORDER BY $id";

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result = sqlsrv_query($link, $query, $params, $options);
$data = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)):

        $id = $row['id'];
        $text = $row['id'];
        switch ($id) {
            case '0':
                $id = 2;
                break;
        }

        switch ($text) {
            case '0':
                $text = 'Mensuales';
                break;
            case '1':
                $text = 'Jornales';
                break;
        }


        $data[] = array(
            'id' => $id,
            'text' => $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
