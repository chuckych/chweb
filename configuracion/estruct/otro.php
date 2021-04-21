<?php

else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_tareas')){
    
    $Cod  = test_input(($_POST['cod'])) ??''; /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ??''; /** Descripcion */
    // sleep(2);
    if(valida_campo($Desc)){
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };

    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
        $query = "SELECT TAREAS.tareDesc FROM TAREAS WHERE TAREAS.tareDesc = '$Desc' COLLATE Latin1_General_CI_AI";

        $result  = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($result) > 0) {
            while ($fila = sqlsrv_fetch_array($result)) {
                PrintRespuestaJson('error', 'La descripción <strong>' .$Desc.'</strong> ya existe');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            }
        }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 TAREAS.TareCodi, TAREAS.tareDesc FROM TAREAS ORDER BY TAREAS.TareCodi DESC";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$Cod) {
                $Codi  = $fila['TareCodi'] + 1;
            }else{
                $Codi  = $Cod;
            }
            $Dato     = 'Tarea: ' .$Desc . ': ' . $Codi;
            $TareEstado = 0;
            $procedure_params = array(
                array(&$Codi),
                array(&$Desc),
                array(&$TareEstado),
                array(&$FechaHora)
            );

            $sql = "exec DATA_TAREASInsert @TareCodi=?,@tareDesc=?,@TareEstado=?,@FechaHora=?"; /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params); /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) { /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato); 
                /** */
                PrintRespuestaJson('ok', 'Tarea: <strong>' .$Desc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error=='23000') ? 'El Codigo ya existe':'Error: '.$error;
                PrintRespuestaJson('error', $error, true);
                exit;
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        PrintRespuestaJson('error', 'Error Cod');
        exit;
    }
    sqlsrv_close($link);
    exit;
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_tareas')){

    $Cod  = test_input(($_POST['cod'])) ??''; /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ??''; /** Descripcion */
    // sleep(2);
    if(valida_campo($Desc)){
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $query="UPDATE TAREAS SET tareDesc = '$Desc', FechaHora = SYSDATETIME() WHERE TareCodi = $Cod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Tarea: ' .$Desc . ': ' . $Cod;
        audito_ch('M', $Dato);

        PrintRespuestaJson('ok', 'Tarea <strong>'.$Desc.'</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_tareas')){

    $Cod  = test_input(($_POST['cod'])) ??''; /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ??''; /** Descripcion */
    // sleep(2);
    if(valida_campo($Cod)){
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si el personal contiene TAREAS. */
    $query = "SELECT PERSONAL.LegTareProd FROM PERSONAL WHERE PERSONAL.LegTareProd = $Cod";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'Error al eliminar. Existe Información en Personal');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */
    $Dato     = 'Tarea: ' .$Desc . ': ' . $Cod;

    $query="DELETE FROM TAREAS WHERE TareCodi = $Cod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Tarea <strong>'.$Desc.'</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

}