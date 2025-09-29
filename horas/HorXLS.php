<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis = date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_HORAS_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

require __DIR__ . '/../config/conect_mssql.php';
require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/valores.php';

ultimoacc();
secure_auth_ch_json();
$Modulo = '16';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("CHWEB")
    ->setLastModifiedBy('CHWEB')
    ->setTitle('Archivo exportado desde CHWEB')
    ->setDescription('Reporte desde CHWEB');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();
$spreadsheet->setTitle("HORAS");
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Fecha",
    "Horario",
    "Dia",
    "Hora",
    "Descripción",
    "Hechas",
    "Pagas",
    "Cod Motivo",
    "Motivo",
    "Observaciones",
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

$spreadsheet->getStyle('A1:L1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:L1');
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
// $spreadsheet->getPageMargins()->setHeader(0.2);
// $spreadsheet->getPageMargins()->setFooter(0.2);
/** ajustar a 1 página de ancho por infinitas páginas de alto */
$spreadsheet->getPageSetup()->setFitToWidth(1);
$spreadsheet->getPageSetup()->setFitToHeight(0);
/** Para centrar una página horizontal o verticalmente */
// $spreadsheet->getPageSetup()->setHorizontalCentered(true);
// $spreadsheet->getPageSetup()->setVerticalCentered(false);
/** Encabezado y Pie de Pagina */
$dateini = FechaFormatVar($FechaIni, 'd/m/Y');
$datefin = FechaFormatVar($FechaFin, 'd/m/Y');
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE HORAS. DESDE ' . ($dateini) . ' A ' . $datefin);
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:L')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle('A1:L1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(12);
$spreadsheet->getColumnDimension('B')->setWidth(30);
$spreadsheet->getColumnDimension('C')->setWidth(15);
$spreadsheet->getColumnDimension('D')->setWidth(15);
$spreadsheet->getColumnDimension('E')->setWidth(12);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(40);
$spreadsheet->getStyle('A1:L1')->getAlignment()->setWrapText(true);
// $Letras = range("H","U");
// foreach ($Letras as $col) {
// }
$spreadsheet->getColumnDimension('F')->setWidth(8);
$spreadsheet->getColumnDimension('G')->setWidth(25);
$spreadsheet->getColumnDimension('J')->setWidth(10);
$spreadsheet->getColumnDimension('K')->setWidth(25);
$spreadsheet->getColumnDimension('L')->setWidth(30);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
$Letras = ['A', 'B', 'C', 'D', 'E', 'G', 'K', 'L'];
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
}

$Letras = ['F', 'H', 'I', 'J'];
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('H')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);
$spreadsheet->getStyle('I')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$Letras = ['A', 'F', 'K'];
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
}
$spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);
/** establecer un salto de impresión */
// $spreadsheet->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

$numeroDeFila = 2;
$Calculos = (!$Calculos == 1) ? "AND TIPOHORA.THoColu > 0" : '';
$query = "SELECT FICHAS1.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', FICHAS1.FicFech AS 'Fecha', 
dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario', 
dbo.fn_DiaDeLaSemana(FICHAS1.FicFech) AS 'Dia',
FICHAS1.FicHora AS 'Hora', 
TIPOHORA.THoDesc AS 'HoraDesc', 
FICHAS1.FicHsAu AS 'FicHsAu', 
FICHAS1.FicHsAu2 AS 'FicHsAu2', 
FICHAS1.FicObse AS 'Observ', 
TIPOHORACAUSA.THoCCodi AS 'Motivo', 
TIPOHORACAUSA.THoCDesc AS 'DescMotivo'
FROM FICHAS1
INNER JOIN FICHAS ON FICHAS1.FicLega=FICHAS.FicLega AND FICHAS1.FicFech=FICHAS.FicFech AND FICHAS1.FicTurn=FICHAS.FicTurn
INNER JOIN PERSONAL ON FICHAS1.FicLega=PERSONAL.LegNume
INNER JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi
LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi
WHERE FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin'
$Calculos $FilterEstruct $FiltrosFichas
ORDER BY FICHAS1.FicLega, TIPOHORA.THoColu, FICHAS1.FicHora";
// h4($query); exit;
$result = sqlsrv_query($link, $query, $param, $options);

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

while ($row = sqlsrv_fetch_array($result)) {
    # Obtener los datos de la base de datos

    $Legajo = $row['Legajo'];
    $Nombre = $row['Nombre'];
    $Fecha = $row['Fecha']->format('Y-m-d');
    $Horario = $row['Horario'];
    $Dia = $row['Dia'];
    $Hora = $row['Hora'];
    $HoraDesc = $row['HoraDesc'];
    $FicHsAu = FormatoHoraToExcel($row['FicHsAu']);
    $FicHsAu2 = FormatoHoraToExcel($row['FicHsAu2']);
    $Observ = $row['Observ'];
    $Motivo = ($row['Motivo'] == '0') ? '' : $row['Motivo'];
    $DescMotivo = $row['DescMotivo'];

    $Fecha = FormatoFechaToExcel($Fecha);

    # Escribirlos en el documento
    $spreadsheet->setCellValue('A' . $numeroDeFila, $Legajo);
    $spreadsheet->setCellValue('B' . $numeroDeFila, $Nombre);
    $spreadsheet->setCellValue('C' . $numeroDeFila, $Fecha);
    $spreadsheet->setCellValue('D' . $numeroDeFila, $Horario);
    $spreadsheet->setCellValue('E' . $numeroDeFila, $Dia);
    $spreadsheet->setCellValue('F' . $numeroDeFila, $Hora);
    $spreadsheet->setCellValue('G' . $numeroDeFila, $HoraDesc);
    $spreadsheet->setCellValue('H' . $numeroDeFila, $FicHsAu);
    $spreadsheet->setCellValue('I' . $numeroDeFila, $FicHsAu2);
    $spreadsheet->setCellValue('J' . $numeroDeFila, $Motivo);
    $spreadsheet->setCellValue('K' . $numeroDeFila, $DescMotivo);
    $spreadsheet->setCellValue('L' . $numeroDeFila, $Observ);

    $numeroDeFila++;
}

foreach (['H', 'I'] as $col) {
    $ref = "{$col}{$numeroDeFila}";
    $spreadsheet->setCellValue($ref, '=SUBTOTAL(9,' . $col . '2:' . $col . ($numeroDeFila - 1) . ')');
    $spreadsheet->getStyle($ref)->getNumberFormat()->setFormatCode("[h]:mm");
    $spreadsheet->getStyle($ref)->getFont()->setBold(true);
}

// añadir indentacion a todas las filas y celdas
foreach ($spreadsheet->getRowIterator() as $row) {
    $rowIndex = $row->getRowIndex();
    $spreadsheet->getStyle("A{$rowIndex}:L{$rowIndex}")->getAlignment()->setIndent(1);
    // añadir altura de 25 a todas las filas
    if ($rowIndex > 1) {
        $spreadsheet->getRowDimension($rowIndex)->setRowHeight(25);
    }
}

sqlsrv_free_stmt($result);
sqlsrv_close($link);
# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Horas_" . $MicroTime . ".xls";

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