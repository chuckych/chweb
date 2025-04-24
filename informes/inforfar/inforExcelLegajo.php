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
    "LEGAJO", // A1 // 1
    "COMPANIA", // B1 // 2
    "SECCION", // C1 // 3
    "SECTOR/LINEA", // D1 // 4
    "TRABAJADAS NORMALES", // E1 // 5
    "TRABAJADAS EXTRAS", // F1 // 6
    "TRANSFERENCIA NORMALES", // G1   // 7
    "TRANSFERENCIA EXTRAS", // H1 // 8
    "CAPACITACION NORMALES", // I1  // 9
    "CAPACITACION EXTRAS", // P1  // 10
    "FAR NORMALES", // K1  // 11
    "FAR EXTRAS", // L1  // 12
    "SUBTOTAL NORMALES", // M1  // 13
    "SUBTOTAL EXTRAS",  // N1 // 14
    "JM", // O1 // 15
    "HS FAR" // P1 // 16
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
$spreadsheet->getStyle('A1:P1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:P1');
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
$spreadsheet->getColumnDimension('B')->setWidth(14);
$spreadsheet->getColumnDimension('C')->setWidth(20);
$spreadsheet->getColumnDimension('D')->setWidth(27);
$spreadsheet->getColumnDimension('E')->setWidth(15);
$spreadsheet->getColumnDimension('F')->setWidth(15);
$spreadsheet->getColumnDimension('G')->setWidth(17);
$spreadsheet->getColumnDimension('H')->setWidth(17);
$spreadsheet->getColumnDimension('I')->setWidth(17);
$spreadsheet->getColumnDimension('J')->setWidth(17);
$spreadsheet->getColumnDimension('K')->setWidth(17);
$spreadsheet->getColumnDimension('L')->setWidth(15);
$spreadsheet->getColumnDimension('M')->setWidth(14);
$spreadsheet->getColumnDimension('N')->setWidth(14);
$spreadsheet->getColumnDimension('O')->setWidth(7);
$spreadsheet->getColumnDimension('P')->setWidth(11);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(45);

$spreadsheet->getStyle('A')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$spreadsheet->getStyle('B:D')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
$spreadsheet->getStyle('E:N')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
$spreadsheet->getStyle('P')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

$spreadsheet->freezePane('A2');
$ColumnCount = 2;
$RowIndex = 2;
$spreadsheet->freezePaneByColumnAndRow($ColumnCount, $RowIndex);

$spreadsheet->getStyle('A1:P1')->getAlignment()->setWrapText(true);
$spreadsheet->getStyle('E1:P1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getStyle('O')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// $spreadsheet->getStyle('A:D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('A1:P1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A:P')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A1:P1')->getAlignment()->setIndent(1);
$spreadsheet->getStyle('A:P')->getAlignment()->setIndent(1);
$numeroDeFila = 2;

require __DIR__ . '/getHorasLegajos.php';

$incio = FechaFormatVar($FechaIni, 'd-m-Y');
$fin = FechaFormatVar($FechaFin, 'd-m-Y');
$spreadsheet->setTitle("Informe $incio a $fin");
// $data[] = array(
// 'legajo'      => $r['Legajo'],
// 'jm'          => ($r['JM'] == 1) ? 'J' : 'M',
// 'compania'    => $r['Compania'],
// 'seccion'     => $r['Seccion'],
// 'sectorLinea' => $r['Sector/Linea'],
// '90'          => $r['90'],
// '3'           => $r['3'],
// '80'          => $r['80'],
// '70'          => $r['70'],
// '50'          => $r['50'],
// '2'           => $r['2'],
// '4'           => $r['4'],
// 'trNorm'      => $trNorm,
// 'trExtras'    => $trExtras,
// 'capExtras'   => $capExtras,
// 'subExtras'   => $subExtras
// );
// print_r($data); exit;
// $data  = array();
foreach ($data as $col) {
    $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(19);
    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $col['legajo']); //LEGAJO
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $col['compania']); //COMPANIA
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $col['seccion']); //SECCION
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $col['sectorLinea']); //SECTOR/LINEA
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $col['trNorm']); //TRABAJADAS NORMALES
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $col['trExtras']); //TRABAJADAS EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, ''); //TRANSFERENCIA NORMALES
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, ''); //TRANSFERENCIA EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, ''); //CAPACITACION NORMALES
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $col['capExtras']); //CAPACITACION EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, ''); //FAR NORMALES
    $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, ''); //FAR EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(13, $numeroDeFila, $col['trNorm']); //SUBTOTAL NORMALES
    $spreadsheet->setCellValueByColumnAndRow(14, $numeroDeFila, $col['subExtras']); //SUBTOTAL EXTRAS
    $spreadsheet->setCellValueByColumnAndRow(15, $numeroDeFila, $col['jm']); //JM
    $spreadsheet->setCellValueByColumnAndRow(16, $numeroDeFila, ''); //HS FAR
    $numeroDeFila++;
}
$spreadsheet->getHeaderFooter()->setOddHeader("&L&BINFORME HORAS FAR POR LEGAJO DESDE $FechaIni A $FechaFin");

$UltimaFila = $numeroDeFila - 1;
$UltimaFila2 = $numeroDeFila;
$spreadsheet->getRowDimension($UltimaFila2)->setRowHeight(25);

foreach (range('E', 'N') as $letra) {
    $UltimaI = $letra . ($UltimaFila);
    $UltimaI_2 = $letra . ($UltimaFila2);
    $Formula = '=SUBTOTAL(109,' . $letra . '2:' . $UltimaI . ')';
    $spreadsheet->setCellValue($UltimaI_2, $Formula);
    $spreadsheet->getStyle($UltimaI_2)->applyFromArray($styleArray);
}
$letra = 'P';
$UltimaI = $letra . ($UltimaFila);
$UltimaI_2 = $letra . ($UltimaFila2);
$Formula = '=SUBTOTAL(109,' . $letra . '2:' . $UltimaI . ')';
$spreadsheet->setCellValue($UltimaI_2, $Formula);
$spreadsheet->getStyle($UltimaI_2)->applyFromArray($styleArray);

# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $NombreArchivo = "Informe_Far_Legajos_" . time() . ".xls";

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
