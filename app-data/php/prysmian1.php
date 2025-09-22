<?php
$data = fic_nove_horas($payload) ?? []; // Obtener datos novedades
if (!$data) { // Si no hay datos, retornar un array vacío
    return Flight::json([]);
}

$Datos = procesar_por_intervalos($data, $payload, $flag);

if ($tipo == 'view') {
    Flight::json($Datos['Data']);
}
if ($tipo == 'xls') {
    require __DIR__ . '/fn_spreadsheet.php';
    include __DIR__ . '/../informes/custom/prysmian/xls.php';
}