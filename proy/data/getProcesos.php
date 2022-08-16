<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
header("Content-Type: application/json");
E_ALL();
$totalRecords = $data = $count = array();
$params = $_REQUEST;
// sleep(1);

$_POST['Plant'] = $_POST['Plant'] ?? '';
$params['start'] = $params['start'] ?? 0;
$params['length'] = $params['length'] ?? 9999;
$params['draw'] = $params['draw'] ?? 0;
$_POST['selectProc'] = $_POST['selectProc'] ?? '';

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";

if (!empty($params['search']['value'])) {
    $where_condition .=    " AND proy_proceso.ProcDesc LIKE '%" . $params['search']['value'] . "%'";
}
if (!empty($_POST['Plant'])) {
    $PROCESOS = simple_pdoQuery("SELECT PlaProcesos FROM `proy_plantilla_proc` where `PlaProPlan` = '" . $_POST['Plant'] . "'");
    $where_condition .=    " AND proy_proceso.ProcID IN ($PROCESOS[PlaProcesos])";
}
if (!empty($_POST['selectProc'])) {
    $p = simple_pdoQuery("SELECT PlaProcesos FROM `proy_proyectos` INNER JOIN `proy_plantilla_proc` ON proy_proyectos.ProyPlant = proy_plantilla_proc.PlaProPlan WHERE `ProyID` = '$_POST[selectProc]'");
    $where_condition .= " AND proy_proceso.ProcID IN ($p[PlaProcesos])";
    if (!empty($_POST['q'])) {
        $where_condition .= (!empty($_POST['q'])) ? " AND CONCAT(ProcID, ProcDesc) LIKE '%$_POST[q]%'" : '';
    }
}

$where_condition .= " AND proy_proceso.Cliente = '$_SESSION[ID_CLIENTE]'";

$query = "SELECT ProcID, ProcDesc, ProcCost, ProcObs FROM proy_proceso WHERE proy_proceso.ProcID > 0";
$queryCount = "SELECT COUNT(*) as 'count' FROM proy_proceso WHERE proy_proceso.ProcID > 0";

if (isset($where_condition) && $where_condition != '') {
    $query .= $where_condition;
    $queryCount .= $where_condition;
}

$query .=  " ORDER BY proy_proceso.ProcDesc LIMIT " . $params['start'] . " ," . $params['length'] . " ";
if (empty($_POST['selectProc'])) { // sino viene de un select
    $totalRecords = simple_pdoQuery($queryCount);
    $count = $totalRecords['count'];
}

$records = array_pdoQuery($query);
// print_r($records); exit;
// print_r($query);exit;
foreach ($records as $key => $row) {

    $ProcID   = $row['ProcID'];
    $ProcDesc = $row['ProcDesc'];
    $ProcCost = $row['ProcCost'];
    $ProcObs  = $row['ProcObs'];

    // $data[] = array(
    //     "ProcID"   => $ProcID,
    //     "ProcDesc" => $ProcDesc,
    //     "ProcCost" => $ProcCost,
    //     "ProcObs"  => $ProcObs,
    // );
    if (!empty($_POST['selectProc'])) {
        $text = '(' . $ProcID . ') ' . $ProcDesc;
        $data[] = array(
            'id'      => $ProcID,
            'text' => utf8str($ProcDesc),
            'title'    => utf8str($text),
        );
    } else {
        $data[] = array(
            "ProcID"   => $ProcID,
            "ProcDesc" => $ProcDesc,
            "ProcCost" => $ProcCost,
            "ProcObs"  => $ProcObs,
        );
    }
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
if (!empty($_POST['selectProc'])) {
    echo json_encode($data);
    exit;
}
echo json_encode($json_data);
exit;
