<?php
function FormatoHoraToExcel($Hora)
{
    $Hora = !empty($Hora) ? $Hora : '00:00:00';
    $timestamp = new \DateTime($Hora);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Hora = ($excelTimestamp - $excelDate) == 0 ? '' : $excelTimestamp - $excelDate;
    return $Hora;
}
function FormatoFechaToExcel($Fecha)
{
    $timestamp = new \DateTime($Fecha);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Fecha = ($excelTimestamp);
    return $Fecha;
}

$styleArray = [
    'font' => [
        'bold' => true,
    ],
    'borders' => [
        'bottom' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
        ],
    ],
];

$PrimerYUltima = 'A1:' . $ultimaLetra . '1';

$spreadsheet->getStyle($PrimerYUltima)->applyFromArray($styleArray);
/** aplicar un auto-filtro a un rango de celdas */
$spreadsheet->setAutoFilter($PrimerYUltima);
/** El último argumento es por defecto A1 */
$spreadsheet->fromArray($encabezado, null, 'A1');
/** Establecer la orientación y el tamaño de la página */
$spreadsheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
$spreadsheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
/** Para establecer márgenes de página */
$spreadsheet->getPageMargins()->setTop(0.5);
$spreadsheet->getPageMargins()->setRight(0.3);
$spreadsheet->getPageMargins()->setLeft(0.3);
$spreadsheet->getPageMargins()->setBottom(0.5);
$spreadsheet->getPageSetup()->setFitToWidth(1);
$spreadsheet->getPageSetup()->setFitToHeight(0);

/** Encabezado y Pie de Pagina */
$dateIni = FechaFormatVar($FechaIni, 'd/m/Y');
$dateFin = FechaFormatVar($FechaFin, 'd/m/Y');
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE TOTALES. DESDE ' . ($dateIni) . ' A ' . $dateFin);
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:' . $ultimaLetra)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle($PrimerYUltima)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);
// $spreadsheet->getColumnDimension('C')->setWidth(12);
// $spreadsheet->getColumnDimension('D')->setWidth(13);
// $spreadsheet->getColumnDimension('E')->setWidth(13);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);
// $Letras = range("H","U");
// foreach ($Letras as $col) {
// }
// $spreadsheet->getColumnDimension('F')->setWidth(8);
// $spreadsheet->getColumnDimension('G')->setWidth(22);
// $spreadsheet->getColumnDimension('J')->setWidth(22);
// $spreadsheet->getColumnDimension('K')->setWidth(8);
// $spreadsheet->getColumnDimension('L')->setWidth(22);


// $Letras = range("F","G");
// foreach ($Letras as $col) {
//     $spreadsheet->getColumnDimension($col)->setWidth(12);
// }

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
// $Letras = array('A', 'B', 'C', 'D', 'E', 'G', 'J', 'L');
// foreach ($Letras as $col) {
//     $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// }

// $Letras = array('F', 'H', 'I', 'K');
// foreach ($Letras as $col) {
//     $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//     $spreadsheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// }

// $spreadsheet->getStyle('C')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

// $spreadsheet->getStyle('H')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);
// $spreadsheet->getStyle('I')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

// $Letras = array('A', 'F', 'K');
// foreach ($Letras as $col) {
//     $spreadsheet->getStyle($col)
//         ->getNumberFormat()
//         ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
// }
// $spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);
/** establecer un salto de impresión */
// $spreadsheet->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);