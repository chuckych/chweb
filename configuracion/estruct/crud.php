<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
E_ALL();

FusNuloPOST('submit', '');
$FechaHora = date('Ymd H:i:s');
$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$data    = array();
$_POST['tipo'] = $_POST['tipo'] ?? false;
$_POST['cod']  = $_POST['cod'] ?? '';
$_POST['desc'] = $_POST['desc'] ?? '';
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_empresas')) {

    $Cod                   = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc                  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    $_POST['EmpProv']      = $_POST['EmpProv'] ?? '';
    $_POST['EmpLoca']      = $_POST['EmpLoca'] ?? '';
    $_POST['EmpEsta']      = $_POST['EmpEsta'] ?? '';
    $_POST['EmpAFIPTipo']  = $_POST['EmpAFIPTipo'] ?? '';
    $_POST['EmpAFIPLiqui'] = $_POST['EmpAFIPLiqui'] ?? '';
    $EmpTipo      = test_input($_POST['EmpTipo']);
    /** Empresa Tipo */
    $EmpCUIT      = test_input($_POST['EmpCUIT']);
    $EmpDomi      = test_input($_POST['EmpDomi']);
    $EmpDoNu      = test_input($_POST['EmpDoNu']);
    $EmpPiso      = test_input($_POST['EmpPiso']);
    $EmpDpto      = test_input($_POST['EmpDpto']);
    $EmpCoPo      = test_input($_POST['EmpCoPo']);
    $EmpProv      = test_input($_POST['EmpProv']);
    $EmpLoca      = test_input($_POST['EmpLoca']);
    $EmpTele      = test_input($_POST['EmpTele']);
    $EmpMail      = test_input($_POST['EmpMail']);
    $EmpCont      = test_input($_POST['EmpCont']);
    $EmpObse      = test_input($_POST['EmpObse']);
    $EmpEsta      = test_input($_POST['EmpEsta']);
    $EmpAFIPTipo  = test_input($_POST['EmpAFIPTipo']);
    $EmpAFIPLiqui = test_input($_POST['EmpAFIPLiqui']);
    $EmpEst       = '';
    $EmpCodActi   = '';
    $EmpActividad = '';
    $EmpBanco     = '';
    $EmpBanSuc    = '';
    $EmpBanCta    = '';
    $EmpLugPag    = '';
    $EmpRecibo    = '';
    $EmpLogo      = '';
    $EmpReduc     = '';
    $EmpForCta    = '';
    $EmpTipoEmpl  = '';

    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo Razón Social requerido');
        exit;
    };

    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT EMPRESAS.EmpRazon FROM EMPRESAS WHERE EMPRESAS.EmpRazon = '$Desc' COLLATE Latin1_General_CI_AI";

    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $Desc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 EMPRESAS.EmpCodi, EMPRESAS.EmpRazon FROM EMPRESAS ORDER BY EMPRESAS.EmpCodi DESC";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$Cod) {
                $EmpCodi  = $fila['EmpCodi'] + 1;
            } else {
                $EmpCodi  = $Cod;
            }
            $Dato     = 'Empresa: ' . $Desc . ': ' . $EmpCodi;

            $query = "INSERT INTO EMPRESAS( [EmpCodi],[EmpRazon],[EmpTipo],[EmpCUIT],[EmpDomi],[EmpDoNu],[EmpPiso],[EmpDpto],[EmpCoPo],[EmpProv],[EmpLoca],[EmpTele],[EmpMail],[EmpCont],[EmpObse],[EmpEsta],[EmpCodActi],[EmpActividad],[EmpBanco],[EmpBanSuc],[EmpBanCta],[EmpLugPag],[EmpRecibo],[EmpLogo],[EmpReduc],[EmpForCta],[EmpTipoEmpl],[FechaHora],[EmpAFIPTipo],[EmpAFIPLiqui] ) VALUES ( '$EmpCodi','$Desc','$EmpTipo','$EmpCUIT','$EmpDomi','$EmpDoNu','$EmpPiso','$EmpDpto','$EmpCoPo','$EmpProv','$EmpLoca','$EmpTele','$EmpMail','$EmpCont','$EmpObse','$EmpEsta','$EmpCodActi','$EmpActividad','$EmpBanco','$EmpBanSuc','$EmpBanCta','$EmpLugPag','$EmpRecibo','$EmpLogo','$EmpReduc','$EmpForCta','$EmpTipoEmpl','$FechaHora','$EmpAFIPTipo','$EmpAFIPLiqui')";
            $result  = sqlsrv_query($link, $query);
            if ($result) {
                audito_ch('A', $Dato);
                // PrintRespuestaJson('ok', 'Empresa <strong>'.$Desc.'</strong> creada correctamente');
                PrintRespuestaJson('ok', $Desc);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    $message = $value['message'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: <strong>' . $error . '</strong><br> ' . $message;
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_empresas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    $_POST['EmpProv']      = $_POST['EmpProv'] ?? '';
    $_POST['EmpLoca']      = $_POST['EmpLoca'] ?? '';
    $_POST['EmpEsta']      = $_POST['EmpEsta'] ?? '';
    $_POST['EmpAFIPTipo']  = $_POST['EmpAFIPTipo'] ?? '';
    $_POST['EmpAFIPLiqui'] = $_POST['EmpAFIPLiqui'] ?? '';
    $EmpTipo      = test_input($_POST['EmpTipo']);
    /** Empresa Tipo */
    $EmpCUIT      = test_input($_POST['EmpCUIT']);
    $EmpDomi      = test_input($_POST['EmpDomi']);
    $EmpDoNu      = test_input($_POST['EmpDoNu']);
    $EmpPiso      = test_input($_POST['EmpPiso']);
    $EmpDpto      = test_input($_POST['EmpDpto']);
    $EmpCoPo      = test_input($_POST['EmpCoPo']);
    $EmpProv      = test_input($_POST['EmpProv']);
    $EmpLoca      = test_input($_POST['EmpLoca']);
    $EmpTele      = test_input($_POST['EmpTele']);
    $EmpMail      = test_input($_POST['EmpMail']);
    $EmpCont      = test_input($_POST['EmpCont']);
    $EmpObse      = test_input($_POST['EmpObse']);
    $EmpEsta      = test_input($_POST['EmpEsta']);
    $EmpAFIPTipo  = test_input($_POST['EmpAFIPTipo']);
    $EmpAFIPLiqui = test_input($_POST['EmpAFIPLiqui']);
    $EmpEst       = '';
    $EmpCodActi   = '';
    $EmpActividad = '';
    $EmpBanco     = '';
    $EmpBanSuc    = '';
    $EmpBanCta    = '';
    $EmpLugPag    = '';
    $EmpRecibo    = '';
    $EmpLogo      = '';
    $EmpReduc     = '';
    $EmpForCta    = '';
    $EmpTipoEmpl  = '';
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';
    $query = "UPDATE EMPRESAS SET 
    EmpRazon = '$Desc',
    EmpTipo      = '$EmpTipo',
    EmpCUIT      = '$EmpCUIT',
    EmpDomi      = '$EmpDomi',
    EmpDoNu      = '$EmpDoNu',
    EmpPiso      = '$EmpPiso',
    EmpDpto      = '$EmpDpto',
    EmpCoPo      = '$EmpCoPo',
    EmpProv      = '$EmpProv',
    EmpLoca      = '$EmpLoca',
    EmpTele      = '$EmpTele',
    EmpMail      = '$EmpMail',
    EmpCont      = '$EmpCont',
    EmpObse      = '$EmpObse',
    EmpEsta      = '$EmpEsta',
    EmpAFIPTipo  = '$EmpAFIPTipo',
    EmpAFIPLiqui = '$EmpAFIPLiqui',
    EmpCodActi   = '$EmpCodActi',
    EmpActividad = '$EmpActividad',
    EmpBanco     = '$EmpBanco',
    EmpBanSuc    = '$EmpBanSuc',
    EmpBanCta    = '$EmpBanCta',
    EmpLugPag    = '$EmpLugPag',
    EmpRecibo    = '$EmpRecibo',
    EmpLogo      = '$EmpLogo',
    EmpReduc     = '$EmpReduc',
    EmpForCta    = '$EmpForCta',
    EmpTipoEmpl  = '$EmpTipoEmpl',
    FechaHora = SYSDATETIME() WHERE EmpCodi = $Cod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Empresa: ' . $Desc . ': ' . $Cod;
        audito_ch('M', $Dato);
        PrintRespuestaJson('ok', 'Empresa <strong>' . $Desc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        foreach (sqlsrv_errors() as $key => $value) {
            $error = $value['SQLSTATE'];
            $message = $value['message'];
            break;
        }
        PrintRespuestaJson('error', $error . ' ' . $message);
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_empresas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Cod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';



    /** Query revisar si el personal contiene empresa. */
    $query = "SELECT PERSONAL.LegEmpr FROM PERSONAL WHERE PERSONAL.LegEmpr = $Cod";
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

    $query = "DELETE FROM EMPRESAS WHERE EmpCodi = $Cod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Empresa: ' . $Desc . ': ' . $Cod;
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Empresa <strong>' . $Desc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_plantas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };

    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT PLANTAS.PlaDesc FROM PLANTAS WHERE PLANTAS.PlaDesc = '$Desc' COLLATE Latin1_General_CI_AI";

    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $Desc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 PLANTAS.PlaCodi, PLANTAS.PlaDesc FROM PLANTAS ORDER BY PLANTAS.PlaCodi DESC";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$Cod) {
                $Codi  = $fila['PlaCodi'] + 1;
            } else {
                $Codi  = $Cod;
            }
            $Dato     = 'Planta: ' . $Desc . ': ' . $Codi;

            $procedure_params = array(
                array(&$Codi),
                array(&$Desc),
                array(&$FechaHora)
            );

            $sql = "exec DATA_PLANTASInsert @PlaCodi=?,@PlaDesc=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato);
                /** */
                PrintRespuestaJson('ok', 'Planta: <strong>' . $Desc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_plantas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';
    $query = "UPDATE PLANTAS SET PlaDesc = '$Desc', FechaHora = SYSDATETIME() WHERE PlaCodi = $Cod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Planta: ' . $Desc . ': ' . $Cod;
        audito_ch('M', $Dato);
        PrintRespuestaJson('ok', 'Planta <strong>' . $Desc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_plantas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Cod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';



    /** Query revisar si el personal contiene Planta. */
    $query = "SELECT PERSONAL.LegPlan FROM PERSONAL WHERE PERSONAL.LegPlan = $Cod";
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

    $query = "DELETE FROM PLANTAS WHERE PlaCodi = $Cod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Planta: ' . $Cod . ': ' . $Desc;
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Planta <strong>' . $Desc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_sucur')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };

    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT SUCURSALES.SucDesc FROM SUCURSALES WHERE SUCURSALES.SucDesc = '$Desc' COLLATE Latin1_General_CI_AI";

    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $Desc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 SUCURSALES.SucCodi, SUCURSALES.SucDesc FROM SUCURSALES ORDER BY SUCURSALES.SucCodi DESC";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$Cod) {
                $Codi  = $fila['SucCodi'] + 1;
            } else {
                $Codi  = $Cod;
            }
            $Dato     = 'Sucursal: ' . $Desc . ': ' . $Codi;

            $procedure_params = array(
                array(&$Codi),
                array(&$Desc),
                array(&$FechaHora)
            );

            $sql = "exec DATA_SUCURSALESInsert @SucCodi=?,@SucDesc=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato);
                /** */
                PrintRespuestaJson('ok', 'Sucursal: <strong>' . $Desc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_sucur')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $query = "UPDATE SUCURSALES SET SucDesc = '$Desc', FechaHora = SYSDATETIME() WHERE SucCodi = $Cod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Sucursal: ' . $Desc . ': ' . $Cod;
        audito_ch('M', $Dato);

        PrintRespuestaJson('ok', 'Sucursal <strong>' . $Desc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_sucur')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Cod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si el personal contiene SUCURSALES. */
    $query = "SELECT PERSONAL.LegSucu FROM PERSONAL WHERE PERSONAL.LegSucu = $Cod";
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
    $Dato     = 'Sucursal: ' . $Desc . ': ' . $Cod;

    $query = "DELETE FROM SUCURSALES WHERE SucCodi = $Cod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Sucursal <strong>' . $Desc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_grupos')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };

    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT GRUPOS.GruDesc FROM GRUPOS WHERE GRUPOS.GruDesc = '$Desc' COLLATE Latin1_General_CI_AI";

    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $Desc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 GRUPOS.GruCodi, GRUPOS.GruDesc FROM GRUPOS ORDER BY GRUPOS.GruCodi DESC";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$Cod) {
                $Codi  = $fila['GruCodi'] + 1;
            } else {
                $Codi  = $Cod;
            }
            $Dato     = 'Grupo: ' . $Desc . ': ' . $Codi;

            $procedure_params = array(
                array(&$Codi),
                array(&$Desc),
                array(&$FechaHora)
            );

            $sql = "exec DATA_GRUPOSInsert @GruCodi=?,@GruDesc=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato);
                /** */
                PrintRespuestaJson('ok', 'Grupo: <strong>' . $Desc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_grupos')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $query = "UPDATE GRUPOS SET GruDesc = '$Desc', FechaHora = SYSDATETIME() WHERE GruCodi = $Cod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Grupo: ' . $Desc . ': ' . $Cod;
        audito_ch('M', $Dato);

        PrintRespuestaJson('ok', 'Grupo <strong>' . $Desc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_grupos')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Cod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si el personal contiene GRUPOS. */
    $query = "SELECT PERSONAL.LegGrup FROM PERSONAL WHERE PERSONAL.LegGrup = $Cod";
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
    $Dato     = 'Grupo: ' . $Desc . ': ' . $Cod;

    $query = "DELETE FROM GRUPOS WHERE GruCodi = $Cod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Grupo <strong>' . $Desc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_sector')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };

    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT SECTORES.SecDesc FROM SECTORES WHERE SECTORES.SecDesc = '$Desc' COLLATE Latin1_General_CI_AI";

    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $Desc . '</strong> ya existe');
            sqlsrv_free_stmt($result);
            sqlsrv_close($link);
            exit;
        }
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 SECTORES.SecCodi, SECTORES.SecDesc FROM SECTORES ORDER BY SECTORES.SecCodi DESC";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {
            if (!$Cod) {
                $Codi  = $fila['SecCodi'] + 1;
            } else {
                $Codi  = $Cod;
            }
            $Dato     = 'Sector: ' . $Desc . ': ' . $Codi;
            $SecTaIn = '';
            $procedure_params = array(
                array(&$Codi),
                array(&$Desc),
                array(&$SecTaIn),
                array(&$FechaHora)
            );

            $sql = "exec DATA_SECTORESInsert @SecCodi=?,@SecDesc=?,@SecTaIn=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato);
                /** */
                PrintRespuestaJson('ok', 'Sector: <strong>' . $Desc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_sector')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $query = "UPDATE SECTORES SET SecDesc = '$Desc', FechaHora = SYSDATETIME() WHERE SecCodi = $Cod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Sector: ' . $Desc . ': ' . $Cod;
        audito_ch('M', $Dato);

        PrintRespuestaJson('ok', 'Sector <strong>' . $Desc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_sector')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Cod)) {
        PrintRespuestaJson('error', 'Campo código requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si el personal contiene SECTORES. */
    $query = "SELECT PERSONAL.LegSect FROM PERSONAL WHERE PERSONAL.LegSect = $Cod";
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
    $Dato     = 'Sector: ' . $Desc . ': ' . $Cod;

    $query = "DELETE FROM SECTORES WHERE SecCodi = $Cod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Sector <strong>' . $Desc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'c_tareas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };

    require_once __DIR__ . '../../../config/conect_mssql.php';

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT TAREAS.tareDesc FROM TAREAS WHERE TAREAS.tareDesc = '$Desc' COLLATE Latin1_General_CI_AI";

    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            PrintRespuestaJson('error', 'La descripción <strong>' . $Desc . '</strong> ya existe');
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
            } else {
                $Codi  = $Cod;
            }
            $Dato     = 'Tarea: ' . $Desc . ': ' . $Codi;
            $TareEstado = 0;
            $procedure_params = array(
                array(&$Codi),
                array(&$Desc),
                array(&$TareEstado),
                array(&$FechaHora)
            );

            $sql = "exec DATA_TAREASInsert @TareCodi=?,@tareDesc=?,@TareEstado=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato);
                /** */
                PrintRespuestaJson('ok', 'Tarea: <strong>' . $Desc . '</strong> creada correctamente.');
                sqlsrv_free_stmt($result);
                sqlsrv_close($link);
                exit;
            } else {
                foreach (sqlsrv_errors() as $key => $value) {
                    $error = $value['SQLSTATE'];
                    break;
                }
                $error = ($error == '23000') ? 'El Codigo ya existe' : 'Error: ' . $error;
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
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'u_tareas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Desc)) {
        PrintRespuestaJson('error', 'Campo descripción requerido');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $query = "UPDATE TAREAS SET tareDesc = '$Desc', FechaHora = SYSDATETIME() WHERE TareCodi = $Cod";

    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        $Dato     = 'Tarea: ' . $Desc . ': ' . $Cod;
        audito_ch('M', $Dato);

        PrintRespuestaJson('ok', 'Tarea <strong>' . $Desc . '</strong> modificada correctamente');
        /** Si se Guardo con exito */
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'd_tareas')) {

    $Cod  = test_input(($_POST['cod'])) ?? '';
    /** Codigo */
    $Desc  = test_input(($_POST['desc'])) ?? '';
    /** Descripcion */
    // sleep(2);
    if (valida_campo($Cod)) {
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
    $Dato     = 'Tarea: ' . $Desc . ': ' . $Cod;

    $query = "DELETE FROM TAREAS WHERE TareCodi = $Cod";
    $rs = sqlsrv_query($link, $query);
    if ($rs) {
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Tarea <strong>' . $Desc . '</strong> eliminada correctamente');
        sqlsrv_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', sqlsrv_errors());
        sqlsrv_close($link);
        exit;
    }
} else if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['tipo'] == 'Reasign')) {

    $_POST['value']         = $_POST['value'] ?? '';
    $_POST['EstructActual'] = $_POST['EstructActual'] ?? '';
    $_POST['checks']        = $_POST['checks'] ?? '';
    $_POST['selectEstruc']  = $_POST['selectEstruc'] ?? '';
    $_POST['EstructName']   = $_POST['EstructName'] ?? '';
    $Checks                 = $_POST['checks'];
    $Estructura             = test_input($_POST['value']);
    $selectEstruc           = test_input($_POST['selectEstruc']);
    $EstructActual          = test_input($_POST['EstructActual']);
    $EstructName            = test_input($_POST['EstructName']);

    $EstructActual = explode('@', $EstructActual);
    $actualCod  = ($EstructActual[0]);
    $actualDes  = ($EstructActual[1]);
    $ActualCD = '(' . $actualCod . ') ' . $actualDes;
    // PrintRespuestaJson('error', $Estructura);
    // exit;
    
    // sleep(2);
    if (valida_campo($Checks)) {
        PrintRespuestaJson('error', 'Debe seleccionar al menos un Legajo');
        exit;
    };
    if (valida_campo($selectEstruc)) {
        PrintRespuestaJson('error', ' Debe seleccionar una entidad');
        exit;
    };
    if (valida_campo($Estructura)) {
        PrintRespuestaJson('error', 'Falta Valor');
        exit;
    };
    if (valida_campo($EstructActual)) {
        PrintRespuestaJson('error', 'Falta EstructActual');
        exit;
    };
    require_once __DIR__ . '../../../config/conect_mssql.php';
    foreach ($Checks as $key => $valor) {
        // sleep(1);
        $Checks    = explode('@', $valor);
        $ChecksCod = test_input($Checks[0]);
        $ChecksDes = test_input($Checks[1]);
        $LegApNo   = '(' . $ChecksCod . ') ' . $ChecksDes;
        $Dato      = 'Legajo: ' . $LegApNo.'. ' . $Estructura . ': ' . $ActualCD . ', por (' . $selectEstruc . ') ' . $EstructName;

        switch ($Estructura) {
            case 'Empresa':
                $Col = 'LegEmpr';
                break;
            case 'Planta':
                $Col = 'LegPlan';
                break;
            case 'Sucursal':
                $Col = 'LegSucu';
                break;
            case 'Sector':
                $Col = 'LegSect';
                break;
            case 'Grupo':
                $Col = 'LegGrup';
                break;
            case 'Tarea':
                $Col = 'LegTareProd';
                break;
            case 'Sector':
                $Col = 'LegSect';
                break;
        }
        $query = "UPDATE PERSONAL SET $Col = '$selectEstruc' WHERE LegNume = '$ChecksCod'";
        $rs = sqlsrv_query($link, $query);
        if ($rs) {
            audito_ch2('M', $Dato);
        } else {
            foreach (sqlsrv_errors() as $key => $v) {
                $error = $v['SQLSTATE'];
                $message = $v['message'];
                break;
                PrintRespuestaJson('error', $message);
                sqlsrv_close($link);
                exit;
            }
        }

    }
    PrintRespuestaJson('ok', 'Legajos Reasignados correctamente');
    sqlsrv_close($link);
    exit;

} else {
    PrintRespuestaJson('error', 'Falta Tipo');
    exit;
}
