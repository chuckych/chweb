<?php
declare(strict_types=1);
namespace App\Http;

interface HttpClientInterface
{
    /**
     * @throws \RuntimeException on cURL / network error
     */
    public function request(HttpRequest $request): string;
}