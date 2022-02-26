<?php
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
E_ALL();
require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';
timeZone();
$_POST['datos'] = $_POST['datos'] ?? '';

if(empty($_POST['datos'])){
    echo json_encode(['error' => 'No se recibieron datos']);
    exit;
}

$Datos = (json_decode($_POST['datos'], true));
$Legajo = $Datos['legajo'];
$ApNo   = $Datos['nombre'];
$Tabla  = $Datos['tabla'];

$aTur = intval($_SESSION["ABM_ROL"]['aTur']);
$bTur = intval($_SESSION["ABM_ROL"]['bTur']);
$mTur = intval($_SESSION["ABM_ROL"]['mTur']);
$aCit = intval($_SESSION["ABM_ROL"]['aCit']);
$bCit = intval($_SESSION["ABM_ROL"]['bCit']);
$mCit = intval($_SESSION["ABM_ROL"]['mCit']);

$params = $columns = $totalRecords = $data = array();
$params = $_REQUEST;
$where_condition = $sqlTot = $sqlRec = $TotalCit = $sql_query = $queryRecords = "";

$ListaHorarios = $_SESSION['ListaHorarios'];
$filtroListaHorarios = '';
if ($ListaHorarios  != "-") {
    $ListaHorarios1 = str_replace(32768, 0, $ListaHorarios);
    $filtroListaHorarios = " AND HORARIOS.HorCodi IN ($ListaHorarios1)";
}
$filtroListaRotaciones = '';
$ListaRotaciones = $_SESSION['ListaRotaciones'];
if ($ListaRotaciones  != "-") {
    $filtroListaRotaciones = " AND ROTACION.RotCodi IN ($ListaRotaciones)";
}
// print_r($Tabla);
switch ($Tabla) {
    case 'Desde':
        $sql_cit = "SELECT SUM(1) as 'cant' FROM CITACION WHERE CITACION.CitLega = '$Legajo'";
        $rs = sqlsrv_query($link, $sql_cit);
        while ($a = sqlsrv_fetch_array($rs)) {
            $TotalCit = $a['cant'];
        }
        sqlsrv_free_stmt($rs);

        $sql_query = "SELECT HORALE1.Ho1Fech as 'fecha', HORALE1.Ho1Hora as 'codHor', HORARIOS.HorDesc as 'horario' FROM HORALE1 INNER JOIN HORARIOS ON HORALE1.Ho1Hora = HORARIOS.HorCodi WHERE HORALE1.Ho1Lega = $Legajo ORDER BY HORALE1.Ho1Fech DESC";
        // print_r($sql_query); exit;
        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;

        if (isset($where_condition) && $where_condition != '') {
            $sqlTot .= $where_condition;
            $sqlRec .= $where_condition;
        }
        $param  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $fecha   = $r['fecha'];
            $codHor  = $r['codHor'];
            $horario = $r['horario'];
            $data[] = array(
                'Fecha'    => $fecha->format('d/m/Y'),
                'FechaStr' => intval($fecha->format('Ymd')),
                'CodHor'   => $codHor,
                'Horario'  => $horario,
                'Legajo'   => $Legajo,
                'ApNo'     => $ApNo
            );
        }
        break;
    case 'DesdeHasta':
        $sql_query = "SELECT HORALE2.Ho2Fec1, HORALE2.Ho2Fec2, HORALE2.Ho2Hora, HORARIOS.HorDesc FROM HORALE2 INNER JOIN HORARIOS ON HORALE2.Ho2Hora = HORARIOS.HorCodi WHERE HORALE2.Ho2Lega = $Legajo ORDER BY HORALE2.Ho2Fec2 DESC";
        // print_r($sql_query); exit;
        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;

        if (isset($where_condition) && $where_condition != '') {
            $sqlTot .= $where_condition;
            $sqlRec .= $where_condition;
        }
        $param  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $Ho2Fec1 = $r['Ho2Fec1'];
            $Ho2Fec2 = $r['Ho2Fec2'];
            $Ho2Hora = $r['Ho2Hora'];
            $HorDesc = $r['HorDesc'];
            $data[] = array(
                'Ho2Fec1' => $Ho2Fec1->format('d/m/Y'),
                'Ho2Fec2' => $Ho2Fec2->format('d/m/Y'),
                'Ho2Hora' => $Ho2Hora,
                'HorDesc' => $HorDesc,
                'Legajo'  => $Legajo,
                'ApNo'    => $ApNo,
            );
        }
        break;
    case 'Citacion':
        $sql_cit = "SELECT SUM(1) as 'cant' FROM CITACION WHERE CITACION.CitLega = '$Legajo'";
        $rs = sqlsrv_query($link, $sql_cit);
        while ($a = sqlsrv_fetch_array($rs)) {
            $TotalCit = $a['cant'];
        }
        sqlsrv_free_stmt($rs);

        $sql_query = "SELECT CITACION.CitLega, CITACION.CitFech, CITACION.CitEntra, CITACION.CitSale, CITACION.CitDesc FROM CITACION WHERE CITACION.CitLega = $Legajo ORDER BY CITACION.CitFech DESC";
        // print_r($sql_query); exit;
        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;

        $param  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $CitLega  = $r['CitLega'];
            $CitFech  = $r['CitFech'];
            $CitEntra = $r['CitEntra'];
            $CitSale  = $r['CitSale'];
            $CitDesc  = $r['CitDesc'];
            $data[] = array(
                'CitFech'  => $CitFech->format('d/m/Y'),
                'CitLega'  => $CitLega,
                'CitEntra' => $CitEntra,
                'CitSale'  => $CitSale,
                'CitDesc'  => $CitDesc,
                'ApNo'     => $ApNo,
            );
        }
        break;
    case 'Rotacion':
        $sql_query = "SELECT ROTALEG.RoLLega, ROTALEG.RoLFech, ROTALEG.RoLRota, ROTACION.RotDesc, ROTALEG.RoLDias, ROTALEG.RoLVenc FROM ROTALEG
        INNER JOIN ROTACION ON ROTALEG.RoLRota = ROTACION.RotCodi WHERE ROTALEG.RoLLega = $Legajo ORDER BY ROTALEG.RoLFech DESC";
        // print_r($sql_query); exit;
        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;

        if (isset($where_condition) && $where_condition != '') {
            $sqlTot .= $where_condition;
            $sqlRec .= $where_condition;
        }
        $param  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);
        
        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $RoLLega = $r['RoLLega'];
            $RoLFech = $r['RoLFech'];
            $RoLRota = $r['RoLRota'];
            $RotDesc = $r['RotDesc'];
            $RoLDias = $r['RoLDias'];
            $RoLVenc = $r['RoLVenc'];
            $data[] = array(
                'RoLLega' => $RoLLega,
                'RoLFech' => $RoLFech->format('d/m/Y'),
                'RoLRota' => $RoLRota,
                'RotDesc' => $RotDesc,
                'RoLDias' => $RoLDias,
                'RoLVenc' => ($RoLVenc->format('d/m/Y') == '31/12/2099') ? '' : $RoLVenc->format('d/m/Y'),
                'ApNo'    => $ApNo,
            );
        }
        break;
    case 'RotaDeta':
        $Datos = (json_decode($_POST['datos'], true));
        $Rota  = $Datos['RoLRota'];
        $sql_query = "SELECT ROTACIO1.RotItem, ROTACION.RotCodi, ROTACION.RotDesc, ROTACIO1.RotDias, ROTACIO1.RotHora, HORARIOS.HorDesc
        FROM ROTACIO1
        INNER JOIN ROTACION ON ROTACIO1.RotCodi = ROTACION.RotCodi
        INNER JOIN HORARIOS ON ROTACIO1.RotHora = HORARIOS.HorCodi
        WHERE ROTACIO1.RotCodi = $Rota
        ORDER BY ROTACIO1.RotItem";
        $sqlRec .= $sql_query;
        $param  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $RotItem = $r['RotItem'];
            $RotCodi = $r['RotCodi'];
            $RotDesc = $r['RotDesc'];
            $RotDias = $r['RotDias'];
            $RotHora = $r['RotHora'];
            $HorDesc = $r['HorDesc'];
            $data[] = array(
                'RotItem' => $RotItem,
                'RotCodi' => $RotCodi,
                'RotDesc' => $RotDesc,
                'RotDias' => $RotDias,
                'RotHora' => $RotHora,
                'HorDesc' => $HorDesc,
            );
        }
        sqlsrv_free_stmt($queryRecords);
        sqlsrv_close($link);
        echo json_encode($data);
        exit;
        break;
    case 'Horario':
        $Datos = (json_decode($_POST['datos'], true));
        $HorCodi  = $Datos['HorCodi'];
        $sql_query = "SELECT
        [HorCodi] ,[HorDesc] ,[HorID] , [HorDomi] ,[HorLune] ,[HorMart] ,[HorMier] ,[HorJuev] ,[HorVier] ,[HorSaba] ,[HorFeri] ,[HorDoDe] ,[HorLuDe] ,[HorMaDe] ,[HorMiDe] ,[HorJuDe] ,[HorViDe] ,[HorSaDe] ,[HorFeDe] ,[HorDoHa] ,[HorLuHa] ,[HorMaHa] ,[HorMiHa] ,[HorJuHa] ,[HorViHa] ,[HorSaHa] ,[HorFeHa] ,[HorDoRe] ,[HorLuRe] ,[HorMaRe] ,[HorMiRe] ,[HorJuRe] ,[HorViRe] ,[HorSaRe] ,[HorFeRe] ,[HorDoLi] ,[HorLuLi] ,[HorMaLi] ,[HorMiLi] ,[HorJuLi] ,[HorViLi] ,[HorSaLi] ,[HorFeLi] ,[HorDoHs] ,[HorLuHs] ,[HorMaHs] ,[HorMiHs] ,[HorJuHs] ,[HorViHs] ,[HorSaHs] ,[HorFeHs] FROM HORARIOS WHERE HorCodi = $HorCodi";
        $sqlRec .= $sql_query;
        $param  = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $data = array(
                'HorCodi' => $r['HorCodi'],
                'HorDesc' => $r['HorDesc'],
                'HorID'   => $r['HorID'],
                'HorDomi' => $r['HorDomi'],
                'HorLune' => $r['HorLune'],
                'HorMart' => $r['HorMart'],
                'HorMier' => $r['HorMier'],
                'HorJuev' => $r['HorJuev'],
                'HorVier' => $r['HorVier'],
                'HorSaba' => $r['HorSaba'],
                'HorFeri' => $r['HorFeri'],
                'HorDoDe' => $r['HorDoDe'],
                'HorLuDe' => $r['HorLuDe'],
                'HorMaDe' => $r['HorMaDe'],
                'HorMiDe' => $r['HorMiDe'],
                'HorJuDe' => $r['HorJuDe'],
                'HorViDe' => $r['HorViDe'],
                'HorSaDe' => $r['HorSaDe'],
                'HorFeDe' => $r['HorFeDe'],
                'HorDoHa' => $r['HorDoHa'],
                'HorLuHa' => $r['HorLuHa'],
                'HorMaHa' => $r['HorMaHa'],
                'HorMiHa' => $r['HorMiHa'],
                'HorJuHa' => $r['HorJuHa'],
                'HorViHa' => $r['HorViHa'],
                'HorSaHa' => $r['HorSaHa'],
                'HorFeHa' => $r['HorFeHa'],
                'HorDoRe' => $r['HorDoRe'],
                'HorLuRe' => $r['HorLuRe'],
                'HorMaRe' => $r['HorMaRe'],
                'HorMiRe' => $r['HorMiRe'],
                'HorJuRe' => $r['HorJuRe'],
                'HorViRe' => $r['HorViRe'],
                'HorSaRe' => $r['HorSaRe'],
                'HorFeRe' => $r['HorFeRe'],
                'HorDoLi' => $r['HorDoLi'],
                'HorLuLi' => $r['HorLuLi'],
                'HorMaLi' => $r['HorMaLi'],
                'HorMiLi' => $r['HorMiLi'],
                'HorJuLi' => $r['HorJuLi'],
                'HorViLi' => $r['HorViLi'],
                'HorSaLi' => $r['HorSaLi'],
                'HorFeLi' => $r['HorFeLi'],
                'HorDoHs' => $r['HorDoHs'],
                'HorLuHs' => $r['HorLuHs'],
                'HorMaHs' => $r['HorMaHs'],
                'HorMiHs' => $r['HorMiHs'],
                'HorJuHs' => $r['HorJuHs'],
                'HorViHs' => $r['HorViHs'],
                'HorSaHs' => $r['HorSaHs'],
                'HorFeHs' => $r['HorFeHs']
            );
        }
        sqlsrv_free_stmt($queryRecords);
        sqlsrv_close($link);
        echo json_encode($data);
        exit;
        break;
    case 'ListHorarios':
        $sql_query = "SELECT HorCodi, HorDesc, HorID FROM HORARIOS WHERE HorCodi >=0 $filtroListaHorarios";
        // print_r($sql_query); exit;
        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;

        if (!empty($params['search']['value'])) {
            $where_condition .=    " AND ";
            $where_condition .= " (dbo.fn_Concatenar(HorCodi,HorDesc)) collate SQL_Latin1_General_CP1_CI_AS LIKE '%" . $params['search']['value'] . "%'";
        }

        if (isset($where_condition) && $where_condition != '') {
            $sqlTot .= $where_condition;
            $sqlRec .= $where_condition;
        }
        $param        = array();
        $options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        $sqlRec .= " ORDER BY HorCodi OFFSET " . $params['start'] . " ROWS FETCH NEXT " . $params['length'] . " ROWS ONLY";
        $queryTot = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);
        // print_r($sqlRec);exit;

        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $data[] = array(
                'HorCodi' => $r['HorCodi'],
                'HorDesc' => $r['HorDesc'],
                'HorID'   => $r['HorID']
            );
        }
        header("Content-Type: application/json");
        $json_data = array(
            "draw"            => intval($params['draw']),
            "recordsTotal"    => intval($totalRecords),
            "recordsFiltered" => intval($totalRecords),
            "data"            => $data
        );
        sqlsrv_free_stmt($queryRecords);
        sqlsrv_close($link);
        echo json_encode($json_data);
        // break;
        exit;
    case 'ListRotaciones':
        $sql_query = "SELECT RotCodi, RotDesc FROM ROTACION WHERE RotCodi >= 0 $filtroListaRotaciones ORDER BY RotCodi";
        // print_r($sql_query); exit;
        $sqlTot .= $sql_query;
        $sqlRec .= $sql_query;

        $param        = array();
        $options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $queryTot     = sqlsrv_query($link, $sqlTot, $param, $options);
        $totalRecords = sqlsrv_num_rows($queryTot);
        $queryRecords = sqlsrv_query($link, $sqlRec, $param, $options);

        while ($r = sqlsrv_fetch_array($queryRecords)) {
            $data[] = array(
                'RotCodi' => $r['RotCodi'],
                'RotDesc' => $r['RotDesc']
            );
        }
        break;
}

sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);

$json_data = array(
    "draw"            => 0,
    "recordsTotal"    => intval($totalRecords),
    "recordsFiltered" => intval($totalRecords),
    "data"            => $data,
    "TotalCit"        => $TotalCit,
    '_aTur'            => $aTur,
    '_bTur'            => $bTur,
    '_mTur'            => $mTur,
    '_aCit'            => $aCit,
    '_bCit'            => $bCit,
    '_mCit'            => $mCit

);
echo json_encode($json_data);
exit;
