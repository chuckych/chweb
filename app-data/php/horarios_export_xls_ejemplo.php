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

    $headers = [
        'HorCodi', 'HorDesc', 'HorID', 'HorColor',
        'HorDomi', 'HorLune', 'HorMart', 'HorMier', 'HorJuev', 'HorVier', 'HorSaba', 'HorFeri',
        'HorDoDe', 'HorLuDe', 'HorMaDe', 'HorMiDe', 'HorJuDe', 'HorViDe', 'HorSaDe', 'HorFeDe',
        'HorDoHa', 'HorLuHa', 'HorMaHa', 'HorMiHa', 'HorJuHa', 'HorViHa', 'HorSaHa', 'HorFeHa',
        'HorDoRe', 'HorLuRe', 'HorMaRe', 'HorMiRe', 'HorJuRe', 'HorViRe', 'HorSaRe', 'HorFeRe',
        'HorDoLi', 'HorLuLi', 'HorMaLi', 'HorMiLi', 'HorJuLi', 'HorViLi', 'HorSaLi', 'HorFeLi',
        'HorDoHs', 'HorLuHs', 'HorMaHs', 'HorMiHs', 'HorJuHs', 'HorViHs', 'HorSaHs', 'HorFeHs',
    ];

    $example = [
        'HorCodi' => '1',
        'HorDesc' => '09.00 a 18.00 Lun a Vie',
        'HorID' => 'HOR',
        'HorColor' => '#393939',
        'HorDomi' => '0',
        'HorLune' => '1',
        'HorMart' => '1',
        'HorMier' => '1',
        'HorJuev' => '1',
        'HorVier' => '1',
        'HorSaba' => '1',
        'HorFeri' => '0',
        'HorDoDe' => '08:00', 'HorLuDe' => '08:00', 'HorMaDe' => '08:00', 'HorMiDe' => '08:00',
        'HorJuDe' => '08:00', 'HorViDe' => '08:00', 'HorSaDe' => '08:00', 'HorFeDe' => '08:00',
        'HorDoHa' => '17:00', 'HorLuHa' => '17:00', 'HorMaHa' => '17:00', 'HorMiHa' => '17:00',
        'HorJuHa' => '17:00', 'HorViHa' => '17:00', 'HorSaHa' => '17:00', 'HorFeHa' => '17:00',
        'HorDoRe' => '01:00', 'HorLuRe' => '01:00', 'HorMaRe' => '01:00', 'HorMiRe' => '01:00',
        'HorJuRe' => '01:00', 'HorViRe' => '01:00', 'HorSaRe' => '01:00', 'HorFeRe' => '01:00',
        'HorDoLi' => '80', 'HorLuLi' => '80', 'HorMaLi' => '80', 'HorMiLi' => '80',
        'HorJuLi' => '80', 'HorViLi' => '80', 'HorSaLi' => '80', 'HorFeLi' => '80',
        'HorDoHs' => '09:00', 'HorLuHs' => '09:00', 'HorMaHs' => '09:00', 'HorMiHs' => '09:00',
        'HorJuHs' => '09:00', 'HorViHs' => '09:00', 'HorSaHs' => '09:00', 'HorFeHs' => '09:00',
    ];

    $documento = new Spreadsheet();
    $sheet = $documento->getActiveSheet();
    $sheet->setTitle('Horarios Ejemplo');

    foreach ($headers as $idx => $header) {
        $col = number_to_letter($idx);
        $sheet->setCellValueExplicit("{$col}1", $header, DataType::TYPE_STRING);
        $sheet->setCellValueExplicit("{$col}2", (string) ($example[$header] ?? ''), DataType::TYPE_STRING);
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
