<?php
$totalRecords = $data = $count = $qTotales = array();
$params = $_REQUEST;
// sleep(1);
$_SESSION['ID_CLIENTE']   = $_SESSION['ID_CLIENTE'] ?? $params['idCliente'];
$params['start']     = $params['start'] ?? 0;
$params['length']    = $params['length'] ?? 5;
$params['sinTar']    = $params['sinTar'] ?? '';
$params['presentes'] = $params['presentes'] ?? '';
$params['FiltroAsignTarFechas'] = ($params['FiltroAsignTarFechas']) ?? print_r(json_encode(array("data" => array()))).exit;
$FiltroAsignTarFechas = dr_($params['FiltroAsignTarFechas']);
$tiempo_inicio = microtime(true);
$w_c = $sqlTot = $sqlRec = "";
($params['testTable'] ?? '') ? $_SESSION['ID_CLIENTE'] = 1 : '';

// $today = date('Y-m-d');
// $w_c .= (!empty($params['search']['value'])) ?  " AND CONCAT_WS(' ', TareID) = '" . $params['search']['value'] . "'" : '';
$w_c .= " AND usr.cliente='$_SESSION[ID_CLIENTE]' AND mrol.modulo=43 AND usr.estado='0'";
// $w_c .= " AND DATE_FORMAT(ptar.TareIni, '%Y-%m-%d') != '$FiltroAsignTarFechas'";
$w_c .= ($params['sinTar']) ? " AND usr.id NOT IN (SELECT pt.TareResp FROM proy_tareas pt WHERE DATE_FORMAT(pt.TareIni, '%Y-%m-%d') = '$FiltroAsignTarFechas')" : " AND usr.id IN (SELECT pt.TareResp FROM proy_tareas pt WHERE DATE_FORMAT(pt.TareIni, '%Y-%m-%d') = '$FiltroAsignTarFechas') AND DATE_FORMAT(ptar.TareIni, '%Y-%m-%d') = '$FiltroAsignTarFechas'";
$w_c .= " GROUP BY usr.id";

// print_r($w_c).exit;