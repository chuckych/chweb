<?php

namespace Classes;

use donatj\UserAgent\UserAgentParser;
use Classes\Log;
use Classes\Tools;
// use Classes\DataCompany;
use Flight;
use flight\net\Request;

class Response
{
    private function normalizeHttpCode(int $code): int
    {
        return ($code >= 100 && $code <= 599) ? $code : 500;
    }
    
    /**
     * Devuelve una respuesta JSON con el código de respuesta especificado.
     *
     * @param int $code El código de respuesta HTTP.
     * @param mixed $data Los datos a incluir en la respuesta.
     */
    public function json($data, $code = 200)
    {
        $code = $this->normalizeHttpCode((int) $code);
        // Convertimos los datos a formato JSON, escapando correctamente los caracteres UTF-8.
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json;
    }
    /**
     * Devuelve una respuesta JSON con el código de respuesta especificado.
     *
     * @param mixed $data Los datos a incluir en la respuesta.
     * @param int $total El total de registros.
     * @param string $msg El mensaje de respuesta.
     * @param int $code El código de respuesta HTTP.
     * @param int $time_start El tiempo de inicio de la respuesta.
     * @param int $count El total de registros de la respuesta.
     * @param int $idCompany El id de la empresa.
     */
    public function respuesta(array $data = [], int $total = 0, string $msg = 'OK', int $code = 200, float $time_start = 0, int $count = 0, int $idCompany = 0)
    {
        $log = new Log; // Log Api
        $nameLog = date('Ymd') . '_request_' . $idCompany . '.log'; // path Log Api
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $file = $trace[0]['file'] ?? 'unknown file';
        $line = $trace[0]['line'] ?? 'unknown line';

        try {
            $code = $this->normalizeHttpCode((int) $code); // code response
            if (!headers_sent()) {
                \http_response_code($code); // Set response code
            }
            $start = ($code != 400) ? $this->start() : 0; // start response
            $length = ($code != 400) ? $this->length() : 0; // length response

            $time_end = microtime(true);
            $tiempoScript = number_format($time_end - $time_start, 4);

            $array = [
                'RESPONSE_CODE' => $code . ' ' . $this->getStatusMessage($code),
                'HTTP_STATUS' => $code,
                'START' => intval($start),
                'LENGTH' => intval($length),
                'TOTAL' => intval($total),
                'COUNT' => intval($count),
                'MESSAGE' => $msg,
                'TIME' => floatval($tiempoScript),
                'DATA' => $data ?? [],
            ];
            // $this->json($array, $code); // response json
            Flight::json($array, $code); // response json Flight

            $textParams = urldecode($_SERVER['REQUEST_URI']); // convert to string

            $ipAdress = $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '';
            $agent = $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

            if ($agent) {
                $parser = new UserAgentParser();
                $ua = $parser->parse();
                $ua = $parser();
                $ua->platform();
                $ua->browser();
                $ua->browserVersion();
                $agent = $ua->platform() . ' ' . $ua->browser() . ' ' . $ua->browserVersion();
            }
            // si ID_COMPANY no esta definido
            $idCompany = (defined('ID_COMPANY')) ? ID_COMPANY : 0;
            
            $fn = $trace[1]['function'] ?? 'unknown function';
            $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ] TRACE: File: {$file} Line: {$line} fn -> {$fn}\n";
            $log->write($TextLog, $nameLog); // Log Api
            exit;
        } catch (\Throwable $th) {
            $log->trace('Response::' . __FUNCTION__ . ': ', $nameLog, $th);
            // $this->json(['error' => 'Error al procesar la respuesta'], 500);
            Flight::json(['error' => 'Error al procesar la respuesta'], $code);
            exit;
        }
        /** END LOG API CONFIG */
    }
    public function start()
    {
        $request = Flight::request();

        $p = (strtolower($request->method) == 'post') ? $request->data : $request->query;
        $p->start = $p->start ?? '0';
        // $start = empty(vp($p->start, 'Start', 'int', 11)) ? 0 : $p->start;
        $start = empty($p->start) ? 0 : $p->start;
        return intval($start);
    }
    public function length()
    {
        $request = Flight::request();
        $p = (strtolower($request->method) == 'post') ? $request->data : $request->query;
        $p->length = $p->length ?? '';
        // $length = empty(vp($p->length, 'Length', 'int', 11)) ? 10 : $p->length;
        $length = empty($p->length) ? 10 : $p->length;
        return intval($length);
    }
    public function getStatusMessage(int $code): string
    {
        $statusCodes = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        );

        return isset($statusCodes[$code]) ? $statusCodes[$code] : 'Unknown';
    }
    function notFound()
    {
        $text = 'Not Found /v1' . Flight::request()->url . '. Method: ' . Flight::request()->method;
        $this->respuesta([], 0, $text, 404, 0, 0, 0);
    }
}
