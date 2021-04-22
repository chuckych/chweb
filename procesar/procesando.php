<?php
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
E_ALL();

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['procesando'] == 'true')) { 

    $SelEmpresa  = FusNuloPOST('SelEmpresa','');
    $SelPlanta   = FusNuloPOST('SelPlanta','');
    $SelSector   = FusNuloPOST('SelSector','');
    $SelSeccion  = FusNuloPOST('SelSeccion','');
    $SelGrupo    = FusNuloPOST('SelGrupo','');
    $SelSucursal = FusNuloPOST('SelSucursal','');

    $Tipo     = FusNuloPOST('ProcTipo','0');
    $LegaIni  = FusNuloPOST('ProcLegaIni','1');
    $LegaFin  = FusNuloPOST('ProcLegaFin','999999999');
    $FechaIni = FusNuloPOST('ProcFechaIni',date('d/m/Y'));
    $FechaFin = FusNuloPOST('ProcFechaFin',date('d/m/Y'));
    $Emp      = FusNuloPOST('ProcEmp','0');
    $Plan     = FusNuloPOST('ProcPlan','0');
    $Sect     = FusNuloPOST('ProcSect','0');
    $Sec2     = FusNuloPOST('ProcSec2','0');
    $Grup     = FusNuloPOST('ProcGrup','0');
    $Sucur    = FusNuloPOST('ProcSucur','0');

    FusNuloPOST('procesaLegajo',false);

    if ((valida_campo($_POST['ProcLegaIni'])) || (valida_campo($_POST['ProcLegaFin'])) || (valida_campo($_POST['ProcFechaIni'])) || (valida_campo($_POST['ProcFechaFin']))) {
        $data = array('status' => 'error', 'dato' => 'Campos requeridos!');
        echo json_encode($data);
        exit;
    };
    if (!ValNumerico($_POST['ProcLegaIni']) || (!ValNumerico($_POST['ProcLegaFin']))) {
        $data = array('status' => 'error', 'dato' => 'Campos de Legajo deben ser Números');
        echo json_encode($data);
        exit;
    };
    
    if ((($LegaIni) > ($LegaFin))) {
        $data = array('status' => 'error', 'dato' => 'Rango de Legajos Incorrecto.');
        echo json_encode($data);
        exit;
    };
    if ((FechaString($FechaIni) > date('Ymd'))) {
        $data = array('status' => 'error', 'dato' => 'Fecha superior a la Actual.');
        echo json_encode($data);
        exit;
    };
    if ((FechaString($FechaFin) > date('Ymd'))) {
        $data = array('status' => 'error', 'dato' => 'Fecha superior a la Actual.');
        echo json_encode($data);
        exit;
    };


    $procesando=Procesar($FechaIni, $FechaFin, $LegaIni, $LegaFin, $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2);

    $arrEstruct = array(
        'Empresa'  => $SelEmpresa,
        'Planta'   => $SelPlanta,
        'Sector'   => $SelSector,
        'Seccion'  => $SelSeccion,
        'Grupo'    => $SelGrupo,
        'Sucursal' => $SelSucursal,
    );

    foreach ($arrEstruct as $key => $value) {
        $datas[] = ($value) ? '<br />'.$key.': <b>'.$value.'</b>' : '';
    }
    $datas = implode("",$datas);

    function dato_proceso($LegaIni,$LegaFin,$FechaIni,$FechaFin){
        $textoLegajo= ($LegaIni == $LegaFin) ? 'Legajo: '.$LegaIni : 'Legajos: '.$LegaIni.' a '.$LegaFin;
        $textoFecha= (FechaString($FechaIni) == FechaString($FechaFin)) ? '. Fecha: '.Fech_Format_Var($FechaIni,'d/m/Y') : '. Desde: '.Fech_Format_Var($FechaIni,'d/m/Y').' hasta '.Fech_Format_Var($FechaFin,'d/m/Y');
        $Dato = 'Proceso. '. $textoLegajo. $textoFecha;
        return $Dato;
    }

    // echo json_encode($procesando['EstadoProceso']);
    // exit;

    if(($procesando['EstadoProceso'])=='Terminado'){
        if ($_POST['procesaLegajo']) {
            // sleep(1);
            $data = array('status' => 'ok', 'dato' => 'Proceso enviado correctamente!<br>Legajo: ('.$LegaIni.') '.$_POST['nombreLegajo'].' <br/>Fecha: '.Fech_Format_Var($FechaIni,'d/m/Y'), 'EstadoProceso'=>$procesando['EstadoProceso'], 'ProcesoId'=> $procesando['ProcesoId']);
            /** Insertar en tabla Auditor */
            $Dato=dato_proceso($LegaIni,$LegaFin,$FechaIni,$FechaFin);
            audito_ch('P', $Dato);
            /** */
        }else{

        $textoLegajo = ($LegaIni == $LegaFin) ? 'Legajo: <b>'.$LegaIni.'</b>' : 'Legajos: <b>'.$LegaIni.'</b> a <b>'.$LegaFin.'</b>';
        $textoFecha = (FechaString($FechaIni) == FechaString($FechaFin)) ? 'Fecha: <b>'.Fech_Format_Var($FechaIni,'d/m/Y').'</b>' : 'Desde<b> '.Fech_Format_Var($FechaIni,'d/m/Y').'</b> hasta <b>'.Fech_Format_Var($FechaFin,'d/m/Y'.'</b>');
        $data = array('status' => 'ok', 'dato' => '<b>Proceso enviado correctamente!</b> <br>'.$textoLegajo.'<br/>'.$textoFecha.$datas, 'EstadoProceso'=>$procesando['EstadoProceso'], 'ProcesoId'=> $procesando['ProcesoId']); 
        /** Insertar en tabla Auditor */
        $Dato=dato_proceso($LegaIni,$LegaFin,$FechaIni,$FechaFin);
         audito_ch('P', $Dato);
         /** */
        }
        
        echo json_encode($data);
        exit;
    }else{
        $data = array('status' => 'error', 'dato' => 'Error', 'EstadoProceso'=>$procesando['EstadoProceso'], 'ProcesoId'=> $procesando['ProcesoId']);
        echo json_encode($data);
        exit;
    };
} else {
    $data = array('status' => 'error', 'dato' => 'Error!');
    echo json_encode($data);
    exit;
}
