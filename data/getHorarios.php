<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
UnsetGet('q');
session_start();
        require_once __DIR__ . '../../config/conect_mssql.php';
        $q = $_GET['q'];
        $query = "SELECT HORARIOS.HorDesc, HORARIOS.HorCodi FROM HORARIOS WHERE HORARIOS.HorDesc LIKE '%$q%' AND HORARIOS.HorCodi > '0'";
    
        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $result  = sqlsrv_query($link, $query, $params, $options);
        $data    = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($fila = sqlsrv_fetch_array($result)) {
                $HorCodi = $fila['HorCodi'];
                $HorDesc  = empty($fila['HorDesc']) ? '-': $fila['HorDesc'];
                $data[] = array(
                    'id' => $HorCodi,
                    'text' => $HorCodi. ' - ' . $HorDesc,
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
