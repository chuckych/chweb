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
                            'AudDato' => 'Rotación ' . $rotCodi,
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
                        'AudDato' => 'Rotación ' . $rotCodi,
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

    // ─────────────────────────────────────────────────────────────────
    // Métodos privados
    // ─────────────────────────────────────────────────────────────────

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
                    'error' => 'Detalle inválido en posición ' . $index . ': ' . $e->getMessage(),
                ];
                continue;
            }

            $rotHora = (int) $horario['RotHora'];
            $rotItem = (int) $horario['RotItem'];

            if (isset($rotHoraSeen[$rotHora])) {
                $errores[] = [
                    'RotCodi' => $rotCodi,
                    'error' => 'RotHora repetido omitido: ' . $rotHora,
                ];
                continue;
            }

            if (isset($rotItemSeen[$rotItem])) {
                $errores[] = [
                    'RotCodi' => $rotCodi,
                    'error' => 'RotItem repetido omitido: ' . $rotItem,
                ];
                continue;
            }

            if (!$this->existeHorario($rotHora)) {
                $errores[] = [
                    'RotCodi' => $rotCodi,
                    'error' => 'No se asignó RotHora ' . $rotHora . ' porque no existe en HORARIOS',
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