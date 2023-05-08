<?php
require '../../vendor/autoload.php';
class requestControl
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

        /** LOG API CONFIG */
        // $textParams = array();
        // foreach ($_REQUEST as $key => $value) {
        //     $arrRequest[] = "$key=$value";
        //     array_push($textParams, $arrRequest);
        // }

        $textParams = urldecode($_SERVER['REQUEST_URI']); // convert to string

        $ipAdress = $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $agent = $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        // $idCompany    = $idCompany;

        if ($agent) {
            require __DIR__ . '../../control/PhpUserAgent/src/UserAgentParser.php';
            $parsedagent[] = parse_user_agent($agent);
            foreach ($parsedagent as $key => $value) {
                $platform = $value['platform'];
                $browser = $value['browser'];
                $version = $value['version'];
            }
            $agent = $platform . ' ' . $browser . ' ' . $version;
        }

        $pathLog = __DIR__ . '/logs/'; // path Log Api
        $nameLog = date('Ymd') . '_request_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
        /** start text log*/
        $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
        /** end text log*/
        writeLog($TextLog, $pathLog . $nameLog); // Log Api
        /** END LOG API CONFIG */
        exit;
    }
}
class apiData
{
    private $dataC;

    public function __construct($dataC)
    {
        $this->dataC = $dataC;
    }

    public function get($key = '')
    {
        if ($key) {
            return $this->dataC[$key];
        }
        return $this->dataC;
    }
}

class webServiceCH
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

        $endpoint = $arg[0] ?? '';
        $method = $arg[1] ?? '';
        $data = $arg[2] ?? array();
        $query = $arg[3] ?? array();
        $estado = $arg[4] ?? true;
        $ping = $arg[5] ?? true;

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
        writelog($this->apiData . $end, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
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

class fnArray
{
    /**
     * Compara dos arreglos ($array1 y $array2) a través de una clave común ($key). Busca los elementos que aparecen en ambos arreglos y devuelve un arreglo que contenga tres arreglos distintos: uno con los elementos duplicados provenientes del $array1, otro con los elementos duplicados provenientes del $array2, y un tercer arreglo con los elementos no duplicados..
     *
     * @param array $array1 El primer arreglo a comparar.
     * @param array $array2 El segundo arreglo a comparar.
     * @param string $key La clave en la cual se basará la comparación.
     * @return array Un arreglo con tres arreglos distintos: duplicados1, duplicados2 y no_duplicados.
     */
    public function comparar($array1, $array2, $key)
    {
        $no_duplicados = array();
        $duplicados1 = array();
        $duplicados2 = array();
        // Iterar sobre el primer array y crear un nuevo array indexado por "$key"
        foreach ($array1 as $item) {
            $no_duplicados[$item[$key]] = $item;
        }
        // Iterar sobre el segundo array y verificar si la clave "$key" existe en el nuevo array
        // Si la clave ya existe, agregar el sub-array correspondiente a un nuevo array que contendrá los duplicados.
        foreach ($array2 as $item) {
            if (isset($no_duplicados[$item[$key]])) {
                $duplicados1[] = $no_duplicados[$item[$key]];
                $duplicados2[] = $item;
                unset($no_duplicados[$item[$key]]); // Eliminar el elemento duplicado del array de elementos no duplicados
            }
        }
        // Retornar el array de elementos duplicados y no duplicados
        return array(
            'duplicados1' => $duplicados1,
            // Datos duplicaodos con los valores recicibidos en el array1
            'duplicados2' => $duplicados2,
            // Datos duplicaodos con los valores recicibidos del array2
            'no_duplicados' => $no_duplicados, // Datos no duplicados
        );
    }

    /** 
     * Toma como parámetro un array multidimensional y elimina subarrays vacíos. Se recorre cada subarray dentro del array principal y se comprueba si está vacío o no. Si el subarray está vacío, se elimina ese subarray del array principal. la función retorna el array resultante con los subarrays vacíos eliminados.
     * @param array arrayMultidimensional
     */
    public static function removeEmptySubarrays($array)
    {
        if ($array) {
            foreach ($array as $key => $subarray) {
                if (empty($subarray)) {
                    unset($array[$key]);
                }
            }
            return array_values($array);
        }
        return '';
    }

}

class validaRequest
{
    public function interperson($array)
    {
        $tools = new tools();
        $errores = array();
        $optionsInt = array(
            'options' => array(
                'min_range' => 0,
                'max_range' => 2147483648
            )
        );

        if ($array) {
            foreach ($array as $key => $value) {
                // Validar longitud de cada valor
                foreach ($value as $k => $v) {
                    switch ($k) {
                        case 'LegNume':
                            // Validar LegNume como entero
                            if (empty($v)) {
                                $errores[] = "El valor de LegNume en el elemento $key es requerido.";
                                unset($array[$key]);
                            }
                            if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                $errores[] = "El valor de LegNume en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegApNo':
                            if (empty($v)) {
                                $errores[] = "El valor de LegApNo en el elemento $key es requerido.";
                                unset($array[$key]);
                            }

                            // Validar LegApNo como string
                            if (!is_string($v)) {
                                $errores[] = "El valor de LegApNo en el elemento $key no es un string.";
                                unset($array[$key]);
                            }
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de LegApNo en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTDoc':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegTDoc en el elemento $key solo admite los siguientes valores 0;1;2;3;4.";
                                unset($array[$key]);
                            }
                            if (($v) > 4) {
                                $errores[] = "El valor de LegTDoc en el elemento $key solo admite los siguientes valores 0;1;2;3;4.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDocu':
                            if ($v) {
                                if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                    $errores[] = "El valor de LegDocu en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                    unset($array[$key]);
                                }
                            }
                            break;
                        case 'LegCUIT':
                            if (strlen($v) > 13) {
                                $errores[] = "El valor de LegCUIT en el elemento $key supera los 13 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDomi':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de LegDomi en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDoNu':
                            if ($v) {
                                if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                    $errores[] = "El valor de LegDoNu en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                    unset($array[$key]);
                                }
                            }
                            break;
                        case 'LegDoPi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de LegDoPi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de LegDoPi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDoDP':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de LegDoDP en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDoOb':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de LegDoOb en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegCOPO':
                            if (strlen($v) > 8) {
                                $errores[] = "El valor de LegCOPO en el elemento $key supera los 8 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTel1':
                            if (strlen($v) > 15) {
                                $errores[] = "El valor de LegTel1 en el elemento $key supera los 15 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTel2':
                            if (strlen($v) > 15) {
                                $errores[] = "El valor de LegTel2 en el elemento $key supera los 15 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTel3':
                            if (strlen($v) > 15) {
                                $errores[] = "El valor de LegTel3 en el elemento $key supera los 15 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTeO1':
                            if (strlen($v) > 20) {
                                $errores[] = "El valor de LegTeO1 en el elemento $key supera los 20 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTeO2':
                            if (strlen($v) > 20) {
                                $errores[] = "El valor de LegTeO2 en el elemento $key supera los 20 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegMail':
                            if (strlen($v) > 250) {
                                $errores[] = "El valor de LegMail en el elemento $key supera los 250 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegEsCi':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegEsCi en el elemento $key solo admite los siguientes valores 0;1;2;3.";
                                unset($array[$key]);
                            }
                            if (($v) > 3) {
                                $errores[] = "El valor de LegEsCi en el elemento $key solo admite los siguientes valores 0;1;2;3.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegSexo':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegSexo en el elemento $key solo admite los siguientes valores 0;1.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegFeNa':
                            $fecha = $tools->validarFecha($v);
                            if ($fecha != false) {
                                $errores[] = "El valor de LegFeNa en el elemento $key es incorrecto. $fecha";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTipo':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegTipo en el elemento $key solo admite los siguientes valores 0;1.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegFeIn':
                            $fecha = $tools->validarFecha($v);
                            if ($fecha != false) {
                                $errores[] = "El valor de LegFeIn en el elemento $key es incorrecto. $fecha";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegFeEg':
                            $fecha = $tools->validarFecha($v);
                            if ($fecha != false) {
                                $errores[] = "El valor de LegFeEg en el elemento $key es incorrecto. $fecha";
                                unset($array[$key]);
                            }
                            break;
                        case 'NacCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de NacCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de NacCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'NacDesc':
                            if (strlen($v) > 30) {
                                $errores[] = "El valor de NacDesc en el elemento $key supera los 30 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ProCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de ProCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de ProCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ProDesc':
                            if (strlen($v) > 30) {
                                $errores[] = "El valor de ProDesc en el elemento $key supera los 30 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LocCodi':
                            $v = ($v) ? $v : 0;
                            if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                $errores[] = "El valor de LocCodi en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LocDesc':
                            if (strlen($v) > 50) {
                                $errores[] = "El valor de LocDesc en el elemento $key supera los 50 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'EmpCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de EmpCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de EmpCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'EmpRazon':
                            if (strlen($v) > 50) {
                                $errores[] = "El valor de EmpRazon en el elemento $key supera los 50 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'PlaCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de PlaCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de PlaCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'PlaDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de PlaDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SucCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de SucCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de SucCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SucDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de SucDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SecCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de SecCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de SecCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SecDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de SecDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'Se2Codi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de Se2Codi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de Se2Codi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'Se2Desc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de Se2Desc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'GruCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de GruCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de GruCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'GruDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de GruDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ConCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de ConCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de ConCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ConDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de ConDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                    }
                }
            }
            $a = array(
                "errores" => $errores,
                "payload" => array_values($array),
            );
            return $a;
        }
        return array();
    }
}

class querydb
{
    private $a;

    public function __construct($a)
    {
        $this->a = $a;
    }
    public function query($query, $count = 0)
    {

        $dataCompany = array(
            'host' => $this->a['DBHost'],
            'user' => $this->a['DBUser'],
            'pass' => $this->a['DBPass'],
            'db' => $this->a['DBName'],
            'auth' => $this->a['DBAuth'],
            'idCompany' => $this->a['idCompany'],
            'nameCompany' => $this->a['nameCompany'],
            'hostCHWeb' => $this->a['hostCHWeb'],
        );

        if (!$query) {
            http_response_code(400);
            (response(array(), 0, 'empty query', 400, microtime(true), 0, $dataCompany['idCompany']));
            exit;
        }

        require __DIR__ . './connectDBPDO.php';
        try {
            $resultSet = array();
            $stmt = $conn->query($query);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resultSet[] = $r;
            }
            $stmt = null;
            $conn = null;
            return $resultSet;
        } catch (Exception $e) {
            $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorMSQuery.log'; // ruta del archivo de Log de errores
            writeLog(PHP_EOL . 'Message: ' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . PHP_EOL . 'Source: ' . '"' . $_SERVER['REQUEST_URI'] . '"', $pathLog); // escribir en el log de errores el error
            writeLog(PHP_EOL . 'Query: ' . $query, $pathLog); // escribir en el log de errores el error
            http_response_code(400);
            (response(array(), 0, $e->getMessage(), 400, microtime(true), 0, ''));
            exit;
        }
    }

    public function save($query, $count = 0)
    {

        $dataCompany = array(
            'host' => $this->a['DBHost'],
            'user' => $this->a['DBUser'],
            'pass' => $this->a['DBPass'],
            'db' => $this->a['DBName'],
            'auth' => $this->a['DBAuth'],
            'idCompany' => $this->a['idCompany'],
            'nameCompany' => $this->a['nameCompany'],
            'hostCHWeb' => $this->a['hostCHWeb'],
        );

        if (!$query) {
            http_response_code(400);
            (response(array(), 0, 'empty query', 400, timeStart(), 0, $dataCompany['idCompany']));
            exit;
        }
        require __DIR__ . './connectDBPDO.php';
        try {
            $resultSet = array();
            $stmt = $conn->query($query);
            if ($stmt) {
                $stmt = null;
                $conn = null;
                return true;
            } else {
                $stmt = null;
                $conn = null;
                return false;
            }
        } catch (Exception $e) {
            $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorMSQuery.log'; // ruta del archivo de Log de errores
            writeLog(PHP_EOL . 'Message: ' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . PHP_EOL . 'Source: ' . '"' . $_SERVER['REQUEST_URI'] . '"', $pathLog); // escribir en el log de errores el error
            writeLog(PHP_EOL . 'Query: ' . $query, $pathLog); // escribir en el log de errores el error
            http_response_code(400);
            (response(array(), 0, $e->getMessage(), 400, timeStart(), 0, ''));
            exit;
        }
    }

}

class tools
{
    public function validarFecha($fecha)
    {
        if ($fecha) {
            $f = explode('-', $fecha);

            if (count($f) != 3) {
                return 'Formato de fecha incorrecto';
            }

            $err = '';
            $y = $f[0];
            $m = ($f[1] > 12 || $f[1] == 0) ? $err .= "Mes ($f[1]) Incorrecto. " : $f[1];
            $d = ($f[2] > 31) ? $err .= "Dia ($f[2]) Incorrecto. " : $f[2];

            if ($err) {
                $err = trim($err);
                return $err;
            }
            $f = "$y-$m-$d";
            return false;
        }
    }
}