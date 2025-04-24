<?php
require __DIR__ . '/../../config/conect_mssql.php';
E_ALL();

//  if(valida_campo($_POST['IDCodigo'])){
//      $data = array('status' => 'cod_requerido', 'dato' => 'Identicador requerido.');
//      echo json_encode($data);
//      exit;
//  };
$LegNume = test_input($_GET['_leg']);
$query = "SELECT TOP 1
        [LegNume] ,[LegApNo] ,[LegEsta] ,[LegEmpr] , EmpRazon, [LegPlan] , PlaDesc, [LegSucu] , SucDesc, [LegGrup] , GruDesc, [LegSect] , SecDesc, [LegSec2] , Se2Desc, [LegTDoc] ,[LegDocu] ,[LegCUIT] ,[LegDomi] ,[LegDoNu] ,[LegDoPi] ,[LegDoDP] ,[LegDoOb] ,[LegCOPO] ,[LegProv] , ProDesc, [LegLoca] , LocDesc, [LegTel1] ,[LegTeO1] ,[LegTel2] ,[LegTeO2] ,[LegTel3] ,[LegMail] ,[LegNaci] , NacDesc, [LegEsCi] ,[LegSexo] ,[LegFeNa] ,[LegTipo] ,[LegFeIn] ,[LegFeEg] ,[LegPrCo] ,[LegPrSe] ,[LegPrGr] ,[LegPrPl] ,[LegPrRe] ,[LegPrHo] ,[LegToTa] ,[LegToIn] ,[LegToSa] ,[LegReTa] ,[LegReIn] ,[LegReSa] ,[LegIncTi] ,[LegDesc] ,[LegHLDe] ,[LegHLDH] ,[LegHLRo] ,[LegHGDe] ,[LegHGDH] ,[LegHGRo] ,[LegHSDe] ,[LegHSDH] ,[LegHSRo] ,[LegHoAl] ,[LegHoLi] ,[LegGrHa], GHaDesc, [LegArea] ,[LegAvisa] ,[LegChkHo] ,[LegAntes] ,[LegDespu] ,[LegTarde] ,[LegRegCH] , RCDesc, [LegRegCO] ,[LegCant] ,[LegValHora] ,[LegHabSali] ,[LegJornada] ,[LegForPago] ,[LegMoneda] ,[LegBanco] ,[LegBanSuc] ,[LegBanCTA] ,[LegBanCBU] ,[LegConv] , ConDesc, [LegCalif] ,[LegTare] ,[LegObs] ,[LegObsPlan] ,[LegZona] ,[LegRedu] ,[LegAFJP] ,[LegSind] ,[LegActi] ,[LegModa] ,[LegSitu] ,[LegCond] ,[LegSine] ,[LegTicket] ,[LegBasico] ,[LegImporte1] ,[LegImporte2] ,[LegImporte3] ,[LegImporte4] ,[LegImporte5] ,[LegImporte6] ,[LegTopeAde] ,[LegCapiLRT] ,[LegCalcGan] ,[LegNo24] ,[LegTZ] ,[LegTZ1] ,[LegTZ2] ,[LegTZ3] ,[LegBandHor] ,[LegTareProd], TareDesc, CierreFech, Se2Codi, [LegPrCosteo], [LegHLPlani]
        FROM PERSONAL
        LEFT JOIN NACIONES ON PERSONAL.LegNaci = NACIONES.NacCodi
        LEFT JOIN PROVINCI ON PERSONAL.LegProv = PROVINCI.ProCodi
        LEFT JOIN LOCALIDA ON PERSONAL.LegLoca = LOCALIDA.LocCodi
        LEFT JOIN EMPRESAS ON PERSONAL.LegEmpr = EMPRESAS.EmpCodi
        LEFT JOIN PLANTAS ON PERSONAL.LegPlan = PLANTAS.PlaCodi
        LEFT JOIN CONVENIO ON PERSONAL.LegConv = CONVENIO.ConCodi
        LEFT JOIN SECTORES ON PERSONAL.LegSect = SECTORES.SecCodi
        LEFT JOIN SECCION ON PERSONAL.LegSec2 = SECCION.Se2Codi AND SECTORES.SecCodi = SECCION.SecCodi
        LEFT JOIN GRUPOS ON PERSONAL.LegGrup = GRUPOS.GruCodi
        LEFT JOIN SUCURSALES ON PERSONAL.LegSucu = SUCURSALES.SucCodi
        LEFT JOIN TAREAS ON PERSONAL.LegTareProd = TAREAS.TareCodi
        LEFT JOIN PERCIERRE ON PERSONAL.LegNume = PERCIERRE.CierreLega
        LEFT JOIN REGLASCH ON PERSONAL.LegRegCH = REGLASCH.RCCodi
        LEFT JOIN GRUPCAPT ON PERSONAL.LegGrHa = GRUPCAPT.GHaCodi
        WHERE LegNume = $LegNume"; /** Query */
// print_r($query);exit;
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $pers = ($fila);
    }
} else {
    // echo h4('ERROR');
    // echo '</br>';
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            $data = array("status" => "error", "dato" => $error['message']);
            echo ($error['message']);
        }
    }
    // print_r($data);
    // die(print_r(sqlsrv_errors(), true));
    sqlsrv_close($link);
    exit;
}

sqlsrv_close($link);

$persLegFeNa = ($pers['LegFeNa']->format('Ymd') == '17530101') ? '' : $pers['LegFeNa']->format('Y-m-d'); /** Fecha de Nacimiento */
$persLegFeIn = ($pers['LegFeIn']->format('Ymd') == '17530101') ? '' : $pers['LegFeIn']->format('Y-m-d'); /** Fecha de Ingreso */
$persLegFeEg = ($pers['LegFeEg']->format('Ymd') == '17530101') ? '' : $pers['LegFeEg']->format('Y-m-d'); /** Fecha de Egreso */
//  ($pers['CierreFech'] ?? '') && ($pers['CierreFech']->format('Y-m-d') == '17530101')

$persCierreFech = (isset($pers['CierreFech']) && !empty($pers['CierreFech'])) ? $pers['CierreFech']->format('Y-m-d') : '1753-01-01';
//  echo 'CierreFech: '. ($persCierreFech); exit;
//  print_r($persCierreFech); exit;

$persCierreFech = ($persCierreFech == '1753-01-01') ? '' : $persCierreFech; /** Fecha de Cierre */

//  $persCierreFech  = ($pers['CierreFech']->format('Y-m-d') == '17530101') ? '' : $pers['CierreFech']->format('Y-m-d'); /** Fecha de Cierre */

$persLegEsta = ($pers['LegEsta'] == '1') ? 'checked' : ''; /** No controlar Horario */
$persLegPrCo = ($pers['LegPrCo'] == '1') ? 'checked' : ''; /** Procesar Convenio */
$persLegPrSe = ($pers['LegPrSe'] == '1') ? 'checked' : ''; /** Procesar Sector */
$persLegPrGr = ($pers['LegPrGr'] == '1') ? 'checked' : ''; /** Procesar Grupo */
$persLegPrHo = ($pers['LegPrHo'] == '1') ? 'checked' : ''; /** Procesar Horario */
$persLegPrRe = ($pers['LegPrRe'] == '1') ? 'checked' : ''; /** Procesar Regla de Control */
$persLegPrPl = ($pers['LegPrPl'] == '1') ? 'checked' : ''; /** Procesar Planta */
$persLegNo24 = ($pers['LegNo24'] == '1') ? 'checked' : ''; /** No Partir Novedades 24Hs */
$persLegHLDH = ($pers['LegHLDH'] == '1') ? 'checked' : ''; /** Asig por Legajo Desde-Hasta  */
$persLegHLDe = ($pers['LegHLDe'] == '1') ? 'checked' : ''; /** Asig por Legajo Desde        */
$persLegHLRo = ($pers['LegHLRo'] == '1') ? 'checked' : ''; /** Asig por Legajo Rotación     */
$persLegHGDH = ($pers['LegHGDH'] == '1') ? 'checked' : ''; /** Asig por Grupo Desde-Hasta   */
$persLegHGDe = ($pers['LegHGDe'] == '1') ? 'checked' : ''; /** Asig por Grupo Desde         */
$persLegHGRo = ($pers['LegHGRo'] == '1') ? 'checked' : ''; /** Asig por Grupo Rotación      */
$persLegHSDH = ($pers['LegHSDH'] == '1') ? 'checked' : ''; /** Asig por Sector Desde-Hasta  */
$persLegHSDe = ($pers['LegHSDe'] == '1') ? 'checked' : ''; /** Asig por Sector Desde        */
$persLegHSRo = ($pers['LegHSRo'] == '1') ? 'checked' : ''; /** Asig por Sector Rotación     */
$persLegPrCosteo = ($pers['LegPrCosteo'] == '1') ? 'checked' : ''; /** Calcular Horas Costeadas */
$persLegHLPlani = ($pers['LegHLPlani'] == '1') ? 'checked' : ''; /** Usar Planificación */
$persRCDesc = ($pers['LegRegCH'] == '0') ? 'Sin Regla' : $pers['RCDesc'];
$persGHaDesc = ($pers['LegGrHa'] == '0') ? 'Sin Grupo' : $pers['GHaDesc'];
$persProDesc = ($pers['LegProv'] == '0') ? 'Sin Provincia' : $pers['ProDesc'];
$persNacDesc = ($pers['LegNaci'] == '0') ? 'Sin Nacionalidad' : $pers['NacDesc'];
$persLocDesc = ($pers['LegLoca'] == '0') ? '' : $pers['LocDesc'];
$persEmpRazon = ($pers['LegEmpr'] == '0') ? 'Sin Empresa' : $pers['EmpRazon'];
$persPlaDesc = ($pers['LegPlan'] == '0') ? 'Sin Planta' : $pers['PlaDesc'];
$persSecDesc = ($pers['LegSect'] == '0') ? 'Sin Sector' : $pers['SecDesc'];
$persGruDesc = ($pers['LegGrup'] == '0') ? 'Sin Grupo' : $pers['GruDesc'];
$persSucDesc = ($pers['LegSucu'] == '0') ? 'Sin Sucursal' : $pers['SucDesc'];
$persTareDesc = ($pers['LegTareProd'] == '0') ? 'Sin Tarea' : $pers['TareDesc'];
$persSe2Desc = ($pers['Se2Codi'] == '0') ? 'Sin Sección' : $pers['Se2Desc'];
$LegValHora = ($pers['LegValHora'] == '0') ? '000' : $pers['LegValHora'];



$CalcEdad = $persLegFeNa != '' ? 'onload="javascript:calcularEdad();"' : '';