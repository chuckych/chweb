<?php
declare(strict_types=1);
namespace App\Http;

use RuntimeException; // ← esto faltaba

final class ApiTokenGenerator
{
    public function fromRecid(string $recid): string
    {
        return sha1($recid);
    }

    public function fromSession(): string
    {
        $recid = $_SESSION['RECID_CLIENTE'] ?? '';

        if ($recid === '') {
            throw new RuntimeException('RECID_CLIENTE no está disponible en la sesión.');
        }

        return sha1($recid);
    }
}