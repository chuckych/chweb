<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
UnsetGet('q');
// session_start();
$respuesta    = '';
        // require __DIR__ . '../../filtros/filtros.php';
        require __DIR__ . '../../config/conect_mssql.php';
        $q = $_GET['q'];
        $query = "SELECT SECTORES.SecDesc, SECTORES.SecCodi FROM SECTORES WHERE SECTORES.SecDesc LIKE '%$q%' AND SECTORES.SecCodi >'0'";

        // print_r($query);
    
        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $result  = sqlsrv_query($link, $query, $params, $options);
        $data    = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($fila = sqlsrv_fetch_array($result)) {
                $SecCodi = $fila['SecCodi'];
                $SecDesc  = empty($fila['SecDesc']) ? '-': $fila['SecDesc'];
                $data[] = array(
                    'id' => $SecCodi,
                    "text" => $SecDesc,
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
