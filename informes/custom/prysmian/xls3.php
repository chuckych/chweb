<?php
/** 
 * Exportar a Excel
 */
$inicioSpreadsheet = microtime(true);
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
require __DIR__ . '/../../../vendor/autoload.php';

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

$title = "Reporte de liquidación";

// /** Columnas del reporte */

foreach ($colsExcel as $k => $v) {
    switch ($v) {
        case 'Legajo':
            $ancho = 10;
            $align = 'HORIZONTAL_CENTER';
            $format = '0';
            $type = 'string';
            break;
        case 'Apellido y Nombre':
            $ancho = 27;
            $align = 'HORIZONTAL_LEFT';
            $format = 'General';
            $type = 'string';
            break;
        case 'Laboral':
        case 'Feriado':
            $ancho = 10;
            $align = 'HORIZONTAL_CENTER';
            $format = 'General';
            $type = 'string';
            break;
        case 'Sector':
            $ancho = 30;
            $align = 'HORIZONTAL_LEFT';
            $format = 'General';
            $type = 'string';
            break;
        case 'Fecha':
            $ancho = 12;
            $align = 'HORIZONTAL_CENTER';
            $format = 'dd/mm/yyyy';
            $type = 'date';
            break;
        // case 'Hs. a Trab.':
        // case 'Hs. Trab.':
        case 'Ingreso':
        case 'Egreso':
        case 'Entrada':
        case 'Salida':
            // case 'Hs. Fer.':
            $ancho = 8;
            $align = 'HORIZONTAL_CENTER';
            $format = 'hh:mm';
            $type = 'time';
            break;
        case 'Día':
            $ancho = 9;
            $align = 'HORIZONTAL_LEFT';
            $format = 'General';
            $type = 'string';
            break;
        case 'EstUltFic':
        case 'EstFic':
            $ancho = 9;
            $align = 'HORIZONTAL_CENTER';
            $format = 'General';
            $type = 'string';
            break;
        default:
            $ancho = 10;
            $align = 'HORIZONTAL_CENTER';
            $format = '0.00';
            $type = 'number';
            break;
    }
    $columnasExcel[$v] = [
        'ancho' => $ancho,
        'key' => $v,
        'align' => $align,
        'format' => $format,
        'type' => $type,
    ];
}

// obtener las key de $columnasExcel donde el type = number
$keysNumber = array_keys(array_filter($columnasExcel, fn($col) => $col['type'] === 'number'));
$keyTime = array_keys(array_filter($columnasExcel, fn($col) => $col['type'] === 'time'));

// unset($keyTime['Ingreso']);
// unset($keyTime['Egreso']);
// unset($keyTime['Entrada']);
// unset($keyTime['Salida']);
// // eliminar las key Ingreso y Egreso de $keyTime
// unset($keyTime[array_search('Ingreso', $keyTime)]);
// unset($keyTime[array_search('Egreso', $keyTime)]);
// unset($keyTime[array_search('Entrada', $keyTime)]);
// unset($keyTime[array_search('Salida', $keyTime)]);
// $keyTime = array_values($keyTime);
// Flight::json($keyTime);


// ordena $Datos['Data'] por Legajo y Fecha
// usort($Datos['Data'], function ($a, $b) {
//     return $a['Legajo'] <=> $b['Legajo'] ?: strtotime($a['Fecha']) <=> strtotime($b['Fecha']);
// });

// Flight::json($Datos['Data']);
// exit;

// $columnasExcel = array_merge($columnasExcel, $colsExcel);

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
    'firstRowHeight' => 50, // Altura de la primera fila, si los encabezados están activados
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

    $legajoAnterior = null;
    $filaInicioGrupo = $numeroDeFila;
    $datosGrupoAnterior = []; // Para almacenar los datos del grupo anterior


    foreach ($Datos['Data'] as $clave => $row) {

        $legajoActual = $row['Legajo'] ?? '';

        // Detectar cambio de legajo
        if ($legajoAnterior !== null && $legajoAnterior !== $legajoActual) {
            // Aplicar borde al grupo anterior
            aplicarBordeGrupo($spreadsheet, $columnasExcel, $filaInicioGrupo, $numeroDeFila - 1);

            // Insertar fila vacía para subtotales con los datos del grupo anterior
            $filaSubtotal = $numeroDeFila;
            aplicarSubtotalPorLegajo($spreadsheet, $columnasExcel, $filaInicioGrupo, $numeroDeFila - 1, $filaSubtotal, $datosGrupoAnterior);

            $numeroDeFila++; // Incrementar para la siguiente fila de datos
            $filaInicioGrupo = $numeroDeFila; // Nuevo inicio de grupo
        }

        // Almacenar los datos del grupo actual
        $datosGrupoAnterior = [
            'Legajo' => $row['Legajo'] ?? '',
            'Apellido y Nombre' => $row['Apellido y Nombre'] ?? '',
            'Sector' => $row['Sector'] ?? '',
        ];

        foreach ($columnasExcel as $key => $e) {
            $letra = $e['col']; // eje:. A
            $letraAnterior = chr(ord($letra) - 1); // Letra anterior
            $valueData = $row[$e['key']] ?? ''; // Obtiene el valor de la celda

            if (!$valueData) {
                continue;
            }

            if ($e['type'] == 'formula') {
                $valueData = $e['key']; // Obtiene el valor de la celda 
            }

            $valor = value_to_cell($valueData, $e['type'], $columnasExcel, $numeroDeFila); // Formatea el valor de la celda
            $spreadsheet->setCellValue("{$letra}{$numeroDeFila}", $valor); // Asignamos el valor a la celda
            // si el valor de la celda contiene  (M) poner en color azul. y solo para las key 'Ingreso' y 'Egreso'
            aplicarColorCelda($spreadsheet, "{$letraAnterior}{$numeroDeFila}", $valor, ['EstFic', 'EstUltFic'], $e['key']);
        }
        $legajoAnterior = $legajoActual;
        $numeroDeFila++;
        unset($valor);
    }

    // Aplicar borde y subtotal al último grupo
    if ($legajoAnterior !== null) {
        aplicarBordeGrupo($spreadsheet, $columnasExcel, $filaInicioGrupo, $numeroDeFila - 1);

        // Subtotal del último grupo con los datos del último grupo
        $filaSubtotal = $numeroDeFila;
        aplicarSubtotalPorLegajo($spreadsheet, $columnasExcel, $filaInicioGrupo, $numeroDeFila - 1, $filaSubtotal, $datosGrupoAnterior);
        $numeroDeFila++; // Incrementar para el total general
    }

    foreach ($keysNumber as $keySubtotal) {
        aplicarSubtotal($spreadsheet, $columnasExcel, $keySubtotal, $numeroDeFila, [
            'formato' => '0.00',
            'negrita' => true
        ]);
    }

    // ocultar las columna cuyo encabezados sean EstFic y EstUltFic
    foreach ($columnasExcel as $key => $e) {
        if (in_array($e['key'], ['EstFic', 'EstUltFic'])) {
            $spreadsheet->getColumnDimension($e['col'])->setVisible(false);
        }
    }
    /** Configuraciones de la hoja */
    config_sheet($configSheet); // Configuraciones de la hoja

    // aplicar a la primer fila ajuste de linea del texto
    if ($configSheet['encabezados']) {
        $spreadsheet->getStyle("1:1")->getAlignment()->setWrapText(true);
    }

    $hash = microtime(true);
    $nameFile = "archivos/$configSheet[nameFile]-{$hash}.xls";

    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save($nameFile);

    $data = [
        'status' => 'ok',
        'archivo' => $nameFile,
        'hash' => $hashPayload,
        'totalEjecutionNoveHoras' => $totalEjecutionNoveHoras,
        'totalEjecutionSpreadsheet' => totalEjecution($inicioSpreadsheet),
        'totalEjecutionScript' => totalEjecution($inicioScript),
    ];
    Flight::json($data);
} catch (\Exception $e) {
    $data = ['status' => 'error', 'mensaje' => $e->getMessage()];
    file_put_contents('error.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
    Flight::json($data);
}