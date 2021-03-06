<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '../../valores.php';

require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';

$id       = 'PERSONAL.LegTipo';
$ColData  = 'REGISTRO';

$query="SELECT $id AS 'id' FROM $ColData INNER JOIN PERSONAL ON REGISTRO.RegLega=PERSONAL.LegNume WHERE $ColData.RegFeAs BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $filtros GROUP BY $id ORDER BY $id";
// print_r($query); exit;
$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['id'];
        $text = $row['id'];
        switch ($id) {
            case '0':
                $id=2;
                break;
        }

        switch ($text) {
            case '0':
                $text= 'Mensuales';
                break;
            case '1':
                $text= 'Jornales';
                break;
        }


        $data[] = array(
            'id'    => $id,
            'text'  => $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
