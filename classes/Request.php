<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');

class Request
{

    private $token;
    private $url_api;
    private $url_api_interna;
    private $log;

    public function __construct()
    {
        $config = new JConfig();
        $ch_token_api = $config->ch_token_api ?? ''; // ch_token_api
        $ch_url_api = $config->ch_url_api ?? ''; // ch_url_api
        $ch_url_api_interna = $config->ch_url_api_interna ?? ''; // ch_url_api_interna

        $this->token = $ch_token_api ?? '';
        $this->url_api = $ch_url_api ?? '';
        $this->url_api_interna = $ch_url_api_interna ?? '';
        $this->log = new Log();
    }
    /**
     * Funcion que llama a una api.
     * @param string $endpoint endpoint de la api
     * @param array $payload body data
     * @param string $method metodo del request, POST | GET | PUT | DELETE
     * @param array $queryParams parametros data para GET request 
     */
    public function chapi()
    {
        $this->log->tz(); // defino el timezone

        $argumento = func_get_args(); // Obtengo los argumentos de la funcion en un array   
        $endpoint = $argumento[0] ?? ''; // Obtengo el endpoint
        $payload = $argumento[1] ?? array(); // Obtengo el payload
        $method = $argumento[2] ?? 'GET'; // Obtengo el método
        $queryParams = $argumento[3] ?? array(); // Obtengo los parámetro de la query
        $method = strtoupper($method); // Convierto el método a mayúsculas

        try {

            if (!$endpoint) {
                throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
            }

            $endpoint = ($queryParams) ? $endpoint . "?" . http_build_query($queryParams) : $endpoint; // Si hay parametros de query, los agrego al endpoint

            $ch = curl_init(); // Inicializo curl

            curl_setopt($ch, CURLOPT_URL, $this->url_api . $endpoint); // Seteo la url
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Seteo el retorno de la respuesta
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Seteo el timeout de la conexión
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Seteo la verificación del host
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Seteo la verificación del peer
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
            }
            if ($method == 'GET') {
                curl_setopt($ch, CURLOPT_HTTPGET, true);
            }
            if ($method == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
            }

            $headers = array(
                "Accept: */*",
                'Content-Type: application/json',
                "Token: $this->token",
            );

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Seteo los headers

            $file_contents = curl_exec($ch); // Ejecuto curl

            $curl_errno = curl_errno($ch); // get error code
            $curl_error = curl_error($ch); // get error information

            if ($curl_errno > 0) { // si hay error
                $text = "cURL Error ($curl_errno): $curl_error"; // set error message
                throw new Exception($text);
            }
            if (!$file_contents) {
                throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Error al obtener datos');
            }
            curl_close($ch);
            $text = 'API CH: ' . date('Y-m-d H:i:s') . ' ' . json_encode($file_contents);
            $this->log->write($text, date('Ymd') . '_Request.log');
            return $file_contents;
        } catch (\Exception $e) {
            $this->log->write($e->getPrevious(), date('Ymd') . '_Request.log');
            curl_close($ch);
            return false;
        }
    }
    public function interno()
    {
        $this->log->tz(); // defino el timezone

        $argumento = func_get_args();
        $endpoint = $argumento[0] ?? '';
        $payload = $argumento[1] ?? array();
        $method = $argumento[2] ?? 'GET';
        $queryParams = $argumento[3] ?? array();
        $method = strtoupper($method); // Convierto el metodo a mayusculas

        try {

            if (!$endpoint) {
                throw new Exception('API Interna: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
            }

            $endpoint = ($queryParams) ? $endpoint . "?" . http_build_query($queryParams) : $endpoint;


            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $this->url_api_interna . $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id()); //Parece que con esto se lleva la sesión del usuario logueado
            if ($method == 'POST') {
                curl_setopt($ch, CURLOPT_POST, true);
                ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
            }
            if ($method == 'GET') {
                curl_setopt($ch, CURLOPT_HTTPGET, true);
            }

            $headers = array(
                "Accept: */*",
                'Content-Type: application/json'
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $file_contents = curl_exec($ch);
            $curl_errno = curl_errno($ch); // get error code
            $curl_error = curl_error($ch); // get error information
            if ($curl_errno > 0) { // si hay error
                $text = "cURL Error ($curl_errno): $curl_error"; // set error message
                throw new Exception($text);
            }
            if (!$file_contents) {
                throw new Exception('API Interna: ' . date('Y-m-d H:i:s') . ' Error al obtener datos');
            }
            curl_close($ch);
            $text = 'API Interna: ' . date('Y-m-d H:i:s') . ' ' . json_encode($file_contents);
            $this->log->write($text, date('Ymd') . '_Request.log');
            return $file_contents;
        } catch (\Exception $e) {
            $this->log->write($e->getMessage(), date('Ymd') . '_Request.log');
            curl_close($ch);
            return false;
        }
    }
}
