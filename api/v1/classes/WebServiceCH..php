<?php

namespace Classes;

class WebServiceCH
{
    private $apiData;

    public function __construct($apiData)
    {
        $this->apiData = $apiData . '/RRHHWebService';
    }
    public function ping()
    {

        $ch = curl_init(); // Inicializar el objeto curl
        curl_setopt($ch, CURLOPT_URL, $this->apiData . '/Ping?'); // Establecer la URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Establecer que retorne el contenido del servidor
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // The number of seconds to wait while trying to connect
        // Especificar cabeceras
        $headers = array(
            'Connection: keep-alive',
            'Accept: */*',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Especificar método
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        $response = curl_exec($ch); // extract information from response
        $curl_errno = curl_errno($ch); // get error code
        $curl_error = curl_error($ch); // get error information
        if ($curl_errno > 0) { // si hay error
            $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
            writelog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
        curl_close($ch); // close curl handle
        if ($http_code == '201') {
            // writelog('Ping correcto', __DIR__ . '/logs/' . date('Ymd') . '_ping.log'); // escribir en el log
            http_response_code(204);
            return true;
        } else {
            writelog('Ping incorrecto', __DIR__ . '/logs/' . date('Ymd') . '_ping.log'); // escribir en el log
            http_response_code(408);
            return false;
        }
    }

    public function estado($processID)
    {
        do {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiData . "/Estado?" . $processID); // Establecer la URL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            $headers = array(
                'Connection: keep-alive',
                'Accept: */*',
            );
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $respuesta = (curl_exec($ch));
            writelog("to: " . ($this->apiData . "Estado?" . $processID), __DIR__ . '/logs/' . date('Ymd') . '_EstadoWS.log'); // 
            writelog("do: " . ($respuesta), __DIR__ . '/logs/' . date('Ymd') . '_EstadoWS.log'); // 
            curl_close($ch);
        } while (($respuesta) === '{Pendiente}');
        writelog("end: " . ($respuesta), __DIR__ . '/logs/' . date('Ymd') . '_EstadoWS.log');
        return 'Proceso terminado';
    }

    public function interperson($proceso = 1)
    {
        if ($this->ping()) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiData . '/INTERPERSONAL');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta = curl_exec($ch);
            $curl_errno = curl_errno($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // $curl_getinfo = json_encode(curl_getinfo($ch));
            if ($curl_errno > 0) {
                $text = "Error al procesar INTERPERSONAL"; // set error message
                writelog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
                return "Error";
            }
            if ($httpCode == 404) {
                writelog("Error al procesar INTERPERSONAL", __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
                return curl_exec($ch);
            }

            $processID = str_replace(array('{', '}'), '', $respuesta);
            curl_close($ch);
            writelog("processID: $processID", __DIR__ . '/logs/' . date('Ymd') . '_EstadoWS.log');
            if ($httpCode == 201) {
                $respuesta = '';
                if ($proceso == '1') {
                    return $this->estado($processID);
                }
            }
        }
    }

    public function request()
    {
        $arg = func_get_args();
        if (!$arg)
            return false;

        $endpoint = $arg[0] ?? ''; // url
        $method = $arg[1] ?? ''; // metodo
        $data = $arg[2] ?? array(); // datos
        $query = $arg[3] ?? array(); // query
        $estado = $arg[4] ?? true; // estado
        $ping = $arg[5] ?? true; // ping

        // print_r($estado).exit;

        if (!$endpoint)
            return false;
        if ($ping) {
            $p = ($this->ping()) ? true : false;
        } else {
            $p = true;
        }
        if (!$p)
            return false;

        $ch = curl_init();

        switch (strtolower($method)) {
            case 'get':
                $end = ($query) ? $endpoint . "?" . http_build_query($query) : $endpoint;
                $method = curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case 'post':
                $method = curl_setopt($ch, CURLOPT_POST, true);
                $method .= ($data) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)) : '';
                $end = $endpoint;
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $this->apiData . $end);
        $method;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_getinfo = json_encode(curl_getinfo($ch));
        if ($curl_errno > 0) {
            $text = "Error al procesar $endpoint . $curl_error"; // set error message
            writelog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return "Error";
        }
        if ($httpCode == 404) {
            writelog("Error al procesar $endpoint", __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return curl_exec($ch);
        }

        $processID = str_replace(array('{', '}'), '', $respuesta);
        curl_close($ch);
        // writelog("processID: $processID", __DIR__ . '/logs/' . date('Ymd') . '_request.log');
        if ($httpCode == 201) {
            $respuesta = '';
            if ($estado) {
                return $this->estado($processID);
            }
        }
    }
}
