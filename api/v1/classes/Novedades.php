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
                    // $this->webservice->procesar_legajos($Legajos, $Desde, $Hasta);
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    return false;
                }
            }
            $this->resp->response([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->response('', 0, $e->getMessage(), 400, $inicio, 0, 0);
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
                    $this->webservice->procesar_legajos($Legajos, $Desde, $Hasta);
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    return false;
                }
            }
            $this->resp->response([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->response('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_addNovedades_' . ID_COMPANY . '.log');
            exit;
        }
    }
    public function delete()
    {
        $inicio = microtime(true);
        $datos = $this->validarInputsDelete();

        $conn = $this->conect->conn();

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
                    // $this->webservice->procesar_legajos($Legajos, $Desde, $Hasta);
                } catch (\Exception $e) {
                    $this->log->write($e->getMessage(), date('Ymd') . '_procesar_legajos_' . ID_COMPANY . '.log');
                    return false;
                }
            }
            $this->resp->response([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->response('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_deleteNovedades_' . ID_COMPANY . '.log');
            exit;
        }
    }
    private function validarInputs()
    {
        $datos = $this->getData;
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
                    if (!array_key_exists($keyD, $dato) || empty($dato[$keyD])) { // Si no existe la clave en el array $dato o esta vacio
                        $dato[$keyD] = $customValueKey[$keyD]; // Le asigno el valor por defecto del array $customValueKey
                    }
                }
                $datosModificados[] = $dato; // Guardo los datos modificados en un array
                $validator = new InputValidator($dato, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validacion del array $rules
                $validator->validate(); // Valido los datos
            }
            return $datosModificados;
            // $this->resp->response($datosModificados, 0, 'Todo bien con los datos', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->response('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputs.log');
        }
    }
    private function validarInputsAdd()
    {
        $datos = $this->getData;
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
                    if (!array_key_exists($keyD, $dato) || empty($dato[$keyD])) { // Si no existe la clave en el array $dato o esta vacio
                        $dato[$keyD] = $customValueKey[$keyD]; // Le asigno el valor por defecto del array $customValueKey
                    }
                }
                $datosModificados[] = $dato; // Guardo los datos modificados en un array
                $validator = new InputValidator($dato, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validacion del array $rules
                $validator->validate(); // Valido los datos
            }
            return $datosModificados;
            // $this->resp->response($datosModificados, 0, 'Todo bien con los datos', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->response('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputsAdd.log');
        }
    }
    private function validarInputsDelete()
    {
        $datos = $this->getData;
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
            // $this->resp->response($datosModificados, 0, 'Todo bien con los datos', 200, microtime(true), 0, 0);
        } catch (\Exception $e) {
            $this->resp->response('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputsDelete.log');
        }
    }
    public function estruct($estruct)
    {
        $estruct = strtolower($estruct); // Convierto a minuscula
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
            $parametros = [$ApNo, $ApNoLega, $Docu, $Tipo, $RegCH, $Tare]; // Parámetros que se utilizan para el INNER JOIN PERSONAL
            $estructTare = in_array($estruct, $estructuras); // Si la estructura esta en el array $estructuras
            $paramPers = $estructTare || in_array(true, $parametros); // Si la estructura esta en el array $estructuras o si algún parámetro esta en el array $parámetros
            /** FIN Fragmento */
            /** SELECT */
            $sql = "SELECT";
            $sql .= $this->columnsEstruct($estruct); // Columnas de la estructura, campos descripcion, codigo y cantidad
            $sql .= " FROM FICHAS";
            $sql .= $this->joinFichas3Estruct(); // Join con la tabla FICHAS3 (NOVEDADES)

            /** JOIN PERSONAL */
            $sql .= $this->joinPersonalEstruct($paramPers); // Join con la tabla PERSONAL
            /** JOIN ESTRUCTURA */
            $sql .= $this->joinEstruct($estruct); // Join con la tabla de la estructura

            /** QUERY TABLA FICHAS */
            $sql .= $this->queryFichasEstruct($Lega, $Empr, $Plan, $Conv, $Sect, $Sec2, $Grup, $Sucu, $NovI, $NovA, $NovS, $NovT, $DiaL, $DiaF);

            /** QUERY ESTRUCTURA */
            $sql .= $this->queryEstructDesc($estruct, $Desc); // Descripcion de la estructura
            $sql .= $this->queryEstructCod($estruct, $Cod); // Codigo de la estructura
            $sql .= $this->queryEstructSect($estruct, $Sector); // Codigo de la estructura

            /** QUERY TABLA FICHAS3 (NOVEDADES) */
            $sql .= $this->queryFichas3Estruct($Nove, $NoveTipo, $Esta);

            /** QUERY TABLA PERSONAL */
            $sql .= $this->queryPersonalEstruct($paramPers, $ApNo, $ApNoLega, $Docu, $Tipo, $RegCH, $Tare);

            /** GROUP, ORDER BY, PAGINACION */
            $sql .= $this->groupByEstruct($estruct); // Group By de la estructura
            $sql .= " OFFSET :start ROWS FETCH NEXT :length ROWS ONLY"; // Paginacion
            // print_r($sql) . exit;
            /** PARAMETROS DE LA CONSULTA SQL */
            $params = [
                ':FechaIni' => $FechaIni,
                ':FechaFin' => $FechaFin,
                ':start' => intval($start),
                ':length' => intval($length)
            ];
            ($Desc) ? $params[':Desc'] = '%' . $Desc . '%' : '';
            ($paramPers && $ApNo) ? $params[':ApNo'] = '%' . $ApNo . '%' : '';
            ($paramPers && $ApNoLega) ? $params[':ApNoLega'] = '%' . $ApNoLega . '%' : '';
            /**  FIN DE PARAMETROS DE LA CONSULTA SQL */
            // print_r($params) . exit;
            $data = $this->conect->executeQueryWhithParams($sql, $params);
            $total = count($data);
            $this->resp->response($data, $total, 'OK', 200, $inicio, 0, 0);
        } catch (\Exception $th) {
            $this->resp->response('', 0, $th->getMessage(), $th->getCode(), $inicio, 0, 0);
            exit;
        }
    }
    private function getEstruct($estruct)
    {
        $arrValidEstruct = ["empr", "plan", "grup", "sect", "sec2", "sucu", "tare", "conv", "regla", "nove", "novetipo", "lega", "tipo"];

        if (!in_array(strtolower($estruct), $arrValidEstruct)) { // Si la estructura no es valida
            $this->resp->response('', 0, "Estructura ($estruct) no valida. Valores permitidos " . json_encode($arrValidEstruct), 400, microtime(true), 0, 0);
            exit;
        }

        $rules = [ // Reglas de validacion
            'Cod' => ['arrInt'], // Codigo de la estructura
            'Desc' => ['varchar40'], // Descripcion de la estructura
            'Sector' => ['arrSmallint'], // Sector
            'Docu' => ['arrInt'], // DNI Del Legajo
            'Lega' => ['arrInt'], // Legajo
            'ApNo' => ['varchar40'], // Apellido y Nombre
            'ApNoLega' => ['varchar40'], // Apellido y Nombre del legajo
            'Empr' => ['arrSmallint'], // Empresa
            'Plan' => ['arrSmallint'], // Planta
            'Conv' => ['arrSmallint'], // Convenio
            'Sect' => ['arrSmallint'], // Seccion
            'Sec2' => ['arrSmallint'], // Seccion 2
            'Grup' => ['arrSmallint'], // Grupo
            'Sucu' => ['arrSmallint'], // Sucursal
            'TareProd' => ['arrSmallint'], // Tarea de produccion
            'RegCH' => ['arrSmallint'], // Regla de Control Horario
            'Tipo' => ['arrSmallint'], // Tipo de Personal
            'FechaIni' => ['required', 'date'], // Fecha de inicio
            'FechaFin' => ['required', 'date'], // Fecha de fin
            'Esta' => ['arrAllowed012'], // Estado de la ficha novedad (FicEsta)
            'start' => ['intempty'], // Pagina de inicio
            'length' => ['intempty'], // Cantidad de registros
            'Nove' => ['arrSmallint'], // Codigo novedad
            'NoveTipo' => ['arrSmallint'], // Codigo tipo novedad
            'NovI' => ['arrAllowed01'], // Si Novedad Inclumiento
            'NovA' => ['arrAllowed01'], // Si Novedad Ausentismo
            'NovS' => ['arrAllowed01'], // Si Novedad Salida anticipada
            'NovT' => ['arrAllowed01'], // Si Novedad Tardanza
            'DiaL' => ['arrAllowed01'], // Si Dia laboral
            'DiaF' => ['arrAllowed01'], // Si Dia feriado
        ];

        try {
            $datos = ($this->getData);

            if ($estruct == 'sec2' && empty($datos['Sector'] ?? '')) {
                throw new \Exception("Parámetro Sector es requerido cuando solicita estruct sección (sec2).", 1);
            }

            $datos['start'] = $datos['start'] ?? 0; // Pagina de inicio si no viene en los datos
            $datos['length'] = $datos['length'] ?? 5; // Cantidad de registros si no viene en los datos
            $datos['FechaIni'] = $datos['FechaIni'] ?? date('Y-m-d'); // Fecha de inicio si no viene en los datos
            $datos['FechaFin'] = $datos['FechaFin'] ?? date('Y-m-d'); // Fecha de fin si no viene en los datos

            $validator = new InputValidator($datos, $rules); // Instancio la clase InputValidator y le paso los datos y las reglas de validacion del array $rules
            $validator->validate(); // Valido los datos

            $keyString = ['ApNo', 'ApNoLega', 'Desc'];

            foreach ($keyString as $key) {
                $datos[$key] = $datos[$key] ?? ''; // Si no existe la clave en el array $datos le asigno un string vacio
            }

            $keysArray = ['Sector', 'Cod', 'Lega', 'Docu', 'Empr', 'Plan', 'Conv', 'Sect', 'Sec2', 'Grup', 'Sucu', 'TareProd', 'RegCH', 'Tipo', 'Nove', 'NoveTipo', 'Esta', 'NovI', 'NovA', 'NovS', 'NovT', 'DiaL', 'DiaF'];

            foreach ($keysArray as $key) { // Recorro las claves del array $keysArray
                $datos[$key] = $datos[$key] ?? []; // Si no existe la clave en el array $datos le asigno un array vacio
            }
        } catch (\Throwable $th) {
            $this->resp->response('', 0, $th->getMessage(), 400, microtime(true), 0, 0);
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
        return ($paramPers) ? " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume" : '';
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
            if (!empty($Filtros[$Nombre])) { // Si el filtro no esta vacio
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

            $datosCod = (array_filter($datos['Cod'])); // Elimino los valores vacios del array $datos['Cod']

            $whereConditions[] = " WHERE NovCodi > 0";
            $whereConditions[] = ($datos['Desc']) ? " NovDesc LIKE :Desc" : '';
            $whereConditions[] = ($datos['Tipo']) ? " NovTipo = :Tipo" : '';
            $whereConditions[] = ($datosCod) ? " NovCodi IN (" . implode(",", $datosCod) . ")" : '';
            $whereConditions = array_filter($whereConditions); // Elimino los valores vacios del array $whereConditions

            $sql = "SELECT NovCodi, NovDesc, NovTipo, dbo.fn_TipoNovedad(NovTipo) as 'NovTipoDesc', NovID, FechaHora FROM NOVEDAD";
            $sql .= ($whereConditions) ? implode(" AND ", $whereConditions) : '';
            $sql .= " ORDER BY NovCodi";
            $sql .= " OFFSET $datos[start] ROWS FETCH NEXT $datos[length] ROWS ONLY"; // Paginacion

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
                $this->resp->response($novedades, count($novedades), 'OK', 200, $inicio, $total['Total'], 0);
            }

        } catch (\PDOException $e) {
            $this->resp->response('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_updateHoras_' . ID_COMPANY . '.log');
            exit;
        }
    }
    private function validarInputsData()
    {
        $datos = $this->query;
        $start = $datos['start'] ?? ''; // Pagina de inicio si no viene en los datos
        $length = $datos['length'] ?? ''; // Cantidad de registros si no viene en los datos
        $tipo = $datos['Tipo'] ?? ''; // Tipo de datos que se reciben
        $desc = $datos['Desc'] ?? ''; // Descripcion de la estructura
        $cod = $datos['Cod'] ?? ''; // Codigo de la estructura

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

            $rules = [ // Reglas de validacion
                'Cod' => ['arrSmallintEmpty'],
                'Desc' => ['varchar40'],
                'Tipo' => ['intempty'],
                'start' => ['intempty'],
                'length' => ['intempty'],
            ];

            $validator = new InputValidator($datosRecibidos, $rules); // Instancio la clase InputValidator y le paso los datos y las reglas de validacion del array $rules
            $validator->validate(); // Valido los datos
            return $datosRecibidos;
        } catch (\Exception $e) {
            $this->resp->response('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
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
}
