<?php

namespace Classes;

use Classes\InputValidator;
use Classes\Tools;

use Flight;

class Params
{
    private $conect;
    private $response;
    private $getData;
    private $getQuery;
    private $request;
    private $tools;


    public function __construct()
    {
        $this->conect = new ConnectDB;
        $this->response = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->getQuery = $this->request->query->getData();
        $this->tools = new Tools;
    }
    public function get()
    {
        try {
            $conn = $this->conect->conn();
            $inicio = microtime(true); // Inicio del script

            $query = $this->filter_params();

            $sql = "SELECT * FROM params WHERE cliente = {$query['cliente']}";
            if ($query['modulo'] != '') {
                $sql .= " AND modulo = {$query['modulo']}";
            }
            if ($query['descripcion'] != '') {
                // $sql .= " AND descripcion LIKE '%{$query['descripcion']}%'";
                $sql .= " AND descripcion = '{$query['descripcion']}'";
            }
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt = null;
            $conn = null;
            $count = count($result);

            // $this->response->respuesta($this->filter_params(), $count, 'OK', 200, 0, $count, $inicio);
            $this->response->respuesta($result, $count, 'OK', 200, 0, $count, $inicio);
        } catch (\PDOException $e) {
            $get_class = get_class($e);
            throw new $get_class($e->getMessage(), (int) $e->getCode());
        }
    }
    private function filter_params(): array
    {
        $datos = $this->getQuery;
        file_put_contents(PATH_LOG . '/datos_params.log', $datos);
        $datosModificados = [];
        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 204);
            }

            $rules = [ // Reglas de validación
                'cliente' => ['required', 'tinyint'],
                'modulo' => ['tinyint'],
                'descripcion' => ['varchar50'],
            ];
            $customValueKey = [ // Valores por defecto
                'cliente' => '',
                'modulo' => '',
                'descripcion' => '',
            ];

            $keyData = array_keys($customValueKey); // Obtengo las claves del array $customValueKey
            $dato = $datos;
            foreach ($keyData as $keyD) { // Recorro las claves del array $customValueKey
                if (!array_key_exists($keyD, $dato) || empty($dato[$keyD])) { // Si no existe la clave en el array $dato o esta vacío
                    $dato[$keyD] = $customValueKey[$keyD]; // Le asigno el valor por defecto del array $customValueKey
                }
            }
            $datosModificados = $dato; // Guardo los datos modificados en un array
            $validator = new InputValidator($dato, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos
            return $datosModificados;
        } catch (\Exception $e) {
            $code = $e->getCode();
            $nameInstance = get_class($e);
            file_put_contents(PATH_LOG . '/validar_alta_cuenta.log', $e->getMessage());
            throw new $nameInstance($e->getMessage(), $code);
        }
    }
    private function alta_params_multiple(): array
    {
        $datos = $this->getData;

        try {

            if ($this->tools->jsonNoValido()) {
                $errores = $this->tools->jsonNoValido();
                throw new \Exception($errores, 400);
            }

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 400);
            }

            $rules = [ // Reglas de validación
                'cliente' => ['required', 'tinyint'],
                'modulo' => ['required', 'tinyint'],
                'descripcion' => ['varchar50'],
                'valores' => ['varcharMax']
            ];
            $customValueKey = [ // Valores por defecto
                'cliente' => '',
                'modulo' => '',
                'descripcion' => '',
                'valores' => ''
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
            $code = $e->getCode();
            $nameInstance = get_class($e);
            file_put_contents(PATH_LOG . '/validar_alta_cuenta.log', $e->getMessage());
            throw new $nameInstance($e->getMessage(), $code);
        }
    }
    public function alta($conn, $query)
    {
        try {

            $checkExist = "SELECT * FROM params WHERE cliente = {$query['cliente']} AND modulo = {$query['modulo']} AND descripcion = '{$query['descripcion']}'";
            $stmt = $conn->prepare($checkExist);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $stmt = null;

            if (count($result) > 0) {
                return $this->update($conn, $query);
            } else {
                return $this->insert($conn, $query);
            }

        } catch (\PDOException $e) {
            $get_class = get_class($e);
            throw new $get_class($e->getMessage(), (int) $e->getCode());
        }
    }
    private function update($conn, $query): void
    {
        try {
            $sql = "UPDATE params SET valores = :valores WHERE cliente = :cliente AND modulo = :modulo AND descripcion = :descripcion";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cliente', $query['cliente'], \PDO::PARAM_INT);
            $stmt->bindParam(':modulo', $query['modulo'], \PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $query['descripcion'], \PDO::PARAM_STR);
            $stmt->bindParam(':valores', $query['valores'], \PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
        } catch (\PDOException $e) {
            $get_class = get_class($e);
            throw new $get_class($e->getMessage(), (int) $e->getCode());
        }
    }
    private function insert($conn, $query): void
    {
        try {
            $sql = "INSERT INTO params (cliente, modulo, descripcion, valores) VALUES (:cliente, :modulo, :descripcion, :valores)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cliente', $query['cliente'], \PDO::PARAM_INT);
            $stmt->bindParam(':modulo', $query['modulo'], \PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $query['descripcion'], \PDO::PARAM_STR);
            $stmt->bindParam(':valores', $query['valores'], \PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
        } catch (\PDOException $e) {
            $get_class = get_class($e);
            throw new $get_class($e->getMessage(), (int) $e->getCode());
        }
    }
    public function delete(): void
    {
        try {
            $conn = $this->conect->conn();
            $inicio = microtime(true); // Inicio del script

            $query = $this->filter_params();

            $sql = "DELETE FROM params WHERE cliente = :cliente AND modulo = :modulo AND descripcion = :descripcion";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cliente', $query['cliente'], \PDO::PARAM_INT);
            $stmt->bindParam(':modulo', $query['modulo'], \PDO::PARAM_INT);
            $stmt->bindParam(':descripcion', $query['descripcion'], \PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
            $conn = null;

            $this->response->respuesta($query, 1, 'OK', 200, 0, 1, $inicio);
        } catch (\PDOException $e) {
            $get_class = get_class($e);
            throw new $get_class($e->getMessage(), (int) $e->getCode());
        }
    }
    public function alta_multiple(): void
    {
        try {
            $conn = $this->conect->conn();
            $inicio = microtime(true); // Inicio del script

            $datos = $this->alta_params_multiple();

            foreach ($datos as $query) {
                $this->alta($conn, $query);
            }

            $conn = null;

            $this->response->respuesta($datos, count($query), 'OK', 200, 0, count($datos), $inicio);

        } catch (\PDOException $e) {
            $get_class = get_class($e);
            throw new $get_class($e->getMessage(), (int) $e->getCode());
        }
    }
}
