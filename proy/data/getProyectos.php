<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
header("Content-Type: application/json");
E_ALL();
$totalRecords = $data = array();
$params = $_REQUEST;

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";
$params['search']['value'] = test_input($params['search']['value']) ?? '';

$where_condition .= (!empty($params['search']['value'])) ?  " AND CONCAT_WS(' ', ProyID, EmpDesc, ProyNom, nombre) LIKE '%" . $params['search']['value'] . "%'" : '';
$params['FiltroEstTipo'] = test_input(($params['FiltroEstTipo']) ?? 'Abierto');

if (($params['ProyFiltroFechas'] ?? '')) {
    $DateRange = explode(' al ', $params['ProyFiltroFechas']);
    $ProyIni  = test_input(dr_fecha($DateRange[0]));
    $ProyFin  = test_input(dr_fecha($DateRange[1]));
    if ($ProyIni == $ProyFin) {
        $where_condition .= " AND ProyIni = '" . $ProyIni. "'";
    }else{
        $where_condition .= " AND ProyIni >= '" . $ProyIni . "' AND ProyFin <= '" . $ProyFin . "'";
    }
}

$where_condition .= " AND proy_proyectos.Cliente = '$_SESSION[ID_CLIENTE]'";
$where_condition .= (test_input($params['ProyEmprFiltro'] ?? '')) ? " AND ProyEmpr = '$params[ProyEmprFiltro]'" : '';
$where_condition .= (test_input($params['ProyRespFiltro'] ?? '')) ? " AND ProyResp = '$params[ProyRespFiltro]'" : '';
$where_condition .= (test_input($params['ProyPlantFiltro'] ?? '')) ? " AND ProyPlant = '$params[ProyPlantFiltro]'" : '';
$where_condition .= (test_input($params['ProyEstaFiltro'] ?? '')) ? " AND ProyEsta = '$params[ProyEstaFiltro]'" : '';
$where_condition .= (test_input($params['ProyNomFiltro'] ?? '')) ? " AND ProyID = '$params[ProyNomFiltro]'" : '';

if ($params['FiltroEstTipo'] != 'Todos') {
    $where_condition .= " AND proy_estados.EstTipo = '$params[FiltroEstTipo]'";
}

$query = "SELECT `ProyID`, `ProyNom`, `ProyDesc`, `ProyEmpr`, `EmpDesc`, `ProyPlant`, `PlantDesc`, `ProyResp`, `nombre` AS 'RespDesc', `ProyEsta`, `EstTipo`, `EstDesc`, `EstColor`, `ProyObs`, `ProyIni`, `ProyFin`, 
(SELECT (SUM(proy_tare_horas.TareHorMin)) FROM proy_tare_horas INNER JOIN .proy_tareas ON proy_tare_horas.TareHorID = proy_tareas.TareID WHERE proy_tare_horas.TareHorProy = proy_proyectos.ProyID AND proy_tareas.TareEsta = '0') AS 'ProyMin' 
FROM proy_proyectos INNER JOIN proy_empresas ON proy_proyectos.ProyEmpr=proy_empresas.EmpID INNER JOIN proy_plantillas ON proy_proyectos.ProyPlant=proy_plantillas.PlantID LEFT JOIN usuarios ON proy_proyectos.ProyResp=usuarios.id INNER JOIN proy_estados ON proy_proyectos.ProyEsta=proy_estados.EstID WHERE proy_proyectos.ProyID >0";

$queryCount = "SELECT COUNT(*) as 'count' FROM proy_proyectos INNER JOIN proy_estados ON proy_proyectos.ProyEsta=proy_estados.EstID WHERE proy_proyectos.ProyID > 0";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $queryCount .= $where_condition;
}

$query .=  " ORDER BY proy_proyectos.ProyFeHo DESC LIMIT " . $params['start'] . " ," . $params['length'] . " ";
$totalRecords = simple_pdoQuery($queryCount);
$count = $totalRecords['count'];
$records = array_pdoQuery($query);
// print_r($records); exit;
// print_r($query);exit;
foreach ($records as $key => $row) {

    $EmpDesc   = $row['EmpDesc'];
    $EstColor  = $row['EstColor'];
    $EstDesc   = $row['EstDesc'];
    $EstTipo   = $row['EstTipo'];
    $PlantDesc = $row['PlantDesc'];
    $ProyDesc  = $row['ProyDesc'];
    $ProyEmpr  = $row['ProyEmpr'];
    $ProyEsta  = $row['ProyEsta'];
    $ProyFin   = $row['ProyFin'];
    $ProyID    = $row['ProyID'];
    $ProyIni   = $row['ProyIni'];
    $ProyNom   = $row['ProyNom'];
    $ProyObs   = $row['ProyObs'];
    $ProyPlant = $row['ProyPlant'];
    $ProyResp  = $row['ProyResp'];
    $RespDesc  = $row['RespDesc'];
    $ProyMin   = intval($row['ProyMin']);

    $data[] = array(
        'ProyFech'   => array(
            'Inicio' => ($ProyIni),
            'Fin'    => ($ProyFin)
        ),
        'ProyEmpr'   => array(
            'Nombre' => ($EmpDesc),
            'ID'     => intval($ProyEmpr)
        ),
        'ProyData'   => array(
            'Nombre' => ($ProyNom),
            'Desc'   => ($ProyDesc),
            'ID'     => intval($ProyID),
            'Obs'    => ($ProyObs)
        ),
        'ProyPlant'   => array(
            'Nombre' => ($PlantDesc),
            'ID'     => intval($ProyPlant)
        ),
        'ProyResp'   => array(
            'Nombre' => ($RespDesc),
            'ID'     => intval($ProyResp)
        ),
        'ProyEsta'   => array(
            'Nombre' => ($EstDesc),
            'ID'     => intval($ProyEsta),
            'Color'  => ($EstColor),
            'Tipo'   => ($EstTipo)
        ),
        'ProyCalc'   => array(
            'Horas'   => MinHora($ProyMin),
            'Minutos' => ($ProyMin)
        ),
    );
}

$tiempo_fin = microtime(true);
$tiempo = ($tiempo_fin - $tiempo_inicio);

$json_data = array(
    "draw"            => intval($params['draw']),
    "recordsTotal"    => intval($count),
    "recordsFiltered" => intval($count),
    "data"            => $data,
    "tiempo"          => round($tiempo, 2)
);
echo json_encode($json_data);
exit;
