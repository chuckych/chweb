<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\InputValidator;
use Classes\RRHHWebService;
use Classes\Tools;
use Classes\Auditor;
use Classes\Personal;
use Flight;


class Horarios
{
    private $resp;
    private $request;
    private $getData;
    // private $query;
    private $log;
    private $conect;
    private $ws;
    private $tools;
    private $auditor;
    private $url;
    private $desde;
    private $desdeHasta;
    private $citacion;
    private $personal;
    private $urlMap;
    private $method;
    private $rotacion;



    function __construct()
    {
        $this->urlMap = [
            '/horarios/desde/' => ['desde' => true, 'desdeHasta' => false, 'citacion' => false],
            '/horarios/legajo-desde/' => ['desde' => true, 'desdeHasta' => false, 'citacion' => false],
            '/horarios/desde-hasta/' => ['desde' => false, 'desdeHasta' => true, 'citacion' => false],
            '/horarios/legajo-desde-hasta/' => ['desde' => false, 'desdeHasta' => true, 'citacion' => false],
            '/horarios/citacion/' => ['desde' => false, 'desdeHasta' => false, 'citacion' => true],
            '/horarios/legajo-citacion/' => ['desde' => false, 'desdeHasta' => false, 'citacion' => true],
            '/horarios/delete/' => ['delete' => true, 'delete-desdeHasta' => false, 'delete-citacion' => false],
            '/horarios/legajo-delete-desde/' => ['delete-desde' => true, 'delete-desdeHasta' => false, 'delete-citacion' => false],
            '/horarios/rotacion/' => ['rotacion' => true],
            '/horarios/legajo-rotacion/' => ['rotacion' => true],
        ];

        $this->resp    = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        // $this->query   = $this->request->query->getData();
        $this->log     = new Log;
        $this->conect  = new ConnectSqlSrv;
        $this->ws      = new RRHHWebService;
        $this->tools   = new Tools;
        $this->auditor = new Auditor;
        $this->url     = (substr($this->request->url, -1) !== '/') ? "{$this->request->url}/" : $this->request->url;
        $this->method  = $this->request->method;

        if (isset($this->urlMap[$this->url])) {
            $this->desde = $this->urlMap[$this->url]['desde'];
            $this->desdeHasta = $this->urlMap[$this->url]['desdeHasta'];
            $this->citacion = $this->urlMap[$this->url]['citacion'];
            $this->rotacion = $this->urlMap[$this->url]['rotacion'];
        } else {
            $this->desde = false;
            $this->desdeHasta = false;
            $this->citacion = false;
            $this->rotacion = false;
        }
        $this->personal = new Personal;
    }

    public function set_horario()
    {
        try {

            $inicio = microtime(true); // Inicio del script
            $datos  = $this->validar_request_horarios(); // Valida los datos
            $conn   = $this->conect->conn(); // Conexión a la base de datos

            $conn->beginTransaction(); // Iniciar transacción

            $filas = 0;
            $arrayAud  = [];
            $auditorCH = [];
            $Proc      = $datos['Proc'];
            $Fecha     = $datos['Fecha'];
            $Legajos   = $datos['Lega'];
            $FechaD    = $datos['FechaD'];
            $FechaH    = $datos['FechaH'];
            $Codi      = $datos['Codi'];
            $User      = $datos['User'];
            $Entr      = $datos['Entr'];
            $Sale      = $datos['Sale'];
            $Desc      = $datos['Desc'];

            $this->personal->check_legajos($Legajos, $conn);

            $ListHorarios = $Codi ? $this->return_horarios($conn) : []; // Devuelve los horarios si el código es distinto de 0
            define('LIST_HORARIOS', $ListHorarios);

            if (!array_key_exists($Codi, LIST_HORARIOS)) {
                if ($Codi) {
                    throw new \Exception("Horario {$Codi} no existe", 400);
                }
            }

            $legajosUpdate = $this->check_horario_data($conn, $datos);
            $legajosInsert = array_values(array_diff($Legajos, $legajosUpdate));

            if ($this->desde) {

                $fechaFormat = $this->tools->formatDateTime($Fecha, 'd-m-Y');
                $AudText = "Horario desde {$fechaFormat}";

                if ($legajosUpdate) {
                    $filas     += $this->update_desde([$conn, $legajosUpdate, $Codi, $Fecha]); // Actualiza los horarios
                    $audUDesde = $this->auditoria([$legajosUpdate, $Codi, 'M', $AudText, $User]);
                }

                if ($legajosInsert) {
                    $filas    += $this->insert_desde([$conn, $legajosInsert, $Codi, $Fecha]); // Inserta los horarios
                    $audIDesde = $this->auditoria([$legajosInsert, $Codi, 'A', $AudText, $User]);
                }

                $arrayAud  = array_merge($arrayAud, $audIDesde['auditor'] ?? [], $audUDesde['auditor'] ?? []); // Auditoría para respuesta
                $auditorCH = array_merge($auditorCH, $audIDesde['auditorCH'] ?? [], $audUDesde['auditorCH'] ?? []); // Auditoría para CH
            }

            if ($this->desdeHasta) {

                $fechaDFormat = $this->tools->formatDateTime($FechaD, 'd-m-Y');
                $fechaHFormat = $this->tools->formatDateTime($FechaH, 'd-m-Y');

                if ($this->tools->formatDateTime($FechaD, 'Ymd') > $this->tools->formatDateTime($FechaH, 'Ymd')) {
                    throw new \Exception("La fecha Desde no puede ser mayor a la fecha Hasta", 400);
                }

                $AudText = "Horario de {$fechaDFormat} a {$fechaHFormat}";

                if ($legajosUpdate) {
                    $filas     += $this->update_desde_hasta([$conn, $legajosUpdate, $Codi, $FechaD]); // Actualiza los horarios
                    $audUDesdeH = $this->auditoria([$legajosUpdate, $Codi, 'M', $AudText, $User]);
                }

                if ($legajosInsert) {
                    $filas     += $this->insert_desde_hasta([$conn, $legajosInsert, $Codi, $FechaD, $FechaH]); // Inserta los horarios
                    $audIDesdeH = $this->auditoria([$legajosInsert, $Codi, 'A', $AudText, $User]);
                }

                $arrayAud  = array_merge($arrayAud, $audIDesdeH['auditor'] ?? [], $audUDesdeH['auditor'] ?? []); // Auditoría para respuesta
                $auditorCH = array_merge($auditorCH, $audIDesdeH['auditorCH'] ?? [], $audUDesdeH['auditorCH'] ?? []); // Auditoría para CH
            }

            if ($this->citacion) {

                $calculoDescanso = $this->tools->validar_si_descanso_es_mayor_a_tiempo_trabajado($Entr, $Sale, $Desc);
                if ($calculoDescanso) {
                    throw new \Exception("El tiempo de descanso debe ser menor o igual al tiempo total a trabajar", 400);
                }

                $fechaFormat = $this->tools->formatDateTime($Fecha, 'd-m-Y');
                $AudText = "Citación: Fecha {$fechaFormat}. Entra: {$Entr}. Sale: {$Sale}. Desc: {$Desc}";

                if ($legajosUpdate) {
                    $filas     += $this->update_citacion([$conn, $legajosUpdate, $Fecha, $Entr, $Sale, $Desc]); // Actualiza los horarios
                    $audUDesde = $this->auditoria([$legajosUpdate, '', 'M', $AudText, $User], 'citaciones');
                }

                if ($legajosInsert) {
                    $filas    += $this->insert_citacion([$conn, $legajosInsert, $Fecha, $Entr, $Sale, $Desc]); // Inserta los horarios
                    $audIDesde = $this->auditoria([$legajosInsert, '', 'A', $AudText, $User], 'citaciones');
                }

                $arrayAud  = array_merge($arrayAud, $audIDesde['auditor'] ?? [], $audUDesde['auditor'] ?? []); // Auditoría para respuesta
                $auditorCH = array_merge($auditorCH, $audIDesde['auditorCH'] ?? [], $audUDesde['auditorCH'] ?? []); // Auditoría para CH
            }

            $conn->commit(); // Confirmar la transacción
            $this->auditor->add($auditorCH); // Guarda un registro en la tabla AUDITOR

            if ($Proc) {
                if ($this->desde) {
                    $this->ws->procesar_legajos($Legajos, $Fecha, $this->conect->Fecha());
                }
                if ($this->desdeHasta) {
                    $this->ws->procesar_legajos($Legajos, $FechaD, $FechaH);
                }
                if ($this->citacion) {
                    $this->ws->procesar_legajos($Legajos, $Fecha, $this->conect->Fecha());
                }
            }

            $this->resp->respuesta($arrayAud, $filas, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $code = $e->getCode();
            $conn->rollBack();
            $this->log->write($e->getMessage(), date('Ymd') . '_Horarios_' . ID_COMPANY . '.log');
            throw new \Exception($e->getMessage(), $code);
        }
    }
    public function delete_horario()
    {
        try {
            $inicio = microtime(true); // Inicio del script
            $datos  = $this->validar_delete(); // Valida los datos

            $conn = $this->conect->conn();

            $conn->beginTransaction();

            $filas = 0;
            $arrayAud  = [];
            $auditorCH = [];
            $Proc      = $datos['Proc'];
            $Fecha     = $datos['Fecha'];
            $Legajos   = $datos['Lega'];
            $User      = $datos['User'];
            $Codi      = $datos['Codi'];

            if (!$this->rotacion) {
                $ListHorarios = $Codi ? $this->return_horarios($conn) : []; // Devuelve los horarios si el código es distinto de 0
                define('LIST_HORARIOS_DELETE', $ListHorarios);

                if (!array_key_exists($Codi, LIST_HORARIOS_DELETE)) {
                    if ($Codi) {
                        throw new \Exception("Horario {$Codi} no existe", 400);
                    }
                }
            }

            if ($this->desde) {

                $fechaFormat = $this->tools->formatDateTime($Fecha, 'd-m-Y');

                $check_delete_legajos = $this->check_delete_legajos([$conn, $Legajos, $Fecha, $Codi]);

                if (empty($check_delete_legajos)) {
                    throw new \Exception("Los legajos enviados no tienen un horario desde configurado para la fecha {$fechaFormat} o ya fueron eliminados", 400);
                }
                $Legajos = $check_delete_legajos;

                $AudText = "Horario desde {$fechaFormat}";

                $filas += $this->delete_desde([$conn, $Legajos, $Fecha]); // Actualiza los horarios
                $audUDesde = $this->auditoria([$Legajos, $Codi, 'B', $AudText, $User], 'horarios-delete');

                $arrayAud  = array_merge($arrayAud, $audIDesde['auditor'] ?? [], $audUDesde['auditor'] ?? []); // Auditoría para respuesta
                $auditorCH = array_merge($auditorCH, $audIDesde['auditorCH'] ?? [], $audUDesde['auditorCH'] ?? []); // Auditoría para CH

            }
            if ($this->desdeHasta) {

                $fechaFormat = $this->tools->formatDateTime($Fecha, 'd-m-Y');

                $check_delete_legajos = $this->check_delete_legajos([$conn, $Legajos, $Fecha, $Codi]);

                if (empty($check_delete_legajos)) {
                    throw new \Exception("Los legajos enviados no tienen un horario desde-hasta configurado para la fecha {$fechaFormat} o ya fueron eliminados", 400);
                }
                $Legajos = $check_delete_legajos;

                $fechaFin = $this->get_fecha_fin([$conn, $Legajos, $Fecha, $Codi]);

                if (!$fechaFin) {
                    throw new \Exception("No se pudo obtener la fecha hasta para los legajos enviados", 400);
                }

                $FechaH = $this->tools->formatDateTime($fechaFin, 'Y-m-d');
                $FechaHFormat = $this->tools->formatDateTime($fechaFin, 'd-m-Y');

                $AudText = "Horario desde-hasta {$fechaFormat} a {$FechaHFormat}";

                $filas += $this->delete_desde_hasta([$conn, $Legajos, $Fecha, $Codi]); // Actualiza los horarios

                $audUDesde = $this->auditoria([$Legajos, $Codi, 'B', $AudText, $User], 'horarios-delete');

                $arrayAud  = array_merge($arrayAud, $audIDesde['auditor'] ?? [], $audUDesde['auditor'] ?? []); // Auditoría para respuesta
                $auditorCH = array_merge($auditorCH, $audIDesde['auditorCH'] ?? [], $audUDesde['auditorCH'] ?? []); // Auditoría para CH

            }
            if ($this->rotacion) {

                $ListRotaciones = $this->return_rotaciones($conn); // Devuelve las rotaciones si el código es distinto de 0
                define('LIST_ROTACIONES_DELETE', $ListRotaciones);

                if (!array_key_exists($Codi, LIST_ROTACIONES_DELETE)) {
                    throw new \Exception("Rotación {$Codi} no existe", 400);
                }

                $fechaFormat = $this->tools->formatDateTime($Fecha, 'd-m-Y');

                $check_delete_legajos = $this->check_delete_legajos([$conn, $Legajos, $Fecha, $Codi]);

                if (empty($check_delete_legajos)) {
                    throw new \Exception("Los legajos enviados no tienen una rotación asignada para la fecha {$fechaFormat} o ya fueron eliminadas", 400);
                }
                $Legajos = $check_delete_legajos;

                $fechaFin = $this->get_fecha_fin([$conn, $Legajos, $Fecha, $Codi]);

                if (!$fechaFin) {
                    throw new \Exception("No se pudo obtener la fecha hasta para los legajos enviados", 400);
                }

                $FechaH = ($fechaFin == '2099-12-31 00:00:00.000') ? $this->conect->Fecha() : $this->tools->formatDateTime($fechaFin, 'Y-m-d');
                $FechaHFormat = ($fechaFin == '2099-12-31 00:00:00.000') ? $this->conect->Fecha('d-m-Y') : $this->tools->formatDateTime($fechaFin, 'd-m-Y');

                $AudText = "Rotación {$fechaFormat} a {$FechaHFormat}";

                $filas += $this->delete_rotacion([$conn, $Legajos, $Fecha, $Codi]); // Actualiza los horarios

                $audUDesde = $this->auditoria([$Legajos, $Codi, 'B', $AudText, $User], 'rotaciones-delete');

                $arrayAud  = array_merge($arrayAud, $audIDesde['auditor'] ?? [], $audUDesde['auditor'] ?? []); // Auditoría para respuesta
                $auditorCH = array_merge($auditorCH, $audIDesde['auditorCH'] ?? [], $audUDesde['auditorCH'] ?? []); // Auditoría para CH

            }
            if ($this->citacion) {

                $fechaFormat = $this->tools->formatDateTime($Fecha, 'd-m-Y');

                $check_delete_legajos = $this->check_delete_legajos([$conn, $Legajos, $Fecha, $Codi]);

                if (empty($check_delete_legajos)) {
                    throw new \Exception("Los legajos enviados no tienen una citacion para la fecha {$fechaFormat} o ya fueron eliminadas", 400);
                }
                $Legajos = $check_delete_legajos;

                $AudText = "Citación {$fechaFormat}";

                $filas += $this->delete_citacion([$conn, $Legajos, $Fecha]); // Actualiza los horarios
                $audUDesde = $this->auditoria([$Legajos, '', 'B', $AudText, $User], 'citaciones');

                $arrayAud  = array_merge($arrayAud, $audIDesde['auditor'] ?? [], $audUDesde['auditor'] ?? []); // Auditoría para respuesta
                $auditorCH = array_merge($auditorCH, $audIDesde['auditorCH'] ?? [], $audUDesde['auditorCH'] ?? []); // Auditoría para CH

            }

            $conn->commit(); // Confirmar la transacción
            $this->auditor->add($auditorCH); // Guarda un registro en la tabla AUDITOR

            if ($Proc) {
                if ($this->desde) {
                    $this->ws->procesar_legajos($Legajos, $Fecha, $this->conect->Fecha());
                }
                if ($this->desdeHasta) {
                    $this->ws->procesar_legajos($Legajos, $Fecha, $FechaH);
                }
                if ($this->citacion) {
                    $this->ws->procesar_legajos($Legajos, $Fecha, $Fecha);
                }
                if ($this->rotacion) {
                    $this->ws->procesar_legajos($Legajos, $Fecha, $FechaH);
                }
            }

            $this->resp->respuesta($arrayAud, $filas, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $code = $e->getCode();
            $conn->rollBack();
            $this->log->write($e->getMessage(), date('Ymd') . '_Horarios_Delete_' . ID_COMPANY . '.log');
            throw new \Exception($e->getMessage(), $code);
        }
    }
    public function set_rotacion()
    {
        try {

            $inicio = microtime(true); // Inicio del script
            $datos  = $this->validar_request_rotacion(); // Valida los datos
            $conn   = $this->conect->conn(); // Conexión a la base de datos
            $conn->beginTransaction(); // Iniciar transacción

            $filas = 0;
            $arrayAud  = [];
            $auditorCH = [];
            $Proc      = $datos['Proc'];
            $Fecha     = $datos['Fecha'];
            $Vence     = $datos['Vence'];
            $Legajos   = $datos['Lega'];
            $Codi      = $datos['Codi'];
            $Dias      = $datos['Dias'];
            $User      = $datos['User'];

            //             Array de Datos
            // (
            //     [Fecha] => 2024-06-28
            //     [Vence] => 2025-06-28
            //     [Proc] => 
            //     [Codi] => 1
            //     [User] => Norberto CH.
            //     [Dias] => 1
            //     [Lega] => Array
            //         (
            //             [0] => 29408391
            //             [1] => 30366320
            //         )

            // )

            // si Vence es Menor o igual a Fecha. Lanzar excepción
            if ($this->tools->formatDateTime($Vence, 'Ymd') <= $this->tools->formatDateTime($Fecha, 'Ymd')) {
                throw new \Exception("La fecha de vencimiento debe ser mayor a la fecha de inicio", 400);
            }

            $ListRotaciones = $Codi ? $this->return_rotaciones($conn) : []; // Devuelve las rotaciones si el código es distinto de 0
            define('LIST_ROTACIONES', $ListRotaciones);

            $this->personal->check_legajos($Legajos, $conn);

            if (!array_key_exists($Codi, LIST_ROTACIONES)) {
                if ($Codi) {
                    throw new \Exception("Rotación {$Codi} no existe", 400);
                }
            }

            $totalDias = $this->return_total_dias_rotacion($conn, $Codi);

            if ($Dias > $totalDias) {
                throw new \Exception("El día inicio no puede ser mayor a la cantidad total de días de rotación", 400);
            }

            $legajosUpdate = $this->check_rotacion_data($conn, $datos);
            $legajosInsert = array_values(array_diff($Legajos, $legajosUpdate));


            $fechaFormat = $this->tools->formatDateTime($Fecha, 'd-m-Y');
            $AudText = "Rotación {$fechaFormat}";

            if ($legajosUpdate) {
                $filas     += $this->update_rotacion([$conn, $legajosUpdate, $Codi, $Fecha, $Dias, $Vence]); // Actualiza los horarios
                $audUDesde = $this->auditoria([$legajosUpdate, $Codi, 'M', $AudText, $User], 'rotaciones');
            }

            if ($legajosInsert) {
                $filas    += $this->insert_rotacion([$conn, $legajosInsert, $Codi, $Fecha, $Dias, $Vence]); // Inserta los horarios
                $audIDesde = $this->auditoria([$legajosInsert, $Codi, 'A', $AudText, $User], 'rotaciones');
            }

            $arrayAud  = array_merge($arrayAud, $audIDesde['auditor'] ?? [], $audUDesde['auditor'] ?? []); // Auditoría para respuesta
            $auditorCH = array_merge($auditorCH, $audIDesde['auditorCH'] ?? [], $audUDesde['auditorCH'] ?? []); // Auditoría para CH

            $conn->commit(); // Confirmar la transacción
            $this->auditor->add($auditorCH); // Guarda un registro en la tabla AUDITOR

            if ($Proc) {
                $this->ws->procesar_legajos($Legajos, $Fecha, $this->conect->Fecha());
            }

            $this->resp->respuesta($arrayAud, $filas, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $code = $e->getCode();
            $conn->rollBack();
            $this->log->write($e->getMessage(), date('Ymd') . '_Horarios_' . ID_COMPANY . '.log');
            throw new \Exception($e->getMessage(), $code);
        }
    }
    private function validar_request_horarios()
    {
        $datos = $this->getData;

        $rules = [ // Reglas de validación
            'Lega' => ['required', 'arrInt'],
            'Codi' => ['smallintEmpty'],
            'User' => ['varchar100'],
            'Proc' => ['boolean'],
        ];
        if ($this->desde) {
            $rules['Fecha'] = ['required', 'date'];
        }
        if ($this->desdeHasta) {
            $rules['FechaD'] = ['required', 'date'];
            $rules['FechaH'] = ['required', 'date'];
        }
        if ($this->citacion) {
            $rules['Entr'] = ['required', 'time'];
            $rules['Sale'] = ['required', 'time'];
            $rules['Desc'] = ['time']; // tiempo de descanso
        }
        $customValueKey = [ // Valores por defecto
            'Lega' => "",
            'Fecha' => "",
            'FechaD' => "",
            'FechaH' => "",
            'Entr' => "",
            'Sale' => "",
            'Desc' => "00:00",
            'Codi' => "",
            'User' => "",
            "Proc" => false
        ];
        return $this->tools->validar_datos($datos, $rules, $customValueKey, 'validar_inputs_horarios');
    }
    private function validar_delete()
    {
        $datos = $this->getData;

        $rules = [ // Reglas de validación
            'Lega' => ['required', 'arrInt'],
            'User' => ['varchar100'],
            'Proc' => ['boolean'],
            'Fecha' => ['required', 'date'],
            'Codi' => ['smallintEmpty']
        ];

        $customValueKey = [ // Valores por defecto
            'Lega' => "",
            'Fecha' => "",
            'User' => "",
            "Proc" => false,
            'Codi' => ""
        ];
        return $this->tools->validar_datos($datos, $rules, $customValueKey, 'validar_inputs_delete_horarios');
    }
    private function validar_request_rotacion()
    {
        $datos = $this->getData;

        $rules = [ // Reglas de validación
            'Lega' => ['required', 'arrInt'],
            'Codi' => ['smallintEmpty'],
            'Dias' => ['smallintEmpty'],
            'User' => ['varchar100'],
            'Proc' => ['boolean'],
            'Fecha' => ['date'],
            'Vence' => ['dateEmpty']
        ];
        $customValueKey = [ // Valores por defecto
            'Lega' => "",
            'Fecha' => "",
            'Vence' => "2099-12-31",
            'Codi' => "",
            'Dias' => "1",
            'User' => "",
            "Proc" => false
        ];

        return $this->tools->validar_datos($datos, $rules, $customValueKey, 'validar_request_rotacion');
    }
    private function check_horario($connDB = '', $datos)
    {
        $conn = $this->conect->check_connection($connDB);

        if ($this->desde) {
            $sqlCheck = "SELECT COUNT(*) AS Cantidad FROM HORALE1 WHERE Ho1Lega = :Lega AND Ho1Fech = :Fecha";
            $stmt = $conn->prepare($sqlCheck);
            $datos['Fecha'] = date('Ymd', strtotime($datos['Fecha'])); // Convierto la fecha a formato YYYYMMDD
            $stmt->bindValue(':Lega', $datos['Lega'], \PDO::PARAM_INT);
            $stmt->bindValue(':Fecha', $datos['Fecha'], \PDO::PARAM_STR);
            $stmt->execute(); // Ejecuto la consulta
            $result = $stmt->fetchColumn(); // Obtengo la cantidad de filas afectadas
            return $result;
        }
        if ($this->desdeHasta) {
            $sqlCheck = "SELECT COUNT(*) AS Cantidad FROM HORALE2 WHERE Ho2Lega = :Lega AND Ho2Fec1 = :FechaD";
            $stmt = $conn->prepare($sqlCheck);
            $datos['FechaD'] = date('Ymd', strtotime($datos['FechaD'])); // Convierto la fecha a formato YYYYMMDD
            $stmt->bindValue(':Lega', $datos['Lega'], \PDO::PARAM_INT);
            $stmt->bindValue(':FechaD', $datos['FechaD'], \PDO::PARAM_STR);
            $stmt->execute(); // Ejecuto la consulta
            $result = $stmt->fetchColumn(); // Obtengo la cantidad de filas afectadas
            return $result;
        }
    }
    private function check_horario_data($connDB = '', $datos)
    {
        try {
            $legajos = $datos['Lega'] ?? [];

            if (!$legajos) {
                return [];
            }

            $conn = $this->conect->check_connection($connDB);

            // $legajos = implode(',', array_map(callback: 'intval', $legajos));
            $legajos = implode(',', array_map('intval', $legajos)); // Convierto los legajos a enteros y los uno con comas

            if ($this->desde) {
                $sqlCheck = "SELECT Ho1Lega AS 'LegNume' FROM HORALE1 WHERE Ho1Fech = :Fecha";
                $sqlCheck .= " AND Ho1Lega IN ($legajos)";
                $stmt = $conn->prepare($sqlCheck);
                $datos['Fecha'] = date('Ymd', strtotime($datos['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $stmt->bindValue(':Fecha', $datos['Fecha'], \PDO::PARAM_STR);
            }

            if ($this->desdeHasta) {
                $sqlCheck = "SELECT Ho2Lega AS 'LegNume' FROM HORALE2 WHERE Ho2Fec1 = :FechaD";
                $sqlCheck .= " AND Ho2Lega IN ({$legajos})";
                $stmt = $conn->prepare($sqlCheck);
                $datos['FechaD'] = date('Ymd', strtotime($datos['FechaD'])); // Convierto la fecha a formato YYYYMMDD
                $stmt->bindValue(':FechaD', $datos['FechaD'], \PDO::PARAM_STR);
            }

            if ($this->citacion) {
                $sqlCheck = "SELECT CitLega AS 'LegNume' FROM CITACION WHERE CitFech = :Fecha";
                $sqlCheck .= " AND CitLega IN ($legajos)";
                $stmt = $conn->prepare($sqlCheck);
                $datos['Fecha'] = date('Ymd', strtotime($datos['Fecha'])); // Convierto la fecha a formato YYYYMMDD
                $stmt->bindValue(':Fecha', $datos['Fecha'], \PDO::PARAM_STR);
            }

            $stmt->execute(); // Ejecuto la consulta
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $result = [];
        }

        return $result ? array_column($result, 'LegNume') : [];
    }
    private function check_rotacion_data($connDB = '', $datos)
    {
        try {
            $legajos = $datos['Lega'] ?? [];

            if (!$legajos) {
                return [];
            }

            $conn = $this->conect->check_connection($connDB);

            $legajos = implode(',', array_map('intval', $legajos)); // Convierto los legajos a enteros y los uno con comas

            $sqlCheck = "SELECT RolLega AS 'LegNume' FROM ROTALEG WHERE RolFech = :Fecha";
            $sqlCheck .= " AND RolLega IN ($legajos)";
            $stmt = $conn->prepare($sqlCheck);
            $datos['Fecha'] = date('Ymd', strtotime($datos['Fecha'])); // Convierto la fecha a formato YYYYMMDD
            $stmt->bindValue(':Fecha', $datos['Fecha'], \PDO::PARAM_STR);

            $stmt->execute(); // Ejecuto la consulta
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            $result = [];
        }

        return $result ? array_column($result, 'LegNume') : [];
    }
    private function return_horarios($connDB = ''): array
    {
        try {
            $conn = $this->conect->check_connection($connDB);
            $getCache = $this->tools->return_cache($conn, 'HORARIOS', 'fechaHoraHorarios', 'horarios');

            if ($getCache->data) {
                return $getCache->data;
            }

            // $sql  = "SELECT * FROM HORARIOS";
            $sql  = "SELECT HorCodi, HorDesc FROM HORARIOS";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$result) {
                throw new \Exception("No se encontraron horarios", 400);
            }

            $result = array_column($result, 'HorDesc', 'HorCodi');

            $this->log->cache($result, 'horarios');
            $this->log->cache($getCache->FHora, 'fechaHoraHorarios', '.txt');
            return $result;
        } catch (\Throwable $th) {
            throw new \Exception("Error al obtener horarios", 400);
            // throw new \Exception("Error al obtener horarios: " . $th->getMessage(), 400);
        }
    }
    private function return_rotaciones($connDB = ''): array
    {
        try {
            $conn = $this->conect->check_connection($connDB);
            $getCache = $this->tools->return_cache($conn, 'ROTACION', 'fechaHoraRotaciones', 'rotaciones');

            if ($getCache->data) {
                return $getCache->data;
            }

            $sql  = "SELECT RotCodi, RotDesc FROM ROTACION";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!$result) {
                throw new \Exception("No se encontraron horarios", 400);
            }

            $result = array_column($result, 'RotDesc', 'RotCodi');

            $this->log->cache($result, 'rotaciones');
            $this->log->cache($getCache->FHora, 'fechaHoraRotaciones', '.txt');
            return $result;
        } catch (\Throwable $th) {
            throw new \Exception("Error al obtener horarios", 400);
        }
    }
    private function return_total_dias_rotacion($connDB = '', $Codi)
    {
        try {
            $conn = $this->conect->check_connection($connDB);

            $sql  = "SELECT sum(RotDias) FROM ROTACIO1 WHERE RotCodi = :Codi";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':Codi', $Codi, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchColumn();

            if (!$result) {
                throw new \Exception("No se encontraron días de rotación", 400);
            }
            return $result;
        } catch (\Throwable $th) {
            throw new \Exception("Error al obtener días de rotación", 400);
        }
    }
    private function update_desde($arrayData): int
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 2) {
                throw new \Exception("Error: (update_desde) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Codi      = $arrayData[2];
        $Fecha     = $arrayData[3];
        $FechaHora = $this->conect->FechaHora();

        $legajosList = implode(',', $legajos);
        $sql = "UPDATE HORALE1 SET Ho1Hora = :Codi, FechaHora = :FechaHora WHERE Ho1Lega IN ({$legajosList}) AND Ho1Fech = :Fecha";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Codi', $Codi, \PDO::PARAM_INT);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->bindValue(':FechaHora', $FechaHora, \PDO::PARAM_STR);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return  $filas ?? 0;
    }
    private function delete_desde($arrayData): int
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value)) {
                throw new \Exception("Error: (delete_desde) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];

        $legajosList = implode(',', $legajos);
        $sql = "DELETE FROM HORALE1 WHERE Ho1Lega IN ({$legajosList}) AND Ho1Fech = :Fecha";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return  $filas ?? 0;
    }
    private function check_delete_legajos($arrayData): array
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 3) {
                throw new \Exception("Error: (check_delete_legajos) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];
        $Codi      = $arrayData[3];

        $legajosList = implode(',', $legajos);

        if ($this->desde) {
            $sql = "SELECT Ho1Lega AS 'LegNume' FROM HORALE1 WHERE Ho1Fech = :Fecha AND Ho1Hora = :Codi";
            $sql .= " AND Ho1Lega IN ($legajosList)";
        }

        if ($this->desdeHasta) {
            $sql = "SELECT Ho2Lega AS 'LegNume' FROM HORALE2 WHERE Ho2Fec1 = :Fecha AND Ho2Hora = :Codi";
            $sql .= " AND Ho2Lega IN ($legajosList)";
        }

        if ($this->citacion) {
            $sql = "SELECT CitLega AS 'LegNume' FROM CITACION WHERE CitFech = :Fecha";
            $sql .= " AND CitLega IN ($legajosList)";
        }

        if ($this->rotacion) {
            $sql = "SELECT RolLega AS 'LegNume' FROM ROTALEG WHERE RolFech = :Fecha AND RolRota = :Codi";
            $sql .= " AND RolLega IN ($legajosList)";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        (!$this->citacion) ? $stmt->bindValue(':Codi', $Codi, \PDO::PARAM_INT) : '';
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return  $result ? array_column($result, 'LegNume') : [];
    }
    private function get_fecha_fin($arrayData)
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 3) {
                throw new \Exception("Error: (get_fecha_fin) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];
        $Codi      = $arrayData[3];
        $legajosList = implode(',', $legajos);

        if ($this->desdeHasta) {
            $sql = "SELECT Ho2Fec2 FROM HORALE2 WHERE Ho2Lega IN ({$legajosList}) AND Ho2Fec1 = :Fecha AND Ho2Hora = :Codi";
        }
        if ($this->rotacion) {
            $sql = "SELECT RolVenc FROM ROTALEG WHERE RolLega IN ({$legajosList}) AND RolFech = :Fecha AND RolRota = :Codi";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->bindValue(':Codi', $Codi, $Codi ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn() ?? null;
        return $result;
    }
    private function delete_desde_hasta($arrayData): int
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 3) {
                throw new \Exception("Error: (delete_desde_hasta) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];
        $Codi      = $arrayData[3];

        $legajosList = implode(',', $legajos);
        $sql = "DELETE FROM HORALE2 WHERE Ho2Lega IN ({$legajosList}) AND Ho2Fec1 = :Fecha AND Ho2Hora = :Codi";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->bindValue(':Codi', $Codi, $Codi ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return  $filas ?? 0;
    }
    private function delete_rotacion($arrayData): int
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 3) {
                throw new \Exception("Error: (delete_rotacion) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];
        $Codi      = $arrayData[3];

        $legajosList = implode(',', $legajos);
        $sql = "DELETE FROM ROTALEG WHERE RolLega IN ({$legajosList}) AND RolFech = :Fecha AND RolRota = :Codi";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->bindValue(':Codi', $Codi, \PDO::PARAM_INT);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return  $filas ?? 0;
    }
    private function update_citacion($arrayData): int
    {

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];
        $Entr      = $arrayData[3];
        $Sale      = $arrayData[4];
        $Desc      = $arrayData[5];
        $FechaHora = $this->conect->FechaHora();

        $legajosList = implode(',', $legajos);
        $sql = "UPDATE CITACION SET CitEntra = :Entr, CitSale = :Sale, CitDesc = :Desc, FechaHora = :FechaHora WHERE CitLega IN ({$legajosList}) AND CitFech = :Fecha";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Entr', $Entr, \PDO::PARAM_STR);
        $stmt->bindValue(':Sale', $Sale, \PDO::PARAM_STR);
        $stmt->bindValue(':Desc', $Desc, \PDO::PARAM_STR);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->bindValue(':FechaHora', $FechaHora, \PDO::PARAM_STR);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return $filas ?? 0;
    }
    private function delete_citacion($arrayData): int
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value)) {
                throw new \Exception("Error: (delete_citacion) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];

        $legajosList = implode(',', $legajos);
        $sql = "DELETE FROM CITACION WHERE CitLega IN ({$legajosList}) AND CitFech = :Fecha";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return $filas ?? 0;
    }
    private function update_rotacion($arrayData): int
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 2) {
                throw new \Exception("Error: (update_rotacion) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Codi      = $arrayData[2];
        $Fecha     = $arrayData[3];
        $Dias      = $arrayData[4];
        $Vence     = $arrayData[5];
        $FechaHora = $this->conect->FechaHora();

        $legajosList = implode(',', $legajos);
        $sql = "UPDATE ROTALEG SET RolRota = :Codi, FechaHora = :FechaHora, RolVenc = :Vence, RolDias = :RolDias WHERE RolLega IN ({$legajosList}) AND RolFech = :Fecha";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Codi', $Codi, \PDO::PARAM_INT);
        $stmt->bindValue(':Fecha', $Fecha, \PDO::PARAM_STR);
        $stmt->bindValue(':FechaHora', $FechaHora, \PDO::PARAM_STR);
        $stmt->bindValue(':Vence', $Vence, \PDO::PARAM_STR);
        $stmt->bindValue(':RolDias', $Dias, \PDO::PARAM_INT);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return  $filas ?? 0;
    }
    private function update_desde_hasta($arrayData)
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 2) {
                throw new \Exception("Error: (update_desde_hasta) {$key} no puede estar vacío", 400);
            }
        }

        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Codi      = $arrayData[2];
        $FechaD    = $arrayData[3];
        $FechaHora = $this->conect->FechaHora();

        $legajosList = implode(',', $legajos);
        $sql = "UPDATE HORALE2 SET Ho2Hora = :Codi, FechaHora = :FechaHora WHERE Ho2Lega IN ({$legajosList}) AND Ho2Fec1 = :FechaD";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Codi', $Codi, \PDO::PARAM_INT);
        $stmt->bindValue(':FechaD', $FechaD, \PDO::PARAM_STR);
        $stmt->bindValue(':FechaHora', $FechaHora, \PDO::PARAM_STR);
        $stmt->execute();
        $filas = $stmt->rowCount();
        return $filas ?? 0;
    }
    private function insert_desde($arrayData)
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 2) {
                throw new \Exception("Error: (update_desde) {$key} no puede estar vacío", 400);
            }
        }

        $values = [];
        $params = [];
        $index = 0;
        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Codi      = $arrayData[2];
        $Fecha     = $arrayData[3];
        $FechaHora = $this->conect->FechaHora();

        foreach ($legajos as $lega) {
            $values[] = "(:Lega{$index}, :Fecha{$index}, :Codi{$index}, :FechaHora{$index})";
            $params[":Lega{$index}"] = $lega;
            $params[":Fecha{$index}"] = $Fecha;
            $params[":Codi{$index}"] = $Codi;
            $params[":FechaHora{$index}"] = $FechaHora;
            $index++;
        }

        $valuesList = implode(',', $values);
        $sql = "INSERT INTO HORALE1 (Ho1Lega, Ho1Fech, Ho1Hora, FechaHora) VALUES $valuesList";
        $stmt = $conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();
        $filas = $stmt->rowCount();
        return $filas;
    }
    private function insert_citacion($arrayData)
    {
        $values = [];
        $params = [];
        $index = 0;
        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Fecha     = $arrayData[2];
        $Entr      = $arrayData[3];
        $Sale      = $arrayData[4];
        $Desc      = $arrayData[5];
        $Turn = 1;
        $FechaHora = $this->conect->FechaHora();

        foreach ($legajos as $lega) {
            $values[] = "(:Lega{$index}, :Fecha{$index}, :Turn{$index}, :Entr{$index}, :Sale{$index}, :Desc{$index}, :FechaHora{$index})";
            $params[":Lega{$index}"] = $lega;
            $params[":Fecha{$index}"] = $Fecha;
            $params[":Turn{$index}"] = $Turn;
            $params[":Entr{$index}"] = $Entr;
            $params[":Sale{$index}"] = $Sale;
            $params[":Desc{$index}"] = $Desc;
            $params[":FechaHora{$index}"] = $FechaHora;
            $index++;
        }
        $valuesList = implode(',', $values);

        $sql = "INSERT INTO CITACION (CitLega, CitFech, CitTurn, CitEntra, CitSale, CitDesc, FechaHora) VALUES $valuesList";
        $stmt = $conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();
        $filas = $stmt->rowCount();
        return $filas;
    }
    private function insert_rotacion($arrayData)
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 2) {
                throw new \Exception("Error: (insert_rotacion) {$key} no puede estar vacío", 400);
            }
        }

        $values = [];
        $params = [];
        $index = 0;
        $conn    = $arrayData[0];
        $legajos = $arrayData[1];
        $Codi    = $arrayData[2];
        $Fecha   = $arrayData[3];
        $Dias    = $arrayData[4];
        $Vence   = $arrayData[5];
        $FechaHora = $this->conect->FechaHora();
        try {
            foreach ($legajos as $lega) {
                $values[] = "(:Lega{$index}, :Fecha{$index}, :Codi{$index}, :Dias{$index},:Vence{$index}, :FechaHora{$index})";
                $params[":Lega{$index}"] = $lega;
                $params[":Fecha{$index}"] = $Fecha;
                $params[":Codi{$index}"] = $Codi;
                $params[":Dias{$index}"] = $Dias;
                $params[":Vence{$index}"] = $Vence;
                $params[":FechaHora{$index}"] = $FechaHora;
                $index++;
            }

            $valuesList = implode(',', $values);

            $sql = "INSERT INTO ROTALEG (RolLega, RolFech, RolRota, RolDias, RolVenc, FechaHora) VALUES $valuesList";
            $stmt = $conn->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            }

            $stmt->execute();
            $filas = $stmt->rowCount();
            return $filas;
        } catch (\PDOException $th) {
            throw new \Exception('Error al insertar rotaciones', 400);
            // throw new \Exception($th->getMessage(), 400);
        }
    }
    private function insert_desde_hasta($arrayData)
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 2) {
                throw new \Exception("Error: (insert_desde_hasta) {$key} no puede estar vacío", 400);
            }
        }

        $values = [];
        $params = [];
        $index = 0;
        $conn      = $arrayData[0];
        $legajos   = $arrayData[1];
        $Codi      = $arrayData[2];
        $FechaD    = $arrayData[3];
        $FechaH    = $arrayData[4];
        $FechaHora = $this->conect->FechaHora();

        foreach ($legajos as $lega) {
            $values[] = "(:Lega{$index}, :FechaD{$index}, :FechaH{$index}, :Codi{$index}, :FechaHora{$index})";
            $params[":Lega{$index}"] = $lega;
            $params[":FechaD{$index}"] = $FechaD;
            $params[":FechaH{$index}"] = $FechaH;
            $params[":Codi{$index}"] = $Codi;
            $params[":FechaHora{$index}"] = $FechaHora;
            $index++;
        }

        $valuesList = implode(',', $values);
        $sql = "INSERT INTO HORALE2 (Ho2Lega, Ho2Fec1, Ho2Fec2, Ho2Hora, FechaHora) VALUES $valuesList";
        $stmt = $conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }

        $stmt->execute();
        $filas = $stmt->rowCount();
        return $filas;
    }
    private function auditoria($arrayData, $tipo = 'horarios')
    {
        foreach ($arrayData as $key => $value) {
            if (empty($value) && $key !== 1) {
                throw new \Exception("Error: (auditoria) {$key} no puede estar vacío", 400);
            }
        }

        $legajos      = $arrayData[0];
        $Codi         = $arrayData[1];
        $AudTipo      = $arrayData[2];
        $AudTipoText  = $arrayData[3];
        $User         = $arrayData[4];

        $CodiHorario = $Codi ? "{$Codi}" : '0';
        // $CodiStr = $Codi ? LIST_HORARIOS[$Codi] : 'Franco';

        switch ($tipo) {
            case 'horarios':
                $CodiStr = $Codi ? LIST_HORARIOS[$Codi] : 'Franco';
                break;
            case 'horarios-delete':
                $CodiStr = $Codi ? LIST_HORARIOS_DELETE[$Codi] : 'Franco';
                break;
            case 'rotaciones':
                $CodiStr = $Codi ? LIST_ROTACIONES[$Codi] : 'Sin Rotación';
                break;
            case 'rotaciones-delete':
                $CodiStr = $Codi ? LIST_ROTACIONES_DELETE[$Codi] : 'Sin Rotación';
                break;
            case 'citaciones':
                $CodiStr = '';
                break;
            default:
                $CodiStr = '-';
                break;
        }

        foreach ($legajos as $lega) {
            $AudDato = sprintf("%s. Legajo: %s. (%s) %s", $AudTipoText, $lega, $CodiHorario, $CodiStr);
            if ($tipo === 'citaciones') {
                $AudDato = sprintf("%s. Legajo: %s.", $AudTipoText, $lega);
            }
            // if ($tipo === 'horarios-delete') {
            //     $AudDato = sprintf("%s. Legajo: %s.", $AudTipoText, $lega);
            // }
            $arrayAud[] = ['AudTipo' => $AudTipo, 'AudDato' => $AudDato, 'AudUser' => $User];
        }

        $AudDato2 = sprintf("%s. Legajos: (%s). Horario (%s) %s", $AudTipoText, count($legajos), $CodiHorario, $CodiStr);

        if ($tipo === 'citaciones') {
            $AudDato2 = sprintf("%s. Legajos: (%s).", $AudTipoText, count($legajos));
        }
        // if ($tipo === 'horarios-delete') {
        //     $AudDato2 = sprintf("%s. Legajos: (%s).", $AudTipoText, count($legajos));
        // }

        $arr2[] = ['AudTipo' => $AudTipo, 'AudDato' => $AudDato2, 'AudUser' => $User];

        $arrRespuesta = [
            'auditor' => $arrayAud,
            'auditorCH' => $arr2
        ];
        return $arrRespuesta ?? [];
    }
    public function get_horarios($connDB = '')
    {
        function arrDia($tipo, $de, $Ha, $Des, $li, $Ho)
        {
            $mapTipo = [
                '0' => 'No Laboral',
                '1' => 'Laboral',
                '2' => 'Según día',
            ];
            return [
                "Laboral"   => $mapTipo[$tipo],
                "LaboralID" => intval($tipo),
                "Desde"     => $de,
                "Hasta"     => $Ha,
                "Descanso"  => $Des,
                "Limite"    => intval($li),
                "Horas"     => $Ho,
            ];
        }

        $conn = $this->conect->check_connection($connDB);

        $getCache = $this->tools->return_cache($conn, 'HORARIOS', 'fechaHoraHorarios', 'horariosFull');

        if ($getCache->data) {
            $total = $getCache->total;
            return $this->resp->respuesta($getCache->data, $total, 'OK', 200, 0, $total, 0);
        }

        $sql = "SELECT * FROM HORARIOS";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $conn = null;

        foreach ($data as $key => $v) {

            $backgroundColorRgb = $this->intToRgb($v['HorColor']);
            $textColor = $this->getTextColor($v['HorColor']);

            $HorLun = arrDia($v['HorLune'], $v['HorLuDe'], $v['HorLuHa'], $v['HorLuRe'], $v['HorLuLi'], $v['HorLuHs']);
            $HorMar = arrDia($v['HorMart'], $v['HorMaDe'], $v['HorMaHa'], $v['HorMaRe'], $v['HorMaLi'], $v['HorMaHs']);
            $HorMie = arrDia($v['HorMier'], $v['HorMiDe'], $v['HorMiHa'], $v['HorMiRe'], $v['HorMiLi'], $v['HorMiHs']);
            $HorJue = arrDia($v['HorJuev'], $v['HorJuDe'], $v['HorJuHa'], $v['HorJuRe'], $v['HorJuLi'], $v['HorJuHs']);
            $HorVie = arrDia($v['HorVier'], $v['HorViDe'], $v['HorViHa'], $v['HorViRe'], $v['HorViLi'], $v['HorViHs']);
            $HorSab = arrDia($v['HorSaba'], $v['HorSaDe'], $v['HorSaHa'], $v['HorSaRe'], $v['HorSaLi'], $v['HorSaHs']);
            $HorDom = arrDia($v['HorDomi'], $v['HorDoDe'], $v['HorDoHa'], $v['HorDoRe'], $v['HorDoLi'], $v['HorDoHs']);
            $HorFer = arrDia($v['HorFeri'], $v['HorFeDe'], $v['HorFeHa'], $v['HorFeRe'], $v['HorFeLi'], $v['HorFeHs']);

            $horarios[] = [
                "Codi"      => $v['HorCodi'],
                "Desc"      => $v['HorDesc'],
                "ID"        => $v['HorID'],
                "ColorInt"  => floatval($v['HorColor']),
                "Color"     => sprintf('rgb(%d, %d, %d)', $backgroundColorRgb[0], $backgroundColorRgb[1], $backgroundColorRgb[2]),
                "ColorText" => $textColor,
                "FechaHora" => $this->tools->formatDateTime($v['FechaHora'], 'Y-m-d H:i:s'),
                "Lunes"     => $HorLun,
                "Martes"    => $HorMar,
                "Miércoles" => $HorMie,
                "Jueves"    => $HorJue,
                "Viernes"   => $HorVie,
                "Sábado"    => $HorSab,
                "Domingo"   => $HorDom,
                "Feriado"   => $HorFer,
            ];
        }

        $this->log->cache($horarios, 'horariosFull');
        $this->log->cache($getCache->FHora, 'fechaHoraHorarios', '.txt');

        $this->resp->respuesta($horarios, count($horarios), 'OK', 200, 0, count($horarios), 0);
    }
    public function get_rotaciones($connDB = '')
    {
        $conn = $this->conect->check_connection($connDB);

        $getCache = $this->tools->return_cache($conn, 'ROTACION', 'fechaHoraRotaciones', 'rotacionesFull');

        if ($getCache->data) {
            $total = $getCache->total;
            return $this->resp->respuesta($getCache->data, $total, 'OK', 200, 0, $total, 0);
        }

        $sql = "SELECT * FROM ROTACIO1";
        $sql .= " INNER JOIN ROTACION ON ROTACIO1.RotCodi = ROTACION.RotCodi";
        $sql .= " ORDER BY ROTACIO1.RotCodi, ROTACIO1.RotItem";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $conn = null;

        // agrupa por rotacion
        $agrup = $data ? $this->tools->agrupar_por($data, 'RotCodi') : [];

        $ListHorarios = $data ? $this->return_horarios($conn) : [];
        $rota = [];
        if ($agrup) {
            foreach ($agrup as $key => $rot) {
                $totalDias = 0;
                foreach ($rot as $d) {
                    $Horario = $ListHorarios[$d['RotHora']] ?? [];
                    $RotHoraStr = $d['RotHora'] ? $Horario : 'Franco';
                    $rotDias[] = [
                        "RotItem"    => $d['RotItem'],
                        "RotHora"    => $d['RotHora'],
                        "RotHoraStr" => $RotHoraStr,
                        "RotDias"    => $d['RotDias'],
                    ];
                    $totalDias += $d['RotDias'];
                    // $arrHorarios[] = $Horarios;
                }
                $RotDesc = $rot[0]['RotDesc'];
                $rota[$key] = [
                    "RotCodi" => $key,
                    "RotDesc" => $RotDesc,
                    "RotData" => $rotDias,
                    "RotDias" => $totalDias,
                    // "Horarios" => $arrHorarios,
                ];
                unset($rotDias);
                // unset($arrHorarios);
            }
        }
        $rota = array_values($rota);
        $this->log->cache($rota, 'rotacionesFull');
        $this->log->cache($getCache->FHora, 'fechaHoraRotaciones', '.txt');
        $this->resp->respuesta($rota, count($rota), 'OK', 200, 0, count($rota), 0);
    }

    public function get_horale_1($Legajo, $connDB = '', $ListHorarios = [], $return = false)
    {
        $this->validar_legajo($Legajo);

        $conn = $this->conect->check_connection($connDB);

        // $getCache = $this->tools->return_cache($conn, 'HORALE1', "fechaHoraHorale1_{$Legajo}", "horale1Full_{$Legajo}");

        // if ($getCache->data) {
        //     $total = $getCache->total;
        //     if ($return) {
        //         return $getCache->data;
        //     }
        //     return $this->resp->respuesta($getCache->data, $total, 'OK', 200, 0, $total, 0);
        // }

        $sql = "SELECT * FROM HORALE1 WHERE Ho1Lega = :Legajo ORDER BY Ho1Fech DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Legajo', $Legajo, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $conn = null;

        foreach ($data as $key => $value) {
            $data[$key]['Ho1HoraStr'] = $ListHorarios[$value['Ho1Hora']] ?? '';
            $data[$key]['Ho1FechStr'] = $this->tools->date_time_str($value['Ho1Fech'], 'd/m/Y');
        }

        // $this->log->cache($data, "horale1Full_{$Legajo}");
        // $this->log->cache($getCache->FHora, "fechaHoraHorale1_{$Legajo}", '.txt');

        if ($return) {
            return $data;
        }

        $this->resp->respuesta($data, count($data), 'OK', 200, 0, count($data), 0);
    }
    public function get_horale_2($Legajo, $connDB = '', $ListHorarios = [], $return = false)
    {
        $this->validar_legajo($Legajo);

        $conn = $this->conect->check_connection($connDB);

        // $getCache = $this->tools->return_cache($conn, 'HORALE2', "fechaHoraHorale2_{$Legajo}", "horale2Full_{$Legajo}");

        // if ($getCache->data) {
        //     $total = $getCache->total;
        //     if ($return) {
        //         return $getCache->data;
        //     }
        //     return $this->resp->respuesta($getCache->data, $total, 'OK', 200, 0, $total, 0);
        // }

        $sql = "SELECT * FROM HORALE2 WHERE Ho2Lega = :Legajo ORDER BY Ho2Fec1 DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Legajo', $Legajo, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $conn = null;

        foreach ($data as $key => $value) {
            $data[$key]['Ho2HoraStr'] = $ListHorarios[$value['Ho2Hora']] ?? '';
            $data[$key]['Ho2Fec1Str'] = $this->tools->date_time_str($value['Ho2Fec1'], 'd/m/Y');
            $data[$key]['Ho2Fec2Str'] = $this->tools->date_time_str($value['Ho2Fec2'], 'd/m/Y');
        }

        // $this->log->cache($data, "horale2Full_{$Legajo}");
        // $this->log->cache($getCache->FHora, "fechaHoraHorale2_{$Legajo}", '.txt');

        if ($return) {
            return $data;
        }

        $this->resp->respuesta($data, count($data), 'OK', 200, 0, count($data), 0);
    }
    public function get_rotaleg($Legajo, $connDB = '', $return = false)
    {
        $this->validar_legajo($Legajo);

        $conn = $this->conect->check_connection($connDB);

        // $getCache = $this->tools->return_cache($conn, 'ROTALEG', "fechaRotaleg_{$Legajo}", "rotalegFull_{$Legajo}");

        // if ($getCache->data) {
        //     $total = $getCache->total;
        //     if ($return) {
        //         return $getCache->data;
        //     }
        //     return $this->resp->respuesta($getCache->data, $total, 'OK', 200, 0, $total, 0);
        // }

        $sql = "SELECT * FROM ROTALEG WHERE RolLega = :Legajo ORDER BY RolFech DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Legajo', $Legajo, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $conn = null;

        $ListRotaciones = $this->return_rotaciones($connDB) ??  []; // Devuelve las 

        foreach ($data as $key => $value) {
            $data[$key]['RolRotaStr'] = $ListRotaciones[$value['RoLRota']] ?? '';
            $data[$key]['RolFechStr'] = $this->tools->date_time_str($value['RoLFech'], 'd/m/Y');
            $data[$key]['RolVencStr'] = $this->tools->date_time_str($value['RoLVenc'], 'd/m/Y');
            $data[$key]['RolPeriodo'] = $value['RoLVenc'] != '2099-12-31 00:00:00.000' ? true : false;
        }

        // $this->log->cache($data, "rotalegFull_{$Legajo}");
        // $this->log->cache($getCache->FHora, "fechaRotaleg_{$Legajo}", '.txt');

        if ($return) {
            return $data;
        }

        $this->resp->respuesta($data, count($data), 'OK', 200, 0, count($data), 0);
    }
    public function get_citacion($Legajo, $connDB = '', $return = false)
    {
        $this->validar_legajo($Legajo);

        $conn = $this->conect->check_connection($connDB);

        // $getCache = $this->tools->return_cache($conn, 'CITACION', "fechaHoraCitacion_{$Legajo}", "citacionFull_{$Legajo}");

        // if ($getCache->data) {
        //     $total = $getCache->total;
        //     if ($return) {
        //         return $getCache->data;
        //     }
        //     return $this->resp->respuesta($getCache->data, $total, 'OK', 200, 0, $total, 0);
        // }


        $sql = "SELECT * FROM CITACION WHERE CitLega = :Legajo ORDER BY CitFech desc";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':Legajo', $Legajo, \PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $conn = null;

        foreach ($data as $key => $value) {
            $data[$key]['CitFechStr'] = $this->tools->date_time_str($value['CitFech'], 'd/m/Y');
            $citHoras = $this->tools->calcularHorasTrabajadas($value['CitEntra'], $value['CitSale'], $value['CitDesc']);
            $data[$key]['CitHoras'] = $citHoras;
        }

        // $this->log->cache($data, "citacionFull_{$Legajo}");
        // $this->log->cache($getCache->FHora, "fechaHoraCitacion_{$Legajo}", '.txt');

        if ($return) {
            return $data;
        }

        $this->resp->respuesta($data, count($data), 'OK', 200, 0, count($data), 0);
    }
    public function get_asign_legajo($Legajo, $connDB = '', $return = false)
    {
        $this->validar_legajo($Legajo);

        $conn = $this->conect->check_connection($connDB);

        $ListHorarios = $this->return_horarios($connDB) ?? []; // Devuelve los horarios
        $horale1 = $this->get_horale_1($Legajo, $conn, $ListHorarios, true);
        $horale2 = $this->get_horale_2($Legajo, $conn, $ListHorarios, true);
        $rotaleg = $this->get_rotaleg($Legajo, $conn, true);
        $citacion = $this->get_citacion($Legajo, $conn, true);

        $data = [
            'desde' => $horale1 ?? [],
            'desde-hasta' => $horale2 ?? [],
            'rotacion' => $rotaleg ?? [],
            'citacion' => $citacion ?? [],
        ];

        if ($return) {
            return $data;
        }

        $this->resp->respuesta($data, count($data), 'OK', 200, 0, count($data), 0);
    }
    private function validar_legajo($Legajo)
    {
        if (empty($Legajo)) {
            throw new \Exception("Debe enviar un Legajo", 400);
        }

        $intOpt = [
            'options' => [
                'min_range' => 1,
                'max_range' => 2147483647,
            ],
        ];

        if (filter_var($Legajo, FILTER_VALIDATE_INT, $intOpt) === false) {
            throw new \Exception("El campo Legajo debe ser un número entero y menor a 2147483647", 400);
        }
    }
    private function intToRgb($colorInt)
    {
        // Convertir el entero a hexadecimal y quitar el prefijo '0x'
        $hexColor = strtoupper(dechex($colorInt & 0xFFFFFF));

        // Asegurarse de que el string tenga 6 caracteres (rellenar con ceros si es necesario)
        $hexColor = str_pad($hexColor, 6, '0', STR_PAD_LEFT);

        // Separar los componentes RGB
        $red = hexdec(substr($hexColor, 0, 2));
        $green = hexdec(substr($hexColor, 2, 2));
        $blue = hexdec(substr($hexColor, 4, 2));

        return [$red, $green, $blue];
    }
    private function getBrightness($r, $g, $b)
    {
        // Fórmula para calcular el brillo percibido
        return ($r * 299 + $g * 587 + $b * 114) / 1000;
    }
    private function getTextColor($backgroundColor)
    {
        list($r, $g, $b) = $this->intToRgb($backgroundColor);
        $brightness = $this->getBrightness($r, $g, $b);

        // Si el brillo es alto, usa texto negro; si es bajo, usa texto blanco
        return ($brightness > 128) ? 'rgb(0, 0, 0)' : 'rgb(255, 255, 255)';
    }
    public function check_horarios($arrayLegajos, $conn)
    {

        if (!$arrayLegajos) {
            throw new \Exception("No se enviaron legajos para comprobar", 400);
        }

        $ListaLegajos = $this->personal->return_legajos($conn) ?? [];

        if (!$ListaLegajos) {
            throw new \Exception("No se enviaron legajos para comprobar", 400);
        }

        $legajosNoExistentes = [];

        foreach ($arrayLegajos as $lega) {
            if (!array_key_exists($lega, $ListaLegajos)) {
                $legajosNoExistentes[] = $lega;
            }
        }
        if ($legajosNoExistentes ?? []) {
            throw new \Exception("Legajos no existentes: " . implode(', ', $legajosNoExistentes), 400);
        }
        return true;
    }
}