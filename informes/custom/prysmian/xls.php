<?php
/** 
 * Exportar a Excel
 */

ini_set('max_execution_time', 600); //180 seconds = 3 minutes
require __DIR__ . '../../../../vendor/autoload.php';

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

$title = ($Datos['LegTipo'] == 1) ? 'Inas. Jornales' : 'Inas. Mensuales';

/** Columnas del reporte */
$columnasExcel = [
    "Action" => [
        'ancho' => 10,
        'key' => 'Action',
        'align' => 'HORIZONTAL_CENTER',
        'format' => '0',
        'type' => 'number',
    ],
    "Company" => [
        'ancho' => 10,
        'key' => 'Company',
        'align' => 'HORIZONTAL_CENTER',
        'format' => '0',
        'type' => 'number',
    ],
    "Employee" => [
        'ancho' => 15,
        'key' => 'Employee',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'number',
    ],
    "Digit" => [
        'ancho' => 10,
        'key' => 'Digit',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'number',
    ],
    "Cod Inasistencia" => [
        'ancho' => 10,
        'key' => 'Cod Inasistencia',
        'align' => 'HORIZONTAL_LEFT',
        'format' => '0',
        'type' => 'number',
    ],
    "Fecha inicio" => [
        'ancho' => 15,
        'key' => 'Fecha inicio',
        'align' => 'HORIZONTAL_CENTER',
        'format' => 'dd/mm/yyyy',
        'type' => 'date',
    ],
    "Fecha fin" => [
        'ancho' => 15,
        'key' => 'Fecha fin',
        'align' => 'HORIZONTAL_CENTER',
        'format' => 'dd/mm/yyyy',
        'type' => 'date',
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
    'oddHeader' => $title, // Encabezado izquierdo
    'oddFooter' => $title, // Encabezado derecho
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
    'nameFile' => slugify($title), // Nombre del archivo
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

try {
    $numeroDeFila = $configSheet['encabezados'] ? 2 : 1; // Si los encabezados están activados, la fila comienza en 2, de lo contrario en 1
    foreach ($Datos['Data'] as $row) {
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

    $data = ['status' => 'ok', 'archivo' => $nameFile];
    Flight::json($data);
} catch (\Exception $e) {
    $data = ['status' => 'error', 'mensaje' => $e->getMessage()];
    Flight::json($data);
}