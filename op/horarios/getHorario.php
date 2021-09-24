<?php
require __DIR__ . '../../../config/index.php';
ini_set('max_execution_time', 900); // 900 segundos 15 minutos
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
E_ALL();

if (($_SERVER["REQUEST_METHOD"] == "POST") && (array_key_exists('Legajo', $_POST))) {
    pingWebService('Horario no disponible');
    
    $_POST['Legajo']  = $_POST['Legajo'] ?? '';
    $Legajo = test_input($_POST['Legajo']);
    $Fecha  = hoyStr();

    $getHorario = getHorario($Fecha, $Fecha, $Legajo, $Legajo, '0', '0', '0', '0', '0', '0', '0');

    $data = array();
    if ($getHorario) {
        $explode = explode(',', $getHorario['Estado']);
        // PrintRespuestaJson('ok',json_encode($getHorario));exit;
        $legajo     = $explode[0];
        $fecha      = $explode[1];
        $desde      = $explode[2];
        $hasta      = $explode[3];
        $descanso   = $explode[4];
        $laboral    = $explode[5];
        $feriado    = $explode[6];
        $asignacion = $explode[7];
        $codigo     = $explode[8];

        $horario = horarioCH($codigo);

        $horariodesc = (intval($asignacion) == 0) ? '' : ' ' . $horario['desc'];
        $horariodesc = (intval($codigo) == 0) ? '' : ' ' . $horario['desc'];

        switch (intval($asignacion)) {
            case 0:
                $tipo = 'Sin Horario Asignado';
                break;
            case 3:
                $tipo = 'Desde Hasta por Legajo';
                break;
            case 9:
                $tipo = 'Desde por Legajo';
                break;
            case 6:
                $tipo = 'Rotación por Legajo';
                break;
            case 7:
                $tipo = 'Rotación por Sector';
                break;
            case 8:
                $tipo = 'Rotación por Grupo';
                break;
            case 1:
                $tipo = 'Citación';
                $horariodesc = '';
                break;
            case 10:
                $tipo = 'Desde por Sector';
                break;
            case 11:
                $tipo = 'Desde por Grupo';
                break;

            default:
                $tipo = '';
                break;
        }
        switch (intval($laboral)) {
            case 0:
                $diaLaboral = 'No';
                break;
            case 1:
                $diaLaboral = 'Sí';
                break;

            default:
                $diaLaboral = '';
                break;
        }
        switch (intval($feriado)) {
            case 0:
                $diaFeriado = 'No';
                break;
            case 1:
                $diaFeriado = 'Sí';
                break;

            default:
                $diaFeriado = '';
                break;
        }

        if (intval($laboral) == 1) {
            $vsHorario = $desde . ' a ' . $hasta;
        } else {
            if (intval($feriado) == 0) {
                $vsHorario = 'Franco';
            } else {
                $vsHorario = 'Feriado';
            }
        }
        $Mensaje = (($getHorario['Estado'])) ? $tipo . ' (' . $vsHorario . ')' . $horariodesc : 'No hay Conexión';

        $data = array(
            'legajo'     => intval($legajo),
            'fecha'      => $fecha,
            'desde'      => $desde,
            'hasta'      => $hasta,
            'descanso'   => $descanso,
            'laboral'    => intval($laboral),
            'diaLaboral' => ($diaLaboral),
            'feriado'    => intval($feriado),
            'diaFeriado' => ($diaFeriado),
            'asignacion' => intval($asignacion),
            'tipoAsign'  => ($tipo),
            'codigo'     => intval($codigo),
            'horario'    => $horario['desc'],
            'Mensaje'  => $Mensaje
        );
        echo json_encode($data);
        exit;
    }
}

