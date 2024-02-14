<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Flight;

/**
 * Clase para obtener la informacion de la empresa y validar el token recibido en cada request
 * @package Classes
 */
class DataCompany
{
    private $urlData;
    private $resp;
    private $request;
    private $log;
    private $iniData;
    private $token;

    function __construct()
    {
        $this->urlData = __DIR__ . '../../../../mobileApikey.php'; // url del archivo ;
        $this->resp = new Response;
        $this->request = Flight::request();
        $this->log = new Log;
        $this->iniData = $this->info();
        $this->token = $_SERVER['HTTP_TOKEN'] ?? '';
    }
    function info() // obtiene el json de la url
    {

        $url = $this->urlData; // url del archivo

        try {
            if (!file_exists($url)) { // Si no existe el archivo
                throw new \Exception("No existe archivo \"$url\"");
            }
            $data = file_get_contents($url); // obtenemos el contenido del archivo

            if (!$data) { // si el contenido está vacío
                throw new \Exception("No hay informacion en el archivo \"$url\"");
            }

            $data = parse_ini_file($url, true); // Obtenemos los datos del mobileApikey.php
            return $data; // devolvemos el json
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_getIni_' . ID_COMPANY . '.log');
        }
    }
    function get($key = '')
    {
        $iniData = $this->iniData;
        $token = $this->token;

        try {

            if (!isset($token) || empty($token) || !$token) {
                throw new \Exception("El token es requerido");
            }
            if (!is_array($iniData) || empty($iniData) || !$iniData || !isset($iniData)) {
                throw new \Exception("Error de configuración");
            }

            $filteredData = array_filter($iniData, function ($element) use ($token) {
                return $element['Token'] === $token;
            });

            $firstElement = reset($filteredData); // Obtener el primer elemento del resultado

            if ($firstElement === false) {
                throw new \Exception("Invalid Token");
            }

            if ($key) {
                if (!isset($firstElement[$key])) {
                    throw new \Exception("Invalid Key");
                }
                $firstElement = $firstElement[$key];
            }
            return $firstElement;
            // $this->resp->respuesta($firstElement, 0, '', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 401, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_getToken.log');
            exit;
        }
    }
    function checkToken()
    {
        $iniData = $this->iniData;
        $token = $this->token;

        try {

            if (!isset($token) || empty($token) || !$token) {
                throw new \Exception("El token es requerido");
            }
            if (!is_array($iniData) || empty($iniData) || !$iniData || !isset($iniData)) {
                throw new \Exception("Error de configuración");
            }

            $filteredData = array_filter($iniData, function ($element) use ($token) {
                return $element['Token'] === $token;
            });

            $dataCompany = reset($filteredData); // Obtener el primer elemento del resultado

            if ($dataCompany === false) {
                throw new \Exception("Invalid Token");
            }
            $_SESSION['DataCompany'] = $dataCompany;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 401, microtime(true), 0, ID_COMPANY);
            $this->log->write($e->getMessage(), date('Ymd') . '_getToken_' . ID_COMPANY . '.log');
            session_destroy();
            exit;
        }
    }
}
