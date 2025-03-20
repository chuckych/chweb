<?php
// Script para generar y descargar un archivo Excel de ejemplo

// Incluir la biblioteca PhpSpreadsheet
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Plantilla Importación');

// Definir los encabezados
$headers = [
    'ID',
    'Nombre y Apellido',
    'Estado',
    'Visualizar zona',
    'Bloqueo Fecha inicio',
    'Bloqueo Fecha Fin'
];

// Establecer los encabezados en la primera fila
foreach ($headers as $index => $header) {
    $column = chr(65 + $index); // A, B, C, etc.
    $sheet->setCellValue($column . '1', $header);
}

// Añadir datos de ejemplo
$exampleData = [
    [1, 'Juan Pérez', 'activo', 'activo', '', ''],
    [2, 'Ana García', 'bloqueado', 'inactivo', '2025-01-01', '2025-12-31'],
    [3, 'Carlos Rodríguez', 'activo', 'inactivo', '', ''],
    [4, 'María Fernández', 'activo', 'activo', '2025-06-15', '2025-09-30'],
];

// Añadir datos de ejemplo al archivo
$row = 2;
foreach ($exampleData as $dataRow) {
    foreach ($dataRow as $index => $value) {
        $column = chr(65 + $index); // A, B, C, etc.
        $sheet->setCellValue($column . $row, $value);
    }
    $row++;
}

// Aplicar formato a los encabezados
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];

$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

// Aplicar formato a los datos
$dataStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
];

$sheet->getStyle('A2:F' . ($row - 1))->applyFromArray($dataStyle);

// Aplicar anchura automática a las columnas
foreach (range('A', 'F') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Añadir una hoja de instrucciones
$instructionsSheet = $spreadsheet->createSheet();
$instructionsSheet->setTitle('Instrucciones');

// Contenido para la hoja de instrucciones
$instructionsSheet->setCellValue('A1', 'Instrucciones para la importación de usuarios');
$instructionsSheet->setCellValue('A3', 'Columnas obligatorias:');
$instructionsSheet->setCellValue('A4', '• ID: Número entero único para cada usuario.');
$instructionsSheet->setCellValue('A5', '• Nombre y Apellido: Texto, máximo 50 caracteres.');
$instructionsSheet->setCellValue('A6', '• Estado: Solo puede ser "activo" o "bloqueado".');
$instructionsSheet->setCellValue('A7', '• Visualizar zona: Solo puede ser "activo" o "inactivo".');

$instructionsSheet->setCellValue('A9', 'Columnas opcionales:');
$instructionsSheet->setCellValue('A10', '• Bloqueo Fecha inicio: Fecha en formato YYYY-MM-DD.');
$instructionsSheet->setCellValue('A11', '• Bloqueo Fecha Fin: Fecha en formato YYYY-MM-DD.');

$instructionsSheet->setCellValue('A13', 'Notas importantes:');
$instructionsSheet->setCellValue('A14', '1. No modifique los nombres de las columnas en la primera fila.');
$instructionsSheet->setCellValue('A15', '2. No debe haber IDs duplicados.');
$instructionsSheet->setCellValue('A16', '3. Las fechas pueden ser ingresadas en formato Excel o como texto YYYY-MM-DD.');
$instructionsSheet->setCellValue('A17', '4. El sistema validará cada fila y mostrará errores específicos si los hubiera.');
$instructionsSheet->setCellValue('A18', '5. Si una fila contiene errores, no será importada pero el proceso continuará con las demás filas.');

// Dar formato a la hoja de instrucciones
$instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$instructionsSheet->getStyle('A3')->getFont()->setBold(true);
$instructionsSheet->getStyle('A9')->getFont()->setBold(true);
$instructionsSheet->getStyle('A13')->getFont()->setBold(true);

// Ajustar anchura de columnas para instrucciones
$instructionsSheet->getColumnDimension('A')->setWidth(50);
$instructionsSheet->getColumnDimension('B')->setWidth(30);

// Crear el escritor para guardar el archivo
$writer = new Xlsx($spreadsheet);

// Configurar encabezados para la descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="plantilla_importacion_usuarios.xlsx"');
header('Cache-Control: max-age=0');

// Guardar el archivo directamente en la salida
$writer->save('php://output');
exit;
