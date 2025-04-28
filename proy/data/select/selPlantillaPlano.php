<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../../config/index.php';
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$q = $params['q'];
$data = array();
$where_condition = '';

$where_condition .= (!empty($params['_c'])) ? " AND clientes.recid = '$params[_c]'" : "";
$where_condition .= " AND proy_plantillas.Cliente = '$_SESSION[ID_CLIENTE]'";
$where_condition .= " AND proy_plantillas.PlantMod = 44";

$FiltroQ = (!empty($q)) ? " AND proy_plantillas.PlantDesc LIKE '%$q%'" : '';
$query = "SELECT PlantID, PlantDesc, PlaPlanos 
FROM proy_plantillas 
LEFT JOIN proy_plantilla_plano ON proy_plantillas.PlantID = proy_plantilla_plano.PlaPlanoID
WHERE proy_plantillas.PlantID > 0 AND proy_plantillas.Cliente = '$_SESSION[ID_CLIENTE]'";
$query .= $FiltroQ;
$query .= $where_condition;
$query .= ' ORDER BY proy_plantillas.PlantID DESC';
$r = array_pdoQuery($query) ?? [];
foreach ($r as $key => $row) {
    $planos = (explode(',', ($row['PlaPlanos'])));
    $text = utf8str($row['PlantDesc']);
    $planosTotal = ($planos != [""]) ? count($planos) : 0;
    $data[] = array(
        'id' => $row['PlantID'],
        'text' => $text,
        'planos' => $planos,
        'planosTotal' => $planosTotal,
        'html' => "<div class='flex-center-between' title='Planos: $planosTotal'><span>$text</span><span class='ms-2 badge bg-azure-lt'>$planosTotal</span></div>"
    );
}
echo json_encode($data);
exit;
