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
    public function legajos()
    {
        $inicio = microtime(true);
        $datos = $this->validar_request_legajos(); // Valida los datos recibidos

        $Lega = $datos['Lega'] ?? [];
        $FechIni = date('Ymd', strtotime($datos['FechIni'])); // Fecha de inicio
        $FechFin = date('Ymd', strtotime($datos['FechFin'])); // Fecha de fin

        if ($FechIni > $FechFin) {
            $this->resp->respuesta([], 0, 'La fecha de Inicio no puede ser mayor a la fecha de Fin', 400, $inicio, 0, 0);
            exit;
        }
        $conn = $this->conect->conn();
        $cols = [
            'FICHAS.FicLega',
            'PERSONAL.LegApNo',
            'FICHAS.FicSect',
            'FICHAS.FicEmpr',
            'FICHAS.FicPlan',
            'FICHAS.FicConv',
            'FICHAS.FicSect',
            'FICHAS.FicSec2',
            'FICHAS.FicGrup',
            'FICHAS.FicSucu',
        ];
        $sql = "SELECT " . implode(',', $cols) . " FROM FICHAS";
        $sql .= " INNER JOIN PERSONAL on FICHAS.FicLega = PERSONAL.LegNume";
        $sql .= " WHERE FICHAS.FicLega > 0";
        if ($Lega) {
            $legajos = implode(',', array_map('intval', $Lega));
            $sql .= " AND FICHAS.FicLega IN ($legajos)"; // Si hay legajos, los agrego a la consulta
        }
        $sql .= " AND FICHAS.FicFech BETWEEN :FechIni AND :FechFin";
        $sql .= " GROUP BY " . implode(',', $cols) . " ORDER BY FICHAS.FicLega";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':FechIni', $FechIni, \PDO::PARAM_STR);
        $stmt->bindParam(':FechFin', $FechFin, \PDO::PARAM_STR);

        $stmt->execute(); // Ejecuto la consulta
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta

        foreach ($data as &$item) {
            $item['FicApNo'] = $item['LegApNo'];
            unset($item['LegApNo']);
        }
        unset($item); // liberar la referencia

        $total = count($data); // Cuento la cantidad de registros

        // $data = array_combine(array_column($data, 'FicLega'), array_column($data, 'FicApNo'));

        $this->resp->respuesta($data, $total, 'OK', 200, microtime(true), $total, 0);
        $stmt->closeCursor(); // Cierro el cursor
    }
    private function validar_request_legajos(): array
    {
        $datos = $this->getData;

        if ($this->tools->jsonNoValido()) {
            $errores = $this->tools->jsonNoValido();
            $this->resp->respuesta($errores, 0, "Formato JSON invalido", 400, microtime(true), 0, 0);
        }

        if (!$datos) {
            $this->resp->respuesta('', 0, "No se recibieron datos o hay errores", 400, microtime(true), 0, 0);
        }

        $FechIni = $datos['FechIni'] ?? '';
        $FechFin = $datos['FechFin'] ?? '';
        $Lega = $datos['Lega'] ?? [];

        $datosRecibidos = [ // Valores por defecto
            'Lega' => !is_array($Lega) ? [] : $Lega,
            'FechIni' => empty($FechIni) ? '' : $FechIni,
            'FechFin' => empty($FechFin) ? '' : $FechFin,
        ];

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'FechIni' => ['required', 'date'],
                'FechFin' => ['required', 'date'],
                'Lega' => ['arrInt'],
            ];

            $validator = new InputValidator($datosRecibidos, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos
            return $datosRecibidos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validar_request_legajos.log');
        }
        return $datosRecibidos;
    }
}
