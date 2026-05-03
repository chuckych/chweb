<?php
declare(strict_types=1);
namespace App\Http;

use InvalidArgumentException; // ← esto faltaba

final class UrlBuilder
{
    public function build(string $endpoint, array $queryParams = []): string
    {
        if ($endpoint === '') {
            throw new InvalidArgumentException('El endpoint no puede estar vacío.');
        }

        return $queryParams
            ? $endpoint . '?' . http_build_query($queryParams)
            : $endpoint;
    }
}