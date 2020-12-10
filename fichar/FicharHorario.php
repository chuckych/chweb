<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '0');

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['FicharHorario'] == 'true')) {

    if ((valida_campo($_POST['FichLegaIni'])) || (valida_campo($_POST['FichLegaFin'])) || (valida_campo($_POST['FichFechaIni'])) || (valida_campo($_POST['FichFechaFin']))) {
        $data = array('status' => 'error', 'dato' => 'Campos requeridos!');
        echo json_encode($data);
        exit;
    };
    if (!ValNumerico($_POST['FichLegaIni']) || (!ValNumerico($_POST['FichLegaFin']))) {
        $data = array('status' => 'error', 'dato' => 'Campos de Legajo deben ser Números');
        echo json_encode($data);
        exit;
    };

    $SelEmpresa  = FusNuloPOST('SelEmpresa','');
    $SelPlanta   = FusNuloPOST('SelPlanta','');
    $SelSector   = FusNuloPOST('SelSector','');
    $SelSeccion  = FusNuloPOST('SelSeccion','');
    $SelGrupo    = FusNuloPOST('SelGrupo','');
    $SelSucursal = FusNuloPOST('SelSucursal','');

    $Tipo     = FusNuloPOST('FichTipo','0');
    $LegaIni  = FusNuloPOST('FichLegaIni','1');
    $LegaFin  = FusNuloPOST('FichLegaFin','999999999');
    $FechaIni = FusNuloPOST('FichFechaIni',date('d/m/Y'));
    $FechaFin = FusNuloPOST('FichFechaFin',date('d/m/Y'));
    $Emp      = FusNuloPOST('FichEmp','0');
    $Plan     = FusNuloPOST('FichPlan','0');
    $Sect     = FusNuloPOST('FichSect','0');
    $Sec2     = FusNuloPOST('FichSec2','0');
    $Grup     = FusNuloPOST('FichGrup','0');
    $Sucur    = FusNuloPOST('FichSucur','0');
    $Ingresar = FusNuloPOST('FichIngresar','4');
    $Laboral  = FusNuloPOST('FichLaboral','1');

    if ((($LegaIni) > ($LegaFin))) {
        $data = array('status' => 'error', 'dato' => 'Rango de Legajos Incorrecto.</span>');
        echo json_encode($data);
        exit;
    };
    if ((FechaString($FechaIni) > FechaString($FechaFin))) {
        $data = array('status' => 'error', 'dato' => 'Rango de Fecha Incorrecto.</span>');
        echo json_encode($data);
        exit;
    };

    $FicharHorario=FicharHorario($FechaIni, $FechaFin, $LegaIni, $LegaFin, $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2, $Ingresar, $Laboral);

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
        $Dato = 'Ingreso de Fichadas. '. $textoLegajo. $textoFecha;
        return $Dato;
    }

    if(($FicharHorario)=='Terminado'){
        $data = array('status' => 'ok', 'dato' => '<b>Ingreso de Fichadas enviado correctamente!</b> <br>De Legajos <b>'.$_POST['FichLegaIni'].'</b> a <b>'.$_POST['FichLegaFin'].'</b><br/>Desde: <b>'.Fech_Format_Var($_POST['FichFechaIni'],'d/m/Y').'</b> hasta: <b>'.Fech_Format_Var($_POST['FichFechaFin'],'d/m/Y').'</b>'.$datas);
        /** Insertar en tabla Auditor */
        $Dato=dato_proceso($LegaIni,$LegaFin,$FechaIni,$FechaFin);
        audito_ch('P', $Dato);
        /** */
        echo json_encode($data);
        exit;
    }else{
        $data = array('status' => 'error', 'dato' => 'Error');
        echo json_encode($data);
        exit;
    };
} else {
    $data = array('status' => 'error', 'dato' => 'Error!');
    echo json_encode($data);
    exit;
}
