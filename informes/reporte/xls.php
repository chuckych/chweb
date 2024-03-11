<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

ultimoacc();
secure_auth_ch_json();
$Modulo = '45';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '../../../vendor/autoload.php';

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
$spreadsheet->setTitle("TOTALES");

// Flight::json($data);
// exit;
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
];

if ($data['tiposDeHs']) {
    foreach ($data['tiposDeHs'] as $tipo) {
        $encabezado[] = $tipo['THoDesc2'];
    }
}
if ($data['novedades']) {
    foreach ($data['novedades'] as $nov) {
        $encabezado[] = $nov['NovDesc'];
    }
}

// Flight::json($data);
// exit;

// last key number of array
$last_key = count($encabezado) - 1;
function numberToLetter($num)
{
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return numberToLetter($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}

$ultimaLetra = numberToLetter($last_key);

$payload = $data['payloadHoras'];

$FechaIni = $payload['FechIni'];
$FechaFin = $payload['FechFin'];
$detalle = $data['legajos'] ?? [];

// $encabezados = $data['tiposDeHs'];

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

$numeroDeFila = 2;

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
// Flight::json($detalle);
// exit;
foreach ($detalle as $key => $r) {

    $objeto = $r;

    // foreach ($objeto as $k => $row) {

    // Flight::json($objeto['LegApNo']);
    // exit;

    $Lega = $objeto['Lega'];
    $LegApNo = $objeto['LegApNo'];
    $TotalesHoras = $objeto['TotalesHoras'] ?? [];

    // $Nombre = $row['Nombre'];
    // $Fecha = $row['Fecha']->format('Y-m-d');
    // $Horario = $row['Horario'];
    // $Dia = $row['Dia'];
    // $Hora = $row['Hora'];
    // $HoraDesc = $row['HoraDesc'];
    // $FicHsAu = FormatoHoraToExcel($row['FicHsAu']);
    // $FicHsAu2 = FormatoHoraToExcel($row['FicHsAu2']);
    // $Observ = $row['Observ'];
    // $Motivo = ($row['Motivo'] == '0') ? '' : $row['Motivo'];
    // $DescMotivo = $row['DescMotivo'];

    // $Fecha = FormatoFechaToExcel($Fecha);

    // # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Lega);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $LegApNo);
    // $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $TotalesHoras);
    // get the name of the column 3
    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3);
    // get the value of the cell at column 3 and row $numeroDeFila
    $cellValue = $spreadsheet->getCell($columnLetter . '1')->getValue();

    if ($TotalesHoras) {
        foreach ($TotalesHoras as $keyTh => $th) {
            if ($th['THoDesc2']) {
                $dato = filtrarElementoArray($TotalesHoras, $cellValue, $th['THoDesc2']);
                $spreadsheet->setCellValueByColumnAndRow($TotalesHoras + 3, $numeroDeFila, $th);
                Flight::json($dato);
                exit;
            }
        }
    } else {
        $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, '00:00');
    }

    $numeroDeFila++;
    // }
}

// Flight::json($rows);
// exit;

# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Totales_" . $MicroTime . ".xls";

    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save('archivos/' . $NombreArchivo);
    // $writer->save('php://output');

    $data = array('status' => 'ok', 'archivo' => 'archivos/' . $NombreArchivo);
    echo json_encode($data);
    exit;

} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}