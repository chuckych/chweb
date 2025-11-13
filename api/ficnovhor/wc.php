<?php
$wc = $wcNov = $wcONov = $wcHoras = $wcFicFech = $wcFicFech = $wcFicFechNov = $wcFicFechONov = $wcFicFechHor = $wcFicFechReg = '';
$dp = $payload;

$start = start();
$length = length();

if (!validarJsonRequest()) {
    http_response_code(400);
    response([], 0, "Json recibido inválido", 400, timeStart(), 0, 0);
}

$dp['FechIni'] = $dp['FechIni'] ?: date('Y-m-d'); // Fecha de inicio
$dp['FechFin'] = $dp['FechFin'] ?: date('Y-m-d'); // Fecha de Fin

$dp['FechIni'] = formatearFecha($dp['FechIni']);
$dp['FechFin'] = formatearFecha($dp['FechFin']);

$errorDeFechas = !$dp['FechFin'] || !$dp['FechFin'] ?: false;

if ($errorDeFechas) {
    http_response_code(400);
    response([], 0, "Error en formato de fechas", 400, timeStart(), 0, 0);
}

$hayLimit = $dp['DiasLimite'] > 0 ?: false;

if ($hayLimit) {
    $length = 1000000;
    $dp['onlyReg'] = "";
    $dp['getReg'] = "";
    $dp['getNov'] = "1";
    $dp['getONov'] = "";
    $dp['getHor'] = "";
    $dp['getEstruct'] = "";
    $dp['getCierre'] = "";
    $dp['NovEx'] = "";
    $dp['ONovEx'] = "";
    $dp['HoraEx'] = "";
    $dp['LegApNo'] = "";
    $dp['LegDocu'] = [];
    $dp['NovA'] = [1];
    $dp['LegRegCH'] = [];
    $dp['LegTipo'] = [];
    $dp['Empr'] = [];
    $dp['Plan'] = [];
    $dp['Conv'] = [];
    $dp['Sect'] = [];
    $dp['Sec2'] = [];
    $dp['Grup'] = [];
    $dp['Sucu'] = [];
    $dp['NovT'] = [];
    $dp['NovS'] = [];
    $dp['NovI'] = [];
    $dp['DiaL'] = [];
    $dp['DiaF'] = [];
    $dp['HsAT'] = [];
    $dp['HsTr'] = [];
    $dp['HorE'] = [];
    $dp['HorS'] = [];
    $dp['HorD'] = [];
    $dp['Falta'] = [];
    $dp['ONov'] = [];
    $dp['Hora'] = [];
    $dp['Esta'] = [];
    $dp['EstaNov'] = [];
    $dp['Dias'] = [];
    $dp['NoTi'] = [];
    $dp['LegaD'] = "";
    $dp['LegaH'] = "";
}

$dp['onlyReg'] = $dp['onlyReg'] ?: '';
$dp['onlyReg'] = vp($dp['onlyReg'], 'onlyReg', 'int01', 1); // Traer Solo Fichadas

$dp['onlyHsTr'] = $dp['onlyHsTr'] ?: '';
$dp['onlyHsTr'] = vp($dp['onlyHsTr'], 'onlyHsTr', 'int01', 1); // Traer Solo registros con Horas trabajadas reales.

$dp['getReg'] = ($dp['getReg']) ?: '';
$dp['getReg'] = vp($dp['getReg'], 'getReg', 'int01', 1); // Traer Fichadas

$dp['getNov'] = ($dp['getNov']) ?: '';
$dp['getNov'] = vp($dp['getNov'], 'getNov', 'int01', 1); // Traer Novedades

$dp['getEstruct'] = ($dp['getEstruct']) ?: '';
$dp['getEstruct'] = vp($dp['getEstruct'], 'getEstruct', 'int01', 1); // Traer codigo de Estructura

$dp['getCierre'] = ($dp['getCierre']) ?: '';
$dp['getCierre'] = vp($dp['getCierre'], 'getCierre', 'int01', 1); // Traer fecha de cierre

$dp['getONov'] = ($dp['getONov']) ?: '';
$dp['getONov'] = vp($dp['getONov'], 'getONov', 'int01', 1); // Traer Otras Novedades

$dp['getHor'] = ($dp['getHor']) ?: '';
$dp['getHor'] = vp($dp['getHor'], 'getHor', 'int01', 1); // Traer Haras

$dp['NovEx'] = ($dp['NovEx']) ?: '';
$dp['NovEx'] = vp($dp['NovEx'], 'NovEx', 'int01', 1); // Filtrar Novedades de forma exclusiva

$dp['ONovEx'] = ($dp['ONovEx']) ?: '';
$dp['ONovEx'] = vp($dp['ONovEx'], 'ONovEx', 'int01', 1); // Filtrar Otras Novedades de forma exclusiva

$dp['HoraEx'] = ($dp['HoraEx']) ?: '';
$dp['HoraEx'] = vp($dp['HoraEx'], 'HoraEx', 'int01', 1); // Filtrar Horas de forma exclusiva

$dp['HoraMin'] = ($dp['HoraMin']) ?: '';
$dp['HoraMin'] = vp($dp['HoraMin'], 'HoraMin', 'str', 5); // str de horas minimo

$dp['HoraMax'] = ($dp['HoraMax']) ?: '';
$dp['HoraMax'] = vp($dp['HoraMax'], 'HoraMax', 'str', 5); // str de horas maximo

$dp['LegApNo'] = $dp['LegApNo'] ?: '';
$dp['LegApNo'] = vp($dp['LegApNo'], 'LegApNo', 'str', 40);
$dp['LegDocu'] = $dp['LegDocu'] ?: [];
$dp['LegDocu'] = vp($dp['LegDocu'], 'LegDocu', 'intArray', 20);
$dp['LegRegCH'] = $dp['LegRegCH'] ?: [];
$dp['LegRegCH'] = vp($dp['LegRegCH'], 'LegRegCH', 'intArray', 20);
$dp['LegTipo'] = $dp['LegTipo'] ?: [];
$dp['LegTipo'] = vp($dp['LegTipo'], 'LegTipo', 'numArray01', 1);

$arrDPPersonal = [
    'LegApNo' => $dp['LegApNo'], // {strArray} Apellido y Nombre
    'LegDocu' => $dp['LegDocu'], // {int} {array} Documento en Personal
    'LegRegCH' => $dp['LegRegCH'], // {int} {array} Regla de Control Horario
    'LegTipo' => $dp['LegTipo'], // 0 = Mensual 1 = Jornal
];

$dp['LegaD'] = ($dp['LegaD']) ?: '';
$dp['LegaD'] = vp($dp['LegaD'], 'LegaD', 'int', 11); // {int} Legajo Desde
$dp['LegaH'] = ($dp['LegaH']) ?: '';
$dp['LegaH'] = vp($dp['LegaH'], 'LegaH', 'int', 11); // {int} Legajo hasta

$dp['Lega'] = ($dp['Lega']) ?: [];
$dp['Lega'] = vp($dp['Lega'], 'Lega', 'intArrayM0', 11);
$dp['Empr'] = ($dp['Empr']) ?: [];
$dp['Empr'] = vp($dp['Empr'], 'Empr', 'intArray', 5);
$dp['Plan'] = ($dp['Plan']) ?: [];
$dp['Plan'] = vp($dp['Plan'], 'Plan', 'intArray', 5);
$dp['Conv'] = ($dp['Conv']) ?: [];
$dp['Conv'] = vp($dp['Conv'], 'Conv', 'intArray', 5);
$dp['Sec2'] = ($dp['Sec2']) ?: [];
$dp['Sec2'] = vp($dp['Sec2'], 'Sec2', 'intArray', 5);
$dp['Sect'] = ($dp['Sect']) ?: [];
$dp['Sect'] = vp($dp['Sect'], 'Sect', 'intArray', 5);
$dp['Grup'] = ($dp['Grup']) ?: [];
$dp['Grup'] = vp($dp['Grup'], 'Grup', 'intArray', 5);
$dp['Sucu'] = ($dp['Sucu']) ?: [];
$dp['Sucu'] = vp($dp['Sucu'], 'Sucu', 'intArray', 5);
$dp['NovT'] = ($dp['NovT']) ?: [];
$dp['NovT'] = vp($dp['NovT'], 'NovT', 'numArray01', 1);
$dp['NovS'] = ($dp['NovS']) ?: [];
$dp['NovS'] = vp($dp['NovS'], 'NovS', 'numArray01', 1);
$dp['NovA'] = ($dp['NovA']) ?: [];
$dp['NovA'] = vp($dp['NovA'], 'NovA', 'numArray01', 1);
$dp['NovI'] = ($dp['NovI']) ?: [];
$dp['NovI'] = vp($dp['NovI'], 'NovI', 'numArray01', 1);
$dp['DiaL'] = ($dp['DiaL']) ?: [];
$dp['DiaL'] = vp($dp['DiaL'], 'DiaL', 'numArray01', 1);
$dp['DiaF'] = ($dp['DiaF']) ?: [];
$dp['DiaF'] = vp($dp['DiaF'], 'DiaF', 'numArray01', 1);
$dp['HsAT'] = ($dp['HsAT']) ?: [];
$dp['HsAT'] = vp($dp['HsAT'], 'HsAT', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HsTr'] = ($dp['HsTr']) ?: [];
$dp['HsTr'] = vp($dp['HsTr'], 'HsTr', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HorE'] = ($dp['HorE']) ?: [];
$dp['HorE'] = vp($dp['HorE'], 'HorE', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HorS'] = ($dp['HorS']) ?: [];
$dp['HorS'] = vp($dp['HorS'], 'HorS', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['HorD'] = ($dp['HorD']) ?: [];
$dp['HorD'] = vp($dp['HorD'], 'HorD', 'strArrayMMlength', 5); // Min y max 5 caracteres
$dp['Falta'] = ($dp['Falta']) ?: [];
$dp['Falta'] = vp($dp['Falta'], 'Falta', 'numArray01', 1); // 0 = normal 1 = Impar (iconsistencias)

$dp['DiasLimite'] = ($dp['DiasLimite']) ?: 0;
$dp['DiasLimite'] = vp($dp['DiasLimite'], 'DiasLimite', 'int', 11); // {int} Legajo hasta

$arrDPSec2 = [
    'Sec2' => $dp['Sec2'], // Seccion {int} {array}
];
$arrDPFichas = [
    'Lega' => $dp['Lega'], // Legajo {int} {array}
    'Empr' => $dp['Empr'], // Empresa {int} {array}
    'Plan' => $dp['Plan'], // Planta {int} {array}
    'Conv' => $dp['Conv'], // Convenio {int} {array}
    'Sect' => $dp['Sect'], // Sector {int} {array}
    'Grup' => $dp['Grup'], // Grupos {int} {array}
    'Sucu' => $dp['Sucu'], // Sucursales {int} {array}
    'NovT' => $dp['NovT'], // Si Hay Novedades de tarde {numeric} {array} 0 = no hay 1 = hay 
    'NovS' => $dp['NovS'], // Si Hay Novedades de Salida {numeric} {array} 0 = no hay 1 = hay 
    'NovA' => $dp['NovA'], // Si Hay Novedades de Ausencia {numeric} {array} 0 = no hay 1 = hay 
    'NovI' => $dp['NovI'], // Si Hay Novedades de Incumplimiento {numeric} {array} 0 = no hay 1 = hay 
    'DiaL' => $dp['DiaL'], // Si es día Laboral {int} {array} 0 = no  1 = Si 
    'DiaF' => $dp['DiaF'], // Si es día feriado {int} {array} 0 = no  1 = Si 
    'HsAT' => $dp['HsAT'], // Horas A trabajar {string} HH:MM
    'HsTr' => $dp['HsTr'], // Horas A trabajadas {string} HH:MM
    'HorE' => $dp['HorE'], // Horario de entrada {string} HH:MM del día
    'HorS' => $dp['HorS'], // Horario de salida {string} HH:MM del día
    'HorD' => $dp['HorD'], // Horas de descanso del día {string} HH:MM
    'Falta' => $dp['Falta'], // Si hay Fichadas Inconsistentes {int} 0 = normal (pares), 1 = si (impares) 
];

$dp['Nove'] = $dp['Nove'] ?: [];
$typeNove = is_array($dp['Nove']) ? 'intArray' : 'int';
$dp['Nove'] = vp($dp['Nove'], 'Nove', $typeNove, 5);

$dp['NoTi'] = $dp['NoTi'] ?: [];
$dp['NoTi'] = vp($dp['NoTi'], 'NoTi', 'intArrayM8', 1);
$dp['Esta'] = $dp['Esta'] ?: [];
$dp['Esta'] = vp($dp['Esta'], 'NoTi', 'intArray', 1);
$dp['EstaNov'] = $dp['EstaNov'] ?: [];
$dp['EstaNov'] = vp($dp['EstaNov'], 'NoTi', 'intArray', 1);

$arrDPNovedad = [
    'Nove' => $dp['Nove'],
    'NoTi' => $dp['NoTi'],
    'EstaNov' => $dp['EstaNov'], // {int}  Estado (0-Normal 2-Manual o modificada)
];

$dp['ONov'] = $dp['ONov'] ?: [];
$dp['ONov'] = vp($dp['ONov'], 'ONov', 'intArray', 5);

$arrDPONovedad = [
    'ONov' => $dp['ONov'], // Otras Novedades {int}
];

$dp['Hora'] = $dp['Hora'] ?: [];
$dp['Hora'] = vp($dp['Hora'], 'Hora', 'intArray', 5);

$Dias = $dp['Dias'] ?: []; // si $Dias no esta vacio. Chequear que sea un arreglo de numeros y que solo se permita del 1 al 7

if (!empty($Dias)) {
    // Validar, filtrar únicos y ordenar en una sola operación
    $validos = array_unique(array_filter(
        array_map('intval', $Dias),
        static fn($v) => $v >= 1 && $v <= 7
    ));

    // Verificar si hubo valores inválidos
    if (count($validos) !== count(array_unique($Dias))) {
        http_response_code(400);
        response([], 0, 'Días debe ser un arreglo de números del 1 al 7', 400, $time_start, 0, $idCompany);
        return; // Añadir return para evitar ejecución posterior
    }

    // Ordenar y convertir a CSV
    sort($validos, SORT_NUMERIC);
    $Dias = implode(',', $validos);
}

$arrDPHOras = [
    'Hora' => $dp['Hora'], // Hora {int}
    'Esta' => $dp['Esta'], // {int}  Estado (0-Normal 2-Manual o modificada)
];
foreach ($arrDPHOras as $key => $Fichas1) { // Novedades
    $e = [];
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
    $e = [];
    if ($key == 'EstaNov') {
        $key = 'Esta';
    }

    if (is_array($Fichas3)) {
        foreach ($Fichas3 as $v1) {
            if ($v1 !== NULL && $v1 !== '') {
                $e[] = $v1;
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
        if ($Fichas3) {
            $wcNov .= " AND FICHAS3.Fic$key = '$Fichas3'";
        }
    }
}
foreach ($arrDPONovedad as $key => $Fichas2) { // Novedades
    $e = [];
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
    $e = [];
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
foreach ($arrDPSec2 as $key => $Fichas) {
    $e = [];
    if (is_array($Fichas)) {
        $v = '';
        $e = array_filter($Fichas, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        $FichasSeccion = " AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2)";
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= "$FichasSeccion IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= "$FichasSeccion = '$v'";
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= "$FichasSeccion = '$v'";
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
        (response([], 0, 'Rango de Fecha Incorrecto', 400, $time_start, 0, $idCompany));
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
    if ($LegaD > $LegaH) {
        http_response_code(400);
        (response([], 0, 'Rango de Legajos Incorrecto', 400, $time_start, 0, $idCompany));
    } else {
        $wc .= " AND FICHAS.FicLega BETWEEN '$LegaD' AND '$LegaH'";
    }
}
if ($dp['onlyHsTr']) {
    $wc .= " AND dbo.fn_STRMinutos(FICHAS.FicHstr) > 0";
}