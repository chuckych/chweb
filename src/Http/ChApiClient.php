<?php
declare(strict_types=1);
namespace App\Http;

use Throwable;

final class ChApiClient
{
    private HttpClientInterface $httpClient;
    private ApiTokenGenerator   $tokenGenerator;
    private UrlBuilder          $urlBuilder;

    public function __construct(
        HttpClientInterface $httpClient,
        ApiTokenGenerator   $tokenGenerator,
        UrlBuilder          $urlBuilder
    ) {
        $this->httpClient     = $httpClient;
        $this->tokenGenerator = $tokenGenerator;
        $this->urlBuilder     = $urlBuilder;
    }

    public function call(
        string $endpoint,
        array  $payload     = [],
        string $method      = 'GET',
        array  $queryParams = [],
        string $recid       = ''
        ) {

        timeZone();
        timeZone_lang();

        try {
            $url   = $this->urlBuilder->build($endpoint, $queryParams);
            $token = $recid !== ''
                ? $this->tokenGenerator->fromRecid($recid)
                : $this->tokenGenerator->fromSession();
            $request = new HttpRequest(  // ← ahora con constructor explícito
                $url,
                $method,
                $this->buildHeaders($token),
                $payload ?: null
            );

            $this->closeSessionIfActive();

            return $this->httpClient->request($request);

        } catch (Throwable $e) {
            error_log('ChApiClient::call — ' . $e->getMessage());
            return false;
        }
    }

    private function buildHeaders(string $token): array
    {
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        return [
            'Accept: */*',
            'Content-Type: application/json',
            "Token: {$token}",
            "User-Agent: {$agent}",
        ];
    }

    private function closeSessionIfActive(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }
}