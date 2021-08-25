<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
UnsetGet('q');
session_start();
E_ALL();

$filtroNov = '';
$ListaNov = $_SESSION['ListaNov'];
if ($ListaNov  != "-") {
    $filtroNov = " AND NOVEDAD.NovCodi IN ($ListaNov)";
}

require_once __DIR__ . '../../config/conect_mssql.php';

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

FusNuloGET('q', '');
$q = test_input($_GET['q']);

$query = "SELECT DISTINCT NOVEDAD.NovTipo FROM NOVEDAD WHERE NOVEDAD.NovCodi > 0 $filtroNov";
$result  = sqlsrv_query($link, $query, $params, $options);
// print_r($query);exit;
$data = array();

if (sqlsrv_num_rows($result) > 0) {

    while ($fila = sqlsrv_fetch_array($result)) {

        $NovTipo = $fila['NovTipo'];

        $query = "SELECT NOVEDAD.NovCodi AS Codigo , NOVEDAD.NovDesc AS Descripción FROM NOVEDAD 
                WHERE NOVEDAD.NovTipo = '$NovTipo' 
                AND NOVEDAD.NovCodi > 0 $filtroNov
                AND CONCAT(' ', NOVEDAD.NovCodi, NOVEDAD.NovDesc) LIKE '%$q%' 
                ORDER BY NOVEDAD.NovCodi";
        // print_r($query);exit;

        $result_Nov  = sqlsrv_query($link, $query, $params, $options);

        $Novedades = array();

        if (sqlsrv_num_rows($result_Nov) > 0) {
            while ($row_Nov = sqlsrv_fetch_array($result_Nov)) :
                // $selected = ($row_Nov['Codigo'] == 2) ? 'selected':'';
                $cod = str_pad($row_Nov['Codigo'], 3, "0", STR_PAD_LEFT);
                $Novedades[] = array(
                    'id'       => $row_Nov['Codigo'],
                    'text'     => $cod . ' - ' . $row_Nov['Descripción'],
                );
            endwhile;
            sqlsrv_free_stmt($result_Nov);
        } else {
            $Novedades = array();
        }


        $tipo = strtoupper(TipoNov($NovTipo));
        $disabled = ($NovTipo > '2') ? 'true' : '';

        $data[] = array(
            'text' => $tipo,
            "children" => $Novedades,
            // "disabled"=> $disabled,
        );
        unset($Novedades);
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(($data)); 
            // print_r($data);
