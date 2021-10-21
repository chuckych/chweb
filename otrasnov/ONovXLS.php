<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '../../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis=date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_NOVEDADES_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

require __DIR__ . '../../config/conect_mssql.php';
require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../valores.php';

ultimoacc();
secure_auth_ch();
$Modulo='17';
ExisteModRol($Modulo);
E_ALL(); 

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
$spreadsheet->setTitle("OTRAS NOVEDADES");
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Fecha",
    "Dia",
    "Horario",
    "Cod",
    "Novedad",
    "Tipo",
    "Valor",
    "Observacion",
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
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE OTRAS NOVEDADES. DESDE '. ($dateini).' A '.$datefin );
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
$spreadsheet->getColumnDimension('C')->setWidth(12);
$spreadsheet->getColumnDimension('D')->setWidth(13);
$spreadsheet->getColumnDimension('E')->setWidth(13);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);
// $Letras = range("H","U");
// foreach ($Letras as $col) {
// }
$spreadsheet->getColumnDimension('F')->setWidth(6);
$spreadsheet->getColumnDimension('G')->setWidth(32);
$spreadsheet->getColumnDimension('H')->setWidth(7);
$spreadsheet->getColumnDimension('I')->setWidth(10);
$spreadsheet->getColumnDimension('J')->setWidth(32);


// $Letras = range("F","G");
// foreach ($Letras as $col) {
//     $spreadsheet->getColumnDimension($col)->setWidth(12);
// }

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
$Letras = array('A','B', 'C','D','E','G','H','J','I');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
}

$spreadsheet->getStyle('F')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('F')
->getNumberFormat()
->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('I')
->getNumberFormat()
->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);

$spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);
/** establecer un salto de impresión */
// $spreadsheet->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

$numeroDeFila = 2;

$query="SELECT DISTINCT 
FICHAS2.FicLega AS 'Legajo',
PERSONAL.LegApNo AS 'Nombre',
FICHAS2.FicFech AS 'Fecha',
dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario' , 
dbo.fn_DiaDeLaSemana(FICHAS2.FicFech) AS 'Dia',
FICHAS2.FicONov AS 'Cod',
OTRASNOV.ONovDesc AS 'Novedad',
OTRASNOV.ONovTipo AS 'Tipo',
FICHAS2.FicValor AS 'Valor',
FICHAS2.FicObsN AS 'Observacion'
FROM FICHAS2
INNER JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega AND FICHAS2.FicFech = FICHAS.FicFech AND FICHAS2.FicTurn = FICHAS.FicTurn
INNER JOIN OTRASNOV ON FICHAS2.FicONov = OTRASNOV.ONovCodi 
INNER JOIN PERSONAL ON FICHAS2.FicLega = PERSONAL.LegNume
WHERE FICHAS2.FicFech BETWEEN '$FechaIni' AND '$FechaFin' 
$FilterEstruct $FilterEstruct2 $FiltrosFichas
ORDER BY FICHAS2.FicFech, FICHAS2.FicLega";
// h4($query); exit;
$result = sqlsrv_query($link, $query,$param, $options);

function FormatoHoraToExcel($Hora){
    $Hora      = !empty($Hora) ? $Hora:'00:00:00' ;
    $timestamp = new \DateTime($Hora);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Hora = ($excelTimestamp - $excelDate)==0 ? '': $excelTimestamp - $excelDate;
    return $Hora;
}
function FormatoFechaToExcel($Fecha){
    $timestamp = new \DateTime($Fecha);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Fecha = ($excelTimestamp);
    return $Fecha;
}

while ($row = sqlsrv_fetch_array($result)) {
    # Obtener los datos de la base de datos
    $Legajo      = $row['Legajo'];
    $Nombre      = $row['Nombre'];
    $Fecha       = $row['Fecha']->format('Y-m-d');
    $Dia         = $row['Dia'];
    $Horario     = $row['Horario'];
    $Cod         = $row['Cod'];
    $Novedad     = $row['Novedad'];
    $Tipo        = ($row['Tipo']=='1')?'Horas':'Valor';
    $Valor       = $row['Valor'];
    $Observacion = $row['Observacion'];
    
    $Fecha = FormatoFechaToExcel($Fecha);

    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Legajo);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $Nombre);
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $Fecha);
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $Dia);
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $Horario);
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $Cod);
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $Novedad);
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $Tipo);
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $Valor);
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $Observacion);

    $numeroDeFila++;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
# Crear un "escritor"
try {
BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
$MicroTime=microtime(true);
$NombreArchivo="Reporte_Otras_Novedades_".$MicroTime.".xls";

$writer = new Xls($documento);
# Le pasamos la ruta de guardado
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
$writer->save('archivos/'.$NombreArchivo);
// $writer->save('php://output');

$data = array('status' => 'ok', 'archivo'=> 'archivos/'.$NombreArchivo);
echo json_encode($data);
exit;

} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}