<?php
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';

use Carbon\Carbon;

session_start();
header("Content-Type: application/json");
ultimoacc();
E_ALL();
timeZone();
// sleep(1);

(!$_SERVER['REQUEST_METHOD'] == 'POST') ? PrintRespuestaJson('error', 'Invalid Request Method') . exit : '';
// $qtar = '';
require __DIR__ . '/../data/wcGetTar.php'; //  require where_conditions y variables
// $qtar = "SET sql_mode =;";
$qTar = "SELECT `proy_tareas`.`TareID`, `proy_empresas`.`EmpDesc`, `proy_tareas`.`TareEmp`, `proy_tareas`.`TareProy`, `proy_proyectos`.`ProyDesc`, `proy_proyectos`.`ProyNom`, `proy_proyectos`.`ProyPlant`, `proy_tareas`.`TareResp`, `resp`.`nombre`, `resp`.`legajo`, `proy_tareas`.`TareProc`, `proy_tareas`.`TareCost`, `proy_proceso`.`ProcDesc`, `proy_tareas`.`TarePlano`, `proy_planos`.`PlanoDesc`, `proy_tareas`.`TareIni`, `proy_tareas`.`TareFin`, `proy_tareas`.`TareFinTipo`, `proy_tareas`.`TareEsta`, `proy_tareas`.`Cliente`, `proy_tare_horas`.`TareHorMin`, `proy_tare_horas`.`TareHorCost`, `proy_tare_horas`.`TareHorHoras` FROM `proy_tareas` 
LEFT JOIN `proy_tare_horas` ON `proy_tareas`.`TareID` = `proy_tare_horas`.`TareHorID` 
INNER JOIN `proy_empresas` ON `proy_tareas`.`TareEmp`=`proy_empresas`.`EmpID` 
INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID` 
INNER JOIN `usuarios`AS `resp` ON `proy_tareas`.`TareResp`=`resp`.`id` 
INNER JOIN `proy_proceso` ON `proy_tareas`.`TareProc`=`proy_proceso`.`ProcID` 
LEFT JOIN `proy_planos` ON `proy_tareas`.`TarePlano`=`proy_planos`.`PlanoID` 
INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID` 
WHERE `proy_tareas`.`TareID` > 0";


$qCount = "SELECT COUNT(*) as 'count' FROM `proy_tareas` LEFT JOIN `proy_tare_horas` ON `proy_tareas`.`TareID` = `proy_tare_horas`.`TareHorID` INNER JOIN `proy_empresas` ON `proy_tareas`.`TareEmp`=`proy_empresas`.`EmpID` INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID` INNER JOIN `usuarios` ON `proy_tareas`.`TareResp`=`usuarios`.`id` INNER JOIN `proy_proceso` ON `proy_tareas`.`TareProc`=`proy_proceso`.`ProcID` LEFT JOIN `proy_planos` ON `proy_tareas`.`TarePlano`=`proy_planos`.`PlanoID` INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID` WHERE `proy_tareas`.`TareID` > 0";

if ($w_c ?? ''): // if where_conditions
    $qTar .= $w_c;
    $qCount .= $w_c;
endif;

if (!$params['tarTotales']) {
    $qTar .= " ORDER BY `proy_tareas`.`TareID` DESC LIMIT " . $params['start'] . "," . $params['length'] . " ";
    $totalRecords = simple_pdoQuery($qCount);
    $count = $totalRecords['count'];
    $r = array_pdoQuery($qTar);
    // print_r($qTar).exit;
    // $pathLog = "qTar_" . date('Ymd') . ".log";
    // fileLog($qTar, $pathLog); 

    foreach ($r as $key => $row) {

        $Cliente = $row['Cliente'];
        $EmpDesc = $row['EmpDesc'];
        $PlanoDesc = $row['PlanoDesc'];
        $ProcDesc = $row['ProcDesc'];
        $ProyDesc = $row['ProyDesc'];
        $ProyNom = $row['ProyNom'];
        $ProyPlant = $row['ProyPlant'];
        $TareFin = $row['TareFin'];
        $TareFinTipo = $row['TareFinTipo'];
        $TareID = $row['TareID'];
        $TareIni = $row['TareIni'];
        $TarePlano = ($row['TarePlano'] == null) ? '' : $row['TarePlano'];
        $TareProc = $row['TareProc'];
        $TareProy = $row['TareProy'];
        $TareResp = $row['TareResp'];
        $TareCost = $row['TareCost'];
        $nombre = $row['nombre'];
        $legajo = $row['legajo'];
        $TareEmp = $row['TareEmp'];
        $TareEsta = $row['TareEsta'];
        $TareHorMin = $row['TareHorMin'];
        $TareHorCost = $row['TareHorCost'];
        $TareHorHoras = $row['TareHorHoras'];

        if ($TareHorHoras) {
            $t = explode(':', $TareHorHoras);
            $TareHorHoras = $t[0] . ':' . $t[1];
        }

        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.UTF-8');

        $f = Carbon::parse($TareIni);
        $f2 = Carbon::parse($TareFin);
        $diffForHumans = $f->diffForHumans($f);
        $d1 = $f->diffForHumans(null, false, false, 1);
        $d2 = $f->diffForHumans(null, false, false, 2);
        $d3 = $f->diffForHumans(null, false, false, 3);
        $now = Carbon::now();
        $d4 = $f->diffInMinutes($now);
        $Fecha = $f->format('d/m/Y');
        $dia = $f->format('l');
        $daysSpanish = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
        $dia = $daysSpanish[$f->dayOfWeek];
        $diaf = ($TareFin == '0000-00-00 00:00:00') ? '' : $f2->format('l');
        $Hora = $f->format('H:i');
        $Fecha2 = ($TareFin == '0000-00-00 00:00:00') ? '' : $f2->format('d/m/Y');
        $Hora2 = $f2->format('H:i');

        $totalDuration = $f2->diffInSeconds($f);
        $totalDuration = gmdate("H:i:s", $totalDuration);
        $totalDuration = ($TareFin == '0000-00-00 00:00:00') ? '' : $totalDuration;

        $estado = ($totalDuration) ? "Completada" : "Pendiente";
        $estado = ($TareEsta == '1') ? "Anulada" : $estado;

        setlocale(LC_MONETARY, 'en_US');
        $money_format = "$TareHorCost";

        $data[] = array(
            'cuenta' => $Cliente,
            'TareID' => intval($TareID),
            'TareProy' => intval($TareProy),
            'estado' => $estado,
            'fechas' => array(
                "TareIni" => $TareIni,
                "TareFin" => $TareFin,
                "inicio" => $Fecha,
                "inicioHora" => $Hora,
                "inicioDia" => $dia,
                "fin" => $Fecha2,
                "finHora" => $Hora2,
                "finTipo" => $TareFinTipo,
                "finDia" => $diaf,
                "duracion" => $totalDuration,
                "duracionHuman" => trim(str_replace('antes', '', $f->diffForHumans($f2, false, false, 2))),
                "duracionMin" => ($totalDuration) ? $f->diffInMinutes($f2) : '',
                "duracionHoras" => ($totalDuration) ? MinHora($f->diffInMinutes($f2)) : '',
                "diffHuman" => trim(str_replace('hace', '', $d2)),
                "diff" => $f->diffInMinutes($now),
                "diffHoras" => MinHora($f->diffInMinutes($now)),
            ),
            'proyecto' => array(
                "nombre" => $ProyNom,
                "descripcion" => $ProyDesc,
                "ID" => intval($TareProy),
                "plantilla" => $ProyPlant
            ),
            'empresa' => array(
                "nombre" => $EmpDesc,
                "ID" => intval($TareEmp)
            ),
            'plano' => array(
                "nombre" => (empty($PlanoDesc) ? '-' : $PlanoDesc),
                "ID" => intval($TarePlano)
            ),
            'proceso' => array(
                "nombre" => $ProcDesc,
                "ID" => intval($TareProc),
                "costo" => floatval($TareCost),
            ),
            'responsable' => array(
                "nombre" => $nombre,
                "ID" => intval($TareResp),
                "legajo" => intval($legajo)
            ),
            'totales' => array(
                "min" => intval($TareHorMin),
                "cost" => floatval($TareHorCost),
                "costFormat" => ($money_format),
                "horas" => ($TareHorHoras)
            ),
        );
    }

    // function groupAssoc($input, $sortkey)
    // {
    //     foreach ($input as $val) $output[$val[$sortkey]][] = $val;
    //     return $output;
    // }

    // $myArray = groupAssoc($data, 'TareProy');

    // foreach ($myArray as $key => $value) {
    //     $data_group[] = array(
    //         'proy' => strtoupper(utf8str($key)),
    //         'data' => $value,
    //         'totaTar' => count($value)
    //     );
    // }
    // $json_data = array(
    //     "draw"            => intval($params['draw']),
    //     "recordsTotal"    => intval($count),
    //     "recordsFiltered" => intval($count),
    //     "data"            => $data_group,
    //     // "tiempo"          => round($tiempo, 2)
    // );
    // echo json_encode($json_data);
    // exit;
}
if ($params['tarTotales']) {

    $qTotales = "SELECT `TareProy`, `ProyNom`, `EmpDesc`, `TareEmp`, SUM(`TareHorMIn`) AS 'totalMin', SUM(`TareHorCost`) AS 'totalCosto', COUNT(`TareID`) AS 'totalTar', 
    MAX(TareID) AS 'lastTar', 
    MAX(TareIni) AS 'lastTareIni' 
    FROM `proy_tareas`
    INNER JOIN `proy_tare_horas` ON `proy_tareas`.`TareID` = `proy_tare_horas`.`TareHorID`
    INNER JOIN `proy_empresas` ON `proy_tareas`.`TareEmp`=`proy_empresas`.`EmpID` 
    INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID` 
    INNER JOIN `usuarios` AS `resp` ON `proy_tareas`.`TareResp`=`resp`.`id` 
    INNER JOIN `proy_proceso` ON `proy_tareas`.`TareProc`=`proy_proceso`.`ProcID` 
    INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID`
    WHERE `proy_tareas`.`TareID` > 0";

    if ($w_c ?? '') {
        $qTotales .= $w_c;
    }

    $qTotales .= " GROUP BY `TareProy`";

    $r = array_pdoQuery($qTotales);

    foreach ($r as $key => $row) {

        $TareProy = $row['TareProy'];
        $ProyNom = $row['ProyNom'];
        $EmpDesc = $row['EmpDesc'];
        $TareEmp = $row['TareEmp'];
        $totalMin = $row['totalMin'];
        $totalCosto = $row['totalCosto'];
        $totalTar = $row['totalTar'];
        $lastTar = $row['lastTar'];
        $lastTareIni = $row['lastTareIni'];

        $data[] = array(
            "proyecto" => array(
                "nombre" => $ProyNom,
                "ID" => intval($TareProy),
            ),
            "empresa" => array(
                "nombre" => $EmpDesc,
                "ID" => intval($TareEmp)
            ),
            "totales" => array(
                "totalMin" => intval($totalMin),
                "totalHoras" => MinHora(intval($totalMin)),
                "totalCosto" => floatval($totalCosto),
                "totalTar" => intval($totalTar),
                "lastTar" => intval($lastTar),
                "lastTareIni" => $lastTareIni
            ),
        );
    }
    $json_data = array(
        "recordsTotal" => count($data),
        "status" => "ok",
        "data" => $data
    );

    echo json_encode($json_data);
    exit;
}

$tiempo_fin = microtime(true);
$tiempo = ($tiempo_fin - $tiempo_inicio);

// fileLog($query, '../query.log');

$json_data = array(
    "draw" => intval($params['draw']),
    "recordsTotal" => intval($count),
    "recordsFiltered" => intval($count),
    "data" => $data,
    "tiempo" => round($tiempo, 2)
);
// $pathLog = "qTar_" . date('Ymd') . ".log";
// fileLog($qTar, $pathLog); 
echo json_encode($json_data);
exit;
