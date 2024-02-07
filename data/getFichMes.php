<?php
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

FusNuloPOST('q', '');
$q = $_POST['q'];

FusNuloPOST('anio', date('Y'));
$anio = $_POST['Anio'];

$query = "SELECT DATEPART(MM, FICHAS.FicFech) AS Mes FROM FICHAS WHERE DATEPART(YY, FICHAS.FicFech) = '$anio' GROUP BY DATEPART(MM, FICHAS.FicFech) ORDER BY DATEPART(MM, FICHAS.FicFech) desc";
// print_r($query); exit;

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result = sqlsrv_query($link, $query, $params, $options);
$data = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)):

        $id = $row['Mes'];
        $text = Nombre_MesNum($id);

        $data[] = array(
            'id' => $id,
            'text' => $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);
