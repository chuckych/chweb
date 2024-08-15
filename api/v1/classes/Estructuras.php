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

class Estructuras
{
    private $resp;
    private $request;
    private $getData;
    private $query;
    private $log;
    private $conect;
    private $webservice;
    private $tools;
    private $paraGene;

    function __construct()
    {
        $this->resp = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->query = $this->request->query->getData();
        $this->log = new Log;
        $this->conect = new ConnectSqlSrv;
        $this->webservice = new RRHHWebService;
        $this->tools = new Tools;
        $this->paraGene = new ParaGene;
    }

    public function estructuras()
    {
        $conn = $this->conect->conn();
        $data = $this->getData;

        if (empty($data)) {
            $this->resp->respuesta('No hay datos', 0, 'Error', 400, microtime(true), 0, 0);
            exit;
        }

        $data['Empr'] = $data['Empr'] ?? '';
        $data['Plan'] = $data['Plan'] ?? '';
        $data['Conv'] = $data['Conv'] ?? '';
        $data['Sect'] = $data['Sect'] ?? '';
        $data['Secc'] = $data['Secc'] ?? '';
        $data['Grup'] = $data['Grup'] ?? '';
        $data['Sucu'] = $data['Sucu'] ?? '';
        $data['Tare'] = $data['Tare'] ?? '';

        $estructuras = [];

        if ($data['Empr']) {
            $empresa = explode(',', $data['Empr']);
            $empresa = array_map('intval', $empresa);
            $empresa = implode(',', $empresa);
            $sql = "SELECT EmpCodi, EmpRazon FROM EMPRESAS WHERE EmpCodi IN ($empresa)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Empresas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Empresas'] = $array['Empresas'];
        }

        if ($data['Plan']) {
            $plantas = explode(',', $data['Plan']);
            $plantas = array_map('intval', $plantas);
            $plantas = implode(',', $plantas);
            $sql = "SELECT PlaCodi, PlaDesc FROM PLANTAS WHERE PlaCodi IN ($plantas)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Plantas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Plantas'] = $array['Plantas'];
        }

        if ($data['Conv']) {
            $convenios = explode(',', $data['Conv']);
            $convenios = array_map('intval', $convenios);
            $convenios = implode(',', $convenios);
            $sql = "SELECT ConCodi, ConDesc FROM CONVENIO WHERE ConCodi IN ($convenios)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Convenios'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Convenios'] = $array['Convenios'];
        }

        if ($data['Sect']) {
            $sectores = explode(',', $data['Sect']);
            $sectores = array_map('intval', $sectores);
            $sectores = implode(',', $sectores);
            $sql = "SELECT SecCodi, SecDesc FROM SECTORES WHERE SecCodi IN ($sectores)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Sectores'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Sectores'] = $array['Sectores'];
        }

        if ($data['Secc']) {
            $secciones = explode(',', $data['Secc']);
            $secciones = array_map('intval', $secciones);
            $secciones = implode(',', $secciones);
            $sql = "SELECT SecCodi, Se2Codi, Se2Desc FROM SECCION WHERE CONCAT(SecCodi, Se2Codi) IN ($secciones)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Secciones'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Secciones'] = $array['Secciones'];
        }

        if ($data['Grup']) {
            $grupos = explode(',', $data['Grup']);
            $grupos = array_map('intval', $grupos);
            $grupos = implode(',', $grupos);
            $sql = "SELECT GruCodi, GruDesc FROM GRUPOS WHERE GruCodi IN ($grupos)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Grupos'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Grupos'] = $array['Grupos'];
        }

        if ($data['Sucu']) {
            $sucursales = explode(',', $data['Sucu']);
            $sucursales = array_map('intval', $sucursales);
            $sucursales = implode(',', $sucursales);
            $sql = "SELECT SucCodi, SucDesc FROM SUCURSALES WHERE SucCodi IN ($sucursales)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['Sucursales'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['Sucursales'] = $array['Sucursales'];
        }

        if ($data['Tare']) {
            $tareas = explode(',', $data['Tare']);
            $tareas = array_map('intval', $tareas);
            $tareas = implode(',', $tareas);
            $sql = "SELECT TareCodi, TareDesc FROM TAREAS WHERE TareCodi IN ($tareas)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(); // Ejecuto la consulta
            $array['tareas'] = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $estructuras['tareas'] = $array['tareas'];
        }

        $this->conect->close($conn);

        $this->resp->respuesta($estructuras, 1, 'OK', 200, microtime(true), 0, 0);
    }
    public function create()
    {
        $inicio = microtime(true);
        $datos = $this->validarDataEstruct(); // Valido los datos
        $conn = $this->conect->conn();

        $Estruct = $datos['Estruct'];
        $FechaHora = date('Ymd H:i:s');

        // si $datos['Cod'] esta vacío, buscar el proximo código
        if (empty($datos['Cod'])) {
            $queryProx = [
                'Empr' => "SELECT ISNULL(MAX(EmpCodi), 0) + 1 AS ProxCodi FROM EMPRESAS",
                'Plan' => "SELECT ISNULL(MAX(PlaCodi), 0) + 1 AS ProxCodi FROM PLANTAS",
                'Conv' => "SELECT ISNULL(MAX(ConCodi), 0) + 1 AS ProxCodi FROM CONVENIO",
                'Sect' => "SELECT ISNULL(MAX(SecCodi), 0) + 1 AS ProxCodi FROM SECTORES",
                'Sec2' => "SELECT ISNULL(MAX(Se2Codi), 0) + 1 AS ProxCodi FROM SECCION WHERE SecCodi = :SecCodi",
                'Grup' => "SELECT ISNULL(MAX(GruCodi), 0) + 1 AS ProxCodi FROM GRUPOS",
                'Sucu' => "SELECT ISNULL(MAX(SucCodi), 0) + 1 AS ProxCodi FROM SUCURSALES",
                'Tare' => 'SELECT ISNULL(MAX(TareCodi), 0) + 1 AS ProxCodi FROM TAREAS',
            ];

            $stmt = $conn->prepare($queryProx[$Estruct]);
            if ($Estruct == 'Sec2') {
                $stmt->bindParam(':SecCodi', $datos['SecCodi'], \PDO::PARAM_INT);
            }
            $stmt->execute(); // Ejecuto la consulta
            $prox = $stmt->fetch(\PDO::FETCH_ASSOC);
            $datos['Cod'] = $prox['ProxCodi'];
        }

        // validar si ya existe el codigo
        $queryValidaCod = [
            'Empr' => "SELECT EmpCodi FROM EMPRESAS WHERE EmpCodi = :Cod",
            'Plan' => "SELECT PlaCodi FROM PLANTAS WHERE PlaCodi = :Cod",
            'Conv' => "SELECT ConCodi FROM CONVENIO WHERE ConCodi = :Cod",
            'Sect' => "SELECT SecCodi FROM SECTORES WHERE SecCodi = :Cod",
            'Sec2' => "SELECT Se2Codi FROM SECCION WHERE SecCodi = :SecCodi AND Se2Codi = :Cod",
            'Grup' => "SELECT GruCodi FROM GRUPOS WHERE GruCodi = :Cod",
            'Sucu' => "SELECT SucCodi FROM SUCURSALES WHERE SucCodi = :Cod",
            'Tare' => 'SELECT TareCodi FROM TAREAS WHERE TareCodi = :Cod',
        ];

        $stmt = $conn->prepare($queryValidaCod[$Estruct]);

        if ($Estruct == 'Sec2') {
            $stmt->bindParam(':SecCodi', $datos['SecCodi'], \PDO::PARAM_INT);
        }
        $stmt->bindParam(':Cod', $datos['Cod'], \PDO::PARAM_INT);
        $stmt->execute(); // Ejecuto la consulta

        $existe = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($existe) {
            $this->conect->close($conn);
            $this->resp->respuesta('', 0, 'El código (' . $datos['Cod'] . ') ya existe', 400, $inicio, 0, 0);
        }

        // validar si ya existe la descripción
        $queryValidaDesc = [
            'Empr' => "SELECT EmpRazon FROM EMPRESAS WHERE EmpRazon = :Desc",
            'Plan' => "SELECT PlaDesc FROM PLANTAS WHERE PlaDesc = :Desc",
            'Conv' => "SELECT ConDesc FROM CONVENIO WHERE ConDesc = :Desc",
            'Sect' => "SELECT SecDesc FROM SECTORES WHERE SecDesc = :Desc",
            'Sec2' => "SELECT Se2Desc FROM SECCION WHERE Se2Desc = :Desc AND SecCodi = :SecCodi",
            'Grup' => "SELECT GruDesc FROM GRUPOS WHERE GruDesc = :Desc",
            'Sucu' => "SELECT SucDesc FROM SUCURSALES WHERE SucDesc = :Desc",
            'Tare' => 'SELECT TareDesc FROM TAREAS WHERE TareDesc = :Desc',
        ];

        $stmt = $conn->prepare($queryValidaDesc[$Estruct]);
        if ($Estruct == 'Sec2') {
            $stmt->bindParam(':SecCodi', $datos['SecCodi'], \PDO::PARAM_INT);
        }
        $stmt->bindParam(':Desc', $datos['Desc'], \PDO::PARAM_STR);
        $stmt->execute(); // Ejecuto la consulta
        $existe = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($existe) {
            $this->conect->close($conn);
            $this->resp->respuesta('', 0, 'La descripción (' . $datos['Desc'] . ') ya existe', 400, microtime(true), 0, 0);
        }

        $queryEstruct = [
            'Empr' => "INSERT INTO EMPRESAS (EmpCodi, EmpRazon, FechaHora) VALUES (:Cod, :Desc, :FechaHora)",
            'Plan' => "INSERT INTO PLANTAS (PlaCodi, PlaDesc, PlaEvEntra, PlaEvSale, PlaZonaHoraria, FechaHora) VALUES (:Cod, :Desc, :EvEntra, :EvSale, :PlaZonaHoraria, :FechaHora)",
            'Conv' => "INSERT INTO CONVENIO (ConCodi, ConDesc, FechaHora) VALUES (:Cod, :Desc, :FechaHora)",
            'Sect' => "INSERT INTO SECTORES (SecCodi, SecDesc, SecTaIn, FechaHora) VALUES (:Cod, :Desc, '', :FechaHora)",
            'Sec2' => "INSERT INTO SECCION (Se2Codi, SecCodi , Se2Desc, FechaHora) VALUES (:Cod, :SecCodi, :Desc, :FechaHora)",
            'Grup' => "INSERT INTO GRUPOS (GruCodi, GruDesc, FechaHora) VALUES (:Cod, :Desc, :FechaHora)",
            'Sucu' => "INSERT INTO SUCURSALES (SucCodi, SucDesc, FechaHora) VALUES (:Cod, :Desc, :FechaHora)",
            'Tare' => 'INSERT INTO TAREAS (TareCodi, TareDesc, TareEstado, FechaHora) VALUES (:Cod, :Desc, 0, :FechaHora)',
        ];

        $stmt = $conn->prepare($queryEstruct[$Estruct]);
        if ($Estruct == 'Sec2') {
            $stmt->bindParam(':SecCodi', $datos['SecCodi'], \PDO::PARAM_INT);
        }
        $stmt->bindParam(':Desc', $datos['Desc'], \PDO::PARAM_STR);
        $stmt->bindParam(':Cod', $datos['Cod'], \PDO::PARAM_INT);
        $stmt->bindParam(':FechaHora', $FechaHora, \PDO::PARAM_STR);

        if ($Estruct == 'Plan') {
            $stmt->bindParam(':EvEntra', $datos['EvEntra'], \PDO::PARAM_INT);
            $stmt->bindParam(':EvSale', $datos['EvSale'], \PDO::PARAM_INT);
            $stmt->bindParam(':PlaZonaHoraria', $datos['PlaZonaHoraria'], \PDO::PARAM_STR);
        }
        $stmt->bindParam(':Cod', $datos['Cod'], \PDO::PARAM_INT);
        if ($Estruct == 'Sec2') {
            $stmt->bindParam(':SecCodi', $datos['SecCodi'], \PDO::PARAM_INT);
        }

        $stmt->execute(); // Ejecuto la consulta

        $this->conect->close($conn);

        $data = [
            'Estruct' => $Estruct,
            'Cod' => $datos['Cod'],
            'Desc' => $datos['Desc'],
        ];

        if ($Estruct == 'Plan') {
            $data['EvEntra'] = $datos['EvEntra'];
            $data['EvSale'] = $datos['EvSale'];
            $data['PlaZonaHoraria'] = $datos['PlaZonaHoraria'];
        }
        if ($Estruct == 'Sec2') {
            $data['SecCodi'] = $datos['SecCodi'];
        }

        $this->resp->respuesta($data, 1, 'OK', 200, microtime(true), 0, 0);
    }
    private function validarDataEstruct()
    {
        $datos = $this->getData;

        if ($this->tools->jsonNoValido()) {
            $errores = $this->tools->jsonNoValido();
            $this->resp->respuesta($errores, 0, "Formato JSON invalido", 400, microtime(true), 0, 0);
        }

        if (!$datos) {
            $this->resp->respuesta('', 0, "No se recibieron datos o hay errores", 400, microtime(true), 0, 0);
        }

        $Estruct = $datos['Estruct'] ?? '';
        $Cod = $datos['Cod'] ?? '';
        $Desc = $datos['Desc'] ?? '';
        $EvEntra = $datos['EvEntra'] ?? '';
        $EvSale = $datos['EvSale'] ?? '';
        $PlaZonaHoraria = $datos['PlaZonaHoraria'] ?? '';
        $SecCodi = $datos['SecCodi'] ?? '';

        $datosRecibidos = array( // Valores por defecto
            'Estruct' => empty($Estruct) ? '' : $Estruct,
            'Cod' => empty($Cod) ? '' : $Cod,
            'SecCodi' => empty($SecCodi) ? '' : $SecCodi,
            'Desc' => empty($Desc) ? '' : $Desc,
            'EvEntra' => empty($EvEntra) ? '' : $EvEntra,
            'EvSale' => empty($EvSale) ? '' : $EvSale,
            'PlaZonaHoraria' => empty($PlaZonaHoraria) ? 'Argentina Standard Time' : $PlaZonaHoraria,
        );

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $estructurasValidas = [
                // 'Empr',
                'Plan',
                // 'Conv',
                'Sect',
                'Sec2',
                'Grup',
                'Sucu',
                'Tare'
            ];

            if (!in_array($Estruct, $estructurasValidas)) {
                throw new \Exception("Estructura no válida", 1);
            }

            $rules = [ // Reglas de validación
                'Cod' => ['smallintEmpty'],
                'Desc' => ['required', 'varchar40'],
            ];

            if ($Estruct == 'Plan') {
                $rules['EvEntra']        = ['smallintEmpty'];
                $rules['EvSale']         = ['smallintEmpty'];
                $rules['PlaZonaHoraria'] = ['varcharMax'];
            }

            if ($Estruct == 'Sec2') {
                $rules['SecCodi'] = ['required', 'smallint'];
            }

            $validator = new InputValidator($datosRecibidos, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos
            return $datosRecibidos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarDataEstruct.log');
        }
    }
}
