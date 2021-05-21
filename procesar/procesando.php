<?php
require __DIR__ . '../../config/index.php';
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();
// sleep(2);

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['procesando'] == 'true')) {
    $tiempo_ini = microtime(true);
    if ($_SESSION['ABM_ROL']['Proc'] != 1) {
        PrintRespuestaJson('ok','No tiene permisos para procesar datos'); exit;
    }

    $SelEmpresa  = FusNuloPOST('SelEmpresa', '');
    $SelPlanta   = FusNuloPOST('SelPlanta', '');
    $SelSector   = FusNuloPOST('SelSector', '');
    $SelSeccion  = FusNuloPOST('SelSeccion', '');
    $SelGrupo    = FusNuloPOST('SelGrupo', '');
    $SelSucursal = FusNuloPOST('SelSucursal', '');

    $Tipo     = FusNuloPOST('ProcTipo', '0');
    $LegaIni  = FusNuloPOST('ProcLegaIni', '1');
    $LegaFin  = FusNuloPOST('ProcLegaFin', '999999999');
    $FechaIni = FusNuloPOST('ProcFechaIni', date('d/m/Y'));
    $FechaFin = FusNuloPOST('ProcFechaFin', date('d/m/Y'));
    $Emp      = FusNuloPOST('ProcEmp', '0');
    $Plan     = FusNuloPOST('ProcPlan', '0');
    $Sect     = FusNuloPOST('ProcSect', '0');
    $Sec2     = FusNuloPOST('ProcSec2', '0');
    $Grup     = FusNuloPOST('ProcGrup', '0');
    $Sucur    = FusNuloPOST('ProcSucur', '0');

    $_POST['_dr'] = $_POST['_dr'] ?? '';
    $_POST['legajo'] = $_POST['legajo'] ?? '';

    if ($_POST['_dr']) {
        $DateRange = explode(' al ', $_POST['_dr']);
        $FechaIni  = test_input(dr_fecha($DateRange[0]));
        $FechaFin  = test_input(dr_fecha($DateRange[1]));
    }

    FusNuloPOST('procesaLegajo', false);
    FusNuloPOST('CheckLegajos', '');
    $CheckLegajos = test_input($_POST['CheckLegajos']);

    if ((valida_campo($_POST['ProcLegaIni'])) || (valida_campo($_POST['ProcLegaFin'])) || (valida_campo($FechaIni)) || (valida_campo($FechaIni))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos requeridos!');
        echo json_encode($data);
        exit;
    };

    if (!ValNumerico($_POST['ProcLegaIni']) || (!ValNumerico($_POST['ProcLegaFin']))) {
        $data = array('status' => 'error', 'Mensaje' => 'Campos de Legajo deben ser Números');
        echo json_encode($data);
        exit;
    };

    if ((($LegaIni) > ($LegaFin))) {
        $data = array('status' => 'error', 'Mensaje' => 'Rango de Legajos Incorrecto.');
        echo json_encode($data);
        exit;
    };
    if ((FechaString($FechaIni) > date('Ymd'))) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha superior a la Actual.');
        echo json_encode($data);
        exit;
    };
    if ((FechaString($FechaFin) > date('Ymd'))) {
        $data = array('status' => 'error', 'Mensaje' => 'Fecha superior a la Actual.');
        echo json_encode($data);
        exit;
    };

    if ($CheckLegajos) {
        $procesando = Procesar($FechaIni, $FechaFin, $LegaIni, $LegaFin, $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2);
    } else {
        if ($_POST['legajo']) {
            $legajos = $_POST['legajo'];
            $arrlega = implode(';', $legajos);
            $arrlega = '[' . $arrlega . ']';
            $procesando = Procesar($FechaIni, $FechaFin, $LegaIni, $LegaFin . ',Legajos=' . $arrlega, $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2);
        }else {
            $procesando = Procesar($FechaIni, $FechaFin, $LegaIni, $LegaFin, $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2);
        }
    }

    $arrEstruct = array(
        'Empresa'  => $SelEmpresa,
        'Planta'   => $SelPlanta,
        'Sector'   => $SelSector,
        'Seccion'  => $SelSeccion,
        'Grupo'    => $SelGrupo,
        'Sucursal' => $SelSucursal,
    );

    foreach ($arrEstruct as $key => $value) {
        $datas[] = ($value) ? '<br />' . $key . ': ' . $value . '' : '';
    }
    $datas = implode("", $datas);

    function dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin)
    {
        $textoLegajo = ($LegaIni == $LegaFin) ? 'Legajo: ' . $LegaIni : '';

        $textoFecha = (FechaString($FechaIni) == FechaString($FechaFin)) ? '. Fecha: ' . Fech_Format_Var($FechaIni, 'd/m/Y') : '. Desde: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' hasta ' . Fech_Format_Var($FechaFin, 'd/m/Y');

        $Dato = '	Proceso de Datos ' . $textoLegajo . $textoFecha;
        return $Dato;
    }

    // echo json_encode($procesando['EstadoProceso']);
    // exit;

    if (($procesando['EstadoProceso']) == 'Terminado') {
        $tiempo_fini = microtime(true);
        $duracion    = (round($tiempo_fini - $tiempo_ini, 2));
        $textDuracion='<br>Duración: '.$duracion.'s.'; 
        if ($_POST['procesaLegajo']) {
            // sleep(1);
            $data = array('status' => 'ok', 'Mensaje' => 'Proceso enviado correctamente!<br>Legajo: (' . $LegaIni . ') ' . $_POST['nombreLegajo'] . ' <br/>Fecha: ' . Fech_Format_Var($FechaIni, 'd/m/Y').$textDuracion, 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion'=> $duracion);
            /** Insertar en tabla Auditor */
            $Dato = dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin);
            audito_ch('P', $Dato);
            /** */
        } else {
            $textoLegajo = ($LegaIni == $LegaFin) ? 'Legajo: ' . $LegaIni . '' : 'Legajos: ' . $LegaIni . ' a ' . $LegaFin . '';
            $textoFecha = (FechaString($FechaIni) == FechaString($FechaFin)) ? 'Fecha: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . '' : 'Desde ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' hasta ' . Fech_Format_Var($FechaFin, 'd/m/Y' . '');
            $data = array('status' => 'ok', 'Mensaje' => 'Proceso enviado correctamente!<br>' . $textoLegajo . '<br/>' . $textoFecha . $datas.$textDuracion, 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion'=> $duracion);
            /** Insertar en tabla Auditor */
            $Dato = dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin);
            audito_ch('P', $Dato);
            /** */
        }

        echo json_encode($data);
        exit;
    } else {
        $tiempo_fini = microtime(true);
        $duracion    = round($tiempo_fini - $tiempo_ini, 2);
        $textDuracion='<br>Duración: '.$duracion.'s.'; 
        $data = array('status' => 'error', 'Mensaje' => 'Error'.$textDuracion, 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion'=> $duracio);
        echo json_encode($data);
        exit;
    };
} else {
    $data = array('status' => 'error', 'Mensaje' => 'Error!');
    echo json_encode($data);
    exit;
}
