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

class Novedades
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
    public function update()
    {
        $inicio = microtime(true);
        $datos = $this->validarInputs();

        $this->query = array('start' => 0, 'length' => 9999); // Para que no se pagine
        $dataNovedades = $this->data(true);

        $procesar = Flight::request()->query['procesar'] ?? false;

        $conn = $this->conect->conn();
        $FechaHoraActual = $this->conect->FechaHora(); // Fecha y hora actual
        try {
            $conn->beginTransaction(); // Iniciar transacción

            $sql = "UPDATE FICHAS3 SET FicNove = :NoveM, FicNoTi= :FicNoTi, FicHoras = :Horas, FicEsta = :Esta, FicCaus = :Causa, FicObse = :Obse, FechaHora = :FechaHora, FicCate = :Cate WHERE FicLega = :Lega AND FicFech = :Fecha AND FicTurn = 1 AND FicNove = :Nove";
            $stmt = $conn->prepare($sql);

            $totalAffectedRows = 0;

            // print_r($datos) . exit;

            foreach ($datos as $dato) { // Recorro los datos

                $tipoNovedad = $this->obtenerTipoDeNovedad($dato['NoveM'], $dataNovedades);

                $dato['Fecha'] = date('Ymd', strtotime($dato['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $stmt->bindValue(':NoveM', $dato['NoveM'], \PDO::PARAM_INT);
                $stmt->bindValue(':Horas', $dato['Horas'], \PDO::PARAM_STR);
                $stmt->bindValue(':Esta', $dato['Esta'], \PDO::PARAM_INT);
                $stmt->bindValue(':Causa', $dato['Causa'], \PDO::PARAM_INT);
                $stmt->bindValue(':Obse', $dato['Obse'], \PDO::PARAM_STR);
                $stmt->bindValue(':FechaHora', $FechaHoraActual, \PDO::PARAM_STR);
                $stmt->bindValue(':Lega', $dato['Lega'], \PDO::PARAM_INT);
                $stmt->bindValue(':Fecha', $dato['Fecha'], \PDO::PARAM_STR);
                $stmt->bindValue(':Nove', $dato['Nove'], \PDO::PARAM_INT);
                $stmt->bindValue(':FicNoTi', $tipoNovedad, \PDO::PARAM_INT);
                $stmt->bindValue(':Cate', $dato['Cate'], \PDO::PARAM_INT);
                $stmt->execute(); // Ejecuto la consulta
                $totalAffectedRows += $stmt->rowCount(); // Cuento la cantidad de filas afectadas
            }

            $conn->commit(); // Confirmar la transacción

            $groupedData = [];

            $minDate = PHP_INT_MAX;
            $maxDate = 0;

            foreach ($datos as $item) {
                $lega = $item["Lega"];
                $fechaMin = strtotime($item["Fecha"]);
                $fechaMax = strtotime($item["Fecha"]);

                if ($fechaMin < $minDate) {
                    $minDate = $fechaMin;
                }
                if ($fechaMax > $maxDate) {
                    $maxDate = $fechaMax;
                }

                if (!isset($groupedData[$lega])) {
                    $groupedData[$lega] = true;
                }
            }

            $agrup = [ // Agrupar los datos para procesarlos en el WebService
                "Legajos" => array_keys($groupedData), // Obtengo los legajos
                "Fechas" => [ // Obtengo las fechas
                    "Desde" => date("Y-m-d", $minDate),
                    "Hasta" => date("Y-m-d", $maxDate)
                ]
            ];

            if ($agrup) { // Si hay datos para procesar
                try { // Procesar los legajos en el WebService
                    if (!isset($agrup['Fechas']['Desde']) || !isset($agrup['Fechas']['Hasta']) || empty($agrup['Fechas']['Desde']) || empty($agrup['Fechas']['Hasta'])) {
                        throw new \Exception("No se recibieron las fechas", 1);
                    }
                    // Validar que existan los legajos
                    if (!isset($agrup['Legajos']) || empty($agrup['Legajos'])) {
                        throw new \Exception("No se recibieron los legajos", 1);
                    }
                    $Legajos = $agrup['Legajos'];
                    $Desde = $agrup['Fechas']['Desde'];
                    $Hasta = $agrup['Fechas']['Hasta'];
                    if ($procesar) {
                        $this->webservice->procesar_legajos($Legajos, $Desde, $Hasta);
                    }
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    return false;
                }
            }
            $this->resp->respuesta([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_updateNovedades_' . ID_COMPANY . '.log');
            exit;
        }
    }
    public function add()
    {
        $inicio = microtime(true);
        $datos = $this->validarInputsAdd();

        $this->query = array('start' => 0, 'length' => 9999); // Para que no se pagine
        $dataNovedades = $this->data(true);

        $procesar = Flight::request()->query['procesar'] ?? false;

        $conn = $this->conect->conn();
        $FechaHoraActual = $this->conect->FechaHora(); // Fecha y hora actual
        try {
            $conn->beginTransaction(); // Iniciar transacción

            $sql = "INSERT INTO FICHAS3 (FicLega, FicFech, FicTurn, FicNove, FicNoTi, FicHoras, FicEsta, FicCaus, FicObse, FechaHora, FicCate, FicJust, FicComp) VALUES (:Lega, :Fecha, 1, :Nove, :FicNoTi, :Horas, :Esta, :Causa, :Obse, :FechaHora, :Cate, :Comp, :Just)";

            $stmt = $conn->prepare($sql);

            $totalAffectedRows = 0;

            foreach ($datos as $dato) { // Recorro los datos

                $tipoNovedad = $this->obtenerTipoDeNovedad($dato['Nove'], $dataNovedades);
                $Fecha = date('Ymd', strtotime($dato['Fecha'])); // Convierto la fecha a formato YYYYMMDD

                $stmt->bindValue(':Lega', $dato['Lega'], \PDO::PARAM_INT);
                $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
                $stmt->bindValue(':Nove', $dato['Nove'], \PDO::PARAM_INT);
                $stmt->bindValue(':FicNoTi', $tipoNovedad, \PDO::PARAM_INT);
                $stmt->bindValue(':Horas', $dato['Horas'], \PDO::PARAM_STR);
                $stmt->bindValue(':Esta', $dato['Esta'], \PDO::PARAM_INT);
                $stmt->bindValue(':Causa', $dato['Causa'], \PDO::PARAM_INT);
                $stmt->bindValue(':Obse', $dato['Obse'], \PDO::PARAM_STR);
                $stmt->bindValue(':FechaHora', $FechaHoraActual, \PDO::PARAM_STR);
                $stmt->bindValue(':Cate', $dato['Cate'], \PDO::PARAM_INT);
                $stmt->bindValue(':Comp', 0, \PDO::PARAM_INT);
                $stmt->bindValue(':Just', '00:00', \PDO::PARAM_STR);

                $stmt->execute(); // Ejecuto la consulta
                $totalAffectedRows += $stmt->rowCount(); // Cuento la cantidad de filas afectadas
            }

            $conn->commit(); // Confirmar la transacción

            $groupedData = [];

            $minDate = PHP_INT_MAX;
            $maxDate = 0;

            foreach ($datos as $item) {
                $lega = $item["Lega"];
                $fechaMin = strtotime($item["Fecha"]);
                $fechaMax = strtotime($item["Fecha"]);

                if ($fechaMin < $minDate) {
                    $minDate = $fechaMin;
                }
                if ($fechaMax > $maxDate) {
                    $maxDate = $fechaMax;
                }

                if (!isset($groupedData[$lega])) {
                    $groupedData[$lega] = true;
                }
            }

            $agrup = [ // Agrupar los datos para procesarlos en el WebService
                "Legajos" => array_keys($groupedData), // Obtengo los legajos
                "Fechas" => [ // Obtengo las fechas
                    "Desde" => date("Y-m-d", $minDate),
                    "Hasta" => date("Y-m-d", $maxDate)
                ]
            ];

            if ($agrup) { // Si hay datos para procesar
                try { // Procesar los legajos en el WebService
                    if (!isset($agrup['Fechas']['Desde']) || !isset($agrup['Fechas']['Hasta']) || empty($agrup['Fechas']['Desde']) || empty($agrup['Fechas']['Hasta'])) {
                        throw new \Exception("No se recibieron las fechas", 1);
                    }
                    // Validar que existan los legajos
                    if (!isset($agrup['Legajos']) || empty($agrup['Legajos'])) {
                        throw new \Exception("No se recibieron los legajos", 1);
                    }
                    $Legajos = $agrup['Legajos'];
                    $Desde = $agrup['Fechas']['Desde'];
                    $Hasta = $agrup['Fechas']['Hasta'];

                    if ($procesar) {
                        $this->webservice->procesar_legajos($Legajos, $Desde, $Hasta);
                    }
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    return false;
                }
            }
            $this->resp->respuesta([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_addNovedades_' . ID_COMPANY . '.log');
            exit;
        }
    }
    public function delete()
    {
        $inicio = microtime(true);
        $datos = $this->validarInputsDelete();

        $conn = $this->conect->conn();

        $procesar = Flight::request()->query['procesar'] ?? false;

        try {
            $conn->beginTransaction(); // Iniciar transacción

            $sql = "DELETE FROM FICHAS3 WHERE FicLega = :Lega AND FicFech = :Fecha AND FicTurn = 1 AND FicNove = :Nove";
            $stmt = $conn->prepare($sql);

            $totalAffectedRows = 0;

            foreach ($datos as $dato) { // Recorro los datos
                $dato['Fecha'] = date('Ymd', strtotime($dato['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $stmt->bindValue(':Lega', $dato['Lega'], \PDO::PARAM_INT);
                $stmt->bindValue(':Fecha', $dato['Fecha'], \PDO::PARAM_STR);
                $stmt->bindValue(':Nove', $dato['Nove'], \PDO::PARAM_INT);
                $stmt->execute(); // Ejecuto la consulta
                $totalAffectedRows += $stmt->rowCount(); // Cuento la cantidad de filas afectadas
            }

            $conn->commit(); // Confirmar la transacción

            $groupedData = [];

            $minDate = PHP_INT_MAX;
            $maxDate = 0;

            foreach ($datos as $item) {
                $lega = $item["Lega"];
                $fechaMin = strtotime($item["Fecha"]);
                $fechaMax = strtotime($item["Fecha"]);

                if ($fechaMin < $minDate) {
                    $minDate = $fechaMin;
                }
                if ($fechaMax > $maxDate) {
                    $maxDate = $fechaMax;
                }

                if (!isset($groupedData[$lega])) {
                    $groupedData[$lega] = true;
                }
            }

            $agrup = [ // Agrupar los datos para procesarlos en el WebService
                "Legajos" => array_keys($groupedData), // Obtengo los legajos
                "Fechas" => [ // Obtengo las fechas
                    "Desde" => date("Y-m-d", $minDate),
                    "Hasta" => date("Y-m-d", $maxDate)
                ]
            ];

            if ($agrup) { // Si hay datos para procesar
                try { // Procesar los legajos en el WebService
                    if (!isset($agrup['Fechas']['Desde']) || !isset($agrup['Fechas']['Hasta']) || empty($agrup['Fechas']['Desde']) || empty($agrup['Fechas']['Hasta'])) {
                        throw new \Exception("No se recibieron las fechas", 1);
                    }
                    // Validar que existan los legajos
                    if (!isset($agrup['Legajos']) || empty($agrup['Legajos'])) {
                        throw new \Exception("No se recibieron los legajos", 1);
                    }
                    $Legajos = $agrup['Legajos'];
                    $Desde = $agrup['Fechas']['Desde'];
                    $Hasta = $agrup['Fechas']['Hasta'];
                    if ($procesar) {
                        $this->webservice->procesar_legajos($Legajos, $Desde, $Hasta);
                    }
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    return false;
                }
            }
            $this->resp->respuesta([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_deleteNovedades_' . ID_COMPANY . '.log');
            exit;
        }
    }
    private function validarInputs()
    {
        $datos = $this->getData;
        if ($this->tools->jsonNoValido()) {
            $errores = $this->tools->jsonNoValido();
            $this->resp->respuesta($errores, 0, "Formato JSON invalido", 400, microtime(true), 0, 0);
        }
        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'Lega' => ['int'],
                'Fecha' => ['required', 'date'],
                'Nove' => ['required', 'smallint'],
                'NoveM' => ['required', 'smallint'],
                'Horas' => ['required', 'time'],
                'Esta' => ['allowed012'],
                'Obse' => ['varchar40'],
                'Causa' => ['smallint'],
                'Cate' => ['smallint']
            ];

            $FechaHoraActual = date('YmdHis') . substr((string) microtime(), 1, 8); // Fecha y hora actual
            $customValueKey = array( // Valores por defecto
                'Lega' => "0",
                'Fecha' => '00000000',
                'Nove' => "0",
                'NoveM' => "0",
                'Horas' => '00:00',
                'Esta' => "1",
                'Obse' => '',
                'Causa' => "0",
                'FechaHora' => '',
                'Cate' => "0"
            );
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
            // $this->resp->respuesta($datosModificados, 0, 'Todo bien con los datos', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputs.log');
        }
    }
    private function validarInputsAdd()
    {
        $datos = $this->getData;
        if ($this->tools->jsonNoValido()) {
            $errores = $this->tools->jsonNoValido();
            $this->resp->respuesta($errores, 0, "Formato JSON invalido", 400, microtime(true), 0, 0);
        }
        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'Lega' => ['required', 'int'],
                'Fecha' => ['required', 'date'],
                'Nove' => ['required', 'smallint'],
                'Horas' => ['time'],
                'Esta' => ['allowed012'],
                'Obse' => ['varchar40'],
                'Causa' => ['smallint'],
                'Cate' => ['smallint']
            ];

            $FechaHoraActual = date('YmdHis') . substr((string) microtime(), 1, 8); // Fecha y hora actual
            $customValueKey = array( // Valores por defecto
                'Lega' => "0",
                'Fecha' => '00000000',
                'Nove' => "0",
                'Horas' => '00:00',
                'Esta' => "0",
                'Obse' => '',
                'Causa' => "0",
                'FechaHora' => '',
                'Cate' => "0"
            );
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
            // $this->resp->respuesta($datosModificados, 0, 'Todo bien con los datos', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputsAdd.log');
        }
    }
    private function validarInputsDelete()
    {
        $datos = $this->getData;

        if ($this->tools->jsonNoValido()) {
            $errores = $this->tools->jsonNoValido();
            $this->resp->respuesta($errores, 0, "Formato JSON invalido", 400, microtime(true), 0, 0);
        }

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'Lega' => ['required', 'int'],
                'Fecha' => ['required', 'date'],
                'Nove' => ['smallint'],
            ];

            $customValueKey = array( // Valores por defecto
                'Lega' => "0",
                'Fecha' => '00000000',
                // 'Nove' => "0",
            );
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
            // $this->resp->respuesta($datosModificados, 0, 'Todo bien con los datos', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputsDelete.log');
        }
    }
    public function estruct($estruct)
    {
        $estruct = strtolower($estruct); // Convierto a minúscula
        $inicio = microtime(true); // Tiempo de inicio de la consulta
        $datos = $this->getEstruct($estruct); // retorna la estructura de datos validada

        try {

            $FechaIni = date('Ymd', strtotime($datos['FechaIni'])); // Fecha de inicio
            $FechaFin = date('Ymd', strtotime($datos['FechaFin'])); // Fecha de fin

            if ($FechaIni > $FechaFin) {
                throw new \Exception("La fecha de inicio no puede ser mayor a la fecha de fin", 400);
            }

            $Cod = ($datos['Cod']); // Código de la estructura
            $Lega = ($datos['Lega']); // Legajo
            $Empr = ($datos['Empr']); // Empresa
            $Plan = ($datos['Plan']); // Planta
            $Conv = ($datos['Conv']); // Convenio
            $Sect = ($datos['Sect']); // Sector
            $Sec2 = ($datos['Sec2']); // Sección
            $Grup = ($datos['Grup']); // Grupo
            $Sucu = ($datos['Sucu']); // Sucursal
            $Tare = ($datos['TareProd']); // Tarea de producción
            $RegCH = ($datos['RegCH']); // Regla de Control Horario
            $Tipo = ($datos['Tipo']); // Tipo de Personal
            $Docu = ($datos['Docu']); // DNI Del Legajo
            $Sector = ($datos['Sector']); // Sector

            $Esta = ($datos['Esta']); // Estado de la ficha hora (FicEsta)
            $Nove = ($datos['Nove']); // Código novedad
            $NoveTipo = ($datos['NoveTipo']); // Código tipo novedad
            $NovI = ($datos['NovI']); // Si Novedad Incumplimiento
            $NovA = ($datos['NovA']); // Si Novedad Ausentismo
            $NovS = ($datos['NovS']); // Si Novedad Salida anticipada
            $NovT = ($datos['NovT']); // Si Novedad Tardanza
            $DiaL = ($datos['DiaL']); // Si Día Laboral
            $DiaF = ($datos['DiaF']); // Si Día Feriado

            $start = $datos['start']; // Pagina de inicio
            $length = $datos['length']; // Cantidad de registros
            $Desc = $datos['Desc']; // Descripción de la estructura
            $ApNo = $datos['ApNo']; // Apellido y Nombre
            $ApNoLega = $datos['ApNoLega']; // Apellido y Nombre + legajo

            /** De este Fragmento de código se defino si paramsPers es true o false. Si es true se utiliza para el INNER JOIN  PERSONAL */
            $estructuras = ['tare', 'regla', 'lega', 'tipo']; // Estructuras que se utilizan para el INNER JOIN PERSONAL
            $parámetros = [$ApNo, $ApNoLega, $Docu, $Tipo, $RegCH, $Tare]; // Parámetros que se utilizan para el INNER JOIN PERSONAL
            $estructTare = in_array($estruct, $estructuras); // Si la estructura esta en el array $estructuras
            $paramPers = $estructTare || in_array(true, $parámetros); // Si la estructura esta en el array $estructuras o si algún parámetro esta en el array $parámetros
            /** FIN Fragmento */
            /** SELECT */
            $sql = "SELECT";
            $sql .= $this->columnsEstruct($estruct); // Columnas de la estructura, campos Descripción, Código y cantidad
            $sql .= " FROM FICHAS";
            $sql .= $this->joinFichas3Estruct(); // Join con la tabla FICHAS3 (NOVEDADES)

            /** JOIN PERSONAL */
            $sql .= $this->joinPersonalEstruct($paramPers); // Join con la tabla PERSONAL
            /** JOIN ESTRUCTURA */
            $sql .= $this->joinEstruct($estruct); // Join con la tabla de la estructura

            /** QUERY TABLA FICHAS */
            $sql .= $this->queryFichasEstruct($Lega, $Empr, $Plan, $Conv, $Sect, $Sec2, $Grup, $Sucu, $NovI, $NovA, $NovS, $NovT, $DiaL, $DiaF);

            /** QUERY ESTRUCTURA */
            $sql .= $this->queryEstructDesc($estruct, $Desc); // Descripción de la estructura
            $sql .= $this->queryEstructCod($estruct, $Cod); // Código de la estructura
            $sql .= $this->queryEstructSect($estruct, $Sector); // Código de la estructura

            /** QUERY TABLA FICHAS3 (NOVEDADES) */
            $sql .= $this->queryFichas3Estruct($Nove, $NoveTipo, $Esta);

            /** QUERY TABLA PERSONAL */
            $sql .= $this->queryPersonalEstruct($paramPers, $ApNo, $ApNoLega, $Docu, $Tipo, $RegCH, $Tare);

            /** GROUP, ORDER BY, PAGINACIÓN */
            $sql .= $this->groupByEstruct($estruct); // Group By de la estructura
            $sql .= " OFFSET :start ROWS FETCH NEXT :length ROWS ONLY"; // Paginación
            // print_r($sql) . exit;
            /** PARÁMETROS DE LA CONSULTA SQL */
            $params = [
                ':FechaIni' => $FechaIni,
                ':FechaFin' => $FechaFin,
                ':start' => intval($start),
                ':length' => intval($length)
            ];
            ($Desc) ? $params[':Desc'] = '%' . $Desc . '%' : '';
            ($paramPers && $ApNo) ? $params[':ApNo'] = '%' . $ApNo . '%' : '';
            ($paramPers && $ApNoLega) ? $params[':ApNoLega'] = '%' . $ApNoLega . '%' : '';
            /**  FIN DE PARÁMETROS DE LA CONSULTA SQL */
            // print_r($params) . exit;
            $data = $this->conect->executeQueryWhithParams($sql, $params);
            $total = count($data);
            $this->resp->respuesta($data, $total, 'OK', 200, $inicio, 0, 0);
        } catch (\Exception $th) {
            $this->resp->respuesta('', 0, $th->getMessage(), $th->getCode(), $inicio, 0, 0);
            exit;
        }
    }
    private function getEstruct($estruct)
    {
        $arrValidEstruct = ["empr", "plan", "grup", "sect", "sec2", "sucu", "tare", "conv", "regla", "nove", "novetipo", "lega", "tipo"];

        if (!in_array(strtolower($estruct), $arrValidEstruct)) { // Si la estructura no es valida
            $this->resp->respuesta('', 0, "Estructura ($estruct) no valida. Valores permitidos " . json_encode($arrValidEstruct), 400, microtime(true), 0, 0);
            exit;
        }

        $rules = [ // Reglas de validación
            'Cod' => ['arrInt'], // Código de la estructura
            'Desc' => ['varchar40'], // Descripción de la estructura
            'Sector' => ['arrSmallint'], // Sector
            'Docu' => ['arrInt'], // DNI Del Legajo
            'Lega' => ['arrInt'], // Legajo
            'ApNo' => ['varchar40'], // Apellido y Nombre
            'ApNoLega' => ['varchar40'], // Apellido y Nombre del legajo
            'Empr' => ['arrSmallint'], // Empresa
            'Plan' => ['arrSmallint'], // Planta
            'Conv' => ['arrSmallint'], // Convenio
            'Sect' => ['arrSmallint'], // Sección
            'Sec2' => ['arrSmallint'], // Sección 2
            'Grup' => ['arrSmallint'], // Grupo
            'Sucu' => ['arrSmallint'], // Sucursal
            'TareProd' => ['arrSmallint'], // Tarea de producción
            'RegCH' => ['arrSmallint'], // Regla de Control Horario
            'Tipo' => ['arrSmallint'], // Tipo de Personal
            'FechaIni' => ['required', 'date'], // Fecha de inicio
            'FechaFin' => ['required', 'date'], // Fecha de fin
            'Esta' => ['arrAllowed012'], // Estado de la ficha novedad (FicEsta)
            'start' => ['intempty'], // Pagina de inicio
            'length' => ['intempty'], // Cantidad de registros
            'Nove' => ['arrSmallint'], // Código novedad
            'NoveTipo' => ['arrSmallint'], // Código tipo novedad
            'NovI' => ['arrAllowed01'], // Si Novedad Incumplimiento
            'NovA' => ['arrAllowed01'], // Si Novedad Ausentismo
            'NovS' => ['arrAllowed01'], // Si Novedad Salida anticipada
            'NovT' => ['arrAllowed01'], // Si Novedad Tardanza
            'DiaL' => ['arrAllowed01'], // Si Dia laboral
            'DiaF' => ['arrAllowed01'], // Si Dia feriado
        ];

        try {
            $datos = ($this->getData);

            if ($this->tools->jsonNoValido()) {
                $errores = $this->tools->jsonNoValido();
                $this->resp->respuesta($errores, 0, "Formato JSON invalido", 400, microtime(true), 0, 0);
            }

            if ($estruct == 'sec2' && empty($datos['Sector'] ?? '')) {
                throw new \Exception("Parámetro Sector es requerido cuando solicita estruct sección (sec2).", 1);
            }

            $datos['start'] = $datos['start'] ?? 0; // Pagina de inicio si no viene en los datos
            $datos['length'] = $datos['length'] ?? 5; // Cantidad de registros si no viene en los datos
            $datos['FechaIni'] = $datos['FechaIni'] ?? date('Y-m-d'); // Fecha de inicio si no viene en los datos
            $datos['FechaFin'] = $datos['FechaFin'] ?? date('Y-m-d'); // Fecha de fin si no viene en los datos

            $validator = new InputValidator($datos, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos

            $keyString = ['ApNo', 'ApNoLega', 'Desc'];

            foreach ($keyString as $key) {
                $datos[$key] = $datos[$key] ?? ''; // Si no existe la clave en el array $datos le asigno un string vacío
            }

            $keysArray = ['Sector', 'Cod', 'Lega', 'Docu', 'Empr', 'Plan', 'Conv', 'Sect', 'Sec2', 'Grup', 'Sucu', 'TareProd', 'RegCH', 'Tipo', 'Nove', 'NoveTipo', 'Esta', 'NovI', 'NovA', 'NovS', 'NovT', 'DiaL', 'DiaF'];

            foreach ($keysArray as $key) { // Recorro las claves del array $keysArray
                $datos[$key] = $datos[$key] ?? []; // Si no existe la clave en el array $datos le asigno un array vacío
            }
        } catch (\Throwable $th) {
            $this->resp->respuesta('', 0, $th->getMessage(), 400, microtime(true), 0, 0);
            exit;
        }
        return $datos;
    }
    private function joinEstruct($estruct)
    {
        $JoinEstruct = [
            'empr' => " INNER JOIN EMPRESAS ON FICHAS.FicEmpr = EMPRESAS.EmpCodi",
            'plan' => " INNER JOIN PLANTAS ON FICHAS.FicPlan = PLANTAS.PlaCodi",
            'grup' => " INNER JOIN GRUPOS ON FICHAS.FicGrup = GRUPOS.GruCodi",
            'sect' => " INNER JOIN SECTORES ON FICHAS.FicSect = SECTORES.SecCodi",
            'sucu' => " INNER JOIN SUCURSALES ON FICHAS.FicSucu = SUCURSALES.SucCodi",
            'tare' => " INNER JOIN TAREAS ON PERSONAL.LegTareProd = TAREAS.TareCodi",
            'conv' => " INNER JOIN CONVENIO ON FICHAS.FicConv = CONVENIO.ConCodi",
            'regla' => " INNER JOIN REGLASCH ON PERSONAL.LegRegCH = REGLASCH.RCCodi",
            'nove' => " INNER JOIN NOVEDAD ON FICHAS3.FicNove = NOVEDAD.NovCodi",
            'sec2' => " INNER JOIN SECCION ON FICHAS.FicSec2 = SECCION.Se2Codi AND FICHAS.FicSect = SECCION.SecCodi INNER JOIN SECTORES ON SECCION.SecCodi = SECTORES.SecCodi",
        ];

        return $JoinEstruct[$estruct] ?? '';
    }
    private function columnsEstruct($estruct)
    {
        $cod = 'Cod';
        $count = 'Count';

        $labelEstruct = ($this->paraGene->return());

        $sinEmpr = "Sin " . $labelEstruct['Etiquetas']['EmprSin'];
        $sinPlan = "Sin " . $labelEstruct['Etiquetas']['PlanSin'];
        $sinGrup = "Sin " . $labelEstruct['Etiquetas']['GrupSin'];
        $sinSect = "Sin " . $labelEstruct['Etiquetas']['SectSin'];
        $sinSucu = "Sin " . $labelEstruct['Etiquetas']['SucuSin'];
        $sinSec2 = "Sin " . $labelEstruct['Etiquetas']['SeccSin'];

        $Select = [
            'empr' => $this->caseWhen('EmpRazon', $sinEmpr) . ", FicEmpr AS '$cod', count(FicEmpr) AS '$count'",
            'plan' => $this->caseWhen('PlaDesc', $sinPlan) . ", FicPlan AS '$cod', count(FicPlan) AS '$count'",
            'grup' => $this->caseWhen('GruDesc', $sinGrup) . ", FicGrup AS '$cod', count(FicGrup) AS '$count'",
            'sect' => $this->caseWhen('SecDesc', $sinSect) . ", FicSect AS '$cod', count(FicSect) AS '$count'",
            'sucu' => $this->caseWhen('SucDesc', $sinSucu) . ", FicSucu AS '$cod', count(FicSucu) AS '$count'",
            'tare' => $this->caseWhen('TareDesc', "Sin Tarea") . ", LegTareProd AS '$cod', count(LegTareProd) AS '$count'",
            'conv' => $this->caseWhen('ConDesc', "Fuera de Convenio") . ", FicConv AS '$cod', count(FicConv) AS '$count'",
            'regla' => $this->caseWhen('RCDesc', "Sin Regla CH") . ", LegRegCH AS '$cod', count(LegRegCH) AS '$count'",
            'nove' => $this->caseWhen('NovDesc', "Sin Novedad") . ", FicNove AS '$cod', count(FicNove) AS '$count'",
            'lega' => $this->caseWhen('LegApNo', "Sin Nombre") . ", FICHAS.FicLega AS '$cod', count(FICHAS.FicLega) AS '$count'",
            'tipo' => " dbo.fn_TipoDePersonal(LegTipo) as 'Desc', LegTipo AS '$cod', count(LegTipo) AS '$count'",
            'novetipo' => " dbo.fn_TipoNovedad(FicNoTi) as 'Desc', FicNoTi AS '$cod', count(FicNoTi) AS '$count'",
            'sec2' => $this->caseWhen('Se2Desc', $sinSec2) . ", SECCION.SecCodi AS 'Sect', SECTORES.SecDesc AS 'SectDesc', FicSec2 AS '$cod', count(FicSec2) AS '$count'",
        ];
        return $Select[$estruct];
    }
    private function groupByEstruct($estruct)
    {
        $group = 'GROUP BY';
        $order = 'ORDER BY';
        $GroupBY = [
            'empr' => " $group EmpRazon, FicEmpr $order EmpRazon",
            'plan' => " $group PlaDesc, FicPlan $order PlaDesc",
            'grup' => " $group GruDesc, FicGrup $order GruDesc",
            'sect' => " $group SecDesc, FicSect $order SecDesc",
            'sucu' => " $group SucDesc, FicSucu $order SucDesc",
            'tare' => " $group TareDesc, LegTareProd $order TareDesc",
            'conv' => " $group ConDesc, FicConv $order ConDesc",
            'regla' => " $group RCDesc, LegRegCH $order RCDesc",
            'nove' => " $group NovDesc, FicNove $order NovDesc",
            'novetipo' => " $group FicNoTi $order FicNoTi",
            'lega' => " $group LegApNo, FICHAS.FicLega $order LegApNo",
            'tipo' => " $group LegTipo $order LegTipo",
            'sec2' => " GROUP BY FICHAS.FicSec2, SECCION.Se2Desc, SECCION.SecCodi, SECTORES.SecDesc $order Se2Desc",
        ];
        return $GroupBY[$estruct];
    }
    private function queryEstructDesc($estruct, $Desc)
    {
        if (!($Desc))
            return '';
        $arr = [
            'empr' => " AND EMPRESAS.EmpRazon LIKE :Desc",
            'plan' => " AND PLANTAS.PlaDesc LIKE :Desc",
            'grup' => " AND GRUPOS.GruDesc LIKE :Desc",
            'sect' => " AND SECTORES.SecDesc LIKE :Desc",
            'sucu' => " AND SUCURSALES.SucDesc LIKE :Desc",
            'tare' => " AND TAREAS.TareDesc LIKE :Desc",
            'conv' => " AND CONVENIO.ConDesc LIKE :Desc",
            'regla' => " AND REGLASCH.RCDesc LIKE :Desc",
            'thora' => " AND TIPOHORA.THoDesc LIKE :Desc",
            'lega' => " AND PERSONAL.LegApNo LIKE :Desc",
            'tipo' => " AND dbo.fn_TipoDePersonal(LegTipo) LIKE :Desc",
            'sec2' => " AND SECCION.Se2Desc LIKE :Desc"
        ];
        return $arr[$estruct];
    }
    private function queryEstructCod($estruct, $Cod)
    {
        if (!($Cod))
            return '';
        $arr = [
            'empr' => " AND FICHAS.FicEmpr IN (" . implode(",", $Cod) . ")",
            'plan' => " AND FICHAS.FicPlan IN (" . implode(",", $Cod) . ")",
            'grup' => " AND FICHAS.FicGrup IN (" . implode(",", $Cod) . ")",
            'sect' => " AND FICHAS.FicSect IN (" . implode(",", $Cod) . ")",
            'sucu' => " AND FICHAS.FicSucu IN (" . implode(",", $Cod) . ")",
            'tare' => " AND PERSONAL.LegTareProd IN (" . implode(",", $Cod) . ")",
            'conv' => " AND FICHAS.FicConv IN (" . implode(",", $Cod) . ")",
            'regla' => " AND PERSONAL.LegRegCH IN (" . implode(",", $Cod) . ")",
            'lega' => " AND FICHAS.FicLega IN (" . implode(",", $Cod) . ")",
            'tipo' => " AND PERSONAL.LegTipo IN (" . implode(",", $Cod) . ")",
            'sec2' => " AND FICHAS.FicSec2 IN (" . implode(",", $Cod) . ")"
        ];
        return $arr[$estruct];
    }
    private function queryEstructSect($estruct, $Sector)
    {
        if (!($Sector))
            return '';
        $arr = [
            'sec2' => " AND FICHAS.FicSect IN (" . implode(",", $Sector) . ")"
        ];
        return $arr[$estruct] ?? '';
    }
    private function joinPersonalEstruct($paramPers)
    {
        return($paramPers) ? " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume" : '';
    }
    private function joinFichas3Estruct()
    {
        return " INNER JOIN FICHAS3 ON FICHAS.FicLega = FICHAS3.FicLega AND FICHAS.FicFech = FICHAS3.FicFech AND FICHAS.FicTurn = FICHAS3.FicTurn";
    }
    private function queryFichasEstruct($Lega, $Empr, $Plan, $Conv, $Sect, $Sec2, $Grup, $Sucu, $NovI, $NovA, $NovS, $NovT, $DiaL, $DiaF)
    {
        $Filtros = [
            'Lega' => $Lega,
            'Empr' => $Empr,
            'Plan' => $Plan,
            'Conv' => $Conv,
            'Sect' => $Sect,
            'Sec2' => $Sec2,
            'Grup' => $Grup,
            'Sucu' => $Sucu,
            'NovI' => $NovI,
            'NovA' => $NovA,
            'NovS' => $NovS,
            'NovT' => $NovT,
            'DiaL' => $DiaL,
            'DiaF' => $DiaF
        ];

        $sql = " WHERE FICHAS.FicLega > 0 AND FICHAS.FicFech BETWEEN :FechaIni AND :FechaFin";

        $FiltrosNombres = ['Lega', 'Empr', 'Plan', 'Conv', 'Sect', 'Sec2', 'Grup', 'Sucu', 'NovI', 'NovA', 'NovS', 'NovT', 'DiaL', 'DiaF'];

        foreach ($FiltrosNombres as $Nombre) {
            if (!empty($Filtros[$Nombre])) { // Si el filtro no esta vacío
                $sql .= " AND FICHAS.Fic{$Nombre} IN (" . implode(",", $Filtros[$Nombre]) . ")";
            }
        }
        return $sql;
    }
    private function queryPersonalEstruct($paramPers, $ApNo, $ApNoLega, $Docu, $Tipo, $RegCH, $Tare)
    {
        $sql = '';
        if ($paramPers) {
            $sql .= ($ApNo) ? " AND PERSONAL.LegApNo LIKE :ApNo" : '';
            $sql .= ($ApNoLega) ? " AND CONCAT(PERSONAL.LegApNo, PERSONAL.LegNume) LIKE :ApNoLega" : '';
            $sql .= ($Docu) ? " AND PERSONAL.LegDocu IN (" . implode(",", $Docu) . ")" : '';
            $sql .= ($Tipo) ? " AND PERSONAL.LegTipo IN (" . implode(",", $Tipo) . ")" : '';
            $sql .= ($RegCH) ? " AND PERSONAL.LegRegCH IN (" . implode(",", $RegCH) . ")" : '';
            $sql .= ($Tare) ? " AND PERSONAL.LegTareProd IN (" . implode(",", $Tare) . ")" : '';
        }
        return $sql;
    }
    private function queryFichas3Estruct($Nove, $NoveTipo, $Esta)
    {
        $sql = '';
        $sql .= ($Nove) ? " AND FICHAS3.FicNove IN (" . implode(",", $Nove) . ")" : ''; // Novedad
        $sql .= ($NoveTipo) ? " AND FICHAS3.FicNoTi IN (" . implode(",", $NoveTipo) . ")" : ''; // Tipo Novedad
        $sql .= ($Esta) ? " AND FICHAS3.FicEsta IN (" . implode(",", $Esta) . ")" : ''; // Estado de la ficha hora (FicEsta)
        return $sql;
    }
    private function caseWhen($ColumDesc, $sinDesc)
    {
        return " CASE WHEN LTRIM(RTRIM($ColumDesc)) = '' THEN '$sinDesc' ELSE $ColumDesc END AS 'Desc'";
    }
    public function data($return = false)
    {
        $inicio = microtime(true);
        $datos = $this->validarInputsData();
        $conn = $this->conect->conn();
        try {

            $datosCod = (array_filter($datos['Cod'])); // Elimino los valores vacíos del array $datos['Cod']

            $whereConditions[] = " WHERE NovCodi > 0";
            $whereConditions[] = ($datos['Desc']) ? " NovDesc LIKE :Desc" : '';
            $whereConditions[] = ($datos['Tipo']) ? " NovTipo = :Tipo" : '';
            $whereConditions[] = ($datosCod) ? " NovCodi IN (" . implode(",", $datosCod) . ")" : '';
            $whereConditions = array_filter($whereConditions); // Elimino los valores vacíos del array $whereConditions

            $sql = "SELECT NovCodi, NovDesc, NovTipo, dbo.fn_TipoNovedad(NovTipo) as 'NovTipoDesc', NovID, FechaHora FROM NOVEDAD";
            $sql .= ($whereConditions) ? implode(" AND ", $whereConditions) : '';
            $sql .= " ORDER BY NovCodi";
            $sql .= " OFFSET $datos[start] ROWS FETCH NEXT $datos[length] ROWS ONLY"; // Paginación

            $stmt = $conn->prepare($sql);

            $desc = "%$datos[Desc]%";
            ($datos['Desc']) ? $stmt->bindParam("Desc", $desc, \PDO::PARAM_STR) : '';
            ($datos['Tipo']) ? $stmt->bindParam("Tipo", $datos["Tipo"], \PDO::PARAM_INT) : '';

            $stmt->execute(); // Ejecuto la consulta

            $novedades = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta

            $stmt->closeCursor(); // Cierro el cursor

            $sql = "SELECT COUNT(NovCodi) as 'Total' FROM NOVEDAD";
            $sql .= ($whereConditions) ? implode(" AND ", $whereConditions) : '';

            $stmt = $conn->prepare($sql);

            ($datos['Desc']) ? $stmt->bindParam("Desc", $desc, \PDO::PARAM_STR) : '';
            ($datos['Tipo']) ? $stmt->bindParam("Tipo", $datos["Tipo"], \PDO::PARAM_INT) : '';

            $stmt->execute(); // Ejecuto la consulta
            $total = $stmt->fetch(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $stmt->closeCursor(); // Cierro el cursor

            if ($return) {
                return $novedades;
            } else {
                $this->resp->respuesta($novedades, count($novedades), 'OK', 200, $inicio, $total['Total'], 0);
            }

        } catch (\PDOException $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_dataNovedades_' . ID_COMPANY . '.log');
            exit;
        }
    }
    private function validarInputsData()
    {
        $datos = $this->query;
        $start = $datos['start'] ?? ''; // Pagina de inicio si no viene en los datos
        $length = $datos['length'] ?? ''; // Cantidad de registros si no viene en los datos
        $tipo = $datos['Tipo'] ?? ''; // Tipo de datos que se reciben
        $desc = $datos['Desc'] ?? ''; // Descripción de la estructura
        $cod = $datos['Cod'] ?? ''; // Código de la estructura

        $datosRecibidos = array( // Valores por defecto
            'Cod' => empty($cod) ? [] : $cod,
            'Desc' => empty($desc) ? '' : $desc,
            'Tipo' => empty($tipo) ? '' : ($tipo),
            'start' => empty($start) ? 0 : ($start),
            'length' => empty($length) ? 5 : ($length),
        );

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'Cod' => ['arrSmallintEmpty'],
                'Desc' => ['varchar40'],
                'Tipo' => ['intempty'],
                'start' => ['intempty'],
                'length' => ['intempty'],
            ];

            $validator = new InputValidator($datosRecibidos, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos
            return $datosRecibidos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputs.log');
        }
    }
    /**
     * Obtiene el tipo de novedad correspondiente al código de novedad proporcionado.
     *
     * @param int $novCodi El código de novedad a buscar.
     * @param array $array El arreglo de elementos a buscar.
     * @return int|string El tipo de novedad correspondiente al código de novedad proporcionado, o 0 si no se encuentra.
     */
    private function obtenerTipoDeNovedad($novCodi, $array)
    {
        if (!$novCodi) {
            return 0;
        }
        if ($array) {
            foreach ($array as $elemento) {
                if (array_key_exists('NovCodi', $elemento) && array_key_exists('NovTipo', $elemento)) {
                    if ($elemento['NovCodi'] == $novCodi) {
                        return $elemento['NovTipo'];
                    }
                }
            }
        }
        return 0;
    }
    private function validarInputsTotales()
    {
        $datos = $this->getData;

        if ($this->tools->jsonNoValido()) {
            $errores = $this->tools->jsonNoValido();
            $this->resp->respuesta($errores, 0, "Formato JSON invalido", 400, microtime(true), 0, 0);
        }

        if (!$datos) {
            $this->resp->respuesta('', 0, "No se recibieron datos o hay errores", 400, microtime(true), 0, 0);
        }

        $FechIni = $datos['FechIni'] ?? '';
        $FechFin = $datos['FechFin'] ?? '';
        $LegApNo = $datos['LegApNo'] ?? '';
        $LegDocu = $datos['LegDocu'] ?? [];
        $LegRegCH = $datos['LegRegCH'] ?? [];
        $LegTipo = $datos['LegTipo'] ?? [];
        $LegaD = $datos['LegaD'] ?? '';
        $LegaH = $datos['LegaH'] ?? '';
        $Lega = $datos['Lega'] ?? [];
        $Empr = $datos['Empr'] ?? [];
        $Plan = $datos['Plan'] ?? [];
        $Conv = $datos['Conv'] ?? [];
        $Sec2 = $datos['Sec2'] ?? [];
        $Sect = $datos['Sect'] ?? [];
        $Grup = $datos['Grup'] ?? [];
        $Sucu = $datos['Sucu'] ?? [];
        $NovT = $datos['NovT'] ?? [];
        $NovS = $datos['NovS'] ?? [];
        $NovA = $datos['NovA'] ?? [];
        $NovI = $datos['NovI'] ?? [];
        $DiaL = $datos['DiaL'] ?? [];
        $DiaF = $datos['DiaF'] ?? [];
        $Nove = $datos['Nove'] ?? [];
        $NoTi = $datos['NoTi'] ?? [];
        $Estruct = $datos['Estruct'] ?? '';

        $start = $datos['start'] ?? ''; // Pagina de inicio si no viene en los datos
        $length = $datos['length'] ?? ''; // Cantidad de registros si no viene en los datos


        $datosRecibidos = array( // Valores por defecto
            'FechIni' => empty($FechIni) ? '' : $FechIni,
            'FechFin' => empty($FechFin) ? '' : $FechFin,
            'LegApNo' => empty($LegApNo) ? '' : $LegApNo,
            'LegDocu' => !is_array($LegDocu) ? [] : $LegDocu,
            'LegRegCH' => !is_array($LegRegCH) ? [] : $LegRegCH,
            'LegTipo' => !is_array($LegTipo) ? [] : $LegTipo,
            'LegaD' => empty($LegaD) ? '' : $LegaD,
            'LegaH' => empty($LegaH) ? '' : $LegaH,
            'Lega' => !is_array($Lega) ? [] : $Lega,
            'Empr' => !is_array($Empr) ? [] : $Empr,
            'Plan' => !is_array($Plan) ? [] : $Plan,
            'Conv' => !is_array($Conv) ? [] : $Conv,
            'Sec2' => !is_array($Sec2) ? [] : $Sec2,
            'Sect' => !is_array($Sect) ? [] : $Sect,
            'Grup' => !is_array($Grup) ? [] : $Grup,
            'Sucu' => !is_array($Sucu) ? [] : $Sucu,
            'NovT' => !is_array($NovT) ? [] : $NovT,
            'NovS' => !is_array($NovS) ? [] : $NovS,
            'NovA' => !is_array($NovA) ? [] : $NovA,
            'NovI' => !is_array($NovI) ? [] : $NovI,
            'DiaL' => !is_array($DiaL) ? [] : $DiaL,
            'DiaF' => !is_array($DiaF) ? [] : $DiaF,
            'Nove' => !is_array($Nove) ? [] : $Nove,
            'NoTi' => !is_array($NoTi) ? [] : $NoTi,
            'Estruct' => empty($Estruct) ? 0 : $Estruct,
            'start' => empty($start) ? 0 : ($start),
            'length' => empty($length) ? 5 : ($length),
        );

        try {

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 1);
            }

            $rules = [ // Reglas de validación
                'FechIni' => ['required', 'date'],
                'FechFin' => ['required', 'date'],
                'LegApNo' => ['varchar40'],
                'LegDocu' => ['arrInt'],
                'LegRegCH' => ['arrSmallint'],
                'LegTipo' => ['arrSmallint'],
                'LegaD' => ['intempty'],
                'LegaH' => ['intempty'],
                'Lega' => ['arrInt'],
                'Empr' => ['arrSmallint'],
                'Plan' => ['arrSmallint'],
                'Conv' => ['arrSmallint'],
                'Sec2' => ['arrSmallint'],
                'Sect' => ['arrSmallint'],
                'Grup' => ['arrSmallint'],
                'Sucu' => ['arrSmallint'],
                'NovT' => ['arrAllowed01'],
                'NovS' => ['arrAllowed01'],
                'NovA' => ['arrAllowed01'],
                'NovI' => ['arrAllowed01'],
                'DiaL' => ['arrAllowed01'],
                'DiaF' => ['arrAllowed01'],
                'Nove' => ['arrSmallint'],
                'NoTi' => ['arrSmallint'],
                'Estruct' => ['allowed01'],
                'start' => ['intempty'],
                'length' => ['intempty'],
            ];

            $validator = new InputValidator($datosRecibidos, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos
            return $datosRecibidos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputsTotales.log');
        }
    }
    public function totales()
    {
        $inicio = microtime(true);
        $datos = $this->validarInputsTotales(); // Valido los datos
        $conn = $this->conect->conn();

        try {

            $FechIni = date('Ymd', strtotime($datos['FechIni']));
            $FechFin = date('Ymd', strtotime($datos['FechFin']));

            if ($FechIni > $FechFin) {
                $this->resp->respuesta([], 0, 'La fecha de Inicio no puede ser mayor a la fecha de Fin', 400, $inicio, 0, 0);
                exit;
            }

            $LegApNo = $datos['LegApNo'] ?? '';
            $Empr = implode(",", $datos["Empr"]);
            $LegDocu = implode(",", $datos["LegDocu"]);
            $LegRegCH = implode(",", $datos["LegRegCH"]);
            $LegTipo = implode(",", $datos["LegTipo"]);
            $LegaD = $datos['LegaD'];
            $LegaH = $datos['LegaH'];
            $Lega = implode(",", $datos["Lega"]);
            $Plan = implode(",", $datos["Plan"]);
            $Conv = implode(",", $datos["Conv"]);
            $Sec2 = implode(",", $datos["Sec2"]);
            $Sect = implode(",", $datos["Sect"]);
            $Grup = implode(",", $datos["Grup"]);
            $Sucu = implode(",", $datos["Sucu"]);
            $NovT = implode(",", $datos["NovT"]);
            $NovS = implode(",", $datos["NovS"]);
            $NovA = implode(",", $datos["NovA"]);
            $NovI = implode(",", $datos["NovI"]);
            $DiaL = implode(",", $datos["DiaL"]);
            $DiaF = implode(",", $datos["DiaF"]);
            $Nove = implode(",", $datos["Nove"]);
            $NoTi = implode(",", $datos["NoTi"]);
            $Estruct = intval($datos['Estruct']); // Estructura a consultar 0 = Personal, 1 = Fichas.

            $wc[] = ($datos["LegApNo"]) ? " PERSONAL.LegApNo LIKE :LegApNo" : '';
            $wc[] = ($datos["LegDocu"]) ? " PERSONAL.LegDocu IN ($LegDocu)" : '';
            $wc[] = ($datos["LegRegCH"]) ? " PERSONAL.LegRegCH IN ($LegRegCH)" : '';
            $wc[] = ($datos["LegTipo"]) ? " PERSONAL.LegTipo IN ($LegTipo)" : '';
            $wc[] = ($datos["LegaD"]) ? " FICHAS.FicLega >= $LegaD" : '';
            $wc[] = ($datos["LegaH"]) ? " FICHAS.FicLega <= $LegaH" : '';
            $wc[] = ($datos["Lega"]) ? " FICHAS.FicLega IN ($Lega)" : '';
            $wc[] = ($datos["Empr"]) ? " FICHAS.FicEmpr IN ($Empr)" : '';
            $wc[] = ($datos["Plan"]) ? " FICHAS.FicPlan IN ($Plan)" : '';
            $wc[] = ($datos["Conv"]) ? " FICHAS.FicConv IN ($Conv)" : '';
            $wc[] = ($datos["Sec2"]) ? " FICHAS.FicSec2 IN ($Sec2)" : '';
            $wc[] = ($datos["Sect"]) ? " FICHAS.FicSect IN ($Sect)" : '';
            $wc[] = ($datos["Grup"]) ? " FICHAS.FicGrup IN ($Grup)" : '';
            $wc[] = ($datos["Sucu"]) ? " FICHAS.FicSucu IN ($Sucu)" : '';
            $wc[] = ($datos["NovT"]) ? " FICHAS.FicNovT IN ($NovT)" : '';
            $wc[] = ($datos["NovS"]) ? " FICHAS.FicNovS IN ($NovS)" : '';
            $wc[] = ($datos["NovA"]) ? " FICHAS.FicNovA IN ($NovA)" : '';
            $wc[] = ($datos["NovI"]) ? " FICHAS.FicNovI IN ($NovI)" : '';
            $wc[] = ($datos["DiaL"]) ? " FICHAS.FicDiaL IN ($DiaL)" : '';
            $wc[] = ($datos["DiaF"]) ? " FICHAS.FicDiaF IN ($DiaF)" : '';
            $wc[] = ($datos["Nove"]) ? " FICHAS3.FicNove IN ($Nove)" : '';
            $wc[] = ($datos["NoTi"]) ? " FICHAS3.FicNoTi IN ($NoTi)" : '';
            $wc = array_filter($wc); // Elimino los valores vacíos del array $wc
            $wc = array_values($wc); // Reordeno los índices del array $wc   

            /** vamos a consultar las novedades que hay en la tabla de novedades */
            $whereConditions = "";
            $whereConditions .= (count($wc) > 1) ? ' AND' . implode(" AND ", $wc) : "";
            $whereConditions .= (count($wc) === 1) ? ' AND' . $wc[0] : "";
            $sql = "SELECT DISTINCT(FicNove) as 'NovCodi', NovDesc, NovTipo FROM FICHAS3";
            $sql .= " INNER JOIN FICHAS ON FICHAS3.FicLega = FICHAS.FicLega AND FICHAS3.FicFech = FICHAS.FicFech AND FICHAS3.FicTurn = FICHAS.FicTurn";
            $sql .= " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume";
            $sql .= " INNER JOIN NOVEDAD ON FICHAS3.FicNove = NOVEDAD.NovCodi";
            $sql .= " WHERE FicNove > 0 AND FICHAS.FicFech BETWEEN '$FechIni' AND '$FechFin'";
            $sql .= $whereConditions;
            $sql .= " ORDER BY FicNove";

            // print_r($sql) . exit;

            $stmt = $conn->prepare($sql);
            $ApNo = "%$datos[LegApNo]%";
            ($datos['LegApNo']) ? $stmt->bindParam("LegApNo", $ApNo, \PDO::PARAM_STR) : '';
            $stmt->execute(); // Ejecuto la consulta
            $colNovedades = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta

            if (empty($colNovedades)) {
                $this->resp->respuesta([], 0, 'No se encontraron novedades', 200, $inicio, 0, 0);
                exit;
            }
            $countNoveCols = array_map(function ($v) {
                return "COUNT(CASE WHEN FICHAS3.FicNove = " . $v['NovCodi'] . " THEN 1 END) as 'Total_" . $v['NovCodi'] . "'";
            }, $colNovedades);

            $sumNoveCols = array_map(function ($v) {
                return "COALESCE(SUM(CASE WHEN FICHAS3.FicNove = " . $v['NovCodi'] . " THEN dbo.fn_STRMinutos(FICHAS3.FicHoras) END), 0) as 'Horas_" . $v['NovCodi'] . "'";

            }, $colNovedades);

            /** **********************************  */

            $columnas = [
                "FICHAS3.FicLega AS 'Lega'",
                "PERSONAL.LegApNo AS 'LegApNo'",
            ];

            $columnasEstruct = [
                "PERSONAL.LegEmpr AS 'Empr'",
                "PERSONAL.LegPlan AS 'Plan'",
                "PERSONAL.LegConv AS 'Conv'",
                "PERSONAL.LegSect AS 'Sect'",
                "PERSONAL.LegSec2 AS 'Secc'",
                "PERSONAL.LegGrup AS 'Grup'",
                "PERSONAL.LegSucu AS 'Sucu'"
            ];
            $groupByEstruct = [
                "PERSONAL.LegEmpr",
                "PERSONAL.LegPlan",
                "PERSONAL.LegConv",
                "PERSONAL.LegSect",
                "PERSONAL.LegSec2",
                "PERSONAL.LegGrup",
                "PERSONAL.LegSucu"
            ];

            if ($Estruct === 1) {
                unset($columnasEstruct);
                unset($groupByEstruct);
                $columnasEstruct = [
                    "FICHAS.FicEmpr AS 'Empr'",
                    "FICHAS.FicPlan AS 'Plan'",
                    "FICHAS.FicConv AS 'Conv'",
                    "FICHAS.FicSect AS 'Sect'",
                    "FICHAS.FicSec2 AS 'Secc'",
                    "FICHAS.FicGrup AS 'Grup'",
                    "FICHAS.FicSucu AS 'Sucu'"
                ];
                $groupByEstruct = [
                    "FICHAS.FicEmpr",
                    "FICHAS.FicPlan",
                    "FICHAS.FicConv",
                    "FICHAS.FicSect",
                    "FICHAS.FicSec2",
                    "FICHAS.FicGrup",
                    "FICHAS.FicSucu"
                ];
            }

            $groupByEstruct = implode(", ", $groupByEstruct);
            $columnasEstruct = implode(", ", $columnasEstruct);

            $columnas2 = implode(", ", $countNoveCols);
            $columnas3 = implode(", ", $sumNoveCols);
            $columnas = implode(", ", $columnas);
            $sql = "SELECT $columnas, $columnasEstruct, $columnas2, $columnas3 FROM FICHAS3";
            $sql .= " INNER JOIN FICHAS ON FICHAS3.FicLega = FICHAS.FicLega AND FICHAS3.FicFech = FICHAS.FicFech AND FICHAS3.FicTurn = FICHAS.FicTurn";
            $sql .= " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume";
            $sql .= " WHERE FicNove > 0";
            $sql .= " AND FICHAS3.FicFech BETWEEN '$FechIni' AND '$FechFin'";
            $sql .= $whereConditions;
            $sql .= " GROUP BY FICHAS3.FicLega, PERSONAL.LegApNo";
            $sql .= ", $groupByEstruct";
            $sql .= " ORDER BY FICHAS3.FicLega";
            $sql .= " OFFSET $datos[start] ROWS FETCH NEXT $datos[length] ROWS ONLY"; // Paginación

            $stmt1 = $conn->prepare($sql);
            ($datos['LegApNo']) ? $stmt1->bindParam("LegApNo", $ApNo, \PDO::PARAM_STR) : '';
            $stmt1->execute(); // Ejecuto la consulta
            $novedades = $stmt1->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta

            // $sql = "SELECT COUNT(DISTINCT(FICHAS.FicLega)) as 'Total' FROM FICHAS";
            $sql = "SELECT COUNT(DISTINCT CONCAT($groupByEstruct)) AS 'Total' FROM FICHAS";
            $sql .= " INNER JOIN FICHAS3 ON FICHAS.FicLega = FICHAS3.FicLega AND FICHAS.FicFech = FICHAS3.FicFech AND FICHAS.FicTurn = FICHAS3.FicTurn";
            $sql .= " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume";
            $sql .= " WHERE FicNove > 0";
            $sql .= " AND FICHAS3.FicFech BETWEEN '$FechIni' AND '$FechFin'";
            $sql .= $whereConditions;
            $stmt2 = $conn->prepare($sql);
            // $this->bindParameters($stmt2, $params);
            ($datos['LegApNo']) ? $stmt2->bindParam("LegApNo", $ApNo, \PDO::PARAM_STR) : '';
            $stmt2->execute(); // Ejecuto la consulta
            $total = $stmt2->fetch(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $stmt->closeCursor(); // Cierro el cursor
            $stmt1->closeCursor(); // Cierro el cursor
            $stmt2->closeCursor(); // Cierro el cursor

            $nuevo_array = array();

            // Recorremos el array original y reestructuramos los datos
            foreach ($novedades as $elemento) {
                $nuevo_elemento = array(
                    'Lega' => $elemento['Lega'],
                    'LegApNo' => $elemento['LegApNo'],
                    'Empr' => intval($elemento['Empr']),
                    'Plan' => intval($elemento['Plan']),
                    'Conv' => intval($elemento['Conv']),
                    'Sect' => intval($elemento['Sect']),
                    'Secc' => intval($elemento['Secc']),
                    'Grup' => intval($elemento['Grup']),
                    'Sucu' => intval($elemento['Sucu']),
                    'Totales' => array()
                );

                // Buscamos las claves dinámicas que comienzan con 'Total_'
                foreach ($elemento as $clave => $valor) {
                    if (strpos($clave, 'Total_') === 0) {
                        // Extraemos el número dinámico de la clave
                        $numero = substr($clave, 6); // Se asume que siempre hay 6 caracteres fijos antes del número
                        $FiltroNovedades = array_values($this->tools->filtrarElementoArray($colNovedades, 'NovCodi', ($numero)));
                        if (intval($valor) == 0) {
                            continue;
                        }

                        $horasEnDecimal = $this->minutosAHorasDecimal(intval($elemento['Horas_' . $numero]));

                        $nuevo_elemento['Totales'][] = array(
                            'NovCodi' => intval($numero),
                            'NovDesc' => $FiltroNovedades[0]['NovDesc'],
                            'Cantidad' => intval($valor),
                            'EnHoras' => $this->minutosAHoras(intval($elemento['Horas_' . $numero])),
                            'EnMinutos' => intval($elemento['Horas_' . $numero]),
                            'EnHorasDecimal' => $horasEnDecimal,
                        );
                    }
                }

                $nuevo_array[] = $nuevo_elemento;
            }

            foreach ($colNovedades as $key => $value) {
                $nov[] = array(
                    'NovCodi' => intval($value['NovCodi']),
                    'NovDesc' => trim($value['NovDesc']),
                    'NovTipo' => intval($value['NovTipo']),
                );
            }


            $sumas = array();

            foreach ($nuevo_array as $empleado) {
                foreach ($empleado["Totales"] as $suma) {
                    $NovCodi = $suma["NovCodi"];
                    $NovDesc = $suma["NovDesc"];
                    $cantidad = $suma["Cantidad"];
                    $enMinutos = $suma["EnMinutos"];
                    $enHoras = $suma["EnHoras"];
                    $EnHorasDecimal = $suma["EnHorasDecimal"];

                    // Si el NovCodi ya existe en el array de sumas, sumamos los valores, de lo contrario lo inicializamos
                    if (array_key_exists($NovCodi, $sumas)) {
                        $sumas[$NovCodi]["Cantidad"] += $cantidad;
                        $sumas[$NovCodi]["EnMinutos"] += $enMinutos;
                        $sumas[$NovCodi]["EnHoras"] = $this->minutosAHoras(intval($sumas[$NovCodi]["EnMinutos"]));
                        $sumas[$NovCodi]["EnHorasDecimal"] = $this->minutosAHorasDecimal(intval($sumas[$NovCodi]["EnMinutos"]));
                    } else {
                        $sumas[$NovCodi] = array(
                            "NovCodi" => $NovCodi,
                            "NovDesc" => $NovDesc,
                            "Cantidad" => $cantidad,
                            "EnMinutos" => $enMinutos,
                            "EnHoras" => $enHoras,
                            "EnHorasDecimal" => $EnHorasDecimal,
                        );

                        // sort $sumas by NovCodi
                        ksort($sumas);
                    }
                }
            }

            $array = [
                'totales' => array_values($sumas),
                'data' => $nuevo_array,
                'novedades' => $nov,
            ];

            $this->resp->respuesta($array, count($novedades), 'OK', 200, $inicio, $total['Total'], 0);

        } catch (\PDOException $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_totalesNovedades_' . ID_COMPANY . '.log');
            exit;
        }
    }
    private function bindParameters($stmt, $params)
    {
        foreach ($params as $param => $info) {
            if ($info !== null) { // Verifica si el valor del parámetro no es null
                $value = $info['value'];
                $type = $info['type'];
                $stmt->bindParam($param, $value, $type);
            }
            unset($param, $info, $value, $type);
        }
    }
    private function minutosAHoras($minutos)
    {
        $horas = floor($minutos / 60);
        $minutos = $minutos % 60;
        return sprintf("%02d:%02d", $horas, $minutos);
    }
    private function round_down($number, $precision = 2)
    {
        $fig = (int) str_pad('1', $precision, '0');
        return(floor($number * $fig) / $fig);
    }
    private function minutosAHorasDecimal($minutos)
    {
        $horas = floor($minutos / 60);
        $minutosRestantes = $minutos % 60;
        $minutosDecimal = $minutosRestantes / 60.0;
        return $horas + $minutosDecimal;
    }
}