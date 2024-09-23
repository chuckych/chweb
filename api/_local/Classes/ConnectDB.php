<?php

namespace Classes;

class ConnectDB
{
    private $conn;
    private $mapDB;
    public function __construct()
    {
        $this->mapDB = [
            'DBHost' => getenv('DB_CHWEB_HOST') !== false ? getenv('DB_CHWEB_HOST') : '', // Servidor de la base de datos
            'DBUser' => getenv('DB_CHWEB_USER') !== false ? getenv('DB_CHWEB_USER') : '', //
            'DBPass' => getenv('DB_CHWEB_PASSWORD') !== false ? getenv('DB_CHWEB_PASSWORD') : '', //
            'DBName' => getenv('DB_CHWEB_NAME') !== false ? getenv('DB_CHWEB_NAME') : '' //
        ];
        $this->check_data_connection($this->mapDB);
        $this->conn = $this->conn();
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
        try { // Intenta conectar a la base de datos
            $dsn = "mysql:host={$this->mapDB['DBHost']};dbname={$this->mapDB['DBName']}";
            $conectar = new \PDO( // Instancia de la clase PDO
                $dsn, // DSN
                $this->mapDB['DBUser'],
                $this->mapDB['DBPass'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );

            if (!$conectar) {
                throw new \PDOException("No se pudo conectar a la base de datos", 400);
            }
            // file_put_contents('conect_sql.log', json_encode($dsn));
            return $conectar;
        } catch (\PDOException $e) {
            throw new \PDOException($e, (int) $e->getCode());
        }
    }
    public function close($conn)
    {
        $conn = null;
    }
    /**
     * Retorna la fecha y hora actual en la zona horaria especificada.
     *
     * @param string $timezone La zona horaria a utilizar. Por defecto 'America/Argentina/Buenos_Aires'.
     * @return string La fecha y hora actual en el formato 'YYYYMMDD HH:MM:SS.mmm'.
     */
    public function FechaHora($timezone = 'America/Argentina/Buenos_Aires'): string
    {
        date_default_timezone_set($timezone);
        $t = explode(" ", microtime());
        $t = date("Ymd H:i:s", $t[1]) . substr((string) $t[0], 1, 4);
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
        $connect = !empty($connDB) ? $connDB : $this->conn; // Si se proporciona una conexión, la utiliza, de lo contrario, utiliza la conexión actual

        if (!$connect) {
            throw new \Exception("No hay conexión a la base de datos", 400);
        }
        return $connect;
    }
}
