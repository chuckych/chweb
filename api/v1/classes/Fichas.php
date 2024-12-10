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

        $minYear = (int) date('Y', strtotime($array['min']));
        $maxYear = (int) date('Y', strtotime($array['max']));
        $years = range($minYear, $maxYear);

        $yearsWithMonths = [];

        foreach ($years as $year) { // Recorro los años para obtener los meses de cada año 
            $startMonth = ($year == $minYear) ? (int) date('m', strtotime($array['min'])) : 1; // Si el año es igual al año mínimo, el mes inicial es el mes mínimo, sino es 1
            $endMonth = ($year == $maxYear) ? (int) date('m', strtotime($array['max'])) : 12; // Si el año es igual al año máximo, el mes final es el mes máximo, sino es 12
            $months = range($startMonth, $endMonth); // Obtengo los meses del año
            $yearsWithMonths[$year] = $months; // Guardo los meses en el array
        }

        $a = [
            'min' => $array['min'], // "min": "2024-09-01",
            'max' => $array['max'], // "max": "2024-10-23"
            'años' => $yearsWithMonths, // "years": {2024: [9, 10], 2025: [1, 2, ..., 12]}
        ];

        $this->resp->respuesta($a, 1, 'OK', 200, microtime(true), 0, 0);
        $stmt->closeCursor(); // Cierro el cursor
    }
}
