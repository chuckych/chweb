<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$iLega2 = $total = $wcFicFech = $FicCountSelect = $joinFichas4 = $joinFichas3 = $joinFichas2 = $joinFichas1 = $onlyRegCount = '';
$IlegFic = array();

$control->check_method("POST");
$control->check_json();

require __DIR__ . '../wc.php';

if ($dp['getReg']) {
    $FicCountSelect = "(SELECT count(1) FROM REGISTRO R WHERE R.RegFeAs = FICHAS.FicFech AND R.RegLega = FICHAS.FicLega) AS 'FicCount',";
    // $FicCountSelect="(SELECT count(1) FROM REGISTRO R WHERE R.RegFeAs = FICHAS.FicFech AND R.RegLega = FICHAS.FicLega) AS 'FicCount',";

    if ($dp['onlyReg']) {
        // $joinFichas4 = " INNER JOIN REGISTRO on FICHAS.FicFech = REGISTRO.RegFeAs AND FICHAS.FicLega = REGISTRO.RegLega";
        $onlyRegCount = " AND (SELECT count(1) FROM REGISTRO R WHERE R.RegFeAs = FICHAS.FicFech AND R.RegLega = FICHAS.FicLega) > 0 ";
    }
}
if ($wcNov) {
    $joinFichas3 = " INNER JOIN FICHAS3 ON FICHAS.FicLega = FICHAS3.FicLega AND FICHAS.FicFech = FICHAS3.FicFech AND FICHAS.FicTurn = FICHAS3.FicTurn";
}
if ($wcONov) {
    $joinFichas2 = " INNER JOIN FICHAS2 ON FICHAS.FicLega = FICHAS2.FicLega AND FICHAS.FicFech = FICHAS2.FicFech AND FICHAS.FicTurn = FICHAS2.FicTurn";
}
if ($wcHoras) {
    $joinFichas1 = " INNER JOIN FICHAS1 ON FICHAS.FicLega = FICHAS1.FicLega AND FICHAS.FicFech = FICHAS1.FicFech AND FICHAS.FicTurn = FICHAS1.FicTurn";
}
// CONVERT(VARCHAR(20),FICHAS.FicFech,120) AS 'Fecha',
$qFic = "SELECT FICHAS.FicFech AS 'Fecha', PERSONAL.LegApNo, PERSONAL.LegDocu, PERSONAL.LegCUIT, FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicHorD, FICHAS.FicNovA, FICHAS.FicNovS, FICHAS.FicNovT, FICHAS.FicNovI, FICHAS.FicLega, FICHAS.FicFech, FICHAS.FicDiaL, FICHAS.FicDiaF, FICHAS.FicHsAT, FICHAS.FicHsTr, FICHAS.FicFalta FROM FICHAS
INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume $joinFichas3 $joinFichas2 $joinFichas1
WHERE FICHAS.FicLega > 0 $wcFicFech";

$qFicCount = "SELECT count(1) as 'count' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume $joinFichas3 $joinFichas2 $joinFichas1 WHERE FICHAS.FicLega > 0 $wcFicFech ";

if ($wc) {
    $qFic .= $wc;
    $qFicCount .= $wc;
}
if ($dp['onlyReg']) { // Muestra solo registros con fichadas
    $qFic .= $onlyRegCount;
    $qFicCount .= $onlyRegCount;
}
if ($wcNov) {
    $qFic .= $wcNov;
    $qFicCount .= $wcNov;
}

if ($wcONov) {
    $qFic .= $wcONov;
    $qFicCount .= $wcONov;
}

if ($wcHoras) {
    $qFic .= $wcHoras;
    $qFicCount .= $wcHoras;
}
// print_r($qFic).exit;

$stmtFicCount = $dbApiQuery($qFicCount)[0]['count'] ?? '';
$qFic .= " ORDER BY FICHAS.FicFech";
$qFic .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
// print_r($qFic).exit;
$stmtFic = $dbApiQuery($qFic) ?? '';

if (empty($stmtFic)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}

$iLega = (implodeArrayByKey($stmtFic, 'FicLega'));

$IlegNoV = array();
$IlegHor = array();
foreach ($stmtFic as $key => $i) {
    $IlegFic[]['FicLega'] = ($i['FicLega']);

    $s = array_sum(array($i['FicNovA'], $i['FicNovS'], $i['FicNovT'], $i['FicNovI']));
    if ($s) {
        $IlegNoV[]['FicLega'] = ($i['FicLega']);
    }
    if ($i['FicHsTr']) {
        $IlegHor[]['FicLega'] = ($i['FicLega']);
    }
}
if ($dp['getNov']) {
    
    $IlegNoV = (implodeArrayByKey($IlegNoV, 'FicLega'));
    $IlegNoV = ($IlegNoV) ? " AND FICHAS3.FicLega IN ($IlegNoV)" : "";
    
    $qNov = "SELECT FICHAS3.FicFech AS 'Fecha', FICHAS3.FicLega, FICHAS3.FicHoras, FICHAS3.FicObse, NovCodi, NovDesc, NovCCodi, NovCDesc, FICHAS3.FicNoTi FROM FICHAS3 LEFT JOIN NOVEDAD ON FICHAS3.FicNove=NOVEDAD.NovCodi LEFT JOIN NOVECAUSA ON FICHAS3.FicNove=NOVECAUSA.NovCNove AND FICHAS3.FicCaus=NOVECAUSA.NovCCodi WHERE FICHAS3.FicLega > 0 $wcFicFechNov $IlegNoV";
    if ($dp['NovEx']) {
        if ($wcNov) {
            $qNov .= $wcNov;
        }
    }
    $qNov .= " ORDER BY FICHAS3.FicFech";
    $stmtNov = $dbApiQuery($qNov);
}
if ($dp['getONov']) {
    $IlegONov = (implodeArrayByKey($IlegFic, 'FicLega'));
    $IlegONov = ($IlegONov) ? " AND FICHAS2.FicLega IN ($IlegONov)" : "";

    $qONov = "SELECT FICHAS2.FicFech AS 'Fecha', FICHAS2.FicLega, FicONov, FicValor, FicObsN, ONovDesc, ONovTipo, CASE ONovTipo WHEN 0 THEN 'Valor' ELSE 'Horas' END as 'Tipo' FROM FICHAS2 LEFT JOIN OTRASNOV ON FICHAS2.FicONov=OTRASNOV.ONovCodi WHERE FICHAS2.FicLega >0 $wcFicFechONov $IlegONov";
    if ($dp['ONovEx']) {
        if ($wcONov) {
            $qONov .= $wcONov;
        }
    }
    $qONov .= " ORDER BY FICHAS2.FicFech";
    $stmtONov = $dbApiQuery($qONov);
}
if ($dp['getHor']) {

    $IlegHor = (implodeArrayByKey($IlegHor, 'FicLega'));
    $IlegHor = ($IlegHor) ? " AND FICHAS1.FicLega IN ($IlegHor)" : "";

    $qHor = "SELECT FICHAS1.FicFech AS 'Fecha', FicLega, FicHora, THoDesc, THoDesc2, THoID, THoColu, FicObse, FicEsta, FICHAS1.FicHsHe AS 'HorasCalc', FICHAS1.FicHsAu AS 'HorasHechas', FICHAS1.FicHsAu2 AS 'HorasAuto', THoCCodi, THoCDesc FROM FICHAS1 LEFT JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi AND FICHAS1.FicHora=TIPOHORACAUSA.THoCHora WHERE FICHAS1.FicLega > 0 $wcFicFechHor $IlegHor";
    
    if ($dp['HoraEx']) {
        if ($wcHoras) {
            $qHor .= $wcHoras;
        }
    }
    $qHor .= " ORDER BY FICHAS1.FicFech, TIPOHORA.THoColu";
    $stmtHoras = $dbApiQuery($qHor);
}
if ($dp['getReg']) {

    $IlegFic = (implodeArrayByKey($IlegFic, 'FicLega'));
    $IlegFic = ($IlegFic) ? " AND REGISTRO.RegLega IN ($IlegFic)" : "";

    $qReg = "SELECT REGISTRO.RegFeAs AS 'Fecha', RegLega, RegTarj, RegFech, RegHora, RegTipo, RegFeAs, RegFeRe, RegHoRe, RegRelo, RegLect FROM REGISTRO WHERE REGISTRO.RegLega > 0 $wcFicFechReg $IlegFic ORDER BY REGISTRO.RegFeRe, REGISTRO.RegHoRe";
    $stmtRegistros = $dbApiQuery($qReg);
}

foreach ($stmtFic as $key => $v) {

    $arrNov = array();
    $dataNov = array();
    $arrONov = array();
    $dataONov = array();
    $dataHora = array();
    $dataHoras = array();
    $dataFichada = array();
    $dataFichadas = array();

    $SumNov = array_sum(array($v['FicNovA'], $v['FicNovS'], $v['FicNovT'], $v['FicNovI']));
    if ($dp['getNov']) {
        if ($SumNov) {
            if (($stmtNov)) {
                $arrNov = filtrarObjetoArr2($stmtNov, 'FicLega', 'Fecha', $v['FicLega'], $v['Fecha']);
                foreach ($arrNov as $key => $n) {
                    $dataNov[] = array(
                        'Codi'  => $n['NovCodi'],
                        'Desc'  => $n['NovDesc'],
                        'Horas' => $n['FicHoras'],
                        'Obse'  => $n['FicObse'],
                        'NoTi'  => $n['FicNoTi'],
                        'NoTiD' => novedadTipo(array($n['FicNoTi'])),
                        'CCodi' => $n['NovCCodi'],
                        'CDesc' => $n['NovCDesc'],
                    );
                }
            }
        }
    }
    if ($dp['getONov']) {
        if (($stmtONov)) {
            $arrONov = filtrarObjetoArr2($stmtONov, 'FicLega', 'Fecha', $v['FicLega'], $v['Fecha']);
            foreach ($arrONov as $key => $vo) {
                $dataONov[] = array(
                    'ONov'  => $vo['FicONov'],
                    'Valor' => ($vo['ONovTipo']) ? decimalToTime($vo['FicValor']) : $vo['FicValor'],
                    'ObsN'  => $vo['FicObsN'],
                    'Desc'  => $vo['ONovDesc'],
                    'Tipo'  => $vo['ONovTipo'],
                    'TipoS' => $vo['Tipo'],
                );
            }
        }
    }
    if ($dp['getHor']) {
        if (($stmtHoras)) {
            if (horaMin($v['FicHsTr'] > 0)) {
                $dataHora = filtrarObjetoArr2($stmtHoras, 'FicLega', 'Fecha', $v['FicLega'], $v['Fecha']);
                
                foreach ($dataHora as $key => $h) {
                    $dataHoras[] = array(
                        'Hora'   => $h['FicHora'],
                        'Desc'   => $h['THoDesc'],
                        'Desc2'  => $h['THoDesc2'],
                        'ID'     => $h['THoID'],
                        'Colu'   => $h['THoColu'],
                        'Obse'   => $h['FicObse'],
                        'Esta'   => $h['FicEsta'],
                        'Calc'   => $h['HorasCalc'],
                        'Hechas' => $h['HorasHechas'],
                        'Auto'   => $h['HorasAuto'],
                        'Causa' => array(
                            'Cod' => ($h['THoCCodi']) ? $h['THoCCodi'] : '',
                            'Desc' => trim($h['THoCDesc']),
                        )
                    );
                    
                }
            }
        }
    }
    if ($dp['getReg']) {
        if ($v['FicNovA'] == 0) {
        // if ($v['FicNovA'] == 0 && $v['FicCount'] > 0) {
            if ($stmtRegistros) {
                $dataFichada = filtrarObjetoArr2($stmtRegistros, 'RegLega', 'Fecha', $v['FicLega'], $v['Fecha']);
                foreach ($dataFichada as $key => $r) {
                    $esta = ($r['RegHora'] == $r['RegHoRe']) ? '' : 'Modificada';
                    $dataFichadas[] = array(
                        'Tarj' => $r['RegTarj'],
                        // 'Fech' => $r['RegFech']->format('Y-m-d'), 
                        'FeRe' => fechFormat($r['RegFeRe'], 'Y-m-d'),
                        'Hora' => $r['RegHora'],
                        'FeAs' => fechFormat($r['RegFeAs'],'Y-m-d'),
                        'HoRe' => $r['RegHoRe'],
                        'Relo' => $r['RegRelo'],
                        'Lect' => $r['RegLect'],
                        'Tipo' => tipoFic($r['RegTipo']),
                        'Esta' => $esta,
                    );
                }
            }
        }
    }
    $horario = array(
        'ent' => $v['FicHorE'],
        'sal' => $v['FicHorS'],
        'des' => $v['FicHorD']
    );
    $CheckNov = array(
        'Aus' => $v['FicNovA'],
        'Sal' => $v['FicNovS'],
        'Tar' => $v['FicNovT'],
        'Inc' => $v['FicNovI'],
    );

    $data[] = array(
        'Lega'     => $v['FicLega'],
        'ApNo'     => trim(str_replace(' ', '', $v['LegApNo'])),
        'Docu'     => ($v['LegDocu'] > 0) ?$v['LegDocu']:'',
        'Cuil'     => $v['LegCUIT'],
        'Fech'     => fechFormat($v['FicFech'], 'Y-m-d' ),
        'Labo'     => $v['FicDiaL'],
        'Feri'     => $v['FicDiaF'],
        'ATra'     => $v['FicHsAT'],
        'Trab'     => ($v['FicHsTr']),
        'Falta'    => $v['FicFalta'],
        // 'FichC'    => $v['FicCount'] ?? '',
        'FichC'    => count($dataFichadas) ?? '',
        'Tur'      => $horario,
        // 'CheckNov' => $CheckNov,
        'Fich'     => $dataFichadas,
        'Nove'     => $dataNov,
        'ONove'    => $dataONov,
        'Horas'    => $dataHoras,
    );
}
$countData    = count($data);
http_response_code(200);
(response($data, $stmtFicCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
