<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\InputValidator;
use Classes\RRHHWebService;
use Classes\Tools;
use Classes\ParaGene;
use Error;
use Flight;
use flight\net\Request;

class Auditor
{
    private Response $resp;
    private Request $request;
    private array $getData;
    private array $query;
    private Log $log;
    private ConnectSqlSrv $conect;
    private Tools $tools;
    private string $NameLog;

    public function __construct()
    {
        $this->resp = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->log = new Log;
        $this->conect = new ConnectSqlSrv;
        $this->tools = new Tools;
        $this->NameLog = date('Ymd') . '_auditor.log';
    }
    public function add($data = [])
    {

        $conn = $this->conect->conn(); // Conexión a la base de datos
        try {

            $inicio = microtime(true); // Tiempo de inicio del proceso
            $datos = $this->validarDatos($data); // Valida los datos

            $get_dbdata = $this->get_dbdata();
            $get_dbdata = $get_dbdata ? explode("_", $get_dbdata[0]['BDVersion']) : '';
            $get_dbdata = \intval($get_dbdata[1] ?? 60) ?? ''; // Ver_61_20230622
            $conn->beginTransaction(); // Iniciar transacción

            $sqlMas70 = "INSERT INTO AUDITOR (AudFech, AudHora, AudUser, AudTerm, AudModu, AudTipo, AudDato, FechaHora, AudZonaHoraria) VALUES (:AudFech, :AudHora, :AudUser, :AudTerm, :AudModu, :AudTipo, :AudDato, CONVERT(datetime, :FechaHora, 121), :AudZonaHoraria)";
            $sqlMenos70 = "INSERT INTO AUDITOR (AudFech, AudHora, AudUser, AudTerm, AudModu, AudTipo, AudDato, FechaHora) VALUES (:AudFech, :AudHora, :AudUser, :AudTerm, :AudModu, :AudTipo, :AudDato, CONVERT(datetime, :FechaHora, 121))"; // se omite el campo AudZonaHoraria en la consulta

            $sql = $get_dbdata < 70 ? $sqlMenos70 : $sqlMas70;
            $totalAffectedRows = 0;
            $ms_offset = 0; // Desfase inicial en milisegundos para evitar colisiones de timestamp en SQL Server DATETIME
            $FechaHora = $this->conect->FechaHora();
            $FechaHoraSql = $this->formatSqlDateTime121($FechaHora);
            // $inicio = microtime(true);
            foreach ($datos as $dato) { // Recorro los datos
                $FechaHora = $this->conect->FechaHora();
                usleep(10000); // Pausa 10ms. Reducir colisiones timestamp SQL DATETIME
                $stmt = $conn->prepare($sql); // Preparo la consulta
                $AudUser = substr($dato['AudUser'], 0, 10);
                $AudDato = substr($dato['AudDato'], 0, 100); // Limita la cantidad de caracteres a 100
                // $AudFech = $this->sumar_segundos_a_fecha($dato['AudFech'], $ms_offset);
                // $AudFechSql = $this->formatSqlDateTime121($AudFech);
                $stmt->bindValue(':AudFech', $FechaHora, \PDO::PARAM_STR);
                $stmt->bindValue(':AudHora', $dato['AudHora'], \PDO::PARAM_STR);
                $stmt->bindValue(':AudUser', $AudUser, \PDO::PARAM_STR);
                $stmt->bindValue(':AudTerm', $dato['AudTerm'], \PDO::PARAM_STR);
                $stmt->bindValue(':AudModu', $dato['AudModu'], \PDO::PARAM_INT);
                $stmt->bindValue(':AudTipo', $dato['AudTipo'], \PDO::PARAM_STR);
                $stmt->bindValue(':AudDato', $AudDato, \PDO::PARAM_STR);
                $stmt->bindValue(':FechaHora', $FechaHoraSql, \PDO::PARAM_STR);
                $get_dbdata < 70 ? '' : $stmt->bindValue(':AudZonaHoraria', $dato['AudZonaHoraria'], \PDO::PARAM_STR);
                $ms_offset += 5; // SQL Server DATETIME mínimo paso ~10ms
                $stmt->execute(); // Ejecuta la consulta
                $totalAffectedRows += $stmt->rowCount(); // Devuelve el número de filas afectadas por la última sentencia SQL
            }
            $conn->commit(); // Confirmar la transacción
            // $fin = microtime(true);
            // $tiempo = round($fin - $inicio, 2);
            // error_log("Auditoría de cierres: en $tiempo segundos");

            if (!$data) {
                $this->resp->respuesta([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
            }
        } catch (\PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Auditor::' . __FUNCTION__ . ': ', $this->NameLog, $e);
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Auditor::' . __FUNCTION__ . ': ', $this->NameLog, $e);
        }
    }

    private function validarDatos(array $data)
    {
        $datos = $data ?? $this->getData;
        $request = $this->request;
        $ip = $request->ip;
        $FechaHoraActual = $this->conect->FechaHora(); // Fecha y hora actual
        $FechaHoraActualUTC = $this->conect->FechaHora('UTC'); // Fecha y hora actual en UTC
        $HoraActual = $this->conect->hora(); // Fecha y hora actual en UTC

        if (!is_array($datos)) {
            throw new \Exception("No se recibieron datos", 1);
        }

        $rules = [ // Reglas de validación
            'AudUser' => ['required', 'varchar100'], // Luego se recorta a 10 caracteres en la función add
            'AudTipo' => ['required', 'varchar1'],
            'AudDato' => ['required', 'varchar200'], // Luego se recorta a 100 caracteres en la función add
        ];
        $customValueKey = [ // Valores por defecto
            'AudFech' => $FechaHoraActual, // Fecha actual
            'AudHora' => $HoraActual, // Hora actual
            'AudUser' => 'API', // Usuario de la API
            'AudTerm' => $ip, // IP del cliente
            'AudModu' => '21',
            'AudTipo' => '',
            'AudDato' => '',
            'FechaHora' => $FechaHoraActualUTC, // Fecha y hora actual
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
        return $datosModificados ?? []; // Devuelvo los datos modificados o un array vacío si no hay datos
    }

    public function get_dbdata()
    {
        $sql = "SELECT * FROM DBData";
        $params = [];
        $resultSet = $this->conect->executeQueryWhithParams($sql, $params);
        return $resultSet;
    }
    private function sumar_segundos_a_fecha(string $fecha, int $milisegundos): string
    {
        try {
            $date = new \DateTime($fecha);
            if ($milisegundos > 0) {
                $date->modify("+{$milisegundos} milliseconds");
            }
            // return $date->format('Y-m-d H:i:s.v'); // v = milliseconds (3 dígitos), formato compatible con SQL Server DATETIME
            return substr($date->format('Y-m-d\TH:i:s.u'), 0, 23);
        } catch (\Exception $e) {
            return $fecha;
        }
    }

    private function formatSqlDateTime121(string $fecha): string
    {
        try {
            $date = new \DateTimeImmutable($fecha);
            return $date->format('Y-m-d H:i:s.v');
        } catch (\Exception $e) {
            $normalizada = str_replace('T', ' ', $fecha);
            return \strlen($normalizada) > 23 ? substr($normalizada, 0, 23) : $normalizada;
        }
    }
}
