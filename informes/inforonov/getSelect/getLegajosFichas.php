<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '/../valores.php';

require __DIR__ . '/../../../filtros/filtros.php';
require __DIR__ . '/../../../config/conect_mssql.php';

$id = 'FICHAS.FicLega';
$Desc = 'PERSONAL.LegApNo';
$DescCodi = 'PERSONAL.LegNume';
$Col = 'PERSONAL';
$ColData = 'FICHAS';
$FiltroQ = (!empty($q)) ? "AND CONCAT($id, $Desc) LIKE '%$q%'" : '';

$query = "SELECT TOP 100 $id AS 'id', $Desc AS 'Desc' FROM $ColData INNER JOIN FICHAS2 ON FICHAS.FicLega = FICHAS2.FicLega AND FICHAS.FicFech = FICHAS2.FicFech INNER JOIN $Col ON $id=$DescCodi WHERE $ColData.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND $id >0 $FiltroQ $FiltrosFichas GROUP BY $id, $Desc ORDER BY $Desc";

$dbData = arrMSQueryData($query);

$data = [];

foreach ($dbData as $row) {
    $id = $row['id'];
    $text = $row['Desc'];

    $data[] = [
        'id' => $id,
        'text' => $text,
        'title' => "$id - $text",
    ];

}
echo json_encode($data);
