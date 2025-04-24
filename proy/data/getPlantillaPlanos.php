<?php
session_start(); // Inicia la sesión
header('Content-type: text/html; charset=utf-8'); // Corregir posible error de codificación
require __DIR__ . '/../../config/index.php'; //config
header("Content-Type: application/json"); //return json
E_ALL(); // Report all PHP errors
$totalRecords = $data = array(); // Arreglo para almacenar el total de registros
$params = $_REQUEST; //Obtenemos los parametros
// sleep(1);
$objPlanoLength = '';

$tiempo_inicio = microtime(true);
$where_condition = $sqlTot = $sqlRec = "";

$params['planosPlant'] = $params['planosPlant'] ?? false;

if (!empty($params['search']['value'])) {
    $where_condition .= " AND proy_planos.PlanoDesc LIKE '%" . $params['search']['value'] . "%'";  // busqueda general
}
$where_condition .= " AND proy_planos.Cliente = '$_SESSION[ID_CLIENTE]'";
$where_condition .= " AND PlanoEsta = '0'";

$params['Plantilla'] = $params['Plantilla'] ?? '0';
if ($params['Plantilla']) {

    $queryProcPlant = "SELECT PlaPlanos as 'plan' FROM proy_plantilla_plano WHERE proy_plantilla_plano.PlaPlanoID = '$params[Plantilla]'";
    $objProc = simple_pdoQuery($queryProcPlant); // Ejecuta la consulta de planos de la plantilla
    $dataObj = $objProc; // Planos asignados a la plantilla separados por comas
    $objProc = explode(",", $objProc['plan']); // Separa los planos de la plantilla
    $objPlanoLength = ($objProc[0] == '') ? 0 : count($objProc); // Obtiene el tamaño del arreglo

    if ($params['planosPlant']) {
        if ($dataObj['plan']) {
            $where_condition = " AND PlanoID IN ($dataObj[plan]) AND PlanoEsta = '0'";
        } else {
            $where_condition = " AND PlanoID = 0";
        }
    }

    $query = "SELECT PlanoID, PlanoDesc, PlanoEsta, PlanoObs, PlanoCod FROM proy_planos WHERE proy_planos.PlanoID > 0 AND proy_planos.PlanoEsta = '0'"; // Consulta de planos
    $queryCount = "SELECT COUNT(*) as 'count' FROM proy_planos WHERE proy_planos.PlanoID > 0"; // Consulta de planos para contar el total de registros
    if (isset($where_condition) && $where_condition != '') { // Condiciones de busqueda
        $query .= $where_condition; // Agrega condiciones de busqueda
        $queryCount .= $where_condition; // Agrega condiciones de busqueda
    }

    $query .= " ORDER BY proy_planos.PlanoDesc LIMIT " . $params['start'] . " ," . $params['length'] . " "; // Add limit
    $totalRecords = simple_pdoQuery($queryCount);
    $count = $totalRecords['count']; // Total records
    $records = array_pdoQuery($query); // Records

    // print_r($records); exit;

    // print_r($query);exit;
    foreach ($records as $key => $row) {
        $PlanoSet = false; // Variable para saber si el plano esta en la plantilla
        foreach ($objProc as $value) {
            if ((($value) == ($row['PlanoID']))) { // si el plano esta en la plantilla
                $PlanoSet = true; // se setea el valor en true
                break; // se sale del ciclo
            }
        }
        $PlanoID = $row['PlanoID']; // ID del plano
        $PlanoDesc = $row['PlanoDesc']; // Descripcion del plano
        $PlanoEsta = $row['PlanoEsta']; // Costo del plano
        $PlanoObs = $row['PlanoObs']; // Observaciones del plano
        $PlanoCod = $row['PlanoCod']; // Codigo del plano

        $data[] = array(
            "PlanoID" => $PlanoID,
            "PlanoDesc" => $PlanoDesc,
            "PlanoEsta" => $PlanoEsta,
            "PlanoObs" => $PlanoObs,
            "PlanoSet" => $PlanoSet,
            "PlanoCod" => $PlanoCod,
        );
    }

    foreach ($data as $key => $row) { // Recorre el array de datos
        $aux[$key] = $row['PlanoSet']; // Ordena los planos de la plantilla en por PlanoSet
        $aux2[$key] = $row['PlanoDesc']; // Ordena los planos por descripcion
    }
    if ($data) {
        array_multisort($aux, SORT_DESC, $aux2, SORT_ASC, $data); // Ordena los planos de la plantilla
    }
} else {
    $data = array();
    $objProc = array();
    $count = '';
}
$tiempo_fin = microtime(true); // Tiempo final
$tiempo = ($tiempo_fin - $tiempo_inicio); // Tiempo de plano

$json_data = array( // Arreglo para formar el json
    "draw" => intval($params['draw']), // Variable para saber que numero de pedido es
    "recordsTotal" => intval($count), // Cantidad de registros
    "recordsFiltered" => intval($count), // Cantidad de registros
    "data" => $data, // Array de datos
    "tiempo" => round($tiempo, 2), // Tiempo de plano
    "objProc" => $objProc, // Array de planos de la plantilla
    "objPlanoLength" => $objPlanoLength, // count planos de la plantilla
    "Plantilla" => $params['Plantilla'], // Array de planos de la plantilla
);
echo json_encode($json_data);
exit;
