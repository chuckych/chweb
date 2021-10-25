<?php
require __DIR__ . '../../../config/index.php';
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();


if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['Update_Leg'] == 'true')) {

    if(valida_campo(test_input($_POST['LegApNo']))){
        $data = array('status' => 'error', 'dato' => '<strong>Campo Apellido y Nombre Obligatorio</strong>.');
        echo json_encode($data);
        exit;
    };

    require __DIR__ . '../../../config/conect_mssql.php';
    
    $params  = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $data    = array();

    $query ="SELECT (COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='PERSONAL'";

    $result  = sqlsrv_query($link, $query, $params, $options);

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :
        foreach ($row as $key => $value) {
            UnsetPost($value);
        }
    endwhile;
    sqlsrv_free_stmt($result);
}
//sqlsrv_close($link);
// print_r(($column)); 
// exit;
UnsetPost('CierreFech');
UnsetPost('LegFeNa');
UnsetPost('LegFeEg');
UnsetPost('LegFeIn');
/** Recibo Variables */
$LegNume=test_input($_POST['LegNume']);$LegApNo=test_input($_POST['LegApNo']);$LegSect=test_input($_POST['LegSect']);$LegEsta=test_input($_POST['LegEsta']);$LegCant=test_input($_POST['LegCant']);$LegSec2=test_input($_POST['LegSec2']);$LegPlan=test_input($_POST['LegPlan']);$LegGrup=test_input($_POST['LegGrup']);$LegTDoc=test_input($_POST['LegTDoc']);$LegDocu=test_input($_POST['LegDocu']);$LegCUIT=test_input($_POST['LegCUIT']);$LegDomi=test_input($_POST['LegDomi']);$LegDoNu=test_input($_POST['LegDoNu']);$LegDoPi=test_input($_POST['LegDoPi']);$LegDoDP=test_input($_POST['LegDoDP']);$LegDoOb=test_input($_POST['LegDoOb']);$LegCOPO=test_input($_POST['LegCOPO']);$LegProv=test_input($_POST['LegProv']);$LegLoca=test_input($_POST['LegLoca']);$LegTel1=test_input($_POST['LegTel1']);$LegTeO1=test_input($_POST['LegTeO1']);$LegTel2=test_input($_POST['LegTel2']);$LegTeO2=test_input($_POST['LegTeO2']);$LegNaci=test_input($_POST['LegNaci']);$LegEsCi=test_input($_POST['LegEsCi']);$LegSexo=test_input($_POST['LegSexo']);$LegFeNa=test_input($_POST['LegFeNa']);$LegToTa=test_input($_POST['LegToTa']);$LegToSa=test_input($_POST['LegToSa']);$LegTipo=test_input($_POST['LegTipo']);$LegFeIn=test_input($_POST['LegFeIn']);$LegFeEg=test_input($_POST['LegFeEg']);$LegPrCo=test_input($_POST['LegPrCo']);$LegPrSe=test_input($_POST['LegPrSe']);$LegPrGr=test_input($_POST['LegPrGr']);$LegPrHo=test_input($_POST['LegPrHo']);$LegConv=test_input($_POST['LegConv']);$LegDesc=test_input($_POST['LegDesc']);$LegToIn=test_input($_POST['LegToIn']);$LegReTa=test_input($_POST['LegReTa']);$LegReIn=test_input($_POST['LegReIn']);$LegReSa=test_input($_POST['LegReSa']);$LegHLDe=test_input($_POST['LegHLDe']);$LegHLDH=test_input($_POST['LegHLDH']);$LegHLRo=test_input($_POST['LegHLRo']);$LegHGDe=test_input($_POST['LegHGDe']);$LegHGDH=test_input($_POST['LegHGDH']);$LegHGRo=test_input($_POST['LegHGRo']);$LegHSDe=test_input($_POST['LegHSDe']);$LegHSDH=test_input($_POST['LegHSDH']);$LegHSRo=test_input($_POST['LegHSRo']);$LegIncTi=test_input($_POST['LegIncTi']);$LegTel3=test_input($_POST['LegTel3']);$LegMail=test_input($_POST['LegMail']);$LegHoAl=test_input($_POST['LegHoAl']);$LegHoLi=test_input($_POST['LegHoLi']);$LegEmpr=test_input($_POST['LegEmpr']);$LegGrHa=test_input($_POST['LegGrHa']);$LegArea=test_input($_POST['LegArea']);$LegAvisa=test_input($_POST['LegAvisa']);$LegChkHo=test_input($_POST['LegChkHo']);$LegAntes=test_input($_POST['LegAntes']);$LegDespu=test_input($_POST['LegDespu']);$LegTarde=test_input($_POST['LegTarde']);$LegRegCH=test_input($_POST['LegRegCH']);$LegPrRe=test_input($_POST['LegPrRe']);$LegPrPl=test_input($_POST['LegPrPl']);$LegValHora=test_input($_POST['LegValHora']);$LegTare=test_input($_POST['LegTare']);$LegHabSali=test_input($_POST['LegHabSali']);$LegNo24=test_input($_POST['LegNo24']);$FechaHora=test_input($_POST['FechaHora']);$LegJornada=test_input($_POST['LegJornada']);$LegForPago=test_input($_POST['LegForPago']);$LegMoneda=test_input($_POST['LegMoneda']);$LegBanco=test_input($_POST['LegBanco']);$LegBanSuc=test_input($_POST['LegBanSuc']);$LegBanCTA=test_input($_POST['LegBanCTA']);$LegBanCBU=test_input($_POST['LegBanCBU']);$LegCalif=test_input($_POST['LegCalif']);$LegObs=test_input($_POST['LegObs']);$LegObsPlan=test_input($_POST['LegObsPlan']);$LegZona=test_input($_POST['LegZona']);$LegRedu=test_input($_POST['LegRedu']);$LegAFJP=test_input($_POST['LegAFJP']);$LegSind=test_input($_POST['LegSind']);$LegActi=test_input($_POST['LegActi']);$LegModa=test_input($_POST['LegModa']);$LegSitu=test_input($_POST['LegSitu']);$LegCond=test_input($_POST['LegCond']);$LegSine=test_input($_POST['LegSine']);$LegTicket=test_input($_POST['LegTicket']);$LegBasico=test_input($_POST['LegBasico']);$LegImporte1=test_input($_POST['LegImporte1']);$LegImporte2=test_input($_POST['LegImporte2']);$LegImporte3=test_input($_POST['LegImporte3']);$LegImporte4=test_input($_POST['LegImporte4']);$LegImporte5=test_input($_POST['LegImporte5']);$LegImporte6=test_input($_POST['LegImporte6']);$LegTopeAde=test_input($_POST['LegTopeAde']);$LegCapiLRT=test_input($_POST['LegCapiLRT']);$LegCalcGan=test_input($_POST['LegCalcGan']);$LegTZ=test_input($_POST['LegTZ']);$LegTZ1=test_input($_POST['LegTZ1']);$LegTZ2=test_input($_POST['LegTZ2']);$LegTZ3=test_input($_POST['LegTZ3']);$LegBandHor=test_input($_POST['LegBandHor']);$LegSucu=test_input($_POST['LegSucu']);$LegRegCO=test_input($_POST['LegRegCO']);$LegTareProd=test_input($_POST['LegTareProd']);$LegPrCosteo=test_input($_POST['LegPrCosteo']);$LegHLPlani=test_input($_POST['LegHLPlani']);
/** Fin Variables */
$CierreFech = test_input($_POST['CierreFech']);

/** Formato Variables */

    // $LegFeNa    = !empty(($LegFeNa)) ? FechaString($LegFeNa) : '17530101';
    $LegFeIn    = !empty(($LegFeIn)) ? dr_fecha($LegFeIn) : '17530101';
    $LegFeEg    = !empty(($LegFeEg)) ? dr_fecha($LegFeEg) : '17530101';
    $LegFeNa    = !empty(($LegFeNa)) ? dr_fecha($LegFeNa) : '17530101';
    $CierreFech = !empty(($CierreFech)) ? dr_fecha($CierreFech) : '17530101';

    if((empty($LegDocu)) && !empty($LegCUIT)){
        $Cuil= explode("-", $LegCUIT);
        $LegDocu = $Cuil[1];
    }

    $LegEsta     = $LegEsta=='on'? '1':'0';
    $LegPrCo     = $LegPrCo=='on'? '1':'0';
    $LegPrSe     = $LegPrSe=='on'? '1':'0';
    $LegPrGr     = $LegPrGr=='on'? '1':'0';
    $LegPrHo     = $LegPrHo=='on'? '1':'0';
    $LegPrRe     = $LegPrRe=='on'? '1':'0';
    $LegPrPl     = $LegPrPl=='on'? '1':'0';
    $LegNo24     = $LegNo24=='on'? '1':'0';
    $LegHLDH     = $LegHLDH=='on'? '1':'0';
    $LegHLDe     = $LegHLDe=='on'? '1':'0';
    $LegHLRo     = $LegHLRo=='on'? '1':'0';
    $LegHGDH     = $LegHGDH=='on'? '1':'0';
    $LegHGDe     = $LegHGDe=='on'? '1':'0';
    $LegHGRo     = $LegHGRo=='on'? '1':'0';
    $LegHSDH     = $LegHSDH=='on'? '1':'0';
    $LegHSDe     = $LegHSDe=='on'? '1':'0';
    $LegHSRo     = $LegHSRo=='on'? '1':'0';
    $LegPrCosteo = $LegPrCosteo=='on'? '1':'0';
    $LegHLPlani  = $LegHLPlani=='on'? '1':'0';

    $LegValHora = $LegValHora==''? '0':$LegValHora;

/** Fin Formato Variables */

    $Dato    = 'Legajo: '. $LegNume . '. '.$LegApNo;

    $FechaHora = date('Ymd H:i:s');

    $sql="UPDATE PERSONAL SET [PERSONAL].[LegApNo]='$LegApNo', [PERSONAL].[LegEsta]='$LegEsta', [PERSONAL].[LegEmpr]='$LegEmpr', [PERSONAL].[LegPlan]='$LegPlan', [PERSONAL].[LegSucu]='$LegSucu', [PERSONAL].[LegGrup]='$LegGrup', [PERSONAL].[LegSect]='$LegSect', [PERSONAL].[LegSec2]='$LegSec2', [PERSONAL].[LegTDoc]='$LegTDoc', [PERSONAL].[LegDocu]='$LegDocu', [PERSONAL].[LegCUIT]='$LegCUIT', [PERSONAL].[LegDomi]='$LegDomi', [PERSONAL].[LegDoNu]='$LegDoNu', [PERSONAL].[LegDoPi]='$LegDoPi', [PERSONAL].[LegDoDP]='$LegDoDP', [PERSONAL].[LegDoOb]='$LegDoOb', [PERSONAL].[LegCOPO]='$LegCOPO', [PERSONAL].[LegProv]='$LegProv', [PERSONAL].[LegLoca]='$LegLoca', [PERSONAL].[LegTel1]='$LegTel1', [PERSONAL].[LegTeO1]='$LegTeO1', [PERSONAL].[LegTel2]='$LegTel2', [PERSONAL].[LegTeO2]='$LegTeO2', [PERSONAL].[LegTel3]='$LegTel3', [PERSONAL].[LegMail]='$LegMail', [PERSONAL].[LegNaci]='$LegNaci', [PERSONAL].[LegEsCi]='$LegEsCi', [PERSONAL].[LegSexo]='$LegSexo', [PERSONAL].[LegFeNa]='$LegFeNa', [PERSONAL].[LegTipo]='$LegTipo', [PERSONAL].[LegFeIn]='$LegFeIn', [PERSONAL].[LegFeEg]='$LegFeEg', [PERSONAL].[LegPrCo]='$LegPrCo', [PERSONAL].[LegPrSe]='$LegPrSe', [PERSONAL].[LegPrGr]='$LegPrGr', [PERSONAL].[LegPrPl]='$LegPrPl', [PERSONAL].[LegPrRe]='$LegPrRe', [PERSONAL].[LegPrHo]='$LegPrHo', [PERSONAL].[LegToTa]='$LegToTa', [PERSONAL].[LegToIn]='$LegToIn', [PERSONAL].[LegToSa]='$LegToSa', [PERSONAL].[LegReTa]='$LegReTa', [PERSONAL].[LegReIn]='$LegReIn', [PERSONAL].[LegReSa]='$LegReSa', [PERSONAL].[LegIncTi]='$LegIncTi', [PERSONAL].[LegHLDe]='$LegHLDe', [PERSONAL].[LegHLDH]='$LegHLDH', [PERSONAL].[LegHLRo]='$LegHLRo', [PERSONAL].[LegHGDe]='$LegHGDe', [PERSONAL].[LegHGDH]='$LegHGDH', [PERSONAL].[LegHGRo]='$LegHGRo', [PERSONAL].[LegHSDe]='$LegHSDe', [PERSONAL].[LegHSDH]='$LegHSDH', [PERSONAL].[LegHSRo]='$LegHSRo', [PERSONAL].[LegHoAl]='$LegHoAl', [PERSONAL].[LegHoLi]='$LegHoLi', [PERSONAL].[LegGrHa]='$LegGrHa', [PERSONAL].[LegRegCH]='$LegRegCH', [PERSONAL].[LegValHora]='$LegValHora', [PERSONAL].[LegConv]='$LegConv', [PERSONAL].[LegNo24]='$LegNo24', [PERSONAL].[LegTareProd]='$LegTareProd', [PERSONAL].[LegPrCosteo]='$LegPrCosteo', [PERSONAL].[LegHLPlani]='$LegHLPlani', [PERSONAL].[FechaHora]='$FechaHora' WHERE LegNume=$LegNume";
        // print_r($sql); exit;
            $stmt = sqlsrv_prepare($link, $sql, $params, $options); /** preparar la sentencia */

            if (!$stmt) {
                if( ($errors = sqlsrv_errors() ) != null) {
                    foreach( $errors as $error ) {
                        $data = array("status" => "error", "dato" => $error['message']);
                    }
                }
            }
            if (sqlsrv_execute($stmt)) { /** ejecuto la sentencia */
                

                /** Consultamos si el legajo existe en la tabla PERCIERRE */
                $queryCierre = "SELECT * FROM PERCIERRE WHERE CierreLega = '$LegNume'"; 
                $resultCierre  = sqlsrv_query($link, $queryCierre, $params, $options);
                

                if (sqlsrv_num_rows($resultCierre) > 0) {

                    while ($fila = sqlsrv_fetch_array($resultCierre)) {
                        // echo json_encode (array("tabla"=> $fila['CierreFech']->format('Ymd'), "post"=> $CierreFech));
                        $perCierre = $fila['CierreFech']->format('Ymd');
                        if($perCierre != $CierreFech){

                            $sql2 = "UPDATE PERCIERRE SET CierreFech = '$CierreFech', FechaHora = '$FechaHora' Where CierreLega = '$LegNume'";
                            $stmt2 = sqlsrv_prepare($link, $sql2);
                            sqlsrv_execute($stmt2);

                            $CierreFech = $CierreFech == '17530101' ? 'Nulo': Fech_Format_Var($CierreFech, 'd/m/Y');
                            $Dato = 'Legajo.: '. $LegNume . '. '.$LegApNo.'. Fecha Cierre: '.$CierreFech;
                            
                            // sleep(1);
                            // audito_ch('M', $Dato,  '10'C);
                        };
                    }
                    
                }else{
                    $sql3= "INSERT INTO PERCIERRE (CierreLega, CierreFech, FechaHora)VALUES('$LegNume','$CierreFech','$FechaHora')";
                    $stmt3 = sqlsrv_prepare($link, $sql3);
                    sqlsrv_execute($stmt3);
                    $Dato = 'Legajo.: '. $LegNume . '. '.$LegApNo.'. Fecha Cierre: '.Fech_Format_Var($CierreFech, 'd/m/Y');
                    // sleep(1);
                    // audito_ch('A', $Dato,  '10'C);
                }

                $data = array("status" => "ok", "dato" => $Dato, "Lega" => $LegNume, "Nombre"=> $LegApNo, 'docu'=> $LegDocu);
                echo json_encode($data); /** retorno resultados en formato json */
                
                audito_ch('M', $Dato,  '10'); /** Grabo en la tabla Auditor */

                sqlsrv_free_stmt($resultCierre);

            } else {
                // die(print_r(sqlsrv_errors(), true));
                if( ($errors = sqlsrv_errors() ) != null) {
                    foreach( $errors as $error ) {
                        $mensaje = explode(']', $error['message']);
                        $data[] = array("status" => "error", "dato" => $mensaje[3]);
                    }
                }
                echo json_encode($data[0]);
            }
    sqlsrv_close($link);
}