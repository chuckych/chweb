<?php
require __DIR__ . '../../config/index.php';
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['ALTALeg'] == 'true')) {

    if(valida_campo(test_input($_POST['LegNume']))){
        $data = array('status' => 'error', 'Mensaje' => 'Campo Legajo Obligatorio.');
        echo json_encode($data);
        exit;
    };

    require __DIR__ . '../../config/conect_mssql.php';

    $params  = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data    = array();

    /** Recibo Variables */
    /**  1:*/   $LegNume = test_input($_POST['LegNume']) ?? ''; 
    /**  2:*/   $LegApNo = test_input($_POST['LegApNo']) ?? ''; 
    /** Fin Variables */

    /** Query revisar si existe un registro igual */
    $query = "SELECT TOP 1 PERSONAL.LegNume 
    FROM PERSONAL 
    WHERE PERSONAL.LegNume = '$LegNume'";
    $result  = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($fila = sqlsrv_fetch_array($result)) {
            $data = array('status' => 'Error', 'Mensaje' => 'El Legajo: (<strong>'.$LegNume.'</strong>) ya existe.');
            echo json_encode($data);
        }
        sqlsrv_free_stmt($result);
        exit;
    }
    
    $Dato    = 'Legajo: '. $LegNume . '. '.$LegApNo;

    $FechaHora = date('Ymd H:i:s');

    $sql ="INSERT INTO PERSONAL (LegNume,LegApNo,LegEsta,LegEmpr,LegPlan,LegSucu,LegGrup,LegSect,LegSec2,LegTDoc,LegDocu,LegCUIT,LegDomi,LegDoNu,LegDoPi,LegDoDP,LegDoOb,LegCOPO,LegProv,LegLoca,LegTel1,LegTeO1,LegTel2,LegTeO2,LegTel3,LegMail,LegNaci,LegEsCi,LegSexo,LegFeNa,LegTipo,LegFeIn,LegFeEg,LegPrCo,LegPrSe,LegPrGr,LegPrPl,LegPrRe,LegPrHo,LegToTa,LegToIn,LegToSa,LegReTa,LegReIn,LegReSa,LegIncTi,LegDesc,LegHLDe,LegHLDH,LegHLRo,LegHGDe,LegHGDH,LegHGRo,LegHSDe,LegHSDH,LegHSRo,LegHoAl,LegHoLi,LegGrHa,LegArea,LegAvisa,LegChkHo,LegAntes,LegDespu,LegTarde,LegRegCH,LegRegCO,LegCant,LegValHora,LegHabSali,LegJornada,LegForPago,LegMoneda,LegBanco,LegBanSuc,LegBanCTA,LegBanCBU,LegConv,LegCalif,LegTare,LegObs,LegObsPlan,LegZona,LegRedu,LegAFJP,LegSind,LegActi,LegModa,LegSitu,LegCond,LegSine,LegTicket,LegBasico,LegImporte1,LegImporte2,LegImporte3,LegImporte4,LegImporte5,LegImporte6,LegTopeAde,LegCapiLRT,LegCalcGan,LegTareProd,LegNo24,LegTZ,LegTZ1,LegTZ2,LegTZ3,LegBandHor,FechaHora) VALUES ('$LegNume','$LegApNo',0,0,0,0,0,0,0,1,0,'','',0,0,'','','',0,0,'','','','','','',0,0,0,'17530101',0,'17530101','17530101',0,0,0,0,1,0,0,0,0,1,1,1,0,0,1,1,1,0,0,0,0,0,0,0,'01:00',0,0,0,0,'00:00','00:00','00:00',0,0,0, 0,'00:00',0,1,0,0,0,'','',0,0,0,0,0,1, 30,0,0,49,8,1,1,0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,0,0,1,0,0,0,0,'1111111111111111','$FechaHora')";
        // print_r($sql);exit;
            $stmt = sqlsrv_prepare($link, $sql, $params, $options); /** preparar la sentencia */

            if (!$stmt) {
                if( ($errors = sqlsrv_errors() ) != null) {
                    foreach( $errors as $error ) {
                        $data = array("status" => "error", "Mensaje" => $error['message']);
                    }
                }
            }
            if (sqlsrv_execute($stmt)) { /** ejecuto la sentencia */
                /** Grabo en la tabla Auditor */
            
                $data = array("status" => "ok", "Mensaje" => $Dato, "Legajo" => $LegNume);
                echo json_encode($data); /** retorno resultados en formato json */
                audito_ch('A', $Dato,  '10'); 

                $sql2 = "INSERT INTO PERCIERRE (CierreLega,CierreFech,FechaHora) Values('$LegNume','17530101','$FechaHora')"; /** Query CierreFech */
                $stmt2 = sqlsrv_prepare($link, $sql2); /** preparar la sentencia */
                sqlsrv_execute($stmt2);

            } else {
                // die(print_r(sqlsrv_errors(), true));
                if( ($errors = sqlsrv_errors() ) != null) {
                    foreach( $errors as $error ) {
                        $mensaje = explode(']', $error['message']);
                        $data[] = array("status" => "error", "Mensaje" => $mensaje[3]);
                    }
                }
                
                echo json_encode($data[0]);
            }
        
    sqlsrv_close($link);
}