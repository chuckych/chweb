<?php
declare(strict_types=1);
namespace App\Http;

use RuntimeException; // ← esto faltaba

final class CurlHttpClient implements HttpClientInterface
{
    private int $connectTimeout;
    private int $timeout;

    public function __construct(int $connectTimeout = 2, int $timeout = 60)
    {
        $this->connectTimeout = $connectTimeout;
        $this->timeout = $timeout;
    }

    public function request(HttpRequest $request): string
    {
        $ch = \curl_init();

        try {
            $this->applyBaseOptions($ch, $request);
            $this->applyMethodOptions($ch, $request);
            \curl_setopt($ch, CURLOPT_HTTPHEADER, $request->headers);

            $response = \curl_exec($ch);
            $errorCode = \curl_errno($ch);
            $errorMsg = \curl_error($ch);
        }catch (\Throwable $e) {
            \error_log('CurlHttpClient::request — ' . $e->getMessage());
             throw new RuntimeException('Error al realizar la solicitud cURL: ' . $e->getMessage(), 0, $e);
        } finally {
            $this->closeHandle($ch);
        }

        if ($errorCode > 0) {
            throw new RuntimeException("cURL Error ({$errorCode}): {$errorMsg}");
        }

        if ($response === false || $response === '') {
            throw new RuntimeException('La respuesta de la API está vacía o es inválida.');
        }

        return $response;
    }

    // $ch es CurlHandle en PHP 8+ y resource en 7.4 — sin typehint para compatibilidad
    /** @param \CurlHandle|resource $ch */
    private function applyBaseOptions($ch, HttpRequest $request): void
    {
        \curl_setopt_array($ch, [
            CURLOPT_URL => $request->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_DNS_CACHE_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
        ]);
    }

    /** @param \CurlHandle|resource $ch */
    private function applyMethodOptions($ch, HttpRequest $request): void
    {
        $body = $request->payload ? \json_encode($request->payload) : null;

        switch ($request->method) {
            case 'POST':
                \curl_setopt($ch, CURLOPT_POST, true);
                if ($body !== null) {
                    \curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                }
                break;

            case 'PUT':
            case 'DELETE':
                \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->method);
                if ($body !== null) {
                    \curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                }
                break;

            case 'GET':
            default:
                \curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
        }
    }

    /** @param \CurlHandle|resource $ch */
    private function closeHandle($ch): void
    {
        if (PHP_VERSION_ID >= 80500) {
            unset($ch);
        } elseif (PHP_VERSION_ID < 80000) {
            // En PHP 7.x $ch es resource, \is_resource() es válido
            if (\is_resource($ch ?? null) && get_resource_type($ch) === 'curl') {
                \curl_close($ch ?? null); // @phpstan-ignore-line
            }
        } else {
            // PHP 8.0 - 8.4: CurlHandle, \curl_close() acepta el objeto directamente
            \curl_close($ch); // @phpstan-ignore-line
        }
    }
}