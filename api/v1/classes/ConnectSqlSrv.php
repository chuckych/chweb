<?php

namespace Classes;

use Classes\DataCompany;
use Classes\Log;


class ConnectSqlSrv
{
    private $conn;
    private $dataCompany;

    private $log;

    public function __construct()
    {
        $this->log = new Log; // Instancia de la clase Log

        $this->dataCompany = new DataCompany; // Instancia de la clase dataCompany
        $dataCompany = $this->dataCompany->get(); // Obtiene los datos de la empresa y valida el token

        $db         = $dataCompany['DBName']; // Nombre de la base de datos
        $user       = $dataCompany['DBUser']; // Usuario de la base de datos
        $pass       = $dataCompany['DBPass']; // Contraseña de la base de datos
        $serverName = $dataCompany['DBHost']; // Host de la base de datos

        try { // Intenta conectar a la base de datos
            if (!$serverName) {
                throw new \PDOException("No hay datos del servidor SQL");
            }
            if (!$db) {
                throw new \PDOException("No hay de base de datos SQL");
            }
            if (!$user) {
                throw new \PDOException("No hay datos de usuario SQL");
            }
            if (!$pass) {
                throw new \PDOException("No hay datos de contraseña SQL");
            }

            $this->conn = new \PDO( // Instancia de la clase PDO
                "sqlsrv:server=$serverName;Database=$db", // DSN
                $user,
                $pass,
                array(
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );
        } catch (\PDOException $e) {
            $this->log->write(($e->getMessage()), date('Ymd') . '_sqlsr_connect.log');
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
    /** 
     * Devuelve la conexión a la base de datos
     * @return \PDO Conexión a la base de datos
     */
    public function conn()
    {
        return $this->conn;
    }

    /**
     * Cierra la conexión a la base de datos
     * @param \PDO $conn Conexión a la base de datos
     * @return \PDO Conexión cerrada
     */
    public function close($conn)
    {
        $conn = null; // Cerrar la conexión
        return $conn;
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
    public function executeQueryWhithParams($sql, $params = array())
    {
        try {
            $stmt = $this->conn->prepare($sql);

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

            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = null;
            $this->conn = null;

            return $resultSet;
        } catch (\PDOException $e) {
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
    public function executeQueryWithCondition($sql, $conditions = array(), $params = array())
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
            $stmt = $this->conn->prepare($sql);

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
            $this->conn = null;

            return $resultSet;
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
    public function FechaHora()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $t = explode(" ", microtime());
        $t = date("Ymd H:i:s", $t[1]) . substr((string) $t[0], 1, 4);
        return $t;
    }
}
