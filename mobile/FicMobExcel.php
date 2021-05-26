<?php
require __DIR__ . '../../config/index.php';
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
header("Content-Type: application/json");
ultimoacc();
secure_auth_ch_json();
$Modulo = '5';
ExisteModRol($Modulo);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1');
$datehis = date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_Fichadas_Mobile_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");
E_ALL();
require __DIR__ . '../../config/conect_mssql.php';
require_once __DIR__ . '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("CHWEB")
    ->setLastModifiedBy('CHWEB')
    ->setTitle('Archivo exportado desde CHWEB')
    ->setDescription('Reporte desde CHWEB');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();
$spreadsheet->setTitle("FICHADAS MOBILE");
# Escribir encabezado de los productos
$encabezado = [
    'ID', /** u_id A*/
    'Nombre', /** name B*/
    'Dia', /** dia C*/
    'Fecha', /** Fecha D*/
    'Hora', /** time E*/
    'Zona', /** zone F*/
    'LinkMapa', /** LinkMapa G*/
    'Certeza', /** certeza H*/
    'Tipo', /** IN_OUT I*/
    'Dispositivo', /** t_type J*/
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
$spreadsheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
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
// $spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE FICHADAS MOBILE');

$DateRange  = explode(' al ', $_POST['_dr']);
$start_date = date("d-m-Y", strtotime((str_replace("/", "-", $DateRange[0]))));
$end_date   = date("d-m-Y", strtotime((str_replace("/", "-", $DateRange[1]))));

$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE FICHADAS MOBILE. DESDE '. ($start_date).' A '.$end_date );
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:J')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A1:J1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);
$spreadsheet->getColumnDimension('C')->setWidth(12);
$spreadsheet->getColumnDimension('D')->setWidth(12);
$spreadsheet->getColumnDimension('E')->setWidth(8);
$spreadsheet->getColumnDimension('F')->setWidth(27);
$spreadsheet->getColumnDimension('G')->setWidth(58);
$spreadsheet->getColumnDimension('H')->setWidth(10);
$spreadsheet->getColumnDimension('I')->setWidth(12);
$spreadsheet->getColumnDimension('J')->setWidth(13);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);
// $Letras = range("H", "U");
// foreach ($Letras as $col) {
//     $spreadsheet->getColumnDimension($col)->setWidth(10);
// }
// $Letras = range("F", "G");
// foreach ($Letras as $col) {
//     $spreadsheet->getColumnDimension($col)->setWidth(12);
// }

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
$LetrasCENTER = range("E", "H");
foreach ($LetrasCENTER as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$spreadsheet->getStyle("F")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle("G")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$spreadsheet->getStyle('D')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('E')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$spreadsheet->getStyle('A')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('H')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('A1')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

$spreadsheet->freezePane('A2');

/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);

$numeroDeFila = 2;

/** Llamamos a la API */
// $token = $_SESSION["TK_MOBILE"];
$token = TokenMobile($_SESSION["TK_MOBILE"], 'token');

$url   = "https://server.xenio.uy/metrics.php?TYPE=GET_CHECKS&tk=" . $token . "&start_date=" . $start_date . "&end_date=" . $end_date;

$json  = file_get_contents($url);
$array = json_decode($json, TRUE);
// $array = json_decode(getRemoteFile($url), true);
$respuesta = array();

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
// print_r($url);exit;

if ($array['SUCCESS'] == 'YES' && (!empty($array['MESSAGE']))) {
    foreach ($array['MESSAGE'] as $key => $valor) {
        $timestamp = $valor['timestamp'];
        /* CONVERTIMOS TIMESTAMP Y LE DAMOS FORMATO AÑO/DIA/MES */
        $datetimeFormat = 'd/m/Y';
        /** Formato de fecha */
        $datetimeFormat2 = 'Y-m-d';
        /** Formato de fecha2 */
        $dates           = new \DateTime();
        $dates           = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $dates->setTimestamp($timestamp);
        $Fecha           = $dates->format($datetimeFormat2);
        $LinkMapa        = "https://www.google.com/maps/place/" . $valor['lat'] . "," . $valor['lng'];
        $gps             = ($valor['lat'] != '0') ? '' : 'Sin GPS';
        $zone            = (!empty($valor['zone'])) ? $valor['zone'] : 'Fuera de Zona';
        $name            = $valor['name'];
        $valor['IN_OUT'] = $valor['IN_OUT'] ?? '';
        switch ($valor['IN_OUT']) {
            case 'OUT':
                $inout = 'Salida';
                break;
            case 'IN':
                $inout = 'Entrada';
                break;
            case 'AUTOMATIC':
                $inout = 'Automático';
                break;

            default:
                $inout = $valor['IN_OUT'];
                break;
        }

        $certeza = round($valor['similarity'], 0, PHP_ROUND_HALF_UP);

        $u_id     = $valor['u_id'];
        $name     = $name;
        $dia      = DiaSemana3($Fecha);
        $Fecha    = FormatoFechaToExcel($Fecha);
        $time     = FormatoHoraToExcel($valor['time']);
        $zone     = $zone;
        $LinkMapa = $LinkMapa;
        $certeza  = $certeza;
        $IN_OUT   = $inout;
        $t_type   = ucfirst($valor['t_type']);

        $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $u_id);
        $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $name);
        $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $dia);
        $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $Fecha);
        $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $time);
        $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $zone);
        $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $LinkMapa);
        $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $certeza);
        $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $IN_OUT);
        $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $t_type);

        $spreadsheet->getCell('G'.$numeroDeFila)->getHyperlink()->setUrl($LinkMapa);

        $numeroDeFila++;
    }
}

try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Fichadas_Mobile_" . $MicroTime . ".xls";

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
