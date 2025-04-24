<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();
FusNuloPOST('submit', '');
/** ALTA DE CLIENTE */
$FechaHora = date('Ymd H:i:s');
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$data = array();
$_POST['tipo'] = $_POST['tipo'] ?? false;
$_POST['cod'] = $_POST['cod'] ?? '';
$_POST['desc'] = $_POST['desc'] ?? '';
/** ALTA NACIONES */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_nacion')) {

    $NacCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $NacDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($NacDesc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    }
    ;

    require_once __DIR__ . '/../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT NACIONES.NacDesc
        FROM NACIONES WHERE NACIONES.NacDesc = '$NacDesc' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $NacDesc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 NACIONES.NacCodi, NACIONES.NacDesc FROM NACIONES ORDER BY NACIONES.NacCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$NacCod) {
                $NacCodi = $fila['NacCodi'] + 1;
            } else {
                $NacCodi = $NacCod;
            }
            $Dato = 'Nacionalidad: ' . $NacDesc . ': ' . $NacCodi;

            $procedure_params = array(
                array(&$NacCodi),
                array(&$NacDesc),
                array(&$FechaHora)
            );

            $sql = "exec DATA_NACIONESInsert @NacCodi=?,@NacDesc=?,@FechaHora=?"; /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params); /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) { /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '30');
                /** */
                PrintRespuestaJson('ok', 'Nacionalidad: <strong>' . $NacDesc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
                PrintRespuestaJson('error', $error);
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_nacion')) {

    $NacCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $NacDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($NacDesc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    }
    ;
    require_once __DIR__ . '/../../config/conect_mssql.php';
    /** Query revisar si la descripción ya existe. */
    // $query = "SELECT NACIONES.NacDesc FROM NACIONES WHERE NACIONES.NacDesc = '$NacDesc' COLLATE Latin1_General_CI_AI";
    // $result  = sqlsrv_query($link, $query, $params, $options);
    // if (sqlsrv_num_rows($result) > 0) {
    //     while ($fila = sqlsrv_fetch_array($result)) {
    //         PrintRespuestaJson('error', 'La descripción <strong>' .$NacDesc.'</strong> ya existe');
    //         sqlsrv_free_stmt($result);
    //         sqlsrv_close($link);
    //         exit;
    //     }
    // }
    /** fin */
    $query = "UPDATE NACIONES SET NacDesc = '$NacDesc', FechaHora = SYSDATETIME() WHERE NacCodi = $NacCod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato = 'Nacionalidad: ' . $NacDesc . ': ' . $NacCod;
        audito_ch('M', $Dato, '30');
        PrintRespuestaJson('ok', 'Nacionalidad <strong>' . $NacDesc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_nacion')) {

    $NacCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $NacDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($NacCod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    }
    ;
    require_once __DIR__ . '/../../config/conect_mssql.php';



    /** Query revisar si el personal contiene nacionalidad. */
    $query = "SELECT PERSONAL.LegNaci FROM PERSONAL WHERE PERSONAL.LegNaci = $NacCod";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'Error al eliminar. Existe Información en Personal');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    $query = "DELETE FROM NACIONES WHERE NacCodi = $NacCod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato = 'Nacionalidad: ' . $NacDesc . ': ' . $NacCod;
        audito_ch('B', $Dato, '30');
        PrintRespuestaJson('ok', 'Nacionalidad <strong>' . $NacDesc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_provincia')) {

    $ProCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $ProDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($ProDesc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    }
    ;

    require_once __DIR__ . '/../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT PROVINCI.ProDesc FROM PROVINCI WHERE PROVINCI.ProDesc = '$ProDesc' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $ProDesc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 PROVINCI.ProCodi, PROVINCI.ProDesc FROM PROVINCI ORDER BY PROVINCI.ProCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$ProCod) {
                $ProCodi = $fila['ProCodi'] + 1;
            } else {
                $ProCodi = $ProCod;
            }
            $Dato = 'Provincia: ' . $ProDesc . ': ' . $ProCodi;

            $procedure_params = array(
                array(&$ProCodi),
                array(&$ProDesc),
                array(&$FechaHora)
            );

            $sql = "exec DATA_PROVINCIInsert @ProCodi=?,@ProDesc=?,@FechaHora=?"; /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params); /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) { /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '30');
                /** */
                PrintRespuestaJson('ok', 'Provincia: <strong>' . $ProDesc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
                PrintRespuestaJson('error', $error);
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_provincia')) {

    $ProCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $ProDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($ProDesc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    }
    ;
    require_once __DIR__ . '/../../config/conect_mssql.php';
    /** Query revisar si la descripción ya existe. */
    // $query = "SELECT PROVINCI.ProDesc FROM PROVINCI WHERE PROVINCI.ProDesc = '$ProDesc' COLLATE Latin1_General_CI_AI";
    // $result  = sqlsrv_query($link, $query, $params, $options);
    // if (sqlsrv_num_rows($result) > 0) {
    //     while ($fila = sqlsrv_fetch_array($result)) {
    //         PrintRespuestaJson('error', 'La descripción <strong>' .$ProDesc.'</strong> ya existe');
    //         sqlsrv_free_stmt($result);
    //         sqlsrv_close($link);
    //         exit;
    //     }
    // }
    /** fin */
    $query = "UPDATE PROVINCI SET ProDesc = '$ProDesc', FechaHora = SYSDATETIME() WHERE ProCodi = $ProCod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato = 'Provincia: ' . $ProDesc . ': ' . $ProCod;
        audito_ch('M', $Dato, '30');
        PrintRespuestaJson('ok', 'Provincia <strong>' . $ProDesc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_provincia')) {

    $ProCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $ProDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($ProCod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    }
    ;
    require_once __DIR__ . '/../../config/conect_mssql.php';



    /** Query revisar si el personal contiene Provincia. */
    $query = "SELECT PERSONAL.LegProv FROM PERSONAL WHERE PERSONAL.LegProv = $ProCod";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'Error al eliminar. Existe Información en Personal');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    $query = "DELETE FROM PROVINCI WHERE ProCodi = $ProCod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato = 'Provincia: ' . $ProCod . ': ' . $ProDesc;
        audito_ch('B', $Dato, '30');
        PrintRespuestaJson('ok', 'Provincia <strong>' . $ProDesc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_localidad')) {

    $LocCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $LocDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($LocDesc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    }
    ;

    require_once __DIR__ . '/../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT LOCALIDA.LocDesc FROM LOCALIDA WHERE LOCALIDA.LocDesc = '$LocDesc' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $LocDesc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 LOCALIDA.LocCodi, LOCALIDA.LocDesc FROM LOCALIDA ORDER BY LOCALIDA.LocCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$LocCod) {
                $LocCodi = $fila['LocCodi'] + 1;
            } else {
                $LocCodi = $LocCod;
            }
            $Dato = 'Localidad: ' . $LocDesc . ': ' . $LocCodi;

            $procedure_params = array(
                array(&$LocCodi),
                array(&$LocDesc),
                array(&$FechaHora)
            );

            $sql = "exec DATA_LOCALIDAInsert @LocCodi=?,@LocDesc=?,@FechaHora=?"; /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params); /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) { /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '30');
                /** */
                PrintRespuestaJson('ok', 'Localidad: <strong>' . $LocDesc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
                PrintRespuestaJson('error', $error);
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_localidad')) {

    $LocCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $LocDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($LocDesc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    }
    ;
    require_once __DIR__ . '/../../config/conect_mssql.php';
    /** Query revisar si la descripción ya existe. */
    // $query = "SELECT LOCALIDA.LocDesc FROM LOCALIDA WHERE LOCALIDA.LocDesc = '$LocDesc' COLLATE Latin1_General_CI_AI";
    // $result  = sqlsrv_query($link, $query, $params, $options);
    // if (sqlsrv_num_rows($result) > 0) {
    //     while ($fila = sqlsrv_fetch_array($result)) {
    //         PrintRespuestaJson('error', 'La descripción <strong>' .$LocDesc.'</strong> ya existe');
    //         sqlsrv_free_stmt($result);
    //         sqlsrv_close($link);
    //         exit;
    //     }
    // }
    /** fin */
    $query = "UPDATE LOCALIDA SET LocDesc = '$LocDesc', FechaHora = SYSDATETIME() WHERE LocCodi = $LocCod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato = 'Localidad: ' . $LocDesc . ': ' . $LocCod;
        audito_ch('M', $Dato, '30');

        PrintRespuestaJson('ok', 'Localidad <strong>' . $LocDesc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_localidad')) {

    $LocCod = test_input(($_POST['cod'])) ?? ''; /** Codigo */
    $LocDesc = test_input(($_POST['desc'])) ?? ''; /** Descripcion */
    // sleep(2);
    if (valida_campo($LocCod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    }
    ;
    require_once __DIR__ . '/../../config/conect_mssql.php';

    /** Query revisar si el personal contiene Localidad. */
    $query = "SELECT PERSONAL.LegLoca FROM PERSONAL WHERE PERSONAL.LegLoca = $LocCod";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'Error al eliminar. Existe Información en Personal');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */
    $Dato = 'Localidad: ' . $LocDesc . ': ' . $LocCod;

    $query = "DELETE FROM LOCALIDA WHERE LocCodi = $LocCod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        audito_ch('B', $Dato, '30');
        PrintRespuestaJson('ok', 'Localidad <strong>' . $LocDesc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }

} else {
    PrintRespuestaJson('error', 'Falta Tipo');
    exit;
}