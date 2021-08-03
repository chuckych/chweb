<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../valores.php';

require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';

$FiltroQ  = (!empty($q)) ? "AND dbo.fn_Concatenar(FICHAS3.FicNove, NOVEDAD.NovDesc) LIKE '%$q%'":'';

 $query="SELECT FICHAS3.FicNove AS 'id', NOVEDAD.NovDesc AS 'text' FROM FICHAS3 INNER JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi INNER JOIN PERSONAL ON FICHAS3.FicLega = PERSONAL.LegNume LEFT JOIN FICHAS ON FICHAS3.FicLega=FICHAS.FicLega AND FICHAS3.FicFech=FICHAS.FicFech WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $FilterEstruct2 $FiltrosFichas GROUP BY FICHAS3.FicNove, NOVEDAD.NovDesc ORDER BY FICHAS3.FicNove";

// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['id'];
        $text = $row['text'];

        $data[] = array(
            'id'    => $id,
            'text'  => $text,
            'title' => $id.' - '.$text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
