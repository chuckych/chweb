<?php

namespace Classes;

// use Classes\DataCompany;
use Classes\Log;
use Classes\InputValidator;
use Classes\Tools;
use Classes\Response;


use Flight;
// use Classes\Response;


class ConnectSqlSrv
{
    private $conn;
    private $log;
    private $mapDB;
    private $tools;
    private $resp;
    public function __construct()
    {
        $this->log = new Log; // Instancia de la clase Log
        // $this->resp = new Response;
        $this->mapDB = [
            'DBHost' => getenv('DB_HOST') !== false ? getenv('DB_HOST') : '', // Servidor de la base de datos
            'DBUser' => getenv('DB_USER') !== false ? getenv('DB_USER') : '', //
            'DBPass' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '', //
            'DBName' => getenv('DB_NAME') !== false ? getenv('DB_NAME') : '' //
        ];
        $this->check_data_connection($this->mapDB);
        // $this->conn = $this->conn();
    }
    private function check_data_connection($mapDB = [])
    {
        if (!$mapDB) {
            throw new \PDOException("No hay datos de conexión a la base de datos", 400);
        }
        foreach ($mapDB as $key => $value) {
            if (empty($value)) {
                throw new \PDOException("No hay datos de {$key}", 400);
            }
        }
    }
    /** 
     * Devuelve la conexión a la base de datos
     * @return \PDO Conexión a la base de datos
     */
    public function conn()
    {
        if ($this->conn) { // Si ya hay una conexión establecida
            return $this->conn; // Retorna la conexión existente si ya está establecida
        }

        try { // Intenta conectar a la base de datos
            $this->conn = new \PDO( // Instancia de la clase PDO
                "sqlsrv:server={$this->mapDB['DBHost']};Database={$this->mapDB['DBName']}", // DSN
                $this->mapDB['DBUser'],
                $this->mapDB['DBPass'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );
            // file_put_contents('log.log', print_r('log', true) . PHP_EOL, FILE_APPEND); // genera log
        } catch (\PDOException $e) {
            $idCompany = (defined('ID_COMPANY')) ? ID_COMPANY : 0;
            $this->log->write($e->getMessage(), date('Ymd') . '_sqlsr_connect_' . $idCompany . '.log');
            throw new \Exception($e->getMessage(), (int) $e->getCode());
        }
        return $this->conn;
    }
    public function test_connect()
    {
        $inicio = microtime(true); // Inicio del script

        $rules = [ // Reglas de validación
            'DBHost' => ['required', 'varchar100'],
            'DBName' => ['required', 'varchar100'],
            'DBUser' => ['required', 'varchar100'],
            'DBPass' => ['varchar100'],
        ];
        $customValueKey = [ // Valores por defecto
            'DBHost' => "",
            'DBName' => "",
            'DBUser' => "",
            'DBPass' => "",
        ];
        $this->tools = new Tools;

        $data = Flight::request()->data->getData();
        $datos = $this->tools->validar_datos($data, $rules, $customValueKey, 'test_connect');
        $this->resp = new Response;
        try { // Intenta conectar a la base de datos
            $conectar = new \PDO( // Instancia de la clase PDO
                "sqlsrv:server={$datos['DBHost']};Database={$datos['DBName']}", // DSN
                $datos['DBUser'],
                $datos['DBPass'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );

            $sql = "SELECT @@VERSION";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
            $rs = $stmt->fetch(\PDO::FETCH_ASSOC);
            $versionStr = array_values($rs)[0];
            $info = [
                // 'SQLServerVersion' => $conectar->getAttribute(\PDO::ATTR_SERVER_VERSION),
                'SQLServerName' => $conectar->getAttribute(\PDO::ATTR_SERVER_INFO),
                // 'DriverName' => $conectar->getAttribute(\PDO::ATTR_DRIVER_NAME),
                // 'ClientVersion' => $conectar->getAttribute(\PDO::ATTR_CLIENT_VERSION),
                'VersionStr' => $versionStr
            ];
            $stmt = null;
            $conectar = null;
            $this->resp->respuesta($info, 1, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $idCompany = (defined('ID_COMPANY')) ? ID_COMPANY : 0;
            $this->log->write($e->getMessage(), date('Ymd') . '_sqlsr_test_connect_' . $idCompany . '.log');
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
    public function close($conn)
    {
        $conn = null;
    }
    /**
     * Ejecuta una consulta SQL
     * @param string $sql Consulta SQL
     * @param array $params Parámetros de la consulta
     * @return array Resultado de la consulta
     * @throws \PDOException
     * Example:
     * $sql = 'SELECT * FROM FICHAS1 WHERE FicLega = :FicLega AND FicEsta = :FicEsta AND FicFech = :FicFech';
     * $params = array(
     * ':FicLega' => 29988600,
     * ':FicEsta' => 0,
     * ':FicFech' => '20191024'
     * );
     * $resultSet = $this->conect->executeQuery($sql, $params);
     */
    public function executeQueryWhithParams($sql, $params = [])
    {
        try {
            $conn = $this->check_connection($this->conn);
            $stmt = $conn->prepare($sql);

            foreach ($params as $paramName => $paramValue) {
                $paramType = \PDO::PARAM_STR; // Tipo de dato por defecto

                // Determina el tipo de dato basado en el valor proporcionado
                if (is_int($paramValue)) {
                    $paramType = \PDO::PARAM_INT;
                } elseif (is_bool($paramValue)) {
                    $paramType = \PDO::PARAM_BOOL;
                } else {
                    $paramType = \PDO::PARAM_STR;
                }

                $stmt->bindValue($paramName, $paramValue, $paramType);
            }

            $stmt->execute();

            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];

            $stmt = null;
            // $this->conn = null;
            $conn = null;

            return $resultSet;
        } catch (\PDOException $e) {
            $this->log->write($e->getMessage(), date('Ymd') . '_executeQuery_' . ID_COMPANY . '.log');
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * Ejecuta una consulta SQL con condiciones
     * @param string $sql Consulta SQL
     * @param array $conditions Condiciones de la consulta
     * @param array $params Parámetros de la consulta
     * @return array Resultado de la consulta
     * @throws \PDOException
     * Example: 
     * $conditions = array(
     * 'FicLega = :FicLega',
     * 'FicEsta = :FicEsta',
     * 'FicFech = :FicFech'
     * );
     * $params = array(
     * ':FicLega' => 29988600,
     * ':FicEsta' => 0,
     * ':FicFech' => '20191024'
     * );
     * $resultSet = $this->conect->executeQueryWithCondition('SELECT * FROM FICHAS1', $conditions, $params);
     */
    public function executeQueryWithCondition($sql, $conditions = [], $params = [])
    {
        try {
            // Construye la parte WHERE de la consulta
            $where = '';
            if (!empty($conditions)) {
                $where = ' WHERE ' . array_shift($conditions);
                foreach ($conditions as $condition) {
                    $where .= ' AND ' . $condition;
                }
            }

            // Construye la consulta completa
            $sql = $sql . $where;
            // Prepara y ejecuta la consulta
            // $stmt = $this->conn->prepare($sql);
            $conn = $this->check_connection($this->conn);
            $stmt = $conn->prepare($sql);

            foreach ($params as $paramName => &$paramValue) {
                if (is_int($paramValue)) {
                    $paramType = \PDO::PARAM_INT;
                } elseif (is_bool($paramValue)) {
                    $paramType = \PDO::PARAM_BOOL;
                } else {
                    $paramType = \PDO::PARAM_STR;
                }

                $stmt->bindParam($paramName, $paramValue, $paramType);
            }

            $stmt->execute();

            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = null;
            // $this->conn = null;
            $conn = null;

            return $resultSet;
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
    /**
     * Retorna la fecha y hora actual en la zona horaria especificada.
     *
     * @param string $timezone La zona horaria a utilizar. Por defecto 'America/Argentina/Buenos_Aires'.
     * @return string La fecha y hora actual en el formato 'YYYYMMDD HH:MM:SS.mmm'.
     */
    public function FechaHora_old($timezone = 'America/Argentina/Buenos_Aires'): string
    {
        date_default_timezone_set($timezone);
        $t = explode(" ", microtime());
        $t = date("Ymd H:i:s", $t[1]) . substr((string) $t[0], 1, 4);
        return $t ?? '';
    }
    public function FechaHora($timezone = 'America/Argentina/Buenos_Aires'): string
    {
        date_default_timezone_set($timezone);
        $t = explode(" ", microtime());
        $t = date("Y-m-d H:i:s", $t[1]) . substr((string) $t[0], 1, 4);
        return $t ?? '';
    }
    public function Fecha($format = "Y-m-d", $timezone = 'America/Argentina/Buenos_Aires'): string
    {
        date_default_timezone_set($timezone);
        $t = date($format);
        return $t ?? '';
    }
    public function hora($timezone = 'America/Argentina/Buenos_Aires'): string
    {
        date_default_timezone_set($timezone);
        $t = date("H:i:s");
        return $t ?? '';
    }
    public function fn_aud_tipo($tipo)
    {
        switch ($tipo) {
            case 'alta':
                return 'A';
            case 'baja':
                return 'B';
            case 'modificación':
                return 'M';
            case 'proceso':
                return 'P';
            default:
                return '';
        }
    }
    public function check_connection($connDB = '')
    {
        $connect = !empty($connDB) ? $connDB : $this->conn(); // Si se proporciona una conexión, la utiliza, de lo contrario, utiliza la conexión actual

        if (!$connect) {
            throw new \Exception("No hay conexión a la base de datos", 400);
        }
        return $connect;
    }
    public function getMapDB()
    {
        return $this->mapDB;
    }
}
