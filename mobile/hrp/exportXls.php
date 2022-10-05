<?php
// print_r($xlsData).exit;
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis = date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_Fichadas_Mobile_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

// require __DIR__ . '../../../config/conect_mssql.php';
ultimoacc();
secure_auth_ch();
$Modulo = '32';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("CHWEB")
    ->setLastModifiedBy('CHWEB')
    ->setTitle('Archivo exportado desde CHWeb')
    ->setDescription('Reporte Fichadas Mobile');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();
$spreadsheet->setTitle("FICHADAS MOBILE");
# Escribir encabezado de los productos
$encabezado = [
    'ID',
    /** u_id A*/
    'Nombre',
    /** name B*/
    'Dia',
    /** dia C*/
    'Fecha',
    /** Fecha D*/
    'Hora',
    /** time E*/
    'Zona',
    /** zone F*/
    'Posición GPS',
    /** Posición G*/
    'Reconocimiento Facial',
    /** certeza H*/
    'Tipo',
    /** Tipo I*/
    'Dispositivo',
    /** t_type J*/
    'Evento Zona',
    /** t_type K*/
    'Evento Dispositivo',
    /** t_type L*/
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
/** ajustar a 1 página de ancho por infinitas páginas de alto */
$spreadsheet->getPageSetup()->setFitToWidth(1);
$spreadsheet->getPageSetup()->setFitToHeight(0);
/** Para centrar una página horizontal o verticalmente */
// $spreadsheet->getPageSetup()->setHorizontalCentered(true);
// $spreadsheet->getPageSetup()->setVerticalCentered(false);
/** Encabezado y Pie de Pagina */
// $spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE FICHADAS MOBILE');

$DateRange  = explode(' al ', $_POST['_drMob2']);
$start_date = date("d-m-Y", strtotime((str_replace("/", "-", $DateRange[0]))));
$end_date   = date("d-m-Y", strtotime((str_replace("/", "-", $DateRange[1]))));

$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE FICHADAS MOBILE. DESDE ' . ($start_date) . ' A ' . $end_date);
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:L')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle('A1:L1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(14);
$spreadsheet->getColumnDimension('B')->setWidth(27);
$spreadsheet->getColumnDimension('C')->setWidth(12);
$spreadsheet->getColumnDimension('D')->setWidth(12);
$spreadsheet->getColumnDimension('E')->setWidth(8);
$spreadsheet->getColumnDimension('F')->setWidth(27);
$spreadsheet->getColumnDimension('G')->setWidth(20);
$spreadsheet->getColumnDimension('H')->setWidth(25);
$spreadsheet->getColumnDimension('I')->setWidth(10);
$spreadsheet->getColumnDimension('J')->setWidth(20);
$spreadsheet->getColumnDimension('K')->setWidth(10);
$spreadsheet->getColumnDimension('L')->setWidth(14);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(33);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
$spreadsheet->getStyle("E")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getStyle("F")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle("G")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle("H")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle("K")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getStyle("L")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getStyle('D')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('E')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$spreadsheet->getStyle('A')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('A1')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

// $spreadsheet->freezePane('A2');

$ColNume = array("A","B","C","D","E","F","G","H","I","J","K","L");
foreach ($ColNume as $colN) {
    $spreadsheet->getStyle($colN)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($colN.'1')->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($colN.'1')->getAlignment()->setWrapText(true);
}
$ColumnCount=3;
$RowIndex=2;
$spreadsheet->freezePaneByColumnAndRow($ColumnCount, $RowIndex);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);

$numeroDeFila = 2;
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
if ($xlsData) {
    foreach ($xlsData as $key => $valor) {
        $timestamp = $valor['timestamp'];
        $datetimeFormat = 'd/m/Y';
        $datetimeFormat2 = 'Y-m-d';
        $dates           = new \DateTime(); // current date/time
        $dates           = new \DateTime('now', new \DateTimeZone('America/Argentina/Buenos_Aires')); // current date/time
        $dates->setTimestamp($timestamp); // set new timestamp
        $Fecha           = $dates->format($datetimeFormat2); // output = 2012-03-15
        $LinkMapa        = "https://www.google.com/maps/place/" . $valor['regLat'] . "," . $valor['regLng']; // Link Maps google
        $gps             = ($valor['regLat'] != '0') ? '' : 'Sin GPS';
        switch ($valor['operationType'] ?? '') {
            case '-1':
                $operationType = 'Fichada';
                break;
            case '1':
                $operationType = 'Ronda';
                break;
            case '3':
                $operationType = 'Evento';
                break;
            default:
                $operationType = 'Desconocido';
                break;
        }
        $userID      = $valor['userID']; // ID del usuario
        $userName    = $valor['userName']; // Nombre del usuario
        $regDay      = $valor['regDay']; // Dia de la fichada
        $Fecha       = FormatoFechaToExcel($valor['regDate']); // Fecha de la fichada
        // $Fecha    = FormatoFechaToExcel($Fecha); // Fecha de la fichada
        $regHora     = FormatoHoraToExcel($valor['regHora']); // Hora de la fichada
        $zoneName    = (!empty($valor['zoneName'])) ? $valor['zoneName'] : 'Fuera de Zona'; // Zona de la fichada
        $confidence  = $valor['confidenceFaceStr'];
        $tipo        = $operationType;
        $device      = $valor['device'];
        $eventDevice = $valor['eventDevice'];
        $eventZone   = $valor['eventZone'];

        if (empty($device)) {
            $spreadsheet->getStyle('J' . $numeroDeFila)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
            $device = $valor['phoneid'];
        } else {
            $spreadsheet->getStyle('J' . $numeroDeFila)
                ->getNumberFormat()
                ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
        }
        $textMap = $valor['regLat'] != 0 ? 'Ver en Google Maps' : 'Sin GPS';

        $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $userID);
        $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $userName);
        $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $regDay);
        $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $Fecha);
        $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $regHora);
        $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $zoneName);
        $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $textMap);
        $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $confidence);
        $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $tipo);
        $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $device);
        $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, $eventZone);
        $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, $eventDevice);

        ($valor['regLat'] != 0) ? $spreadsheet->getCell('G' . $numeroDeFila)->getHyperlink()->setUrl($LinkMapa):''; // Si no hay GPS no se puede ver el link mapa 

        $numeroDeFila++;
    }
}

try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save($routeFile3); // Guardamos el archivo en la ruta especificada
    // $writer->save('php://output');
} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}
