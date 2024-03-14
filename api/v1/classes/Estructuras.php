<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\InputValidator;
use Classes\RRHHWebService;
use Classes\Tools;
use Classes\ParaGene;
use Flight;

class Estructuras
{
    private $resp;
    private $request;
    private $getData;
    private $query;
    private $log;
    private $conect;
    private $webservice;
    private $tools;
    private $paraGene;

    function __construct()
    {
        $this->resp = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->query = $this->request->query->getData();
        $this->log = new Log;
        $this->conect = new ConnectSqlSrv;
        $this->webservice = new RRHHWebService;
        $this->tools = new Tools;
        $this->paraGene = new ParaGene;
    }

    public function estructuras()
    {
        $conn = $this->conect->conn();
        $data = $this->getData;

        if (empty($data)) {
            $this->resp->respuesta('No hay datos', 0, 'Error', 400, microtime(true), 0, 0);
            exit;
        }

        $data['Empr'] = $data['Empr'] ?? '';
        $data['Plan'] = $data['Plan'] ?? '';
        $data['Conv'] = $data['Conv'] ?? '';
        $data['Sect'] = $data['Sect'] ?? '';
        $data['Secc'] = $data['Secc'] ?? '';
        $data['Grup'] = $data['Grup'] ?? '';
        $data['Sucu'] = $data['Sucu'] ?? '';

        $estructuras = [];

        if ($data['Empr']) {
            $empresa = explode(',', $data['Empr']);
            $empresa = array_map('intval', $empresa);
            $empresa = implode(',', $empresa);
            $sql = "SELECT EmpCodi, EmpRazon FROM EMPRESAS WHERE EmpCodi IN ($empresa)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Empresas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Empresas'] = $array['Empresas'];
        }

        if ($data['Plan']) {
            $plantas = explode(',', $data['Plan']);
            $plantas = array_map('intval', $plantas);
            $plantas = implode(',', $plantas);
            $sql = "SELECT PlaCodi, PlaDesc FROM PLANTAS WHERE PlaCodi IN ($plantas)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Plantas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Plantas'] = $array['Plantas'];
        }

        if ($data['Conv']) {
            $convenios = explode(',', $data['Conv']);
            $convenios = array_map('intval', $convenios);
            $convenios = implode(',', $convenios);
            $sql = "SELECT ConCodi, ConDesc FROM CONVENIO WHERE ConCodi IN ($convenios)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Convenios'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Convenios'] = $array['Convenios'];
        }

        if ($data['Sect']) {
            $sectores = explode(',', $data['Sect']);
            $sectores = array_map('intval', $sectores);
            $sectores = implode(',', $sectores);
            $sql = "SELECT SecCodi, SecDesc FROM SECTORES WHERE SecCodi IN ($sectores)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Sectores'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Sectores'] = $array['Sectores'];
        }

        if ($data['Secc']) {
            $secciones = explode(',', $data['Secc']);
            $secciones = array_map('intval', $secciones);
            $secciones = implode(',', $secciones);
            $sql = "SELECT SecCodi, Se2Codi, Se2Desc FROM SECCION WHERE CONCAT(SecCodi, Se2Codi) IN ($secciones)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Secciones'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Secciones'] = $array['Secciones'];
        }

        if ($data['Grup']) {
            $grupos = explode(',', $data['Grup']);
            $grupos = array_map('intval', $grupos);
            $grupos = implode(',', $grupos);
            $sql = "SELECT GruCodi, GruDesc FROM GRUPOS WHERE GruCodi IN ($grupos)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Grupos'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Grupos'] = $array['Grupos'];
        }

        if ($data['Sucu']) {
            $sucursales = explode(',', $data['Sucu']);
            $sucursales = array_map('intval', $sucursales);
            $sucursales = implode(',', $sucursales);
            $sql = "SELECT SucCodi, SucDesc FROM SUCURSALES WHERE SucCodi IN ($sucursales)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Sucursales'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Sucursales'] = $array['Sucursales'];
        }

        $this->conect->close($conn);

        $this->resp->respuesta($estructuras, 1, 'OK', 200, microtime(true), 0, 0);


    }
}
