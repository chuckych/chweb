<?php

namespace Classes;

use Classes\InputValidator;
use Classes\Tools;

use Flight;

class Usuarios
{
    private $conect;
    private $response;
    private $getData;
    private $request;
    private $tools;

    public function __construct()
    {
        $this->conect = new ConnectDB;
        $this->response = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->tools = new Tools;
    }
    public function get_usuarios()
    {
        $inicio = microtime(true);
        try {
            $conn = $this->conect->conn();
            $query = "SELECT usuarios.*, roles.nombre as 'nombre_rol' FROM usuarios INNER JOIN roles ON usuarios.rol=roles.id ORDER BY usuarios.id DESC;";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $return = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];
            foreach ($return as $key => $value) {
                unset($return[$key]['clave']);
            }
            // $return = \array_column($return, null, 'usuario');
            $this->response->respuesta($return, 1, "Usuarios obtenidos correctamente", 200, $inicio, count($return), count($return));
        } catch (\Exception $th) {
            // \error_log(print_r('get_usuarios: '.$th->getMessage(), true));
            $this->response->respuesta([], 0, $th->getMessage(), $th->getCode() ?: 400, $inicio, 0, 0);
        }
    }
    public function alta_usuario()
    {
        $datos = $this->validar_datos_user_ad();
        $inicio = microtime(true);
        try {
            $conn = $this->conect->conn();
            $recid_c = $datos['recid_c'] ?? '';
            $dataCliente = $this->obtener_datos_cliente($recid_c, $conn);
            // \error_log(print_r($dataCliente, true));
            // si no hay datos del cliente
            if (empty($dataCliente)) {
                throw new \Exception("El cliente no existe", 400);
            }
            $ident = $dataCliente['ident'] ?? '';
            $recid_user = $this->tools->recid();
            $nombre = $datos['nombre'] ?? '';
            $usuario = $datos['usuario'] ?? '';
            $rol = $datos['rol'] ?? '';
            $clave = $datos['clave'] ?? '';
            $cliente = $dataCliente['id'] ?? '';
            $legajo = $datos['legajo'] ?? 0;
            $user_ad = $datos['user_ad'] ?? '0';

            if ($this->si_existe_usuario($usuario, $conn)) {
                throw new \Exception("El usuario {$usuario} ya existe", 400);
            }

            if (!$this->si_existe_rol($rol, $conn)) {
                throw new \Exception("El rol {$rol} no existe", 400);
            }

            $strClave = !empty($clave) ? $clave : $usuario;
            $clave = password_hash($strClave, PASSWORD_DEFAULT);
            
            if($user_ad === '1') {
                $clave = password_hash($recid_user, PASSWORD_DEFAULT); // Si es usuario AD se establece una clave por defecto aleatoria
            }

            $query = "INSERT INTO usuarios (recid, nombre, usuario, rol, clave, estado, principal, cliente, legajo, user_ad, fecha_alta, fecha ) VALUES (:recid, :nombre, :usuario, :rol, :clave, '0', '0', :cliente, :legajo, :user_ad, NOW(), NOW());";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':recid', $recid_user, \PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $nombre, \PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $usuario, \PDO::PARAM_STR);
            $stmt->bindParam(':rol', $rol, \PDO::PARAM_INT);
            $stmt->bindParam(':clave', $clave, \PDO::PARAM_STR);
            $stmt->bindParam(':cliente', $cliente, \PDO::PARAM_INT);
            $stmt->bindParam(':legajo', $legajo, \PDO::PARAM_INT);
            $stmt->bindParam(':user_ad', $user_ad, \PDO::PARAM_STR);
            $stmt->execute();
            $AffectedRows = $stmt->rowCount();
            $stmt = null;

            if ($AffectedRows === 0) {
                throw new \Exception("No se pudo agregar el usuario", 400);
            }

            $dataUsuario = $this->obtener_datos_usuario($recid_user, $conn);
            unset($dataUsuario['clave']);
            $this->response->respuesta($dataUsuario, 1, "Usuario agregado correctamente", 200, $inicio, 1, 1);

        } catch (\Exception $th) {
            // \error_log(print_r('alta_usuario: '.$th->getMessage(), true));
            $this->response->respuesta([], 0, $th->getMessage(), $th->getCode() ?: 400, $inicio, 0, 0);
        }

    }
    private function validar_datos_user_ad()
    {
        $datos = $this->getData;
        $datosModificados = [];
        try {

            if ($this->tools->jsonNoValido()) {
                $error = 'Json no valido: ' . $this->tools->jsonNoValido();
                throw new \Exception($error, 400);
            }

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 204);
            }

            $rules = [ // Reglas de validación
                'recid_c' => ['required', 'varchar8'],
                'nombre' => ['required', 'varchar100'],
                'usuario' => ['required', 'varchar50'],
                'rol' => ['required', 'numeric5'],
                'clave' => ['varchar255'],
                'legajo' => ['numeric10'],
                'user_ad' => ['allowed01'],
            ];
            $customValueKey = [ // Valores por defecto
                'recid_c' => '',
                'nombre' => '',
                'usuario' => '',
                'rol' => '',
                'clave' => '',
                'legajo' => 0,
                'user_ad' => '0',
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
            throw new $nameInstance($e->getMessage(), $code);
        }
    }
    private function obtener_datos_cliente($recid_c, $conn)
    {
        try {
            $inicio = microtime(true);
            if (!$recid_c) {
                throw new \Exception("No se recibieron datos: recid_c", 204);
            }
            $query = "SELECT * FROM clientes WHERE recid=:recid_c LIMIT 1;";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':recid_c', $recid_c, \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC)[0] ?? [];
        } catch (\Exception $e) {
            $this->response->respuesta([], 0, $e->getMessage(), $e->getCode() ?: 400, $inicio, 0, 0);
        }
    }
    private function si_existe_usuario($usuario, $conn)
    {
        try {
            $inicio = microtime(true);
            if (!$usuario) {
                throw new \Exception("No se recibieron datos: usuario", 204);
            }
            $query = "SELECT COUNT(*) as 'cantidad' FROM usuarios WHERE usuario=:usuario LIMIT 1;";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':usuario', $usuario, \PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['cantidad'] > 0 ? true : false;
        } catch (\Exception $e) {
            $this->response->respuesta([], 0, $e->getMessage(), $e->getCode() ?: 400, $inicio, 0, 0);
        }

    }
    private function si_existe_rol($rol, $conn)
    {
        try {
            $inicio = microtime(true);
            if (!$rol) {
                throw new \Exception("No se recibieron datos: rol", 204);
            }
            $query = "SELECT COUNT(*) as 'cantidad' FROM roles WHERE id=:rol LIMIT 1;";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':rol', $rol, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['cantidad'] > 0 ? true : false;
        } catch (\Exception $e) {
            $this->response->respuesta([], 0, $e->getMessage(), $e->getCode() ?: 400, $inicio, 0, 0);
        }
    }
    private function obtener_datos_usuario($recid_u, $conn)
    {
        try {
            $inicio = microtime(true);
            if (!$recid_u) {
                throw new \Exception("No se recibieron datos: recid_u", 204);
            }
            $query = "SELECT * FROM usuarios INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.recid=:recid_u LIMIT 1;";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':recid_u', $recid_u, \PDO::PARAM_STR);
            $stmt->execute();
            $return = $stmt->fetchAll(\PDO::FETCH_ASSOC)[0] ?? [];
            return $return;
        } catch (\Exception $e) {
            $this->response->respuesta([], 0, $e->getMessage(), $e->getCode() ?: 400, $inicio, 0, 0);
        }
    }
}