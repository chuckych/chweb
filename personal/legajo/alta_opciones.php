<?php
require __DIR__ . '../../../config/index.php';
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

$token = sha1($_SESSION['RECID_CLIENTE']);
$pathApiCH = gethostCHWeb() . "/" . HOMEHOST . "/api";

// $_POST['dato']           = $_POST['dato'] ?? '';
FusNuloPOST('dato', '');
$_POST['alta_localidad'] = $_POST['alta_localidad'] ?? '';
$_POST['alta_provincia'] = $_POST['alta_provincia'] ?? '';
$_POST['alta_nacion'] = $_POST['alta_nacion'] ?? '';
$_POST['dato_conv'] = $_POST['dato_conv'] ?? '';
$_POST['alta-diasvac'] = $_POST['alta-diasvac'] ?? '';
$_POST['del_ConvVac'] = $_POST['del_ConvVac'] ?? '';
$_POST['alta-feriConv'] = $_POST['alta-feriConv'] ?? '';
$_POST['del_ConvFeri'] = $_POST['del_ConvFeri'] ?? '';
$_POST['DelPerineg'] = $_POST['DelPerineg'] ?? '';
$_POST['PERPREMI'] = $_POST['PERPREMI'] ?? '';
$_POST['DelPerPremi'] = $_POST['DelPerPremi'] ?? '';
$_POST['OTROCONLEG'] = $_POST['OTROCONLEG'] ?? '';
$_POST['DelOtroConLeg'] = $_POST['DelOtroConLeg'] ?? '';
$_POST['PERHOAL'] = $_POST['PERHOAL'] ?? '';
$_POST['DelPerHoAl'] = $_POST['DelPerHoAl'] ?? '';
$_POST['IDENTIFICA'] = $_POST['IDENTIFICA'] ?? '';
$_POST['DelIdentifica'] = $_POST['DelIdentifica'] ?? '';
$_POST['GrupoHabi'] = $_POST['GrupoHabi'] ?? '';
$_POST['DelPerrelo'] = $_POST['DelPerrelo'] ?? '';
$_POST['PERRELO'] = $_POST['PERRELO'] ?? '';
$_POST['OTROConValor'] = $_POST['OTROConValor'] ?? '';
$_POST['EmpDomi'] = $_POST['EmpDomi'] ?? '';
$_POST['EmpProv'] = $_POST['EmpProv'] ?? '';
$_POST['EmpLoca'] = $_POST['EmpLoca'] ?? '';
$_POST['EmpCodi'] = $_POST['EmpCodi'] ?? '';

/** ALTA NACIONES */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_nacion'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $NacDesc = (test_input(($_POST['NacDesc'])));
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');
    // sleep(2);
    if (valida_campo($_POST['NacDesc'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT NACIONES.NacDesc
        FROM NACIONES WHERE NACIONES.NacDesc = '$NacDesc' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $NacDesc);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 NACIONES.NacCodi, NACIONES.NacDesc
    FROM NACIONES
    ORDER BY NACIONES.NacCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $NacCodi = $fila['NacCodi'] + 1;
            $Dato = 'Nacionalidad: ' . $NacDesc . ': ' . $NacCodi;

            $procedure_params = array(
                array(&$NacCodi),
                array(&$NacDesc),
                array(&$FechaHora)
            );

            $sql = "exec DATA_NACIONESInsert @NacCodi=?,@NacDesc=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(1);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $NacCodi, 'desc' => $NacDesc);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $NacCodi, 'desc' => $NacDesc);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA PROVINCIAS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_provincia'] == 'alta_provincia')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_prov = (test_input(($_POST['desc_prov'])));
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');

    if (valida_campo($_POST['desc_prov'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT PROVINCI.ProDesc FROM PROVINCI WHERE PROVINCI.ProDesc = '$desc_prov' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_prov);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 PROVINCI.ProCodi, PROVINCI.ProDesc
    FROM PROVINCI
    ORDER BY PROVINCI.ProCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $ProCodi = $fila['ProCodi'] + 1;
            $Dato = 'Provincia: ' . $desc_prov . ': ' . $ProCodi;

            $procedure_params = array(
                array(&$ProCodi),
                array(&$desc_prov),
                array(&$FechaHora)
            );

            $sql = "exec DATA_PROVINCIInsert @ProCodi=?,@ProDesc=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(1);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $ProCodi, 'desc' => $desc_prov);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $ProCodi, 'desc' => $desc_prov);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA LOCALIDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_localidad'] == 'alta_localidad')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_local = test_input($_POST['desc_local']);
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');
    // print_r($desc_local);
    // exit;
    if (valida_campo($_POST['desc_local'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT LOCALIDA.LocDesc FROM LOCALIDA WHERE LOCALIDA.LocDesc = '$desc_local' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_local);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 LOCALIDA.LocCodi, LOCALIDA.LocDesc
    FROM LOCALIDA
    ORDER BY LOCALIDA.LocCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $LocCodi = $fila['LocCodi'] + 1;
            $Dato = 'Localidad: ' . $desc_local . ': ' . $LocCodi;

            $procedure_params = array(
                array(&$LocCodi),
                array(&$desc_local),
                array(&$FechaHora)
            );

            $sql = "exec DATA_LOCALIDAInsert @LocCodi=?,@LocDesc=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(1);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $LocCodi, 'desc' => $desc_local);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $LocCodi, 'desc' => $desc_local);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA PLANTA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_planta')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $systemVersion = explode('_', $_SESSION['VER_DB_CH']);
    $systemVersion = intval($systemVersion[1]) ?? '';

    if ($systemVersion >= 70) { // si la version es mayor o igual a 7.0

        $payload = Flight::request()->data;

        if (valida_campo($payload['desc_planta'] ?? '')) {
            $data = array('status' => 'requerido');
            Flight::json($data);
            exit;
        }

        $payload = [
            "Estruct" => "Plan",
            "Desc" => $payload['desc_planta'],
            "EvEntra" => "",
            "EvSale" => ""
        ];

        $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/estructuras/alta";
        $rs = curlAPI($endpoint, $payload, 'POST', $token);
        $result = json_decode($rs, true);
        $result['MESSAGE'] = $result['MESSAGE'] ?? 'ERROR';

        if ($result['MESSAGE'] == "OK") {
            $PlaCodi = $result['DATA']['Cod'];
            $desc_planta = $result['DATA']['Desc'];
            $Dato = 'Planta: ' . $desc_planta . ': ' . $PlaCodi;
            audito_ch('A', $Dato, '10');
            $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $PlaCodi, 'desc' => $desc_planta);
        } else {
            $data = array('status' => 'error', 'dato' => $result['MESSAGE'], 'cod' => '', 'desc' => $payload['desc_planta']);
        }
        Flight::json($data);
        exit;
    }

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_planta = test_input($_POST['desc_planta']);
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['desc_planta'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT PLANTAS.PlaDesc FROM PLANTAS WHERE PLANTAS.PlaDesc = '$desc_planta' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_planta);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo código disponible y sumarle 1 */
    $query = "SELECT TOP 1 PLANTAS.PlaCodi, PLANTAS.PlaDesc
    FROM PLANTAS
    ORDER BY PLANTAS.PlaCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $PlaCodi = $fila['PlaCodi'] + 1;
            $Dato = 'Planta: ' . $desc_planta . ': ' . $PlaCodi;

            $procedure_params = array(
                array(&$PlaCodi),
                array(&$desc_planta),
                array(&$FechaHora)
            );

            $sql = "exec DATA_PLANTASInsert @PlaCodi=?,@PlaDesc=?,@FechaHora=?";
            /** Query del Store Procedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(1);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $PlaCodi, 'desc' => $desc_planta);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $PlaCodi, 'desc' => $desc_planta);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA EMPRESAS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_empresa')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();
    $EmpRazon = test_input($_POST['EmpRazon']);
    /** Razon Social */
    $EmpTipo = test_input($_POST['EmpTipo']);
    /** Empresa Tipo */
    $EmpCUIT = test_input($_POST['EmpCUIT']);
    $EmpDomi = test_input($_POST['EmpDomi']);
    $EmpDoNu = test_input($_POST['EmpDoNu']);
    $EmpPiso = test_input($_POST['EmpPiso']);
    $EmpDpto = test_input($_POST['EmpDpto']);
    $EmpCoPo = test_input($_POST['EmpCoPo']);
    $EmpProv = test_input($_POST['EmpProv']);
    $EmpLoca = test_input($_POST['EmpLoca']);
    $EmpTele = test_input($_POST['EmpTele']);
    $EmpMail = test_input($_POST['EmpMail']);
    $EmpCont = test_input($_POST['EmpCont']);
    $EmpObse = test_input($_POST['EmpObse']);
    $EmpEst = '';
    $EmpCodActi = '';
    $EmpActividad = '';
    $EmpBanco = '';
    $EmpBanSuc = '';
    $EmpBanCta = '';
    $EmpLugPag = '';
    $EmpRecibo = '';
    $EmpLogo = '';
    $EmpReduc = '';
    $EmpForCta = '';
    $EmpTipoEmpl = '';
    $FechaHora = date('Ymd H:i:s');

    if (valida_campo($_POST['EmpRazon'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT EMPRESAS.EmpRazon FROM EMPRESAS WHERE EMPRESAS.EmpRazon = '$EmpRazon' COLLATE Latin1_General_CI_AI";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $EmpRazon);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        sqlsrv_close($link);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 EMPRESAS.EmpCodi
    FROM EMPRESAS
    ORDER BY EMPRESAS.EmpCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);


    while ($fila = sqlsrv_fetch_array($result)) {
        $EmpCodi = $fila['EmpCodi'] + 1;
    }

    $EmpCodi = $EmpCodi ?? '1';

    $Dato = 'Empresa: ' . $EmpCodi . ': ' . $EmpRazon;

    //$procedure_params=array( array(&$EmpCodi), array(&$EmpRazon), array(&$EmpTipo), array(&$EmpCUIT), array(&$EmpDomi), array(&$EmpDoNu), array(&$EmpPiso), array(&$EmpDpto), array(&$EmpCoPo), array(&$EmpProv), array(&$EmpLoca), array(&$EmpTele), array(&$EmpMail), array(&$EmpCont), array(&$EmpObse), array(&$EmpEst), array(&$EmpCodActi), array(&$EmpActividad), array(&$EmpBanco), array(&$EmpBanSuc), array(&$EmpBanCta), array(&$EmpLugPag), array(&$EmpRecibo), array(&$EmpLogo), array(&$EmpReduc), array(&$EmpForCta), array(&$EmpTipoEmpl), array(&$FechaHora), );
    // echo json_encode($procedure_params);exit;

    //$sql="exec DATA_EMPRESASInsert @EmpCodi=?, @EmpRazon=?, @EmpTipo=?, @EmpCUIT=?, @EmpDomi=?, @EmpDoNu=?, @EmpPiso=?, @EmpDpto=?, @EmpCoPo=?, @EmpProv=?, @EmpLoca=?, @EmpTele=?, @EmpMail=?, @EmpCont=?, @EmpObse=?, @EmpEsta=?, @EmpCodActi=?, @EmpActividad=?, @EmpBanco=?, @EmpBanSuc=?, @EmpBanCta=?, @EmpLugPag=?, @EmpRecibo=?, @EmpLogo=?, @EmpReduc=?, @EmpForCta=?, @EmpTipoEmpl=?, @FechaHora=?"; /** Query del Store Prcedure */
    //$stmt = sqlsrv_prepare($link, $sql, $procedure_params); /** preparar la sentencia */

    // if (!$stmt) {
    //     die(print_r(sqlsrv_errors(), true));
    // }
    if (InsertRegistro("INSERT INTO EMPRESAS( [EmpCodi],[EmpRazon],[EmpTipo],[EmpCUIT],[EmpDomi],[EmpDoNu],[EmpPiso],[EmpDpto],[EmpCoPo],[EmpProv],[EmpLoca],[EmpTele],[EmpMail],[EmpCont],[EmpObse],[EmpEsta],[EmpCodActi],[EmpActividad],[EmpBanco],[EmpBanSuc],[EmpBanCta],[EmpLugPag],[EmpRecibo],[EmpLogo],[EmpReduc],[EmpForCta],[EmpTipoEmpl],[FechaHora],[EmpAFIPTipo],[EmpAFIPLiqui] ) values ( '$EmpCodi','$EmpRazon','$EmpTipo','$EmpCUIT','$EmpDomi','$EmpDoNu','$EmpPiso','$EmpDpto','$EmpCoPo','$EmpProv','$EmpLoca','$EmpTele','$EmpMail','$EmpCont','$EmpObse','$EmpEsta','$EmpCodActi','$EmpActividad','$EmpBanco','$EmpBanSuc','$EmpBanCta','$EmpLugPag','$EmpRecibo','$EmpLogo','$EmpReduc','$EmpForCta','$EmpTipoEmpl','$FechaHora','$EmpAFIPTipo','$EmpAFIPLiqui')")) {
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(1);
        $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $EmpCodi, 'desc' => $EmpRazon);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $EmpCodi, 'desc' => $EmpRazon);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_free_stmt($result);

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA SECTOR */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_sector')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_sector = test_input($_POST['desc_sector']);
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['desc_sector'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT SECTORES.SecDesc FROM SECTORES WHERE SECTORES.SecDesc = '$desc_sector' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_sector);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 SECTORES.SecCodi, SECTORES.SecDesc
    FROM SECTORES
    ORDER BY SECTORES.SecCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $SecCodi = $fila['SecCodi'] + 1;
            $Dato = 'Sector: ' . $desc_sector . ': ' . $SecCodi;
            $SecTaIn = '';

            $procedure_params = array(
                array(&$SecCodi),
                array(&$desc_sector),
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

                try {
                    $Se2Codi = 0;
                    $Se2Desc = '';
                    $procedure_params_seccion = array(
                        array(&$SecCodi),
                        array(&$Se2Codi),
                        array(&$Se2Desc),
                        array(&$FechaHora)
                    );
                    // INSERTAMOS EN LA TABLA SECCIONES EL REGISTRO CON VALOR 0
                    $sql = "exec DATA_SECCIONInsert @SecCodi=?,@Se2Codi=?,@Se2Desc=?,@FechaHora=?";
                    $stmt = sqlsrv_prepare($link, $sql, $procedure_params_seccion);
                    if (!$stmt) {
                        throw new Exception(sqlsrv_errors());
                    }
                    sqlsrv_execute($stmt); // ejecuto la sentencia
                } catch (Exception $e) {
                    echo $e->getMessage();
                    $data = array('status' => 'Error', 'dato' => $e->getMessage());
                    echo json_encode($data);
                    exit;
                }


                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(1);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $SecCodi, 'desc' => $desc_sector);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $SecCodi, 'desc' => $desc_sector);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA SECCION */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_seccion')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $Se2Desc = test_input($_POST['Se2Desc']);
    /** Descripcion */
    $SecCodi = test_input($_POST['SecCodi']);
    $Se2Desc = test_input($_POST['Se2Desc']);
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['Se2Desc'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT SECCION.Se2Desc FROM SECCION WHERE SECCION.Se2Desc = '$Se2Desc' COLLATE Latin1_General_CI_AI AND SECCION.SecCodi='$SecCodi'";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $Se2Desc);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 SECCION.Se2Codi
    FROM SECCION WHERE SECCION.SecCodi = '$SecCodi'
    ORDER BY SECCION.Se2Codi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);

    while ($fila = sqlsrv_fetch_array($result)) {
        $Se2Codi = $fila['Se2Codi'] + 1;
    }

    $Se2Codi = $Se2Codi ?? '1';
    $Dato = 'Seccion: ' . $Se2Codi . ': ' . $Se2Desc . '. Sector: ' . $SecCodi;

    $procedure_params = array(
        array(&$SecCodi),
        array(&$Se2Codi),
        array(&$Se2Desc),
        array(&$FechaHora)
    );
    // echo json_encode($procedure_params);exit;

    $sql = "exec DATA_SECCIONInsert @SecCodi=?,@Se2Codi=?,@Se2Desc=?,@FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(1);
        $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $Se2Codi, 'desc' => $Se2Desc);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3], 'cod' => $Se2Codi, 'desc' => $Se2Desc);
            }
        }

        echo json_encode($data[0]);
        // $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $Se2Codi, 'desc' => $Se2Desc);
        // echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }
    // }
    sqlsrv_free_stmt($result);
    // } else {
    //     echo json_encode('Error');
    // }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA GRUPOS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_grupo')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_grupo = test_input($_POST['desc_grupo']);
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['desc_grupo'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT GRUPOS.GruDesc FROM GRUPOS WHERE GRUPOS.GruDesc = '$desc_grupo' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_grupo);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 GRUPOS.GruCodi, GRUPOS.GruDesc
    FROM GRUPOS
    ORDER BY GRUPOS.GruCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $GruCodi = $fila['GruCodi'] + 1;
            $Dato = 'Grupo: ' . $desc_grupo . ': ' . $GruCodi;

            $procedure_params = array(
                array(&$GruCodi),
                array(&$desc_grupo),
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
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(1);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $GruCodi, 'desc' => $desc_grupo);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $GruCodi, 'desc' => $desc_grupo);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA SUCURSALES */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_sucur')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_sucur = test_input($_POST['desc_sucur']);
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['desc_sucur'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT SUCURSALES.SucDesc FROM SUCURSALES WHERE SUCURSALES.SucDesc = '$desc_sucur' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_sucur);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 SUCURSALES.SucCodi, SUCURSALES.SucDesc
    FROM SUCURSALES
    ORDER BY SUCURSALES.SucCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $SucCodi = $fila['SucCodi'] + 1;
            $Dato = 'Sucursal: ' . $desc_sucur . ': ' . $SucCodi;

            $procedure_params = array(
                array(&$SucCodi),
                array(&$desc_sucur),
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
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(3);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $SucCodi, 'desc' => $desc_sucur);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $SucCodi, 'desc' => $desc_sucur);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA TAREAS DE PRODUCCIÓN */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_tarea')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_tarea = test_input($_POST['desc_tarea']);
    /** Descripcion */
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['desc_tarea'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT TAREAS.TareDesc FROM TAREAS WHERE TAREAS.TareDesc = '$desc_tarea' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_tarea);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 TAREAS.TareCodi, TAREAS.TareDesc
    FROM TAREAS
    ORDER BY TAREAS.TareCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $TareCodi = $fila['TareCodi'] + 1;
            $Dato = 'Tareas Prod: ' . $desc_tarea . ': ' . $TareCodi;
            $TareEstado = '0';

            $procedure_params = array(
                array(&$TareCodi),
                array(&$desc_tarea),
                array(&$TareEstado),
                array(&$FechaHora)
            );

            $sql = "exec DATA_TAREASInsert @TareCodi=?,@TareDesc=?,@TareEstado=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(3);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $TareCodi, 'desc' => $desc_tarea);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $TareCodi, 'desc' => $desc_tarea);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA CONVENIOS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato_conv'] == 'alta_convenio')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_convenio = test_input($_POST['desc_convenio']);
    /** Descripcion */
    $ConDias = test_input($_POST['ConDias']);
    /** 1 día cada 'ConDias' trabajados */
    $ConTDias = test_input($_POST['ConTDias']);
    /** Dias Tipo */
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['desc_convenio'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;

    /** Query revisar si la descripción ya existe. */
    $query = "SELECT CONVENIO.ConDesc FROM CONVENIO WHERE CONVENIO.ConDesc = '$desc_convenio' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_convenio);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    /** Query para obtener el ultimo codigo disponible y sumarle 1 */
    $query = "SELECT TOP 1 CONVENIO.ConCodi, CONVENIO.ConDesc
    FROM CONVENIO
    ORDER BY CONVENIO.ConCodi DESC";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {

        while ($fila = sqlsrv_fetch_array($result)) {

            $ConCodi = $fila['ConCodi'] + 1;
            $Dato = 'Convenio: ' . $desc_convenio . ': ' . $ConCodi;
            $TareEstado = '0';

            $procedure_params = array(
                array(&$ConCodi),
                array(&$desc_convenio),
                array(&$ConDias),
                array(&$ConTDias),
                array(&$FechaHora)
            );

            $sql = "exec DATA_CONVENIOInsert @ConCodi=?,@ConDesc=?,@ConDias=?,@ConTDias=?,@FechaHora=?";
            /** Query del Store Prcedure */
            $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
            /** preparar la sentencia */

            if (!$stmt) {
                die(print_r(sqlsrv_errors(), true));
            }
            if (sqlsrv_execute($stmt)) {
                /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
                audito_ch('A', $Dato, '10');
                /** */
                // sleep(3);
                $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $ConCodi, 'desc' => $desc_convenio, 'ConDias' => $ConDias, 'ConTDias' => $ConTDias);
                echo json_encode($data);
                /** retorno resultados en formato json */
            } else {
                $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $ConCodi, 'desc' => $desc_convenio);
                echo json_encode($data);
                die(print_r(sqlsrv_errors(), true));
            }
        }
        sqlsrv_free_stmt($result);
    } else {
        echo json_encode('Error');
    }
    sqlsrv_close($link);

    // echo json_encode($data);

}
/** UPDATE CONVENIOS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato_conv'] == 'mod_convenio')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $desc_convenio = test_input($_POST['desc_convenio']);
    /** Descripcion */
    $codConv = test_input($_POST['codConv']);
    /** 1 día cada 'ConDias' trabajados */
    $ConDias = test_input($_POST['ConDias']);
    /** 1 día cada 'ConDias' trabajados */
    $ConTDias = test_input($_POST['ConTDias']);
    /** Dias Tipo */
    $FechaHora = date('Ymd H:i:s');


    if (valida_campo($_POST['desc_convenio'])) {
        $data = array('status' => 'requerido');
        echo json_encode($data);
        exit;
    }
    ;
    /** Query para comparar datos de la bd con lo enviado. */
    $query = "SELECT CONVENIO.ConDesc FROM CONVENIO WHERE CONVENIO.ConCodi = '$codConv' AND CONVENIO.ConDesc = '$desc_convenio' COLLATE Latin1_General_CI_AI AND CONVENIO.ConDias='$ConDias' AND CONVENIO.ConTDias = '$ConTDias'";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'nomod', 'desc' => $desc_convenio);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */
    /** Query revisar si la descripción ya existe. */
    $query = "SELECT CONVENIO.ConDesc FROM CONVENIO WHERE CONVENIO.ConCodi != '$codConv' AND CONVENIO.ConDesc = '$desc_convenio' COLLATE Latin1_General_CI_AI";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'duplicado', 'desc' => $desc_convenio);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    $Dato = 'Convenio: ' . $desc_convenio . ': ' . $codConv;
    $TareEstado = '0';

    $procedure_params = array(
        array(&$codConv),
        array(&$desc_convenio),
        array(&$ConDias),
        array(&$ConTDias),
        array(&$FechaHora)
    );

    $sql = "exec DATA_CONVENIOUpdate @ConCodi=?,@ConDesc=?,@ConDias=?,@ConTDias=?,@FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('M', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'okm', 'dato' => $Dato, 'cod' => $codConv, 'desc' => $desc_convenio, 'ConDias' => $ConDias, 'ConTDias' => $ConTDias);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato, 'cod' => $codConv, 'desc' => $desc_convenio);
        echo json_encode($data);
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTAS DIAS VACACIONES CONVENIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta-diasvac'] == 'alta-diasvac')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';
    // $data = array('status' => 'diasvac_ok');
    // echo json_encode($data);exit;
    if (valida_campo($_POST['cod-diasvac'])) {
        $data = array('status' => 'cod_requerido');
        echo json_encode($data);
        exit;
    }
    ;

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $CVConv = test_input($_POST['cod-diasvac']);
    /** cod-diasvac */
    $CVAnios = test_input($_POST['anios']);
    /** Años */
    $CVMeses = test_input($_POST['meses']);
    /** Meses */
    $CVDias = test_input($_POST['diasvac']);
    /** Diasvac */
    $FechaHora = date('Ymd H:i:s');

    $Dato = 'Antiguedad Convenio: ' . $CVConv . ': ' . $CVAnios . '-' . $CVMeses . '-' . $CVDias;

    /** Query revisar si existe un registro igual */
    $query = "SELECT CONVVACA.CVConv FROM CONVVACA WHERE CONVVACA.CVConv = '$CVConv' AND CONVVACA.CVAnios = '$CVAnios' AND CONVVACA.CVMeses = '$CVMeses'";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'existe', 'dato' => 'Conv: ' . $CVConv . '. Años: ' . $CVAnios . '. Meses: ' . $CVMeses);
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */
    $procedure_params = array(
        array(&$CVConv),
        array(&$CVAnios),
        array(&$CVMeses),
        array(&$CVDias),
        array(&$FechaHora)
    );

    $sql = "exec DATA_CONVVACAInsert @CVConv=?,@CVAnios=?,@CVMeses=?,@CVDias=?,@FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** BAJAS DIAS VACACIONES CONVENIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['del_ConvVac'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $CVConv = test_input($_POST['del_cod']);
    /** cod-diasvac */
    $CVAnios = test_input($_POST['del_anios']);
    /** Años */
    $CVMeses = test_input($_POST['del_meses']);
    /** Meses */
    $CVDias = test_input($_POST['del_dias']);
    /** Meses */
    $FechaHora = date('Ymd H:i:s');

    $Dato = 'Antiguedad Convenio: ' . $CVConv . ': ' . $CVAnios . '-' . $CVMeses . '-' . $CVDias;

    $procedure_params = array(
        array(&$CVConv),
        array(&$CVAnios),
        array(&$CVMeses),
    );

    $sql = "exec DATA_CONVVACADelete @CVConv=?,@CVAnios=?,@CVMeses=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('B', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok_delete', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error_delete', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTAS FERIADOS CONVENIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta-feriConv'] == 'alta-feriConv')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';
    // $data = array('status' => 'diasvac_ok');
    // echo json_encode($data);exit;
    if (valida_campo($_POST['CFConv']) || valida_campo($_POST['CFFech']) || valida_campo($_POST['CFDesc'])) {
        $data = array('status' => 'requeridos');
        echo json_encode($data);
        exit;
    }
    ;

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $FechaHora = date('Ymd H:i:s');

    $_POST['CFInfM'] = $_POST['CFInfM'] ?? '';
    $_POST['CFInfJ'] = $_POST['CFInfJ'] ?? '';
    $_POST['CFInFeTR'] = $_POST['CFInFeTR'] ?? '';

    $CFConv = test_input($_POST['CFConv']);
    $CFFech = test_input(FechaString($_POST['CFFech']));
    $CFDesc = test_input($_POST['CFDesc']);
    $CFCodM = test_input($_POST['CFCodM']);
    $CFCodJ = test_input($_POST['CFCodJ']);
    $CFInfM = ($_POST['CFInfM'] == 'on') ? '1' : '0';
    $CFInfJ = ($_POST['CFInfJ'] == 'on') ? '1' : '0';
    ;
    $CFCodM2 = test_input($_POST['CFCodM2']);
    $CFCodJ2 = test_input($_POST['CFCodJ2']);
    $CFCodM3 = test_input($_POST['CFCodM3']);
    $CFCodJ3 = test_input($_POST['CFCodJ3']);
    $CFInFeTR = ($_POST['CFInFeTR'] == 'on') ? '1' : '0';
    $CFInMeNL = test_input($_POST['CFInMeNL']);
    $CFInJoNL = test_input($_POST['CFInJoNL']);


    $Dato = 'Feriado Convenio: ' . $CFConv . '. Fecha: ' . Fech_Format_Var($CFFech, 'd/m/Y') . '. Desc: ' . $CFDesc . ' CFInFeTR = ' . $CFInFeTR;

    /** Query revisar si existe un registro igual */
    $query = "SELECT CONVFERI.CFConv FROM CONVFERI WHERE CONVFERI.CFConv = '$CFConv' AND CONVFERI.CFFech = '$CFFech'";
    // print_r($query);exit;

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'existe', 'dato' => 'Feriado convenio: ' . $CFConv . '. Fecha: ' . Fech_Format_Var($CFFech, 'd/m/Y'));
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */

    $procedure_params = array(
        array(&$CFConv),
        array(&$CFFech),
        array(&$CFDesc),
        array(&$CFCodM),
        array(&$CFCodJ),
        array(&$CFInfM),
        array(&$CFInfJ),
        array(&$CFCodM2),
        array(&$CFCodJ2),
        array(&$CFCodM3),
        array(&$CFCodJ3),
        array(&$CFInFeTR),
        array(&$CFInMeNL),
        array(&$CFInJoNL),
        array(&$FechaHora)
    );

    $sql = "exec DATA_CONVFERIInsert @CFConv=?,@CFFech=?,@CFDesc=?,@CFCodM=?,@CFCodJ=?,@CFInfM=?,@CFInfJ=?,@CFCodM2=?,@CFCodJ2=?,@CFCodM3=?,@CFCodJ3=?,@CFInFeTR=?,@CFInMeNL=?,@CFInJoNL=?,@FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok', 'dato' => $Dato, 'cod' => $CFConv);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** BAJAS DIAS FERIADOS CONVENIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['del_ConvFeri'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $CFConv = test_input($_POST['CFConv']);
    $CFFech = test_input($_POST['CFFech']);
    $CFDesc = test_input($_POST['CFDesc']);

    $FechaHora = date('Ymd H:i:s');

    $Dato = 'Feriado Convenio: ' . $CFConv . '. Fecha: ' . Fech_Format_Var($CFFech, 'd/m/Y') . '. Desc: ' . $CFDesc;

    $procedure_params = array(
        array(&$CFConv),
        array(&$CFFech),
    );

    $sql = "exec DATA_CONVFERIDelete @CFConv=?,@CFFech=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('B', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok_delete', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error_delete', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTA HISTORIAl INGRESOS LEGAJOS  */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'alta_perineg')) {

    if (valida_campo($_POST['InEgLega']) || valida_campo($_POST['InEgFeIn'])) {
        $data = array('status' => 'requeridos', 'dato' => 'Fecha de Ingreso es requerida');
        echo json_encode($data);
        exit;
    }
    ;

    FusNuloPOST('InEgFeIn', '');
    FusNuloPOST('InEgFeEg', '');
    FusNuloPOST('InEgCaus', '');

    $InEgLega = test_input($_POST['InEgLega']);

    $InEgFeIn = test_input(($_POST['InEgFeIn']));
    $InEgFeIn = !empty(($InEgFeIn)) ? dr_fecha($InEgFeIn, 'Y-m-d') : '';

    $InEgFeEg = test_input(($_POST['InEgFeEg']));
    $InEgFeEg = !empty(($InEgFeEg)) ? dr_fecha($InEgFeEg, 'Y-m-d') : '';

    $InEgCaus = test_input($_POST['InEgCaus']);

    $payload = array(
        "Lega" => $InEgLega,
        "FeIn" => $InEgFeIn,
        "FeEg" => $InEgFeEg,
        "Caus" => $InEgCaus
    );

    // print_r($payload).exit;

    $sendApi['DATA'] = $sendApi['DATA'] ?? '';
    $sendApi['MESSAGE'] = $sendApi['MESSAGE'] ?? '';

    $sendApi = curlAPI("$pathApiCH/perineg/", $payload, 'POST', $token);
    $sendApi = json_decode($sendApi, true);

    $Dato = 'Leg: ' . $InEgLega . '. In: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y') . '. Eg: ' . Fech_Format_Var($InEgFeEg, 'd/m/Y');

    if ($sendApi['MESSAGE'] == 'OK') {

        audito_ch('A', $Dato, '10');
        $data = array('status' => 'ok', 'dato' => $sendApi['DATA']);
        echo json_encode($data);

    } else {

        $data = array('status' => $sendApi['MESSAGE'], 'dato' => $sendApi['DATA']);
        echo json_encode($data);

    }

    exit;



    // require_once __DIR__ . '../../../config/conect_mssql.php';
    // if (valida_campo($_POST['InEgLega']) || valida_campo($_POST['InEgFeIn'])) {
    //     $data = array('status' => 'requeridos');
    //     echo json_encode($data);
    //     exit;
    // };
    // @InEgLega,@InEgFeIn,@InEgFeEg,@InEgCaus,@FechaHora

    // $params  = array();
    // $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    // $data    = array();
    // FusNuloPOST('InEgFeIn', '');
    // FusNuloPOST('InEgFeEg', '');
    // FusNuloPOST('InEgCaus', '');

    // $InEgLega  = test_input($_POST['InEgLega']);

    // $InEgFeIn  = test_input(($_POST['InEgFeIn']));
    // $InEgFeIn  = !empty(($InEgFeIn)) ? dr_fecha($InEgFeIn) : '17530101';

    // $InEgFeEg  = test_input(($_POST['InEgFeEg']));
    // $InEgFeEg  = !empty(($InEgFeEg)) ? dr_fecha($InEgFeEg) : '17530101';

    // $InEgCaus  = test_input($_POST['InEgCaus']);
    // $FechaHora = date('Ymd H:i:s');

    // if (!valida_campo($_POST['InEgFeEg'])) {
    //     if (($InEgFeIn) > ($InEgFeEg)) {
    //         $data = array('status' => 'Error Fecha', 'dato' => 'Ingreso es mayor que Egreso');
    //         echo json_encode($data);
    //         exit;
    //     };
    //     if (($InEgFeIn) == ($InEgFeEg)) {
    //         $data = array('status' => 'Error Fecha', 'dato' => 'Ingreso es igual Egreso');
    //         echo json_encode($data);
    //         exit;
    //     };
    // }


    // $Dato    = 'Leg: ' . $InEgLega . '. In: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y') . '. Eg: ' . Fech_Format_Var($InEgFeEg, 'd/m/Y');
    // $Dato2    = 'Leg: ' . $InEgLega . '.<br/> In: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y') . '. Eg: ' . Fech_Format_Var($InEgFeEg, 'd/m/Y');

    /** Query revisar si existe un registro de fecha Ingreso igual */
    // $query = "SELECT PERINEG.InEgLega FROM PERINEG WHERE PERINEG.InEgFeIn = '$InEgFeIn' AND PERINEG.InEgLega = '$InEgLega'";

    // $result  = sqlsrv_query($link, $query, $params, $options);
    // if (sqlsrv_num_rows($result) > 0) {
    //     while ($fila = sqlsrv_fetch_array($result)) {
    //         // print_r($query);
    //         $data = array('status' => 'existe', 'dato' => 'Fecha de ingreso: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y'));
    //         echo json_encode($data);
    //     }
    //     sqlsrv_free_stmt($result);
    //     exit;
    // }
    /** fin */
    /** Fecha de ingreso no puede ser igual o inferior a la fecha de egreso mas alta */
    // $query = "SELECT TOP 1 PERINEG.InEgFeEg FROM PERINEG WHERE PERINEG.InEgLega = '$InEgLega' ORDER BY PERINEG.InEgFeEg DESC";

    // $result  = sqlsrv_query($link, $query, $params, $options);
    // if (sqlsrv_num_rows($result) > 0) {
    //     while ($fila = sqlsrv_fetch_array($result)) {
    //         // print_r($query);
    //         $UltimoEg=$fila['InEgFeEg']->format('Ymd');
    //         $UltimoEg2=$fila['InEgFeEg']->format('d/m/Y');
    //         if($UltimoEg>=$InEgFeIn){
    //             $data = array('status' => 'error', 'dato'=> 'La fecha de Ingreso no puede ser igual o inferior al Egreso del '.$UltimoEg2);
    //             echo json_encode($data);
    //         };
    //     }
    //     sqlsrv_free_stmt($result);
    //     exit;

    // }
    /** fin */
    /** Fecha de egreso no puede ser superior a la fecha de ingreso mas alta */
    // $query = "SELECT TOP 1 PERINEG.InEgFeIn, PERINEG.InEgFeEg FROM PERINEG WHERE PERINEG.InEgLega = '$InEgLega' AND PERINEG.InEgFeIn < '$InEgFeIn' ORDER BY PERINEG.InEgFeIn DESC";

    // $result  = sqlsrv_query($link, $query, $params, $options);
    // if (sqlsrv_num_rows($result) > 0) {
    //     while ($fila = sqlsrv_fetch_array($result)) {
    //         $UltimoIn=$fila['InEgFeIn']->format('Ymd');
    //         $UltimoIn2=$fila['InEgFeIn']->format('d/m/Y');
    //         if($UltimoIn<$InEgFeEg){
    //             $data = array('status' => 'error', 'dato'=> 'La fecha de Egreso no puede ser superior al Ingreso del '.$UltimoIn2);
    //             echo json_encode($data);
    //         };
    //     }
    //     sqlsrv_free_stmt($result);
    //     exit;

    // }
    /** fin */

    // $procedure_params = array(
    //     array(&$InEgLega),
    //     array(&$InEgFeIn),
    //     array(&$InEgFeEg),
    //     array(&$InEgCaus),
    //     array(&$FechaHora)
    // );

    // $sql = "exec DATA_PERINEGInsert @InEgLega=?,@InEgFeIn=?,@InEgFeEg=?,@InEgCaus=?,@FechaHora=?";
    // /** Query del Store Prcedure */
    // $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    // /** preparar la sentencia */

    // if (!$stmt) {
    //     die(print_r(sqlsrv_errors(), true));
    // }
    // if (sqlsrv_execute($stmt)) {
    //     /** ejecuto la sentencia */
    //     /** Grabo en la tabla Auditor */
    //     audito_ch('A', $Dato,  '10');
    //     /** */
    //     // sleep(3);
    //     $data = array('status' => 'ok', 'dato' => $Dato2);
    //     echo json_encode($data);
    //     /** retorno resultados en formato json */
    // } else {
    //     $data = array('status' => 'error', 'dato' => $Dato2);
    //     echo json_encode($data);
    //     // die(print_r(sqlsrv_errors(), true));
    // }

    // sqlsrv_close($link);

    // echo json_encode($data);

}
/** EDITA HISTORIAl INGRESOS LEGAJOS  */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['dato'] == 'edita_perineg')) {

    if (valida_campo($_POST['InEgLega']) || valida_campo($_POST['InEgFeIn'])) {
        $data = array('status' => 'requeridos');
        echo json_encode($data);
        exit;
    }
    ;

    FusNuloPOST('InEgFeIn', '');
    FusNuloPOST('InEgFeEg', '');
    FusNuloPOST('InEgCaus', '');

    $InEgLega = test_input($_POST['InEgLega']);

    $InEgFeIn = test_input(($_POST['InEgFeIn']));
    $InEgFeIn = !empty(($InEgFeIn)) ? dr_fecha($InEgFeIn, 'Y-m-d') : '';

    $InEgFeEg = test_input(($_POST['InEgFeEg']));
    $InEgFeEg = !empty(($InEgFeEg)) ? dr_fecha($InEgFeEg, 'Y-m-d') : '';

    $InEgCaus = test_input($_POST['InEgCaus']);

    $payload = array(
        "Lega" => $InEgLega,
        "FeIn" => $InEgFeIn,
        "FeEg" => $InEgFeEg,
        "Caus" => $InEgCaus
    );

    // print_r($payload).exit;

    $sendApi['DATA'] = $sendApi['DATA'] ?? '';
    $sendApi['MESSAGE'] = $sendApi['MESSAGE'] ?? '';

    $sendApi = curlAPI("$pathApiCH/perineg/", $payload, 'PUT', $token);
    $sendApi = json_decode($sendApi, true);

    $Dato = 'Leg: ' . $InEgLega . '. In: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y') . '. Eg: ' . Fech_Format_Var($InEgFeEg, 'd/m/Y');

    if ($sendApi['MESSAGE'] == 'OK') {

        audito_ch('M', $Dato, '10');
        $data = array('status' => 'ok', 'dato' => $sendApi['DATA']);
        echo json_encode($data);

    } else {

        $data = array('status' => $sendApi['MESSAGE'], 'dato' => $sendApi['DATA']);
        echo json_encode($data);

    }

    exit;

}
/** BAJAS HISTORIAl INGRESOS LEGAJOS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['DelPerineg'] == 'true')) {


    if (valida_campo($_POST['DelInEgLega']) || valida_campo($_POST['DelInEgFeIn'])) {
        $data = array('status' => 'requeridos');
        echo json_encode($data);
        exit;
    }
    ;

    $InEgLega = test_input($_POST['DelInEgLega']);
    $InEgFeIn = test_input(Fech_Format_Var($_POST['DelInEgFeIn'], 'Y-m-d'));

    $Dato = 'Leg: ' . $InEgLega . '. In: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y');

    $payload = array(
        "Lega" => $InEgLega,
        "FeIn" => $InEgFeIn
    );

    $sendApi['DATA'] = $sendApi['DATA'] ?? '';
    $sendApi['MESSAGE'] = $sendApi['MESSAGE'] ?? '';

    $sendApi = curlAPI("$pathApiCH/perineg/", $payload, 'DELETE', $token);
    $sendApi = json_decode($sendApi, true);

    $Dato = 'Leg: ' . $InEgLega . '. In: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y') . '. Eg: ' . Fech_Format_Var($InEgFeEg, 'd/m/Y');

    if ($sendApi['MESSAGE'] == 'OK') {

        audito_ch('B', $Dato, '10');
        $data = array('status' => 'ok_delete', 'dato' => $sendApi['DATA']);
        Flight::json($data);

        audito_ch('B', $Dato, '10');
        exit;

    } else {

        $data = array('status' => $sendApi['MESSAGE'], 'dato' => $sendApi['DATA']);
        Flight::json($data);

    }

    exit;

    // require_once __DIR__ . '../../../config/conect_mssql.php';

    // $params  = array();
    // $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    // $data    = array();

    // $InEgLega = test_input($_POST['DelInEgLega']);
    // $InEgFeIn = test_input(FechaString($_POST['DelInEgFeIn']));

    // $FechaHora = date('Ymd H:i:s');

    // $Dato    = 'Leg: ' . $InEgLega . '. In: ' . Fech_Format_Var($InEgFeIn, 'd/m/Y');
    // $procedure_params = array(
    //     array(&$InEgLega),
    //     array(&$InEgFeIn),
    // );
    // // echo json_encode($procedure_params);exit; 

    // $sql = "exec DATA_PERINEGDelete @InEgLega=?,@InEgFeIn=?";
    // /** Query del Store Prcedure */
    // $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    // /** preparar la sentencia */

    // if (!$stmt) {
    //     die(print_r(sqlsrv_errors(), true));
    // }
    // if (sqlsrv_execute($stmt)) {
    //     /** ejecuto la sentencia */
    //     /** Grabo en la tabla Auditor */
    //     audito_ch('B', $Dato,  '10');
    //     /** */
    //     // sleep(3);
    //     $data = array('status' => 'ok_delete', 'dato' => $Dato);
    //     echo json_encode($data);
    //     /** retorno resultados en formato json */
    // } else {
    //     $data = array('status' => 'error_delete', 'dato' => $Dato);
    //     echo json_encode($data);
    //     // die(print_r(sqlsrv_errors(), true));
    // }

    // sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTAS PREMIOS PERSONAL */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['PERPREMI'] == 'PERPREMI')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';
    // $data = array('status' => 'diasvac_ok');
    // echo json_encode($data);exit;
    $_POST['LPreCodi'] = $_POST['LPreCodi'] ?? '';
    if (valida_campo($_POST['LPreCodi'])) {
        $data = array('status' => 'cod_requerido', 'dato' => 'Debe seleccionar un premio.');
        echo json_encode($data);
        exit;
    }
    ;

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $LPreLega = test_input($_POST['LPreLega']);
    /** Legajo */
    $LPreCodi = test_input($_POST['LPreCodi']);
    /** Codigo premio */
    $FechaHora = date('Ymd H:i:s');

    $Dato = 'Premio: (' . $LPreCodi . '). Legajo: ' . $LPreLega;

    /** Query revisar si existe un registro igual */
    $query = "SELECT TOP 1 PERPREMI.LPreLega FROM PERPREMI WHERE PERPREMI.LPreLega = '$LPreLega' AND PERPREMI.LPreCodi = '$LPreCodi'";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'existe', 'dato' => 'Ya existe premio.');
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */
    $procedure_params = array(
        array(&$LPreLega),
        array(&$LPreCodi),
        array(&$FechaHora)
    );

    $sql = "exec DATA_PERPREMIInsert @LPreLega=?,@LPreCodi=?,@FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** BAJAS PREMIOS PERSONAL */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['DelPerPremi'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $LPreLega = test_input($_POST['DelLPreLega']);
    $LPreCodi = test_input(($_POST['DelLPreCodi']));


    $Dato = 'Premio: (' . $LPreCodi . '). Legajo: ' . $LPreLega;

    $procedure_params = array(
        array(&$LPreLega),
        array(&$LPreCodi),
    );
    // echo json_encode($procedure_params);exit; 

    $sql = "exec DATA_PERPREMIDelete @LPreLega=?,@LPreCodi=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('B', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok_delete', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error_delete', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}
/** ALTAS OTROS CONCEPTOS LEGAJO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['OTROCONLEG'] == 'OTROCONLEG')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';
    $_POST['OTROConCodi'] = $_POST['OTROConCodi'] ?? '';
    if (valida_campo($_POST['OTROConCodi'])) {
        $data = array('status' => 'cod_requerido', 'dato' => 'Debe seleccionar un concepto.');
        echo json_encode($data);
        exit;
    }
    ;

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $OTROConLega = test_input($_POST['OTROConLega']);
    /** Legajo */
    $OTROConCodi = test_input($_POST['OTROConCodi']);
    $OTROConValor = empty(test_input($_POST['OTROConValor'])) ? '0' : test_input($_POST['OTROConValor']);
    $FechaHora = date('Ymd H:i:s');

    $Dato = 'Concepto: (' . $OTROConCodi . '). Legajo: ' . $OTROConLega;

    /** Query revisar si existe un registro igual */
    $query = "SELECT TOP 1 OTROCONLEG.OTROConLega FROM OTROCONLEG WHERE OTROCONLEG.OTROConLega = '$OTROConLega' AND OTROCONLEG.OTROConCodi = '$OTROConCodi'";

    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'existe', 'dato' => 'Ya existe concepto.');
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */
    $procedure_params = array(
        array(&$OTROConLega),
        array(&$OTROConCodi),
        array(&$OTROConValor),
        array(&$FechaHora)
    );

    $sql = "exec DATA_OTROCONLEGInsert @OTROConLega=?,@OTROConCodi=?, @OTROConValor=?, @FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** BAJAS OTROS CON LEG */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['DelOtroConLeg'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $OTROConLega = test_input($_POST['OTROConLega']);
    $OTROConCodi = test_input(($_POST['OTROConCodi']));


    $Dato = 'Concepto: (' . $OTROConCodi . '). Legajo: ' . $OTROConLega;

    $procedure_params = array(
        array(&$OTROConLega),
        array(&$OTROConCodi),
    );
    // echo json_encode($procedure_params);exit; 

    $sql = "exec DATA_OTROCONLEGDelete @OTROConLega=?,@OTROConCodi=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('B', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok_delete', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error_delete', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** ALTAS PERSONAL HORARIO ALTERNATIVO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['PERHOAL'] == 'PERHOAL')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';
    $_POST['LegHoAl'] = $_POST['LegHoAl'] ?? '';
    if (valida_campo($_POST['LegHoAl'])) {
        $data = array('status' => 'cod_requerido', 'dato' => 'Debe seleccionar un horario.');
        echo json_encode($data);
        exit;
    }
    ;

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $LegHoAl = test_input($_POST['LegHoAl']);
    /** Legajo */
    $LeHALega = test_input($_POST['LeHALega']);
    $OTROConValor = empty(test_input($_POST['OTROConValor'])) ? '0' : test_input($_POST['OTROConValor']);
    $FechaHora = date('Ymd H:i:s');

    $Dato = 'Horario Alternativo: (' . $LegHoAl . '). Legajo: ' . $LeHALega;

    /** Query revisar si existe un registro igual */
    $query = "SELECT TOP 1 PERHOALT.LeHALega FROM PERHOALT WHERE PERHOALT.LeHALega = '$LeHALega' AND PERHOALT.LeHAHora = '$LegHoAl'";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'existe', 'dato' => 'Ya existe horario.');
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** fin */
    $procedure_params = array(
        array(&$LeHALega),
        array(&$LegHoAl),
        array(&$FechaHora)
    );

    $sql = "exec DATA_PERHOALTInsert @LeHALega=?,@LeHAHora=?, @FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** BAJAS PERSONAL HORARIO ALTERNATIVO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['DelPerHoAl'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $LeHALega = test_input($_POST['LeHALega']);
    $LeHAHora = test_input(($_POST['LeHAHora']));


    $Dato = 'Horario Alternativo: (' . $LeHAHora . '). Legajo: ' . $LeHALega;
    $procedure_params = array(
        array(&$LeHALega),
        array(&$LeHAHora),
    );
    // echo json_encode($procedure_params);exit; 

    $sql = "exec DATA_PERHOALTDelete @LeHALega=?,@LeHAHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('B', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok_delete', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error_delete', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** ALTAS IDENTIFICA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['IDENTIFICA'] == 'IDENTIFICA')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    if (valida_campo($_POST['IDCodigo'])) {
        $data = array('status' => 'cod_requerido', 'dato' => 'Identicador requerido.');
        echo json_encode($data);
        exit;
    }
    ;
    $_POST['IDVence'] = $_POST['IDVence'] ?? '';
    $_POST['IDCap01'] = $_POST['IDCap01'] ?? '';
    $_POST['IDCap02'] = $_POST['IDCap02'] ?? '';
    $_POST['IDCap03'] = $_POST['IDCap03'] ?? '';
    $_POST['IDCap04'] = $_POST['IDCap04'] ?? '';
    $_POST['IDCap05'] = $_POST['IDCap05'] ?? '';
    $_POST['IDCap06'] = $_POST['IDCap06'] ?? '';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();
    $_POST['IDTarjeta'] = $_POST['IDTarjeta'] ?? '';
    $IDCodigo = test_input($_POST['IDCodigo']);
    $IDLegajo = test_input($_POST['IDLegajo']);
    $IDTarjeta = test_input($_POST['IDTarjeta']);
    /** Dispositivos */
    $IDCap01 = ($_POST['IDCap01'] == 'on') ? '1' : '0'; // Macronet
    $IDCap02 = ($_POST['IDCap02'] == 'on') ? '1' : '0'; // nose..
    $IDCap03 = ($_POST['IDCap03'] == 'on') ? '1' : '0'; // Silycon Bayres
    $IDCap04 = ($_POST['IDCap04'] == 'on') ? '1' : '0'; // ZKTECO
    $IDCap05 = ($_POST['IDCap05'] == 'on') ? '1' : '0'; // SUPREMA
    $IDCap06 = ($_POST['IDCap06'] == 'on') ? '1' : '0'; // Hikvsion
    /** */
    $IDVence = !empty(test_input($_POST['IDVence'])) ? dr_fecha(test_input($_POST['IDVence'])) : '17530101';
    $IDVence = FechaString($IDVence);
    $IDFichada = '1';
    $FechaHora = date('Ymd H:i:s');

    $Dato = 'Identificador: (' . $IDCodigo . '). Legajo: ' . $IDLegajo;

    /** Query revisar si existe un registro igual */
    $query = "SELECT TOP 1 IDENTIFICA.IDLegajo, PERSONAL.LegApNo FROM IDENTIFICA INNER JOIN PERSONAL ON IDENTIFICA.IDLegajo=PERSONAL.LegNume WHERE IDENTIFICA.IDCodigo='$IDCodigo'";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'existe', 'dato' => 'ID asignado al legajo:<br /> <strong>' . $fila['IDLegajo'] . ' - ' . $fila['LegApNo'] . '</strong>');
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    /** Revisar que la tarjeta no este asignada a otro legajo */
    $a = simpleQueryDataMS("SELECT TOP 1 I.IDLegajo, P.LegApNo, I.IDTarjeta, I.IDCodigo FROM IDENTIFICA I INNER JOIN PERSONAL P ON I.IDLegajo=P.LegNume WHERE I.IDTarjeta='$IDTarjeta' AND I.IDTarjeta !=''");
    // if ((CountRegistrosMayorCero("SELECT TOP 1 IDTarjeta FROM IDENTIFICA WHERE IDTarjeta='$IDTarjeta' AND IDTarjeta !=''")))
    if ($a) {
        $data = array('status' => 'existe', 'dato' => "La tarjeta se encuentra asignada a ($a[IDLegajo]) $a[LegApNo]", 'otro' => $a);
        echo json_encode($data);
        exit;
    }

    $IDComedor = '';
    $IDInvitado = '0';
    $IDAcceso = '1';
    $IDAlerta = '0';
    $IDProvis = '0';
    $IDAsigna = '1';
    $IDTDoc = '0';
    $IDDocu = '0';
    $IDDisp = '';
    $IDPrivi = '0';
    $IDPass = '';
    $IDFing = '0';
    $IDFing1 = '0';
    $IDFing2 = '0';
    $IDFing3 = '0';
    $IDFing4 = '0';
    $IDFing5 = '0';
    $IDFing6 = '0';
    $IDFing7 = '0';
    $IDFing8 = '0';
    $IDFing9 = '0';
    // $IDCap01 = $IDCap01;
    // $IDCap02 = $IDCap02;
    // $IDCap03 = $IDCap03;
    // $IDCap04 = $IDCap04;
    // $IDCap05 = $IDCap05;
    // $IDCap06 = $IDCap06;
    $IDSupTarj = '';
    $IDSupAdmLev = '0';
    $IDSupAutMod = '0';
    $IDSupName = '';
    $IDSupPass = '';
    $IDSupFinger1 = '0';
    $IDSupDuress1 = '0';
    $IDSupFinger2 = '0';
    $IDSupDuress2 = '0';
    $IDSupStart = '17530101';
    $IDSupExpiry = '17530101';
    $IDSupTarjFC = '0';
    /** fin */
    // $procedure_params = array(
    //     array(&$IDCodigo),array(&$IDFichada),array(&$IDComedor),array(&$IDInvitado),array(&$IDAcceso),array(&$IDAlerta),array(&$IDProvis),array(&$IDVence),array(&$IDAsigna),array(&$IDLegajo),array(&$IDTDoc),array(&$IDDocu),array(&$IDDisp),array(&$IDPrivi),array(&$IDPass),array(&$IDFing),array(&$IDFing1),array(&$IDFing2),array(&$IDFing3),array(&$IDFing4),array(&$IDFing5),array(&$IDFing6),array(&$IDFing7),array(&$IDFing8),array(&$IDFing9),array(&$IDCap01),array(&$IDCap02),array(&$IDCap03),array(&$IDCap04),array(&$IDCap05),array(&$IDTarjeta),array(&$IDSupTarj),array(&$IDSupAdmLev),array(&$IDSupAutMod),array(&$IDSupName),array(&$IDSupPass),array(&$IDSupFinger1),array(&$IDSupDuress1),array(&$IDSupFinger2),array(&$IDSupDuress2),array(&$IDSupStart),array(&$IDSupExpiry),array(&$IDSupTarjFC),array(&$FechaHora)
    // );        

    // $sql = "exec DATA_IDENTIFICAInsert @IDCodigo=?,@IDFichada=?,@IDComedor=?,@IDInvitado=?,@IDAcceso=?,@IDAlerta=?,@IDProvis=?,@IDVence=?,@IDAsigna=?,@IDLegajo=?,@IDTDoc=?,@IDDocu=?,@IDDisp=?,@IDPrivi=?,@IDPass=?,@IDFing=?,@IDFing1=?,@IDFing2=?,@IDFing3=?,@IDFing4=?,@IDFing5=?,@IDFing6=?,@IDFing7=?,@IDFing8=?,@IDFing9=?,@IDCap01=?,@IDCap02=?,@IDCap03=?,@IDCap04=?,@IDCap05=?,@IDTarjeta=?,@IDSupTarj=?,@IDSupAdmLev=?,@IDSupAutMod=?,@IDSupName=?,@IDSupPass=?,@IDSupFinger1=?,@IDSupDuress1=?,@IDSupFinger2=?,@IDSupDuress2=?,@IDSupStart=?,@IDSupExpiry=?,@IDSupTarjFC=?,@FechaHora=?"; /** Query del Store Prcedure */

    $sql = "INSERT INTO IDENTIFICA(
                [IDCodigo],[IDFichada],[IDComedor],[IDInvitado],[IDAcceso],[IDAlerta],[IDProvis],[IDVence],[IDAsigna],[IDLegajo],[IDTDoc],[IDDocu],[IDDisp],[IDPrivi],[IDPass],[IDFing],[IDFing1],[IDFing2],[IDFing3],[IDFing4],[IDFing5],[IDFing6],[IDFing7],[IDFing8],[IDFing9],[IDCap01],[IDCap02],[IDCap03],[IDCap04],[IDCap05],[IDCap06],[IDTarjeta],[IDSupTarj],[IDSupAdmLev],[IDSupAutMod],[IDSupName],[IDSupPass],[IDSupFinger1],[IDSupDuress1],[IDSupFinger2],[IDSupDuress2],[IDSupStart],[IDSupExpiry],[IDSupTarjFC],[FechaHora]
               )
               VALUES (
                '$IDCodigo','$IDFichada','$IDComedor','$IDInvitado','$IDAcceso','$IDAlerta','$IDProvis','$IDVence','$IDAsigna','$IDLegajo','$IDTDoc','$IDDocu','$IDDisp','$IDPrivi','$IDPass','$IDFing','$IDFing1','$IDFing2','$IDFing3','$IDFing4','$IDFing5','$IDFing6','$IDFing7','$IDFing8','$IDFing9','$IDCap01','$IDCap02','$IDCap03','$IDCap04','$IDCap05','$IDCap06','$IDTarjeta','$IDSupTarj','$IDSupAdmLev','$IDSupAutMod','$IDSupName','$IDSupPass','$IDSupFinger1','$IDSupDuress1','$IDSupFinger2','$IDSupDuress2','$IDSupStart','$IDSupExpiry','$IDSupTarjFC','$FechaHora'
               )";
    //    print_r($sql); exit;

    // $stmt = sqlsrv_prepare($link, $sql, $procedure_params); /** preparar la sentencia */
    $stmt = sqlsrv_query($link, $sql, $params, $options);
    /** preparar la sentencia */

    if (!$stmt) {
        // die(print_r(sqlsrv_errors(), true));
        $data = array('status' => 'error', 'dato' => 'Error');
        echo json_encode($data);
        exit;
    }
    if (($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** BAJAS IDENTIFICA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['DelIdentifica'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $IDCodigo = test_input($_POST['IDCodigo']);
    $IDLegajo = test_input($_POST['IDLegajo']);



    $Dato = 'Identificador: (' . $IDCodigo . '). Legajo: ' . $IDLegajo;

    $procedure_params = array(
        array(&$IDCodigo)
    );

    $sql = "exec DATA_IDENTIFICADelete @IDCodigo=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('B', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok_delete', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error_delete', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** UPDATE GRUPO CAPTURADORES */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['GrupoHabi'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $LegGrHa = test_input($_POST['LegGrHa2']);
    $LegajoGrHa = test_input($_POST['LegajoGrHa']);

    $Dato = 'Grupo Capt: (' . $LegGrHa . '). Legajo: ' . $LegajoGrHa;

    $sql = "UPDATE PERSONAL SET PERSONAL.LegGrHa = '$LegGrHa' WHERE PERSONAL.LegNume = '$LegajoGrHa'";
    /** Query */
    // print_r($sql);exit;

    $stmt = sqlsrv_prepare($link, $sql);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('M', $Dato, '10');
        /** */
        // sleep(3);
        $data = array('status' => 'ok', 'dato' => $Dato, 'LegGrHa' => $LegGrHa);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** ALTAS PERRELO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['PERRELO'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    if (valida_campo($_POST['ReloMarca']) or valida_campo($_POST['RelFech']) or valida_campo($_POST['RelFech2'])) {
        $data = array('status' => 'cod_requerido', 'dato' => 'Datos requeridos.');
        echo json_encode($data);
        exit;
    }
    ;


    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $ReloMarca = ($_POST['ReloMarca']);
    $ReloMarca = explode("-", $ReloMarca);

    $RelLega = test_input($_POST['RelLega']);
    $RelRelo = $ReloMarca[0];
    $RelReMa = $ReloMarca[1];
    $RelFech = test_input(FechaString($_POST['RelFech']));
    $RelFech2 = test_input(FechaString($_POST['RelFech2']));
    $FechaHora = date('Ymd H:i:s');

    if ($RelFech > $RelFech2) {
        $data = array('status' => 'error', 'dato' => 'Fecha Inválida.');
        echo json_encode($data);
        exit;
    }
    ;

    $Dato = 'Reloj habilitado: (' . $RelRelo . '), Marca: (' . $RelReMa . '). Legajo: ' . $RelLega;

    /** Query revisar si existe un registro igual */
    $query = "SELECT TOP 1 PERRELO.RelLega 
            FROM PERRELO
            WHERE PERRELO.RelLega = '$RelLega' AND PERRELO.RelReMa = '$RelReMa' AND PERRELO.RelRelo = '$RelRelo'";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'existe', 'dato' => '');
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }

    /** fin */
    $procedure_params = array(
        array(&$RelLega),
        array(&$RelReMa),
        array(&$RelRelo),
        array(&$RelFech),
        array(&$RelFech2),
        array(&$FechaHora),
    );

    $sql = "exec DATA_PERRELOInsert @RelLega=?,@RelReMa=?,@RelRelo=?,@RelFech=?,@RelFech2=?,@FechaHora=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('A', $Dato, '10');
        $data = array('status' => 'ok', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error', 'dato' => $Dato);
        echo json_encode($data);
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}

/** BAJAS PERRELO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['DelPerrelo'] == 'true')) {
    require_once __DIR__ . '../../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    $RelRelo = test_input($_POST['RelRelo']);
    $RelReMa = test_input($_POST['RelReMa']);
    $RelLega = test_input($_POST['RelLega']);



    $Dato = 'Reloj habilitado: (' . $RelRelo . '), Marca: (' . $RelReMa . '). Legajo: ' . $RelLega;

    $procedure_params = array(
        array(&$RelLega),
        array(&$RelReMa),
        array(&$RelRelo),
    );

    $sql = "exec DATA_PERRELODelete @RelLega=?, @RelReMa=?, @RelRelo=?";
    /** Query del Store Prcedure */
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);
    /** preparar la sentencia */

    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */
        audito_ch('B', $Dato);
        /** */
        // sleep(3);
        $data = array('status' => 'ok_delete', 'dato' => $Dato);
        echo json_encode($data);
        /** retorno resultados en formato json */
    } else {
        $data = array('status' => 'error_delete', 'dato' => $Dato);
        echo json_encode($data);
        // die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_close($link);

    // echo json_encode($data);

}