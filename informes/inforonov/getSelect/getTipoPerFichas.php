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

$id = 'PERSONAL.LegTipo';
$ColData = 'FICHAS';

$query = "SELECT TOP 100 $id AS 'id' FROM $ColData INNER JOIN FICHAS2 ON FICHAS.FicLega = FICHAS2.FicLega AND FICHAS.FicFech = FICHAS2.FicFech INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE $ColData.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltrosFichas GROUP BY $id ORDER BY $id";

$dbData = arrMSQueryData($query);

$data = [];

foreach ($dbData as $row) {
    if (!empty($row)) {

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


        $data[] = [
            'id' => $id,
            'text' => $text,
        ];
    }
}

echo json_encode($data);
