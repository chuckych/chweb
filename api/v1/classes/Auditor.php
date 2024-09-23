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

class Auditor
{
    private $resp;
    private $request;
    private $getData;
    private $query;
    private $log;
    private $conect;
    private $tools;
    function __construct()
    {
        $this->resp    = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->log     = new Log;
        $this->conect  = new ConnectSqlSrv;
        $this->tools   = new Tools;
    }
    public function add($data = [])
    {
        $inicio          = microtime(true); // Tiempo de inicio del proceso
        $datos           = $this->validarDatos($data); // Valida los datos
        $conn            = $this->conect->conn(); // Conexión a la base de datos

        $get_dbdata = $this->get_dbdata();
        $get_dbdata = $get_dbdata ? explode("_", $get_dbdata[0]['BDVersion']) : '';
        $get_dbdata = intval($get_dbdata[1] ?? 60) ?? '';

        try {
            $conn->beginTransaction(); // Iniciar transacción

            $sql = "INSERT INTO AUDITOR (AudFech, AudHora, AudUser, AudTerm, AudModu, AudTipo, AudDato, FechaHora, AudZonaHoraria) VALUES (:AudFech, :AudHora, :AudUser, :AudTerm, :AudModu, :AudTipo, :AudDato, :FechaHora, :AudZonaHoraria)";

            if ($get_dbdata < 70) { // Si la versión de la base de datos es menor a 70
                $sql = "INSERT INTO AUDITOR (AudFech, AudHora, AudUser, AudTerm, AudModu, AudTipo, AudDato, FechaHora, AudZonaHoraria) VALUES (:AudFech, :AudHora, :AudUser, :AudTerm, :AudModu, :AudTipo, :AudDato, :FechaHora"; // se omite el campo AudZonaHoraria en la consulta
            }

            $totalAffectedRows = 0;
            $seconds = 0.0001;
            foreach ($datos as $dato) { // Recorro los datos
                $stmt = $conn->prepare($sql); // Preparo la consulta
                $AudUser = substr($dato['AudUser'], 0, 10);
                $AudDato = substr($dato['AudDato'], 0, 100); // Limita la cantidad de caracteres a 100
                $AudFech = $this->sumar_segundos_a_fecha($dato['AudFech'], $seconds);
                $stmt->bindValue(':AudFech', $AudFech, \PDO::PARAM_STR);
                $stmt->bindValue(':AudHora', $dato['AudHora'], \PDO::PARAM_STR);
                $stmt->bindValue(':AudUser', $AudUser, \PDO::PARAM_STR);
                $stmt->bindValue(':AudTerm', $dato['AudTerm'], \PDO::PARAM_STR);
                $stmt->bindValue(':AudModu', $dato['AudModu'], \PDO::PARAM_INT);
                $stmt->bindValue(':AudTipo', $dato['AudTipo'], \PDO::PARAM_STR);
                $stmt->bindValue(':AudDato', $AudDato, \PDO::PARAM_STR);
                $stmt->bindValue(':FechaHora', $dato['FechaHora'], \PDO::PARAM_STR);
                ($get_dbdata < 70) ? '' : $stmt->bindValue(':AudZonaHoraria', $dato['AudZonaHoraria'], \PDO::PARAM_STR);
                $seconds += 0.0004;
                $stmt->execute(); // Ejecuta la consulta
                $totalAffectedRows += $stmt->rowCount(); // Devuelve el número de filas afectadas por la última sentencia SQL
            }
            $conn->commit(); // Confirmar la transacción
            if (!$data) {
                $this->resp->respuesta([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
            }
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_HorariosDesde_' . ID_COMPANY . '.log');
        }
    }

    private function validarDatos($data)
    {
        $datos              = $data ?? $this->getData;
        $request            = $this->request;
        $ip                 = $request->ip;
        $FechaHoraActual    = $this->conect->FechaHora(); // Fecha y hora actual
        $FechaHoraActualUTC = $this->conect->FechaHora('UTC'); // Fecha y hora actual en UTC
        $HoraActual = $this->conect->hora(); // Fecha y hora actual en UTC

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'AudUser' => ['required', 'varchar100'], // Luego se recorta a 10 caracteres en la función add
                'AudTipo' => ['required', 'varchar1'],
                'AudDato' => ['required', 'varchar200'], // Luego se recorta a 100 caracteres en la función add
            ];
            $customValueKey = [ // Valores por defecto
                'AudFech'        => $FechaHoraActual, // Fecha actual
                'AudHora'        => $HoraActual, // Hora actual
                'AudUser'        => 'API', // Usuario de la API
                'AudTerm'        => $ip, // IP del cliente
                'AudModu'        => '21',
                'AudTipo'        => '',
                'AudDato'        => '',
                'FechaHora'      => $FechaHoraActualUTC, // Fecha y hora actual
                'AudZonaHoraria' => '(UTC-03:00) Ciudad de Buenos Aires', // Zona horaria
            ];

            $keyData = array_keys($customValueKey); // Obtengo las claves del array $customValueKey

            foreach ($datos as $dato) { // Recorro los datos recibidos
                foreach ($keyData as $keyD) { // Recorro las claves del array $customValueKey
                    if (!array_key_exists($keyD, $dato) || empty($dato[$keyD])) { // Si no existe la clave en el array $dato o esta vacío
                        $dato[$keyD] = $customValueKey[$keyD]; // Le asigno el valor por defecto del array $customValueKey
                    }
                }
                $datosModificados[] = $dato; // Guardo los datos modificados en un array
                $validator = new InputValidator($dato, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
                $validator->validate(); // Valido los datos
            }
            return $datosModificados;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputsAuditor.log');
        }
    }
    public function get_dbdata()
    {
        // return data from table DBData
        $sql = "SELECT * FROM DBData";
        $params = [];
        $resultSet = $this->conect->executeQueryWhithParams($sql, $params);
        return $resultSet;
    }
    public function sumar_segundos_a_fecha($fecha, $seconds)
    {
        $fecha = \DateTime::createFromFormat('Ymd H:i:s.u', $fecha);
        $fecha->modify("+{$seconds} seconds");
        $fechaStr = $fecha->format('Ymd H:i:s.u');
        $fechaStr = substr($fechaStr, 0, 21);
        return $fechaStr;
    }
}
