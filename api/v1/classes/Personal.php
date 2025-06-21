<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\InputValidator;
use Classes\RRHHWebService;
use Classes\Tools;
use Classes\ParaGene;
use Classes\Auditor;
use Flight;

class Personal
{
    private $resp;
    private $request;
    private $getData;
    private $query;
    private $log;
    private $conect;
    private $tools;
    private $paraGene;
    private $auditor;
    private $url;

    function __construct()
    {
        $this->resp = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->query = $this->request->query->getData();
        $this->log = new Log;
        $this->conect = new ConnectSqlSrv;
        $this->tools = new Tools;
        $this->paraGene = new ParaGene;
        $this->auditor = new Auditor;
        $this->url = $this->request->url;
    }
    public function return_legajos($connDB = '')
    {
        $conn = $this->conect->check_connection($connDB);
        $FHora = $this->tools->get_fecha_hora($conn, 'PERSONAL');
        $FHoraCache = $this->log->get_cache('fechaHoraPersonal', '.txt') ?? 0;

        if ($FHoraCache >= $FHora) {
            $legajos = $this->log->get_cache('legajos');
            if ($legajos) {
                $conn = null;
                return $legajos;
            }
        }
        return $this->query_legajos($conn, $FHora);
    }
    public function get_existing_legajos($connDB, $arrayDeLegajos)
    {
        $conn = $this->conect->check_connection($connDB);
        $legajos = $this->query_legajos_in($conn, $arrayDeLegajos);
        return $legajos;
    }
    public function legajos()
    {
        $conn = $this->conect->check_connection();
        $data = $this->return_legajos($conn);
        $this->resp->respuesta($data, count($data), 'OK', 200, 0, count($data), 0);
    }

    public function check_legajos($arrayLegajos, $connDB = '')
    {


        if (!$arrayLegajos) {
            throw new \Exception("No se enviaron legajos para comprobar", 400);
        }

        $conn = $this->conect->check_connection($connDB);

        $ListaLegajos = $this->return_legajos($conn) ?? [];

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
    private function query_legajos($conn, $FechaHora)
    {
        $sql = "SELECT LegNume, LegApNo FROM PERSONAL WHERE LegNume > 0 ORDER BY LegNume";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $data = array_column($data, 'LegApNo', 'LegNume');
        $conn = null;
        $this->log->cache($data, 'legajos');
        $this->log->cache($FechaHora, 'fechaHoraPersonal', '.txt');
        return $data ?? [];
    }
    private function query_legajos_in($connDB = '', $array)
    {
        try {
            if (!$array) {
                throw new \Exception("No se enviaron legajos para comprobar (query_legajos_in)", 400);
            }
            $conn = $this->conect->check_connection($connDB);

            $sql = "SELECT LegNume FROM PERSONAL WHERE LegNume IN (" . implode(',', $array) . ") ORDER BY LegNume";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $conn = null;
            $data = array_column($data, 'LegNume');
            return $data ?? [];
        } catch (\Throwable $th) {
            return [];
        }
    }
    public function filtros_estructura()
    {
        $data = $this->inputs_filtros_estructura(); // Validación de inputs

        try {

            $FiltroEstructura = $data['estructura'] ?? 1;
            $Descripcion = $data['descripcion'] ?? '';
            $FiltroEstado = $data['estado'] ?? 2;
            $FiltroTipo = $data['tipo'] ?? 2;
            $FiltroFechaEgreso = $data['activo'] ?? 1;
            $Filtros = $data['filtros'] ?? [];
            $NullCant = $data['nullCant'] ?? 1;
            $Strict = $data['strict'] ?? 1;
            $FiltroProyectar = $data['proyectar'] ?? 0;

            $Emp = $this->array_to_string($Filtros['empresas'] ?? []) ?? '';
            $Pla = $this->array_to_string($Filtros['plantas'] ?? []) ?? '';
            $Con = $this->array_to_string($Filtros['convenios'] ?? []) ?? '';
            $Sec = $this->array_to_string($Filtros['sectores'] ?? []) ?? '';
            $Se2 = $this->array_to_string($Filtros['secciones'] ?? []) ?? '';
            $Gru = $this->array_to_string($Filtros['grupos'] ?? []) ?? '';
            $Suc = $this->array_to_string($Filtros['sucursales'] ?? []) ?? '';
            $Tar = $this->array_to_string($Filtros['tareas'] ?? []) ?? '';
            $Per = $this->array_to_string($Filtros['personal'] ?? []) ?? '';

            $stringCod = [
                1 => $Emp,
                2 => $Pla,
                3 => $Con,
                4 => $Sec,
                5 => $Se2,
                6 => $Gru,
                7 => $Suc,
                8 => $Per,
                9 => $Tar
            ];

            $fnFiltroEstado = function () use ($FiltroEstado) {
                switch ($FiltroEstado) {
                    case 2:
                        break;
                    case 1:
                        return " AND P.LegEsta = 1";
                    default:
                        return " AND P.LegEsta = 0";
                }
            };
            $fnFiltroProyectar = function () use ($FiltroProyectar) {
                switch ($FiltroProyectar) {
                    case 2:
                        break;
                    case 1:
                        return " AND P.LegProyeHoras = 1";
                    default:
                        return " AND P.LegProyeHoras = 0";
                }
            };


            $fnFiltroFechaEgreso = function () use ($FiltroFechaEgreso) {
                switch ($FiltroFechaEgreso) {
                    case 1:
                        return " AND P.LegFeEg = '1753-01-01 00:00:00.000'";
                    case 2:
                        return " AND P.LegFeEg != '1753-01-01 00:00:00.000'";
                    default:
                        return '';
                }
            };
            $fnFiltroTipo = function () use ($FiltroTipo) {
                switch ($FiltroTipo) {
                    case 2:
                        break;
                    case 1:
                        return " AND P.LegTipo = 1";
                    default:
                        return " AND P.LegTipo = 0";
                }
            };

            $arr = array_values($stringCod);
            $fnPersonalFilters = function () use ($arr, $FiltroEstructura, $Strict) {

                $campos = [
                    'LegEmpr' => [0, 'P.LegEmpr'],
                    'LegPlan' => [1, 'P.LegPlan'],
                    'LegConv' => [2, 'P.LegConv'],
                    'LegSect' => [3, 'P.LegSect'],
                    'LegSec2' => [4, 'CONCAT(P.LegSect, P.LegSec2)'],
                    'LegGrup' => [5, 'P.LegGrup'],
                    'LegSucu' => [6, 'P.LegSucu'],
                    'LegNume' => [7, 'P.LegNume'],
                    'LegTare' => [8, 'P.LegTareProd'],
                ];

                $sql = '';
                // Función interna para armar el filtro correctamente como int
                $whereIn = function ($valor, $campo, $not = false) {
                    if (strlen($valor) > 0 && strpos($valor, ',') !== false) {
                        return " AND {$campo} " . ($not ? 'NOT ' : '') . "IN ({$valor})";
                    }
                    return " AND {$campo} " . ($not ? '!' : '') . "= {$valor}";
                };

                foreach ($campos as $key => [$idx, $campo]) {
                    $valor = isset($arr[$idx]) && $arr[$idx] === '-1' ? '0' : ($arr[$idx] ?? '');
                    if (($FiltroEstructura - 1) === $idx) {
                        $ifValor = $valor === '' ? false : true;
                        if ($Strict) { // Si es estricto, no permite valores vacíos
                            if ($ifValor) { // si no esta vacio
                                $sql .= $whereIn($valor, $campo, false); // Agrega el filtro IN
                            }
                        } else if ($ifValor) { // Si no es estricto y el valor no está vacío
                            $sql .= $whereIn($valor, $campo, false); // Agrega el filtro IN
                        }
                    } else if ($valor !== '') {
                        $sql .= $whereIn($valor, $campo);
                    }
                }
                return $sql;
            };

            $db = $this->conect->check_connection();

            $sql = "";

            $dataE = $this->data_estructura($FiltroEstructura, $Descripcion);


            $entidades = $NullCant ? $this->get_entidades(
                $db,
                $FiltroEstructura,
                $stringCod[$FiltroEstructura] ?? '',
                $dataE
            ) : [];

            $sql .= $dataE['sql'] ?? '';
            $sql .= $fnPersonalFilters(); // Filtros en tabla de entidades
            $sql .= $dataE['sqlDesc'] ?? '';

            $sql .= $fnFiltroEstado(); // Filtro de LegEsta
            $sql .= $fnFiltroFechaEgreso(); // Filtro de fecha de LegFeEg
            $sql .= $fnFiltroTipo(); // Filtro de tipo de personal LegTipo
            $sql .= $fnFiltroProyectar(); // Filtro de proyección de horas LegProyeHoras
            $sql .= $dataE['sqlGroup'] ?? '';

            // $this->log->write($sql, 'sql_filtros.sql');

            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);


            if ($entidades && $NullCant) {
                $count = 0;
                // Definir función para obtener la clave única según el filtro
                $getKey = function ($row) use ($FiltroEstructura) {
                    return $FiltroEstructura === 5
                        ? ($row['CodSect'] ?? '') . $row['Cod']
                        : $row['Cod'];
                };

                // Crear array asociativo de claves existentes para búsquedas rápidas
                $codigosExistentes = [];
                foreach ($result as $row) {
                    $codigosExistentes[$getKey($row)] = true;
                }

                foreach ($entidades as $item) {
                    $key = $getKey($item);
                    if (!isset($codigosExistentes[$key])) {
                        $nuevo = [
                            'Cod' => $item['Cod'],
                            'Descripcion' => $item['Descripcion'],
                            'Cantidad' => 0
                        ];
                        if ($FiltroEstructura === 5) {
                            $nuevo['CodSect'] = $item['CodSect'] ?? '';
                            $nuevo['Sector'] = $item['Sector'] ?? '';
                        }
                        $result[] = $nuevo;
                        $count++;
                    }
                }
            }

            $claveSecundaria = ($FiltroEstructura === 5) ? 'CodSect' : 'Cod';

            // Ordenar el resultado por Cantidad de forma descendente y por clave secundaria
            usort($result, function ($a, $b) use ($claveSecundaria) {
                if ($a['Cantidad'] == $b['Cantidad']) {
                    return $a[$claveSecundaria] <=> $b[$claveSecundaria]; // Descendente por clave secundaria
                }
                return $b['Cantidad'] <=> $a['Cantidad']; // Descendente por Cantidad
            });

            $estructName = $dataE['estructura'] ?? '';
            $total = count($result);
            $data = [
                'estructura' => $estructName,
                'total' => $total,
                'total_con_datos' => $total - ($count ?? 0),
                'total_sin_datos' => ($count ?? 0),
                'data' => $result,
                'filtros' => [
                    'estructura' => $FiltroEstructura,
                    'activo' => $FiltroFechaEgreso,
                    'tipo' => $FiltroTipo,
                    'estado' => $FiltroEstado,
                    'descripcion' => $Descripcion,
                    'empresas' => $Emp,
                    'plantas' => $Pla,
                    'convenios' => $Con,
                    'sectores' => $Sec,
                    'secciones' => $Se2,
                    'grupos' => $Gru,
                    'sucursales' => $Suc,
                    'tareas' => $Tar,
                    'personal' => $Per
                ]
            ];
            $this->resp->respuesta($data, $total, 'OK', 200, 0, $total, 0);
        } catch (\Throwable $th) {
            $this->log->write($th->getMessage(), date('Ymd') . '_personal_filtros_estructura.log');
            $this->resp->respuesta('', 0, $th->getMessage(), 400, microtime(true), 0, 0);
        }
    }
    private function data_estructura($estructura, $Descripcion = '')
    {
        static $cache = [];
        $cacheKey = "{$estructura}-{$Descripcion}";
        if (isset($cache[$cacheKey])) {
            // \file_put_contents('data_estructura.txt', "Cache hit for key: {$cacheKey}\n", FILE_APPEND);
            return $cache[$cacheKey];
        }
        $a = [
            1 => [
                'sql' => "SELECT P.LegEmpr AS Cod, E.EmpRazon AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN EMPRESAS E ON P.LegEmpr=E.EmpCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(E.EmpCodi,E.EmpRazon) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegEmpr, E.EmpRazon ORDER BY E.EmpRazon",
                'table' => 'EMPRESAS',
                'column' => 'EmpCodi',
                'description' => 'EmpRazon',
                'estructura' => 'empresas'
            ],
            2 => [
                'sql' => "SELECT P.LegPlan AS Cod, E.PlaDesc AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN PLANTAS E ON P.LegPlan=E.PlaCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(E.PlaCodi,E.PlaDesc) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegPlan, E.PlaDesc ORDER BY E.PlaDesc",
                'table' => 'PLANTAS',
                'column' => 'PlaCodi',
                'description' => 'PlaDesc',
                'estructura' => 'plantas'
            ],
            3 => [
                'sql' => "SELECT P.LegConv AS Cod, E.ConDesc AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN CONVENIO E ON P.LegConv=E.ConCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(E.ConCodi,E.ConDesc) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegConv, E.ConDesc ORDER BY E.ConDesc",
                'table' => 'CONVENIO',
                'column' => 'ConCodi',
                'description' => 'ConDesc',
                'estructura' => 'convenios'
            ],
            4 => [
                'sql' => "SELECT P.LegSect AS Cod, E.SecDesc AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN SECTORES E ON P.LegSect=E.SecCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(E.SecCodi,E.SecDesc) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegSect, E.SecDesc ORDER BY E.SecDesc",
                'table' => 'SECTORES',
                'column' => 'SecCodi',
                'description' => 'SecDesc',
                'estructura' => 'sectores'
            ],
            5 => [
                'sql' => "SELECT P.LegSec2 AS Cod, E.Se2Desc AS Descripcion, P.LegSect AS CodSect, S.SecDesc AS Sector, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN SECCION E ON CONCAT(E.SecCodi, E.Se2Codi) = CONCAT(P.LegSect, P.LegSec2) INNER JOIN SECTORES S ON E.SecCodi = S.SecCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(E.Se2Codi,E.Se2Desc) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegSec2, E.Se2Desc, P.LegSect, S.SecDesc ORDER BY E.Se2Desc",
                'table' => 'SECCION',
                'column' => 'Se2Codi',
                'description' => 'Se2Desc',
                'estructura' => 'secciones'
            ],
            6 => [
                'sql' => "SELECT P.LegGrup AS Cod, E.GruDesc AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN GRUPOS E ON P.LegGrup=E.GruCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(E.GruCodi, E.GruDesc) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegGrup, E.GruDesc ORDER BY E.GruDesc",
                'table' => 'GRUPOS',
                'column' => 'GruCodi',
                'description' => 'GruDesc',
                'estructura' => 'grupos'
            ],
            7 => [
                'sql' => "SELECT P.LegSucu AS Cod, E.SucDesc AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN SUCURSALES E ON P.LegSucu=E.SucCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(E.SucCodi, E.SucDesc) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegSucu, E.SucDesc ORDER BY E.SucDesc",
                'table' => 'SUCURSALES',
                'column' => 'SucCodi',
                'description' => 'SucDesc',
                'estructura' => 'sucursales'
            ],
            8 => [
                'sql' => "SELECT P.LegNume AS Cod, P.LegApNo AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND CONCAT(P.LegNume,P.LegApNo) LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegNume, P.LegApNo ORDER BY P.LegApNo",
                'table' => 'PERSONAL',
                'column' => 'LegNume',
                'description' => 'LegApNo',
                'estructura' => 'personal'
            ],
            9 => [
                'sql' => "SELECT P.LegTareProd AS Cod, E.TareDesc AS Descripcion, COUNT(*) AS Cantidad FROM PERSONAL P LEFT JOIN TAREAS E ON P.LegTareProd=E.TareCodi WHERE P.LegNume > 0",
                'sqlDesc' => $Descripcion ? " AND E.TareDesc LIKE '%$Descripcion%'" : '',
                'sqlGroup' => " GROUP BY P.LegTareProd, E.TareDesc ORDER BY E.TareDesc",
                'table' => 'TAREAS',
                'column' => 'TareCodi',
                'description' => 'TareDesc',
                'estructura' => 'tareas'
            ]
        ];
        if (!array_key_exists($estructura, $a)) { // Verifica si la estructura es válida
            throw new \Exception("Estructura no valida. Estructuras válidas: " . implode(", ", array_keys($a)), 400);
        }
        // \file_put_contents('data_estructura.txt', "Cache miss for key: {$cacheKey}\n", FILE_APPEND);
        return $cache[$cacheKey] = $a[$estructura];
    }
    private function get_entidades($db, $estructura, $codi, $dataE)
    {
        if ($estructura === 8) {
            return [];
            // Si la estructura es 'personal', no se necesita consultar la base de datos
        }

        $Tabl = $dataE['table'] ?? '';
        $Codi = $dataE['column'] ?? '';
        $Desc = $dataE['description'] ?? '';

        $query = "SELECT $Codi AS Cod, $Desc AS Descripcion FROM $Tabl";
        if ($estructura === 5) {
            $query = "SELECT
                E.Se2Codi AS Cod,
                E.Se2Desc AS Descripcion,
                E.SecCodi AS CodSect,
                S.SecDesc AS Sector
            FROM
                SECCION E
                INNER JOIN SECTORES S ON E.SecCodi = S.SecCodi";
            if ($codi) {
                $query .= ($codi === '-1')
                    ? " WHERE CONCAT(E.SecCodi, E.Se2Codi) IN (0)"
                    : " WHERE CONCAT(E.SecCodi, E.Se2Codi) IN ($codi)";
            }
        } else {
            if ($codi) {
                $query .= ($codi === '-1')
                    ? " WHERE $Codi IN (0)"
                    : " WHERE $Codi IN ($codi)";
            }
        }
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];
    }
    public function inputs_filtros_estructura()
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
                'estructura' => ['required', 'numeric'],
                'activo' => ['allowed012'],
                'tipo' => ['allowed012'],
                'estado' => ['allowed012'],
                'descripcion' => ['varchar100'],
                'nullCant' => ['allowed01'],
                'strict' => ['allowed01'],
                'proyectar' => ['allowed012'],
            ];

            $customValueKey = array( // Valores por defecto
                'estructura' => 1,
                'activo' => 1,
                'tipo' => 2,
                'estado' => 2,
                'nullCant' => 0,
                'strict' => 0,
                'proyectar' => 2,
            );
            $keyData = array_keys($customValueKey); // Obtengo las claves del array $customValueKey
            foreach ($keyData as $key) { // Recorro las claves
                if (!array_key_exists($key, $datos)) { // Si no existe la clave en $datos
                    $datos[$key] = $customValueKey[$key]; // Asigno el valor por defecto
                }
            }

            $this->data_estructura($datos['estructura'] ?? '', $datos['descripcion'] ?? ''); // Validación de la estructura

            $validator = new InputValidator($datos, $rules); // Instancia 
            $validator->validate(); // Valido los datos

            $filtros = $datos['filtros'] ?? []; // Obtengo los filtros del array $datos
            // \file_put_contents('filtros_estructura.json', print_r(json_encode($datos['filtros']), true), FILE_APPEND);
            foreach ($datos['filtros']['secciones'] as $key => $value) {
                if ($value === '00' || $value == '0' || $value == '00') {
                    $datos['filtros']['secciones'][$key] = "0";
                }
            }
            // Asegúrate de actualizar $filtros con los cambios
            $filtros = $datos['filtros'];
            // \file_put_contents('filtros_estructura.json', print_r(json_encode($datos['filtros']), true), FILE_APPEND);
            $rulesFiltros = [ // Reglas de validación para los filtros
                'empresas' => ['arrInt'],
                'plantas' => ['arrInt'],
                'convenios' => ['arrInt'],
                'sectores' => ['arrInt'],
                'secciones' => ['arrInt'],
                'grupos' => ['arrInt'],
                'sucursales' => ['arrInt'],
                'personal' => ['arrInt'],
            ];

            if (is_array($filtros)) {
                $validatorFiltros = new InputValidator($filtros, $rulesFiltros); // Instancia 
                $validatorFiltros->validate(); // Valido los datos
            }

            // Flight::json($datos);
            return $datos;
        } catch (\Exception $e) {
            $this->resp->respuesta('', 0, $e->getMessage(), 400, microtime(true), 0, 0);
            $this->log->write($e->getMessage(), date('Ymd') . '_inputs_filtros_estructura.log');
        }
    }
    private function array_to_string($array)
    {
        if (empty($array)) {
            return '';
        }
        $values = array_map(function ($item) {
            return $item;
        }, $array);
        $result = implode(', ', $values);
        if ($result == '0' || $result === 0) {
            return "-1";
        }
        return $result;
    }
}
