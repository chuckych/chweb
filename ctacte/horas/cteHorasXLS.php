<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '../../../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis=date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_CTACTEHORAS_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

require __DIR__ . '../../../config/conect_mssql.php';
require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../valores.php';

ultimoacc();
secure_auth_ch();
$Modulo='3';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '../../../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));

    $FechaPag = (isset($_POST['k'])) ? test_input($_POST['k']):'';
    $FechaIni = ((isset($_POST['k']))) ? $FechaPag : $FechaIni;

    $FechaIni = FechaString($FechaIni);
    $FechaFin = FechaString($FechaFin);
    $Visualizar = test_input($_POST['Visualizar']);

    switch ($_POST['cta']) {
        case '2':
            $cta = "AND Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) > 0";
            break;
        case '1':
            $cta = "AND Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) < 0";
            break;
        default:
            // $cta = "AND Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) <> 0";
            $cta ='';
            break;
    }

    switch ($Visualizar) {
        case '2':
            $FiltroNulo = 'AND (S.FrancoCompe1+S.FrancoCompe2+S.JornadaReducida1+S.JornadaReducida2+Ex.HorasEx) <> 0';
            break;
        default:
            $FiltroNulo = '';
            break;
    }

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("CHWEB")
    ->setLastModifiedBy('CHWEB')
    ->setTitle('Archivo exportado desde CHWEB')
    ->setDescription('Reporte desde CHWEB');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();
$spreadsheet->setTitle("CTA CTE HORAS");
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Hechas",
    "Franco -",
    "Franco +",
    "Jor Red -",
    "Jor Red +",
    "Cta Cte",
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

$spreadsheet->getStyle('A1:H1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:H1');
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
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BCTA CTE DE HORAS. DESDE '. ($dateini).' A '.$datefin );
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:H')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle('A1:H1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);
$letras = array('C', 'D', 'E', 'F', 'G', 'H');
foreach ($letras as $letra) {
    $spreadsheet->getColumnDimension($letra)->setWidth(13);
    $spreadsheet->getStyle($letra.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getStyle($letra)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getStyle($letra)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME9);
}
$spreadsheet->getStyle('H')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

$Letras = array('A','B');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
}
$spreadsheet->getStyle('A')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$commentRichText = $spreadsheet->getComment('D1')->getText()->createTextRun('Descripción:');
$commentRichText1 = $spreadsheet->getComment('F1')->getText()->createTextRun('Descripción:');
$commentRichText->getFont()->setBold(true);
$commentRichText1->getFont()->setBold(true);

$spreadsheet->getComment('D1')->getText()->createTextRun("\r\nFranco compensatorio\r\nResta en Cta Cte");
$spreadsheet->getComment('F1')->getText()->createTextRun("\r\nFranco compensatorio\r\nSuma en Cta Cte");

$numeroDeFila = 2;

    $query="SELECT PERSONAL.LegNume as 'Legajo', PERSONAL.LegApNo as 'Nombre', Ex.HorasEx, S.FrancoCompe1, S.FrancoCompe2, S.JornadaReducida1, S.JornadaReducida2, CtaCte=Ex.HorasEx-((S.JornadaReducida1 + S.FrancoCompe1)-(S.JornadaReducida2 + S.FrancoCompe2)) FROM PERSONAL CROSS APPLY (SELECT ISNULL(SUM( CASE WHEN N.NovTiCo=1 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS FrancoCompe1, ISNULL(SUM( CASE WHEN N.NovTiCo=4 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS FrancoCompe2, ISNULL(SUM( CASE WHEN N.NovTiCo=2 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS JornadaReducida1, ISNULL(SUM( CASE WHEN N.NovTiCo=5 THEN LEFT(H3.FicHoras,2)*60+RIGHT(H3.FicHoras,2) ELSE 0 END),0) AS JornadaReducida2 FROM FICHAS3 H3 JOIN NOVEDAD N ON H3.FicNove=N.NovCodi WHERE H3.FicLega=PERSONAL.LegNume AND H3.FicFech >='$FechaIni' AND H3.FicFech <='$FechaFin' AND H3.FicNove >0 ) S CROSS APPLY (SELECT ISNULL(SUM((LEFT(H1.FicHsAu,2)*60+RIGHT(H1.FicHsAu,2)) - (LEFT(H1.FicHsAu2,2)*60+RIGHT(H1.FicHsAu2,2))),0) AS HorasEx FROM FICHAS1 H1 JOIN TIPOHORA TH ON H1.FicHora=TH.THoCodi WHERE H1.FicLega=PERSONAL.LegNume AND H1.FicFech >='$FechaIni' AND H1.FicFech <='$FechaFin' AND TH.THoCtaH=1 AND H1.FicHsAu2 < H1.FicHsAu) Ex WHERE PERSONAL.LegNume >0 AND PERSONAL.LegFeEg='17530101' $cta $FiltroNulo $FilterEstruct $filtros";
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
    $Legajo           = $row['Legajo'];
    $Nombre           = $row['Nombre'];
    $HorasEx          = (MinExcel($row['HorasEx']));
    $FrancoCompe1     = (MinExcel($row['FrancoCompe1']));
    $FrancoCompe2     = (MinExcel($row['FrancoCompe2']));
    $JornadaReducida1 = (MinExcel($row['JornadaReducida1']));
    $JornadaReducida2 = (MinExcel($row['JornadaReducida2']));
    $CtaCte           = (($row['CtaCte']));

    if($CtaCte>=0){
        $CtaCte = (FormatHoraR($CtaCte)); /** positivo */
    }
    else{
        $CtaCte  = str_replace("-", "", $CtaCte);
        $CtaCte = "-".(FormatHoraR($CtaCte)); /** negativo */
    }

    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Legajo); /** A */
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $Nombre); /** B */
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $HorasEx); /** C */
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $FrancoCompe1); /** D */
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $FrancoCompe2); /** E */
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $JornadaReducida1); /** F */
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $JornadaReducida2);/** G */
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $CtaCte); /** H */
    $numeroDeFila++;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
# Crear un "escritor"
try {
BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
$MicroTime=microtime(true);
$NombreArchivo="Reporte_CtaCte_Horas_".$MicroTime.".xls";

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