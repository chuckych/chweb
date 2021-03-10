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

require __DIR__ . '../../../config/conect_mssql.php';

ultimoacc();
secure_auth_ch();

$Modulo = '29';
ExisteModRol($Modulo);

error_reporting(E_ALL);
ini_set('display_errors', '1');


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
$spreadsheet->setTitle("REPORTE");
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
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE PRESENTISMO');
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:J')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle('A1:J1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);
$spreadsheet->getColumnDimension('C')->setWidth(13);
$spreadsheet->getColumnDimension('D')->setWidth(13);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);

$Letras = array("E","F","G","H","I","J");
foreach ($Letras as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(10);
}

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:V1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');

$spreadsheet->getStyle('A:D')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $spreadsheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('E:J')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
$spreadsheet->getStyle('D')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

// $spreadsheet->getStyle('F:U')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$spreadsheet->getStyle('E:G')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$spreadsheet->getStyle('J')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$spreadsheet->getStyle('H:G')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);

// $spreadsheet->getStyle('A')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

// $spreadsheet->getStyle('A1')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);
/** establecer un salto de impresión */
// $spreadsheet->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
// $commentRichText = $spreadsheet->getComment('F1')->getText()->createTextRun('Descripción:');
// $commentRichText1 = $spreadsheet->getComment('G1')->getText()->createTextRun('Descripción:');
// $commentRichText->getFont()->setBold(true);
// $commentRichText1->getFont()->setBold(true);

// $spreadsheet->getComment('F1')->getText()->createTextRun("\r\nPrimer Fichada\r\ndel día");
// $spreadsheet->getComment('G1')->getText()->createTextRun("\r\nUltima Fichada\r\ndel día");

$numeroDeFila = 2;
require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../valores.php';
function calcularMeses($a)
{
    $fecha1 = new DateTime($a[0]);
    $fecha2 = new DateTime($a[1]);
    //calcular con diff
    $fecha = $fecha1->diff($fecha2);

    $fechay = $fecha->y;
    $fecham = $fecha->m;
    $fechad = $fecha->d;
    $fechah = $fecha->h;
    $fechai = $fecha->i;

    $fechameses = $fechay * 12 + $fecham;
    return $fechameses;
}
function calcularFechas($a)
{
    $fecha1 = new DateTime($a[0]);
    $fecha2 = new DateTime($a[1]);
    //calcular con diff
    $fecha = $fecha1->diff($fecha2);

    $fechay = $fecha->y;
    $fecham = $fecha->m;
    $fechad = $fecha->d;

    // $tiempo = printf('%d años, %d meses, %d días', $fecha->y, $fecha->m, $fecha->d);
    // return $tiempo;
}
$TotalMeses = calcularMeses(array($FechaIni, $FechaFin)) + 1;
$TotalFechas = '';

$presentes = ($_SESSION['CONCEPTO_PRESENTES']);
$ausentes  = ($_SESSION['CONCEPTO_AUSENTES']);

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$query = "SELECT DISTINCT FICHAS.FicLega AS 'legajo', PERSONAL.LegApNo AS 'nombre',
(SELECT COUNT(FI.FicLega) FROM FICHAS FI INNER JOIN FICHAS3 ON FI.FicLega = FICHAS3.FicLega AND FI.FicFech = FICHAS3.FicFech AND FI.FicTurn = FICHAS3.FicTurn WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FI.FicHsTr = '00:00' AND  FICHAS.FicLega = FI.FicLega AND FICHAS3.FicNove IN ($presentes)) AS 'TotDiasPre',
(SELECT COUNT(FI2.FicLega) FROM FICHAS FI2 WHERE FI2.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FI2.FicHsTr > '00:00' AND  FICHAS.FicLega = FI2.FicLega) AS 'TotDiasPreHs', 
(SELECT COUNT(FI3.FicLega) FROM FICHAS FI3 INNER JOIN FICHAS3 ON FI3.FicLega = FICHAS3.FicLega AND FI3.FicFech = FICHAS3.FicFech AND FI3.FicTurn = FICHAS3.FicTurn WHERE FICHAS3.FicFech BETWEEN '$FechaIni' AND '$FechaFin' AND FI3.FicHsTr = '00:00' AND  FICHAS.FicLega = FI3.FicLega AND FICHAS3.FicNove IN ($ausentes)) AS 'TotDiasAus' FROM FICHAS INNER JOIN FICHAS3 ON  FICHAS.FicLega = FICHAS3.FicLega AND FICHAS.FicFech = FICHAS3.FicFech AND FICHAS.FicTurn = FICHAS3.FicTurn INNER JOIN PERSONAL ON  FICHAS.FicLega = PERSONAL.LegNume WHERE  FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltrosFichas $FilterEstruct ORDER BY FICHAS.FicLega";
// print_r($query); exit;

function ConvMesesPresentes($TotalDiasPresentes, $TotalMeses, $TotalDias)
{
    if ($TotalDiasPresentes > 0) {
        $v = ($TotalDiasPresentes * $TotalMeses) / $TotalDias;
        /** Hacemos el calculo */
        $v = round($v, 2, PHP_ROUND_HALF_UP);
        return $v;
    }
}

$result = sqlsrv_query($link, $query, $param, $options);

function FormatoHoraToExcel($Hora)
{
    $Hora      = !empty($Hora) ? $Hora : '00:00:00';
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

while ($r = sqlsrv_fetch_array($result)) {

    $legajo         = $r['legajo'];
    $nombre         = $r['nombre'];
    $TotDiasPre     = $r['TotDiasPre'];
    $TotDiasPreHs   = $r['TotDiasPreHs'];
    $TotDiasAus     = $r['TotDiasAus'];
    $presentes      = $TotDiasPre + $TotDiasPreHs;
    $TotalDias      = $presentes + $TotDiasAus;
    $TotalDias      = $presentes + $TotDiasAus;
    $ConvPres       = (ConvMesesPresentes($presentes, $TotalMeses, $TotalDias));
    $ConvAus        = (ConvMesesPresentes($TotDiasAus, $TotalMeses, $TotalDias));
    $TotalMesesConv = ($ConvPres) + ($ConvAus);
    $TotalMesesConv = number_format($TotalMesesConv, 0);
    $ConvAus        = number_format($ConvAus, 2, ',', '.');
    $ConvPres       = number_format($ConvPres, 2, ',', '.');

    # Obtener los datos de la base de datos
    // $FechaIni = $FechaIni->format('Y-m-d');
    $FechaIni = (Fech_Format_Var($FechaIni, 'd-m-Y'));
    $FechaFin = (Fech_Format_Var($FechaFin, 'd-m-Y'));
    // $FechaFin = FormatoFechaToExcel($FechaFin);
    // $FechaIni = ($FechaIni);
    // $FechaFin = ($FechaFin);

    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $legajo);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $nombre);
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $FechaIni);
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $FechaFin);
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $presentes);
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $TotDiasAus);
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $TotalDias);
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $ConvPres);
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $ConvAus);
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $TotalMesesConv);
    $numeroDeFila++;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Presentismo_" . $MicroTime . ".xls";

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
