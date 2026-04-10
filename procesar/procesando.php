<?php
require __DIR__ . '/../config/index.php';
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();
// sleep(2);

if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['procesando'] === 'true')) {

    pingWebService('Error al procesar. Intentelo denuevo mas tarde');

    $tiempo_ini = microtime(true);
    if ($_SESSION['ABM_ROL']['Proc'] != 1) {
        PrintRespuestaJson('ok', 'No tiene permisos para procesar datos');
        exit;
    }

    $SelEmpresa = $_POST['SelEmpresa'] ?? '';
    $SelPlanta = $_POST['SelPlanta'] ?? '';
    $SelSector = $_POST['SelSector'] ?? '';
    $SelSeccion = $_POST['SelSeccion'] ?? '';
    $SelGrupo = $_POST['SelGrupo'] ?? '';
    $SelSucursal = $_POST['SelSucursal'] ?? '';

    $_POST['ProcLegaIni'] ??= '';
    $_POST['ProcLegaFin'] ??= '';

    $Tipo = $_POST['ProcTipo'] ?? '0';
    $LegaIni = $_POST['ProcLegaIni'] ?: '1';
    $LegaFin = $_POST['ProcLegaFin'] ?: '999999999';
    $FechaIni = $_POST['ProcFechaIni'] ?? date('d/m/Y');
    $FechaFin = $_POST['ProcFechaFin'] ?? date('d/m/Y');
    $Emp = $_POST['ProcEmp'] ?? '0';
    $Plan = $_POST['ProcPlan'] ?? '0';
    $Sect = $_POST['ProcSect'] ?? '0';
    $Sec2 = $_POST['ProcSec2'] ?? '0';
    $Grup = $_POST['ProcGrup'] ?? '0';
    $Sucur = $_POST['ProcSucur'] ?? '0';

    $_POST['_dr'] ??= '';
    $_POST['legajo'] ??= '';

    if ($_POST['_dr']) {
        $DateRange = explode(' al ', $_POST['_dr']);
        $FechaIni = test_input(dr_fecha($DateRange[0]));
        $FechaFin = test_input(dr_fecha($DateRange[1]));
    }

    $_POST['CheckLegajos'] ??= '';
    $_POST['procesaLegajo'] ??= false;

    $CheckLegajos = test_input($_POST['CheckLegajos']);

    if ((valida_campo($LegaIni)) || (valida_campo($LegaFin)) || (valida_campo($FechaIni)) || (valida_campo($FechaFin))) {
        $data = ['status' => 'error', 'Mensaje' => 'Campos requeridos!'];
        echo json_encode($data);
        exit;
    }

    if (!is_numeric($LegaIni) || (!is_numeric($LegaFin))) {
        $data = ['status' => 'error', 'Mensaje' => 'Campos de Legajo deben ser Números'];
        echo json_encode($data);
        exit;
    }

    if ((int) $LegaIni > (int) $LegaFin) {
        $data = ['status' => 'error', 'Mensaje' => 'Rango de Legajos Incorrecto.'];
        echo json_encode($data);
        exit;
    }

    if ((dr_fecha($FechaIni) > date('Ymd'))) {
        $data = ['status' => 'error', 'Mensaje' => 'Fecha superior a la Actual.'];
        echo json_encode($data);
        exit;
    }
    if ((dr_fecha($FechaFin) > date('Ymd'))) {
        $FechaFin = date('d/m/Y');
    }

    if ($CheckLegajos) {
        $procesando = Procesar($FechaIni, $FechaFin, $LegaIni, $LegaFin, $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2);
    } else {
        if ($_POST['legajo']) {
            $legajos = $_POST['legajo'];
            $arrlega = implode(';', $legajos);
            $arrlega = "[{$arrlega}]";
            $procesando = Procesar($FechaIni, $FechaFin, $LegaIni, "$LegaFin,Legajos=$arrlega", $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2);
        } else {
            $procesando = Procesar($FechaIni, $FechaFin, $LegaIni, $LegaFin, $Tipo, $Emp, $Plan, $Sucur, $Grup, $Sect, $Sec2);
        }
    }

    $arrEstruct = [
        'Empresa' => $SelEmpresa,
        'Planta' => $SelPlanta,
        'Sector' => $SelSector,
        'Seccion' => $SelSeccion,
        'Grupo' => $SelGrupo,
        'Sucursal' => $SelSucursal,
    ];

    foreach ($arrEstruct as $key => $value) {
        $datas[] = ($value) ? "<br />$key: $value" : '';
    }
    $datas = implode("", $datas);

    function dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin)
    {
        $textoLegajo = ($LegaIni == $LegaFin) ? 'Legajo: ' . $LegaIni : '';

        $textoFecha = (FechaString($FechaIni) == FechaString($FechaFin)) ? '. Fecha: ' . Fech_Format_Var($FechaIni, 'd/m/Y') : '. Desde: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' hasta ' . Fech_Format_Var($FechaFin, 'd/m/Y');

        $Dato = "\tProceso de Datos $textoLegajo$textoFecha";
        return $Dato;
    }
    $error = $procesando['error'] ?? false;

    if ($error) {
        $tiempo_fini = microtime(true);
        $duracion = round($tiempo_fini - $tiempo_ini, 2);
        $textDuracion = '<br>Duración: ' . $duracion . 's.';
        $data = ['status' => 'error', 'Mensaje' => (string) $error, 'EstadoProceso' => ($procesando['EstadoProceso'] ?? ''), 'ProcesoId' => ($procesando['ProcesoId'] ?? ''), 'Duracion' => $duracion];
        echo json_encode($data);
        exit;
    }

    if ((($procesando['EstadoProceso'] ?? '')) === 'Terminado') {
        $tiempo_fini = microtime(true);
        $duracion = (round($tiempo_fini - $tiempo_ini, 2));
        
        $textDuracion = "<br>Duración: {$duracion}s.";

        if ($_POST['procesaLegajo']) {
            // sleep(1);
            $data = ['status' => 'ok', 'Mensaje' => "Proceso enviado correctamente!<br>Legajo: ($LegaIni) " . $_POST['nombreLegajo'] . ' <br/>Fecha: <b><span class="ls1">' . Fech_Format_Var($FechaIni, 'd/m/Y') . '</span></b>' . $textDuracion, 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion' => $duracion];
            /** Insertar en tabla Auditor */
            $Dato = dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin);
            audito_ch('P', $Dato, '12');
            /** */
        } else {
            $textoLegajo = ($LegaIni == $LegaFin) ? 'Legajo: ' . $LegaIni . '' : 'Legajos: ' . $LegaIni . ' a ' . $LegaFin . '';
            $textoFecha = (FechaString($FechaIni) == FechaString($FechaFin)) ? 'Fecha: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . '' : 'Desde <b><span class="ls1">' . Fech_Format_Var($FechaIni, 'd/m/Y') . '</span></b> hasta <b><span class="ls1">' . Fech_Format_Var($FechaFin, 'd/m/Y' . '</span></b>');
            $data = ['status' => 'ok', 'Mensaje' => "Proceso enviado correctamente!<br>$textoLegajo<br/>$textoFecha$datas$textDuracion", 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion' => $duracion];
            /** Insertar en tabla Auditor */
            $Dato = dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin);
            audito_ch('P', $Dato, '12');
            /** */
        }

        echo json_encode($data);
        exit;
    } else {
        $tiempo_fini = microtime(true);
        $duracion = round($tiempo_fini - $tiempo_ini, 2);
        $textDuracion = '<br>Duración: ' . $duracion . 's.';
        $data = ['status' => 'error', 'Mensaje' => "Error$textDuracion", 'EstadoProceso' => ($procesando['EstadoProceso'] ?? ''), 'ProcesoId' => ($procesando['ProcesoId'] ?? ''), 'Duracion' => $duracion];
        echo json_encode($data);
        exit;
    }
    ;
} else {
    $data = ['status' => 'error', 'Mensaje' => 'Error!'];
    echo json_encode($data);
    exit;
}
