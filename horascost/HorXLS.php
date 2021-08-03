<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '../../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis=date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_HORAS_COSTEADAS'.$datehis.'.xls"');
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
$Modulo='3';
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
$spreadsheet->setTitle("HORAS");
# Escribir encabezado de los productos
$encabezado = [
   /** COL: A */ "Legajo",
   /** COL: B */ "Nombre",
   /** COL: C */ "Fecha",
   /** COL: D */ "Dia",
   /** COL: E */ "Horario",
   /** COL: F */ "Desde",
   /** COL: G */ "Tipo Hora",
   /** COL: H */ "Hs Hechas",
   /** COL: I */ "Hs Autor.",
   /** COL: J */ "Costo",
   /** COL: K */ "Tarea",
   /** COL: L */ "Empresa",
   /** COL: M */ "Planta",
   /** COL: N */ "Sucursal",
   /** COL: O */ "Grupo",
   /** COL: P */ "Sector",
   /** COL: Q */ "Sección"
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
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
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

$spreadsheet->getStyle('A1:Q1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
// $spreadsheet->setAutoFilter('A1:Q1');

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
$dateini = FechaFormatVar($FechaIni, 'd/m/Y');
$datefin = FechaFormatVar($FechaFin, 'd/m/Y');

$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE HORAS COSTEADAS. DESDE '. ($dateini).' A '.$datefin );
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:Q')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle('A1:Q1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);
$spreadsheet->getColumnDimension('C')->setWidth(13);
$spreadsheet->getColumnDimension('D')->setWidth(13);
$spreadsheet->getColumnDimension('E')->setWidth(13);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);
// $Letras = range("H","U");
// foreach ($Letras as $col) {
// }
$spreadsheet->getColumnDimension('F')->setWidth(8);
$spreadsheet->getColumnDimension('G')->setWidth(22);
$spreadsheet->getColumnDimension('H')->setWidth(10);
$spreadsheet->getColumnDimension('I')->setWidth(10);
$spreadsheet->getColumnDimension('J')->setWidth(10);
$spreadsheet->getColumnDimension('K')->setWidth(22);
$spreadsheet->getColumnDimension('L')->setWidth(22);
$spreadsheet->getColumnDimension('M')->setWidth(22);
$spreadsheet->getColumnDimension('N')->setWidth(22);
$spreadsheet->getColumnDimension('O')->setWidth(22);
$spreadsheet->getColumnDimension('P')->setWidth(22);
$spreadsheet->getColumnDimension('Q')->setWidth(22);


// $Letras = range("F","G");
// foreach ($Letras as $col) {
//     $spreadsheet->getColumnDimension($col)->setWidth(12);
// }

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
$Letras = array('A','B','C','D','E','G','K','L','M','N','O','P','Q');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
}

$Letras = array('F','H','I','J');
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('F')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);
$spreadsheet->getStyle('H')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);
$spreadsheet->getStyle('I')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$spreadsheet->getStyle('A')
->getNumberFormat()
->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$spreadsheet->getStyle('J')
->getNumberFormat()
->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

$spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);
/** establecer un salto de impresión */
// $spreadsheet->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);

$numeroDeFila = 2;
$Calculos = (!$Calculos==1) ? "AND TIPOHORA.THoColu > 0" : '';
$query="SELECT FICHAS01.FicLega AS 'Legajo',
    PERSONAL.LegApNo AS 'Nombre',
    FICHAS01.FicFech AS 'Fecha',
    dbo.fn_DiaDeLaSemana(FICHAS01.FicFech) AS 'Dia',
    dbo.fn_HorarioAsignado( FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF ) AS 'Horario',
    FICHAS01.FicCFec as 'Desde',
    TIPOHORA.THoDesc AS 'TipoHora',
    FICHAS01.FicHsHeC AS 'HsHechas',
    FICHAS01.FicHsAuC AS 'HsAutor',
    FICHAS01.FicCosto AS 'Costo',
    TAREAS.TareDesc AS 'Tarea',
    Empresas.EmpRazon AS 'Empresa',
    PLANTAS.PlaDesc AS 'Planta',
    SUCURSALES.SucDesc AS 'Sucursal',
    GRUPOS.GruDesc AS 'Grupos',
    SECTORES.SecDesc AS 'Sector',
    SECCION.Se2Desc AS 'Seccion'
FROM FICHAS01
    INNER JOIN FICHAS ON FICHAS01.FicLega = FICHAS.FicLega
    AND FICHAS01.FicFech = FICHAS.FicFech
    AND FICHAS01.FicTurn = FICHAS.FicTurn
    INNER JOIN PERSONAL ON FICHAS01.FicLega = PERSONAL.LegNume
    INNER JOIN TIPOHORA ON FICHAS01.FicHora = TIPOHORA.THoCodi
    INNER JOIN EMPRESAS ON FICHAS01.FicEmpr = EMPRESAS.EmpCodi
    INNER JOIN TAREAS ON FICHAS01.FicTare = TAREAS.TareCodi
    INNER JOIN PLANTAS ON FICHAS01.FicPlan = PLANTAS.PlaCodi
    INNER JOIN SUCURSALES ON FICHAS01.FicSucu = SUCURSALES.SucCodi
    INNER JOIN GRUPOS ON FICHAS01.FicGrup = GRUPOS.GruCodi
    INNER JOIN SECTORES ON FICHAS01.FicSect = SECTORES.SecCodi
    INNER JOIN SECCION ON FICHAS01.FicSec2 = SECCION.Se2Codi
    AND SECCION.SecCodi = SECTORES.SecCodi
WHERE FICHAS01.FicLega > 0
    AND FICHAS01.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $Calculos $FilterEstruct $FiltrosFichas
ORDER BY FICHAS01.FicLega,
    FICHAS01.FicFech,
    FICHAS01.FicHora,
    FICHAS01.FicCFec";


// print_r($query); exit;
$result = sqlsrv_query($link, $query,$param, $options);

function FormatoHoraToExcel($Hora){
    $Hora      = !empty($Hora) ? $Hora:'00:00:00' ;
    $timestamp = new \DateTime($Hora);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Hora = ($excelTimestamp - $excelDate)==0 ? '': $excelTimestamp - $excelDate;
    return $Hora;
}
function FormatoHoraToExcel2($Hora){
    $Hora      = !empty($Hora) ? $Hora:'00:00:00' ;
    $timestamp = new \DateTime($Hora);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Hora = ($excelTimestamp - $excelDate)==0 ? 'Inicio': $excelTimestamp - $excelDate;
    return $Hora;
}
function FormatoHoraToExcel3($Hora){
    $Hora      = !empty($Hora) ? $Hora:'00:00:00' ;
    $timestamp = new \DateTime($Hora);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Hora = ($excelTimestamp - $excelDate)==0 ? '00:00': $excelTimestamp - $excelDate;
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
    $Legajo   = $row['Legajo'];
    $Nombre   = $row['Nombre'];
    $Fecha    = $row['Fecha']->format('Y-m-d');
    $Dia      = $row['Dia'];
    $Horario  = $row['Horario'];
    $Desde    = FormatoHoraToExcel2($row['Desde']->format('H:i'));
    // $Desde    = $Desde = '00:00' ? 'Inicio' : $Desde;
    $TipoHora = $row['TipoHora'];
    $HsHechas = FormatoHoraToExcel($row['HsHechas']);
    $HsAutor  = FormatoHoraToExcel3($row['HsAutor']);
    $Costo    = $row['Costo'];
    $Tarea    = $row['Tarea'];
    $Empresa  = $row['Empresa'];
    $Planta   = $row['Planta'];
    $Sucursal = $row['Sucursal'];
    $Grupos   = $row['Grupos'];
    $Sector   = $row['Sector'];
    $Seccion  = $row['Seccion'];

    $Fecha = FormatoFechaToExcel($Fecha);

    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Legajo);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $Nombre);
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $Fecha);
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $Dia);
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $Horario);
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $Desde);
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $TipoHora);
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $HsHechas);
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $HsAutor);
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $Costo);
    $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, $Tarea);
    $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, $Empresa);
    $spreadsheet->setCellValueByColumnAndRow(13, $numeroDeFila, $Planta);
    $spreadsheet->setCellValueByColumnAndRow(14, $numeroDeFila, $Sucursal);
    $spreadsheet->setCellValueByColumnAndRow(15, $numeroDeFila, $Grupos);
    $spreadsheet->setCellValueByColumnAndRow(16, $numeroDeFila, $Sector);
    $spreadsheet->setCellValueByColumnAndRow(17, $numeroDeFila, $Seccion);

    $numeroDeFila++;
}
$UltimaFila    = $numeroDeFila-1;
$UltimaFila2   = $numeroDeFila;
$UltimaFila3   = $numeroDeFila+1;

$UltimaI       = 'I'.($UltimaFila);
$UltimaI_2     = 'I'.($UltimaFila2);
$UltimaI_3     = 'I'.($UltimaFila3);

$UltimaH       = 'H'.($UltimaFila);
$UltimaH_2     = 'H'.($UltimaFila2);
$UltimaH_3     = 'H'.($UltimaFila3);

$UltimaJ       = 'J'.($UltimaFila);
$UltimaJ_2     = 'J'.($UltimaFila2);
$UltimaJ_3     = 'J'.($UltimaFila3);

$UltimaA_2     = 'A'.($UltimaFila2);
$UltimaA_3     = 'A'.($UltimaFila3);

$UltimaQ       = 'Q'.($UltimaFila);
$UltimaQ_2     = 'Q'.($UltimaFila2);
$UltimaQ_3     = 'Q'.($UltimaFila3);

$UltimaG_2     = 'G'.($UltimaFila2);
$UltimaG_3     = 'G'.($UltimaFila3);

$FormulaAutor       = '=SUBTOTAL(109,I2:'.$UltimaI.')';
$FormulaAutorTotal  = '=SUM(I2:'.$UltimaI.')';
$FormulaHechas      = '=SUBTOTAL(109,H2:'.$UltimaH.')';
$FormulaHechasTotal = '=SUM(H2:'.$UltimaH.')';
$FormulaCosto       = '=SUBTOTAL(109,J2:'.$UltimaJ.')';
$FormulaCostoTotal  = '=SUM(J2:'.$UltimaJ.')';

$spreadsheet->setAutoFilter('A1:'.$UltimaQ);

$spreadsheet->setCellValue($UltimaI_2,$FormulaAutor);
// $spreadsheet->setCellValue($UltimaI_3,$FormulaAutorTotal);
$spreadsheet->setCellValue($UltimaH_2,$FormulaHechas);
// $spreadsheet->setCellValue($UltimaH_3,$FormulaHechasTotal);
$spreadsheet->setCellValue($UltimaJ_2,$FormulaCosto);
// $spreadsheet->setCellValue($UltimaJ_3,$FormulaCostoTotal);
$spreadsheet->getCell($UltimaG_2)->setValue('Total');
// $spreadsheet->getCell($UltimaG_3)->setValue('Totales');
$spreadsheet->getRowDimension($UltimaFila2)->setRowHeight(25);
// $spreadsheet->getRowDimension($UltimaFila3)->setRowHeight(25);

$spreadsheet->getStyle($UltimaI_2)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME9);
// $spreadsheet->getStyle($UltimaI_3)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME9);
$spreadsheet->getStyle($UltimaH_2)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME9);
// $spreadsheet->getStyle($UltimaH_3)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME9);
// $spreadsheet->getStyle('J')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

$spreadsheet->getStyle($UltimaA_2.':'.$UltimaQ_2)->applyFromArray($styleArray2);
// $spreadsheet->getStyle($UltimaA_3.':'.$UltimaQ_3)->applyFromArray($styleArray2);

sqlsrv_free_stmt($result);
sqlsrv_close($link);
# Crear un "escritor"
try {
BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
$MicroTime=microtime(true);
$NombreArchivo="Reporte_Horas_Costeadas_".$MicroTime.".xls";

$writer = new Xls($documento);
# Le pasamos la ruta de guardado
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
$writer->save('archivos/'.$NombreArchivo);
// $writer->save('php://output');

$data = array('status' => 'ok', 'archivo'=> 'archivos/'.$NombreArchivo, 'Otros'=>$UltimaQ_3);
echo json_encode($data);
exit;

} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}