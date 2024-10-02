<?php
require __DIR__ . '../../config/index.php';
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['ALTALeg'] == 'true')) {

    $_POST['LegEmpr'] = $_POST['LegEmpr'] ?? '';
    $_POST['LegNume'] = $_POST['LegNume'] ?? '';
    $_POST['LegApNo'] = $_POST['LegApNo'] ?? '';

    if (valida_campo(test_input($_POST['LegNume']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo Legajo es requerido.');
        echo json_encode($data);
        exit;
    };
    if (valida_campo(test_input($_POST['LegApNo']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo Nombre es requerido.');
        echo json_encode($data);
        exit;
    };
    if (valida_campo(test_input($_POST['LegEmpr']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campo Empresa es requerido.');
        echo json_encode($data);
        exit;
    };

    require __DIR__ . '../../config/conect_mssql.php';

    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data = array();

    /** Recibo Variables */
    /**  1:*/
    $LegNume = test_input($_POST['LegNume']) ?? '';
    /**  2:*/
    $LegApNo = test_input($_POST['LegApNo']) ?? '';
    $LegApNo = substr($LegApNo, 0, 40);
    /**  3:*/
    $LegEmpr = test_input($_POST['LegEmpr']) ?? '';
    /** Fin Variables */

    /** Query revisar si existe un registro igual */
    $query = "SELECT TOP 1 PERSONAL.LegNume 
    FROM PERSONAL 
    WHERE PERSONAL.LegNume = '$LegNume'";
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'Error', 'Mensaje' => 'El Legajo: (<strong>' . $LegNume . '</strong>) ya existe.');
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }

    $Dato = 'Legajo: ' . $LegNume . '. ' . $LegApNo;

    $FechaHora = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')));
    // CREATE TABLE "PERSONAL" (
    //     "LegNume" INT NOT NULL,
    //     "LegApNo" VARCHAR(40) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegSect" SMALLINT NULL DEFAULT NULL,
    //     "LegEsta" SMALLINT NULL DEFAULT NULL,
    //     "LegCant" SMALLINT NULL DEFAULT NULL,
    //     "LegSec2" SMALLINT NULL DEFAULT NULL,
    //     "LegPlan" SMALLINT NULL DEFAULT NULL,
    //     "LegGrup" SMALLINT NULL DEFAULT NULL,
    //     "LegTDoc" SMALLINT NULL DEFAULT NULL,
    //     "LegDocu" INT NULL DEFAULT NULL,
    //     "LegCUIT" VARCHAR(13) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegDomi" VARCHAR(40) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegDoNu" INT NULL DEFAULT NULL,
    //     "LegDoPi" SMALLINT NULL DEFAULT NULL,
    //     "LegDoDP" VARCHAR(5) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegDoOb" VARCHAR(40) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegCOPO" VARCHAR(8) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegProv" SMALLINT NULL DEFAULT NULL,
    //     "LegLoca" INT NULL DEFAULT NULL,
    //     "LegTel1" VARCHAR(15) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegTeO1" VARCHAR(20) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegTel2" VARCHAR(15) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegTeO2" VARCHAR(20) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegNaci" SMALLINT NULL DEFAULT NULL,
    //     "LegEsCi" SMALLINT NULL DEFAULT NULL,
    //     "LegSexo" SMALLINT NULL DEFAULT NULL,
    //     "LegFeNa" DATETIME NULL DEFAULT NULL,
    //     "LegToTa" SMALLINT NULL DEFAULT NULL,
    //     "LegToSa" SMALLINT NULL DEFAULT NULL,
    //     "LegTipo" SMALLINT NULL DEFAULT NULL,
    //     "LegFeIn" DATETIME NULL DEFAULT NULL,
    //     "LegFeEg" DATETIME NULL DEFAULT NULL,
    //     "LegPrCo" SMALLINT NULL DEFAULT NULL,
    //     "LegPrSe" SMALLINT NULL DEFAULT NULL,
    //     "LegPrGr" SMALLINT NULL DEFAULT NULL,
    //     "LegPrHo" SMALLINT NULL DEFAULT NULL,
    //     "LegConv" SMALLINT NULL DEFAULT NULL,
    //     "LegDesc" SMALLINT NULL DEFAULT NULL,
    //     "LegToIn" SMALLINT NULL DEFAULT NULL,
    //     "LegReTa" SMALLINT NULL DEFAULT NULL,
    //     "LegReIn" SMALLINT NULL DEFAULT NULL,
    //     "LegReSa" SMALLINT NULL DEFAULT NULL,
    //     "LegHLDe" SMALLINT NULL DEFAULT NULL,
    //     "LegHLDH" SMALLINT NULL DEFAULT NULL,
    //     "LegHLRo" SMALLINT NULL DEFAULT NULL,
    //     "LegHGDe" SMALLINT NULL DEFAULT NULL,
    //     "LegHGDH" SMALLINT NULL DEFAULT NULL,
    //     "LegHGRo" SMALLINT NULL DEFAULT NULL,
    //     "LegHSDe" SMALLINT NULL DEFAULT NULL,
    //     "LegHSDH" SMALLINT NULL DEFAULT NULL,
    //     "LegHSRo" SMALLINT NULL DEFAULT NULL,
    //     "LegIncTi" SMALLINT NULL DEFAULT NULL,
    //     "LegTel3" VARCHAR(15) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegMail" VARCHAR(250) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegHoAl" SMALLINT NULL DEFAULT NULL,
    //     "LegHoLi" VARCHAR(5) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegEmpr" INT NULL DEFAULT NULL,
    //     "LegGrHa" SMALLINT NULL DEFAULT NULL,
    //     "LegArea" SMALLINT NULL DEFAULT NULL,
    //     "LegAvisa" SMALLINT NULL DEFAULT NULL,
    //     "LegChkHo" SMALLINT NULL DEFAULT NULL,
    //     "LegAntes" VARCHAR(5) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegDespu" VARCHAR(5) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegTarde" VARCHAR(5) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegRegCH" SMALLINT NULL DEFAULT NULL,
    //     "LegPrRe" SMALLINT NULL DEFAULT NULL,
    //     "LegPrPl" SMALLINT NULL DEFAULT NULL,
    //     "LegValHora" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegTare" SMALLINT NULL DEFAULT NULL,
    //     "LegHabSali" VARCHAR(5) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegNo24" SMALLINT NULL DEFAULT NULL,
    //     "FechaHora" DATETIME NULL DEFAULT NULL,
    //     "LegJornada" SMALLINT NULL DEFAULT NULL,
    //     "LegForPago" SMALLINT NULL DEFAULT NULL,
    //     "LegMoneda" INT NULL DEFAULT NULL,
    //     "LegBanco" SMALLINT NULL DEFAULT NULL,
    //     "LegBanSuc" SMALLINT NULL DEFAULT NULL,
    //     "LegBanCTA" VARCHAR(30) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegBanCBU" VARCHAR(30) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegCalif" SMALLINT NULL DEFAULT NULL,
    //     "LegObs" SMALLINT NULL DEFAULT NULL,
    //     "LegObsPlan" SMALLINT NULL DEFAULT NULL,
    //     "LegZona" SMALLINT NULL DEFAULT NULL,
    //     "LegRedu" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegAFJP" SMALLINT NULL DEFAULT NULL,
    //     "LegSind" SMALLINT NULL DEFAULT NULL,
    //     "LegActi" SMALLINT NULL DEFAULT NULL,
    //     "LegModa" SMALLINT NULL DEFAULT NULL,
    //     "LegSitu" SMALLINT NULL DEFAULT NULL,
    //     "LegCond" SMALLINT NULL DEFAULT NULL,
    //     "LegSine" SMALLINT NULL DEFAULT NULL,
    //     "LegTicket" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegBasico" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegImporte1" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegImporte2" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegImporte3" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegImporte4" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegImporte5" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegImporte6" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegTopeAde" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegCapiLRT" DECIMAL(12,2) NULL DEFAULT NULL,
    //     "LegCalcGan" SMALLINT NULL DEFAULT NULL,
    //     "LegTZ" SMALLINT NULL DEFAULT NULL,
    //     "LegTZ1" SMALLINT NULL DEFAULT NULL,
    //     "LegTZ2" SMALLINT NULL DEFAULT NULL,
    //     "LegTZ3" SMALLINT NULL DEFAULT NULL,
    //     "LegBandHor" VARCHAR(16) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegSucu" SMALLINT NULL DEFAULT NULL,
    //     "LegRegCO" SMALLINT NULL DEFAULT NULL,
    //     "LegTareProd" INT NULL DEFAULT NULL,
    //     "LegAFIPCCT" SMALLINT NULL DEFAULT NULL,
    //     "LegAFIPSCVO" SMALLINT NULL DEFAULT NULL,
    //     "LegAFIPLoc" VARCHAR(2) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegAFIPRedu" SMALLINT NULL DEFAULT NULL,
    //     "LegHLPlani" SMALLINT NULL DEFAULT NULL,
    //     "LegPrCosteo" SMALLINT NULL DEFAULT NULL,
    //     "LegIntExt" SMALLINT NULL DEFAULT NULL,
    //     "LegTZConId" SMALLINT NULL DEFAULT NULL,
    //     "LegCtaTP" VARCHAR(100) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegNume2" VARCHAR(10) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     "LegAGPass" VARCHAR(100) NULL DEFAULT NULL COLLATE 'Modern_Spanish_CI_AS',
    //     PRIMARY KEY ("LegNume")
    // )
    // ;

    $systemVersion = explode('_', $_SESSION['VER_DB_CH']);
    $systemVersion = intval($systemVersion[1]) ?? '';

    $sql = "INSERT INTO PERSONAL (LegNume,LegApNo,LegEsta,LegEmpr,LegPlan,LegSucu,LegGrup,LegSect,LegSec2,LegTDoc,LegDocu,LegCUIT,LegDomi,LegDoNu,LegDoPi,LegDoDP,LegDoOb,LegCOPO,LegProv,LegLoca,LegTel1,LegTeO1,LegTel2,LegTeO2,LegTel3,LegMail,LegNaci,LegEsCi,LegSexo,LegFeNa,LegTipo,LegFeIn,LegFeEg,LegPrCo,LegPrSe,LegPrGr,LegPrPl,LegPrRe,LegPrHo,LegToTa,LegToIn,LegToSa,LegReTa,LegReIn,LegReSa,LegIncTi,LegDesc,LegHLDe,LegHLDH,LegHLRo,LegHGDe,LegHGDH,LegHGRo,LegHSDe,LegHSDH,LegHSRo,LegHoAl,LegHoLi,LegGrHa,LegArea,LegAvisa,LegChkHo,LegAntes,LegDespu,LegTarde,LegRegCH,LegRegCO,LegCant,LegValHora,LegHabSali,LegJornada,LegForPago,LegMoneda,LegBanco,LegBanSuc,LegBanCTA,LegBanCBU,LegConv,LegCalif,LegTare,LegObs,LegObsPlan,LegZona,LegRedu,LegAFJP,LegSind,LegActi,LegModa,LegSitu,LegCond,LegSine,LegTicket,LegBasico,LegImporte1,LegImporte2,LegImporte3,LegImporte4,LegImporte5,LegImporte6,LegTopeAde,LegCapiLRT,LegCalcGan,LegTareProd,LegNo24,LegTZ,LegTZ1,LegTZ2,LegTZ3,LegBandHor,FechaHora, LegAFIPCCT, LegAFIPSCVO, LegAFIPLoc, LegAFIPRedu, LegHLPlani, LegPrCosteo, LegIntExt, LegTZConId";
    $sql .= ($systemVersion >= 70) ?  ", LegCtaTP, LegNume2, LegAGPass" : '';
    $sql .= ") ";

    $sql .= " VALUES ('$LegNume','$LegApNo',0,'$LegEmpr',0,0,0,0,0,1,0,'','',0,0,'','','',0,0,'','','','','','',0,0,0, CONVERT(datetime, '1753-01-01', 120),0, CONVERT(datetime, '1753-01-01', 120), CONVERT(datetime, '1753-01-01', 120),0,0,0,0,1,0,0,0,0,1,1,1,0,0,1,1,1,0,0,0,0,0,0,0,'01:00',0,0,0,0,'00:00','00:00','00:00',0,0,0, 0,'00:00',0,1,0,0,0,'','',0,0,0,0,0,1, 30,0,0,49,8,1,1,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0,0,1,0,0,0,0,'1111111111111111', CONVERT(datetime, '$FechaHora', 121),0,0,0,0,0,0,0,0";
    $sql .= ($systemVersion >= 70) ?  ",0,0,0" : '';
    $sql .= ")";
    // print_r($sql);
    // exit;
    $stmt = sqlsrv_prepare($link, $sql, $params, $options);
    /** preparar la sentencia */

    if (!$stmt) {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $data = array("status" => "error", "Mensaje" => $error['message']);
            }
        }
    }
    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        /** Grabo en la tabla Auditor */

        $data = array("status" => "ok", "Mensaje" => $Dato, "Legajo" => $LegNume);
        echo json_encode($data);
        /** retorno resultados en formato json */
        audito_ch('A', $Dato, '10');

        $sql2 = "INSERT INTO PERCIERRE (CierreLega,CierreFech,FechaHora) Values('$LegNume','17530101','$FechaHora')";
        /** Query CierreFech */
        $stmt2 = sqlsrv_prepare($link, $sql2);
        /** preparar la sentencia */
        sqlsrv_execute($stmt2);
    } else {
        // die(print_r(sqlsrv_errors(), true));
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "Mensaje" => $mensaje[3]);
            }
        }

        echo json_encode($data[0]);
    }

    sqlsrv_close($link);
}
