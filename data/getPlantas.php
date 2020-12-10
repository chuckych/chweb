<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
UnsetGet('q');
// session_start();
$respuesta    = '';
        require_once __DIR__ . '../../config/conect_mssql.php';
        $q = $_GET['q'];
        $query = "SELECT PLANTAS.PlaDesc, PLANTAS.PlaCodi FROM PLANTAS WHERE PLANTAS.PlaDesc LIKE '%$q%' AND PLANTAS.PlaCodi > '0'";
    
        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $result  = sqlsrv_query($link, $query, $params, $options);
        $data    = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($fila = sqlsrv_fetch_array($result)) {
                $PlaCodi = $fila['PlaCodi'];
                $PlaDesc  = empty($fila['PlaDesc']) ? '-': $fila['PlaDesc'];
                $data[] = array(
                    'id' => $PlaCodi,
                    'text' => $PlaDesc,
                );
            }
        } else {
            $data[] = array(
                'id' => false,
                'text' => false
            );
        }
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
echo json_encode($data);
