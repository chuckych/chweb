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

class Horas
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
        $conn = $this->conect->conn();
        $FechaHoraActual = $this->conect->FechaHora(); // Fecha y hora actual
        try {
            $conn->beginTransaction(); // Iniciar transacción

            $sql = "UPDATE FICHAS1 SET FicHsAu2 = :HsAu, FicEsta = :Esta, FicCaus = :Moti, FicObse = :Obse, FechaHora = :FechaHora, FicValor = :Valor WHERE FicLega = :Lega AND FicFech = :Fecha AND FicTurn = 1 AND FicHora = :Hora";
            $stmt = $conn->prepare($sql);

            $totalAffectedRows = 0;

            foreach ($datos as $dato) { // Recorro los datos
                $dato['Fecha'] = date('Ymd', strtotime($dato['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $stmt->bindValue(':HsAu', $dato['HsAu'], \PDO::PARAM_STR);
                $stmt->bindValue(':Esta', $dato['Esta'], \PDO::PARAM_INT);
                $stmt->bindValue(':Moti', $dato['Moti'], \PDO::PARAM_INT);
                $stmt->bindValue(':Obse', $dato['Obse'], \PDO::PARAM_STR);
                $stmt->bindValue(':FechaHora', $FechaHoraActual, \PDO::PARAM_STR);
                $stmt->bindValue(':Lega', $dato['Lega'], \PDO::PARAM_INT);
                $stmt->bindValue(':Fecha', $dato['Fecha'], \PDO::PARAM_STR);
                $stmt->bindValue(':Hora', $dato['Hora'], \PDO::PARAM_INT);
                $stmt->bindValue(':Valor', $dato['Valor'], \PDO::PARAM_STR);
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
            $this->resp->respuesta([], $totalAffectedRows, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $conn->rollBack();
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_updateHoras_' . ID_COMPANY . '.log');
            exit;
        }
    }
    private function procpend($datos)
    {
        $conn = $this->conect->conn();
        $FechaHoraActual = $this->conect->FechaHora(); // Fecha y hora actual
        try {
            if (empty($datos)) {
                return false;
            }
            foreach ($datos as $dato) {
                $dato['Fecha'] = date('Ymd', strtotime($dato['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $sql = "INSERT INTO PROCPEND (PrPeTipo, PrPeLega, PrPeFech, FechaHora) VALUES (0, '$dato[Lega]',  '$dato[Fecha]', '$FechaHoraActual')";
                $stmt = $conn->prepare($sql);
                $stmt->execute(); // Ejecuto la consulta
            }
            // $stmt = null;
            $this->conect = null;
        } catch (\PDOException $e) {
            // $conn->rollBack();
            // $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            // $this->log->write($e->getMessage(), date('Ymd') . '_procPend_' . ID_COMPANY . '.log');
            // exit;
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
                'Hora' => ['required', 'smallint'],
                'HsAu' => ['required', 'time'],
                'Esta' => ['allowed012'],
                'Obse' => ['varchar40'],
                'Moti' => ['smallint'],
                'Valor' => ['decima12.2'],
            ];

            $FechaHoraActual = date('YmdHis') . substr((string) microtime(), 1, 8); // Fecha y hora actual
            $customValueKey = array( // Valores por defecto
                'Lega' => "0",
                'Fecha' => '00000000',
                'Hora' => "0",
                'HsAu' => '00:00',
                'Esta' => "2",
                'Obse' => '',
                'Moti' => "0",
                'Valor' => "0",
                'FechaHora' => ''
            );
            $keyData = array_keys($customValueKey); // Obtengo las claves del array $customValueKey

            foreach ($datos as $dato) { // Recorro los datos recibidos
                foreach ($keyData as $keyD) { // Recorro las claves del array $customValueKey
                    if (!array_key_exists($keyD, $dato) || empty($dato[$keyD])) { // Si no existe la clave en el array $dato o esta vacio
                        $dato[$keyD] = $customValueKey[$keyD]; // Le asigno el valor por defecto del array $customValueKey
                    }
                }
                $datosModificados[] = $dato; // Guardo los datos modificados en un array
                $validator = new InputValidator($dato, $rules); // Instancio la clase InputValidator y le paso los datos y las reglas de validacion del array $rules
                $validator->validate(); // Valido los datos
            }
            return $datosModificados;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputs.log');
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
            $THora = ($datos['THora']); // Tipo de Hora
            $Esta = ($datos['Esta']); // Estado de la ficha hora (FicEsta)
            $Docu = ($datos['Docu']); // DNI Del Legajo
            $Sector = ($datos['Sector']); // Sector
            $DiaL = ($datos['DiaL']); // Si Día Laboral
            $DiaF = ($datos['DiaF']); // Si Día Feriado

            $start = $datos['start']; // Pagina de inicio
            $length = $datos['length']; // Cantidad de registros
            $Desc = $datos['Desc']; // Descripción de la estructura
            $ApNo = $datos['ApNo']; // Apellido y Nombre
            $ApNoLega = $datos['ApNoLega']; // Apellido y Nombre + legajo
            $HoraMin = $datos['HoraMin']; // Hora minima
            $HoraMax = $datos['HoraMax']; // Hora maxima
            $HsTr = $datos['HsTr']; // Solo horas trabajadas

            /** De este Fragmento de código se define si paramsPers es true o false. Si es true se utiliza para el INNER JOIN  PERSONAL */
            $estructuras = ['tare', 'regla', 'lega', 'tipo']; // Estructuras que se utilizan para el INNER JOIN PERSONAL
            $parametros = [$ApNo, $ApNoLega, $Docu, $Tipo, $RegCH, $Tare]; // Parámetros que se utilizan para el INNER JOIN PERSONAL
            $estructTare = in_array($estruct, $estructuras); // Si la estructura esta en el array $estructuras
            $paramPers = $estructTare || in_array(true, $parametros); // Si la estructura esta en el array $estructuras o si algún parámetro esta en el array $parámetros
            /** FIN Fragmento */

            /** SELECT */
            $sql = "SELECT";
            $sql .= $this->columnsEstruct($estruct); // Columnas de la estructura, campos descripción, código y cantidad
            $sql .= " FROM FICHAS";
            $sql .= $this->joinFichas1Estruct(); // Join con la tabla FICHAS1 (HORAS)

            /** JOIN PERSONAL */
            $sql .= $this->joinPersonalEstruct($paramPers); // Join con la tabla PERSONAL

            /** JOIN ESTRUCTURA */
            $sql .= $this->joinEstruct($estruct); // Join con la tabla de la estructura

            /** QUERY TABLA FICHAS */
            $sql .= $this->queryFichasEstruct($Lega, $Empr, $Plan, $Conv, $Sect, $Sec2, $Grup, $Sucu, $DiaL, $DiaF);

            /** QUERY ESTRUCTURA */
            $sql .= $this->queryEstructDesc($estruct, $Desc); // Descripción de la estructura
            $sql .= $this->queryEstructCod($estruct, $Cod); // Código de la estructura
            $sql .= $this->queryEstructSect($estruct, $Sector); // Código de la estructura

            /** QUERY TABLA FICHAS1 (HORAS) */
            $sql .= $this->queryFichas1Estruct($THora, $Esta, $HoraMin, $HoraMax, $HsTr);

            /** QUERY TABLA PERSONAL */
            $sql .= $this->queryPersonalEstruct($paramPers, $ApNo, $ApNoLega, $Docu, $Tipo, $RegCH, $Tare);

            /** GROUP, ORDER BY, PAGINACIÓN */
            $sql .= $this->groupByEstruct($estruct); // Group By de la estructura
            // print_r($sql) . exit;
            $sql .= " OFFSET :start ROWS FETCH NEXT :length ROWS ONLY"; // Paginación
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
        $arrValidEstruct = ["empr", "plan", "grup", "sect", "sec2", "sucu", "tare", "conv", "regla", "thora", "lega", "tipo"];
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
            'THora' => ['arrSmallint'], // Tipo de Hora
            'Esta' => ['arrAllowed012'], // Estado de la ficha hora (FicEsta)
            'FechaIni' => ['required', 'date'], // Fecha de inicio
            'FechaFin' => ['required', 'date'], // Fecha de fin
            'HoraMax' => ['time'], // Hora maxima
            'HoraMin' => ['time'], // Hora minima
            'DiaL' => ['arrAllowed01'], // Si Dia laboral
            'DiaF' => ['arrAllowed01'], // Si Dia feriado
            'HsTr' => ['allowed01'], // Solo horas trabajadas
            'start' => ['intempty'], // Pagina de inicio
            'length' => ['intempty'] // Cantidad de registros
        ];

        try {
            $datos = ($this->getData);

            if ($estruct == 'sec2' && empty($datos['Sector'] ?? '')) {
                throw new \Exception("Parámetro Sector es requerido cuando solicita estruct sección (sec2).", 1);
            }

            $datos['HsTr'] = $datos['HsTr'] ?? "0"; // Solo horas trabajadas. 0 = No, 1 = Si
            $datos['HoraMin'] = $datos['HoraMin'] ?? ''; // Hora minima
            $datos['HoraMax'] = $datos['HoraMax'] ?? ''; // Hora maxima
            $datos['start'] = $datos['start'] ?? 0; // Pagina de inicio si no viene en los datos
            $datos['length'] = $datos['length'] ?? 5; // Cantidad de registros si no viene en los datos
            $datos['FechaIni'] = $datos['FechaIni'] ?? date('Y-m-d'); // Fecha de inicio si no viene en los datos
            $datos['FechaFin'] = $datos['FechaFin'] ?? date('Y-m-d'); // Fecha de fin si no viene en los datos

            $validator = new InputValidator($datos, $rules); // Instancio la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos

            $keyString = ['ApNo', 'ApNoLega', 'Desc'];

            foreach ($keyString as $key) {
                $datos[$key] = $datos[$key] ?? ''; // Si no existe la clave en el array $datos le asigno un string vacio
            }

            $keysArray = ['Sector', 'Cod', 'Lega', 'Docu', 'Empr', 'Plan', 'Conv', 'Sect', 'Sec2', 'Grup', 'Sucu', 'TareProd', 'RegCH', 'Tipo', 'THora', 'Esta', 'DiaL', 'DiaF'];

            foreach ($keysArray as $key) { // Recorro las claves del array $keysArray
                $datos[$key] = $datos[$key] ?? []; // Si no existe la clave en el array $datos le asigno un array vacio
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
            'thora' => " INNER JOIN TIPOHORA ON FICHAS1.FicHora = TIPOHORA.THoCodi",
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
            'thora' => $this->caseWhen('THoDesc', "Sin Tipo de Hora") . ", FicHora AS '$cod', count(FicHora) AS '$count'",
            'lega' => $this->caseWhen('LegApNo', "Sin Nombre") . ", FICHAS.FicLega AS '$cod', count(FICHAS.FicLega) AS '$count'",
            'tipo' => " dbo.fn_TipoDePersonal(LegTipo) as 'Desc', LegTipo AS '$cod', count(LegTipo) AS '$count'",
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
            'thora' => " $group THoDesc, FicHora $order THoDesc",
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
            'thora' => " AND FICHAS1.FicHora IN (" . implode(",", $Cod) . ")",
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
    private function joinFichas1Estruct()
    {
        return " INNER JOIN FICHAS1 ON FICHAS.FicLega = FICHAS1.FicLega AND FICHAS.FicFech = FICHAS1.FicFech AND FICHAS.FicTurn = FICHAS1.FicTurn";
    }
    private function queryFichasEstruct($Lega, $Empr, $Plan, $Conv, $Sect, $Sec2, $Grup, $Sucu, $DiaL, $DiaF)
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
            'DiaL' => $DiaL,
            'DiaF' => $DiaF
        ];

        $sql = " WHERE FICHAS.FicLega > 0 AND FICHAS.FicFech BETWEEN :FechaIni AND :FechaFin";

        $FiltrosNombres = ['Lega', 'Empr', 'Plan', 'Conv', 'Sect', 'Sec2', 'Grup', 'Sucu', 'NoveI', 'NoveA', 'NoveS', 'NoveT', 'DiaL', 'DiaF'];

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
    private function queryFichas1Estruct($THora, $Esta, $HoraMin, $HoraMax, $HsTr)
    {
        $sql = '';
        $sql .= ($THora) ? " AND FICHAS1.FicHora IN (" . implode(",", $THora) . ")" : ''; // Tipo de Hora
        $sql .= ($Esta) ? " AND FICHAS1.FicEsta IN (" . implode(",", $Esta) . ")" : ''; // Estado de la ficha hora (FicEsta)
        $sql .= ($HoraMin) ? " AND dbo.fn_STRMinutos(FICHAS1.FicHsAu) >= dbo.fn_STRMinutos('$HoraMin')" : ''; // Hora minima
        $sql .= ($HoraMax) ? " AND dbo.fn_STRMinutos(FICHAS1.FicHsAu) <= dbo.fn_STRMinutos('$HoraMax')" : ''; // Hora maxima
        $sql .= ($HsTr) ? "AND dbo.fn_STRMinutos(FICHAS.FicHstr) > 0" : ''; // Solo registros con horas trabajadas
        return $sql;
    }
    private function caseWhen($ColumDesc, $sinDesc)
    {
        return " CASE WHEN LTRIM(RTRIM($ColumDesc)) = '' THEN '$sinDesc' ELSE $ColumDesc END AS 'Desc'";
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
        $DiaL = $datos['DiaL'] ?? [];
        $DiaF = $datos['DiaF'] ?? [];
        $Hora = $datos['Hora'] ?? [];
        $Esta = $datos['Esta'] ?? [];
        $HsTrAT = $datos['HsTrAT'] ?? '';
        $HoraMin = $datos['HoraMin'] ?? ''; // Hora minima
        $HoraMax = $datos['HoraMax'] ?? ''; // Hora maxima
        $MinMaxH = $datos['MinMaxH'] ?? 0; // Si se quiere el mínimo y máximo de horas

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
            'DiaL' => !is_array($DiaL) ? [] : $DiaL,
            'DiaF' => !is_array($DiaF) ? [] : $DiaF,
            'Hora' => !is_array($Hora) ? [] : $Hora,
            'Esta' => !is_array($Esta) ? [] : $Esta,
            'HoraMin' => empty($HoraMin) ? '' : ($HoraMin),
            'HoraMax' => empty($HoraMax) ? '' : ($HoraMax),
            'MinMaxH' => empty($MinMaxH) ? 0 : ($MinMaxH),
            'HsTrAT' => empty($HsTrAT) ? '' : ($HsTrAT),
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
                'DiaL' => ['arrAllowed01'],
                'DiaF' => ['arrAllowed01'],
                'Hora' => ['arrSmallint'],
                'Esta' => ['arrSmallint'],
                'HoraMin' => ['time'],
                'HoraMax' => ['time'],
                'MinMaxH' => ['allowed01'],
                'HsTrAT' => ['allowed01'],
                'start' => ['intempty'],
                'length' => ['intempty'],
            ];

            $validator = new InputValidator($datosRecibidos, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos
            return $datosRecibidos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_validarInputsTotalesHoras.log');
        }
    }
    public function totales()
    {
        $inicio = microtime(true);
        $datos = $this->validarInputsTotales(); // Valido los datos
        $conn = $this->conect->conn();

        try {

            $FechIni = date('Ymd', strtotime($datos['FechIni'])); // Fecha de inicio
            $FechFin = date('Ymd', strtotime($datos['FechFin'])); // Fecha de fin

            if ($FechIni > $FechFin) {
                $this->resp->respuesta([], 0, 'La fecha de Inicio no puede ser mayor a la fecha de Fin', 400, $inicio, 0, 0);
                exit;
            }

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
            $DiaL = implode(",", $datos["DiaL"]);
            $DiaF = implode(",", $datos["DiaF"]);
            $Hora = implode(",", $datos["Hora"]);
            $Esta = implode(",", $datos["Esta"]);
            $HoraMin = $datos['HoraMin']; // Hora minima
            $HoraMax = $datos['HoraMax']; // Hora maxima
            $MinMaxH = $datos['MinMaxH']; // Sobre horas hechas o autorizadas `0` = Hechas (defecto); `1` = Autorizadas
            $HsTrAT = intval($datos['HsTrAT']); // retorna horas trabajadas y a trabajar. `0` = No, `1` = Si

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
            $wc[] = ($datos["DiaL"]) ? " FICHAS.FicDiaL IN ($DiaL)" : '';
            $wc[] = ($datos["DiaF"]) ? " FICHAS.FicDiaF IN ($DiaF)" : '';
            $wc[] = ($datos["Hora"]) ? " FICHAS1.FicHora IN ($Hora)" : '';
            $wc[] = ($datos["Esta"]) ? " FICHAS1.FicEsta IN ($Esta)" : '';
            $ColFiltroMinMax = ($MinMaxH) ? "FICHAS1.FicHsAu2" : "FICHAS1.FicHsAu";
            $wc[] = ($datos["HoraMin"]) ? " dbo.fn_STRMinutos($ColFiltroMinMax) >= dbo.fn_STRMinutos('$HoraMin')" : '';
            $wc[] = ($datos["HoraMax"]) ? " dbo.fn_STRMinutos($ColFiltroMinMax) <= dbo.fn_STRMinutos('$HoraMax')" : '';
            $wc = array_filter($wc); // Elimino los valores vacíos del array $wc
            $wc = array_values($wc); // Reordeno los índices del array $wc   

            // Flight::json($wc) . exit;

            /** vamos a consultar las horas que hay en la tabla de tipos Horas */
            $whereConditions = "";
            $whereConditions .= (count($wc) > 1) ? ' AND' . implode(" AND ", $wc) : "";
            $whereConditions .= (count($wc) === 1) ? ' AND' . $wc[0] : "";
            $sql = "SELECT DISTINCT(FicHora) as 'HoraCodi', THoDesc, THoDesc2 FROM FICHAS1";
            $sql .= " INNER JOIN FICHAS ON FICHAS1.FicLega = FICHAS.FicLega AND FICHAS1.FicFech = FICHAS.FicFech AND FICHAS1.FicTurn = FICHAS.FicTurn";
            $sql .= " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume";
            $sql .= " INNER JOIN TIPOHORA ON FICHAS1.FicHora = TIPOHORA.THoCodi";
            $sql .= " WHERE FicHora > 0 AND FICHAS.FicFech BETWEEN '$FechIni' AND '$FechFin'";
            $sql .= $whereConditions;
            $sql .= " ORDER BY FicHora";

            // print_r($sql) . exit;

            $stmt = $conn->prepare($sql);
            $ApNo = "%$datos[LegApNo]%";
            ($datos['LegApNo']) ? $stmt->bindParam("LegApNo", $ApNo, \PDO::PARAM_STR) : '';
            $stmt->execute(); // Ejecuto la consulta
            $colHoras = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta

            // print_r($colHoras) . exit;

            if (empty($colHoras)) {
                $this->resp->respuesta([], 0, 'No se encontraron horas', 200, $inicio, 0, 0);
                exit;
            }
            $countHoraCols = array_map(function ($v) {
                return "COUNT(CASE WHEN FICHAS1.FicHora = " . $v['HoraCodi'] . " THEN 1 END) as 'Total_" . $v['HoraCodi'] . "'";
            }, $colHoras);

            $sumHoraCols = array_map(function ($v) {
                return "COALESCE(SUM(CASE WHEN FICHAS1.FicHora = " . $v['HoraCodi'] . " THEN dbo.fn_STRMinutos(FICHAS1.FicHsAu) END), 0) as 'Horas_" . $v['HoraCodi'] . "'";

            }, $colHoras);
            $sumHoraCols2 = array_map(function ($v) {
                return "COALESCE(SUM(CASE WHEN FICHAS1.FicHora = " . $v['HoraCodi'] . " THEN dbo.fn_STRMinutos(FICHAS1.FicHsAu2) END), 0) as 'Horas2_" . $v['HoraCodi'] . "'";

            }, $colHoras);
            $sumHoraCols3 = array_map(function ($v) {
                return "COALESCE(SUM(CASE WHEN FICHAS1.FicHora = " . $v['HoraCodi'] . " THEN dbo.fn_STRMinutos(FICHAS1.FicHsHe) END), 0) as 'Horas1_" . $v['HoraCodi'] . "'";

            }, $colHoras);

            /** **********************************  */

            $columnas = [
                "FICHAS1.FicLega AS 'Lega'",
                "PERSONAL.LegApNo AS 'LegApNo'",
            ];

            $columnas = implode(", ", $columnas);
            $columnas2 = implode(", ", $countHoraCols);
            $columnas3 = implode(", ", $sumHoraCols);
            $columnas4 = implode(", ", $sumHoraCols2);
            $columnas5 = implode(", ", $sumHoraCols3);
            $sql = "SELECT $columnas, $columnas2, $columnas3, $columnas4";
            $sql .= ", $columnas5";
            $sql .= " ,COALESCE(SUM(dbo.fn_STRMinutos(FICHAS.FicHsTr)), 0) as 'Horas_Tr'";
            $sql .= " ,COALESCE(SUM(dbo.fn_STRMinutos(FICHAS.FicHsAT)), 0) as 'Horas_AT'";
            $sql .= " FROM FICHAS1";
            $sql .= " INNER JOIN FICHAS ON FICHAS1.FicLega = FICHAS.FicLega AND FICHAS1.FicFech = FICHAS.FicFech AND FICHAS1.FicTurn = FICHAS.FicTurn";
            $sql .= " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume";
            $sql .= " WHERE FicHora > 0";
            $sql .= " AND FICHAS1.FicFech BETWEEN '$FechIni' AND '$FechFin'";
            $sql .= $whereConditions;
            $sql .= " GROUP BY FICHAS1.FicLega, PERSONAL.LegApNo";
            $sql .= " ORDER BY FICHAS1.FicLega";
            $sql .= " OFFSET $datos[start] ROWS FETCH NEXT $datos[length] ROWS ONLY"; // Paginación
            // print_r($sql) . exit;

            $stmt1 = $conn->prepare($sql);
            ($datos['LegApNo']) ? $stmt1->bindParam("LegApNo", $ApNo, \PDO::PARAM_STR) : '';
            $stmt1->execute(); // Ejecuto la consulta
            $horas = $stmt1->fetchAll(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $horasATyTR = [];

            if ($HsTrAT == 1) { // Si se quiere el total de horas trabajadas y a trabajar
                $legajos = array_column($horas, 'Lega');
                $horasATyTR = $this->horasATyTR($legajos, $FechIni, $FechFin);
            }

            $sql = "SELECT COUNT(DISTINCT(FICHAS.FicLega)) AS 'Total' FROM FICHAS";
            $sql .= " INNER JOIN FICHAS1 ON FICHAS.FicLega = FICHAS1.FicLega AND FICHAS.FicFech = FICHAS1.FicFech AND FICHAS.FicTurn = FICHAS1.FicTurn";
            $sql .= " INNER JOIN PERSONAL ON FICHAS.FicLega = PERSONAL.LegNume";
            $sql .= " WHERE FicHora > 0";
            $sql .= " AND FICHAS1.FicFech BETWEEN '$FechIni' AND '$FechFin'";
            $sql .= $whereConditions;
            // print_r($sql) . exit;
            $stmt2 = $conn->prepare($sql);

            ($datos['LegApNo']) ? $stmt2->bindParam("LegApNo", $ApNo, \PDO::PARAM_STR) : '';

            $stmt2->execute(); // Ejecuto la consulta
            $total = $stmt2->fetch(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
            $stmt->closeCursor(); // Cierro el cursor
            $stmt1->closeCursor(); // Cierro el cursor
            $stmt2->closeCursor(); // Cierro el cursor

            $nuevo_array = array();

            // Recorremos el array original y reestructuramos los datos
            foreach ($horas as $elemento) {

                if ($HsTrAT == 1) { // Si se quiere el total de horas trabajadas y a trabajar
                    $hsATyTR = array_values($this->tools->filtrarElementoArray($horasATyTR, 'Lega', $elemento['Lega']));
                    $hsATyTR = (empty($hsATyTR)) ? [] : $hsATyTR[0];
                    $Horas_Tr = intval($hsATyTR['Horas_Tr']);
                    $Horas_AT = intval($hsATyTR['Horas_AT']);
                    $arrayHsATyTR = array(
                        'HsTrEnMinutos' => intval($Horas_Tr),
                        'HsTrEnHoras' => $this->minutosAHoras(intval($Horas_Tr)),
                        'HsTrEnDecimal' => $this->minutosAHorasDecimal(intval($Horas_Tr)),
                        'HsATEnMinutos' => intval($Horas_AT),
                        'HsATEnHoras' => $this->minutosAHoras(intval($Horas_AT)),
                        'HsATEnDecimal' => $this->minutosAHorasDecimal(intval($Horas_AT)),
                    );
                }
                $arrayHsATyTR = $arrayHsATyTR ?? [];

                $nuevo_elemento = array(
                    'Lega' => $elemento['Lega'],
                    'LegApNo' => $elemento['LegApNo'],
                    'HsATyTR' => $arrayHsATyTR,
                    'Totales' => array()
                );

                // Buscamos las claves dinámicas que comienzan con 'Total_'
                foreach ($elemento as $clave => $valor) {
                    if (strpos($clave, 'Total_') === 0) {
                        // Extraemos el número dinámico de la clave
                        $numero = substr($clave, 6); // Se asume que siempre hay 6 caracteres fijos antes del número
                        $FiltroHoras = array_values($this->tools->filtrarElementoArray($colHoras, 'HoraCodi', ($numero)));
                        if (intval($valor) == 0) {
                            continue;
                        }

                        $EnMinutos = (intval($elemento['Horas_' . $numero]));
                        $EnMinutos2 = (intval($elemento['Horas2_' . $numero]));
                        $EnMinutos1 = (intval($elemento['Horas1_' . $numero]));
                        $sumaDeMinutos = $EnMinutos + $EnMinutos2;
                        if ($sumaDeMinutos == 0) {
                            continue;
                        }

                        $horasEnDecimal = $this->minutosAHorasDecimal(intval($elemento['Horas_' . $numero]));
                        $horasEnDecimal2 = $this->minutosAHorasDecimal(intval($elemento['Horas2_' . $numero]));
                        $horasEnDecimal1 = $this->minutosAHorasDecimal(intval($elemento['Horas1_' . $numero]));

                        $nuevo_elemento['Totales'][] = array(
                            'HoraCodi' => intval($numero),
                            'THoDesc' => $FiltroHoras[0]['THoDesc'],
                            'THoDesc2' => $FiltroHoras[0]['THoDesc2'],
                            'Cantidad' => intval($valor),
                            'EnHoras' => $this->minutosAHoras(intval($elemento['Horas_' . $numero])),
                            'EnHoras1' => $this->minutosAHoras(intval($elemento['Horas1_' . $numero])),
                            'EnHoras2' => $this->minutosAHoras(intval($elemento['Horas2_' . $numero])),
                            'EnMinutos' => $EnMinutos,
                            'EnMinutos1' => $EnMinutos1,
                            'EnMinutos2' => $EnMinutos2,
                            'EnHorasDecimal' => $horasEnDecimal,
                            'EnHorasDecimal1' => $horasEnDecimal1,
                            'EnHorasDecimal2' => $horasEnDecimal2,
                        );
                    }
                }

                $nuevo_array[] = $nuevo_elemento;
            }

            foreach ($colHoras as $key => $value) {
                $hor[] = array(
                    'HoraCodi' => intval($value['HoraCodi']),
                    'THoDesc' => trim($value['THoDesc']),
                    'THoDesc2' => trim($value['THoDesc2']),
                );
            }

            $sumas = array();

            foreach ($nuevo_array as $empleado) {
                $datHsATyTR[] = ($HsTrAT == 1) ? $empleado['HsATyTR'] : []; // Si se quiere el total de horas trabajadas y a trabajar, creamos un array con los valores de horas trabajadas y a trabajar
                foreach ($empleado["Totales"] as $suma) {

                    $horaCodi = $suma["HoraCodi"];
                    $horaDesc = $suma["THoDesc"];
                    $horaDesc2 = $suma["THoDesc2"];
                    $cantidad = $suma["Cantidad"];
                    $enMinutos = $suma["EnMinutos"];
                    $enMinutos1 = $suma["EnMinutos1"];
                    $enMinutos2 = $suma["EnMinutos2"];
                    $enHoras = $suma["EnHoras"];
                    $enHoras1 = $suma["EnHoras1"];
                    $enHoras2 = $suma["EnHoras2"];
                    $EnHorasDecimal = $suma["EnHorasDecimal"];
                    $EnHorasDecimal1 = $suma["EnHorasDecimal1"];
                    $EnHorasDecimal2 = $suma["EnHorasDecimal2"];

                    // Si el HoraCodi ya existe en el array de sumas, sumamos los valores, de lo contrario lo inicializamos
                    if (array_key_exists($horaCodi, $sumas)) {
                        $sumas[$horaCodi]["Cantidad"] += $cantidad;
                        $sumas[$horaCodi]["EnMinutos"] += $enMinutos;
                        $sumas[$horaCodi]["EnMinutos1"] += $enMinutos1;
                        $sumas[$horaCodi]["EnMinutos2"] += $enMinutos2;
                        $sumas[$horaCodi]["EnHoras"] = $this->minutosAHoras(intval($sumas[$horaCodi]["EnMinutos"]));
                        $sumas[$horaCodi]["EnHoras1"] = $this->minutosAHoras(intval($sumas[$horaCodi]["EnMinutos1"]));
                        $sumas[$horaCodi]["EnHoras2"] = $this->minutosAHoras(intval($sumas[$horaCodi]["EnMinutos2"]));
                        $sumas[$horaCodi]["EnHorasDecimal1"] = $this->minutosAHorasDecimal(intval($sumas[$horaCodi]["EnMinutos1"]));
                        $sumas[$horaCodi]["EnHorasDecimal"] = $this->minutosAHorasDecimal(intval($sumas[$horaCodi]["EnMinutos"]));
                        $sumas[$horaCodi]["EnHorasDecimal2"] = $this->minutosAHorasDecimal(intval($sumas[$horaCodi]["EnMinutos2"]));
                    } else {
                        $sumas[$horaCodi] = array(
                            "HoraCodi" => $horaCodi, // Código de la hora
                            "THoDesc" => $horaDesc, // Descripción de la hora
                            "THoDesc2" => $horaDesc2, // Descripción de la hora corta
                            "Cantidad" => $cantidad, // Cantidad de horas en totales 
                            "EnMinutos" => $enMinutos, // Suma de minutos de horas calculadas
                            "EnMinutos1" => $enMinutos1, // Suma de minutos de horas hechas
                            "EnMinutos2" => $enMinutos2, // Suma de minutos de horas autorizadas
                            "EnHoras" => $enHoras, // Suma de horas calculadas en horas:minutos
                            "EnHoras1" => $enHoras1, // Suma de horas hechas en horas:minutos
                            "EnHoras2" => $enHoras2, // Suma de horas autorizadas en horas:minutos
                            "EnHorasDecimal" => $EnHorasDecimal, // Suma de horas calculadas en decimal
                            "EnHorasDecimal1" => $EnHorasDecimal1, // Suma de horas hechas en decimal
                            "EnHorasDecimal2" => $EnHorasDecimal2, // Suma de horas autorizadas en decimal
                        );
                    }
                    ksort($sumas);
                }
            }

            if ($HsTrAT) {
                $horasTrabajadasTotales = array_sum(array_column($datHsATyTR, 'HsTrEnMinutos'));
                $horasATrabajarTotales = array_sum(array_column($datHsATyTR, 'HsATEnMinutos'));

                $totalesATyTr = [
                    'HsTrEnMinutos' => $horasTrabajadasTotales,
                    'HsTrEnHoras' => $this->minutosAHoras(intval($horasTrabajadasTotales)),
                    'HsTrEnDecimal' => $this->minutosAHorasDecimal(intval($horasTrabajadasTotales)),
                    'HsATEnMinutos' => $horasATrabajarTotales,
                    'HsATEnHoras' => $this->minutosAHoras(intval($horasATrabajarTotales)),
                    'HsATEnDecimal' => $this->minutosAHorasDecimal(intval($horasATrabajarTotales)),
                ];
            }

            $array = [
                'totales' => array_values($sumas),
                'totalesTryAT' => $totalesATyTr ?? [],
                'data' => $nuevo_array,
                'tiposHoras' => $hor,
            ];

            $this->resp->respuesta($array, count($nuevo_array), 'OK', 200, $inicio, $total['Total'], 0);

        } catch (\PDOException $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, $inicio, 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_totalesHoras_' . ID_COMPANY . '.log');
            exit;
        }
    }
    public function dateMinMax()
    {
        $conn = $this->conect->conn();

        $sql = "SELECT MIN(FICHAS1.FicFech) AS 'min', MAX(FICHAS1.FicFech) AS 'max' FROM FICHAS1 WHERE FICHAS1.FicFech !='17530101' AND FICHAS1.FicFech < GETDATE()";
        $stmt = $conn->prepare($sql);
        $stmt->execute(); // Ejecuto la consulta
        $array = $stmt->fetch(\PDO::FETCH_ASSOC); // Obtengo los datos de la consulta
        $array['min'] = date('Y-m-d', strtotime($array['min']));
        $array['max'] = date('Y-m-d', strtotime($array['max']));
        $a = array(
            'min' => ($array['min']),
            'max' => $array['max']
        );
        $this->resp->respuesta($array, 1, 'OK', 200, microtime(true), 0, 0);
        $stmt->closeCursor(); // Cierro el cursor

    }
    private function minutosAHoras($minutos)
    {
        $horas = floor($minutos / 60);
        $minutos = $minutos % 60;
        return sprintf("%02d:%02d", $horas, $minutos);
    }
    private function minutosAHorasDecimal($minutos)
    {
        $horas = floor($minutos / 60);
        $minutosRestantes = $minutos % 60;
        $minutosDecimal = $minutosRestantes / 60.0;
        return $horas + $minutosDecimal;
    }
    /**
     * Retrieves the total hours worked and overtime hours for the given employees within a specified date range.
     *
     * @param array $arrayLegajos An array of employee IDs.
     * @param string $FechIni The start date of the date range in 'YYYY-MM-DD' format.
     * @param string $FechFin The end date of the date range in 'YYYY-MM-DD' format.
     * @return array An array containing the employee ID, total hours worked, and overtime hours for each employee.
     */
    private function horasATyTR($arrayLegajos, $FechIni, $FechFin)
    {
        if (!$arrayLegajos)
            return [];

        $conn = $this->conect->conn();
        $sql = "SELECT FICHAS.FicLega as 'Lega'";
        $sql .= " ,COALESCE(SUM(dbo.fn_STRMinutos(FICHAS.FicHsTr)), 0) as 'Horas_Tr'";
        $sql .= " ,COALESCE(SUM(dbo.fn_STRMinutos(FICHAS.FicHsAT)), 0) as 'Horas_AT'";
        $sql .= " FROM FICHAS";
        $sql .= " WHERE FICHAS.FicLega IN(" . implode(",", $arrayLegajos) . ")";
        $sql .= " AND FICHAS.FicFech BETWEEN '$FechIni' AND '$FechFin'";
        $sql .= " GROUP BY FICHAS.FicLega";
        $stmt = $conn->prepare($sql);
        $stmt->execute(); // Ejecuto la consulta
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $data;
    }
}
