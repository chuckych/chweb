<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '../../../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis = date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_Fichadas_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

ultimoacc();
secure_auth_ch();

$Modulo = '29';
ExisteModRol($Modulo);

error_reporting(E_ALL);
ini_set('display_errors', '0');

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

$data = ($_POST['datos']);
$data = json_decode(($data), true);

$Desde = $data['data'][0]['desde']; 
$Hasta = $data['data'][0]['hasta']; 
$Desde = str_replace("/", "-", $Desde);
$Hasta = str_replace("/", "-", $Hasta);
// print_r($Hasta);
// exit;

# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Fecha Desde",
    "Fecha Hasta",
    "Total Presentes",
    "Total Ausentes",
    "Total Días",
    "Meses Presentes",
    "Meses Ausentes",
    "Total Meses",
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
$titleHoja = 'Del '.$Desde.' al '.$Hasta;
$spreadsheet->setTitle($titleHoja);
$spreadsheet->getStyle('A1:J1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:J1');
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

$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);

$Letras = array("C","D");
foreach ($Letras as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(11);
}
$Letras = array("E","F","G","H","I","J");
foreach ($Letras as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(10);
}
$spreadsheet->getRowDimension('1')->setRowHeight(45);

$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
$spreadsheet->getStyle('D')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
$spreadsheet->getStyle('E:G')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$spreadsheet->getStyle('J')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$spreadsheet->getStyle('H:I')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

$spreadsheet->freezePane('A2');

$spreadsheet->getStyle('C1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getStyle('C1:J1')->getAlignment()->setWrapText(true);
$spreadsheet->getStyle('C:J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getStyle('A:B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:J')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A1:J1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

$numeroDeFila = 2;

// print_r($data['data']); exit;
foreach ($data['data'] as $r) {
    $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(19);
    $legajo          = $r['legajo'];
    $nombre          = $r['nombre'];
    $desde           = $r['desde'];
    $hasta           = $r['hasta'];
    $_presentes      = $r['_presentes'];
    $_ausentes       = $r['_ausentes'];
    $_totaldias      = $r['_totaldias'];
    $_convpres       = $r['_convpres'];
    $_convaus        = $r['_convaus'];
    $_convpres       = str_replace(",", ".", $_convpres);
    $_convaus        = str_replace(",", ".", $_convaus);
    $_totalmesesconv = $r['_totalmesesconv'];

    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $legajo);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $nombre);
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $desde);
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $hasta);
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $_presentes);
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $_ausentes);
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $_totaldias);
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $_convpres);
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $_convaus);
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $_totalmesesconv);
    $numeroDeFila++;
}
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE PRESENTISMO DESDE '.$desde.' A '.$hasta);

# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $NombreArchivo = "Reporte_Presentismo_" . time() . ".xls";

    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save('archivos/' . $NombreArchivo);
    // $writer->save('php://output');

    $data = array('status' => 'ok', 'desde'=>$desde, 'hasta'=>$hasta , 'archivo' => 'archivos/' . $NombreArchivo);
    echo json_encode($data);
    exit;
} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}
