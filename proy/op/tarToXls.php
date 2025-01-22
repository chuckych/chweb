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
ultimoacc();
secure_auth_ch();
$Modulo = '37';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("CHWEB")
    ->setLastModifiedBy('CHWEB')
    ->setTitle('Exportado desde Proyectos ' . $_SERVER['REMOTE_ADDR'])
    ->setDescription('Reporte de Tareas');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();
$spreadsheet->setTitle("Reporte de Tareas");
# Escribir encabezado de los productos
$encabezado = [
    'ID', // Columna A
    'Proyecto', // Columna B
    'Proceso', // Columna C
    'Costo Proceso', // Columna D
    'Plano', // Columna E
    'Empresa', // Columna F
    'Responsable', // Columna G
    'Fecha Inicio', // Columna H
    'Hora Inicio', // Columna I
    'Fecha Fin', // Columna J
    'Hora Fin', // Columna K
    'Duración', // Columna L
    'Estado', // Columna M
    'Tipo Fin', // Columna N
];
$ColIni = 'A1';
$ColIni2 = 'A';
$ColFin = 'N1';
$ColFin2 = 'N';

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
$styleArray2 = [
    'font' => [
        'bold' => true,
    ],
    // 'alignment' => [
    //     'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    // ],
    'borders' => [
        'top' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];

$spreadsheet->getStyle($ColIni . ':' . $ColFin)->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter($ColIni . ':' . $ColFin);
/** El último argumento es por defecto A1 */
$spreadsheet->fromArray($encabezado, null, $ColIni);
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

$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE TAREAS');
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle($ColIni2 . ':' . $ColFin2)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getStyle($ColIni, $ColFin)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

/** cálculo automático de ancho de columna */
// foreach (range($ColIni .':'. $ColFin, $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(20);
$spreadsheet->getColumnDimension('C')->setWidth(20);
$spreadsheet->getColumnDimension('D')->setWidth(16);
$spreadsheet->getColumnDimension('E')->setWidth(20);
$spreadsheet->getColumnDimension('F')->setWidth(20);
$spreadsheet->getColumnDimension('G')->setWidth(20);
$spreadsheet->getColumnDimension('H')->setWidth(14);
$spreadsheet->getColumnDimension('I')->setWidth(13);
$spreadsheet->getColumnDimension('J')->setWidth(14);
$spreadsheet->getColumnDimension('K')->setWidth(13);
$spreadsheet->getColumnDimension('L')->setWidth(12);
$spreadsheet->getColumnDimension('M')->setWidth(14);
$spreadsheet->getColumnDimension('N')->setWidth(14);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle($ColIni, $ColFin)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
// $spreadsheet->getStyle("E")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// $spreadsheet->getStyle("F")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $spreadsheet->getStyle("G")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $spreadsheet->getStyle("H")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$spreadsheet->getStyle('H')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
$spreadsheet->getStyle('J')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('I')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);
$spreadsheet->getStyle('K')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);
$spreadsheet->getStyle('L')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME9);
$spreadsheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

// $spreadsheet->getStyle('A')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

// $spreadsheet->getStyle('A1')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

// $spreadsheet->freezePane('A2');

$ColNume = range($ColIni2, $ColFin2);
foreach ($ColNume as $colN) {
    $spreadsheet->getStyle($colN)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($colN . '1')->getAlignment()->setIndent(1);
}
$ColumnCount = 2;
$RowIndex = 2;
$spreadsheet->freezePaneByColumnAndRow($ColumnCount, $RowIndex);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);

$numeroDeFila = 2;
$respuesta = array();

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
    if (($Fecha != '0000-00-00')) {
        $timestamp = new \DateTime($Fecha);
        $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
        $excelDate = floor($excelTimestamp);
        $Fecha = ($excelTimestamp);
        return $Fecha;
    } else {
        return '';
    }
}
if ($xlsData) {
    foreach ($xlsData as $key => $valor) {

        $idTar = $valor['TareID'];
        $nombreProy = $valor['proyecto']['nombre'];
        $nombreProceso = $valor['proceso']['nombre'];
        $costoProceso = $valor['totales']['cost'];
        $plano = $valor['plano']['nombre'];
        $nombreEmpresa = $valor['empresa']['nombre'];
        $nombreResponsable = $valor['responsable']['nombre'];
        $fechaInicio = FechaFormatVar($valor['fechas']['TareIni'], 'Y-m-d');
        $horaInicio = $valor['fechas']['inicioHora'];
        $fechaFin = FechaFormatVar($valor['fechas']['TareFin'], 'Y-m-d');
        if (empty($fechaFin)) {
            $fechaFin = '';
        } else {
            $fechaFin = FormatoFechaToExcel($fechaFin);
        }
        $horaFin = $valor['fechas']['finHora'];
        $duracion = $valor['fechas']['duracion'];
        $estado = $valor['estado'];
        $tipoFin = $valor['fechas']['finTipo'];

        $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $idTar);
        $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $nombreProy);
        $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $nombreProceso);
        $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $costoProceso);
        $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $plano);
        $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $nombreEmpresa);
        $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $nombreResponsable);
        $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, FormatoFechaToExcel($fechaInicio));
        $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, FormatoHoraToExcel($horaInicio));
        $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, ($fechaFin));
        $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, FormatoHoraToExcel($horaFin));
        $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, FormatoHoraToExcel($duracion));
        $spreadsheet->setCellValueByColumnAndRow(13, $numeroDeFila, $estado);
        $spreadsheet->setCellValueByColumnAndRow(14, $numeroDeFila, ucfirst($tipoFin));
        $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(20);
        $numeroDeFila++;
    }
    $UltimaFila = $numeroDeFila - 1;
    $UltimaFila2 = $numeroDeFila;
    $UltimaL = 'L' . ($UltimaFila);
    $UltimaL_2 = 'L' . ($UltimaFila2);
    $FormulaHechas = '=SUBTOTAL(109,L2:' . $UltimaL . ')';
    $UltimaA_2 = 'A' . ($UltimaFila2);
    $UltimaN_2 = 'N' . ($UltimaFila2);
    $spreadsheet->setCellValue($UltimaL_2, $FormulaHechas);
    $spreadsheet->getStyle($UltimaL_2)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME9);
    $spreadsheet->getStyle($UltimaA_2 . ':' . $UltimaN_2)->applyFromArray($styleArray2);

    $UltimaD = 'D' . ($UltimaFila);
    $UltimaD_2 = 'D' . ($UltimaFila2);
    // $UltimaD_3     = 'D' . ($UltimaFila2+1);
    $FormulaHechas = '=SUBTOTAL(109,D2:' . $UltimaD . ')';
    // $FormulaPromedio      = '=SUBTOTAL(101,D2:' . $UltimaD . ')';
    $spreadsheet->setCellValue($UltimaD_2, $FormulaHechas);
    // $spreadsheet->setCellValue($UltimaD_3, $FormulaPromedio);
    $spreadsheet->getStyle($UltimaD_2)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    // $spreadsheet->getStyle($UltimaD_3)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

    $UltimaA = 'A' . ($UltimaFila);
    $UltimaA_2 = 'A' . ($UltimaFila2);
    $FormulaHechas = '=SUBTOTAL(2,A2:' . $UltimaA . ')';
    $spreadsheet->setCellValue($UltimaA_2, $FormulaHechas);
    $spreadsheet->getStyle($UltimaA_2)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

    $spreadsheet->getRowDimension($UltimaFila2)->setRowHeight(25);
} else {
    echo PrintRespuestaJson('error', 'No se encontraron datos para exportar');
    exit;
}

$tm = microtime(true);
$routeFile = 'archivos/reporte_tareas_' . $idCliente . $tm . '.xls';
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save($routeFile); // Guardamos el archivo en la ruta especificada
    // $writer->save('php://output');
} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}