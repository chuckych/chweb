<?php

namespace Classes;

use Exception;
use PDO;

class Querydb
{
    private $a;

    public function __construct($a)
    {
        $this->a = $a;
    }
    public function query($query, $count = 0)
    {

        $dataCompany = array(
            'host' => $this->a['DBHost'],
            'user' => $this->a['DBUser'],
            'pass' => $this->a['DBPass'],
            'db' => $this->a['DBName'],
            'auth' => $this->a['DBAuth'],
            'idCompany' => $this->a['idCompany'],
            'nameCompany' => $this->a['nameCompany'],
            'hostCHWeb' => $this->a['hostCHWeb'],
        );

        if (!$query) {
            http_response_code(400);
            (response(array(), 0, 'empty query', 400, microtime(true), 0, $dataCompany['idCompany']));
            exit;
        }

        require __DIR__ . './connectDBPDO.php';
        try {
            $resultSet = array();
            $stmt = $conn->query($query);
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $resultSet[] = $r;
            }
            $stmt = null;
            $conn = null;
            return $resultSet;
        } catch (Exception $e) {
            $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorMSQuery.log'; // ruta del archivo de Log de errores
            writeLog(PHP_EOL . 'Message: ' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . PHP_EOL . 'Source: ' . '"' . $_SERVER['REQUEST_URI'] . '"', $pathLog); // escribir en el log de errores el error
            writeLog(PHP_EOL . 'Query: ' . $query, $pathLog); // escribir en el log de errores el error
            http_response_code(400);
            (response(array(), 0, $e->getMessage(), 400, microtime(true), 0, ''));
            exit;
        }
    }

    public function save($query, $count = 0)
    {

        $dataCompany = array(
            'host' => $this->a['DBHost'],
            'user' => $this->a['DBUser'],
            'pass' => $this->a['DBPass'],
            'db' => $this->a['DBName'],
            'auth' => $this->a['DBAuth'],
            'idCompany' => $this->a['idCompany'],
            'nameCompany' => $this->a['nameCompany'],
            'hostCHWeb' => $this->a['hostCHWeb'],
        );

        if (!$query) {
            http_response_code(400);
            (response(array(), 0, 'empty query', 400, timeStart(), 0, $dataCompany['idCompany']));
            exit;
        }
        require __DIR__ . './connectDBPDO.php';
        try {
            $resultSet = array();
            $stmt = $conn->query($query);
            if ($stmt) {
                $stmt = null;
                $conn = null;
                return true;
            } else {
                $stmt = null;
                $conn = null;
                return false;
            }
        } catch (Exception $e) {
            $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorMSQuery.log'; // ruta del archivo de Log de errores
            writeLog(PHP_EOL . 'Message: ' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . PHP_EOL . 'Source: ' . '"' . $_SERVER['REQUEST_URI'] . '"', $pathLog); // escribir en el log de errores el error
            writeLog(PHP_EOL . 'Query: ' . $query, $pathLog); // escribir en el log de errores el error
            http_response_code(400);
            (response(array(), 0, $e->getMessage(), 400, timeStart(), 0, ''));
            exit;
        }
    }
}
