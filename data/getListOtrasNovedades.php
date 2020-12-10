<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
UnsetGet('q');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '0');


        require_once __DIR__ . '../../config/conect_mssql.php';

        $params  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
      

        $_POST['q'] = $_POST['q'] ?? '';
        $q = test_input($_POST['q']);

        $Datos = explode('-', $_POST['Datos']);
        $FicLega = $Datos[0];
        $FicFech = $Datos[1];
        $FiltrarNovTipo2 = '';

        $query = "SELECT OTRASNOV.ONovTipo AS Tipo FROM OTRASNOV GROUP BY OTRASNOV.ONovTipo";
        $result  = sqlsrv_query($link, $query, $params, $options);
        // print_r($query);exit;
        $data = array();
        
        if (sqlsrv_num_rows($result) > 0) {
            
            while ($fila = sqlsrv_fetch_array($result)) {

                $ONovTipo = $fila['Tipo'];           

                $query="SELECT OTRASNOV.ONovCodi AS Codigo,OTRASNOV.ONovDesc AS Descrip,OTRASNOV.ONovColo AS Color,OTRASNOV.ONovCol1 AS Codigo1,OTRASNOV.ONovCol2 AS Codigo2,
                OTRASNOV.ONovCol3 AS Codigo3,OTRASNOV.ONovCol4 AS Codigo4
                FROM OTRASNOV
                WHERE OTRASNOV.ONovCodi > 0 AND OTRASNOV.ONovTipo = '$ONovTipo'
                AND CONCAT(' ', OTRASNOV.ONovCodi, OTRASNOV.ONovDesc) LIKE '%$q%'
                AND OTRASNOV.ONovCodi NOT IN (SELECT FICHAS2.FicONov FROM FICHAS2 WHERE FICHAS2.FicLega = '$FicLega' AND FICHAS2.FicFech = '$FicFech')
                ORDER BY OTRASNOV.ONovCodi";
                // print_r($query);exit;
               
                $result_ONov  = sqlsrv_query($link, $query, $params, $options);

                $ONovedades = array();

                if (sqlsrv_num_rows($result_ONov) > 0) {
                    while ($row_ONov = sqlsrv_fetch_array($result_ONov)) :
                        $cod= str_pad($row_ONov['Codigo'], 3, "0", STR_PAD_LEFT);
                            $ONovedades[] = array(
                                'id'       => $row_ONov['Codigo'],
                                'text'     => $cod.' - '.$row_ONov['Descrip'],
                            );
                        endwhile;
                        sqlsrv_free_stmt($result_ONov);
                    }else{
                        $ONovedades = array();
                    }

                    $tipo = strtoupper(TipoONov($ONovTipo));

                    $data[] = array(
                        'text' => $tipo,
                        "children"=> $ONovedades, 
                    );
                    unset($ONovedades);
            }
        }
        sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            echo json_encode(($data)); 
            // print_r($data);
