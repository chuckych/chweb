<?php
require __DIR__ . '/config/index.php';
session_start();
header("Content-Type: application/json");
ultimoacc();
$_POST['FechaIni'] = $_POST['FechaIni'] ?? '';
$_POST['FechaFin'] = $_POST['FechaFin'] ?? '';
$_POST['LegaIni']  = $_POST['LegaIni'] ?? '';
$_POST['LegaFin']  = $_POST['LegaFin'] ?? '';

$FechaIni = test_input($_POST['FechaIni']);
$FechaFin = test_input($_POST['FechaFin']);
$LegaIni  = test_input($_POST['LegaIni']);
$LegaFin  = test_input($_POST['LegaFin']);

if (!secure_auth_ch_json()) {

    if (valida_campo($LegaIni)) {
        PrintRespuestaJson('error', 'Falta LegaIni');
        exit;
    };
    if (valida_campo($LegaFin)) {
        PrintRespuestaJson('error', 'Falta LegaFin');
        exit;
    };
    if (valida_campo($LegaFin)) {
        PrintRespuestaJson('error', 'Falta LegaFin');
        exit;
    };
    if (valida_campo($FechaIni)) {
        PrintRespuestaJson('error', 'Falta FechaIni');
        exit;
    };
    
    $procesando = Procesar($FechaIni, $FechaFin, $LegaIni, $LegaFin, '0', '0', '0', '0', '0', '0', '0');

    if (($procesando['EstadoProceso']) == 'Terminado') {
        $tiempo_fini = microtime(true);
        $duracion    = (round($tiempo_fini - $tiempo_ini, 2));
        $textDuracion = '<br>Duración: ' . $duracion . 's.';
        if ($_POST['procesaLegajo']) {
            // sleep(1);
            $data = array('status' => 'ok', 'Mensaje' => 'Proceso enviado correctamente!<br>Legajo: (' . $LegaIni . ') ' . $_POST['nombreLegajo'] . ' <br/>Fecha: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . $textDuracion, 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion' => $duracion);
            /** Insertar en tabla Auditor */
            $Dato = dato_proceso($LegaIni, $LegaFin, $FechaIni, $FechaFin);
            audito_ch('P', $Dato);
            /** */
        } else {
            $textoLegajo = ($LegaIni == $LegaFin) ? 'Legajo: ' . $LegaIni . '' : 'Legajos: ' . $LegaIni . ' a ' . $LegaFin . '';
            $textoFecha = (FechaString($FechaIni) == FechaString($FechaFin)) ? 'Fecha: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . '' : 'Desde ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' hasta ' . Fech_Format_Var($FechaFin, 'd/m/Y' . '');
            $data = array('status' => 'ok', 'Mensaje' => 'Proceso enviado correctamente!<br>' . $textoLegajo . '<br/>' . $textoFecha . $datas . $textDuracion, 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion' => $duracion);
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
        $textDuracion = '<br>Duración: ' . $duracion . 's.';
        $data = array('status' => 'error', 'Mensaje' => 'Error' . $textDuracion, 'EstadoProceso' => $procesando['EstadoProceso'], 'ProcesoId' => $procesando['ProcesoId'], 'Duracion' => $duracio);
        echo json_encode($data);
        exit;
    };
}
E_ALL();
exit;
