<?php
/**
 * Converts a time value to a decimal representation of hours, considering values greater than 24 hours.
 *
 * @param string $Hora The time value to convert (format: HH:MM:SS).
 * @return float The decimal representation of the time value.
 */
function HorasToExcelMas24($Hora)
{
    $Hora = !empty($Hora) ? $Hora : '00:00:00';
    $timeParts = explode(':', $Hora);
    $h = $timeParts[0];
    $m = isset($timeParts[1]) ? $timeParts[1] : 0;
    $s = isset($timeParts[2]) ? $timeParts[2] : 0;
    $decimalHours = $h / 24 + $m / (24 * 60) + $s / (24 * 60 * 60); // Converts the time to a decimal value
    return $decimalHours;
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
$spreadsheet->getHeaderFooter()->setOddHeader('&L&B' . $TituloReporte . '. Periodo: ' . ($dateIni) . ' a ' . $dateFin);
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:' . $ultimaLetra)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
// $spreadsheet->freezePane('A2');
$spreadsheet->freezePane('C2');
/** Ajustar automáticamente el ancho de las columnas */
$spreadsheet->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getColumnDimension('B')->setAutoSize(true);

$spreadsheet->getStyle($PrimerYUltima)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

// $spreadsheet->getColumnDimension('A')->setWidth(12);
// $spreadsheet->getColumnDimension('B')->setWidth(33);
// $spreadsheet->getColumnDimension('C')->setWidth(25);
// $spreadsheet->getColumnDimension('F')->setWidth(30);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $Letras = array('F', 'H', 'I', 'K');
// foreach ($Letras as $col) {
//     $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//     $spreadsheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// }

// $spreadsheet->getStyle('C')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

// $spreadsheet->getStyle('C')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME4);
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