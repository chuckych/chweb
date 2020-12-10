<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
UnsetGet('q');
session_start();
        require_once __DIR__ . '../../config/conect_mssql.php';
        $q = $_GET['q'];
        $query = "SELECT RELOJES.RelReMa AS Marca ,RELOJES.RelRelo AS Reloj ,RELOJES.RelDeRe AS Nombre FROM RELOJES WHERE RELOJES.RelReMa > 0 AND RELOJES.RelDeRe LIKE '%$q%' ORDER BY RELOJES.RelDeRe";
    
        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $result  = sqlsrv_query($link, $query, $params, $options);
        $data    = array();
        if (sqlsrv_num_rows($result) > 0) {
            while ($fila = sqlsrv_fetch_array($result)) {
                $id = $fila['Reloj'].'-'.$fila['Marca'];
                $Nombre  = empty($fila['Nombre']) ? '-': $fila['Nombre'];
                $data[] = array(
                    'id' => $id,
                    'text' => $Nombre,
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
