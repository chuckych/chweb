<?php
/** 
 * Exportar a Excel
 */

ini_set('max_execution_time', 600); //180 seconds = 3 minutes
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../app-data/fn_spreadsheet.php';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // Tipo de contenido
header('Cache-Control: max-age=0'); // No cache
header('Cache-Control: max-age=1'); // Cache for 1 second
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$documento = new Spreadsheet();
$spreadsheet = $documento->getActiveSheet();

$fechaInicio = $payload['FechaDesde'] ?? '';
$fechaFin = $payload['FechaHasta'] ?? '';
// formatear fechas a dd/mm/yyyy
if ($fechaInicio) {
    $fechaInicio = date('d-m-Y', strtotime($fechaInicio));
}
if ($fechaFin) {
    $fechaFin = date('d-m-Y', strtotime($fechaFin));
}

$nameFile = 'reporte de horarios asignados';
$title = 'Horarios asignados';
$title2 = 'Horarios asignados desde ' . $fechaInicio . ' hasta ' . $fechaFin;
/** Columnas del reporte */

$columnasExcel = [
    "Legajo" => [
        'ancho' => 10,
        'key' => 'Legajo',
        'align' => 'HORIZONTAL_CENTER',
        'format' => '0',
        'type' => 'number',
    ],
    "Nombre" => [
        'ancho' => 30,
        'key' => 'Nombre',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'general',
    ],
    "Fecha" => [
        'ancho' => 15,
        'key' => 'Fecha',
        'align' => 'HORIZONTAL_CENTER',
        'format' => 'dd/mm/yyyy',
        'type' => 'date',
    ],
    "Día" => [
        'ancho' => 13,
        'key' => 'Dia',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'general',
    ],
    "Horario" => [
        'ancho' => 15,
        'key' => 'Horario',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'general',
    ],
    "Descanso" => [
        'ancho' => 10,
        'key' => 'Descanso',
        'align' => 'HORIZONTAL_CENTER',
        'format' => 'hh:mm',
        'type' => 'time',
    ],
    "Hs a Trab." => [
        'ancho' => 10,
        'key' => 'HsATrab',
        'align' => 'HORIZONTAL_CENTER',
        'format' => 'hh:mm',
        'type' => 'time',
    ],
    "Hs del Día" => [
        'ancho' => 10,
        'key' => 'HsDelDia',
        'align' => 'HORIZONTAL_CENTER',
        'format' => 'hh:mm',
        'type' => 'time',
    ],
    "Cod" => [
        'ancho' => 10,
        'key' => 'CodigoHorario',
        'align' => 'HORIZONTAL_CENTER',
        'format' => '0',
        'type' => 'number',
    ],
    "Desc. Horario" => [
        'ancho' => 28,
        'key' => 'DescripcionHorario',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'general',
    ],
    "ID" => [
        'ancho' => 10,
        'key' => 'HorID',
        'align' => 'HORIZONTAL_CENTER',
        'format' => '0',
        'type' => 'general',
    ],
    "Asignacion" => [
        'ancho' => 22,
        'key' => 'Asignacion',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'general',
    ],
    "Asignacion Fecha" => [
        'ancho' => 25,
        'key' => 'Referencia',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'general',
    ],
    "Feriado" => [
        'ancho' => 10,
        'key' => 'Feriado',
        'align' => 'HORIZONTAL_CENTER',
        'format' => '0',
        'type' => 'general',
    ],
    "Feriado Desc." => [
        'ancho' => 25,
        'key' => 'FeriadoStr',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'general',
    ],
];

$columnasExcel = add_letter_column_to_array($columnasExcel);
$configSheet = [
    'sheet' => $spreadsheet, // Hoja de cálculo
    'columnasExcel' => $columnasExcel, // Columnas del reporte
    'encabezados' => true, // Mostrar Encabezados de la hoja
    'autoFilter' => true, // true, false. Agregar filtro a los encabezados si están activos
    'orientation' => 'landscape', // landscape, portrait
    'paperSize' => 'A4', // A4, LETTER
    'margin' => [
        'top' => 0.5, // Margen superior
        'right' => 0.3, // Margen derecho
        'left' => 0.3, // Margen izquierdo
        'bottom' => 0.5, // Margen inferior
    ],
    'fitToWidth' => 1, // Ajusta la hoja de cálculo para que se ajuste a una página de ancho cuando se imprime.
    'fitToHeight' => 0, //Ajusta la hoja de cálculo para que no se limite a un número específico de páginas de alto cuando se imprime, permitiendo que se extienda verticalmente según sea necesario.
    'title' => $title, // Título de la hoja
    'oddHeader' => $title2, // Encabezado izquierdo
    'oddFooter' => $title2, // Encabezado derecho
    'showGridlines' => true, // Mostrar / ocultar las líneas de cuadrícula al imprimir
    'verticalAlignment' => 'center', // Alineación vertical
    'freezePane' => 1, // Inmovilizar fila; 0 = Ninguno
    'zoomScale' => 100, // Establecer el nivel de zoom de la hoja
    'firstRowHeight' => 25, // Altura de la primera fila, si los encabezados están activados
    'allRowHeight' => 20, // Altura de todas las filas de la hoja
    'colorTab' => 'FFFFFF', // Color de la pestaña de la hoja,
    'indentRows' => true, // Indenter las filas,
    'cleanFiles' => [
        'active' => true, // Activar limpieza de archivos
        'path' => "archivos", // Ruta de los archivos
        'days' => 1, // Días para eliminar los archivos
        'pattern' => '*.xls', // Patrón de búsqueda
    ],
    'nameFile' => slugify($nameFile), // Nombre del archivo
];

$documento
    ->getProperties()
    ->setCreator("Portal CHWeb")
    ->setLastModifiedBy('Portal CHWeb')
    ->setTitle($configSheet['oddHeader'])
    ->setDescription('Reporte generado desde Portal CHWeb')
    ->setSubject($title)
    ->setKeywords('Portal CHWeb, Notify, xls, Reporte')
    ->setCategory($title);

$resultado = [];
// Verificar que $Datos['DATA'] existe y es un array
$totalLegajos = count($Datos['DATA'] ?? []);
if (isset($Datos['DATA']) && is_array($Datos['DATA'])) {
    foreach ($Datos['DATA'] as $key => $value) {
        // Verificar que $value es un array
        if (is_array($value)) {
            foreach ($value as $v) {
                $v['DescripcionHorario'] ??= 'Sin Horario Asignado';
                $v['Feriado'] = $v['Feriado'] == '0' ? 'No' : 'Sí';
                $v['Horario'] = ucfirst(strtolower($v['Horario']));
                $resultado[] = $v;
            }
        }
    }
}
// error_log(print_r($resultado, true));
try {
    $numeroDeFila = $configSheet['encabezados'] ? 2 : 1; // Si los encabezados están activados, la fila comienza en 2, de lo contrario en 1
    foreach ($resultado as $row) {
        foreach ($columnasExcel as $key => $e) {
            $letra = $e['col']; // eje:. A
            $valueData = $row[$e['key']]; // Obtiene el valor de la celda

            if ($e['type'] == 'formula') {
                $valueData = $e['key']; // Obtiene el valor de la celda 
            }

            $valor = value_to_cell($valueData, $e['type'], $columnasExcel, $numeroDeFila); // Formatea el valor de la celda
            $spreadsheet->setCellValue("{$letra}{$numeroDeFila}", $valor); // Asignamos el valor a la celda
        }
        $numeroDeFila++;
        unset($valor);
    }

    /** Configuraciones de la hoja */
    config_sheet($configSheet); // Configuraciones de la hoja

    $hash = microtime(true);
    $nameFile = "archivos/$configSheet[nameFile]-{$hash}.xls";

    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save($nameFile);

    $data = [
        'status' => 'ok',
        'archivo' => $nameFile,
        'resultado' => $resultado,
        'totalLegajos' => $totalLegajos,
        'payload' => $payload
    ];
    Flight::json($data);
} catch (\Exception $e) {
    $data = [
        'status' => 'error',
        'mensaje' => $e->getMessage(),
        'resultado' => $resultado,
        'totalLegajos' => $totalLegajos,
        'payload' => $payload
    ];
    file_put_contents('error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
    Flight::json($data);
}