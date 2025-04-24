<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("CHWEB")
    ->setLastModifiedBy('CHWEB')
    ->setTitle('Archivo exportado desde CHWEB')
    ->setDescription('Reporte desde CHWEB');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();

# Escribir encabezado de los productos
$encabezado = [
    "COMPANIA", // A1
    "SECCION", // B1
    "SECTOR/LINEA", // C1
    "TRABAJADAS NORMALES", // D1
    "TRABAJADAS EXTRAS", // E1
    "TRANSFERENCIA NORMALES", // F1
    "TRANSFERENCIA EXTRAS", // G1
    "CAPACITACION NORMALES", // H1 
    "CAPACITACION EXTRAS", // I1
    "FAR NORMALES", // J1
    "FAR EXTRAS", // K1
    "SUBTOTAL NORMALES", // L1
    "SUBTOTAL EXTRAS", // M1   
];

$styleArray = [
    'font' => [
        'bold' => true,
    ],
    // 'alignment' => [
    //     'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    // ],
    'borders' => [
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
        ],
    ],
];
$spreadsheet->getStyle('A1:M1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:M1');
/** El último argumento es por defecto A1 */
$spreadsheet->fromArray($encabezado, null, 'A1');
/** Establecer la orientación y el tamaño de la página */
$spreadsheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
/** Para establecer márgenes de página */
$spreadsheet->getPageMargins()->setTop(0.5);
$spreadsheet->getPageMargins()->setRight(0.3);
$spreadsheet->getPageMargins()->setLeft(0.3);
$spreadsheet->getPageMargins()->setBottom(0.5);
/** ajustar a 1 página de ancho por infinitas páginas de alto */
$spreadsheet->getPageSetup()->setFitToWidth(1);
$spreadsheet->getPageSetup()->setFitToHeight(0);
/** Para centrar una página horizontal o verticalmente */
// $spreadsheet->getPageSetup()->setHorizontalCentered(true);
// $spreadsheet->getPageSetup()->setVerticalCentered(false);
/** Encabezado y Pie de Pagina */

$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);

$spreadsheet->getColumnDimension('A')->setWidth(13);
$spreadsheet->getColumnDimension('A')->setWidth(14);
$spreadsheet->getColumnDimension('B')->setWidth(20);
$spreadsheet->getColumnDimension('C')->setWidth(27);
$spreadsheet->getColumnDimension('D')->setWidth(15);
$spreadsheet->getColumnDimension('E')->setWidth(15);
$spreadsheet->getColumnDimension('F')->setWidth(17);
$spreadsheet->getColumnDimension('G')->setWidth(17);
$spreadsheet->getColumnDimension('H')->setWidth(17);
$spreadsheet->getColumnDimension('I')->setWidth(17);
$spreadsheet->getColumnDimension('J')->setWidth(17);
$spreadsheet->getColumnDimension('K')->setWidth(15);
$spreadsheet->getColumnDimension('L')->setWidth(14);
$spreadsheet->getColumnDimension('M')->setWidth(14);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(45);

$spreadsheet->getStyle('A:C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
$spreadsheet->getStyle('D:M')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->freezePane('A2');
// $ColumnCount = 2;
// $RowIndex = 2;
// $spreadsheet->freezePaneByColumnAndRow($ColumnCount, $RowIndex);

$spreadsheet->getStyle('A1:M1')->getAlignment()->setWrapText(true);
$spreadsheet->getStyle('E1:M1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// $spreadsheet->getStyle('E:M')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// $spreadsheet->getStyle('A:C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('A1:M1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A:M')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A1:M1')->getAlignment()->setIndent(1);
$spreadsheet->getStyle('A:M')->getAlignment()->setIndent(1);
$numeroDeFila = 2;

require __DIR__ . '/getHorasEstruct.php';

$incio = FechaFormatVar($FechaIni, 'd-m-Y');
$fin = FechaFormatVar($FechaFin, 'd-m-Y');
$spreadsheet->setTitle("Informe $incio a $fin");
// $data[] = array(
// [compania] => COMPANIA 1
// [seccion] => WORKER
// [sectorLinea] => WAREHOUSE
// [90] => 345124
// [3] => 660
// [80] => 3105
// [70] => 240
// [50] => 5100
// [2] => 0
// [4] => 13335
// [trNorm] => 5822.57
// [trExtras] => 140.75
// [capExtras] => 
// [subExtras] => 140.75
// );
// print_r($data); exit;
// $data  = array();
foreach ($data as $col) {
    $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(19);
    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $col['compania']); //COMPANIA
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $col['seccion']); //SECCION
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $col['sectorLinea']); //SECTOR/LINEA
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $col['trNorm']); //TRABAJADAS NORMALES
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $col['trExtras']); //TRABAJADAS EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, ''); //TRANSFERENCIA NORMALES
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, ''); //TRANSFERENCIA EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, ''); //CAPACITACION NORMALES
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $col['capExtras']); //CAPACITACION EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, ''); //FAR NORMALES
    $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, ''); //FAR EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, $col['trNorm']); //SUBTOTAL NORMALES
    $spreadsheet->setCellValueByColumnAndRow(13, $numeroDeFila, $col['subExtras']); //SUBTOTAL EXTRAS
    $numeroDeFila++;
}
$spreadsheet->getHeaderFooter()->setOddHeader("&L&BINFORME HORAS FAR POR ESTRUCTURA DESDE $FechaIni A $FechaFin");

$UltimaFila = $numeroDeFila - 1;
$UltimaFila2 = $numeroDeFila;
$spreadsheet->getRowDimension($UltimaFila2)->setRowHeight(25);

foreach (range('D', 'M') as $letra) {
    $UltimaI = $letra . ($UltimaFila);
    $UltimaI_2 = $letra . ($UltimaFila2);
    $Formula = '=SUBTOTAL(109,' . $letra . '2:' . $UltimaI . ')';
    $spreadsheet->setCellValue($UltimaI_2, $Formula);
    $spreadsheet->getStyle($UltimaI_2)->applyFromArray($styleArray);
}

# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $NombreArchivo = "Informe_Far_Estructura_" . time() . ".xls";

    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save('archivos/' . $NombreArchivo);
    // $writer->save('php://output');
    // $desde = $FechaIni;
    // $hasta = $FechaFin;
    $data = array('status' => 'ok', 'desde' => fechFormat($FechaIni), 'hasta' => fechFormat($FechaFin), 'archivo' => 'archivos/' . $NombreArchivo);
    echo json_encode($data);
    exit;
} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}
