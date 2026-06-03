<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

return static function (array $rows, string $absoluteFile): string {
    if (!class_exists(Spreadsheet::class)) {
        require __DIR__ . '/../../vendor/autoload.php';
    }

    require_once __DIR__ . '/../fn_spreadsheet.php';

    $headers = ['RotCodi', 'RotDesc', 'RotItem', 'RotHora', 'RotDias', 'User'];

    $exampleRows = [
        ['RotCodi' => '101', 'RotDesc' => 'Rotacion Manana Tarde', 'RotItem' => '1', 'RotHora' => '1', 'RotDias' => '2', 'User' => ''],
        ['RotCodi' => '101', 'RotDesc' => 'Rotacion Manana Tarde', 'RotItem' => '2', 'RotHora' => '4', 'RotDias' => '1', 'User' => ''],
        ['RotCodi' => '202', 'RotDesc' => 'Rotacion Noche', 'RotItem' => '1', 'RotHora' => '5', 'RotDias' => '1', 'User' => ''],
    ];

    $documento = new Spreadsheet();
    $sheet = $documento->getActiveSheet();
    $sheet->setTitle('Rotaciones Ejemplo');

    foreach ($headers as $idx => $header) {
        $col = number_to_letter($idx);
        $sheet->setCellValueExplicit("{$col}1", $header, DataType::TYPE_STRING);
    }

    $rowNumber = 2;
    foreach ($exampleRows as $row) {
        foreach ($headers as $idx => $header) {
            $col = number_to_letter($idx);
            $sheet->setCellValueExplicit("{$col}{$rowNumber}", (string) ($row[$header] ?? ''), DataType::TYPE_STRING);
        }
        $rowNumber++;
    }

    $sheet->setAutoFilter('A1:' . number_to_letter(count($headers) - 1) . '1');

    $dir = dirname($absoluteFile);
    if (!is_dir($dir)) {
        throw new Exception('No existe el directorio destino para exportar.');
    }

    $writer = IOFactory::createWriter($documento, 'Xls');
    $writer->save($absoluteFile);

    return $absoluteFile;
};
