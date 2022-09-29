<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(400);
    (response(array(), 0, 'Invalid Request Method: ' . $_SERVER['REQUEST_METHOD'], 400, $time_start, 0, $idCompany));
    exit;
}

$wc = '';

$dp = ($_REQUEST); // dataPayload
$dp = file_get_contents("php://input");

if (strlen($dp) > 0 && isValidJSON($dp)) {
    $dp = json_decode($dp, true);
} else {
    isValidJSON($dp);
    http_response_code(400);
    (response(array(), 0, 'Invalid json Payload', 400, $time_start, 0, $idCompany));
}

$start  = start();
$length = length();

$dp['getDatos']    = ($dp['getDatos']) ?? '';
$dp['getDatos']    = vp($dp['getDatos'], 'getDatos', 'int01', 1); // Traer Datos
$dp['getLiqui']    = ($dp['getLiqui']) ?? '';
$dp['getLiqui']    = vp($dp['getLiqui'], 'getLiqui', 'int01', 1); // Traer Liquidacion
$dp['getEstruct']  = ($dp['getEstruct']) ?? '';
$dp['getEstruct']  = vp($dp['getEstruct'], 'getEstruct', 'int01', 1); // Traer Estructura
$dp['getHorarios'] = ($dp['getHorarios']) ?? '';
$dp['getHorarios'] = vp($dp['getHorarios'], 'getHorarios', 'int01', 1); // Traer Horarios
$dp['getControl']  = ($dp['getControl']) ?? '';
$dp['getControl']  = vp($dp['getControl'], 'getControl', 'int01', 1); // Traer Control y Procesos
$dp['getAcceso']  = ($dp['getAcceso']) ?? '';
$dp['getAcceso']  = vp($dp['getAcceso'], 'getAcceso', 'int01', 1); // Traer Acceso

$dp['Nume']  = ($dp['Nume']) ?? [];
$dp['Nume']  = vp($dp['Nume'], 'Nume', 'intArray', 11);
$dp['Docu']  = ($dp['Docu']) ?? [];
$dp['Docu']  = vp($dp['Docu'], 'Docu', 'intArray', 11);
$dp['Baja']  = ($dp['Baja']) ?? [];
$dp['Baja']  = vp($dp['Baja'], 'Baja', 'numArray01', 1);
$dp['IntExt']  = ($dp['IntExt']) ?? [];
$dp['IntExt']  = vp($dp['IntExt'], 'IntExt', 'numArray01', 1);
$dp['ApNo'] = $dp['ApNo'] ?? '';
$dp['ApNo'] = vp($dp['ApNo'], 'ApNo', 'str', 40);
$dp['ApNoNume'] = $dp['ApNoNume'] ?? '';
$dp['ApNoNume'] = vp($dp['ApNoNume'], 'ApNoNume', 'str', 40);

// $dp['ID']  = ($dp['ID']) ?? [];
// $dp['ID']  = vp($dp['ID'], 'ID', 'strArray', 3);

// $dp['Desc'] = $dp['Desc'] ?? '';
// $dp['Desc'] = vp($dp['Desc'], 'Desc', 'str', 40);

$arrDPPersonal = array(
    'Nume'     => $dp['Nume'], // Codigo de Horario {int} {array}
    'ApNo'     => $dp['ApNo'], // Nombre y apellido {string}
    'Docu'     => $dp['Docu'], // Documento {string}
    'ApNoNume' => $dp['ApNoNume'], // Nombre y apellido y Legajo {string}
    'IntExt'   => $dp['IntExt'], // Tipo de legajo. Interno, Externo {int} {array}
);
$arrDPPersonalBaja = array(
    'Baja' => $dp['Baja'], // Codigo de Horario {int} {array}
);
$arrDPSTR = array(
    // 'Desc'  => $dp['Desc'], // Descripcion de Horario {int} {array}
);

foreach ($arrDPPersonal as $key => $per) {
    $e = array();
    if (is_array($per)) {
        $v = '';
        $e = array_filter($per, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= " AND PERSONAL.Leg$key IN ($e)";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= " AND PERSONAL.Leg$key = '$v'";
                    }
                }
            }
        }
    } else {
        if ($per) {
            if ($key == 'ApNoNume') {
                $wc .= " AND CONCAT(PERSONAL.LegApNo, PERSONAL.LegNume) LIKE '%$per%'";
            } else if ($key == 'ApNo') {
                $wc .= " AND PERSONAL.Leg$key LIKE '%$per%'";
            } else {
                $wc .= " AND PERSONAL.Leg$key = '$per'";
            }
        }
    }
}
foreach ($arrDPPersonalBaja as $key => $baja) {
    $e = array();
    if (is_array($baja)) {
        $v = '';
        $e = array_filter($baja, function ($v) {
            return ($v !== false && !is_null($v) && ($v != '' || $v == '0'));
        });
        $e = array_unique($e);
        if (($e)) {
            if (count($e) > 1) {
                $e = "'" . implode("','", $e) . "'";
                $wc .= "";
            } else {
                foreach ($e as $v) {
                    if ($v !== NULL) {
                        $wc .= ($v == 0) ? " AND PERSONAL.LegFeEg = '17530101'" : '';
                        $wc .= ($v == 1) ? " AND PERSONAL.LegFeEg != '17530101'" : '';
                    }
                }
            }
        }
    } else {
        if ($v) {
            $wc .= " AND PERSONAL.LegFeEg = '$v'";
        }
    }
}
foreach ($arrDPSTR as $key => $v) {

    if (is_array($v)) {
        if ($e = array_filter($v)) {
            if ($e) {
                if (count($e) > 1) {
                    $e = "'" . implode("','", $e) . "'";
                    $wc .= " AND HORARIOS.$key IN ($e)";
                } else {
                    foreach ($e as $v) {
                        if ($v !== NULL) {
                            $wc .= " AND HORARIOS.$key = '$v'";
                        }
                    }
                }
            }
        }
    } else {
        if ($v) {
            if ($key == 'HorDesc') {
                $wc .= " AND HORARIOS.Hor$key LIKE '%$v%'";
            } else {
                $wc .= " AND HORARIOS.Hor$key = '$v'";
            }
        }
    }
}
// print_r($wc).exit;

$columnas[] = "LegNume,LegApNo,LegIntExt,PERSONAL.FechaHora";

$joinData = '';

/** Datos */
if ($dp['getDatos']) {
    $joinData .= " INNER JOIN NACIONES ON PERSONAL.LegNaci = NACIONES.NacCodi";
    $joinData .= " INNER JOIN PROVINCI ON PERSONAL.LegProv = PROVINCI.ProCodi";
    $joinData .= " INNER JOIN LOCALIDA ON PERSONAL.LegLoca = LOCALIDA.LocCodi";
    $columnas[] = "LegTDoc, dbo.fn_TipoDeDocumento(LegTDoc) as LegTDocStr,LegDocu,LegCUIT,LegDomi,LegDoNu,LegDoPi,LegDoDP,LegDoOb,LegCOPO,LegProv,LegLoca,LegTel1,LegTeO1,LegTel2,LegTeO2,LegNaci,LegEsCi, dbo.fn_EstadoCivil(LegEsCi) AS LegEsCiStr, LegSexo, dbo.fn_Sexo(LegSexo) as LegSexoStr,LegFeNa,NacDesc,ProDesc,LocDesc";
}
/** Estructura */
if ($dp['getEstruct']) {
    $joinData .= " INNER JOIN EMPRESAS ON PERSONAL.LegEmpr = EMPRESAS.EmpCodi";
    $joinData .= " INNER JOIN PLANTAS ON PERSONAL.LegPlan = PLANTAS.PlaCodi";
    $joinData .= " INNER JOIN CONVENIO ON PERSONAL.LegConv = CONVENIO.ConCodi";
    $joinData .= " INNER JOIN SECTORES ON PERSONAL.LegSect = SECTORES.SecCodi";
    $joinData .= " INNER JOIN SECCION ON PERSONAL.LegSec2 = SECCION.Se2Codi AND PERSONAL.LegSect = SECCION.SecCodi";
    $joinData .= " INNER JOIN GRUPOS ON PERSONAL.LegGrup = GRUPOS.GruCodi";
    $joinData .= " INNER JOIN SUCURSALES ON PERSONAL.LegSucu = SUCURSALES.SucCodi";
    $joinData .= " INNER JOIN TAREAS ON PERSONAL.LegTareProd = TAREAS.TareCodi";
    $columnas[] = "LegEmpr,LegPlan,LegConv,LegSect,LegSec2,LegGrup,LegSucu,EmpRazon,PlaDesc,ConDesc,SecDesc,Se2Desc,GruDesc,SucDesc,TareDesc,LegTareProd,LegTel3,LegMail";
}

/** Acceso */
if ($dp['getAcceso']) {

    $queryIdentifica = "SELECT IDCodigo,IDFichada,IDTarjeta,IDLegajo,IDVence,IDCap04,IDCap05,IDCap06 FROM IDENTIFICA WHERE IDLegajo > 0";
    $stmtIdentifica = $dbApiQuery($queryIdentifica) ?? '';

    $queryReloHabi = "SELECT RELOHABI.RelReMa, RELOHABI.RelRelo, RELOJES.RelDeRe, RELOJES.RelSeri, RELOHABI.RelGrup FROM RELOHABI INNER JOIN RELOJES ON RELOHABI.RelReMa = RELOJES.RelReMa AND RELOHABI.RelRelo = RELOJES.RelRelo";
    $stmtReloHabi = $dbApiQuery($queryReloHabi) ?? '';
    
    $querPerRelo = "SELECT PERRELO.RelReMa, PERRELO.RelRelo, RELOJES.RelDeRe, RELOJES.RelSeri, PERRELO.RelLega, PERRELO.RelFech, PERRELO.RelFech2 FROM PERRELO
    INNER JOIN RELOJES ON PERRELO.RelReMa = RELOJES.RelReMa AND PERRELO.RelRelo = RELOJES.RelRelo
    WHERE PERRELO.RelLega > 0";
    $stmtPerRelo = $dbApiQuery($querPerRelo) ?? '';

    $joinData .= " LEFT JOIN GRUPCAPT ON PERSONAL.LegGrHa = GRUPCAPT.GHaCodi";
    $columnas[] = "LegGrHa,GHaDesc";
}

/** Control */
if ($dp['getControl']) {
    $joinData .= " INNER JOIN REGLASCH ON PERSONAL.LegRegCH = REGLASCH.RCCodi";
    $joinData .= " LEFT JOIN PERCIERRE ON PERSONAL.LegNume = PERCIERRE.CierreLega";
    $columnas[] = "LegEsta,LegIncTi,LegToTa,LegToSa,LegToIn,LegReTa,LegReIn,LegReSa,LegRegCH,LegPrCo,LegPrSe,LegPrGr,LegPrHo,LegPrRe,LegPrPl,LegValHora,LegNo24,LegPrCosteo,RCDesc,CierreFech";
}

/** Liquidación */
if ($dp['getLiqui']) {
    $columnas[] = "LegTipo,dbo.fn_TipoDePersonal(LegTipo) as LegTipoStr,LegFeIn,LegFeEg";
}

/**Horarios */
if ($dp['getHorarios']) {
    $queryPerHoAlt = "SELECT PERHOALT.LeHALega,PERHOALT.LeHAHora,PERHOALT.FechaHora, HORARIOS.HorDesc, HORARIOS.HorID FROM PERHOALT INNER JOIN HORARIOS ON PERHOALT.LeHAHora=HORARIOS.HorCodi WHERE PERHOALT.LeHALega > 0 ORDER BY PERHOALT.LeHALega";
    $stmtPerHoAlt = $dbApiQuery($queryPerHoAlt) ?? '';
    $columnas[] = "LegHLPlani,LegHoAl,LegHoLi,LegHLDe,LegHLDH,LegHLRo,LegHGDe,LegHGDH,LegHGRo,LegHSDe,LegHSDH,LegHSRo";
}
// print_r($stmtPerHoAlt).exit;

$columnas = implode(',', $columnas);

$query = "SELECT $columnas FROM PERSONAL $joinData WHERE PERSONAL.LegNume > 0";
$queryCount = "SELECT count(1) as 'count' FROM PERSONAL WHERE PERSONAL.LegNume > 0";


if ($wc) {
    $query .= $wc;
    $queryCount .= $wc;
}

$stmtCount = $dbApiQuery($queryCount)[0]['count'] ?? '';

$query .= " ORDER BY PERSONAL.LegNume";
$query .= " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
// print_r($query).exit;

$stmt = $dbApiQuery($query) ?? '';
$_1753 = '1753-01-01 00:00:00.000';
foreach ($stmt  as $key => $v) {

    $Datos          = array();
    $Liquidacion    = array();
    $Estructura     = array();
    $Control        = array();
    $Horarios       = array();
    $Acceso         = array();
    $arrPerHoAlt    = array();
    $arrIdentifica  = array();
    $dataPerHoAlt   = array();
    $dataIdentifica = array();
    $dataReloHabi   = array();
    $arrReloHabi    = array();
    $dataPerRelo   = array();
    $arrPerRelo    = array();

    if ($dp['getDatos']) {

        $Domicilio = '';
        $Domicilio .= ($v['LegDomi']) ? $v['LegDomi'] . '' : '';
        $Domicilio .= ($v['LegDoNu']) ? ' ' . $v['LegDoNu'] : '';
        $Domicilio .= ($v['LegDoPi']) ? ', Piso: ' . $v['LegDoPi'] : '';
        $Domicilio .= ($v['LegDoDP']) ? ', Depto: ' . $v['LegDoDP'] : '';
        $Domicilio .= ($v['LegCOPO']) ? ', CP: ' . $v['LegCOPO'] : '';
        $Domicilio .= ($v['LegLoca']) ? ', ' . $v['LocDesc'] : '';
        $Domicilio .= ($v['LegProv']) ? ', ' . $v['ProDesc'] : '';
        $Domicilio .= ($v['LegDoOb']) ? ', Observ: ' . $v['LegDoOb'] . '.' : '.';

        $Edad    = ($v['LegFeNa'] != $_1753) ? intval(calculaEdad(fechFormat($v['LegFeNa'], 'Y-m-d'))->format('%y')) : '';
        $EdadStr = calculaEdadStr(fechFormat($v['LegFeNa'], 'Y-m-d'));

        $Datos = array(
            "TDoc"    => intval($v['LegTDoc']), // Tipo de Documento 0=DU 1=DNI 2=CI 3=LC 4=LE 5=PAS
            "TDocStr" => $v['LegTDocStr'], // Tipo de Documento String
            "Docu"    => intval($v['LegDocu']), // Documento
            "CUIT"    => $v['LegCUIT'], // CUIT
            "Naci"    => intval($v['LegNaci']), // Código de Nacionalidad
            "NaciStr" => $v['NacDesc'], // Nacionalidad
            "EsCi"    => intval($v['LegEsCi']), // Estado Civil 0=Soltero/a 1=Casado/a 2=Viudo/a 3=Divorciado/a
            "EsCiStr" => $v['LegEsCiStr'], // Estado Civil String
            "Sexo"    => intval($v['LegSexo']), // Sexo
            "SexoStr" => $v['LegSexoStr'], // Sexo
            "FeNa"    => fechFormat($v['LegFeNa'], 'Y-m-d'), // Fecha de Nacimiento
            "Edad"    => $Edad, // Edad
            "EdadStr" => $EdadStr, // Edad String
            "Domi"    => $v['LegDomi'], // Dirección Calle
            "DoNu"    => $v['LegDoNu'], // Dirección Número
            "DoPi"    => $v['LegDoPi'], // Dirección Piso
            "DoDP"    => $v['LegDoDP'], // Dirección Depto
            "DoOb"    => $v['LegDoOb'], // Dirección Observacion
            "COPO"    => $v['LegCOPO'], // Dirección Código Postal
            "Prov"    => intval($v['LegProv']), // Dirección Código Provincia
            "ProvStr" => $v['ProDesc'], // Dirección Provincia
            "Loca"    => intval($v['LegLoca']), // Dirección Código Localidad
            "LocaStr" => $v['LocDesc'], // Dirección Localidad
            "DomiStr" => $Domicilio, // Domicilio String
            "Tel1"    => $v['LegTel1'], // Telefono 1
            "TeO1"    => $v['LegTeO1'], // Observacion Telefono 1
            "Tel2"    => $v['LegTel2'], // Telefono 2
            "TeO2"    => $v['LegTeO2'], // Observacion Telefono2
        );
    }
    if ($dp['getLiqui']) {
        $Anti    = ($v['LegFeIn'] != $_1753) ? intval(calculaEdad(fechFormat($v['LegFeIn'], 'Y-m-d'))->format('%y')) : '';
        $AntiStr = calculaEdadStr(fechFormat($v['LegFeIn'], 'Y-m-d'));
        $Liquidacion = array(
            "Tipo"    => intval($v['LegTipo']), // Tipo de Legajo 0=Mensual 1=Jornal
            "TipoStr" => $v['LegTipoStr'], // Tipo de Legajo String
            "FeIn"    => fechFormat($v['LegFeIn'], 'Y-m-d'), // Fecha de Ingreso
            "FeEg"    => ($v['LegFeEg'] != $_1753) ? fechFormat($v['LegFeEg'], 'Y-m-d') : '', // Fecha de Egreso
            "Baja"    => ($v['LegFeEg'] == $_1753) ? 0 : 1, // Si esta de baja
            "BajaStr" => ($v['LegFeEg'] == $_1753) ? 'No' : 'Si', // Si esta de baja Str
            "Antig"    => $Anti, // Antiguedad
            "AntigStr" => $AntiStr, // Antiguedad String
        );
    }
    if ($dp['getEstruct']) {
        $Estructura   = array(
            "Empr"     => intval($v['LegEmpr']), // Código de Empresa; 0 ó valor según Tabla
            "EmprStr"  => $v['EmpRazon'], // Nombre de la empresa
            "Plan"     => intval($v['LegPlan']), // Código de planta; 0 ó valor según Tabla
            "PlanStr"  => trim($v['PlaDesc']), // Nombre de la Planta
            "Conv"     => intval($v['LegConv']), // Código de Convenio; 0 ó valor según Tabla
            "ConvStr"  => trim($v['ConDesc']), // Nombre del convenio
            "Sect"     => intval($v['LegSect']), // Código de Sector; 0 ó valor según Tabla
            "SectStr"  => trim($v['SecDesc']), // Nombre del sector
            "Sec2"     => intval($v['LegSec2']), // código de Sección; 0 ó valor según Tabla
            "Sec2Str"  => trim($v['Se2Desc']), // Nombre de la seccion
            "Grup"     => intval($v['LegGrup']), // Código de Grupo; 0 ó valor según Tabla
            "GrupStr"  => trim($v['GruDesc']), // Nombre del Grupo
            "Sucu"     => intval($v['LegSucu']), // Código de sucursal; 0 ó valor según Tabla
            "SucuStr"  => trim($v['SucDesc']), // Nombre de la sucursal
            "TareProd" => intval($v['LegTareProd']), // Código de Tarea de Produccion; 0 ó valor según Tabla
            "TareStr"  => trim($v['TareDesc']), // Nombre de la tarea
            "Tel"      => trim($v['LegTel3']), // Telefono Empresa
            "Mail"     => trim($v['LegMail']), // Correo electrónico Empresa
        );
    }
    if ($dp['getHorarios']) {

        if (($stmtPerHoAlt)) {
            $arrPerHoAlt = filtrarObjetoArr($stmtPerHoAlt, 'LeHALega', $v['LegNume']);
            foreach ($arrPerHoAlt as $key => $n) {
                $dataPerHoAlt[] = array(
                    'HAHora'  => $n['LeHAHora'],
                    'HorDesc' => $n['HorDesc'],
                    'HorID'   => $n['HorID'],
                );
            }
        }

        $Horarios = array(
            "HoAl"    => $v['LegHoAl'], // Tipo de asignacion horaria; 0 = Segun Asignacion 1 = Alternativos Segun Fichadas
            "HoAlStr" => LegHoAlStr($v['LegHoAl']), // Tipo de asignacion String
            "HorAlt"  => $dataPerHoAlt,
            "HoLi"    => ($v['LegHoLi']), // Limite de horario para cambio a alternativo
            "HLPlani" => $v['LegHLPlani'], // Usar Planificación 0=Deshabilitar 1=Habilitar
            "HLDe"    => intval($v['LegHLDe']), // Asignación por Legajo desde una Fecha; 0=Deshabilitar 1=Habilitar
            "HLDH"    => intval($v['LegHLDH']), // Asignación por Legajo desde-hasta una Fecha; 0=Deshabilitar 1=Habilitar
            "HLRo"    => intval($v['LegHLRo']), // Asignación por Legajo de Rotación; 0=Deshabilitar 1=Habilitar
            "HGDe"    => intval($v['LegHGDe']), // Asignación por Grupo desde una Fecha; 0=Deshabilitar 1=Habilitar
            "HGDH"    => intval($v['LegHGDH']), // Asignación por Grupo desde-hasta una Fecha; 0=Deshabilitar 1=Habilitar
            "HGRo"    => intval($v['LegHGRo']), // Asignación por Grupo de Rotación; 0=Deshabilitar 1=Habilitar
            "HSDe"    => intval($v['LegHSDe']), // Asignación por Sector desde una Fecha; 0=Deshabilitar 1=Habilitar
            "HSDH"    => intval($v['LegHSDH']), // Asignación por Sector desde-hasta una Fecha; 0=Deshabilitar 1=Habilitar
            "HSRo"    => intval($v['LegHSRo']) // Asignación por Sector de Rotación; 0=Deshabilitar 1=Habilitar
        );
    }
    if ($dp['getControl']) {
        $Control = array(
            "Esta"     => intval($v['LegEsta']), // No controla horario 0 = si 1 = no
            "EstaStr"  => (intval($v['LegEsta']) == 0) ? "Controla horario" : "No controla horario", // No controla horario 0 = si 1 = no
            "IncTi"    => intval($v['LegIncTi']),
            "IncTiStr" => IncTiStr($v['LegIncTi']),
            "ToTa"     => intval($v['LegToTa']), // Tolerancia tarde
            "ToSa"     => intval($v['LegToSa']), // Tolerancia Salida
            "ToIn"     => intval($v['LegToIn']), // Tolerancia Incumplimiento
            "ReTa"     => intval($v['LegReTa']), // Recorte de Horas Tarde
            "ReIn"     => intval($v['LegReIn']), // Recorte de Horas Incumpliemnto
            "ReSa"     => intval($v['LegReSa']), // Recorte de Horas Salida
            "RegCH"    => intval($v['LegRegCH']), // Código Regla de Control 0 ó valor según Tabla
            "RegCHStr" => $v['RCDesc'], // Regla de Control String
            "PrCo"     => intval($v['LegPrCo']), // Procesar por Convenio 0=No 1=Si
            "PrSe"     => intval($v['LegPrSe']), // Procesar por Sector 0=No 1=Si
            "PrGr"     => intval($v['LegPrGr']), // Procesar por Grupo 0=No 1=Si
            "PrHo"     => intval($v['LegPrHo']), // Procesar por Horario 0=No 1=Si
            "PrRe"     => intval($v['LegPrRe']), // Procesar por Regla de control 0=No 1=Si
            "PrPl"     => intval($v['LegPrPl']), // Procesar por Planta 0=No 1=Si
            "ValHora"  => $v['LegValHora'], // Valor Hora
            "No24"     => intval($v['LegNo24']), // No Partir Novedades 24Hs  0=No 1=Si
            "PrCosteo" => intval($v['LegPrCosteo']), // Procesar costeo  0=No 1=Si
            "Cierre" => ($v['CierreFech'] != $_1753) ? fechFormat($v['CierreFech'], 'Y-m-d') : '', // Fecha de cierre
        );
    }
    if ($dp['getAcceso']) {

        if (($stmtIdentifica)) {
            $arrIdentifica = filtrarObjetoArr($stmtIdentifica, 'IDLegajo', $v['LegNume']);
            foreach ($arrIdentifica as $key => $n) {
                $dataIdentifica [] = array(
                    'Codigo'   => ($n['IDCodigo']),
                    'Fichada'  => intval($n['IDFichada']),
                    'Tarjeta'  => $n['IDTarjeta'],
                    'Legajo'   => intval($n['IDLegajo']),
                    'Vence'    => ($n['IDVence'] != $_1753) ? fechFormat($n['IDVence'], 'Y-m-d'):'',
                    'Cap04'    => intval($n['IDCap04']),
                    'Cap05'    => intval($n['IDCap05']),
                    'Cap06'    => intval($n['IDCap06']),
                );
            }
        }
        if (($stmtReloHabi)) {
            $arrReloHabi = filtrarObjetoArr($stmtReloHabi, 'RelGrup', $v['LegGrHa']);
            foreach ($arrReloHabi as $key => $n) {
                $dataReloHabi [] = array(
                    'ReMa' => intval($n['RelReMa']),
                    'Relo' => intval($n['RelRelo']),
                    'DeRe' => trim($n['RelDeRe']),
                    'Seri' => trim($n['RelSeri'])
                );
            }
        }
        if (($stmtPerRelo)) {
            $arrPerRelo = filtrarObjetoArr($stmtPerRelo, 'RelLega', $v['LegNume']);
            foreach ($arrPerRelo as $key => $n) {
                $dataPerRelo [] = array(
                    'ReMa'  => intval($n['RelReMa']),
                    'Relo'  => intval($n['RelRelo']),
                    'DeRe'  => trim($n['RelDeRe']),
                    'Seri'  => trim($n['RelSeri']),
                    'Lega'  => intval($n['RelLega']),
                    'Desde'  => ($n['RelFech'] != $_1753) ? fechformat($n['RelFech'], 'Y-m-d'):'',
                    'Vence' => ($n['RelFech2'] != $_1753) ? fechformat($n['RelFech2'], 'Y-m-d'):'',
                );
            }
        }

        $Acceso = array(
            "GrHa"     => $v['LegGrHa'], // Grupo de Capturadores
            "GrHaStr"  => $v['GHaDesc'], // Grupo de Capturadores String
            "Identif"  => $dataIdentifica, // Array de identificadores 
            "ReloHabi" => $dataReloHabi, // Array de relojes habilitados
            "PerRelo"  => $dataPerRelo, // Array de relojes habilitados con vencimiento
        );
    }
    
    $data[] = array(
        "Lega"         => intval($v['LegNume']), // Número de Legajo
        "ApNo"         => $v['LegApNo'], // Apellido y Nombre
        "IntExt"       => $v['LegIntExt'], // Tipo de Legajo
        "IntExtString" => ($v['LegIntExt'] == '0') ? 'Interno' : 'Externo', // Tipo de Legajo
        "FechaHora"    => fechFormat($v['FechaHora'], 'Y-m-d H:i:s'), // lasta update
        "Datos"        => $Datos, // Datos del Legajos
        "Liquidacion"  => $Liquidacion, // Datos de liquidacion
        "Estructura"   => $Estructura, // Estructura organizacional del Legajo
        "Control"      => $Control, // Datos de Control
        "Horarios"     => $Horarios, // Datos de Horarios
        "Acceso" => $Acceso // Datos de Acceso. 
    );
}

if (empty($stmt)) {
    http_response_code(200);
    (response('', 0, 'OK', 200, $time_start, 0, $idCompany));
    exit;
}
$countData    = count($data);
http_response_code(200);
(response($data, $stmtCount, 'OK', 200, $time_start, $countData, $idCompany));
exit;
