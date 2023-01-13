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
$Modulo = '10';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '../../vendor/autoload.php';
// require __DIR__ . '/valores.php';

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
    ->setDescription('Reporte de Personal desde CHWEB');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();
$spreadsheet->setTitle("PERSONAL");
# Escribir encabezado de los productos
$encabezado = [
    "LEGAJO",
    "NOMBRE Y APELLIDO",
    "TIPO",
    "DNI",
    "CUIL",
    "EMP",
    "EMPRESA",
    "PLA",
    "PLANTA",
    "CONV",
    "CONVENIO",
    "SEC",
    "SECTOR",
    "SE2",
    "SECCION",
    "GRU",
    "GRUPO",
    "SUC",
    "SUCURSAL",
    "REG",
    "REGLA CH",
    "EMAIL",
    "CALLE",
    "NUM",
    "OBSERVACION",
    "PISO",
    "DEPTO",
    "LOCALIDAD",
    "CP",
    "PROVINCIA",
    "NACIONALIDAD",
    "TEL1",
    "OB. TEL1",
    "TEL2",
    "OB. TEL2",
    "ESTADO",
    "FECHA INGRESO",
    "FECHA EGRESO"
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

$spreadsheet->getStyle('A1:AL1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:AL1');
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
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE PERSONAL');
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:AL')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
// $spreadsheet->freezePaneByColumnAndRow('B', '2');
$ColumnCount=3;
$RowIndex=2;
$spreadsheet->freezePaneByColumnAndRow($ColumnCount, $RowIndex);

$spreadsheet->getStyle('A1:AL1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
// foreach (range('A:E', $spreadsheet->getHighestDataColumn()) as $col) {
//     $spreadsheet->getColumnDimension($col)->setAutoSize(true);
// }
$spreadsheet->getColumnDimension('A')->setWidth(12);
$spreadsheet->getColumnDimension('B')->setWidth(25);
$spreadsheet->getColumnDimension('C')->setWidth(10);
$spreadsheet->getColumnDimension('D')->setWidth(12);
$spreadsheet->getColumnDimension('E')->setWidth(15);
$spreadsheet->getColumnDimension('F')->setWidth(10);

$cols = array("V","W","Y","AB","AD","AE","R","T","X","Z");
foreach ($cols as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(28);
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setIndent(1);
}
$spreadsheet->getColumnDimension("W")->setWidth(42);
$spreadsheet->getColumnDimension("V")->setWidth(35);

$cols = array("AA","AC","AF","AG","AH","AI", "AJ");
foreach ($cols as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(12);
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setIndent(1);
}
$cols = array("AK","AL");
foreach ($cols as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(17);
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setIndent(1);
}

$ColNume = array("F","H","J","L","N","P","R","T","X","Z");
foreach ($ColNume as $colN) {
    $spreadsheet->getColumnDimension($colN)->setWidth(7);
    $spreadsheet->getStyle($colN)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($colN.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($colN)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($colN.'1')->getAlignment()->setIndent(1);
}
$spreadsheet->getColumnDimension("X")->setWidth(9);

$ColNume = array("B","G","I","K","M","O","Q","S","U");
foreach ($ColNume as $colN) {
    $spreadsheet->getColumnDimension($colN)->setWidth(28);
    $spreadsheet->getStyle($colN)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($colN.'1')->getAlignment()->setIndent(1);
}
$cols = getcolumnrange('AF','AI');
foreach ($cols as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(17);
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    $spreadsheet->getStyle($col)->getAlignment()->setIndent(1);
    $spreadsheet->getStyle($col.'1')->getAlignment()->setIndent(1);
}
// $spreadsheet->getColumnDimension('E')->setWidth(13);

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

// $spreadsheet->getStyle('A1:V1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');

// $spreadsheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $spreadsheet->getStyle('C')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
// $spreadsheet->getStyle('F:AL')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// $spreadsheet->getStyle('F1:AL1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// $spreadsheet->getStyle('C')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

// $spreadsheet->getStyle('F:U')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

// $spreadsheet->getStyle('V')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

// $spreadsheet->getStyle('A')
//     ->getNumberFormat()
//     ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('D')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED3);
$ColNume = array("C","D","E");
foreach ($ColNume as $colN) {
    $spreadsheet->getStyle($colN.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getStyle($colN)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$ColNume = array("A","F","H","J","L","N","P","R","T","X","Z","AA");
foreach ($ColNume as $colN) {
    $spreadsheet->getStyle($colN)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
}
$spreadsheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('A')->getAlignment()->setIndent(1);
$spreadsheet->getStyle('A1')->getAlignment()->setIndent(1);

$ColFecha = array("AK","AL");
foreach ($ColFecha as $colF) {
    $spreadsheet->getStyle($colF)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
}
// $spreadsheet->getStyle("AK")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
// $spreadsheet->getStyle("AL")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

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
require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '/valores.php';

 $sql_query="SELECT PERSONAL.LegNume AS 'LegNume', PERSONAL.LegApNo AS 'LegApNo', PERSONAL.LegTipo AS 'LegTipo', PERSONAL.LegDocu AS 'LegDocu', PERSONAL.LegCUIT AS 'LegCUIT', PERSONAL.LegEmpr AS 'EmpCodi', EMPRESAS.EmpRazon AS 'EmpRazo', PERSONAL.LegPlan AS 'PlaCodi', PLANTAS.PlaDesc AS 'PlaDesc', PERSONAL.LegConv AS 'ConCodi', CONVENIO.ConDesc AS 'ConDesc', PERSONAL.LegSect AS 'SecCodi', SECTORES.SecDesc AS 'SecDesc', PERSONAL.LegSec2 AS 'Se2Codi', SECCION.Se2Desc AS 'Se2Desc', PERSONAL.LegGrup AS 'GruCodi', GRUPOS.GruDesc AS 'GruDesc', PERSONAL.LegSucu AS 'SucCodi', SUCURSALES.SucDesc AS 'SucDesc', PERSONAL.LegMail AS 'LegMail', PERSONAL.LegDomi AS 'LegDomi', PERSONAL.LegDoNu AS 'LegDoNu', PERSONAL.LegDoOb AS 'LegDoOb', PERSONAL.LegDoPi AS 'LegDoPi', PERSONAL.LegDoDP AS 'LegDoDP', LOCALIDA.LocDesc AS 'LocDesc', PERSONAL.LegCOPO AS 'LegCOPO', PROVINCI.ProDesc AS 'ProDesc', NACIONES.NacDesc AS 'NacDesc', (CASE PERSONAL.LegFeEg WHEN '17530101' THEN '0' ELSE '1' END) AS 'LegEsta', PERSONAL.LegFeEg AS 'LegFeEg', PERSONAL.LegFeIn AS 'LegFeIn', PERSONAL.LegTel1 AS 'LegTel1', PERSONAL.LegTeO1 AS 'LegTeO1', PERSONAL.LegTel2 AS 'LegTel2', PERSONAL.LegTeO2 AS 'LegTeO2', PERSONAL.LegRegCH AS 'LegRegl', REGLASCH.RCDesc AS 'RCHDesc' FROM PERSONAL INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND SECTORES.SecCodi=SECCION.SecCodi INNER JOIN EMPRESAS ON PERSONAL.LegEmpr=EMPRESAS.EmpCodi INNER JOIN CONVENIO ON PERSONAL.LegConv=CONVENIO.ConCodi INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi INNER JOIN PROVINCI ON PERSONAL.LegProv=PROVINCI.ProCodi INNER JOIN LOCALIDA ON PERSONAL.LegLoca=LOCALIDA.LocCodi INNER JOIN NACIONES ON PERSONAL.LegNaci=NACIONES.NacCodi LEFT JOIN REGLASCH ON PERSONAL.LegRegCH=REGLASCH.RCCodi WHERE PERSONAL.LegNume >'0' $filtros $FilterEstruct $OrderBy";

// print_r($sql_query); exit;

$result = sqlsrv_query($link, $sql_query, $param, $options);

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

function getcolumnrange($min,$max){
    $pointer=strtoupper($min);
    $output=array();
    while(positionalcomparison($pointer,strtoupper($max))<=0){
       array_push($output,$pointer);
       $pointer++;
    }
    return $output;
}

function positionalcomparison($a,$b){
 $a1=stringtointvalue($a); $b1=stringtointvalue($b);
 if($a1>$b1)return 1;
 else if($a1<$b1)return -1;
 else return 0;
}

/*
* e.g. A=1 - B=2 - Z=26 - AA=27 - CZ=104 - DA=105 - ZZ=702 - AAA=703
*/
function stringtointvalue($str){
 $amount=0;
 $strarra=array_reverse(str_split($str));

 for($i=0;$i<strlen($str);$i++){
    $amount+=(ord($strarra[$i])-64)*pow(26,$i);
 }
 return $amount;
}

while ($row = sqlsrv_fetch_array($result)) {
    # Obtener los datos de la base de datos
    // $FicLega    = $row['FicLega'];
    // $Nombre     = $row['Nombre'];
    // $FicFechaAs = $row['FicFechaAs']->format('Y-m-d');
    // $FicFechaAs = FormatoFechaToExcel($FicFechaAs);
    $LegNume = $row['LegNume'];
    $LegApNo = $row['LegApNo'];
    $LegTipo = ($row['LegTipo']==0)?'Mensual':'Jornal';
    $LegDocu = $row['LegDocu'];
    $LegCUIT = $row['LegCUIT'];
    $EmpCodi = $row['EmpCodi'];
    $EmpRazo = $row['EmpRazo'];
    $PlaCodi = $row['PlaCodi'];
    $PlaDesc = $row['PlaDesc'];
    $ConCodi = $row['ConCodi'];
    $ConDesc = $row['ConDesc'];
    $SecCodi = $row['SecCodi'];
    $SecDesc = $row['SecDesc'];
    $Se2Codi = $row['Se2Codi'];
    $Se2Desc = $row['Se2Desc'];
    $GruCodi = $row['GruCodi'];
    $GruDesc = $row['GruDesc'];
    $SucCodi = $row['SucCodi'];
    $SucDesc = $row['SucDesc'];
    $LegMail = $row['LegMail'];
    $LegDomi = $row['LegDomi'];
    $LegDoNu = $row['LegDoNu'];
    $LegDoOb = $row['LegDoOb'];
    $LegDoPi = $row['LegDoPi'];
    $LegDoDP = $row['LegDoDP'];
    $LocDesc = $row['LocDesc'];
    $LegCOPO = $row['LegCOPO'];
    $ProDesc = $row['ProDesc'];
    $NacDesc = $row['NacDesc'];
    $LegEsta = ($row['LegEsta']==0)?'Activo':'Inactivo';
    $LegFeEg = $row['LegFeEg'];

    $LegFeIn = $row['LegFeIn']->format('Y-m-d');
    $LegFeIn = FormatoFechaToExcel($LegFeIn);
    $LegFeIn = ($LegFeIn<0)?0:$LegFeIn;

    $LegFeEg = $row['LegFeEg']->format('Y-m-d');
    $LegFeEg = FormatoFechaToExcel($LegFeEg);
    $LegFeEg = ($LegFeEg<0)?0:$LegFeEg;

    $LegTel1 = $row['LegTel1'];
    $LegTeO1 = $row['LegTeO1'];
    $LegTel2 = $row['LegTel2'];
    $LegTeO2 = $row['LegTeO2'];
    $LegRegl = $row['LegRegl'];
    $RCHDesc = $row['RCHDesc'];

    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1,$numeroDeFila, $LegNume);
    $spreadsheet->setCellValueByColumnAndRow(2,$numeroDeFila, $LegApNo);
    $spreadsheet->setCellValueByColumnAndRow(3,$numeroDeFila, $LegTipo);
    $spreadsheet->setCellValueByColumnAndRow(4,$numeroDeFila, $LegDocu);
    $spreadsheet->setCellValueByColumnAndRow(5,$numeroDeFila, $LegCUIT);
    $spreadsheet->setCellValueByColumnAndRow(6,$numeroDeFila, $EmpCodi);
    $spreadsheet->setCellValueByColumnAndRow(7,$numeroDeFila, $EmpRazo);
    $spreadsheet->setCellValueByColumnAndRow(8,$numeroDeFila, $PlaCodi);
    $spreadsheet->setCellValueByColumnAndRow(9,$numeroDeFila, $PlaDesc);
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $ConCodi);
    $spreadsheet->setCellValueByColumnAndRow(11, $numeroDeFila, $ConDesc);
    $spreadsheet->setCellValueByColumnAndRow(12, $numeroDeFila, $SecCodi);
    $spreadsheet->setCellValueByColumnAndRow(13, $numeroDeFila, $SecDesc);
    $spreadsheet->setCellValueByColumnAndRow(14, $numeroDeFila, $Se2Codi);
    $spreadsheet->setCellValueByColumnAndRow(15, $numeroDeFila, $Se2Desc);
    $spreadsheet->setCellValueByColumnAndRow(16, $numeroDeFila, $GruCodi);
    $spreadsheet->setCellValueByColumnAndRow(17, $numeroDeFila, $GruDesc);
    $spreadsheet->setCellValueByColumnAndRow(18, $numeroDeFila, $SucCodi);
    $spreadsheet->setCellValueByColumnAndRow(19, $numeroDeFila, $SucDesc);
    $spreadsheet->setCellValueByColumnAndRow(20, $numeroDeFila, $LegRegl);
    $spreadsheet->setCellValueByColumnAndRow(21, $numeroDeFila, $RCHDesc);
    $spreadsheet->setCellValueByColumnAndRow(22, $numeroDeFila, $LegMail);
    $spreadsheet->setCellValueByColumnAndRow(23, $numeroDeFila, $LegDomi);
    $spreadsheet->setCellValueByColumnAndRow(24, $numeroDeFila, $LegDoNu);
    $spreadsheet->setCellValueByColumnAndRow(25, $numeroDeFila, $LegDoOb);
    $spreadsheet->setCellValueByColumnAndRow(26, $numeroDeFila, $LegDoPi);
    $spreadsheet->setCellValueByColumnAndRow(27, $numeroDeFila, $LegDoDP);
    $spreadsheet->setCellValueByColumnAndRow(28, $numeroDeFila, $LocDesc);
    $spreadsheet->setCellValueByColumnAndRow(29, $numeroDeFila, $LegCOPO);
    $spreadsheet->setCellValueByColumnAndRow(30, $numeroDeFila, $ProDesc);
    $spreadsheet->setCellValueByColumnAndRow(31, $numeroDeFila, $NacDesc);
    $spreadsheet->setCellValueByColumnAndRow(32, $numeroDeFila, $LegTel1);
    $spreadsheet->setCellValueByColumnAndRow(33, $numeroDeFila, $LegTeO1);
    $spreadsheet->setCellValueByColumnAndRow(34, $numeroDeFila, $LegTel2);
    $spreadsheet->setCellValueByColumnAndRow(35, $numeroDeFila, $LegTeO2);
    $spreadsheet->setCellValueByColumnAndRow(36, $numeroDeFila, $LegEsta);
    $spreadsheet->setCellValueByColumnAndRow(37, $numeroDeFila, $LegFeIn);
    $spreadsheet->setCellValueByColumnAndRow(38, $numeroDeFila, $LegFeEg);
    $Today2 = ($LegFeIn == 0) ? 'TODAY()':'AK'.$numeroDeFila;
    $Today = ($LegFeEg == 0) ? 'TODAY()':'AL'.$numeroDeFila;
    if ($LegEsta=='Inactivo') {
        $rangeCol = getcolumnrange('A','AL');
        foreach ($rangeCol as $value) {
            $spreadsheet->getStyle($value.$numeroDeFila)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
        }
    }
    if ($LegFeEg==0) {
        $spreadsheet->getStyle('AL'.$numeroDeFila)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_EFEFEFEF);
    }
    if ($LegFeIn==0) {
        $spreadsheet->getStyle('AK'.$numeroDeFila)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_EFEFEFEF);
    }
    // $FormulaAntiguedad='=DATEDIF(AK'.$numeroDeFila.',AL'.$numeroDeFila.',"Y") & " Años con " & DATEDIF(AK'.$numeroDeFila.',AL'.$numeroDeFila.',"ym") & " meses y " & DATEDIF(AK'.$numeroDeFila.',AL'.$numeroDeFila.',"md") & " dias."';
    //$FormulaAntiguedad='=DATEDIF('.$Today2.','.$Today.',"Y")& "Años con "&DATEDIF('.$Today2.','.$Today.',"ym")&" meses y "&DATEDIF('.$Today2.','.$Today.',"md")&" dias."';
    // $FormulaAntiguedad='=DATEDIF('.$Today2.','.$Today.',"Y")&" Años con "&DATEDIF('.$Today2.','.$Today.',"ym")&" meses y "&DATEDIF('.$Today2.','.$Today.',"md")&" dias."';

    //$spreadsheet->setCellValueByColumnAndRow(39, $numeroDeFila, $FormulaAntiguedad);
    //$spreadsheet->setCellValueByColumnAndRow(17, $numeroDeFila, '=IF(ISBLANK(O'.$numeroDeFila.'),"Sin fecha",IF(O'.$numeroDeFila.'<J'.$numeroDeFila.',"En Fecha",IF(O'.$numeroDeFila.'>J'.$numeroDeFila.',"Fuera de Fecha")))');
    $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(19);

    $numeroDeFila++;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Personal_" . $MicroTime . ".xls";

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
