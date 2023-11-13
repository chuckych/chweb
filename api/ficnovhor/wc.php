<?php
$wc = $wcNov = $wcONov = $wcHoras = $wcFicFech = $wcFicFech = $wcFicFechNov = $wcFicFechONov = $wcFicFechHor = $wcFicFechReg = '';
$dp = $payload;

$start = start();
$length = length();

$dp['FechIni'] = $dp['FechIni'] ?? date('Y-m-d'); // Fecha de inicio
$dp['FechFin'] = $dp['FechFin'] ?? date('Y-m-d'); // Fecha de Fin

$dp['onlyReg'] = ($dp['onlyReg']) ?? '';
$dp['onlyReg'] = vp($dp['onlyReg'], 'onlyReg', 'int01', 1); // Traer Solo Fichadas

$dp['onlyHsTr'] = ($dp['onlyHsTr']) ?? '';
$dp['onlyHsTr'] = vp($dp['onlyHsTr'], 'onlyHsTr', 'int01', 1); // Traer Solo registros con Horas trabajadas reales.

$dp['getReg'] = ($dp['getReg']) ?? '';
$dp['getReg'] = vp($dp['getReg'], 'getReg', 'int01', 1); // Traer Fichadas

$dp['getNov'] = ($dp['getNov']) ?? '';
$dp['getNov'] = vp($dp['getNov'], 'getNov', 'int01', 1); // Traer Novedades

$dp['getEstruct'] = ($dp['getEstruct']) ?? '';
$dp['getEstruct'] = vp($dp['getEstruct'], 'getEstruct', 'int01', 1); // Traer codigo de Estructura

$dp['getCierre'] = ($dp['getCierre']) ?? '';
$dp['getCierre'] = vp($dp['getCierre'], 'getCierre', 'int01', 1); // Traer fecha de cierre

$dp['getONov'] = ($dp['getONov']) ?? '';
$dp['getONov'] = vp($dp['getONov'], 'getONov', 'int01', 1); // Traer Otras Novedades

$dp['getHor'] = ($dp['getHor']) ?? '';
$dp['getHor'] = vp($dp['getHor'], 'getHor', 'int01', 1); // Traer Haras

$dp['NovEx'] = ($dp['NovEx']) ?? '';
$dp['NovEx'] = vp($dp['NovEx'], 'NovEx', 'int01', 1); // Filtrar Novedades de forma exclusiva

$dp['ONovEx'] = ($dp['ONovEx']) ?? '';
$dp['ONovEx'] = vp($dp['ONovEx'], 'ONovEx', 'int01', 1); // Filtrar Otras Novedades de forma exclusiva

$dp['HoraEx'] = ($dp['HoraEx']) ?? '';
$dp['HoraEx'] = vp($dp['HoraEx'], 'HoraEx', 'int01', 1); // Filtrar Horas de forma exclusiva

$dp['HoraMin'] = ($dp['HoraMin']) ?? '';
$dp['HoraMin'] = vp($dp['HoraMin'], 'HoraMin', 'str', 5); // str de horas minimo

$dp['HoraMax'] = ($dp['HoraMax']) ?? '';
$dp['HoraMax'] = vp($dp['HoraMax'], 'HoraMax', 'str', 5); // str de horas maximo

$dp['LegApNo'] = $dp['LegApNo'] ?? '';
$dp['LegApNo'] = vp($dp['LegApNo'], 'LegApNo', 'str', 40);
$dp['LegDocu'] = $dp['LegDocu'] ?? [];
$dp['LegDocu'] = vp($dp['LegDocu'], 'LegDocu', 'intArray', 20);
$dp['LegRegCH'] = $dp['LegRegCH'] ?? [];
$dp['LegRegCH'] = vp($dp['LegRegCH'], 'LegRegCH', 'intArray', 20);
$dp['LegTipo'] = $dp['LegTipo'] ?? [];
$dp['LegTipo'] = vp($dp['LegTipo'], 'LegTipo', 'numArray01', 1);

$arrDPPersonal = array(
    'LegApNo' => $dp['LegApNo'],
    // {strArray} Apellido y Nombre
    'LegDocu' => $dp['LegDocu'],
    // {int} {array} Documento en Personal
    'LegRegCH' => $dp['LegRegCH'],
    // {int} {array} Regla de Control Horario
    'LegTipo' => $dp['LegTipo'],
    // 0 = Mensual 1 = Jornal
);

$dp['LegaD'] = ($dp['LegaD']) ?? '';
$dp['LegaD'] = vp($dp['LegaD'], 'LegaD', 'int', 11); // {int} Legajo Desde
$dp['LegaH'] = ($dp['LegaH']) ?? '';
$dp['LegaH'] = vp($dp['LegaH'], 'LegaH', 'int', 11); // {int} Legajo hasta

$dp['Lega'] = ($dp['Lega']) ?? [];
$dp['Lega'] = vp($dp['Lega'], 'Lega', 'intArrayM0', 11);
$dp['Empr'] = ($dp['Empr']) ?? [];
$dp['Empr'] = vp($dp['Empr'], 'Empr', 'intArray', 5);
$dp['Plan'] = ($dp['Plan']) ?? [];
$dp['Plan'] = vp($dp['Plan'], 'Plan', 'intArray', 5);
$dp['Conv'] = ($dp['Conv']) ?? [];
$dp['Conv'] = vp($dp['Conv'], 'Conv', 'intArray', 5);
$dp['Sec2'] = ($dp['Sec2']) ?? [];
$dp['Sec2'] = vp($dp['Sec2'], 'Sec2', 'intArray', 5);
$dp['Sect'] = ($dp['Sect']) ?? [];
$dp['Sect'] = vp($dp['Sect'], 'Sect', 'intArray', 5);
$dp['Grup'] = ($dp['Grup']) ?? [];
$dp['Grup'] = vp($dp['Grup'], 'Grup', 'intArray', 5);
$dp['Sucu'] = ($dp['Sucu']) ?? [];
$dp['Sucu'] = vp($dp['Sucu'], 'Sucu', 'intArray', 5);
$dp['NovT'] = ($dp['NovT']) ?? [];
$dp['NovT'] = vp($dp['NovT'], 'NovT', 'numArray01', 1);
$dp['NovS'] = ($dp['NovS']) ?? [];
$dp['NovS'] = vp($dp['NovS'], 'NovS', 'numArray01', 1);
$dp['NovA'] = ($dp['NovA']) ?? [];
$dp['NovA'] = vp($dp['NovA'], 'NovA', 'numArray01', 1);
$dp['NovI'] = ($dp['NovI']) ?? [];
$dp['NovI'] = vp($dp['NovI'], 'NovI', 'numArray01', 1);
$dp['DiaL'] = ($dp['DiaL']) ?? [];
$dp['DiaL'] = vp($dp['DiaL'], 'DiaL', 'numArray01', 1);
$dp['DiaF'] = ($dp['DiaF']) ?? [];
$dp['DiaF'] = vp($dp['DiaF'], 'DiaF', 'numArray01', 1);
$dp['HsAT'] = ($dp['HsAT']) ?? [];
$dp['HsAT'] = vp($dp['HsAT'], 'HsAT', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HsTr'] = ($dp['HsTr']) ?? [];
$dp['HsTr'] = vp($dp['HsTr'], 'HsTr', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HorE'] = ($dp['HorE']) ?? [];
$dp['HorE'] = vp($dp['HorE'], 'HorE', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HorS'] = ($dp['HorS']) ?? [];
$dp['HorS'] = vp($dp['HorS'], 'HorS', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HorD'] = ($dp['HorD']) ?? [];
$dp['HorD'] = vp($dp['HorD'], 'HorD', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['Falta'] = ($dp['Falta']) ?? [];
$dp['Falta'] = vp($dp['Falta'], 'Falta', 'numArray01', 1); // 0 = normal 1 = Impar (iconsistencias)

$arrDPFichas = array(
    'Lega' => $dp['Lega'],
    // Legajo {int} {array}
    'Empr' => $dp['Empr'],
    // Empresa {int} {array}
    'Plan' => $dp['Plan'],
    // Planta {int} {array}
    'Conv' => $dp['Conv'],
    // Convenio {int} {array}
    'Sec2' => $dp['Sec2'],
    // Seccion {int} {array}
    'Sect' => $dp['Sect'],
    // Sector {int} {array}
    'Grup' => $dp['Grup'],
    // Grupos {int} {array}
    'Sucu' => $dp['Sucu'],
    // Sucursales {int} {array}
    'NovT' => $dp['NovT'],
    // Si Hay Novedades de tarde {numeric} {array} 0 = no hay 1 = hay 
    'NovS' => $dp['NovS'],
    // Si Hay Novedades de Salida {numeric} {array} 0 = no hay 1 = hay 
    'NovA' => $dp['NovA'],
    // Si Hay Novedades de Ausencia {numeric} {array} 0 = no hay 1 = hay 
    'NovI' => $dp['NovI'],
    // Si Hay Novedades de Incumplimiento {numeric} {array} 0 = no hay 1 = hay 
    'DiaL' => $dp['DiaL'],
    // Si es día Laboral {int} {array} 0 = no  1 = Si 
    'DiaF' => $dp['DiaF'],
    // Si es día feriado {int} {array} 0 = no  1 = Si 
    'HsAT' => $dp['HsAT'],
    // Horas A trabajar {string} HH:MM
    'HsTr' => $dp['HsTr'],
    // Horas A trabajajadas {string} HH:MM
    'HorE' => $dp['HorE'],
    // Horario de entrada {string} HH:MM del día
    'HorS' => $dp['HorS'],
    // Horario de salida {string} HH:MM del día
    'HorD' => $dp['HorD'],
    // Horas de descanso del día {string} HH:MM
    'Falta' => $dp['Falta'],
    // Si hay Fichadas Inconsistentes {int} 0 = normal (pares), 1 = si (impares) 
);
$dp['Nove'] = $dp['Nove'] ?? [];
$dp['Nove'] = vp($dp['Nove'], 'Nove', 'intArray', 5);
$dp['NoTi'] = $dp['NoTi'] ?? [];
$dp['NoTi'] = vp($dp['NoTi'], 'NoTi', 'intArrayM8', 1);
$dp['Esta'] = $dp['Esta'] ?? [];
$dp['Esta'] = vp($dp['Esta'], 'NoTi', 'intArray', 1);
$dp['EstaNov'] = $dp['EstaNov'] ?? [];
$dp['EstaNov'] = vp($dp['EstaNov'], 'NoTi', 'intArray', 1);

$arrDPNovedad = array(
    'Nove' => $dp['Nove'],
    // Novedad {int}
    'NoTi' => $dp['NoTi'],
    // Tipo de Novedad {int}
    'EstaNov' => $dp['EstaNov'],
    // {int}  Estado (0-Normal 2-Manual o modificada)
);

$dp['ONov'] = $dp['ONov'] ?? [];
$dp['ONov'] = vp($dp['ONov'], 'ONov', 'intArray', 5);

$arrDPONovedad = array(
    'ONov' => $dp['ONov'],
    // Otras Novedades {int}
);

$dp['Hora'] = $dp['Hora'] ?? [];
$dp['Hora'] = vp($dp['Hora'], 'Hora', 'intArray', 5);

$arrDPHOras = array(
    'Hora' => $dp['Hora'],
    // Hora {int}
    'Esta' => $dp['Esta'],
    // {int}  Estado (0-Normal 2-Manual o modificada)
);
foreach ($arrDPHOras as $key => $Fichas1) { // Novedades
    $e = array();
    if (is_array($Fichas1)) {
        foreach ($Fichas1 as $v1) {
            if ($v1 !== NULL && $v1 !== '') {
                $e[] = ($v1);
            }
        }
        $e = array_unique($e);
        if ($e) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wcHoras .= " AND FICHAS1.Fic$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wcHoras .= " AND FICHAS1.Fic$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wcHoras .= " AND FICHAS1.Fic$key = '$v'";
        }
    }
}
foreach ($arrDPNovedad as $key => $Fichas3) { // Novedades
    $e = array();
    if ($key == 'EstaNov') {
        $key = 'Esta';
    }
    if (is_array($Fichas3)) {
        foreach ($Fichas3 as $v1) {
            if ($v1 !== NULL && $v1 !== '') {
                $e[] = ($v1);
            }
        }
        $e = array_unique($e);
        if ($e) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wcNov .= " AND FICHAS3.Fic$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wcNov .= " AND FICHAS3.Fic$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wcNov .= " AND FICHAS3.Fic$key = '$v'";
        }
    }
}
foreach ($arrDPONovedad as $key => $Fichas2) { // Novedades
    $e = array();
    if (is_array($Fichas2)) {
        foreach ($Fichas2 as $v1) {
            if ($v1 !== NULL && $v1 !== '') {
                $e[] = ($v1);
            }
        }
        $e = array_unique($e);
        if ($e) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wcONov .= " AND FICHAS2.Fic$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wcONov .= " AND FICHAS2.Fic$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wcONov .= " AND FICHAS2.Fic$key = '$v'";
        }
    }
}
foreach ($arrDPFichas as $key => $Fichas) {
    $e = array();
    if (is_array($Fichas)) {
        $v = '';
        $e = array_filter($Fichas, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= " AND FICHAS.Fic$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= " AND FICHAS.Fic$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= " AND FICHAS.Fic$key = '$v'";
        }
    }
}
foreach ($arrDPPersonal as $key => $v) {

    if (is_array($v)) {
        if ($e = array_filter($v)) {
            if ($e) {
                if (count($e) > 1) {
                    $e = "'" . implode("','", $e) . "'";
                    $wc .= " AND PERSONAL.$key IN ($e)";
                } else {
                    foreach ($e as $v) {
                        if ($v !== NULL) {
                            $wc .= " AND PERSONAL.$key = '$v'";
                        }
                    }
                }
            }
        }
    } else {
        if ($v) {
            if ($key == 'LegApNo') {
                $wc .= " AND PERSONAL.$key LIKE '%$v%'";
            } else {
                $wc .= " AND PERSONAL.$key = '$v'";
            }
        }
    }
}
if ($dp['FechIni'] && $dp['FechFin']) {
    $fecha1 = fechFormat($dp['FechIni'], 'Ymd');
    $fecha2 = fechFormat($dp['FechFin'], 'Ymd');
    if (intval($fecha1) > intval($fecha2)) {
        http_response_code(400);
        (response(array(), 0, 'Rango de Fecha Incorrecto', 400, $time_start, 0, $idCompany));
    }
    $wcFicFech = " AND FICHAS.FicFech BETWEEN '$fecha1' AND '$fecha2'";
    $wcFicFechNov = " AND FICHAS3.FicFech BETWEEN '$fecha1' AND '$fecha2'";
    $wcFicFechONov = " AND FICHAS2.FicFech BETWEEN '$fecha1' AND '$fecha2'";
    $wcFicFechHor = " AND FICHAS1.FicFech BETWEEN '$fecha1' AND '$fecha2'";
    $wcFicFechReg = " AND REGISTRO.RegFeAS BETWEEN '$fecha1' AND '$fecha2'";
    if (intval($fecha1) == intval($fecha2)) {
        $wcFicFech = " AND FICHAS.FicFech = '$fecha1'";
        $wcFicFechNov = " AND FICHAS3.FicFech = '$fecha1'";
        $wcFicFechONov = " AND FICHAS2.FicFech = '$fecha1'";
        $wcFicFechHor = " AND FICHAS1.FicFech = '$fecha1'";
        $wcFicFechReg = " AND REGISTRO.RegFeAS = '$fecha1'";
    }
}
if ($dp['LegaD']) {
    $LegaD = intval($dp['LegaD']);
    $LegaH = ($dp['LegaH']) ? intval($dp['LegaH']) : intval($dp['LegaD']); // si legaH viene vacio asignamos LegaD
    if (($LegaD) > ($LegaH)) {
        http_response_code(400);
        (response(array(), 0, 'Rango de Legajos Incorrecto', 400, $time_start, 0, $idCompany));
    } else {
        $wc .= " AND FICHAS.FicLega BETWEEN '$LegaD' AND '$LegaH'";
    }
}
if ($dp['onlyHsTr']) {
    $wc .= " AND dbo.fn_STRMinutos(FICHAS.FicHstr) > 0";
}
// $pathLogss = __DIR__ . '../../logs/' . date('Ymd') . '_wc.log';
// writeLog(PHP_EOL . 'DP: ' . json_encode($dp), $pathLogss);
// writeLog(PHP_EOL . 'Wc: ' . $wc, $pathLogss);
// print_r($wcFicFech) . exit;
// writeLog(PHP_EOL . 'arrDPFichas: '.json_encode($arrDPFichas), $pathLogss); 
// writeLog(PHP_EOL . 'arrDPNovedad: '.json_encode($arrDPNovedad), $pathLogss); 
// writeLog(PHP_EOL . 'arrDPONovedad: '.json_encode($arrDPONovedad), $pathLogss); 
// writeLog(PHP_EOL . 'arrDPHOras: '.json_encode($arrDPHOras), $pathLogss); 
// writeLog(PHP_EOL . 'arrDPPersonal: '.json_encode($arrDPPersonal), $pathLogss); 
