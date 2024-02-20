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

class Fichas
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

    public function dateMinMax()
    {
        $conn = $this->conect->conn();

        $sql = "SELECT MIN(FICHAS.FicFech) AS 'min', MAX(FICHAS.FicFech) AS 'max' FROM FICHAS WHERE FICHAS.FicFech !='17530101'";
        $stmt = $conn->prepare($sql);
        $stmt->execute(); // Ejecuto la consulta
        $array = $stmt->fetch(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
        $array['min'] = date('Y-m-d', strtotime($array['min']));
        $array['max'] = date('Y-m-d', strtotime($array['max']));
        $a = array(
            'min' => ($array['min']),
            'max' => $array['max']
        );
        $this->resp->respuesta($array, 1, 'OK', 200, microtime(true), 0, 0);
        $stmt->closeCursor(); // Cierro el cursor

    }
}
