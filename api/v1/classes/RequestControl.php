<?php

namespace Classes;

use Flight;
use donatj\UserAgent\UserAgentParser;

class RequestControl
{
    public function check_get($queryParams = array(), $url)
    {
        $url_parts = parse_url($url);
        if (isset($url_parts['query'])) {
            parse_str($url_parts['query'], $params);
            $params = array_keys($params); // Obtenemos los nombres de los parámetros GET
            $invalid_params = array_diff($params, $queryParams); // Buscamos los parámetros no permitidos
            if (!empty($invalid_params)) {
                $respuesta = array(
                    // "permitidos" => $queryParams,
                    "no permitidos" => $invalid_params
                );
                http_response_code(400);
                (response($respuesta, 0, 'GET parameter error', 400, 0, 0, 0));
                exit;
            }
        }
    }
    /**
     * comprueba si el metodo recibido por un request es el correcto
     *
     * @param string $value valor del metodo permitido. Puede ser POST, GET, etc. 
     * @param $method metodo recibido.
     */
    public function check_method($value)
    {
        $method = Flight::request()->method;
        if ($method != $value) {
            http_response_code(400);
            (response(array(), 0, 'Invalid Request Method: ' . $method, 400, microtime(true), 0, 0));
            exit;
        }
    }
    /**
     * comprueba si el valor de un parámetro pasado coincide con un valor específico. Sino coincide genera respuesta con error
     *
     * @param $param parametro. 
     * @param $value valor a comparar.
     * @param string $nameParams nombre del parametro (opcional).
     */
    public function check_param($param, $value, $nameParams = '')
    {
        $param = $param ?? '';
        if ($param && ($param != $value)) {
            http_response_code(400);
            (response('', 0, "Error. Solo se permite el valor '$value' o vacio en el parámetro $nameParams", 400, microtime(true), 0, 0));
            exit;
        }
        return $param;
    }
    /**
     * Valida el formato de un request data json y devuelve un array de errores finalizando script
     */
    public function check_json()
    {
        $array = file_get_contents('php://input');
        $data = json_decode($array, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            $errores[] = json_last_error_msg();
            // la cadena no está en formato JSON válido
            http_response_code(400);
            (response($errores, 0, 'Formato JSON inválido', 400, microtime(true), 0, 0));
            exit;
        }
    }

    public function response($data = array(), $total = 0, $msg = 'OK', $code = 200, $time_start = 0, $count = 0, $idCompany = 0)
    {
        $code = intval($code);
        $start = ($code != 400) ? start() : 0;
        $length = ($code != 400) ? length() : 0;

        $time_end = microtime(true);
        $tiempoScript = number_format($time_end - $time_start, 4);

        $array = array(
            'RESPONSE_CODE' => http_response_code($code),
            'START' => intval($start),
            'LENGTH' => intval($length),
            'TOTAL' => intval($total),
            'COUNT' => intval($count),
            'MESSAGE' => $msg,
            'TIME' => floatval($tiempoScript),
            // 'REQUEST_URI'   => $_SERVER['REQUEST_URI'],
            'DATA' => $data,
        );

        Flight::json($array);

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

        $pathLog = __DIR__ . '/logs/'; // path Log Api
        $nameLog = date('Ymd') . '_request_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
        /** start text log*/
        $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"HOLA $agent\" ]\n----------";
        /** end text log*/
        writeLog($TextLog, $pathLog . $nameLog); // Log Api
        /** END LOG API CONFIG */
        exit;
    }
}
