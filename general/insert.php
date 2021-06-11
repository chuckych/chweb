<?php
session_start();
require __DIR__ . '../../config/index.php';
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

E_ALL();

$params    = array();
$options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$data      = array();
$FechaHora = date('Ymd H:i:s');

$_POST['alta_fichada']  = $_POST['alta_fichada'] ?? '';
$_POST['baja_fichada']  = $_POST['baja_fichada'] ?? '';
$_POST['mod_fichada']   = $_POST['mod_fichada'] ?? '';
$_POST['alta_novedad']  = $_POST['alta_novedad'] ?? '';
$_POST['baja_novedad']  = $_POST['baja_novedad'] ?? '';
$_POST['alta_horas']    = $_POST['alta_horas'] ?? '';
$_POST['baja_Hora']     = $_POST['baja_Hora'] ?? '';
$_POST['alta_OtrasNov'] = $_POST['alta_OtrasNov'] ?? '';
$_POST['baja_ONov']     = $_POST['baja_ONov'] ?? '';
$_POST['alta_Citación'] = $_POST['alta_Citación'] ?? '';

/** ALTA FICHADA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_fichada'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['aFic'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para ingresar Fichadas.');
        echo json_encode($data);
        exit;
    };
    if (ValidaFormatoHora($_POST['RegHora'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto');
        echo json_encode($data);
        exit;
    }

    if (valida_campo($_POST['RegHora']) || ($_POST['RegHora'] == '00:00')) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo requerido.');
        echo json_encode($data);
        exit;
    };
    if (valida_campo($_POST['datos_fichada'])) {
        $data = array('status' => 'error', 'Mensaje' => 'datos_fichada requerido.');
        echo json_encode($data);
        exit;
    };

    if ((strlen($_POST['RegHora']) < '5') || (strlen($_POST['RegHora']) > '5')) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto.');
        echo json_encode($data);
        exit;
    };

    if (ValidarHora($_POST['RegHora'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto: ' . $_POST['RegHora']);
        echo json_encode($data);
        exit;
    }

    $Hora = explode(':', $_POST['RegHora']);
    $datos_fichada = explode('-', $_POST['datos_fichada']);

    if (($Hora[0] > '23') || ($Hora[1] > '59')) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto.');
        echo json_encode($data);
        exit;
    };


    $RegLega = $datos_fichada[0];
    //$RegFech = $datos_fichada[1];
    $RegFeAs = $datos_fichada[1];

    if (PerCierre($RegFeAs, $RegLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual: ' . Fech_Format_Var($RegFeAs, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    require __DIR__ . '../../config/conect_mssql.php';

    $sql = "SELECT TOP 1 IDCodigo FROM IDENTIFICA WHERE IDAsigna = '1' AND IDLegajo = '$RegLega' AND IDFichada = '1' ORDER BY IDCodigo";
    $result  = sqlsrv_query($link, $sql, $params, $options);

    if (sqlsrv_num_rows($result) == 0) {
        $data = array('status' => 'error', 'Mensaje' => 'No hay identificador asociado al Legajo.');
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    } else {
        while ($fila = sqlsrv_fetch_array($result)) {
            $RegTarj = $fila['IDCodigo'];
        }
    }
    sqlsrv_free_stmt($result);
    sqlsrv_close($link);


    //$RegTarj= $_POST['RegTarj']; /** 29988600 */
    $RegFech = $_POST['RegFech'];
    /** 20200630 */
    $RegFech = Fech_Format_Var($RegFech, 'Ymd');
    $RegHora = $_POST['RegHora'];
    /** 10:00 */
    $RegTipo = '1';
    /** 1 */
    //$RegLega= $_POST['RegLega']; /** 29988600 */
    //$RegFeAs= $_POST['RegFeAs']; /** 20200630 */
    $RegFeRe = $RegFech;
    /** 20200630 */
    $RegHoRe = $RegHora;
    /** 10:00 */
    $RegTran = '1';
    /** 1 */
    $RegSect = '0';
    /** 0 */
    $RegRelo = '';
    /**  */
    $RegLect = '';
    /**  */

    $ExisteRegistro = CountRegistrosMayorCero("SELECT TOP 1 REGISTRO.RegTarj FROM REGISTRO WHERE RegTarj = '$RegTarj' and RegFech = '$RegFech' and RegHora = '$RegHora' ORDER BY RegTarj,RegFech,RegHora");

    if ($ExisteRegistro) {
        /** Si existe un registro en la tabla registro donde la regtarj, regfech y reghora existen procedemos al update de esa fila */
        $Dato    = 'Alta Fichada: (' . $RegHora . '). Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd-m-Y');
        $Dato2    = 'Hora: <span class="ls1 fw5">' . $RegHora . '</span>Hs. Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd/m/Y');

        if (UpdateRegistro("UPDATE REGISTRO Set RegTipo = '$RegTipo',RegLega = '$RegLega',RegFeAs = '$RegFeAs',RegFeRe = '$RegFeRe',RegHoRe = '$RegHoRe',RegTran = '$RegTran',RegSect = '$RegSect',RegRelo = '$RegRelo',RegLect = '$RegLect',FechaHora = '$FechaHora' WHERE RegTarj = '$RegTarj' and RegFech = '$RegFech' and RegHora = '$RegHora'")) {

            audito_ch('A', $Dato);
            if (procesar_legajo($RegLega, $RegFeAs, $RegFeAs) == 'Terminado') {
                $Procesado = " - Procesado.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
            } else {
                $Procesado = " - Sin procesar.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
            }
            echo json_encode($data);
            exit;
        } else {
            $data[] = array("status" => "error", "Mensaje" => 'Error');
            echo json_encode($data);
            exit;
        }
    } else {
        /** Si no existe un registro en la tabla registro donde la regtarj, regfech y reghora existen Insertartamos fichada nueva */
        $Dato    = 'Alta Fichada: (' . $_POST['RegHora'] . '). Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd-m-Y');
        $Dato2    = 'Hora: <span class="ls1 fw5">' . $_POST['RegHora'] . '</span>Hs. Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd/m/Y');

        if (InsertRegistro("INSERT INTO REGISTRO (RegTarj,RegFech,RegHora,RegTipo,RegLega,RegFeAs,RegFeRe,RegHoRe,RegTran,RegSect,RegRelo,RegLect,FechaHora) Values('$RegTarj','$RegFech','$RegHora','$RegTipo','$RegLega','$RegFeAs','$RegFeRe','$RegHoRe','$RegTran','$RegSect','$RegRelo','$RegLect','$FechaHora')")) {
            audito_ch('A', $Dato);
            if (procesar_legajo($RegLega, $RegFeAs, $RegFeAs) == 'Terminado') {
                $Procesado = " - Procesado.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
            } else {
                $Procesado = " - Sin procesar.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
            }
            echo json_encode($data);
            exit;
        } else {
            $data[] = array("status" => "error", "Mensaje" => 'Error');
            echo json_encode($data);
            exit;
        }
    }
}
/** BAJA FICHADA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['baja_fichada'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['bFic'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para eliminar Fichadas.');
        echo json_encode($data);
        exit;
    };

    $datos = explode('-', $_POST['Datos']);
    $RegFech = $datos[0];
    /** Fecha */
    $RegTarj = $datos[1];
    /** ID */
    $RegHora = $datos[2];
    /** Hora */
    $RegLega = $datos[3];
    $RegLega2 = $datos[3];
    /** Legajo */

    if (ValidarHora($RegHora)) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto: ' . $RegHora);
        echo json_encode($data);
        exit;
    }

    if (PerCierre($RegFech, $RegLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($RegFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }
    $RegTipo = '1';
    $RegFeAs = '17530101';
    $RegFeRe = $RegFech;
    $RegHoRe = $RegHora;
    $RegTran = '1';
    $RegSect = '0';
    $RegRelo = '';
    $RegLect = '';

    if ((valida_campo($_POST['Datos']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos requerido.');
        echo json_encode($data);
        exit;
    };

    $Dato    = 'Baja Fichada: (' . $RegHora . '). Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd-m-Y');
    $Dato2    = 'Hora: <span class="ls1 fw5">' . $RegHora . '</span>Hs. Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd/m/Y');

    $RegLega = '0';

    if (UpdateRegistro("UPDATE REGISTRO SET RegTipo=1, RegLega=$RegLega, RegFeAs='$RegFeAs', RegFeRe='$RegFech', RegHoRe='$RegHora', RegTran='$RegTran', RegSect='$RegSect', RegRelo='$RegRelo', RegLect='$RegLect', FechaHora='$FechaHora' WHERE RegTarj='$RegTarj' AND RegFech='$RegFech' AND RegHora='$RegHora'")) {

        audito_ch('B', $Dato);
        if (procesar_legajo($RegLega2, $RegFech, $RegFech) == 'Terminado') {
            $Procesado = " - Procesado.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        } else {
            $Procesado = " - Sin procesar.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        }
        echo json_encode($data);
        exit;
    } else {
        $data[] = array("status" => "error", 'Mensaje' => 'Error');
        echo json_encode($data);
        exit;
    }
}
/** MODIFICACION FICHADA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['mod_fichada'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['mFic'] == '0') {
        PrintRespuestaJson('error', 'No tiene permiso para modificar Fichadas');
        exit;
    };

    $datos = explode('-', $_POST['datos_fichada_mod']);

    $RegFech1 = $datos[0];
    /** Fecha */
    $RegTarj = $datos[1];
    /** ID */
    $RegHora1 = $datos[2];
    /** Hora */
    $RegLega = $datos[3];
    /** Legajo */
    $RegTipo = $datos[4];
    /** Legajo */

    if (ValidaFormatoHora($_POST['RegHora_mod'])) {
        PrintRespuestaJson('error', 'Formato de Hora incorrecto');
        exit;
    }

    if (valida_campo($_POST['RegHora_mod']) || ($_POST['RegHora_mod'] == '00:00')) {
        PrintRespuestaJson('error', 'Campo requerido.');
        exit;
    };

    if ((strlen($_POST['RegHora_mod']) < '5') || (strlen($_POST['RegHora_mod']) > '5')) {
        PrintRespuestaJson('error', 'Formato de Hora incorrecto');
        exit;
    };

    if (ValidarHora($_POST['RegHora_mod'])) {
        PrintRespuestaJson('error', 'Formato de Hora incorrecto: ' . $_POST['RegHora_mod']);
        exit;
    }


    $Hora = explode(':', $_POST['RegHora_mod']);

    if (($Hora[0] > '23') || ($Hora[1] > '59')) {
        PrintRespuestaJson('error', 'Formato de Hora incorrecto');
        exit;
    };

    if (PerCierre($RegFech1, $RegLega)) {
        PrintRespuestaJson('error', 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($RegFech1, ('d/m/Y')));
        exit;
    }

    $RegHora = $_POST['RegHora_mod'];
    $RegFech = Fech_Format_Var($_POST['RegFech_mod'], 'Ymd');

    $RegFeAs = Fech_Format_Var($RegFech1, 'Ymd');
    $RegFeRe = $RegFech;
    $RegHoRe = $RegHora;
    $RegTran = '1';
    $RegSect = '0';
    $RegRelo = '';
    $RegLect = '';

    if ((valida_campo($_POST['datos_fichada_mod']))) {
        PrintRespuestaJson('error', 'Campos requerido');
        exit;
    };

    $ExisteRegistro = CountRegistrosMayorCero("SELECT TOP 1 REGISTRO.RegLega FROM REGISTRO WHERE RegLega='$RegLega' AND RegFech = '$RegFech1' and RegHora = '$RegHora1' ORDER BY RegLega,RegFech,RegHora");
    if ($ExisteRegistro) {

        $Dato    = 'Modificación Fichada: (' . $RegHora . '). Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd-m-Y');
        $Dato2    = 'Hora: <span class="ls1 fw5">' . $RegHora . '</span>Hs. Legajo: ' . $RegLega . '. Fecha: ' . Fech_Format_Var($RegFech, 'd/m/Y');

        if (UpdateRegistro("UPDATE REGISTRO SET RegTipo = $RegTipo, RegLega = '$RegLega',RegFeAs = '$RegFeAs', RegFeRe = '$RegFeRe', RegHoRe = '$RegHora',RegTran = $RegTran,RegSect = '$RegSect',RegRelo = '',RegLect = '',FechaHora = '$FechaHora' WHERE RegTarj='$RegTarj' AND RegFech='$RegFech1' AND RegHora = '$RegHora1'")) {

            audito_ch('M', $Dato);
            if (procesar_legajo($RegLega, $RegFech1, $RegFech1) == 'Terminado') {
                $Procesado = " - Procesado.";
                PrintRespuestaJson('ok', $Dato2 . $Procesado);
                exit;
            } else {
                $Procesado = " - Sin procesar.";
                PrintRespuestaJson('ok', $Dato2 . $Procesado);
                exit;
            }
            echo json_encode($data);
            exit;
        } else {
            PrintRespuestaJson('error', 'Error');
            exit;
        }
    } else {
        PrintRespuestaJson('error', 'No existe Fichada');
        exit;
    }
}
/** ALTA NOVEDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_novedad'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['aNov'] == '0') {
        PrintRespuestaJson('error', 'No tiene permiso para agregar Novedades');
        exit;
    };

    $_POST['FicNove']  = $_POST['FicNove'] ?? '';
    $_POST['FicHoras'] = $_POST['FicHoras'] ?? '';
    $_POST['FicJust']  = $_POST['FicJust'] ?? '';
    $_POST['FicObse']  = $_POST['FicObse'] ?? '';
    $_POST['FicCaus']  = $_POST['FicCaus'] ?? '';
    $_POST['FicCate']  = $_POST['FicCate'] ?? '0';

    if (ValidaFormatoHora($_POST['FicHoras'])) {
        PrintRespuestaJson('error', 'Formato de Hora incorrecto');
        exit;
    }

    if (ValidarHora($_POST['FicHoras'])) {
        PrintRespuestaJson('error', 'Formato de Hora incorrecto: ' . $_POST['FicHoras']);
        exit;
    }

    $Hora          = explode(':', $_POST['FicHoras']);
    $datos_novedad = explode('-', $_POST['datos_novedad']);

    if (($Hora[0] > '23') || ($Hora[1] > '59')) {
        PrintRespuestaJson('error', 'Formato de Hora incorrecto');
        exit;
    };

    if ((valida_campo($_POST['FicHoras'])) || (valida_campo($_POST['FicNove']))) {
        PrintRespuestaJson('error', 'Campo <strong>Horas y Novedad</strong> son requeridos!');
        exit;
    };
    if (valida_campo($_POST['datos_novedad'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>datos_novedad</strong> requerido!');
        echo json_encode($data);
        exit;
    };


    if (($Hora[0] > '23') || ($Hora[1] > '59')) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto.');
        echo json_encode($data);
        exit;
    };
    $FicLega  = test_input($datos_novedad[0]);
    $FicFech  = test_input($datos_novedad[1]);

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    $FicNove  = test_input($_POST['FicNove']);
    $FicHoras = test_input($_POST['FicHoras']);
    $FicJust  = test_input($_POST['FicJust']);
    $FicObse  = test_input($_POST['FicObse']);
    $FicCaus  = test_input($_POST['FicCaus']);
    $FicCate  = test_input($_POST['FicCate']);
    $FicCate  = $FicCate == 'on' ? '2' : '0';
    $FicJust  = $FicJust == 'on' ? '1' : '0';

    require __DIR__ . '../../config/conect_mssql.php';

    $DiaLaboral = CountRegistrosMayorCero("SELECT FicDiaL FROM FICHAS WHERE FICHAS.FicLega= '$FicLega' AND FICHAS.FicFech= '$FicFech' AND FICHAS.FicDiaL='1'");

    if (!$DiaLaboral && $FicCate == 0) {
        // echo $DiaLaboral;
        $data = array('status' => 'Error', 'Mensaje' => 'Error. Solo se permiten Novedades Secundarias en día no Laboral');
        echo json_encode($data);
        exit;
    }



    $query = "SELECT TOP 1 NovTipo, NovDesc, NovCodi FROM NOVEDAD WHERE NovCodi = '$FicNove' ORDER BY NovCodi";
    $result  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($result)) {
        $NovTipo = $row['NovTipo'];
        $NovDesc = $row['NovDesc'];
        $NovCodi = $row['NovCodi'];
    }
    sqlsrv_free_stmt($result);


    switch ($NovTipo) {
        case '0':
        case '1':
        case '2':
            $SelectNovTipo = "AND FICHAS3.FicNoTi = '$NovTipo'";
            break;
        case '3':
        case '4':
        case '5':
        case '6':
        case '7':
        case '8':
            $SelectNovTipo = "AND FICHAS3.FicNoTi IN ('3','4','5','6','7','8')";
            break;
    }

    $Dato = 'Alta Novedad: (' . $NovCodi . ') ' . $NovDesc . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
    $Dato2 = 'Nov: (' . $NovCodi . ') ' . $NovDesc;

    $sql = "SELECT FicLega, FicNove, FicFech FROM FICHAS3 WHERE FICHAS3.FicLega = '$FicLega' AND FICHAS3.FicFech = '$FicFech' $SelectNovTipo AND FICHAS3.FicCate = '0' ORDER BY FICHAS3.FicLega,FICHAS3.FicFech";
    // print_r($sql);
    // echo json_encode($sql);
    //     exit;

    $result  = sqlsrv_query($link, $sql, $params, $options);
    if ((sqlsrv_num_rows($result) > 0) && $FicCate == 0) {

        while ($row = sqlsrv_fetch_array($result)) {
            $Fi3FicFech  = $row['FicFech']->format('Ymd');
            $Fic3FicNove = $row['FicNove'];
            $Fic3FicLega = $row['FicLega'];
        }
        sqlsrv_free_stmt($result);
        // print_r($sql); exit;

        $result  = sqlsrv_query($link, $sql, $params, $options);

        switch ($NovTipo) {
            case '0':
            case '1':
            case '2':
                $deletNov = "AND FicNove = '$Fic3FicNove' AND FicCate = '0'";
                break;
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
                $deletNov = "AND FicNoTi IN ('3','4','5','6','7','8') AND FicCate = '0'";
                break;
        }
        /** Chequeamos si existe una novedad del mismo tipo en Fichas3 */ /** Si existe la borramos */
        DeleteRegistro("DELETE FROM FICHAS3 WHERE FicLega = '$Fic3FicLega' and FicFech = '$Fi3FicFech' $deletNov");
        // exit;
    }
    /** Leugo insertamos la novedad en Fichas3 */
    if (InsertRegistro("INSERT INTO FICHAS3 (FicLega,FicFech,FicTurn,FicNove,FicNoTi,FicHoras,FicJust,FicObse,FicCaus,FicEsta,FicCate,FicComp,FechaHora) Values('$FicLega','$FicFech',1,'$FicNove','$NovTipo','$FicHoras','$FicJust','$FicObse','$FicCaus',1,'$FicCate','00:00','$FechaHora')")) {
        audito_ch('A', $Dato);
        /** Grabamos en Auditor */
        if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
            $Procesado = " - Procesado.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        } else {
            $Procesado = " - Sin procesar.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        }
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    } else {
        $data = array('status' => 'Error', 'Mensaje' => $Dato);
        echo json_encode($data);
        exit;
    }
    sqlsrv_free_stmt($result);
    sqlsrv_close($link);
}
/** MODIFICACIÓN NOVEDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_novedad'] == 'Mod')) {

    if ($_SESSION["ABM_ROL"]['mNov'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para modificar Novedades.');
        echo json_encode($data);
        exit;
    };

    $_POST['FicNove']  = $_POST['FicNove'] ?? '';
    $_POST['FicHoras'] = $_POST['FicHoras'] ?? '';
    $_POST['FicJust']  = $_POST['FicJust'] ?? '';
    $_POST['FicObse']  = $_POST['FicObse'] ?? '';
    $_POST['FicCaus']  = $_POST['FicCaus'] ?? '';
    $_POST['FicCate']  = $_POST['FicCate'] ?? '0';

    if ((valida_campo($_POST['FicHoras'])) || (valida_campo($_POST['FicNove']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>Horas y Novedad</strong> son requeridos!');
        echo json_encode($data);
        exit;
    };

    if (ValidaFormatoHora($_POST['FicHoras'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto');
        echo json_encode($data);
        exit;
    }

    if (ValidarHora($_POST['FicHoras'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto: ' . $_POST['FicHoras']);
        echo json_encode($data);
        exit;
    }

    if (valida_campo($_POST['datos_novedad'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>datos_novedad</strong> requerido!');
        echo json_encode($data);
        exit;
    };
    $Hora          = explode(':', $_POST['FicHoras']);
    $datos_novedad = explode('-', $_POST['datos_novedad']);

    if (($Hora[0] > '23') || ($Hora[1] > '59')) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto.');
        echo json_encode($data);
        exit;
    };

    $FicLega  = test_input($datos_novedad[0]);
    $FicFech  = test_input($datos_novedad[1]);

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    $FicNove  = test_input($_POST['FicNove']);
    $FicHoras = test_input($_POST['FicHoras']);
    $FicJust  = test_input($_POST['FicJust']);
    $FicObse  = test_input($_POST['FicObse']);
    $FicCaus  = test_input($_POST['FicCaus']);
    $FicCate  = test_input($_POST['FicCate']);
    $FicCate  = $FicCate == 'on' ? '2' : '0';
    $FicJust  = $FicJust == 'on' ? '1' : '0';

    require __DIR__ . '../../config/conect_mssql.php';

    $DiaLaboral = CountRegistrosMayorCero("SELECT FicDiaL FROM FICHAS WHERE FICHAS.FicLega= '$FicLega' AND FICHAS.FicFech= '$FicFech' AND FICHAS.FicDiaL='1'");

    if (!$DiaLaboral && $FicCate == 0) {
        echo $DiaLaboral;
        $data = array('status' => 'Error', 'Mensaje' => 'Error. Solo se permiten Novedades Secundarias en día no Laboral');
        echo json_encode($data);
        exit;
    }

    $query = "SELECT TOP 1 NovTipo, NovDesc, NovCodi FROM NOVEDAD WHERE NovCodi = '$FicNove' ORDER BY NovCodi";
    $result  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($result)) {
        $NovTipo = $row['NovTipo'];
        $NovDesc = $row['NovDesc'];
        $NovCodi = $row['NovCodi'];
    }
    sqlsrv_free_stmt($result);

    $MNove     = explode('-', $_POST['CNove']);
    /** Cod Nov, Tipo y Categoria */
    $MNoveCod  = test_input($MNove[0]);
    $MNoveTipo = test_input($MNove[1]);
    $MNoveCat  = test_input($MNove[2]);

    $Dato = 'Novedad: (' . $NovCodi . ') ' . $NovDesc . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
    $Dato2 = 'Nov: (' . $NovCodi . ') ' . $NovDesc;

    if (UpdateRegistro("UPDATE FICHAS3 SET FicNove = '$FicNove', FicNoTi='$NovTipo', FicHoras='$FicHoras', FicJust='$FicJust', FicObse='$FicObse', FicCaus='$FicCaus', FicCate='$FicCate', FechaHora = '$FechaHora' WHERE FicNove = '$MNoveCod' AND FicLega = '$FicLega' AND FicFech = '$FicFech' AND FicTurn='1'")) {
        audito_ch('M', $Dato);
        /** Grabamos en Auditor */
        if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
            $Procesado = " - Procesado.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado, 'tipo' => 'mod');
        } else {
            $Procesado = " - Sin procesar.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        }
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    } else {
        $data = array('status' => 'Error', 'Mensaje' => $Dato);
        echo json_encode($data);
        exit;
    }
    sqlsrv_free_stmt($result);
    sqlsrv_close($link);
}
/** BAJA NOVEDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['baja_novedad'] == 'true')) {
    if ($_SESSION["ABM_ROL"]['bNov'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para eliminar Novedades.');
        echo json_encode($data);
        exit;
    };

    $datos = explode('-', $_POST['Datos']);
    /** FicNov, FicFech, FicLega */
    $NovDes = test_input($_POST['NovDes']);
    $FicNov  = $datos[0];
    $FicFech = $datos[1];
    $FicLega = $datos[2];

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    if ((valida_campo($_POST['Datos']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos requerido.');
        echo json_encode($data);
        exit;
    };


    $Dato    = 'Baja Novedad: (' . $FicNov . ') ' . $NovDes . '. Legajo: ' . $FicLega . '. Fecha: ' . Fech_Format_Var($FicFech, 'd-m-Y');
    $Dato2    = 'Nov: (' . $FicNov . ') ' . $NovDes;

    if ((DeleteRegistro("DELETE FROM FICHAS3 WHERE FicLega = '$FicLega' AND FicFech = '$FicFech' AND FicTurn = 1 AND FicNove = '$FicNov'"))) {
        audito_ch('B', $Dato);
        /** Grabo en la tabla Auditor */
        if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
            $Procesado = " - Procesado.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        } else {
            $Procesado = " - Sin procesar.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        }
        echo json_encode($data);
        exit;
    } else {
        exit;
    }
}
/** ALTA HORAS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_horas'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['aHor'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para agregar Horas.');
        echo json_encode($data);
        exit;
    };

    $_POST['Fic1Hora']   = $_POST['Fic1Hora'] ?? '';
    $_POST['Fic1HsAu2']  = $_POST['Fic1HsAu2'] ?? '';
    $_POST['Fic1Caus']   = $_POST['Fic1Caus'] ?? '0';
    $_POST['Fic1Observ'] = $_POST['Fic1Observ'] ?? '';
    $_POST['datos_hora'] = $_POST['datos_hora'] ?? '';

    if (ValidarHora($_POST['Fic1HsAu2'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto: ' . $_POST['Fic1HsAu2']);
        echo json_encode($data);
        exit;
    }

    if ((valida_campo($_POST['Fic1Hora'])) || (valida_campo($_POST['Fic1HsAu2']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>Tipo Hora y Autorizadas</strong> son requeridos!');
        echo json_encode($data);
        exit;
    };

    if (valida_campo($_POST['datos_hora'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>datos_novedad</strong> requerido!');
        echo json_encode($data);
        exit;
    };
    $Fic1HsAu2  = explode(':', $_POST['Fic1HsAu2']);
    $datos_hora = explode('-', $_POST['datos_hora']);

    if (ValidaFormatoHora($_POST['Fic1HsAu2'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto');
        echo json_encode($data);
        exit;
    }

    if (($Fic1HsAu2[0] > '23') || ($Fic1HsAu2[1] > '59')) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto.');
        echo json_encode($data);
        exit;
    };

    $FicLega  = test_input($datos_hora[0]);
    $FicFech  = test_input($datos_hora[1]);

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    $Fic1Hora   = test_input($_POST['Fic1Hora']);
    $Fic1HsAu2  = test_input($_POST['Fic1HsAu2']);
    $Fic1Caus   = test_input($_POST['Fic1Caus']);
    $Fic1Observ = test_input($_POST['Fic1Observ']);


    require __DIR__ . '../../config/conect_mssql.php';

    $Ausente = CountRegistrosMayorCero("SELECT TOP 1 REGISTRO.RegLega FROM REGISTRO WHERE REGISTRO.RegFeAs = '$FicFech' AND REGISTRO.RegLega = '$FicLega'");

    if (!$Ausente) {
        $data = array('status' => 'Error', 'Mensaje' => 'Error. No se permite la carga de Horas en ausente');
        echo json_encode($data);
        exit;
    }

    $query = "SELECT TOP 1 THoCodi ,THoDesc FROM TipoHora WHERE THoCodi > 0 AND THoCodi = '$Fic1Hora'";
    $result  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($result)) {
        $THoDesc = $row['THoDesc'];
    }
    sqlsrv_free_stmt($result);

    $Dato = 'Alta Hora: (' . $Fic1Hora . ') ' . $THoDesc . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
    $Dato2 = 'Hora: (' . $Fic1Hora . ') ' . $THoDesc;

    if (InsertRegistro("INSERT INTO FICHAS1 (FicLega,FicFech,FicTurn,FicHora,FicHsHe,FicHsAu,FicHsAu2,FicEsta,FechaHora,FicObse,FicCaus) Values('$FicLega','$FicFech',1,'$Fic1Hora','$Fic1HsAu2','$Fic1HsAu2','$Fic1HsAu2',2,'$FechaHora','$Fic1Observ','$Fic1Caus ')")) {
        audito_ch('A', $Dato);
        /** Grabamos en Auditor */
        if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
            $Procesado = " - Procesado.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        } else {
            $Procesado = " - Sin procesar.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        }
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    } else {
        $data = array('status' => 'Error', 'Mensaje' => $Dato);
        echo json_encode($data);
        exit;
    }
    sqlsrv_close($link);
}
/** BAJA HORAS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['baja_Hora'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['bHor'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para eliminar Horas.');
        echo json_encode($data);
        exit;
    };

    $datos    = explode('-', $_POST['Datos']);
    /** FicHora, FicFech, FicLega */
    $HoraDesc = test_input($_POST['HoraDesc']);
    $FicHora  = $datos[0];
    $FicFech  = $datos[1];
    $FicLega  = $datos[2];

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    if ((valida_campo($_POST['Datos']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos requerido.');
        echo json_encode($data);
        exit;
    };

    $Dato  = 'Baja Hora: (' . $FicHora . ') ' . $HoraDesc . '. Legajo: ' . $FicLega . '. Fecha: ' . Fech_Format_Var($FicFech, 'd-m-Y');
    $Dato2 = 'Hora: (' . $FicHora . ') ' . $HoraDesc;

    if ((DeleteRegistro("DELETE FROM FICHAS1 WHERE FicLega = '$FicLega' AND FicFech = '$FicFech' AND FicTurn = 1 AND FicHora = '$FicHora'"))) {
        audito_ch('B', $Dato);
        /** Grabo en la tabla Auditor */
        if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
            $Procesado = " - Procesado.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        } else {
            $Procesado = " - Sin procesar.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        }
        echo json_encode($data);
        exit;
    } else {
        exit;
    }
}
/** MODIFICACIÓN HORAS */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_horas'] == 'mod')) {

    if ($_SESSION["ABM_ROL"]['mHor'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para modificar Horas.');
        echo json_encode($data);
        exit;
    };

    $_POST['Fic1Hora']   = $_POST['Fic1Hora'] ?? '';
    $_POST['Fic1HsAu2']  = $_POST['Fic1HsAu2'] ?? '';
    $_POST['Fic1Caus']   = $_POST['Fic1Caus'] ?? '0';
    $_POST['Fic1Observ'] = $_POST['Fic1Observ'] ?? '';
    $_POST['datos_hora'] = $_POST['datos_hora'] ?? '';

    if (ValidaFormatoHora($_POST['Fic1HsAu2'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto');
        echo json_encode($data);
        exit;
    }

    if (ValidarHora($_POST['Fic1HsAu2'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto: ' . $_POST['Fic1HsAu2']);
        echo json_encode($data);
        exit;
    }

    if ((valida_campo($_POST['Fic1Hora'])) || (valida_campo($_POST['Fic1HsAu2']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>Tipo Hora y Autorizadas</strong> son requeridos!');
        echo json_encode($data);
        exit;
    };
    if (valida_campo($_POST['datos_hora'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>datos_novedad</strong> requerido!');
        echo json_encode($data);
        exit;
    };
    $Fic1HsAu2  = explode(':', $_POST['Fic1HsAu2']);
    $datos_hora = explode('-', $_POST['datos_hora']);

    if (($Fic1HsAu2[0] > '23') || ($Fic1HsAu2[1] > '59')) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto.');
        echo json_encode($data);
        exit;
    };

    $FicLega  = test_input($datos_hora[0]);
    $FicFech  = test_input($datos_hora[1]);


    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    $Fic1Hora   = test_input($_POST['Fic1Hora']);
    $Fic1HsAu2  = test_input($_POST['Fic1HsAu2']);
    $Fic1Caus   = test_input($_POST['Fic1Caus']);
    $Fic1Observ = test_input($_POST['Fic1Observ']);
    $NombreLega = test_input($_POST['NombreLega']);
    $FicHsAu    = test_input($_POST['FicHsAu']);

    require __DIR__ . '../../config/conect_mssql.php';

    $Ausente = CountRegistrosMayorCero("SELECT TOP 1 RegLega FROM REGISTRO WHERE RegFeAs = '$FicFech' AND RegLega = '$FicLega' ORDER BY RegTarj, RegFech, RegHora");

    if (!$Ausente) {
        $data = array('status' => 'Error', 'Mensaje' => 'Error. No se permite la carga de Horas en ausente');
        echo json_encode($data);
        exit;
    }

    $ExisteRegistro = CountRegistrosMayorCero("SELECT TOP 1 FicLega FROM FICHAS1 WHERE FicLega = '$FicLega' and FicFech = '$FicFech' and FicTurn = '1' and FicHora = '$Fic1Hora' Order by FicLega,FicFech,FicTurn,FicHora");

    if (!$ExisteRegistro) {
        $data = array('status' => 'Error', 'Mensaje' => 'Error. No existe el registro');
        echo json_encode($data);
        exit;
    }

    $query = "SELECT TOP 1 THoCodi ,THoDesc FROM TipoHora WHERE THoCodi > 0 AND THoCodi = '$Fic1Hora'";
    $result  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($result)) {
        $THoDesc = $row['THoDesc'];
    }
    sqlsrv_free_stmt($result);

    $Dato = 'Modificación Hora: (' . $Fic1Hora . ') ' . $THoDesc . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
    $Dato2 = 'Hora: (' . $Fic1Hora . ') ' . $THoDesc;

    if (UpdateRegistro("UPDATE FICHAS1 Set FicHsHe = '$Fic1HsAu2',FicHsAu = '$Fic1HsAu2',FicHsAu2 = '$Fic1HsAu2', FicEsta = '2', FechaHora = '$FechaHora',FicObse = '$Fic1Observ', FicCaus = '$Fic1Caus' WHERE FicLega = '$FicLega' and FicFech = '$FicFech' and FicTurn = 1 and FicHora = '$Fic1Hora'")) {
        /** Grabamos en Auditor */
        audito_ch('M', $Dato);

        //setup request to send json via POST
        $extraHours[] = array(
            'name'       => $NombreLega,
            'hour'       => $FicHsAu,
            'auth_hours' => $Fic1HsAu2,
            'employe_id' => $FicLega,
            'type'       => $THoDesc,
            'type_id'    => $Fic1Hora,
            'date'       => FechaFormatVar($FechaHora, 'd M Y'),
            'date_str'   => $FechaHora,
        );

        $data = array(
            'apiKey' => "7BB3A26C25687BCD56A9BAF353A78",
            'eventType' => 201,
            'extraHours' => $extraHours,
        );
        $data = array(
            'data' => array('data' => $data),
            'to' => regid_legajo('29988600')
        );

        $payload = json_encode($data);

        function sendMessaje($url, $payload, $timeout = 10)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $headers = [
                'Content-Type: application/json',
                'Authorization:key=AAAALZBjrKc:APA91bH2dmW3epeVB9UFRVNPCXoKc27HMvh6Y6m7e4oWEToMSBDEc4U7OUJhm2yCkcRKGDYPqrP3J2fktNkkTJj3mUGQBIT2mOLGEbwXfGSPAHg_haryv3grT91GkKUxqehYZx_0_kX8'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $file_contents = curl_exec($ch);
            curl_close($ch);
            return ($file_contents) ? $file_contents : false;
            exit;
        }

        if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
            $Procesado = " - Procesado.";

            $url = 'https://fcm.googleapis.com/fcm/send';

            if ($_SESSION["ID_CLIENTE"]=='1') {
                $sendMensaje = sendMessaje($url, $payload, 10);
            }else{
                $sendMensaje = '';
            }

            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado, 'tipo' => 'mod', 'ApiMobile' => json_decode($sendMensaje));
        } else {
            $Procesado = " - Sin procesar.";
            $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado);
        }
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    } else {
        $data = array('status' => 'Error', 'Mensaje' => $Dato);
        echo json_encode($data);
        exit;
    }
    sqlsrv_close($link);
}
/** ALTA OTRAS NOVEDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_OtrasNov'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['aONov'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para agregar Otras Novedades.');
        echo json_encode($data);
        exit;
    };

    $_POST['FicONov']        = $_POST['FicONov'] ?? '';
    $_POST['FicValor']       = $_POST['FicValor'] ?? '';
    $_POST['FicObsN']        = $_POST['FicObsN'] ?? '';
    $_POST['datos_OtrasNov'] = $_POST['datos_OtrasNov'] ?? '';

    if ((valida_campo($_POST['FicONov'])) || (valida_campo($_POST['FicValor']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>Novedad y Valor</strong> son requeridos!');
        echo json_encode($data);
        exit;
    };
    if (valida_campo($_POST['datos_OtrasNov'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>datos_OtrasNov</strong> requerido!');
        echo json_encode($data);
        exit;
    };
    $datos_OtrasNov = explode('-', $_POST['datos_OtrasNov']);

    $FicLega  = test_input($datos_OtrasNov[0]);
    $FicFech  = test_input($datos_OtrasNov[1]);

    $FicONov  = test_input($_POST['FicONov']);
    $FicValor = test_input($_POST['FicValor']);
    $FicObsN  = test_input($_POST['FicObsN']);

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    $ExisteRegistro = CountRegistrosMayorCero("SELECT * FROM FICHAS2 WHERE FicFech = '$FicFech' AND FicLega = '$FicLega' AND FicTurn = 1 AND FicONov = '$FicONov'");

    if ($ExisteRegistro) {
        $data = array('status' => 'Error', 'Mensaje' => 'Error. Ya existe el registro');
        echo json_encode($data);
        exit;
    }
    require __DIR__ . '../../config/conect_mssql.php';

    $query = "SELECT TOP 1 OTRASNOV.ONovCodi, OTRASNOV.ONovDesc FROM OTRASNOV WHERE OTRASNOV.ONovCodi > 0 AND OTRASNOV.ONovCodi = '$FicONov'";
    $result  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($result)) {
        $ONovDesc = $row['ONovDesc'];
        $ONovCodi = $row['ONovCodi'];
    }
    sqlsrv_free_stmt($result);


    $Dato = 'Alta Otra Novedad: (' . $ONovCodi . ') ' . $ONovDesc . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
    $Dato2 = 'Otra Novedad: (' . $ONovCodi . ') ' . $ONovDesc;

    /** Luego insertamos  */
    if (InsertRegistro("INSERT INTO FICHAS2 (FicLega,FicFech,FicTurn,FicONov,FicValor,FicObsN,FechaHora) VALUES ('$FicLega','$FicFech',1,'$FicONov', '$FicValor','$FicObsN','$FechaHora')")) {
        $data = array('status' => 'ok', 'Mensaje' => $Dato2, 'tipo' => '');
        audito_ch('A', $Dato);
        /** Grabamos en Auditor */
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    } else {
        $data = array('status' => 'Error', 'Mensaje' => $Dato);
        echo json_encode($data);
        exit;
    }
    sqlsrv_close($link);
}
/** BAJA OTRAS NOVEDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['baja_ONov'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['bONov'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para eliminar Otras Novedades.');
        echo json_encode($data);
        exit;
    };

    $datos    = explode('-', $_POST['Datos']);
    /** FicOnov, FicFech, FicLega */
    $Descrip = test_input($_POST['Descrip']);
    $FicONov  = $datos[0];
    $FicFech  = $datos[1];
    $FicLega  = $datos[2];

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }

    if ((valida_campo($_POST['Datos']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos requerido.');
        echo json_encode($data);
        exit;
    };

    $Dato  = 'Baja Otra Novedad: (' . $FicONov . ') ' . $Descrip . '. Legajo: ' . $FicLega . '. Fecha: ' . Fech_Format_Var($FicFech, 'd-m-Y');
    $Dato2 = 'Otra Novedad: (' . $FicONov . ') ' . $Descrip;

    if ((DeleteRegistro("DELETE FROM FICHAS2 WHERE FicLega = '$FicLega' AND FicFech = '$FicFech' AND FicTurn = 1 AND FicONov = '$FicONov'"))) {
        audito_ch('B', $Dato);
        /** Grabo en la tabla Auditor */
        $data = array('status' => 'ok', 'Mensaje' => $Dato2);
        echo json_encode($data);
        exit;
    } else {
        exit;
    }
}
/** MODIFICAR OTRAS NOVEDAD */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_OtrasNov'] == 'mod')) {

    if ($_SESSION["ABM_ROL"]['mONov'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para modificar Otras Novedades.');
        echo json_encode($data);
        exit;
    };

    $_POST['FicONov']        = $_POST['FicONov'] ?? '';
    $_POST['FicValor']       = $_POST['FicValor'] ?? '';
    $_POST['FicObsN']        = $_POST['FicObsN'] ?? '';
    $_POST['datos_OtrasNov'] = $_POST['datos_OtrasNov'] ?? '';

    if ((valida_campo($_POST['FicONov'])) || (valida_campo($_POST['FicValor']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>Novedad y Valor</strong> son requeridos!');
        echo json_encode($data);
        exit;
    };
    if (valida_campo($_POST['datos_OtrasNov'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>datos_OtrasNov</strong> requerido!');
        echo json_encode($data);
        exit;
    };
    $datos_OtrasNov = explode('-', $_POST['datos_OtrasNov']);

    $FicLega  = test_input($datos_OtrasNov[0]);
    $FicFech  = test_input($datos_OtrasNov[1]);

    $FicONov  = test_input($_POST['FicONov']);
    $FicValor = test_input($_POST['FicValor']);
    $FicObsN  = test_input($_POST['FicObsN']);

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }
    $ExisteRegistro = CountRegistrosMayorCero("SELECT FicLega FROM FICHAS2 WHERE FicFech = '$FicFech' AND FicLega = '$FicLega' AND FicTurn = 1 AND FicONov = '$FicONov'");

    if (!$ExisteRegistro) {
        $data = array('status' => 'Error', 'Mensaje' => 'No existe el registro');
        echo json_encode($data);
        exit;
    }

    $ExisteRegistro = CountRegistrosMayorCero("SELECT FicLega FROM FICHAS2 WHERE FicFech = '$FicFech' AND FicLega = '$FicLega' AND FicTurn = 1 AND FicONov = '$FicONov' AND FicValor = '$FicValor' AND FicObsN = '$FicObsN'");

    if ($ExisteRegistro) {
        $data = array('status' => 'Error', 'Mensaje' => 'Ya existe el registro');
        echo json_encode($data);
        exit;
    }
    require __DIR__ . '../../config/conect_mssql.php';

    $query = "SELECT TOP 1 OTRASNOV.ONovCodi, OTRASNOV.ONovDesc FROM OTRASNOV WHERE OTRASNOV.ONovCodi > 0 AND OTRASNOV.ONovCodi = '$FicONov'";
    $result  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($result)) {
        $ONovDesc = $row['ONovDesc'];
        $ONovCodi = $row['ONovCodi'];
    }
    sqlsrv_free_stmt($result);


    $Dato = 'Modificación Otra Novedad: (' . $ONovCodi . ') ' . $ONovDesc . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
    $Dato2 = 'Otra Novedad: (' . $ONovCodi . ') ' . $ONovDesc;

    /** Luego UPDATE  */
    if (UpdateRegistro("UPDATE FICHAS2 Set FicValor = '$FicValor', FicObsN = '$FicObsN', FechaHora = '$FechaHora' WHERE FicLega = '$FicLega' and FicFech = '$FicFech' and FicTurn = 1 and FicONov = '$FicONov'")) {
        $data = array('status' => 'ok', 'Mensaje' => $Dato2, 'tipo' => 'mod');
        audito_ch('M', $Dato);
        /** Grabamos en Auditor */
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    } else {
        $data = array('status' => 'Error', 'Mensaje' => $Dato);
        echo json_encode($data);
        exit;
    }
    sqlsrv_close($link);
}
/** ALTA CITACION */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['alta_Citación'] == 'true')) {

    if ($_SESSION["ABM_ROL"]['aCit'] == '0') {
        $data = array('status' => 'error', 'Mensaje' => 'No tiene permiso para modificar Otras Novedades.');
        echo json_encode($data);
        exit;
    };

    $_POST['CitEntra']       = $_POST['CitEntra'] ?? '';
    $_POST['CitSale']        = $_POST['CitSale'] ?? '';
    $_POST['CitDesc']        = $_POST['CitDesc'] ?? '';
    $_POST['datos_Citación'] = $_POST['datos_Citación'] ?? '';

    $CitEntra       = test_input($_POST['CitEntra']);
    $CitSale        = test_input($_POST['CitSale']);
    $CitDesc        = test_input($_POST['CitDesc']);
    $datos_Citación = test_input($_POST['datos_Citación']);

    if (ValidaFormatoHora($CitEntra)) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto');
        echo json_encode($data);
        exit;
    }
    if (ValidaFormatoHora($CitSale)) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto');
        echo json_encode($data);
        exit;
    }
    if (ValidaFormatoHora($CitDesc)) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto');
        echo json_encode($data);
        exit;
    }

    if ((valida_campo($CitEntra)) || (valida_campo($CitSale))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos requeridos!');
        echo json_encode($data);
        exit;
    };
    if ((($CitEntra == '00:00') && ($CitSale == '00:00'))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos Entrada y Salida no pueden estar en 00:00');
        echo json_encode($data);
        exit;
    };
    if ((($CitEntra == '00:00'))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo Entrada no puede estar en 00:00');
        echo json_encode($data);
        exit;
    };
    if ((($CitSale == '00:00'))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo Salida no puede estar en 00:00');
        echo json_encode($data);
        exit;
    };
    if (ValidarHora($_POST['CitEntra'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto: ' . $_POST['CitSale']);
        echo json_encode($data);
        exit;
    }

    if (ValidarHora($_POST['CitSale'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Formato de Hora incorrecto: ' . $_POST['CitSale']);
        echo json_encode($data);
        exit;
    }

    if ((HoraMin($CitEntra) >= HoraMin($CitSale))) {
        $data = array('status' => 'error', 'Mensaje' => 'El Horario de entrada no puede ser Mayor o Igual de Salida.');
        echo json_encode($data);
        exit;
    };
    $Intervalo = HoraMin($CitSale) - HoraMin($CitEntra);

    if ((HoraMin($CitDesc) >= ($Intervalo))) {
        $data = array('status' => 'error', 'Mensaje' => 'El Descanso no puede ser Mayor al intervalo entre Entrada y Salida<br/>Intervalo: ' . FormatHora($Intervalo) . '<br/>Descanso: ' . $CitDesc);
        echo json_encode($data);
        exit;
    };

    if (valida_campo($_POST['datos_Citación'])) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo <strong>datos_Citación</strong> requerido!');
        echo json_encode($data);
        exit;
    };


    $datos_Citación = explode('-', $datos_Citación);
    $FicLega  = ($datos_Citación[0]);
    $FicFech  = ($datos_Citación[1]);

    if (PerCierre($FicFech, $FicLega)) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha de Cierre es Menor o Igual a: ' . Fech_Format_Var($FicFech, ('d/m/Y')));
        echo json_encode($data);
        exit;
    }
    $Dato = 'Modificación Citación: ' . $CitEntra . ' - ' . $CitSale . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
    $Dato2 = 'Citación: ' . $CitEntra . ' - ' . $CitSale;

    $ExisteCitacion = CountRegistrosMayorCero("SELECT CitLega ,CitFech ,CitTurn ,CitEntra ,CitSale ,CitDesc ,FechaHora FROM CITACION WHERE CitLega = '$FicLega' and CitFech = '$FicFech' and CitTurn = 1");

    if ($ExisteCitacion) {
        if (UpdateRegistro("UPDATE CITACION SET CitEntra = '$CitEntra', CitSale='$CitSale', CitDesc='$CitDesc', FechaHora ='$FechaHora' WHERE CitLega='$FicLega' AND CitFech ='$FicFech' AND CitTurn = 1")) {
            audito_ch('M', $Dato);
            if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
                $Procesado = " - Procesado.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado, 'tipo' => 'mod');
            } else {
                $Procesado = " - Sin procesar.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado, 'tipo' => 'mod');
            }
            echo json_encode($data);
            exit;
        };
    } else {
        $Dato = 'Alta Citación: ' . $CitEntra . ' - ' . $CitSale . ' de Legajo: ' . $FicLega . ' Fecha: ' . Fech_Format_Var($FicFech, 'd/m/Y');
        if (InsertRegistro("INSERT INTO CITACION (CitLega,CitFech,CitTurn,CitEntra,CitSale,CitDesc,FechaHora) VALUES ( '$FicLega','$FicFech','1','$CitEntra','$CitSale','$CitDesc','$FechaHora' )")) {
            audito_ch('A', $Dato);
            if (procesar_legajo($FicLega, $FicFech, $FicFech) == 'Terminado') {
                $Procesado = " - Procesado.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado, 'tipo' => 'alta');
            } else {
                $Procesado = " - Sin procesar.";
                $data = array('status' => 'ok', 'Mensaje' => $Dato2 . $Procesado, 'tipo' => 'alta');
            }
            echo json_encode($data);
            exit;
        };
    }
}
