<?php
declare(strict_types=1);
namespace App\Http;

final class HttpRequest
{
    public string  $url;
    public string  $method;
    public array   $headers;
    public ?array  $payload;

    public function __construct(
        string  $url,
        string  $method  = 'GET',
        array   $headers = [],
        ?array  $payload = null
    ) {
        $this->url     = $url;
        $this->method  = \strtoupper($method);
        $this->headers = $headers;
        $this->payload = $payload;
    }
}