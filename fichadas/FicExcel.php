<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '../../config/index.php';
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

require __DIR__ . '../../config/conect_mssql.php';

ultimoacc();
secure_auth_ch();
$Modulo = '3';
ExisteModRol($Modulo);
E_ALL();
$UltimaFic = $PrimeraFic = '';
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
$spreadsheet->setTitle("FICHADAS");
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Fecha",
    "Dia",
    "Horario",
    "Primera",
    "Ultima",
    "Entra",
    "Sale",
    "Entra",
    "Sale",
    "Entra",
    "Sale",
    "Entra",
    "Sale",
    "Entra",
    "Sale",
    "Entra",
    "Sale",
    "Entra",
    "Sale",
    "Cant."
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

$spreadsheet->getStyle('A1:V1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:V1');
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
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE FICHADAS');
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:V')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle('A1:V1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(10);
$spreadsheet->getColumnDimension('B')->setWidth(27);
$spreadsheet->getColumnDimension('C')->setWidth(12);
$spreadsheet->getColumnDimension('D')->setWidth(11);
$spreadsheet->getColumnDimension('E')->setWidth(13);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(25);
$Letras = range("H", "U");
foreach ($Letras as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(10);
}
$Letras = range("F", "G");
foreach ($Letras as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(12);
}

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:V1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');

$spreadsheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('F:V')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getStyle('F1:V1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('F:U')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$spreadsheet->getStyle('V')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('A')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('A1')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
/** Mostrar / ocultar una columna */
// $spreadsheet->getColumnDimension('E')->setVisible(true);
// $spreadsheet->getColumnDimension('F')->setVisible(true);
/** establecer un salto de impresión */
// $spreadsheet->setBreak('A10', \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
$commentRichText = $spreadsheet->getComment('F1')->getText()->createTextRun('Descripción:');
$commentRichText1 = $spreadsheet->getComment('G1')->getText()->createTextRun('Descripción:');
$commentRichText->getFont()->setBold(true);
$commentRichText1->getFont()->setBold(true);

$spreadsheet->getComment('F1')->getText()->createTextRun("\r\nPrimer Fichada\r\ndel día");
$spreadsheet->getComment('G1')->getText()->createTextRun("\r\nUltima Fichada\r\ndel día");

$numeroDeFila = 2;
require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../valores.php';
$FicFalta = ($FicFalta && $FicFalta != 'null') ? " HAVING (MAX(rn) % 2) = 1" : "";
/** Fichadas Inconsistentes */
$query = "WITH CTE AS(
    SELECT *,
        ROW_NUMBER() OVER (PARTITION BY RegLega, RegFeAs ORDER BY RegHoRe) AS rn
    FROM REGISTRO
    WHERE RegFeAs BETWEEN '$FechaIni' AND '$FechaFin'

)
SELECT CTE.RegLega AS 'FicLega', PERSONAL.LegApNo AS 'Nombre',
    (CTE.RegFeAs) AS 'FicFechaAs', 
                dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario' , 
                FICHAS.FicHorE AS 'FicHorE', FICHAS.FicHorS AS 'FicHorS', FICHAS.FicDiaL AS 'FicDiaL', FICHAS.FicDiaF AS 'FicDiaF',
                dbo.fn_DiaDeLaSemana(CTE.RegFeAs) AS 'Dia',
    COUNT(rn)AS  'Fic_Cant',
    MAX(CASE WHEN rn = 1 THEN RegHoRe END)AS  'Primera',
    MAX(CASE WHEN rn > 0 THEN RegHoRe END)AS  'Ultima',
    MAX( CASE WHEN rn = 1 THEN RegHoRe END) AS 'Fic_1',
    MAX( CASE WHEN rn = 2 THEN RegHoRe END) AS 'Fic_2',
    MAX( CASE WHEN rn = 3 THEN RegHoRe END) AS 'Fic_3', 
    MAX( CASE WHEN rn = 4 THEN RegHoRe END) AS 'Fic_4', 
    MAX( CASE WHEN rn = 5 THEN RegHoRe END) AS 'Fic_5', 
    MAX( CASE WHEN rn = 6 THEN RegHoRe END) AS 'Fic_6',
    MAX( CASE WHEN rn = 7 THEN RegHoRe END) AS 'Fic_7', 
    MAX( CASE WHEN rn = 8 THEN RegHoRe END) AS 'Fic_8',
    MAX( CASE WHEN rn = 9 THEN RegHoRe END) AS 'Fic_9', 
    MAX( CASE WHEN rn = 10 THEN RegHoRe END) AS 'Fic_10',
    MAX( CASE WHEN rn = 11 THEN RegHoRe END) AS 'Fic_11', 
    MAX( CASE WHEN rn = 12 THEN RegHoRe END) AS 'Fic_12',
    MAX( CASE WHEN rn = 13 THEN RegHoRe END) AS 'Fic_13', 
    MAX( CASE WHEN rn = 14 THEN RegHoRe END) AS 'Fic_14'
FROM CTE 
INNER JOIN PERSONAL ON CTE.RegLega = PERSONAL.LegNume
LEFT JOIN FICHAS ON CTE.RegLega = FICHAS.FicLega AND CTE.RegFeAs = FICHAS.FicFech
WHERE CTE.RegLega > 0 $FilterEstruct $filtros
GROUP BY CTE.RegLega,
         CTE.RegFeAs, 
         PERSONAL.LegApNo,
         FICHAS.FicHorE,
         FICHAS.FicHorS,
         FICHAS.FicDiaL,
         FICHAS.FicDiaF
         $FicFalta
ORDER BY CTE.RegFeAs desc, CTE.RegLega";
// print_r($query); exit;
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
function HoraVal($hora){
    $hora = str_replace(':', '', $hora);
    $hora = str_replace(' ', '', $hora);
    return intval($hora);
}
while ($row = sqlsrv_fetch_array($result)) {
    # Obtener los datos de la base de datos
    $FicLega    = $row['FicLega'];
    $Nombre     = $row['Nombre'];
    $FicFechaAs = $row['FicFechaAs']->format('Y-m-d');
    $FicFechaAs = FormatoFechaToExcel($FicFechaAs);
    $Horario    = $row['Horario'];
    $FicHorE   = $row['FicHorE'];
    $FicHorS   = $row['FicHorS'];
    $Dia        = $row['Dia'];
    $Fic_Cant   = $row['Fic_Cant'];
    $Primera    = $row['Primera'];
    $Ultima     = $row['Ultima'];

    $Pri = intval(str_replace(':', '', $Primera));
    $Ult  = intval(str_replace(':', '', $Ultima));

    $horEnt = intval(str_replace(':', '', $FicHorE));
    $horSal  = intval(str_replace(':', '', $FicHorS));

    $Ultima = ($Ultima == $Primera) ? '' : $Ultima;

    if (($horEnt) > ($horSal)) {
        $PrimeraFic = $Ultima;
    } 
    if (($horEnt) < ($horSal)) {
        $UltimaFic = $Primera;
    }

    $arrFic = array(
        'Fic_1'  => (HoraVal($row['Fic_1'])),
        'Fic_2'  => (HoraVal($row['Fic_2'])),
        'Fic_3'  => (HoraVal($row['Fic_3'])),
        'Fic_4'  => (HoraVal($row['Fic_4'])),
        'Fic_5'  => (HoraVal($row['Fic_5'])),
        'Fic_6'  => (HoraVal($row['Fic_6'])),
        'Fic_7'  => (HoraVal($row['Fic_7'])),
        'Fic_8'  => (HoraVal($row['Fic_8'])),
        'Fic_9'  => (HoraVal($row['Fic_9'])),
        'Fic_10' => (HoraVal($row['Fic_10'])),
        'Fic_11' => (HoraVal($row['Fic_11'])),
        'Fic_12' => (HoraVal($row['Fic_12'])),
        'Fic_13' => (HoraVal($row['Fic_13'])),
        'Fic_14' => (HoraVal($row['Fic_14']))
    );
    // order
    $arrFic = array_values($arrFic);
    $arrFic = array_filter($arrFic);
    // ksort($arrFic);
    // order reverse
    // $arrFic = array_reverse($arrFic);
    arsort($arrFic);


    

    $arr[] = array(
        'FicLega'    => $FicLega,
        'Nombre'     => $Nombre,
        'FicFechaAs' => $FicFechaAs,
        'Horario'    => $Horario,
        'entrada'    => ($horEnt),
        'salida'     => ($horSal),
        'Dia'        => $Dia,
        'Fic_Cant'   => $Fic_Cant,
        'Primera'    => $Primera,
        'Ultima'     => $Ultima,
        'Primera2'    => ($horEnt) > ($horSal) ? $Ultima : $Primera,
        'Ultima2'     => ($horEnt) > ($horSal) ? $Primera : $Ultima,
        'arrFic'      => $arrFic,
    );
    print_r($arr);
    exit;

    $Primera = FormatoHoraToExcel($Primera);
    $Ultima  = FormatoHoraToExcel($Ultima);
    $Fic_1   = FormatoHoraToExcel($row['Fic_1']);
    $Fic_1   = FormatoHoraToExcel($row['Fic_1']);
    $Fic_2   = FormatoHoraToExcel($row['Fic_2']);
    $Fic_3   = FormatoHoraToExcel($row['Fic_3']);
    $Fic_4   = FormatoHoraToExcel($row['Fic_4']);
    $Fic_5   = FormatoHoraToExcel($row['Fic_5']);
    $Fic_6   = FormatoHoraToExcel($row['Fic_6']);
    $Fic_7   = FormatoHoraToExcel($row['Fic_7']);
    $Fic_8   = FormatoHoraToExcel($row['Fic_8']);
    $Fic_9   = FormatoHoraToExcel($row['Fic_9']);
    $Fic_10  = FormatoHoraToExcel($row['Fic_10']);
    $Fic_11  = FormatoHoraToExcel($row['Fic_11']);
    $Fic_12  = FormatoHoraToExcel($row['Fic_12']);
    $Fic_13  = FormatoHoraToExcel($row['Fic_13']);
    $Fic_14  = FormatoHoraToExcel($row['Fic_14']);

    // $Primera = (str_replace('', ':', $Primera));
    // $Ultima  = (str_replace('', ':', $Ultima));




    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $FicLega);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $Nombre);
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $FicFechaAs);
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $Dia);
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $Horario);
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $Primera);
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $Ultima);
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $Fic_1);
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $Fic_2);
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $Fic_3);
    $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, $Fic_4);
    $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, $Fic_5);
    $spreadsheet->setCellValueByColumnAndRow(13, $numeroDeFila, $Fic_6);
    $spreadsheet->setCellValueByColumnAndRow(14, $numeroDeFila, $Fic_7);
    $spreadsheet->setCellValueByColumnAndRow(15, $numeroDeFila, $Fic_8);
    $spreadsheet->setCellValueByColumnAndRow(16, $numeroDeFila, $Fic_9);
    $spreadsheet->setCellValueByColumnAndRow(17, $numeroDeFila, $Fic_10);
    $spreadsheet->setCellValueByColumnAndRow(18, $numeroDeFila, $Fic_11);
    $spreadsheet->setCellValueByColumnAndRow(19, $numeroDeFila, $Fic_12);
    $spreadsheet->setCellValueByColumnAndRow(20, $numeroDeFila, $Fic_13);
    $spreadsheet->setCellValueByColumnAndRow(21, $numeroDeFila, $Fic_14);
    $spreadsheet->setCellValueByColumnAndRow(22, $numeroDeFila, $Fic_Cant);
    $numeroDeFila++;
}

sqlsrv_free_stmt($result);
sqlsrv_close($link);
# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Fichadas_" . $MicroTime . ".xls";

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
