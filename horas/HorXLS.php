<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
if (session_status() == PHP_SESSION_NONE) {
    require __DIR__ . '/../config/session_start.php';
}
require __DIR__ . '/../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis = date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_HORAS_'.$datehis.'.xls"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

// require __DIR__ . '/../config/conect_mssql.php';
require __DIR__ . '/../filtros/filtros.php';
require __DIR__ . '/valores.php';

ultimoacc();
secure_auth_ch_json();
$Modulo = '16';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

function FormatoHoraToExcel(string $Hora = '00:00:00')
{
    $timestamp = new \DateTime($Hora);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Hora = ($excelTimestamp - $excelDate) == 0 ? '' : $excelTimestamp - $excelDate;
    return $Hora;
}
function FormatoFechaToExcel(string $Fecha = '1970-01-01')
{
    $timestamp = new \DateTime($Fecha);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    // $Fecha = ($excelTimestamp);
    return $excelDate;
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
$spreadsheet->setTitle("HORAS");
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Fecha",
    "Horario",
    "Entrada",
    "Salida",
    "Primer Fichada",
    "Última Fichada",
    "Dia",
    "Hora",
    "Descripción",
    "Hechas",
    "Pagas",
    "Cod Motivo",
    "Motivo",
    "Observaciones",
    "Usuario",
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

$spreadsheet->getStyle('A1:Q1')->applyFromArray($styleArray);
// $spreadsheet->getStyle('E:F')->applyFromArray($styleArray2);
/** aplicar un autofiltro a un rango de celdas */
$spreadsheet->setAutoFilter('A1:Q1');
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
/** Encabezado y Pie de Pagina */
$dateini = FechaFormatVar($FechaIni, 'd/m/Y');
$datefin = FechaFormatVar($FechaFin, 'd/m/Y');
$spreadsheet->getHeaderFooter()->setOddHeader('&L&BREPORTE DE HORAS. DESDE ' . ($dateini) . ' A ' . $datefin);
$spreadsheet->getHeaderFooter()->setOddFooter('&L' . $spreadsheet->getTitle() . '&RPágina &P de &N');
/** Para mostrar / ocultar las líneas de cuadrícula al imprimir */
$spreadsheet->setShowGridlines(true);
/**  alineación centrada de texto */
$spreadsheet->getStyle('A:Q')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$spreadsheet->freezePane('A2');
$spreadsheet->getStyle('A1:Q1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
/** cálculo automático de ancho de columna */
$spreadsheet->getColumnDimension('A')->setWidth(12);
$spreadsheet->getColumnDimension('B')->setWidth(30);
$spreadsheet->getColumnDimension('C')->setWidth(15);
$spreadsheet->getColumnDimension('D')->setWidth(15);
$spreadsheet->getColumnDimension('E')->setWidth(11);
$spreadsheet->getColumnDimension('F')->setWidth(11);
$spreadsheet->getColumnDimension('G')->setWidth(11);
$spreadsheet->getColumnDimension('H')->setWidth(11);
$spreadsheet->getColumnDimension('I')->setWidth(14);
$spreadsheet->getColumnDimension('J')->setWidth(11);
$spreadsheet->getColumnDimension('K')->setWidth(30);
$spreadsheet->getColumnDimension('L')->setWidth(11);
$spreadsheet->getColumnDimension('M')->setWidth(11);
$spreadsheet->getColumnDimension('N')->setWidth(9);
$spreadsheet->getColumnDimension('O')->setWidth(25);
$spreadsheet->getColumnDimension('P')->setWidth(30);
$spreadsheet->getColumnDimension('Q')->setWidth(16);

/** La altura de una fila. Fila 1 de encabezados */
$spreadsheet->getRowDimension('1')->setRowHeight(40);
$spreadsheet->getStyle('A1:Q1')->getAlignment()->setWrapText(true);

/** establecer el nivel de zoom de la hoja */
$spreadsheet->getSheetView()->setZoomScale(100);
/** Color de pestaña de hoja */
$spreadsheet->getTabColor()->setRGB('FFFFFF');

// $spreadsheet->getStyle('A1:M1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF');
$Letras = ['A', 'B', 'C', 'D', 'I', 'K', 'O', 'P', 'Q'];
foreach ($Letras as $col) {
    // alinear a la izquierda
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
}

$Letras = ['E', 'F', 'G', 'H', 'J', 'L', 'M', 'N'];
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $spreadsheet->getStyle($col . '1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
}

$spreadsheet->getStyle('C')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);

$spreadsheet->getStyle('L')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);
$spreadsheet->getStyle('M')
    ->getNumberFormat()
    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_TIME3);

$Letras = ['A', 'J', 'N'];
foreach ($Letras as $col) {
    $spreadsheet->getStyle($col)
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
}
$spreadsheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

$numeroDeFila = 2;
$Calculos = (!$Calculos == 1) ? "AND TIPOHORA.THoColu > 0" : '';

$FicUsua = $_SESSION['DBDATA'] > 7000000000 ? 'Fichas1.FicUsua,' : '';

$query = "SELECT FICHAS1.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', FICHAS1.FicFech AS 'Fecha', CONVERT(varchar, FICHAS1.FicFech, 112) AS Fecha2,
dbo.fn_HorarioAsignado(FICHAS.FicHorE, FICHAS.FicHorS, FICHAS.FicDiaL, FICHAS.FicDiaF) AS 'Horario', 
FICHAS.FicHorE AS 'Entrada',
FICHAS.FicHorS AS 'Salida',
dbo.fn_DiaDeLaSemana(FICHAS1.FicFech) AS 'Dia',
FICHAS1.FicHora AS 'Hora', 
TIPOHORA.THoDesc AS 'HoraDesc', 
FICHAS1.FicHsAu AS 'FicHsAu', 
FICHAS1.FicHsAu2 AS 'FicHsAu2', 
FICHAS1.FicObse AS 'Observ', $FicUsua
TIPOHORACAUSA.THoCCodi AS 'Motivo', 
TIPOHORACAUSA.THoCDesc AS 'DescMotivo'
FROM FICHAS1
INNER JOIN FICHAS ON FICHAS1.FicLega=FICHAS.FicLega AND FICHAS1.FicFech=FICHAS.FicFech AND FICHAS1.FicTurn=FICHAS.FicTurn
INNER JOIN PERSONAL ON FICHAS1.FicLega=PERSONAL.LegNume
INNER JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi
LEFT JOIN TIPOHORACAUSA ON FICHAS1.FicHora=TIPOHORACAUSA.THoCHora AND FICHAS1.FicCaus=TIPOHORACAUSA.THoCCodi
WHERE FICHAS1.FicFech BETWEEN '$FechaIni' AND '$FechaFin'
$Calculos $FilterEstruct $FiltrosFichas
ORDER BY FICHAS1.FicLega, TIPOHORA.THoColu, FICHAS1.FicHora";

// query de la tabla registro para obtener la primer y ultima fichada de cada dia
$qReg = "WITH DatosOrdenados AS (
            SELECT 
                RegLega,
                RegFeAs,
                RegFeRe,
                RegHoRe,
                CONVERT(datetime, CONVERT(varchar, RegFeRe, 23) + ' ' + RegHoRe) AS FechaHoraReal,
                ROW_NUMBER() OVER (PARTITION BY RegLega, RegFeAs ORDER BY RegFeRe, RegHoRe) AS rn_primero,
                ROW_NUMBER() OVER (PARTITION BY RegLega, RegFeAs ORDER BY RegFeRe DESC, RegHoRe DESC) AS rn_ultimo,
                COUNT(*) OVER (PARTITION BY RegLega, RegFeAs) AS TotalRegistros
            FROM REGISTRO
            INNER JOIN FICHAS1 ON REGISTRO.RegLega = FICHAS1.FicLega AND REGISTRO.RegFeAs = FICHAS1.FicFech
            INNER JOIN PERSONAL ON FICHAS1.FicLega=PERSONAL.LegNume
            INNER JOIN TIPOHORA ON FICHAS1.FicHora=TIPOHORA.THoCodi
            WHERE RegLega > 0
                AND RegFeAs BETWEEN '$FechaIni' AND '$FechaFin' $Calculos $FilterEstruct $FiltrosFichas
        )
        SELECT 
            RegLega,
        CONVERT(varchar, RegFeAs, 112) AS Fecha,
            -- RegFeAs,
            MAX(CASE WHEN rn_primero = 1 THEN RegHoRe END) AS PrimerFichada,
            -- Si solo hay un registro, mostrar NULL (vacío)
            CASE 
                WHEN COUNT(*) = 1 THEN NULL
                ELSE MAX(CASE WHEN rn_ultimo = 1 THEN RegHoRe END)
            END AS UltimaFichada,
            -- Determinar tipo de turno
            CASE 
                WHEN COUNT(*) = 1 THEN 'Único registro'
                WHEN MAX(CASE WHEN rn_primero = 1 THEN RegFeRe END) < 
                    MAX(CASE WHEN rn_ultimo = 1 THEN RegFeRe END) 
                THEN 'Nocturno'
                ELSE 'Normal'
            END AS Turno
        FROM DatosOrdenados
        GROUP BY RegLega, RegFeAs
        ORDER BY RegLega, RegFeAs;";

try {
    $result = arrMSQuery($query) ?: [];

    if (empty($result)) {
        throw new \Exception("No se encontraron registros para el rango de fechas especificado.");
    }

    $fichadas = arrMSQuery($qReg) ?: [];

    $fichadas = array_reduce_safe($fichadas, function ($carry, $item) {
        $carry[(int) ($item['RegLega'] . $item['Fecha'])] = $item;
        return $carry;
    }, []);


    foreach ($result as $row) {
        # Obtener los datos de la base de datos
        $claveFechaLega = (int) ($row['Legajo'] . $row['Fecha2']);
        $PrimerFichada = $fichadas[$claveFechaLega]['PrimerFichada'] ?? '';
        $UltimaFichada = $fichadas[$claveFechaLega]['UltimaFichada'] ?? '';
        $Legajo = $row['Legajo'];
        $Nombre = $row['Nombre'];
        $Fecha = $row['Fecha']->format('Y-m-d');
        $Horario = $row['Horario'];
        $Entrada = $row['Entrada'];
        $Salida = $row['Salida'];
        $Dia = $row['Dia'];
        $Hora = $row['Hora'];
        $HoraDesc = $row['HoraDesc'];
        $FicHsAu = FormatoHoraToExcel($row['FicHsAu']);
        $FicHsAu2 = FormatoHoraToExcel($row['FicHsAu2']);
        $Observ = $row['Observ'];
        $Motivo = ($row['Motivo'] == '0') ? '' : $row['Motivo'];
        $DescMotivo = $row['DescMotivo'];
        $FicUsua = $row['FicUsua'] ?? '';

        $Fecha = FormatoFechaToExcel($Fecha);

        # Escribirlos en el documento
        $spreadsheet->setCellValue('A' . $numeroDeFila, $Legajo);
        $spreadsheet->setCellValue('B' . $numeroDeFila, $Nombre);
        $spreadsheet->setCellValue('C' . $numeroDeFila, $Fecha);
        $spreadsheet->setCellValue('D' . $numeroDeFila, $Horario);
        $spreadsheet->setCellValue('E' . $numeroDeFila, $Entrada);
        $spreadsheet->setCellValue('F' . $numeroDeFila, $Salida);
        $spreadsheet->setCellValue('G' . $numeroDeFila, $PrimerFichada);
        $spreadsheet->setCellValue('H' . $numeroDeFila, $UltimaFichada);
        $spreadsheet->setCellValue('I' . $numeroDeFila, $Dia);
        $spreadsheet->setCellValue('J' . $numeroDeFila, $Hora);
        $spreadsheet->setCellValue('K' . $numeroDeFila, $HoraDesc);
        $spreadsheet->setCellValue('L' . $numeroDeFila, $FicHsAu);
        $spreadsheet->setCellValue('M' . $numeroDeFila, $FicHsAu2);
        $spreadsheet->setCellValue('N' . $numeroDeFila, $Motivo);
        $spreadsheet->setCellValue('O' . $numeroDeFila, $DescMotivo);
        $spreadsheet->setCellValue('P' . $numeroDeFila, $Observ);
        $spreadsheet->setCellValue('Q' . $numeroDeFila, $FicUsua);

        $numeroDeFila++;
    }

    foreach (['L', 'M'] as $col) {
        $ref = "{$col}{$numeroDeFila}";
        $spreadsheet->setCellValue($ref, '=SUBTOTAL(9,' . $col . '2:' . $col . ($numeroDeFila - 1) . ')');
        $spreadsheet->getStyle($ref)->getNumberFormat()->setFormatCode("[h]:mm");
        $spreadsheet->getStyle($ref)->getFont()->setBold(true);
    }

    // añadir indentacion a todas las filas y celdas
    foreach ($spreadsheet->getRowIterator() as $row) {
        $rowIndex = $row->getRowIndex();
        $spreadsheet->getStyle("A{$rowIndex}:Q{$rowIndex}")->getAlignment()->setIndent(1);
        // añadir altura de 25 a todas las filas
        if ($rowIndex > 1) {
            $spreadsheet->getRowDimension($rowIndex)->setRowHeight(25);
        }
    }

    BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Horas_" . $MicroTime . ".xls";

    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    $writer->save(__DIR__ . '/archivos/' . $NombreArchivo);
    // $writer->save('php://output');

    $data = array('status' => 'ok', 'archivo' => 'archivos/' . $NombreArchivo);
    echo json_encode($data);
    exit;

} catch (\Exception $e) {
    $data = ['status' => 'error', 'message' => $e->getMessage()];
    echo json_encode($data);
    exit;
}