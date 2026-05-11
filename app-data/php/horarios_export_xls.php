<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

return static function (array $rows, string $absoluteFile): string {
    if (empty($rows)) {
        throw new Exception('No hay filas para exportar.');
    }

    if (!class_exists(Spreadsheet::class)) {
        require __DIR__ . '/../../vendor/autoload.php';
    }

    require_once __DIR__ . '/../fn_spreadsheet.php';

    $first = reset($rows);
    if (!is_array($first) || empty($first)) {
        throw new Exception('Formato de filas inválido para exportación.');
    }

    $headers = array_keys($first);

    $documento = new Spreadsheet();
    $sheet = $documento->getActiveSheet();
    $sheet->setTitle('Horarios');

    foreach ($headers as $colIndex => $header) {
        $col = number_to_letter($colIndex);
        $sheet->setCellValueExplicit("{$col}1", (string) $header, DataType::TYPE_STRING);
    }

    $rowNumber = 2;
    foreach ($rows as $row) {
        $safeRow = is_array($row) ? $row : [];
        foreach ($headers as $colIndex => $header) {
            $col = number_to_letter($colIndex);
            $value = array_key_exists($header, $safeRow) ? (string) ($safeRow[$header] ?? '') : '';
            $sheet->setCellValueExplicit("{$col}{$rowNumber}", $value, DataType::TYPE_STRING);
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
