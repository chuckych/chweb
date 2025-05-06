<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '/../../config/index.php';
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
$Modulo = '19';
ExisteModRol($Modulo);
E_ALL();
$UltimaFic = $PrimeraFic = '';
//require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$request = Flight::request();
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
    "Legajo", // A
    "Nombre", // B
    "Fecha", // C
    "Dia", // D
    "Horario", // E
    "Descripcion", // F
    "ID", // G
    "Asignacion", // H
    "Feriado" // I
];

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
/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');
$spreadsheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getPageMargins()->setTop(0.5);
$spreadsheet->getPageMargins()->setRight(0.3);
$spreadsheet->getPageMargins()->setLeft(0.3);
$spreadsheet->getPageMargins()->setBottom(0.5);
$spreadsheet->getPageSetup()->setFitToWidth(1);
$spreadsheet->getPageSetup()->setFitToHeight(0);
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE HORARIOS');
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
$spreadsheet->setShowGridlines(true);
$spreadsheet->getStyle('A:I')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A1:I1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

$spreadsheet->getColumnDimension('A')->setWidth(12);
$spreadsheet->getColumnDimension('B')->setWidth(30);
$spreadsheet->getColumnDimension('C')->setWidth(12);
$spreadsheet->getColumnDimension('D')->setWidth(12);
$spreadsheet->getColumnDimension('E')->setWidth(15);
$spreadsheet->getColumnDimension('F')->setWidth(30);
$spreadsheet->getColumnDimension('G')->setWidth(8);
$spreadsheet->getColumnDimension('H')->setWidth(60);
$spreadsheet->getColumnDimension('I')->setWidth(12);

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

$params = $_REQUEST;
$data = array();
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);
$params['start'] = $params['start'] ?? '0';
$params['length'] = $params['length'] ?? '99999';
$_POST['_dr'] = $_POST['_dr'] ?? '';
// (!$_POST['_dr']) ? exit : '';

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni = date('Ymd');
    $FechaFin = date('Ymd');
}

$fileJson = (file_get_contents("archivos/" . $request->data->time . ".json"));
$dataLega = (json_decode($fileJson, true));
$dataLega = $dataLega['data'][0];
$dataHorarios = (json_decode($fileJson, true));
$dataHorarios = $dataHorarios['data2']['data'];

if ($dataLega && $dataHorarios) {

    $Legajo = $dataLega['pers_legajo'];
    $ApNo = $dataLega['pers_nombre'];
    $spreadsheet->setTitle("Horarios $Legajo");
    foreach ($dataHorarios as $row) {
        $Horario = "$row[Desde] a $row[Hasta]";
        $Horario = ($row['Laboral'] == 'No') ? 'Franco' : $Horario;
        $Fecha = explode('/', $row['Fecha']);
        $Fecha = "$Fecha[2]-$Fecha[1]-$Fecha[0]";
        $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(20);
        $Feriado = ($row['Feriado'] == 'Sí') ? 'Feriado' : '';

        $spreadsheet->setCellValue("A" . $numeroDeFila, $Legajo);
        $spreadsheet->setCellValue("B" . $numeroDeFila, $ApNo);
        $spreadsheet->setCellValue("C" . $numeroDeFila, FormatoFechaToExcel($Fecha));
        $spreadsheet->setCellValue("D" . $numeroDeFila, $row['Dia']);
        $spreadsheet->setCellValue("E" . $numeroDeFila, $Horario);
        $spreadsheet->setCellValue("F" . $numeroDeFila, $row['Horario']);
        $spreadsheet->setCellValue("G" . $numeroDeFila, $row['HorarioID']);
        $spreadsheet->setCellValue("H" . $numeroDeFila, $row['TipoAsignStr']);
        $spreadsheet->setCellValue("I" . $numeroDeFila, $Feriado);
        $numeroDeFila++;
    }
}

$spreadsheet->freezePane('A2');
$spreadsheet->getRowDimension('1')->setRowHeight(30);
$ColumnCount = 3;
$RowIndex = 2;
// $spreadsheet->freezePaneByColumnAndRow($ColumnCount, $RowIndex);
$spreadsheet->getStyle('A1:I1')->applyFromArray($styleArray);
$spreadsheet->setAutoFilter('A1:I1');
$spreadsheet->fromArray($encabezado, null, 'A1');

$cols = range("A", "I");
foreach ($cols as $key => $value) {
    $spreadsheet->getStyle($value)->getAlignment()->setIndent(1);
}
$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('A')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

try {
    borrarLogs('archivos/', 1, '.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Horarios_asignados_" . $MicroTime . ".xls";

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
