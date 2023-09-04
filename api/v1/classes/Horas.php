<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\InputValidator;
use Classes\RRHHWebService;
use Classes\Tools;
use Flight;

class Horas
{
    private $resp;
    private $request;
    private $getData;
    private $query;
    private $log;
    private $conect;
    private $webservice;
    private $tools;

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
    }
    public function update()
    {
        $inicio = microtime(true);
        $datos = $this->validarInputs();
        $conn = $this->conect->conn();
        $FechaHoraActual = $this->conect->FechaHora(); // Fecha y hora actual
        try {
            $conn->beginTransaction(); // Iniciar transacción

            $sql = "UPDATE FICHAS1 SET FicHsAu2 = :HsAu, FicEsta = :Esta, FicCaus = :Moti, FicObse = :Obse, FechaHora = :FechaHora, FicValor = :Valor WHERE FicLega = :Lega AND FicFech = :Fecha AND FicTurn = 1 AND FicHora = :Hora";
            $stmt = $conn->prepare($sql);

            $totalAffectedRows = 0;

            foreach ($datos as $dato) { // Recorro los datos
                $dato['Fecha'] = date('Ymd', strtotime($dato['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $stmt->bindValue(':HsAu', $dato['HsAu'], \PDO::PARAM_STR);
                $stmt->bindValue(':Esta', $dato['Esta'], \PDO::PARAM_INT);
                $stmt->bindValue(':Moti', $dato['Moti'], \PDO::PARAM_INT);
                $stmt->bindValue(':Obse', $dato['Obse'], \PDO::PARAM_STR);
                $stmt->bindValue(':FechaHora', $FechaHoraActual, \PDO::PARAM_STR);
                $stmt->bindValue(':Lega', $dato['Lega'], \PDO::PARAM_INT);
                $stmt->bindValue(':Fecha', $dato['Fecha'], \PDO::PARAM_STR);
                $stmt->bindValue(':Hora', $dato['Hora'], \PDO::PARAM_INT);
                $stmt->bindValue(':Valor', $dato['Valor'], \PDO::PARAM_STR);
                $stmt->execute(); // Ejecuto la consulta
                $totalAffectedRows += $stmt->rowCount(); // Cuento la cantidad de filas afectadas
            }

            $conn->commit(); // Confirmar la transacción

            $groupedData = [];

            $minDate = PHP_INT_MAX;
            $maxDate = 0;

            foreach ($datos as $item) {
                $lega = $item["Lega"];
                $fechaMin = strtotime($item["Fecha"]);
                $fechaMax = strtotime($item["Fecha"]);

                if ($fechaMin < $minDate) {
                    $minDate = $fechaMin;
                }
                if ($fechaMax > $maxDate) {
                    $maxDate = $fechaMax;
                }

                if (!isset($groupedData[$lega])) {
                    $groupedData[$lega] = true;
                }
            }

            $agrup = [ // Agrupar los datos para procesarlos en el WebService
                "Legajos" => array_keys($groupedData), // Obtengo los legajos
                "Fechas" => [ // Obtengo las fechas
                    "Desde" => date("Y-m-d", $minDate),
                    "Hasta" => date("Y-m-d", $maxDate)
                ]
            ];

            if ($agrup) { // Si hay datos para procesar
                try { // Procesar los legajos en el WebService
                    if (!isset($agrup['Fechas']['Desde']) || !isset($agrup['Fechas']['Hasta']) || empty($agrup['Fechas']['Desde']) || empty($agrup['Fechas']['Hasta'])) {
                        throw new \Exception("No se recibieron las fechas", 1);
                    }
                    // Validar que existan los legajos
                    if (!isset($agrup['Legajos']) || empty($agrup['Legajos'])) {
                        throw new \Exception("No se recibieron los legajos", 1);
                    }
                    $Legajos = $agrup['Legajos'];
                    $Desde = $agrup['Fechas']['Desde'];
                    $Hasta = $agrup['Fechas']['Hasta'];
                    $this->webservice->procesar_legajos($Legajos, $Desde, $Hasta);
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    return false;
                }
            }
            $this->resp->response([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->response('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_updateHoras_' . ID_COMPANY . '.log');
            exit;
        }
    }
    private function procpend($datos)
    {
        $conn = $this->conect->conn();
        $FechaHoraActual = $this->conect->FechaHora(); // Fecha y hora actual
        try {
            if (empty($datos)) {
                return false;
            }
            foreach ($datos as $dato) {
                $dato['Fecha'] = date('Ymd', strtotime($dato['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $sql = "INSERT INTO PROCPEND (PrPeTipo, PrPeLega, PrPeFech, FechaHora) VALUES (0, '$dato[Lega]',  '$dato[Fecha]', '$FechaHoraActual')";
                $stmt = $conn->prepare($sql);
                $stmt->execute(); // Ejecuto la consulta
            }
            // $stmt = null;
            $this->conect = null;
        } catch (\PDOException $e) {
            // $conn->rollBack();
            // $this->resp->response('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            // $this->log->write($e->getMessage(), date('Ymd') . '_procPend_' . ID_COMPANY . '.log');
            // exit;
        }
    }
    private function validarInputs()
    {
        $datos = $this->getData;
        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validacion
                'Lega'  => ['int'],
                'Fecha' => ['required', 'date'],
                'Hora'  => ['required', 'smallint'],
                'HsAu'  => ['required', 'time'],
                'Esta'  => ['allowed012'],
                'Obse'  => ['varchar40'],
                'Moti'  => ['smallint'],
                'Valor' => ['decima12.2'],
            ];

            $FechaHoraActual = date('YmdHis') . substr((string)microtime(), 1, 8); // Fecha y hora actual
            $customValueKey = array( // Valores por defecto
                'Lega' => "0",
                'Fecha' => '00000000',
                'Hora' => "0",
                'HsAu' => '00:00',
                'Esta' => "2",
                'Obse' => '',
                'Moti' => "0",
                'Valor' => "0",
                'FechaHora' => ''
            );
            $keyData = array_keys($customValueKey); // Obtengo las claves del array $customValueKey

            foreach ($datos as $dato) { // Recorro los datos recibidos
                foreach ($keyData as $keyD) { // Recorro las claves del array $customValueKey
                    if (!array_key_exists($keyD, $dato) || empty($dato[$keyD])) { // Si no existe la clave en el array $dato o esta vacio
                        $dato[$keyD] = $customValueKey[$keyD]; // Le asigno el valor por defecto del array $customValueKey
                    }
                }
                $datosModificados[] = $dato; // Guardo los datos modificados en un array
                $validator = new InputValidator($dato, $rules); // Instancio la clase InputValidator y le paso los datos y las reglas de validacion del array $rules
                $validator->validate(); // Valido los datos
            }
            return $datosModificados;
            // $this->resp->response($datosModificados, 0, 'Todo bien con los datos', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->response('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputs.log');
        }
    }
}
