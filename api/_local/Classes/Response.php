<?php

namespace Classes;

use donatj\UserAgent\UserAgentParser;
// use Classes\Log;
// use Classes\Tools;
// use Classes\DataCompany;
use Flight;

class Response
{
    private $request;
    /**
     * Devuelve una respuesta JSON con el código de respuesta especificado.
     *
     * @param int $code El código de respuesta HTTP.
     * @param mixed $data Los datos a incluir en la respuesta.
     */

    function __construct()
    {
        $this->request = Flight::request();
    }
    function json($data, $code = 200)
    {
        // Establecemos el código de respuesta HTTP.
        http_response_code($code);

        // Establecemos las cabeceras para indicar que la respuesta es JSON.
        header('Content-Type: application/json; charset=utf-8');

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
    function respuesta($data = [], $total = 0, $msg = 'OK', $code = 200, $time_start = 0, $count = 0, $idCompany = 0)
    {
        // $log = new Log; // Log Api
        $code   = intval($code); // code response
        $start  = ($code != 400) ? $this->start() : 0; // start response
        $length = ($code != 400) ? $this->length() : 0; // length response

        $time_end     = microtime(true);
        $tiempoScript = number_format($time_end - $time_start, 4);

        $array = [
            'RESPONSE_CODE' => $code . ' ' . $this->getStatusMessage($code),
            'START'         => intval($start),
            'LENGTH'        => intval($length),
            'TOTAL'         => intval($total),
            'COUNT'         => intval($count),
            'MESSAGE'       => $msg,
            'TIME'          => floatval($tiempoScript),
            'DATA'          => $data ?? [],
        ];
        $this->json($array, $code); // response json

        $textParams = urldecode($_SERVER['REQUEST_URI']); // convert to string

        $ipAdress = $this->request->ip ?? '';
        $agent    = $this->request->user_agent ?? '';

        // if ($agent) {
        //     $parser = new UserAgentParser();
        //     $ua = $parser->parse();
        //     $ua = $parser();
        //     $ua->platform();
        //     $ua->browser();
        //     $ua->browserVersion();
        //     $agent = $ua->platform() . ' ' . $ua->browser() . ' ' . $ua->browserVersion();
        // }
        // si ID_COMPANY no esta definido
        $idCompany = (defined('ID_COMPANY')) ? ID_COMPANY : 0;
        $nameLog = date('Ymd') . '_request_' . $idCompany . '.log'; // path Log Api
        /** start text log*/
        $EOL = PHP_EOL;

        $textResponse = "{$EOL} REQUEST  = [ $textParams ]{$EOL} RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]{$EOL}----------";

        // $textResponse = sprintf("%s REQUEST  = [ %s ]%s RESPONSE = [ RESPONSE_CODE=\"%s\" START=\"%s\" LENGTH=\"%s\" TOTAL=\"%s\" COUNT=\"%s\" MESSAGE=\"%s\" TIME=\"%s\" IP=\"%s\" AGENT=\"%s\" ]%s----------", $EOL, $textParams, $EOL, $array['RESPONSE_CODE'], $array['START'], $array['LENGTH'], $array['TOTAL'], $array['COUNT'], $array['MESSAGE'], $array['TIME'], $ipAdress, $agent, $EOL);

        // $log->write(print_r($textResponse, true), $nameLog); // Log Api
        /** END LOG API CONFIG */
    }
    function start()
    {
        $p = (strtolower($this->request->method) == 'post') ? $this->request->data : $this->request->query;
        $start = $p->start ?? '0';
        $start = empty($start) ? 0 : $start;
        return intval($start);
    }
    function length()
    {
        $p = (strtolower($this->request->method) == 'post') ? $this->request->data : $this->request->query;
        $length = $p->length ?? '';
        $length = empty($length) ? 10 : $length;
        return intval($length);
    }
    function getStatusMessage($code)
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
        $text = 'Not Found /v1' . $this->request->url . '. Method: ' . $this->request->method;
        $this->respuesta([], 0, $text, 404, 0, 0, 0);
    }
}
