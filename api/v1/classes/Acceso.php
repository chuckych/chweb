<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\InputValidator;
use Flight;

class Acceso
{
    private $request;
    private $getData;
    private $conect;
    private $log;
    private $resp;
    private $query;

    public function __construct()
    {
        $this->resp = new Response();
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->query = $this->request->query->getData();
        $this->log = new Log();
        $this->conect = new ConnectSqlSrv();
    }

    public function relojes_habilitados(): void
    {
        $inputs = $this->inputs_relojes_habilitados();
        $inicio = microtime(true);
        $conn = $this->conect->conn();

        $relgrup = $inputs['relgrup'] ?? [];

        // Construir query SQL
        $sql = "SELECT RELOHABI.RelGrup, GRUPCAPT.GHaDesc, RELOHABI.RelReMa, RELOJES.RelRelo, RELOJES.RelSeri, RELOJES.RelDeRe 
        FROM RELOHABI, RELOJES, GRUPCAPT
        WHERE RELOHABI.RelGrup > 0";

        // Agregar filtro IN si hay grupos especificados
        if (!empty($relgrup)) {
            $placeholders = [];
            foreach ($relgrup as $index => $value) {
                $placeholders[] = ":relgrup{$index}";
            }
            $sql .= " AND RELOHABI.RelGrup IN (" . implode(',', $placeholders) . ")";
        }

        $sql .= " AND RELOHABI.RelGrup = GRUPCAPT.GHaCodi 
        AND RELOHABI.RelReMa = RELOJES.RelReMa 
        AND RELOHABI.RelRelo = RELOJES.RelRelo
        ORDER BY RELOHABI.RelGrup, RELOHABI.RelReMa, RELOHABI.RelRelo";

        // Preparar y vincular parámetros
        $stmt = $conn->prepare($sql);
        if (!empty($relgrup)) {
            foreach ($relgrup as $index => $value) {
                $stmt->bindValue(":relgrup{$index}", $value, \PDO::PARAM_INT);
            }
        }

        // Ejecutar query
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Procesar resultados
        $data = $this->procesarDatosRelojes($data);

        $totalAffectedRows = $stmt->rowCount();
        $this->resp->respuesta($data, $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
    }

    private function procesarDatosRelojes(array $data): array
    {
        $mapRelReMa = [
            '0' => 'ASCII',
            '1' => 'Macronet',
            '10' => 'Hand Reader',
            '21' => 'SB CAuto',
            '30' => 'ZKTeco',
            '41' => 'Suprema',
            '50' => 'HikVision'
        ];

        foreach ($data as &$item) {
            // Agregar descripción de marca
            $marcaKey = trim($item['RelReMa']);
            if (isset($mapRelReMa[$marcaKey])) {
                $item['RelReMaStr'] = $mapRelReMa[$marcaKey];
            }

            // Trim de todos los valores string
            foreach ($item as $key => $value) {
                if (is_string($value)) {
                    $item[$key] = trim($value);
                }
            }
        }

        return $data;
    }
    private function inputs_relojes_habilitados()
    {
        $datos = $this->query;

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'relgrup' => ['arrSmallint']
            ];

            $customValueKey = [ // Valores por defecto
                'relgrup' => []
            ];

            // Asignar valores por defecto si no existen o están vacíos
            foreach ($customValueKey as $key => $defaultValue) {
                if (!array_key_exists($key, $datos) || empty($datos[$key])) {
                    $datos[$key] = $defaultValue;
                }
            }

            // Validar los datos
            $validator = new InputValidator($datos, $rules);
            $validator->validate();

            return $datos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_inputs_relojes_habilitados.log');
        }
    }
    public function personal_relojes()
    {
        $inputs = $this->inputs_perrelo();
        $inicio = microtime(true);
        $conn = $this->conect->conn();
        $legajo = $inputs['legajo'] ?? [];

        // Construir query SQL
        $sql = "SELECT PERRELO.RelLega ,PERRELO.RelFech, PERRELO.RelFech2, PERRELO.RelReMa ,PERRELO.RelRelo ,RELOJES.RelDeRe, RELOJES.RelSeri 
        FROM PERRELO,RELOJES 
        WHERE PERRELO.RelLega > 0";

        // Agregar filtro IN si hay legajos especificados
        if (!empty($legajo)) {
            $placeholders = [];
            foreach ($legajo as $index => $value) {
                $placeholders[] = ":legajo{$index}";
            }
            $sql .= " AND PERRELO.RelLega IN (" . implode(',', $placeholders) . ")";
        }
        $sql .= " AND PERRELO.RelReMa = RELOJES.RelReMa 
        AND PERRELO.RelRelo = RELOJES.RelRelo 
        ORDER BY PERRELO.RelLega,PERRELO.RelReMa,PERRELO.RelRelo";

        // Preparar y vincular parámetros
        $stmt = $conn->prepare($sql);
        if (!empty($legajo)) {
            foreach ($legajo as $index => $value) {
                $stmt->bindValue(":legajo{$index}", $value, \PDO::PARAM_INT);
            }
        }
        // Ejecutar query
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Procesar resultados
        $data = $this->procesarDatosPersonalRelojes($data);
        $totalAffectedRows = $stmt->rowCount();
        $this->resp->respuesta($data, $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
    }
    private function procesarDatosPersonalRelojes(array $data): array
    {
        $mapRelReMa = [
            '0' => 'ASCII',
            '1' => 'Macronet',
            '10' => 'Hand Reader',
            '21' => 'SB CAuto',
            '30' => 'ZKTeco',
            '41' => 'Suprema',
            '50' => 'HikVision',
            '9999' => ''
        ];

        foreach ($data as &$item) {
            // Agregar descripción de marca
            $marcaKey = trim($item['RelReMa']);
            if (isset($mapRelReMa[$marcaKey])) {
                $item['RelReMaStr'] = $mapRelReMa[$marcaKey];
            }

            // Trim de todos los valores string
            foreach ($item as $key => $value) {
                if (is_string($value)) {
                    $item[$key] = trim($value);
                }
            }

            // RelFech RelFech2 en formato d-m-Y 
            $item['RelFechStr'] = date('d-m-Y', strtotime($item['RelFech']));
            $item['RelFech2Str'] = date('d-m-Y', strtotime($item['RelFech2']));
            $item['RelReMaStr'] = $mapRelReMa[$item['RelReMa']] ?? '';
        }

        return $data;
    }
    private function inputs_perrelo()
    {
        $datos = $this->query;

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'legajo' => ['arrInt']
            ];

            $customValueKey = [ // Valores por defecto
                'legajo' => []
            ];

            // Asignar valores por defecto si no existen o están vacíos
            foreach ($customValueKey as $key => $defaultValue) {
                if (!array_key_exists($key, $datos) || empty($datos[$key])) {
                    $datos[$key] = $defaultValue;
                }
            }

            // Validar los datos
            $validator = new InputValidator($datos, $rules);
            $validator->validate();

            return $datos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_inputs_perrelo.log');
        }
    }
    public function identifica()
    {
        $inputs = $this->inputs_identifica();
        $inicio = microtime(true);
        $conn = $this->conect->conn();
        $legajo = $inputs['legajo'] ?? [];

        $sql = "SELECT * 
        FROM IDENTIFICA WHERE IDLegajo > 0";

        // Agregar filtro IN si hay legajos especificados
        if (!empty($legajo)) {
            $placeholders = [];
            foreach ($legajo as $index => $value) {
                $placeholders[] = ":legajo{$index}";
            }
            $sql .= " AND IDENTIFICA.IDLegajo IN (" . implode(',', $placeholders) . ")";
        }

        $sql .= " ORDER BY FechaHora DESC";

        // Preparar y vincular parámetros
        $stmt = $conn->prepare($sql);
        if (!empty($legajo)) {
            foreach ($legajo as $index => $value) {
                $stmt->bindValue(":legajo{$index}", $value, \PDO::PARAM_INT);
            }
        }

        // Ejecutar query
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // \error_log(print_r($data, true));

        // Procesar resultados
        $data = $this->procesar_datos_identifica($data);
        $totalAffectedRows = $stmt->rowCount();
        $this->resp->respuesta($data, $totalAffectedRows, 'OK', 200, $inicio, 0, 0);

    }
    private function inputs_identifica()
    {
        $datos = $this->query;

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'legajo' => ['arrInt']
            ];

            $customValueKey = [ // Valores por defecto
                'legajo' => []
            ];

            // Asignar valores por defecto si no existen o están vacíos
            foreach ($customValueKey as $key => $defaultValue) {
                if (!array_key_exists($key, $datos) || empty($datos[$key])) {
                    $datos[$key] = $defaultValue;
                }
            }

            // Validar los datos
            $validator = new InputValidator($datos, $rules);
            $validator->validate();

            return $datos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_inputs_perrelo.log');
        }
    }
    private function procesar_datos_identifica(array $data): array
    {
        foreach ($data as &$item) {
           // Trim de todos los valores string
            foreach ($item as $key => $value) {
                if (is_string($value)) {
                    $item[$key] = trim($value);
                }
            }

            foreach (['IDVence', 'FechaHora', 'IDSupStart', 'IDSupExpiry'] as $value) {
                $item["{$value}Str"] = date('d-m-Y', strtotime($item[$value]));
            }

        }

        return $data;
    }
}
