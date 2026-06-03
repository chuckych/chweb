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
    $flatRows = [];

    foreach ($rows as $rotacion) {
        if (!is_array($rotacion)) {
            continue;
        }

        $rotCodi = (string) ($rotacion['RotCodi'] ?? '');
        $rotDesc = (string) ($rotacion['RotDesc'] ?? '');
        $user = (string) ($rotacion['User'] ?? '');
        $horarios = is_array($rotacion['Horarios'] ?? null) ? $rotacion['Horarios'] : [];

        if (empty($horarios)) {
            $flatRows[] = [
                'RotCodi' => $rotCodi,
                'RotDesc' => $rotDesc,
                'RotItem' => '',
                'RotHora' => '',
                'RotDias' => '',
                'User' => $user,
            ];
            continue;
        }

        foreach ($horarios as $item) {
            if (!is_array($item)) {
                continue;
            }
            $flatRows[] = [
                'RotCodi' => $rotCodi,
                'RotDesc' => $rotDesc,
                'RotItem' => (string) ($item['RotItem'] ?? ''),
                'RotHora' => (string) ($item['RotHora'] ?? ''),
                'RotDias' => (string) ($item['RotDias'] ?? ''),
                'User' => $user,
            ];
        }
    }

    if (empty($flatRows)) {
        throw new Exception('No hay filas para exportar.');
    }

    $documento = new Spreadsheet();
    $sheet = $documento->getActiveSheet();
    $sheet->setTitle('Rotaciones');

    foreach ($headers as $idx => $header) {
        $col = number_to_letter($idx);
        $sheet->setCellValueExplicit("{$col}1", $header, DataType::TYPE_STRING);
    }

    $rowNumber = 2;
    foreach ($flatRows as $row) {
        foreach ($headers as $idx => $header) {
            $col = number_to_letter($idx);
            $value = (string) ($row[$header] ?? '');
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
    if (method_exists($writer, 'setPreCalculateFormulas')) {
        $writer->setPreCalculateFormulas(false);
    }
    $writer->save($absoluteFile);

    return $absoluteFile;
};
