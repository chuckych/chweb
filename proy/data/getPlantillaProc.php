<?php
session_start(); // Inicia la sesión
header('Content-type: text/html; charset=utf-8'); // Corregir posible error de codificación
require __DIR__ . '/../../config/index.php'; //config
header("Content-Type: application/json"); //return json
E_ALL(); // Report all PHP errors
$totalRecords = $data = array(); // Arreglo para almacenar el total de registros
$params = $_REQUEST; //Obtenemos los parametros
// sleep(1);
$objProcLength = '';

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";

if (!empty($params['search']['value'])) {
    $where_condition .= " AND proy_proceso.ProcDesc LIKE '%" . $params['search']['value'] . "%'";  // busqueda general
}
$where_condition .= " AND proy_proceso.Cliente = '$_SESSION[ID_CLIENTE]'";

$params['Plantilla'] = $params['Plantilla'] ?? '0';
if ($params['Plantilla']) {

    $queryProcPlant = "SELECT PlaProcesos as 'proc' FROM proy_plantilla_proc WHERE proy_plantilla_proc.PlaProPlan = '$params[Plantilla]'";
    $objProc = simple_pdoQuery($queryProcPlant); // Ejecuta la consulta de procesos de la plantilla
    $objProc = explode(",", $objProc['proc']); // Separa los procesos de la plantilla
    $objProcLength = ($objProc[0] == '') ? 0 : count($objProc); // Obtiene el tamaño del arreglo

    $query = "SELECT ProcID, ProcDesc, ProcCost, ProcObs FROM proy_proceso WHERE proy_proceso.ProcID > 0"; // Consulta de procesos
    $queryCount = "SELECT COUNT(*) as 'count' FROM proy_proceso WHERE proy_proceso.ProcID > 0"; // Consulta de procesos para contar el total de registros
    if (isset($where_condition) && $where_condition != '') { // Condiciones de busqueda
        $query .= $where_condition; // Agrega condiciones de busqueda
        $queryCount .= $where_condition; // Agrega condiciones de busqueda
    }

    $query .= " ORDER BY proy_proceso.ProcDesc LIMIT " . $params['start'] . " ," . $params['length'] . " "; // Add limit
    $totalRecords = simple_pdoQuery($queryCount);
    $count = $totalRecords['count']; // Total records
    $records = array_pdoQuery($query); // Records
// print_r($records); exit;
// print_r($query);exit;
    foreach ($records as $key => $row) {
        $ProcSet = false; // Variable para saber si el proceso esta en la plantilla
        foreach ($objProc as $value) {
            if ((($value) == ($row['ProcID']))) { // si el proceso esta en la plantilla
                $ProcSet = true; // se setea el valor en true
                break; // se sale del ciclo
            }
        }
        $ProcID = $row['ProcID']; // ID del proceso
        $ProcDesc = $row['ProcDesc']; // Descripcion del proceso
        $ProcCost = $row['ProcCost']; // Costo del proceso
        $ProcObs = $row['ProcObs']; // Observaciones del proceso

        $data[] = array(
            "ProcID" => $ProcID,
            "ProcDesc" => $ProcDesc,
            "ProcCost" => $ProcCost,
            "ProcObs" => $ProcObs,
            "ProcSet" => $ProcSet,
        );
    }

    foreach ($data as $key => $row) { // Recorre el array de datos
        $aux[$key] = $row['ProcSet']; // Ordena los procesos de la plantilla en por ProcSet
        $aux2[$key] = $row['ProcDesc']; // Ordena los procesos por descripcion
    }
    array_multisort($aux, SORT_DESC, $aux2, SORT_ASC, $data); // Ordena los procesos de la plantilla
} else {
    $data = array();
    $objProc = array();
    $count = '';
}
$tiempo_fin = microtime(true); // Tiempo final
$tiempo = ($tiempo_fin - $tiempo_inicio); // Tiempo de proceso

$json_data = array( // Arreglo para formar el json
    "draw" => intval($params['draw']), // Variable para saber que numero de pedido es
    "recordsTotal" => intval($count), // Cantidad de registros
    "recordsFiltered" => intval($count), // Cantidad de registros
    "data" => $data, // Array de datos
    "tiempo" => round($tiempo, 2), // Tiempo de proceso
    "objProc" => $objProc, // Array de procesos de la plantilla
    "objProcLength" => $objProcLength, // count procesos de la plantilla
    "Plantilla" => $params['Plantilla'], // Array de procesos de la plantilla
);
echo json_encode($json_data);
exit;
