<?php

namespace Classes;

use Classes\DataCompany;
use Classes\Log;
use Classes\Response;
use Classes\Tools;
use Flight;

class RRHHWebService
{
    private $url;
    private $Comando;
    private $Usuario;
    private $Legajos = [];
    private $TipoDePersonal;
    private $LegajoDesde;
    private $LegajoHasta;
    private $FechaDesde;
    private $FechaHasta;
    private $Empresa;
    private $Planta;
    private $Sucursal;
    private $Grupo;
    private $Sector;
    private $Seccion;
    // private $dataCompany;
    private $log;
    private $resp;
    private $tools;

    public function __construct()
    {
        // $this->dataCompany = new DataCompany; // Instancia de la clase dataCompany
        // $url = $this->url = $this->dataCompany->get('WebServiceCH');
        $url = $this->url = $_SESSION['DataCompany']['WebServiceCH'];
        if ($url) {
            $this->url = $url . '/RRHHWebService'; // Assign the value to the property
        }
        $this->log = new Log; // Instancia de la clase Log
        $this->resp = new Response;
        $this->tools = new Tools;
    }

    public function baseUrl()
    {
        if (!$this->url) { // Si no hay url del WebService
            $this->log->write('No se establecio el baseUrl del webservice', date('Ymd') . '_RRHHWebService_' . ID_COMPANY . '.log');
            return; // Salir
        }
        return $this->url;
    }

    // Envio: 
    // POST http://xxx.xxx.xxx.xxx:6400/RRHHWebService/Procesar HTTP/1.1 
    // Content-Type: text/html 
    // {Usuario=usuario, Legajos=[], TipoDePersonal=0, LegajoDesde=1, LegajoHasta=99999999, FechaDesde=01/01/2020, FechaHasta=01/01/2020, Empresa=0, Planta=0, Sucursal=0, Grupo=0, Sector=0, Seccion=0} 

    // Respuesta: 
    // HTTPCode: 201 OK 
    // {ProcesoId=Identificador de Proceso} 

    // HTTPCode: 404 Not Found 
    // Descripción del Error 

    // Parámetros Obligatorios: 
    // Usuario=usuario Legajos=[] o TipoDePersonal=0,LegajoDesde=1, LegajoHasta=99999999 
    // FechaDesde=01/01/2020 
    // FechaHasta=01/01/2020 
    // Parámetros Opcionales: Empresa=0 Planta=0 Sucursal=0 Grupo=0 Sector=0 Seccion=0 

    /** 
     * Procesa los legajos apartir de un arreglo de legajos
     * @param array $Legajos Arreglo de legajos
     * @param string $FechaDesde Fecha desde
     * @param string $FechaHasta Fecha hasta
     * @return string Respuesta del WebService
     * @example $Legajos = [1,2,3,4,5,6,7,8,9,10];
     * @example $FechaDesde = '2023-08-23';
     * @example $FechaHasta = '2023-08-26';
     */
    function procesar_legajos($Legajos = [], $FechaDesde, $FechaHasta)
    {
        try {

            if (!$this->ping()) {
                throw new \Exception('Error al conectar con el WebService', 1);
            }

            if (!is_array($Legajos) || empty($FechaDesde) || empty($FechaHasta)) { // Valida los parametros
                throw new \Exception('Parametros no validos', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaDesde)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaHasta)) { // Valida la fecha hasta
                throw new \Exception('Fecha hasta no es valida', 1);
            }
            $ruta = $this->baseUrl() . '/' . "Procesar"; // Ruta del WebService

            $dateSegments = $this->tools->dividefecha31dias($FechaDesde, $FechaHasta); // Divide las fechas en segmentos de 31 dias

            $Legajos = (is_array($Legajos)) ? implode(';', $Legajos) : '';

            if ($dateSegments) { // Si hay segmentos de fechas procesa los legajos en cada segmento
                $ch = curl_init();

                foreach ($dateSegments as $segment) { // Recorre los segmentos


                    $FechaDesde = date('d/m/Y', strtotime($segment['FechaMin']));
                    $FechaHasta = date('d/m/Y', strtotime($segment['FechaMax']));

                    if (strtotime($segment['FechaMin']) > time()) { // Si la fecha minima es mayor a la fecha actual
                        break; // Salir del bucle
                    }
                    if (strtotime($segment['FechaMax']) > time()) { // Si la fecha maxima es mayor a la fecha actual
                        $FechaHasta = date('d/m/Y'); // La fecha maxima es la fecha actual
                    }

                    $post_data = "{Usuario=Supervisor, Legajos=[$Legajos],FechaDesde='$FechaDesde',FechaHasta='$FechaHasta'}"; // Parametros del WebService

                    curl_setopt($ch, CURLOPT_URL, $ruta);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $respuesta = curl_exec($ch);
                    $curl_errno = curl_errno($ch);
                    // $curl_error = curl_error($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($curl_errno > 0) {
                        $text = $curl_errno . ": Error al procesar."; // set error 
                        throw new \Exception($text, $httpCode);
                    }
                    if ($httpCode == 404) {
                        $text = $respuesta . ": Error al procesar."; // set error 
                        throw new \Exception($text, $httpCode);
                    }
                    $dias = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']);
                    $text = "Legajos procesados [$Legajos] - $FechaDesde a $FechaHasta $dias dias";
                    $this->log->write($text, date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    // $this->resp->response('', 0, $respuesta, $httpCode, microtime(true), 0, 0);
                }
                curl_close($ch);
            }
        } catch (\Exception $e) {
            $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
            //$this->resp->response('', 0, $e->getMessage(), $e->getCode(), microtime(true), 0, 0);
            // exit;
        }
    }
    /** 
     * Procesa los legajos de un empleado
     * @param string $Legajo Legajo del empleado
     * @param string $FechaDesde Fecha desde
     * @param string $FechaHasta Fecha hasta
     * @return string Respuesta del WebService
     */
    function procesar($Legajo, $FechaDesde, $FechaHasta)
    {
        // if ($this->ping()) {

        $FechaDesde = date('d/m/Y', strtotime($FechaDesde));
        $FechaHasta = date('d/m/Y', strtotime($FechaHasta));
        $Legajo = $Legajo;
        $ruta = $this->baseUrl() . '/' . "Procesar";

        $post_data = "{Usuario=Supervisor,TipoDePersonal=0,Legajos=[], LegajoDesde='$Legajo',LegajoHasta='$Legajo',FechaDesde='$FechaDesde',FechaHasta='$FechaHasta',Empresa=0,Planta=0,Sucursal=0,Grupo=0,Sector=0,Seccion=0}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        // $curl_error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        try {
            if ($curl_errno > 0) {
                $text = $curl_errno . ": Error al procesar."; // set error 
                throw new \Exception($text, $httpCode);
            }
            if ($httpCode == 404) {
                $text = $respuesta . ": Error al procesar."; // set error 
                throw new \Exception($text, $httpCode);
            }
            curl_close($ch);
            // $this->resp->response('', 0, $respuesta, $httpCode, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
            //$this->resp->response('', 0, $e->getMessage(), $e->getCode(), microtime(true), 0, 0);
            // exit;
        }
        // $processID = respuestaWebService($respuesta);
        // $url = rutaWebService("Estado?ProcesoId=" . $processID);

        // if ($httpCode == 201) {
        //     return EstadoProceso($url);
        // }
        // }
    }
    function ping()
    {
        $ch = curl_init(); // Inicializar el objeto curl
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl() . '/Ping?'); // Establecer la URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // The number of seconds to wait while trying to connect
        // Especificar cabeceras
        $headers = array(
            'Connection: keep-alive',
            'Accept: */*',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Especificar método
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_exec($ch); // extract information from response

        $curl_errno = curl_errno($ch); // get error code
        $curl_error = curl_error($ch); // get error information

        if ($curl_errno > 0) { // si hay error
            $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
            throw new \Exception($text, 1);
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
        curl_close($ch); // close curl handle
        try {
            if ($http_code != '201') {
                throw new \Exception("Error Ping WebService. \"Cod: $curl_errno: $curl_error\"", 1);
            }
            // $this->resp->response('', 0, 'Ping Correcto', $http_code, microtime(true), 0, 0);
            $this->log->write('Ping Webservice Correcto', date('Ymd') . '_ws_ping_' . ID_COMPANY . '.log');
            return true;
        } catch (\Exception $th) {
            $this->log->write($th->getMessage(), date('Ymd') . '_ws_ping_' . ID_COMPANY . '.log');
            $this->resp->response('', 0, $th->getMessage(), 400, microtime(true), 0, 0);
        }
    }
}
