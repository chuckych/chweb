<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
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
ultimoacc();
secure_auth_ch();
$Modulo = '3';
ExisteModRol($Modulo);
E_ALL();
$UltimaFic = $PrimeraFic = '';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

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
    "Cant",
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
    "Entra",
    "Sale"
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
$spreadsheet->getStyle('A1:V1')->applyFromArray($styleArray);
$spreadsheet->setAutoFilter('A1:V1');
$spreadsheet->fromArray($encabezado, null, 'A1');
/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');
$spreadsheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
$spreadsheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
$spreadsheet->getPageMargins()->setTop(0.5);
$spreadsheet->getPageMargins()->setRight(0.3);
$spreadsheet->getPageMargins()->setLeft(0.3);
$spreadsheet->getPageMargins()->setBottom(0.5);
$spreadsheet->getPageSetup()->setFitToWidth(1);
$spreadsheet->getPageSetup()->setFitToHeight(0);
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE FICHADAS');
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
$spreadsheet->setShowGridlines(true);
$spreadsheet->getStyle('A:V')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle('A1:V1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->getColumnDimension('A')->setWidth(12);
$spreadsheet->getColumnDimension('B')->setWidth(25);
$spreadsheet->getColumnDimension('C')->setWidth(12);
$spreadsheet->getColumnDimension('D')->setWidth(12);
$spreadsheet->getColumnDimension('E')->setWidth(14);
$spreadsheet->getColumnDimension('F')->setWidth(8);
$Letras = range("G", "V");
foreach ($Letras as $col) {
    $spreadsheet->getColumnDimension($col)->setWidth(10);
}
$spreadsheet->getStyle('A')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('B')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
$spreadsheet->getStyle('C1:V1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$spreadsheet->getStyle('C:V')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
    $timestamp = new \DateTime($Fecha);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Fecha = ($excelTimestamp);
    return $Fecha;
}
function HoraVal($hora)
{
    $hora = str_replace(':', '', $hora);
    $hora = str_replace(' ', '', $hora);
    return intval($hora);
}

$params = $_REQUEST;
$data = array();
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);
$params['start'] = $params['start'] ?? '0';
$params['length'] = $params['length'] ?? '99999';
$_POST['_dr'] = $_POST['_dr'] ?? '';
(!$_POST['_dr']) ? exit : '';

// print_r($_SESSION['EmprRol']).exit;

if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));
} else {
    $FechaIni = date('Ymd');
    $FechaFin = date('Ymd');
}
$params['Per'] = $params['Per'] ?? '';
$params['Per2'] = $params['Per2'] ?? '';
$params['Emp'] = $params['Emp'] ?? '';
$params['Plan'] = $params['Plan'] ?? '';
$params['Sect'] = $params['Sect'] ?? '';
$params['Sec2'] = $params['Sec2'] ?? '';
$params['Grup'] = $params['Grup'] ?? '';
$params['Sucur'] = $params['Sucur'] ?? '';
$params['_l'] = $params['_l'] ?? $data = array();
$params['draw'] = $params['draw'] ?? '';
$params['FicFalta'] = $params['FicFalta'] ?? '';
$params['Tipo'] = ($params['Tipo']) ?? '';
$params['onlyReg'] = ($params['onlyReg']) ?? '';

$Empr = $params['Emp'] ? ($params['Emp']) : explode(',', $_SESSION['EmprRol']);
$Per = $params['Per'] ? ($params['Per']) : array();
$Per2 = $params['Per2'] ? array($params['Per2']) : explode(',', $_SESSION['EstrUser']);
$Plan = $params['Plan'] ? $params['Plan'] : explode(',', $_SESSION['PlanRol']);
$Sect = $params['Sect'] ? $params['Sect'] : explode(',', $_SESSION['SectRol']);
$Grup = $params['Grup'] ? $params['Grup'] : explode(',', $_SESSION['GrupRol']);
$Sucu = $params['Sucur'] ? $params['Sucur'] : explode(',', $_SESSION['SucuRol']);
$Sec2 = $params['Sec2'] ? $params['Sec2'] : explode(',', $_SESSION['Sec2Rol']);
$FicFalta = $params['FicFalta'] ? array(intval($params['FicFalta'])) : [];
$LegTipo = $params['Tipo'] ? $params['Tipo'] : array();

$Legajos = ($Per2) ? ($Per2) : $Per;
$Legajos = ($Per) ? ($Per) : $Legajos;

$dataParametros = array(
    'Lega' => $Legajos,
    'Falta' => $FicFalta,
    'Empr' => ($Empr),
    'Plan' => ($Plan),
    'Sect' => ($Sect),
    'Grup' => ($Grup),
    'Sucu' => ($Sucu),
    'Sec2' => ($Sec2),
    'LegTipo' => ($LegTipo),
    'FechIni' => FechaFormatVar($FechaIni, 'Y-m-d'),
    'FechFin' => FechaFormatVar($FechaFin, 'Y-m-d'),
    'start' => intval($params['start']),
    'length' => intval($params['length']),
    'getReg' => 1,
    'onlyReg' => $params['onlyReg']
);

$url = gethostCHWeb() . "/" . HOMEHOST . "/api/ficnovhor/";

$dataApi['DATA'] = $dataApi['DATA'] ?? '';
$dataApi['MESSAGE'] = $dataApi['MESSAGE'] ?? '';

$dataApi = json_decode(requestApi($url, $token, $authBasic, $dataParametros, 10), true);
$numeroDeFila = 2;

if ($dataApi['DATA']) {
    foreach ($dataApi['DATA'] as $row) {
        $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(20);
        $col = '';
        $pers_legajo = $row['Lega'];
        $pers_nombre = empty($row['ApNo']) ? 'Sin Nombre' : $row['ApNo'];
        $dia = DiaSemana3(FechaFormatVar($row['Fech'], 'Ymd'));
        $ficHorario = $row['Tur']['ent'] . ' a ' . $row['Tur']['sal'];
        $ficHorario = ($row['Labo'] == '0') ? 'Franco' : $ficHorario;
        $ficHorario = ($row['Feri'] == '1') ? 'Feriado' : $ficHorario;

        $spreadsheet->setCellValue("A" . $numeroDeFila, $row['Lega']);
        $spreadsheet->setCellValue("B" . $numeroDeFila, $pers_nombre);
        $spreadsheet->setCellValue("C" . $numeroDeFila, FormatoFechaToExcel($row['Fech']));
        $spreadsheet->setCellValue("D" . $numeroDeFila, $dia);
        $spreadsheet->setCellValue("E" . $numeroDeFila, $ficHorario);
        $spreadsheet->setCellValue("F" . $numeroDeFila, $row['FichC']);

        $col = 6;
        try {
            foreach ($row['Fich'] as $key => $fich) {
                $col++;
                $colString = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $spreadsheet->setCellValue($colString . $numeroDeFila, $fich['Hora']);
            }
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }
        $numeroDeFila++;
    }
}
$cols = range("A", "V");
foreach ($cols as $key => $value) {
    $spreadsheet->getStyle($value)->getAlignment()->setIndent(1);
}
$spreadsheet->getRowDimension('1')->setRowHeight(25);
$ColumnCount = 3;
$RowIndex = 2;
// $spreadsheet->freezePaneByColumnAndRow($ColumnCount, $RowIndex);
$cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ColumnCount) . $RowIndex;
$spreadsheet->freezePane($cell);
# Crear un "escritor"
$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('G:V')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$spreadsheet->getStyle('V')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

$spreadsheet->getStyle('A')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
$numeroDeFila = 2;
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
