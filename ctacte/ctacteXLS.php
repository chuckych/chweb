<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '../../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis=date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_CTECTE_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");
$novedad = $periodo = '';

require __DIR__ . '../../config/conect_mssql.php';
require __DIR__ . '../../filtros/filtros.php';

ultimoacc();
secure_auth_ch();
$Modulo='9';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '../../vendor/autoload.php'; 

require __DIR__ . '../valores.php';
$periodo2 = $periodo+1;
$param  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT NOVEDAD.NovCtaD AS NovCtaD, NOVEDAD.NovCtaH AS NovCtaH FROM NOVEDAD WHERE NOVEDAD.NovCodi = '$novedad'";
    $res   = sqlsrv_query($link, $query, $param, $options);
    while ($fila = sqlsrv_fetch_array($res)) {
        $NovCtaD = $fila['NovCtaD']; 
        $NovCtaH = $fila['NovCtaH']; 
    }
    sqlsrv_free_stmt($res);
    // print_r($query).PHP_EOL;   exit;

    $query = "SELECT TOP 1 CTANOVE.CTA2Lega FROM CTANOVE WHERE CTANOVE.CTA2Peri='$periodo' ORDER BY CTANOVE.CTA2Lega";
    $res   = sqlsrv_query($link, $query, $param, $options);
    while ($fila = sqlsrv_fetch_array($res)) {
        $CTA2Lega = $fila['CTA2Lega']; 
    }
    sqlsrv_free_stmt($res);
    $Lega  = ((isset($_GET['_per'])) && (!empty($_GET['_per']))) ? implode(",", $_GET['_per']) : $CTA2Lega;

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
$spreadsheet->setTitle("CTA CTE NOVEDADES");
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Periodo",
    "Cod",
    "Novedad",
    "Disponible",
    "Consumidos",
    "Total",
    "Contingente",
    "Saldo Ant.",
    "Desde",
    "Hasta",
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
$spreadsheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
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
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BCUENTA CORRIENTE NOVEDADES. PERIODO '. $periodo );
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
$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);
$spreadsheet->getColumnDimension('C')->setWidth(12);
$spreadsheet->getColumnDimension('D')->setWidth(9);
$spreadsheet->getColumnDimension('E')->setWidth(32);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);
$Letras = array('F','G', 'F','G', 'H', 'I', 'J', 'K', 'L');
foreach ($Letras as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(15);
}
/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
$Letras = array('A','B','E');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
}

$Letras = array('A','C','D','F','G','H', 'I', 'J');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
}
$Letras = array('K', 'L');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME10);
}
$Letras = array('C','D','F','G', 'H', 'I', 'J', 'K', 'L');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);


$numeroDeFila = 2;
$query="SELECT 
CTANOVE.CTA2Lega AS 'Legajo',
PERSONAL.LegApNo AS 'Nombre',
CTANOVE.CTA2Peri AS 'Periodo',
CTANOVE.CTA2Nove AS 'CodNov',
NOVEDAD.NovDesc AS 'Novedad',
NOVEDAD.NovCtaD AS 'Desde',
NOVEDAD.NovCtaH AS 'Hasta',
CTANOVE.CTA2Sald AS 'Saldo',
CTANOVE.CTA2Cant AS 'Cantidad',
(
        Select COUNT(FICHAS3.FicNove) as Novedad
        From FICHAS3,
            NOVEDAD
        Where FICHAS3.FicLega = CTANOVE.CTA2Lega
            and FICHAS3.FicFech >= '"._data_first_month_day($periodo,$NovCtaD)."'
            and FICHAS3.FicFech <= '"._data_last_month_day($periodo2,$NovCtaH)."'
            and FICHAS3.FicNove = CTANOVE.CTA2Nove
            and FICHAS3.FicNoTi >= 3
            and FICHAS3.FicNove = NOVEDAD.NovCodi
            and NOVEDAD.NovTiCo = 3
    ) AS 'Consumidos'
FROM CTANOVE,
PERSONAL,
NOVEDAD
WHERE CTANOVE.CTA2Peri = '$periodo'
AND CTANOVE.CTA2Lega <= 999999999
AND CTANOVE.CTA2Lega = PERSONAL.LegNume
AND CTANOVE.CTA2Nove = NOVEDAD.NovCodi $FilterEstruct $filtros ORDER BY CTANOVE.CTA2Peri,CTANOVE.CTA2Nove,CTANOVE.CTA2Lega";
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
    $Legajo     = $row['Legajo'];
    $Nombre     = $row['Nombre'];
    $Periodo    = $row['Periodo'];
    $CodNov     = $row['CodNov'];
    $Novedad    = $row['Novedad'];
    $Desde      = $row['Desde'];
    $Hasta      = $row['Hasta'];

    $Peri       = ($Desde > $Hasta) ? ($Periodo-1) : $Periodo;
    $Desde      = '1-'.$row['Desde'].'-'.$Peri;
    $Desde         = FormatoFechaToExcel($Desde);

    $Hasta      = '1-'.$row['Hasta'].'-'.$Periodo;
    $Hasta         = FormatoFechaToExcel($Hasta);

    $Saldo      = $row['Saldo'];
    $Cantidad   = $row['Cantidad'];
    $Consumidos = $row['Consumidos'];

    # Escribirlos en el documento
    /** A */ $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Legajo);
    /** B */ $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $Nombre);
    /** C */ $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $Periodo);
    /** D */ $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $CodNov);
    /** E */ $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $Novedad);
    /** F */ $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, ($Saldo+$Cantidad)-$Consumidos);
    /** G */ $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $Consumidos);
    /** H */ $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $Saldo+$Cantidad);
    /** I */ $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $Cantidad);
    /** J */ $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $Saldo);
    /** K */ $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, $Desde);
    /** L */ $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, $Hasta);

    /** Formato Condicional */ 
    /** Si el valor es menor 0, texto en color rojo COLOR_RED*/
    $conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
    $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
    $conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_LESSTHAN);
    $conditional1->addCondition('0');
    $conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
    $conditional1->getStyle()->getFont()->setBold(true);
    /** Si el valor es mayor o igual 0, texto en color verde COLOR_DARKGREEN*/
    $conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
    $conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS);
    $conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_GREATERTHANOREQUAL);
    $conditional2->addCondition('0');
    $conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_DARKGREEN);
    $conditional2->getStyle()->getFont()->setBold(true);

    $conditionalStyles = $spreadsheet->getStyle('F'.$numeroDeFila)->getConditionalStyles();
    $conditionalStyles[] = $conditional1;
    $conditionalStyles[] = $conditional2;

    $spreadsheet->getStyle('F'.$numeroDeFila)->setConditionalStyles($conditionalStyles);

    $numeroDeFila++;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
try {
BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
$MicroTime=microtime(true);
$NombreArchivo="Reporte_CtaCteNovedades_".$MicroTime.".xls";

$writer = new Xls($documento);
# Le pasamos la ruta de guardado
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
$writer->save('archivos/'.$NombreArchivo);
// $writer->save('php://output');

$data = array('status' => 'ok', 'archivo'=> 'archivos/'.$NombreArchivo, 'filas'=> $numeroDeFila-1);
echo json_encode($data);
exit;

} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}