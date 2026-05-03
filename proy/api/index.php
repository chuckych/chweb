<?php
require '../../vendor/autoload.php';
require __DIR__ . '/../../config/index.php';
header("Content-Type: application/json");

use Carbon\Carbon;
$rsEnd = false;
$data = [];
$request = Flight::request();
// error_log(print_r($request, true));

$endPoints = ["/?redondear=1", "/?descanso=1"];

foreach ($endPoints as $v) {
    if ($v == $request->url) {
        $rsEnd = true;
    }
}

if (!$rsEnd) {
    $data = [
        'status' => 'error',
        'msg' => "Not Found: {$request->url}",
    ];
    Flight::jsonHalt($data, 404);
}

if ($request->method != 'POST') {
    $data = [
        'status' => 'error',
        'msg' => "Method Not Allowed: {$request->method}",
        'request' => $request,
    ];
    Flight::jsonHalt($data, 405);
}

if ($request->query->redondear == '1') {

    $fechahora = ($request->data->datetime);

    if (empty($fechahora)) {
        $data = [
            'status' => 'error',
            'msg' => 'datetime required',
        ];
        Flight::jsonHalt($data, 400);
    }

    foreach (getConfTar() as $v) {
        $confTar = [
            'ProcDescTar' => $v['ProcDescTar'],
            'ProcRedTar' => $v['ProcRedTar'],
            'MinimoDesc' => HoraMin($v['MinimoDesc']),
            'LimitTar' => intval($v['LimitTar']) * 60, // Limite de tiempo maximo en tareas
            'RecRedTar' => ($v['RecRedTar']), // Recorte y redondeo de tareas
        ];
    }

    if ($confTar['RecRedTar'] && $confTar['ProcRedTar']) {
        $RecRedTar = explode('/', $confTar['RecRedTar']);
        $limite = $RecRedTar[0];
        $redondear = $RecRedTar[1];
    } else {
        $limite = 0;
        $redondear = 0;
    }

    function redondear($fechahora, $redondearEn, $limite)
    {
        try {
            $date = new DateTime($fechahora);
            $year = $date->format("Y");
            $month = $date->format("m");
            $day = $date->format("d");
            $hour = $date->format("H");
            $min = $date->format("i");
            $date = Carbon::create($year, $month, $day, $hour, $min, 0);
            $nearestSec = $redondearEn * 60;
            $minimumMoment = $date->subMinutes((int)$limite);
            $futureTimestamp = ceil($minimumMoment->timestamp / $nearestSec) * $nearestSec;
            $futureMoment = Carbon::createFromTimestamp($futureTimestamp);
            $futureMoment->startOfMinute()->format("Y-m-d H:i");
            return ($futureMoment);
        } catch (exception $e) {
            $data = [
                'status' => 'error',
                'msg' => $e->getMessage(),
                "resultado" => '',
                "redondear" => $redondearEn,
                "limite" => $limite
            ];
            Flight::jsonHalt($data, 400);
        }
    }
    if ($confTar['RecRedTar'] && $confTar['ProcRedTar']) {
        $rs = (redondear(   $fechahora, $redondear, $limite));
        $rs = new DateTime($rs);
    } else {
        $d = new DateTime($fechahora);
        $rs = $d;
    }

    $data = [
        'status' => 'ok',
        'msg' => '',
        "resultado" => $rs,
        "redondear" => $redondear,
        "limite" => $limite
    ];
    Flight::json($data);
    exit;
}

if ($request->query->descanso == '1') {
    foreach (getConfTar() as $key => $v) {
        $confTar = array(
            'ProcDescTar' => $v['ProcDescTar'],
            'MinimoDesc' => HoraMin($v['MinimoDesc']),
            'LimitTar' => intval($v['LimitTar']) * 60, // Limite de tiempo maximo en tareas
        );
    }

    $start = ($request->data->start);
    $end = ($request->data->end);
    $user = ($request->data->user);

    try {
        $start = new DateTime($start);
        $start = $start->format("Y-m-d H:i");
    } catch (exception $e) {
        $data = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
        Flight::jsonHalt($data, 400);
    }
    try {
        $end = new DateTime($end);
        $end = $end->format("Y-m-d H:i");
    } catch (exception $e) {
        $data = [
            'status' => 'error',
            'msg' => $e->getMessage()
        ];
        Flight::jsonHalt($data, 400);
    }

    $confUser = [];
    $confGeneral = [];
    $confDesc = [];
    $calcDesc = [];
    $status = '';
    $msg = '';
    $diff_1 = 0;
    $diff_2 = 0;
    $diff_3 = 0;
    $diff_4 = 0;
    $diff_5 = 0;
    $diff_desc = 0;

    $startInt = intval(FechaFormatVar($start, 'YmdHi'));
    $startDateInt = intval(FechaFormatVar($start, 'Ymd'));
    $endInt = intval(FechaFormatVar($end, 'YmdHi'));
    $endDateInt = intval(FechaFormatVar($end, 'Ymd'));

    function horarioInt($hora)
    {
        if (!$hora) {
            return false;
        }
        $hora = explode(':', $hora);
        $hora = $hora[0] . $hora[1];
        return intval($hora);
    }
    function getConfDesc($user)
    {
        if (!$user) {
            return false;
        }
        $q = "SELECT TarDesUsr, TIME_FORMAT(TarDesIni, '%H:%i') AS TarDesIni, TIME_FORMAT(TarDesFin, '%H:%i') AS TarDesFin, TarDesEsta FROM proy_tareas_desc p WHERE p.TarDesUsr = $user  OR p.TarDesUsr IS NULL LIMIT 2";
        $q = array_pdoQuery($q);
        return $q;
    }
    if ($confTar['ProcDescTar'] == '1') {
        /** Recorremos la configuracion de descanso y sepramos dos arrays. 
         * Uno la configuracion del usuario y la otra la del sistema */
        foreach (getConfDesc($user) as $key => $h) {
            if ($h['TarDesUsr'] == NULL) {
                if ($h['TarDesEsta'] == '0') {
                    if ($h['TarDesIni'] != '00:00' || $h['TarDesFin'] != '00:00') {
                        $iniInt = intval($startDateInt . horarioInt($h['TarDesIni']));
                        $finInt = intval($endDateInt . horarioInt($h['TarDesFin']));
                        $confGeneral = array(
                            "User" => -1,
                            "IniInt" => ($h['TarDesIni'] == '00:00') ? intval($iniInt . "000") : $iniInt,
                            "Ini" => ($h['TarDesIni']),
                            "FinInt" => ($h['TarDesFin'] == '00:00') ? intval($finInt . "000") : $finInt,
                            "Fin" => ($h['TarDesFin']),
                            "Esta" => $h['TarDesEsta'],
                        );
                    }
                }
            }
            if ($h['TarDesUsr'] == $user) {
                if ($h['TarDesEsta'] == '0') {
                    if ($h['TarDesIni'] != '00:00' || $h['TarDesFin'] != '00:00') {
                        $iniInt = intval($startDateInt . horarioInt($h['TarDesIni']));
                        $finInt = intval($endDateInt . horarioInt($h['TarDesFin']));
                        $confUser = array(
                            "User" => intval($h['TarDesUsr']),
                            "IniInt" => ($h['TarDesIni'] == '00:00') ? intval($iniInt . "000") : $iniInt,
                            "Ini" => ($h['TarDesIni']),
                            "FinInt" => ($h['TarDesFin'] == '00:00') ? intval($finInt . "000") : $finInt,
                            "Fin" => ($h['TarDesFin']),
                            "Esta" => $h['TarDesEsta'],
                        );
                    }
                }
            }
        }
        $confDesc = ($confUser) ? $confUser : $confGeneral; // Definimos la configuracion de descanso asignada.
    }
    /** Si encontramos configuración de descanso 
     * Arrancamos con los calculos de horario de descanso
     */
    if ($confDesc) {
        $a = ($start); // Fecha Hora de Inicio en formato estandar 2022-12-06 09:00
        $a1 = ($startInt); // Fecha Hora de Inicio en formato int 202212060900
        $b = FechaFormatVar($confDesc['IniInt'], 'Y-m-d H:i'); // Fecha Hora de Inicio Descanso en formato estandar 2022-12-06 12:00
        $b1 = ($confDesc['IniInt']); // Fecha Hora de Inicio Descanso en formato int 202212061200
        $c = FechaFormatVar($confDesc['FinInt'], 'Y-m-d H:i'); // Fecha Hora de Fin en formato estandar 2022-12-06 13:00
        $c1 = ($confDesc['FinInt']); // Fecha Hora de Fin en formato int 202212061300
        $d = ($end); // Fecha Hora de Fin en formato estandar 2022-12-06 15:00
        $d1 = ($endInt); // Fecha Hora de Fin en formato int 202212061500

        /** 
         * a = horario de inicio de tarea 
         * 
         * b = horario de inicio de descanso ej. 12:00
         * c = horario de fin de descanso ej. 13:00
         * 
         * d = horario de fin de tarea 
         */

        /** particion 1 * ejemplo de 09:00 a 12:00 */
        $diff_1 = ($a1 < $b1 && $d1 <= $b1) ? diffStartEnd($a, $d) : 0;
        /** */

        /** particion 2 * ejemplo de 11:00 a 13:00 (antes del descanso y fin durante el descanso)*/
        if (intval($confTar['MinimoDesc']) > 0) {
            $diff_2 = ($a1 < $b1 && $d1 <= $c1 && $d1 > $b1) ? diffStartEnd($a, $b) : 0;
            $diff_desc2 = ($diff_2) ? diffStartEnd($b, $d) : 0; // calculo tiempo de descanso
            $diff_desc = (intval($diff_desc2['diffInMinutes'] ?? 0) <= intval($confTar['MinimoDesc'])) ? $diff_desc2 : 0; // comparo total desc. con delta de config.desc.
        } else {
            $diff_2 = ($a1 < $b1 && $d1 <= $c1 && $d1 > $b1) ? diffStartEnd($a, $d) : 0;
        }
        /** */
        /** particion 3 * (dentro del horario de descanso)*/
        $diff_3 = ($a1 >= $b1 && $d1 <= $c1) ? diffStartEnd($a, $d) : 0;
        (!$diff_3) ? '' : $status = 'error' . $msg = 'Horas calculadas dentro del intervalo de descanso';
        /** */
        /** particion 4 * ejemplo de 11:00 a 14:00 (antes y despues del descanso)*/
        $diff_4 = ($a1 < $b1 && $d1 > $c1) ? diffStartEnd($a, $d) : 0;
        $diff_desc_4 = ($diff_4) ? diffStartEnd($b, $c) : 0; // calculo tiempo de descanso
        if ($diff_desc_4['diffInMinutes'] ?? 0) {
            $diff_4['diffInMinutes'] = ($diff_4['diffInMinutes'] - $diff_desc_4['diffInMinutes']); // resto descanso del total 
            $diff_desc_4 = 0;
        }

        /** particion 5 * ejemplo de 12:30 a 18:00 (inicio dentro del descanso y fin fuera del descanso)*/
        $diff_5 = ($a1 >= $b1 && $d1 > $c1 && $a1 < $c1) ? diffStartEnd($c, $d) : 0;
        $diff_desc5 = ($diff_5) ? diffStartEnd($a, $c) : 0; // calculo tiempo de descanso
        $diff_desc_5 = (intval($diff_desc5['diffInMinutes'] ?? 0) <= intval($confTar['MinimoDesc'])) ? $diff_desc5 : 0; // comparo total desc. con delta de config.desc.
        /** */

        /** */
        /** particion 6 * ejemplo de 13:00 a 18:00 (Inicio y Fin despues del descanso */
        $diff_6 = ($a1 >= $c1) ? diffStartEnd($a, $d) : 0;
        /** */

        $arrValores = [
            $diff_1['diffInMinutes'] ?? 0,
            $diff_2['diffInMinutes'] ?? 0,
            $diff_3['diffInMinutes'] ?? 0,
            $diff_4['diffInMinutes'] ?? 0,
            $diff_5['diffInMinutes'] ?? 0,
            $diff_6['diffInMinutes'] ?? 0,
            $diff_desc['diffInMinutes'] ?? 0,
            $diff_desc_5['diffInMinutes'] ?? 0,
            $diff_desc_4['diffInMinutes'] ?? 0,
        ];

        // Flight::json($arrValores).exit;

        $totalMin = array_sum($arrValores);

        if (intval($confTar['LimitTar']) < $totalMin) {
            $status = 'Error';
            $msg = "El límite de tiempo para las tareas es de " . MinHora($confTar['LimitTar']) . " Hs. Y el tiempo calculado es " . MinHora($totalMin) . " Hs.";
        } else {
            $status = 'ok';
        }

        $calcDesc = [
            "part_1" => $diff_1['diffInMinutes'] ?? 0,
            "part_2" => $diff_2['diffInMinutes'] ?? 0,
            "part_3" => $diff_3['diffInMinutes'] ?? 0,
            "part_4" => $diff_4['diffInMinutes'] ?? 0,
            "part_5" => $diff_5['diffInMinutes'] ?? 0,
            "part_6" => $diff_6['diffInMinutes'] ?? 0,
            "part_desc" => $diff_desc['diffInMinutes'] ?? 0,
            "tipo" => '1', // en descanso
            "min" => $totalMin,
            "horas" => MinHora($totalMin)
        ];
    }
    /** Arrancamos con el calculo */
    $f = Carbon::parse($start); // Fecha hora de inicio
    $f2 = Carbon::parse($end); // Fecha hora de finalizacion
    $minutos = ($f2->diffInMinutes($f)); // Diferencia en minutos -> 360
    $total = MinHora($f2->diffInMinutes($f)); // Diferencia en formato Horas y minutos -> 06:00

    if (!$confDesc) {
        $calcDesc = [
            "tipo" => '0', // sin calculo de descanso
            "min" => $minutos,
            "horas" => MinHora($minutos)
        ];
    }

    $calculos = [
        "reales" => [
            'min' => $minutos,
            'horas' => $total,
        ],
        "calculadas" => $calcDesc
    ];

    $data = [
        'totales' => $calculos,
        'status' => 'ok',
        'msg' => $msg,
    ];

    Flight::json($data);
    exit;
}

Flight::start();