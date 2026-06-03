<?php

namespace Classes;

use Classes\ConnectSqlSrv;
use Classes\Response;
use Classes\InputValidator;
use Classes\ValidationException;
use Classes\Auditor;
use Classes\Log;
use Flight;
use flight\net\Request;

class Rotacion
{
    private ConnectSqlSrv $conect;
    private Request $request;
    private array $getData;
    private Response $resp;
    private Log $log;
    private Auditor $auditor;
    private float $inicio;
    private string $NameLog;


    public function __construct()
    {
        $this->conect = new ConnectSqlSrv;
        $this->resp = new Response();
        $this->log = new Log();
        $this->auditor = new Auditor();
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->NameLog = date('Ymd') . '_rotacion.log';
    }

    // ─────────────────────────────────────────────────────────────────
    // Métodos públicos
    // ─────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->inicio = microtime(true);
        $idCompany = \defined('ID_COMPANY') ? ID_COMPANY : 0;

        $items = $this->normalizeItems($this->getData);

        if (empty($items)) {
            throw new \Exception('No se recibieron datos', 400);
        }

        $insertados = [];
        $actualizados = [];
        $errores = [];
        $conn = $this->conect->conn();

        try {
            $conn->beginTransaction();

            foreach ($items as $item) {
                $audUser = isset($item['User']) && $item['User'] !== '' ? (string) $item['User'] : '';

                try {
                    $item = $this->applyDefaults($item, (new ConnectSqlSrv())->FechaHora());
                    $this->validateRotacionItem($item);

                    $rotCodi = (int) $item['RotCodi'];
                    $detalle = $this->normalizeAndValidateDetalle($item['Horarios'], $rotCodi, $errores);

                    if (empty($detalle)) {
                        $errores[] = [
                            'RotCodi' => $rotCodi,
                            'error' => 'No hay horarios válidos para procesar en ROTACIO1',
                        ];
                        continue;
                    }

                    if ($this->existeRotacion($rotCodi, $conn)) {
                        $this->updateRotacion($item, $conn);
                        $this->deleteDetalleRotacion($rotCodi, $conn);
                        $this->insertDetalleRotacion($rotCodi, $detalle, $item['FechaHora'], $conn);

                        $this->auditor->add([[
                            'AudUser' => $audUser,
                            'AudTipo' => 'M',
                            'AudDato' => "Rotación $rotCodi",
                        ]]);

                        $actualizados[] = [
                            'RotCodi' => $rotCodi,
                            'RotDesc' => $item['RotDesc'],
                        ];
                        continue;
                    }

                    $this->insertRotacion($item, $conn);
                    $this->insertDetalleRotacion($rotCodi, $detalle, $item['FechaHora'], $conn);

                    $this->auditor->add([[
                        'AudUser' => $audUser,
                        'AudTipo' => 'A',
                        'AudDato' => "Rotación $rotCodi",
                    ]]);

                    $insertados[] = [
                        'RotCodi' => $rotCodi,
                        'RotDesc' => $item['RotDesc'],
                    ];
                } catch (ValidationException $e) {
                    $errores[] = [
                        'RotCodi' => $item['RotCodi'] ?? null,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            if (empty($insertados) && empty($actualizados)) {
                $conn->rollBack();
                $this->resp->respuesta(
                    ['errores' => $errores],
                    0,
                    'No se procesó ningún registro',
                    400,
                    $this->inicio,
                    \count($items),
                    $idCompany
                );
                return;
            }

            $conn->commit();
            $this->resp->respuesta(
                [
                    'insertados' => $insertados,
                    'actualizados' => $actualizados,
                    'errores' => $errores,
                ],
                \count($insertados) + \count($actualizados),
                'OK',
                200,
                $this->inicio,
                \count($items),
                $idCompany
            );
        } catch (\PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al crear rotación', 400);
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function delete(): void
    {
        $this->inicio = microtime(true);
        $idCompany = \defined('ID_COMPANY') ? ID_COMPANY : 0;

        $items = $this->normalizeItems($this->getData);

        if (empty($items)) {
            $this->resp->respuesta([], 0, 'No se recibieron datos', 400, $this->inicio, 0, $idCompany);
            return;
        }

        $eliminados = [];
        $errores = [];
        $conn = $this->conect->conn();

        $rules = [
            'RotCodi' => ['required', 'smallint'],
            'User' => ['varchar100'],
        ];

        try {
            $conn->beginTransaction();

            foreach ($items as $item) {
                try {
                    (new InputValidator($item, $rules))->validate();
                } catch (ValidationException $e) {
                    $errores[] = [
                        'RotCodi' => $item['RotCodi'] ?? null,
                        'error' => $e->getMessage(),
                    ];
                    continue;
                }

                $audUser = isset($item['User']) && $item['User'] !== '' ? (string) $item['User'] : '';
                $rotCodi = (int) $item['RotCodi'];

                if (!$this->existeRotacion($rotCodi, $conn)) {
                    $errores[] = [
                        'RotCodi' => $rotCodi,
                        'error' => "La rotación {$rotCodi} no existe",
                    ];
                    continue;
                }

                $tablaConflicto = $this->checkConsistencia($rotCodi, $conn);
                if ($tablaConflicto !== null) {
                    $errores[] = [
                        'RotCodi' => $rotCodi,
                        'error' => "No se puede eliminar la rotación {$rotCodi}: existen asignaciones relacionadas ({$tablaConflicto})",
                    ];
                    continue;
                }

                $this->deleteDetalleRotacion($rotCodi, $conn);
                $this->deleteRotacion($rotCodi, $conn);

                $this->auditor->add([[
                    'AudUser' => $audUser,
                    'AudTipo' => 'B',
                    'AudDato' => 'Rotación ' . $rotCodi,
                ]]);

                $eliminados[] = ['RotCodi' => $rotCodi];
            }

            if (empty($eliminados)) {
                $conn->rollBack();
                $this->resp->respuesta(
                    ['errores' => $errores],
                    0,
                    'No se eliminó ningún registro',
                    400,
                    $this->inicio,
                    count($items),
                    $idCompany
                );
                return;
            }

            $conn->commit();
            $this->resp->respuesta(
                ['eliminados' => $eliminados, 'errores' => $errores],
                count($eliminados),
                'OK',
                200,
                $this->inicio,
                count($items),
                $idCompany
            );
        } catch (\PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al eliminar rotación', 400);
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function get(): void
    {
        $this->inicio = microtime(true);
        $idCompany = \defined('ID_COMPANY') ? ID_COMPANY : 0;

        try {
            $query = $this->request->query->getData();
            $paginacion = $this->parseAndValidateGetParams($query);
            $start = (int) $paginacion['start'];
            $length = (int) $paginacion['length'];
            $rotDesc = (string) $paginacion['RotDesc'];
            $rotCodi = (array) $paginacion['RotCodi'];

            $conn = $this->conect->conn();
            $whereData = $this->buildWhereClause($rotDesc, $rotCodi);
            $where = (string) $whereData['sql'];
            $params = (array) $whereData['params'];

            $total = $this->fetchTotal($conn, $where, $params);
            $data = $this->fetchRotaciones($conn, $where, $params, $start, $length);

            $rotCodis = $this->collectRotCodis($data);
            $horariosPorRotacion = $this->fetchHorariosPorRotacion($conn, $rotCodis);

            $this->mergeHorarios($data, $horariosPorRotacion, []);

            $rotHoras = $this->collectRotHoras($data);
            $horariosDescMap = $this->fetchHorariosDescMap($conn, $rotHoras);
            $this->mergeHorarios($data, [], $horariosDescMap);

            $this->resp->respuesta(
                $data,
                $total,
                'OK',
                200,
                $this->inicio,
                \count($data),
                $idCompany
            );
        } catch (\PDOException $e) {
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al obtener rotaciones', 400);
        } catch (\Exception $e) {
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function getUnused()
    {
        $this->inicio = microtime(true);
        $idCompany = \defined('ID_COMPANY') ? ID_COMPANY : 0;

        try {
            $conn = $this->conect->conn();

            $sql = "SELECT RotCodi, RotDesc FROM ROTACION r
                    WHERE r.RotCodi > 0
                      AND NOT EXISTS (SELECT 1 FROM ROTALEG WHERE RoLRota  = r.RotCodi)
                      AND NOT EXISTS (SELECT 1 FROM ROTASEC WHERE RoSRota = r.RotCodi)
                      AND NOT EXISTS (SELECT 1 FROM ROTAGRU  WHERE RoGRota  = r.RotCodi)
                    ORDER BY r.RotCodi";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $this->resp->respuesta(
                $data,
                \count($data),
                'OK',
                200,
                $this->inicio,
                \count($data),
                $idCompany
            );
        } catch (\PDOException $e) {
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al obtener horarios unused', 400);
        } catch (\Exception $e) {
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function deleteUnused(): void
    {
        $this->inicio = microtime(true);
        $idCompany = \defined('ID_COMPANY') ? ID_COMPANY : 0;

        $conn = $this->conect->conn();

        try {

            $body = $this->getData;
            $audUser = (isset($body['User']) && $body['User'] !== '') ? $body['User'] : '';

            // Obtener todos los HorCodi sin referencias
            $sqlSelect = "SELECT RotCodi, RotDesc FROM ROTACION r
                    WHERE r.RotCodi > 0
                      AND NOT EXISTS (SELECT 1 FROM ROTALEG WHERE RoLRota  = r.RotCodi)
                      AND NOT EXISTS (SELECT 1 FROM ROTASEC WHERE RoSRota = r.RotCodi)
                      AND NOT EXISTS (SELECT 1 FROM ROTAGRU  WHERE RoGRota  = r.RotCodi)";

            $stmt = $conn->prepare($sqlSelect);
            $stmt->execute();
            $unused = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];

            if (empty($unused)) {
                $this->resp->respuesta(
                    ['eliminados' => []],
                    0,
                    'No hay rotaciones sin uso para eliminar',
                    200,
                    $this->inicio,
                    0,
                    $idCompany
                );
                return;
            }

            $conn->beginTransaction();

            $sqlDelete = "DELETE FROM ROTACION WHERE RotCodi = :RotCodi";
            $stmtDel = $conn->prepare($sqlDelete);

            $eliminados = [];
            $auditItems = [];

            foreach ($unused as $row) {
                $rotCodi = (int) $row['RotCodi'];
                $stmtDel->bindValue(':RotCodi', $rotCodi, \PDO::PARAM_INT);
                $stmtDel->execute();
                $this->deleteDetalleRotacion($rotCodi, $conn);
                $eliminados[] = ['RotCodi' => $rotCodi, 'RotDesc' => $row['RotDesc']];
                $auditItems[] = ['AudUser' => $audUser, 'AudTipo' => 'B', 'AudDato' => "Rotacion {$rotCodi}"];
            }

            $conn->commit();

            $this->auditor->add($auditItems);

            $this->resp->respuesta(
                ['eliminados' => $eliminados],
                \count($eliminados),
                'OK',
                200,
                $this->inicio,
                \count($eliminados),
                $idCompany
            );
        } catch (\PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al eliminar rotaciones sin asignar', 400);
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Rotacion::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Métodos privados
    // ─────────────────────────────────────────────────────────────────

    private function parseAndValidateGetParams(array $query): array
    {
        $start = $query['start'] ?? '';
        $length = $query['length'] ?? '';
        $rotDesc = $query['RotDesc'] ?? '';
        $rotCodi = $this->normalizeRotCodiFilter($query['RotCodi'] ?? '', $this->extractRepeatedQueryParamValues('RotCodi'));

        $paginacion = [
            'start' => $start === '' ? 0 : $start,
            'length' => $length === '' ? 10 : $length,
            'RotDesc' => $rotDesc === '' ? '' : $rotDesc,
            'RotCodi' => $rotCodi,
        ];

        $rules = [
            'start' => ['intempty'],
            'length' => ['intempty'],
            'RotDesc' => ['varchar40'],
        ];

        (new InputValidator($paginacion, $rules))->validate();

        if (!empty($paginacion['RotCodi'])) {
            (new InputValidator(['RotCodi' => $paginacion['RotCodi']], ['RotCodi' => ['arrSmallint']]))->validate();
        }

        return $paginacion;
    }

    private function buildWhereClause(string $rotDesc, array $rotCodi): array
    {
        $where = ' WHERE RotCodi > 0';
        $params = [];

        if ($rotDesc !== '') {
            $where .= ' AND (RotDesc LIKE :RotDesc1 OR CONCAT(RotCodi, RotDesc) LIKE :RotDesc2 OR CONCAT(RotCodi, CHAR(32), RotDesc) LIKE :RotDesc3)';
            $like = '%' . $rotDesc . '%';
            $params[':RotDesc1'] = $like;
            $params[':RotDesc2'] = $like;
            $params[':RotDesc3'] = $like;
        }
        if (!empty($rotCodi)) {
            if (count($rotCodi) === 1) {
                $where .= ' AND RotCodi = :RotCodi';
                $params[':RotCodi'] = (int) $rotCodi[0];
            } else {
                $placeholders = [];
                foreach (array_values($rotCodi) as $index => $value) {
                    $param = ':RotCodiIn' . $index;
                    $placeholders[] = $param;
                    $params[$param] = (int) $value;
                }
                $where .= ' AND RotCodi IN (' . implode(', ', $placeholders) . ')';
            }
        }
        return [
            'sql' => $where,
            'params' => $params,
        ];
    }

    private function fetchTotal(\PDO $conn, string $where, array $params): int
    {
        $sqlTotal = 'SELECT COUNT(*) FROM ROTACION' . $where;
        $stmtTotal = $conn->prepare($sqlTotal);

        $this->bindWhereParams($stmtTotal, $params);

        $stmtTotal->execute();
        return (int) $stmtTotal->fetchColumn();
    }

    private function fetchRotaciones(\PDO $conn, string $where, array $params, int $start, int $length): array
    {
        $sql = 'SELECT * FROM ROTACION' . $where . ' ORDER BY RotCodi OFFSET :start ROWS FETCH NEXT :length ROWS ONLY';
        $stmt = $conn->prepare($sql);

        $this->bindWhereParams($stmt, $params);

        $stmt->bindValue(':length', $length, \PDO::PARAM_INT);
        $stmt->bindValue(':start', $start, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function bindWhereParams(\PDOStatement $stmt, array $params): void
    {
        foreach ($params as $param => $value) {
            $type = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }
    }

    /**
     * Normaliza el filtro RotCodi aceptando valor único o múltiples ocurrencias.
     *
     * @param mixed $rotCodi Valor recibido desde query (string|int|array|null).
     * @param array<int, mixed> $repeatedValues Valores repetidos extraídos del query string.
     * @return array<int, string|int>
     */
    private function normalizeRotCodiFilter($rotCodi, array $repeatedValues = []): array
    {
        if (!empty($repeatedValues)) {
            $values = $repeatedValues;
        } elseif (\is_array($rotCodi)) {
            $values = $rotCodi;
        } elseif ($rotCodi === null || $rotCodi === '') {
            $values = [];
        } else {
            $values = [$rotCodi];
        }

        $normalized = [];
        foreach ($values as $value) {
            if ($value === null) {
                continue;
            }

            $value = is_string($value) ? trim($value) : $value;
            if ($value === '') {
                continue;
            }

            $normalized[] = $value;
        }

        return array_values(array_unique($normalized));
    }

    private function extractRepeatedQueryParamValues(string $paramName): array
    {
        $queryString = isset($_SERVER['QUERY_STRING']) ? (string) $_SERVER['QUERY_STRING'] : '';
        if ($queryString === '') {
            return [];
        }

        $values = [];
        $pairs = explode('&', $queryString);

        foreach ($pairs as $pair) {
            if ($pair === '') {
                continue;
            }

            $parts = explode('=', $pair, 2);
            $rawKey = urldecode($parts[0]);
            if ($rawKey !== $paramName) {
                continue;
            }

            $rawValue = isset($parts[1]) ? urldecode($parts[1]) : '';
            $values[] = $rawValue;
        }

        return $values;
    }

    private function collectRotCodis(array $data): array
    {
        $rotCodis = [];
        foreach ($data as $row) {
            $rotCodi = isset($row['RotCodi']) ? (int) $row['RotCodi'] : 0;
            if ($rotCodi > 0) {
                $rotCodis[$rotCodi] = $rotCodi;
            }
        }

        return $rotCodis;
    }

    private function fetchHorariosPorRotacion(\PDO $conn, array $rotCodis): array
    {
        $horariosPorRotacion = [];
        if (empty($rotCodis)) {
            return $horariosPorRotacion;
        }

        $placeholders = [];
        $binds = [];
        $index = 0;

        foreach ($rotCodis as $rotCodi) {
            $param = ':RotCodiIn' . $index;
            $placeholders[] = $param;
            $binds[$param] = $rotCodi;
            $index++;
        }

        $sqlHorarios = 'SELECT RotCodi, RotItem, RotHora, RotDias FROM ROTACIO1 WHERE RotCodi IN (' . implode(', ', $placeholders) . ') ORDER BY RotCodi, RotItem';
        $stmtHorarios = $conn->prepare($sqlHorarios);

        foreach ($binds as $param => $value) {
            $stmtHorarios->bindValue($param, (int) $value, \PDO::PARAM_INT);
        }

        $stmtHorarios->execute();
        $rowsHorarios = $stmtHorarios->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($rowsHorarios as $horario) {
            $key = (int) $horario['RotCodi'];
            if (!isset($horariosPorRotacion[$key])) {
                $horariosPorRotacion[$key] = [];
            }
            $horariosPorRotacion[$key][] = $horario;
        }

        return $horariosPorRotacion;
    }

    private function collectRotHoras(array $data): array
    {
        $rotHoras = [];

        foreach ($data as $row) {
            $horarios = $row['Horarios'] ?? [];
            if (!is_array($horarios)) {
                continue;
            }

            foreach ($horarios as $horario) {
                if (!is_array($horario)) {
                    continue;
                }

                $rotHora = $horario['RotHora'] ?? null;
                if ($rotHora === null || $rotHora === '' || !is_numeric($rotHora)) {
                    continue;
                }

                $rotHora = (int) $rotHora;
                $rotHoras[$rotHora] = $rotHora;
            }
        }

        return $rotHoras;
    }

    private function fetchHorariosDescMap(\PDO $conn, array $rotHoras): array
    {
        $horariosDescMap = [];
        if (empty($rotHoras)) {
            return $horariosDescMap;
        }

        $placeholdersHoras = [];
        $bindsHoras = [];
        $indexHora = 0;

        foreach ($rotHoras as $rotHora) {
            $paramHora = ':HorCodiIn' . $indexHora;
            $placeholdersHoras[] = $paramHora;
            $bindsHoras[$paramHora] = $rotHora;
            $indexHora++;
        }

        $sqlHoras = 'SELECT HorCodi, HorDesc, HorID, HorColor, HorDomi, HorLune, HorMart, HorMier, HorJuev, HorVier, HorSaba, HorFeri FROM HORARIOS WHERE HorCodi IN (' . implode(', ', $placeholdersHoras) . ')';
        $stmtHoras = $conn->prepare($sqlHoras);

        foreach ($bindsHoras as $param => $value) {
            $stmtHoras->bindValue($param, (int) $value, \PDO::PARAM_INT);
        }

        $stmtHoras->execute();
        $rowsHoras = $stmtHoras->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($rowsHoras as $rowHora) {
            $horCodi = isset($rowHora['HorCodi']) ? (int) $rowHora['HorCodi'] : null;
            if ($horCodi === null) {
                continue;
            }

            $horariosDescMap[$horCodi] = [
                'HorDesc' => $rowHora['HorDesc'] ?? null,
                'HorID' => $rowHora['HorID'] ?? null,
                'HorColor' => isset($rowHora['HorColor']) && $rowHora['HorColor'] !== null
                    ? $this->decimalToRgbHex((int) $rowHora['HorColor'])
                    : null,
                'HorDomi' => isset($rowHora['HorDomi']) ? (int) $rowHora['HorDomi'] : null,
                'HorLune' => isset($rowHora['HorLune']) ? (int) $rowHora['HorLune'] : null,
                'HorMart' => isset($rowHora['HorMart']) ? (int) $rowHora['HorMart'] : null,
                'HorMier' => isset($rowHora['HorMier']) ? (int) $rowHora['HorMier'] : null,
                'HorJuev' => isset($rowHora['HorJuev']) ? (int) $rowHora['HorJuev'] : null,
                'HorVier' => isset($rowHora['HorVier']) ? (int) $rowHora['HorVier'] : null,
                'HorSaba' => isset($rowHora['HorSaba']) ? (int) $rowHora['HorSaba'] : null,
                'HorFeri' => isset($rowHora['HorFeri']) ? (int) $rowHora['HorFeri'] : null,
            ];
        }

        return $horariosDescMap;
    }

    private function mergeHorarios(array &$data, array $horariosPorRotacion, array $horariosDescMap): void
    {
        foreach ($data as &$row) {
            $key = isset($row['RotCodi']) ? (int) $row['RotCodi'] : 0;

            if (!empty($horariosPorRotacion)) {
                $row['Horarios'] = $horariosPorRotacion[$key] ?? [];
            }

            if (!empty($horariosDescMap)) {
                if (!isset($row['Horarios']) || !is_array($row['Horarios'])) {
                    $row['Horarios'] = [];
                    continue;
                }

                foreach ($row['Horarios'] as &$horario) {
                    if (!is_array($horario)) {
                        continue;
                    }

                    $rotHora = $horario['RotHora'] ?? null;
                    $rotHoraKey = (is_numeric($rotHora)) ? (int) $rotHora : null;

                    if ($rotHoraKey !== null && isset($horariosDescMap[$rotHoraKey]) && is_array($horariosDescMap[$rotHoraKey])) {
                        $horario['RotHoraStr'] = $horariosDescMap[$rotHoraKey]['HorDesc'] ?? null;
                        $horario['RotHoraID'] = $horariosDescMap[$rotHoraKey]['HorID'] ?? null;
                        $horario['RotHoraColor'] = $horariosDescMap[$rotHoraKey]['HorColor'] ?? null;
                        $horario['HorDomi'] = $horariosDescMap[$rotHoraKey]['HorDomi'] ?? null;
                        $horario['HorLune'] = $horariosDescMap[$rotHoraKey]['HorLune'] ?? null;
                        $horario['HorMart'] = $horariosDescMap[$rotHoraKey]['HorMart'] ?? null;
                        $horario['HorMier'] = $horariosDescMap[$rotHoraKey]['HorMier'] ?? null;
                        $horario['HorJuev'] = $horariosDescMap[$rotHoraKey]['HorJuev'] ?? null;
                        $horario['HorVier'] = $horariosDescMap[$rotHoraKey]['HorVier'] ?? null;
                        $horario['HorSaba'] = $horariosDescMap[$rotHoraKey]['HorSaba'] ?? null;
                        $horario['HorFeri'] = $horariosDescMap[$rotHoraKey]['HorFeri'] ?? null;
                    } else {
                        $horario['RotHoraStr'] = null;
                        $horario['RotHoraID'] = null;
                        $horario['RotHoraColor'] = null;
                        $horario['HorDomi'] = null;
                        $horario['HorLune'] = null;
                        $horario['HorMart'] = null;
                        $horario['HorMier'] = null;
                        $horario['HorJuev'] = null;
                        $horario['HorVier'] = null;
                        $horario['HorSaba'] = null;
                        $horario['HorFeri'] = null;
                    }
                }
                unset($horario);
            }
        }
        unset($row);
    }

    private function decimalToRgbHex(int $decimal): string
    {
        $unsigned = ($decimal < 0) ? $decimal + 4294967296 : $decimal;
        $rgb24 = $unsigned & 0xFFFFFF;
        $r = ($rgb24 >> 16) & 0xFF;
        $g = ($rgb24 >> 8) & 0xFF;
        $b = $rgb24 & 0xFF;
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

    private function normalizeItems(array $payload): array
    {
        if (isset($payload['RotCodi'])) {
            return [$payload];
        }

        return $payload;
    }

    private function applyDefaults(array $item, string $fechaHora): array
    {
        if (!isset($item['FechaHora']) || $item['FechaHora'] === '') {
            $item['FechaHora'] = $fechaHora;
        }
        if (!isset($item['User']) || $item['User'] === '') {
            $item['User'] = '';
        }

        return $item;
    }

    private function validateRotacionItem(array $item): void
    {
        $rules = [
            'RotCodi' => ['required', 'smallint'],
            'RotDesc' => ['required', 'varchar40'],
            'User' => ['varchar100'],
        ];

        (new InputValidator($item, $rules))->validate();

        if (!isset($item['Horarios']) || !is_array($item['Horarios']) || empty($item['Horarios'])) {
            throw new ValidationException('El campo Horarios es requerido y debe ser un arreglo con al menos un elemento', 400);
        }
    }

    private function normalizeAndValidateDetalle(array $horarios, int $rotCodi, array &$errores): array
    {
        $detalleValido = [];
        $rotHoraSeen = [];
        $rotItemSeen = [];

        foreach ($horarios as $index => $horario) {
            if (!is_array($horario)) {
                $errores[] = [
                    'RotCodi' => $rotCodi,
                    'error' => 'Elemento de Horarios inválido en posición ' . $index,
                ];
                continue;
            }

            if (!isset($horario['RotDias']) || $horario['RotDias'] === '') {
                $horario['RotDias'] = '1';
            }

            try {
                $rules = [
                    'RotItem' => ['required', 'smallint'],
                    'RotHora' => ['required', 'smallint'],
                    'RotDias' => ['smallintEmpty'],
                ];
                (new InputValidator($horario, $rules))->validate();
            } catch (ValidationException $e) {
                $errores[] = [
                    'RotCodi' => $rotCodi,
                    'error' => "Detalle inválido en posición $index: " . $e->getMessage(),
                ];
                continue;
            }

            $rotHora = (int) $horario['RotHora'];
            $rotItem = (int) $horario['RotItem'];

            // if (isset($rotHoraSeen[$rotHora])) {
            //     $errores[] = [
            //         'RotCodi' => $rotCodi,
            //         'error' => 'RotHora repetido omitido: ' . $rotHora,
            //     ];
            //     continue;
            // }

            if (isset($rotItemSeen[$rotItem])) {
                $errores[] = [
                    'RotCodi' => $rotCodi,
                    'error' => "RotItem repetido omitido: $rotItem",
                ];
                continue;
            }

            if (!$this->existeHorario($rotHora)) {
                $errores[] = [
                    'RotCodi' => $rotCodi,
                    'error' => "No se asignó RotHora $rotHora porque no existe en HORARIOS",
                ];
                continue;
            }

            $rotHoraSeen[$rotHora] = true;
            $rotItemSeen[$rotItem] = true;

            $detalleValido[] = [
                'RotItem' => $rotItem,
                'RotHora' => $rotHora,
                'RotDias' => (int) $horario['RotDias'],
            ];
        }

        return $detalleValido;
    }
    private function existeRotacion(int $rotCodi, \PDO $conn): bool
    {
        $sql = 'SELECT COUNT(*) FROM ROTACION WHERE RotCodi = :RotCodi';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':RotCodi', $rotCodi, \PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    private function existeHorario(int $horCodi): bool
    {
        $conn = $this->conect->conn();
        $sql = 'SELECT COUNT(*) FROM HORARIOS WHERE HorCodi = :HorCodi';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':HorCodi', $horCodi, \PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    private function insertRotacion(array $data, \PDO $conn): void
    {
        $sql = 'INSERT INTO ROTACION (RotCodi, RotDesc, FechaHora) VALUES (:RotCodi, :RotDesc, :FechaHora)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':RotCodi', (int) $data['RotCodi'], \PDO::PARAM_INT);
        $stmt->bindValue(':RotDesc', $data['RotDesc'], \PDO::PARAM_STR);
        $stmt->bindValue(':FechaHora', $data['FechaHora'], \PDO::PARAM_STR);
        $stmt->execute();
    }
    private function updateRotacion(array $data, \PDO $conn): void
    {
        $sql = 'UPDATE ROTACION SET RotDesc = :RotDesc, FechaHora = :FechaHora WHERE RotCodi = :RotCodi';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':RotCodi', (int) $data['RotCodi'], \PDO::PARAM_INT);
        $stmt->bindValue(':RotDesc', $data['RotDesc'], \PDO::PARAM_STR);
        $stmt->bindValue(':FechaHora', $data['FechaHora'], \PDO::PARAM_STR);
        $stmt->execute();
    }

    private function insertDetalleRotacion(int $rotCodi, array $detalle, string $fechaHora, \PDO $conn): void
    {
        $sql = 'INSERT INTO ROTACIO1 (RotCodi, RotItem, RotHora, RotDias, FechaHora) VALUES (:RotCodi, :RotItem, :RotHora, :RotDias, :FechaHora)';
        $stmt = $conn->prepare($sql);

        foreach ($detalle as $row) {
            $stmt->bindValue(':RotCodi', $rotCodi, \PDO::PARAM_INT);
            $stmt->bindValue(':RotItem', (int) $row['RotItem'], \PDO::PARAM_INT);
            $stmt->bindValue(':RotHora', (int) $row['RotHora'], \PDO::PARAM_INT);
            $stmt->bindValue(':RotDias', (int) $row['RotDias'], \PDO::PARAM_INT);
            $stmt->bindValue(':FechaHora', $fechaHora, \PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    private function deleteDetalleRotacion(int $rotCodi, \PDO $conn): void
    {
        $sql = 'DELETE FROM ROTACIO1 WHERE RotCodi = :RotCodi';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':RotCodi', $rotCodi, \PDO::PARAM_INT);
        $stmt->execute();
    }
    private function deleteRotacion(int $rotCodi, \PDO $conn): void
    {
        $sql = 'DELETE FROM ROTACION WHERE RotCodi = :RotCodi';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':RotCodi', $rotCodi, \PDO::PARAM_INT);
        $stmt->execute();
    }

    private function checkConsistencia(int $rotCodi, \PDO $conn): ?string
    {
        $tablas = [
            'ROTALEG' => 'RoLRota',
            'ROTASEC' => 'RoSRota',
            'ROTAGRU' => 'RoGRota',
        ];

        foreach ($tablas as $tabla => $columna) {
            $sql = "SELECT COUNT(*) FROM {$tabla} WHERE {$columna} = :RotCodi";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':RotCodi', $rotCodi, \PDO::PARAM_INT);
            $stmt->execute();
            if ((int) $stmt->fetchColumn() > 0) {
                return $tabla;
            }
        }

        return null;
    }
}