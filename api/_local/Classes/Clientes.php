<?php

namespace Classes;

use Classes\InputValidator;
use Classes\Tools;

use Flight;

class Clientes
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
    public function get_clientes()
    {
        try {
            $conn = $this->conect->conn();
            $inicio = microtime(true); // Inicio del script

            $sql = "SELECT * FROM clientes";
            $sql .= " LEFT JOIN params ON clientes.id = params.cliente AND params.modulo = 1 AND params.descripcion = 'host'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $clientes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $count_usuarios = $this->count_usuarios_clientes($conn);
            $count_roles = $this->count_usuarios_rol($conn);
            $stmt = null;
            $conn = null;
            $count = count($clientes);

            foreach ($clientes as $key => $value) {
                if (intval($value['id'])) {
                    $clientes[$key]['count_usuarios'] = $count_usuarios[$value['id']] ?? 0;
                    $clientes[$key]['count_roles'] = $count_roles[$value['id']] ?? 0;
                }
            }
            // cambiar la key clientes.valores por host
            foreach ($clientes as $key => $value) {
                $clientes[$key]['hostLocal'] = $value['valores'];
                unset($clientes[$key]['valores']);
                unset($clientes[$key]['modulo']);
                unset($clientes[$key]['descripcion']);
                unset($clientes[$key]['cliente']);
            }

            $this->response->respuesta($clientes, $count, 'OK', 200, 0, $count, $inicio);
        } catch (\PDOException $e) {
            $get_class = get_class($e);
            throw new $get_class($e->getMessage(), (int) $e->getCode());
        }
    }
    private function count_usuarios_clientes($conn)
    {
        $sql = "SELECT COUNT(*) as 'count', cliente FROM usuarios group by cliente";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = null;
        $usuarios = array_column($usuarios, 'count', 'cliente');
        return $usuarios;
    }
    private function count_usuarios_rol($conn)
    {
        $sql = "SELECT COUNT(*) as 'count', cliente FROM roles group by cliente";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = null;
        $usuarios = array_column($usuarios, 'count', 'cliente');
        return $usuarios;
    }
    private function validar_alta_cuenta()
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
                'Nombre' => ['required', 'varchar50'],
                'Ident' => ['varchar3'],
                'Host' => ['required', 'varchar50'],
                'DBHost' => ['varchar100'],
                'DBName' => ['varchar50'],
                'DBUser' => ['varchar50'],
                'DBPass' => ['varchar50'],
                'DBAuth' => ['allowed01'],
                'AppCode' => ['varchar8'],
                'WebService' => ['varchar100'],
                'ApiMobile' => ['varchar100'],
                'ApiMobileApp' => ['varchar100'],
                'LocalCH' => ['allowed01'],
            ];
            $customValueKey = [ // Valores por defecto
                'Nombre' => '',
                'Ident' => '',
                'Host' => '',
                'DBHost' => '',
                'DBName' => '',
                'DBUser' => '',
                'DBPass' => '',
                'DBAuth' => '0',
                'AppCode' => '',
                'WebService' => '',
                'ApiMobile' => '',
                'ApiMobileApp' => '',
                "LocalCH" => '0',
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
    public function alta_cliente()
    {
        $datos = $this->validar_alta_cuenta();
        $conn = $this->conect->conn();
        $inicio = microtime(true);

        $Nombre = $datos['Nombre']; // Nombre de la cuenta
        $Ident = $datos['Ident']; // Identificador de la cuenta
        $Host = $datos['Host']; // Host de la cuenta eje: https://chweb.local
        $DBHost = $datos['DBHost']; // Host de la base de datos
        $DBName = $datos['DBName']; // Nombre de la base de datos
        $DBUser = $datos['DBUser']; // Usuario de la base de datos
        $DBPass = $datos['DBPass']; // Contraseña de la base de datos
        $DBAuth = $datos['DBAuth']; // Autenticación de la base de datos
        $LocalCH = $datos['LocalCH']; // Local de CH eje: 0 o 1. Esto es para saber si la cuenta es local o no. En función de esto se insertan fichadas mobile en la base datos SQL Server de la tabla FICHADAS al momento de descargar fichadas mobile.
        $n_ident = str_replace(" ", "", $datos['Nombre']); // Nombre de la cuenta sin espacios
        $Ident = empty($Ident) ? substr(strtoupper($n_ident), 0, 3) : $Ident; // Identificador de la cuenta si no se envía se toma las primeras 3 letras del nombre
        $WebService = $datos['WebService']; // WebService de la cuenta
        $ApiMobile = $datos['ApiMobile']; // Api Mobile de la cuenta. Eje: https://cloudhr.ar Esto es donde se aloja la API de mobile. Si la cuenta usa la app de mobile siempre va https://cloudhr.ar.
        $ApiMobileApp = $datos['ApiMobileApp']; // Api Mobile App de la cuenta. Eje: http://awsapi.chweb.ar:7575 Esto es donde se aloja la API de mobile de la app en aws. Si la cuenta usa la app de mobile siempre va http://awsapi.chweb.ar:7575.
        $Recid = (!$datos['AppCode']) ? $this->tools->recid() : $datos['AppCode']; // Recid de la cuenta
        if ($this->si_existe_nombre_cliente($Nombre, $conn)) {
            throw new \ValueError("Ya existe una cuenta con el nombre: {$Nombre}", 400);
        }

        if ($this->si_existe_ident($Ident, $conn)) {
            $Ident = $this->tools->random_ident();
        }

        if ($this->si_existe_recid($Recid, $conn)) {
            $Recid = $this->tools->recid();
        }

        $TokenMobileHRP = sha1($Recid);

        $datos['TokenMobileHRP'] = $TokenMobileHRP;
        $datos['Recid'] = $Recid;
        $datos['Ident'] = $Ident;

        if ($this->insert_cliente($datos, $conn)) {
            $this->response->respuesta([], 1, 'OK', 200, $inicio, 1, 0);
        }
    }
    private function si_existe_ident($ident, $conn)
    {
        if (!$ident) {
            return false;
        }

        $sql = "SELECT ident FROM clientes WHERE ident = :ident";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':ident', $ident, \PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount() ?? 0;
        $stmt = null;
        $conn = null;
        return ($count > 0) ? true : false;
    }
    private function si_existe_nombre_cliente($nombre, $conn, $IDCliente = null)
    {
        $sql = "SELECT nombre FROM clientes WHERE nombre = :nombre";
        $sql .= $IDCliente ? " AND id != :id" : '';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, \PDO::PARAM_STR);
        if ($IDCliente) {
            $stmt->bindParam(':id', $IDCliente, \PDO::PARAM_INT);
        }
        $stmt->execute();
        $count = $stmt->rowCount() ?? 0;
        return $count ? true : false;
    }
    private function si_no_existe_id_cliente($IDCliente, $conn)
    {
        $sql = "SELECT id FROM clientes WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $IDCliente, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount() ?? 0;
        return $count ? false : true;
    }
    private function si_existe_recid($recid, $conn)
    {
        $sql = "SELECT recid FROM clientes WHERE recid = :recid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':recid', $recid, \PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount() ?? 0;
        $stmt = null;
        $conn = null;
        return ($count > 0) ? true : false;
    }
    private function insert_cliente($data, $conn)
    {
        $params = [];
        $params[':Recid'] = $data['Recid'];
        $params[':Ident'] = $data['Ident'];
        $params[':Nombre'] = $data['Nombre'];
        $params[':DBHost'] = $data['DBHost'];
        $params[':DBName'] = $data['DBName'];
        $params[':DBUser'] = $data['DBUser'];
        $params[':DBPass'] = $data['DBPass'];
        $params[':DBAuth'] = $data['DBAuth'];
        $params[':WebService'] = $data['WebService'];
        $params[':ApiMobileHRP'] = $data['ApiMobile'];
        $params[':Fecha'] = $this->tools->formatDateTime('now');
        $params[':LocalCH'] = $data['LocalCH'];
        $params[':ApiMobileHRPApp'] = $data['ApiMobileApp'];
        $params[':tkmobile'] = '';

        $sql = "INSERT INTO clientes (recid, ident, nombre, host, db, user, pass, auth, WebService, ApiMobileHRP, fecha_alta, localCH, tkmobile, UrlAppMobile, fecha ) VALUES(:Recid, :Ident, :Nombre, :DBHost, :DBName, :DBUser, :DBPass, :DBAuth, :WebService, :ApiMobileHRP, :Fecha, :LocalCH, :tkmobile, :ApiMobileHRPApp, :Fecha)";

        try {
            $stmt = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            }
            $stmt->execute();
            $AffectedRows = $stmt->rowCount();
            $stmt = null;

            if ($AffectedRows > 0) {
                $get_cliente_recid = $this->get_cliente_recid($data['Recid'], $conn);
                $id = $get_cliente_recid['id'];
                $Host = $data['Host'];
                $set_params_host = $this->set_params_host($Host, $id, $conn);
            }

            return $set_params_host;
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), $th->getCode());
        }
    }
    private function get_cliente_recid($recid, $conn)
    {
        $sql = "SELECT id, nombre FROM clientes WHERE recid = :recid";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':recid', $recid, \PDO::PARAM_STR);
        $stmt->execute();
        $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt = null;
        return $cliente;
    }
    private function set_params_host($host, $id, $conn)
    {
        $sqlI = "INSERT INTO `params` (`descripcion`, `modulo`, `cliente`, `valores`) VALUES ('host', 1, :id, :host)";
        $sqlU = "UPDATE `params` SET `valores` = :host WHERE `descripcion` = 'host' and `modulo` = 1 and `cliente` = :id";
        $sql = $this->si_existe_params_host($id, $conn) ? $sqlU : $sqlI;

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':host', $host, \PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $affectedRows = $stmt->rowCount();
        $stmt = null;
        $this->write_apiKeysFile($conn);
        return $affectedRows > 0 ? true : false;
    }
    private function si_existe_params_host($id, $conn)
    {
        $sql = "SELECT * FROM `params` where `descripcion` = 'host' and `modulo` = 1 and `cliente` = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount() ?? 0;
        $stmt = null;
        return ($count > 0) ? true : false;
    }
    private function write_apiKeysFile($conn)
    {

        $path = PATH_APIKEY;

        if (!file_exists($path)) {
            throw new \Exception("El archivo api_key no existe: " . PATH_APIKEY, 500);
        }

        $q = "SELECT `c`.`host` AS 'hostDB', `c`.`user` AS 'userDB',`c`.`pass` AS 'passDB', `c`.`db` AS 'DB', `c`.`auth` AS 'authDB', `c`.`id` as 'idCompany', `c`.`nombre` as 'nameCompany', `c`.`recid` as 'recidCompany', 'key' as 'key', `c`.`urlAppMobile` AS 'urlAppMobile', `c`.`localCH` as 'localCH', (SELECT `valores` FROM `params` `p` WHERE `p`.`modulo` = 1 AND `p`.`descripcion` = 'host' AND `p`.`cliente` = `c`.`id` LIMIT 1) AS 'hostCHWeb', `c`.`WebService` AS 'WebService', `c`.`ApiMobileHRP` AS 'apiMobileHRP' FROM `clientes` `c`";
        $stmt = $conn->prepare($q);
        $stmt->execute();
        $assoc_arr = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($assoc_arr as $key => $value) {
            $assoc[] = [
                'idCompany' => $value['idCompany'],
                'nameCompany' => $value['nameCompany'],
                'recidCompany' => $value['recidCompany'],
                'urlAppMobile' => $value['urlAppMobile'],
                'apiMobileHRP' => $value['apiMobileHRP'],
                'localCH' => ($value['localCH'] == '') ? "0" : $value['localCH'],
                'hostCHWeb' => $value['hostCHWeb'],
                'homeHost' => HOMEHOST,
                'DBHost' => $value['hostDB'],
                'DBUser' => $value['userDB'],
                'DBPass' => $value['passDB'],
                'DBName' => $value['DB'],
                'DBAuth' => $value['authDB'],
                'Token' => sha1($value['recidCompany']),
                'WebServiceCH' => ($value['WebService']),
            ];
        }
        $content = "; <?php exit; ?> <-- ¡No eliminar esta línea! --> \n";
        foreach ($assoc as $key => $elem) {
            $content .= "[" . $key . "]\n";
            foreach ($elem as $key2 => $elem2) {
                if (is_array($elem2)) {
                    for ($i = 0; $i < count($elem2); $i++) {
                        $content .= $key2 . "[] =\"" . $elem2[$i] . "\"\n";
                    }
                } else if ($elem2 == "")
                    $content .= $key2 . " =\n";
                else
                    $content .= $key2 . " = \"" . $elem2 . "\"\n";
            }
        }
        if (!$handle = fopen($path, 'w')) {
            return false;
        }
        $success = fwrite($handle, $content);
        fclose($handle);
        return $success;
    }

    public function edita_cliente($IDCliente)
    {
        $datos = $this->validar_alta_cuenta();
        $conn = $this->conect->conn();
        $inicio = microtime(true);
        $Nombre = $datos['Nombre']; // Nombre de la cuenta

        if ($this->si_existe_nombre_cliente($Nombre, $conn, $IDCliente)) {
            throw new \ValueError("Ya existe una cuenta con el nombre: {$Nombre}", 400);
        }
        if ($this->si_no_existe_id_cliente($IDCliente, $conn)) {
            throw new \ValueError("No existe una cuenta con el id: {$IDCliente}", 400);
        }

        if ($this->update_cliente($datos, $conn, $IDCliente)) {
            $this->response->respuesta([], 1, 'OK', 200, $inicio, 1, 0);
        }
    }
    private function update_cliente($datos, $conn, $IDCliente)
    {

        $sql = "UPDATE clientes SET nombre = :Nombre, host = :Host, db = :DB, user = :DBUser, pass = :DBPass, auth = :DBAuth, WebService = :WebService, ApiMobileHRP = :ApiMobileHRP, urlAppMobile = :urlAppMobile, localCH = :LocalCH, fecha = :Fecha WHERE id = :id";
        try {
            $fecha = $this->tools->formatDateTime('now');
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':Nombre', $datos['Nombre'], \PDO::PARAM_STR);
            $stmt->bindParam(':Host', $datos['DBHost'], \PDO::PARAM_STR);
            $stmt->bindParam(':DB', $datos['DBName'], \PDO::PARAM_STR);
            $stmt->bindParam(':DBUser', $datos['DBUser'], \PDO::PARAM_STR);
            $stmt->bindParam(':DBPass', $datos['DBPass'], \PDO::PARAM_STR);
            $stmt->bindParam(':DBAuth', $datos['DBAuth'], \PDO::PARAM_STR);
            $stmt->bindParam(':WebService', $datos['WebService'], \PDO::PARAM_STR);
            $stmt->bindParam(':ApiMobileHRP', $datos['ApiMobile'], \PDO::PARAM_STR);
            $stmt->bindParam(':urlAppMobile', $datos['ApiMobileApp'], \PDO::PARAM_STR);
            $stmt->bindParam(':LocalCH', $datos['LocalCH'], \PDO::PARAM_STR);
            $stmt->bindParam(':Fecha', $fecha, \PDO::PARAM_STR);
            $stmt->bindParam(':id', $IDCliente, \PDO::PARAM_INT);
            $stmt->execute();
            $stmt = null;

            $this->set_params_host($datos['Host'], $IDCliente, $conn);

            $conn = null;

            return true;
        } catch (\PDOException $th) {
            throw new \PDOException($th->getMessage(), $th->getCode());
        }
    }
}
