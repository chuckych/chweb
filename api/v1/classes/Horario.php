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

class Horario
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
        $this->NameLog = date('Ymd') . '_horario.log';
    }

    // ─────────────────────────────────────────────────────────────────
    // Método público
    // ─────────────────────────────────────────────────────────────────

    public function create(): void
    {
        $this->inicio = microtime(true);

        $items = $this->getData;

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
                $audUser = isset($item['User']) && $item['User'] !== '' ? $item['User'] : '';
                $fechaHora = (new ConnectSqlSrv())->FechaHora();
                $item = $this->applyDefaults($item, $fechaHora);

                // Validación manual: HorID debe ser alfanumérico
                if (empty($item['HorID']) || !preg_match('/^[a-zA-Z0-9]+$/', $item['HorID'])) {
                    $errores[] = [
                        'HorCodi' => $item['HorCodi'] ?? null,
                        'error' => 'El campo HorID debe ser alfanumérico y requerido',
                    ];
                    continue;
                }

                // Convertir HorColor si viene como hex (#RRGGBB) desde el POST
                // applyDefaults lo deja como string numérico '-16777216' si no vino en el POST
                if (isset($item['_horColorFromPost']) && $item['_horColorFromPost']) {
                    $item['HorColor'] = (string) $this->rgbHexToDecimal($item['HorColor']);
                    unset($item['_horColorFromPost']);
                }

                $rules = [
                    'HorCodi' => ['required', 'smallint'],
                    'HorDesc' => ['required', 'varchar40'],
                    'HorID' => ['required', 'varchar10'],
                    // Días
                    'HorDomi' => ['allowed01'], 'HorLune' => ['allowed01'], 'HorMart' => ['allowed01'],
                    'HorMier' => ['allowed01'], 'HorJuev' => ['allowed01'], 'HorVier' => ['allowed01'],
                    'HorSaba' => ['allowed01'], 'HorFeri' => ['allowed0a3'],
                    // Horas De
                    'HorDoDe' => ['time'], 'HorLuDe' => ['time'], 'HorMaDe' => ['time'],
                    'HorMiDe' => ['time'], 'HorJuDe' => ['time'], 'HorViDe' => ['time'],
                    'HorSaDe' => ['time'], 'HorFeDe' => ['time'],
                    // Horas Ha
                    'HorDoHa' => ['time'], 'HorLuHa' => ['time'], 'HorMaHa' => ['time'],
                    'HorMiHa' => ['time'], 'HorJuHa' => ['time'], 'HorViHa' => ['time'],
                    'HorSaHa' => ['time'], 'HorFeHa' => ['time'],
                    // Horas Re
                    'HorDoRe' => ['time'], 'HorLuRe' => ['time'], 'HorMaRe' => ['time'],
                    'HorMiRe' => ['time'], 'HorJuRe' => ['time'], 'HorViRe' => ['time'],
                    'HorSaRe' => ['time'], 'HorFeRe' => ['time'],
                    // Horas HsDia
                    'HorDoHs' => ['time'], 'HorLuHs' => ['time'], 'HorMaHs' => ['time'],
                    'HorMiHs' => ['time'], 'HorJuHs' => ['time'], 'HorViHs' => ['time'],
                    'HorSaHs' => ['time'], 'HorFeHs' => ['time'],
                    // Horas LiDia 
                    'HorDoLi' => ['allowed0a100'], 'HorLuLi' => ['allowed0a100'], 'HorMaLi' => ['allowed0a100'],
                    'HorMiLi' => ['allowed0a100'], 'HorJuLi' => ['allowed0a100'], 'HorViLi' => ['allowed0a100'],
                    'HorSaLi' => ['allowed0a100'], 'HorFeLi' => ['allowed0a100'],
                    'User' => ['varchar100'],
                ];

                try {
                    (new InputValidator($item, $rules))->validate();
                } catch (ValidationException $e) {
                    $errores[] = [
                        'HorCodi' => $item['HorCodi'],
                        'error' => $e->getMessage(),
                    ];
                    continue;
                }

                if ($this->existeHorario((int) $item['HorCodi'])) {
                    unset($item['User']);
                    $this->updateHorario($item, $conn);
                    $this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'M', 'AudDato' => 'Horario ' . $item['HorCodi']]]);
                    $actualizados[] = [
                        'HorCodi' => $item['HorCodi'],
                        'HorDesc' => $item['HorDesc'],
                    ];
                    continue;
                }

                unset($item['User']);
                $this->insertHorario($item, $conn);
                $this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'A', 'AudDato' => 'Horario ' . $item['HorCodi']]]);

                $insertados[] = [
                    'HorCodi' => $item['HorCodi'],
                    'HorDesc' => $item['HorDesc'],
                ];
            }

            if (empty($insertados) && empty($actualizados)) {
                $conn->rollBack();
                $this->resp->respuesta(
                    ['errores' => $errores],
                    0,
                    'No se procesó ningún registro',
                    400,
                    $this->inicio,
                    count($items),
                    defined('ID_COMPANY') ? ID_COMPANY : 0
                );
                return;
            }

            $conn->commit();
            $this->resp->respuesta(
                ['insertados' => $insertados, 'actualizados' => $actualizados, 'errores' => $errores],
                count($insertados) + count($actualizados),
                'OK',
                200,
                $this->inicio,
                count($items),
                defined('ID_COMPANY') ? ID_COMPANY : 0
            );
        } catch (\PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al crear horario', 400);
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function delete(): void
    {
        $this->inicio = microtime(true);
        $idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

        $items = $this->getData;

        if (empty($items)) {
            $this->resp->respuesta([], 0, 'No se recibieron datos', 400, $this->inicio, 0, $idCompany);
            return;
        }

        $eliminados = [];
        $errores = [];
        $conn = $this->conect->conn();

        $rules = [
            'HorCodi' => ['required', 'smallint'],
            'User' => ['varchar100'],
        ];

        try {
            $conn->beginTransaction();

            foreach ($items as $item) {

                // Validar HorCodi
                try {
                    (new InputValidator($item, $rules))->validate();
                } catch (ValidationException $e) {
                    $errores[] = [
                        'HorCodi' => $item['HorCodi'] ?? null,
                        'error' => $e->getMessage(),
                    ];
                    continue;
                }

                $audUser = isset($item['User']) && $item['User'] !== '' ? $item['User'] : '';
                $horCodi = (int) $item['HorCodi'];

                // Verificar que el registro exista antes de intentar eliminar
                if (!$this->existeHorario($horCodi)) {
                    $errores[] = [
                        'HorCodi' => $horCodi,
                        'error' => "El horario {$horCodi} no existe",
                    ];
                    continue;
                }

                // Verificar consistencia referencial en las tablas relacionadas
                $tablaConflicto = $this->checkConsistencia($horCodi, $conn);
                if ($tablaConflicto !== null) {
                    $errores[] = [
                        'HorCodi' => $horCodi,
                        'error' => "No se puede eliminar el horario {$horCodi}: existen asignaciones relacionadas ({$tablaConflicto})",
                    ];
                    continue;
                }

                // Eliminar
                $this->deleteHorario($horCodi, $conn);
                $this->auditor->add([['AudUser' => $audUser, 'AudTipo' => 'B', 'AudDato' => 'Horario ' . $horCodi]]);

                $eliminados[] = ['HorCodi' => $horCodi];
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
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al eliminar horario', 400);
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function get(): void
    {
        $this->inicio = microtime(true);
        $idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

        try {
            $conn = $this->conect->conn();
            $sql = "SELECT * FROM HORARIOS WHERE HorCodi > 0 ORDER BY HorCodi";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($data as &$row) {
                $row['HorColor'] = $this->decimalToRgbHex((int) $row['HorColor']);
            }
            unset($row);

            $this->resp->respuesta(
                $data,
                count($data),
                'OK',
                200,
                $this->inicio,
                count($data),
                $idCompany
            );
       } catch (\PDOException $e) {
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al obtener horarios', 400);
        } catch (\Exception $e) {
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function getUnused(): void
    {
        $this->inicio = microtime(true);
        $idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

        try {
            $conn = $this->conect->conn();

            $sql = "SELECT HorCodi, HorDesc, HorID, HorColor FROM HORARIOS h
                    WHERE h.HorCodi > 0
                      AND NOT EXISTS (SELECT 1 FROM ROTACIO1 WHERE RotHora  = h.HorCodi)
                      AND NOT EXISTS (SELECT 1 FROM PERHOALT WHERE LeHAHora = h.HorCodi)
                      AND NOT EXISTS (SELECT 1 FROM HORALE1  WHERE Ho1Hora  = h.HorCodi)
                      AND NOT EXISTS (SELECT 1 FROM HORALE2  WHERE Ho2Hora  = h.HorCodi)
                      AND NOT EXISTS (SELECT 1 FROM HORAGR1  WHERE Ho3Hora  = h.HorCodi)
                      AND NOT EXISTS (SELECT 1 FROM HORAGR2  WHERE Ho4Hora  = h.HorCodi)
                      AND NOT EXISTS (SELECT 1 FROM HORASE1  WHERE Ho5Hora  = h.HorCodi)
                      AND NOT EXISTS (SELECT 1 FROM HORASE2  WHERE Ho6Hora  = h.HorCodi)
                    ORDER BY h.HorCodi";

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($data as &$row) {
                $row['HorColor'] = $this->decimalToRgbHex((int) $row['HorColor']);
            }
            unset($row);

            $this->resp->respuesta(
                $data,
                count($data),
                'OK',
                200,
                $this->inicio,
                count($data),
                $idCompany
            );
        } catch (\PDOException $e) {
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al obtener horarios unused', 400);
        } catch (\Exception $e) {
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
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
            $sqlSelect = "SELECT HorCodi, HorDesc FROM HORARIOS h
                          WHERE h.HorCodi > 0
                            AND NOT EXISTS (SELECT 1 FROM ROTACIO1 WHERE RotHora  = h.HorCodi)
                            AND NOT EXISTS (SELECT 1 FROM PERHOALT WHERE LeHAHora = h.HorCodi)
                            AND NOT EXISTS (SELECT 1 FROM HORALE1  WHERE Ho1Hora  = h.HorCodi)
                            AND NOT EXISTS (SELECT 1 FROM HORALE2  WHERE Ho2Hora  = h.HorCodi)
                            AND NOT EXISTS (SELECT 1 FROM HORAGR1  WHERE Ho3Hora  = h.HorCodi)
                            AND NOT EXISTS (SELECT 1 FROM HORAGR2  WHERE Ho4Hora  = h.HorCodi)
                            AND NOT EXISTS (SELECT 1 FROM HORASE1  WHERE Ho5Hora  = h.HorCodi)
                            AND NOT EXISTS (SELECT 1 FROM HORASE2  WHERE Ho6Hora  = h.HorCodi)";

            $stmt = $conn->prepare($sqlSelect);
            $stmt->execute();
            $unused = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?? [];

            if (empty($unused)) {
                $this->resp->respuesta(
                    ['eliminados' => []],
                    0,
                    'No hay horarios sin uso para eliminar',
                    200,
                    $this->inicio,
                    0,
                    $idCompany
                );
                return;
            }

            $conn->beginTransaction();

            $sqlDelete = "DELETE FROM HORARIOS WHERE HorCodi = :HorCodi";
            $stmtDel = $conn->prepare($sqlDelete);

            $eliminados = [];
            $auditItems = [];

            foreach ($unused as $row) {
                $horCodi = (int) $row['HorCodi'];
                $stmtDel->bindValue(':HorCodi', $horCodi, \PDO::PARAM_INT);
                $stmtDel->execute();
                $eliminados[] = ['HorCodi' => $horCodi, 'HorDesc' => $row['HorDesc']];
                $auditItems[] = ['AudUser' => $audUser, 'AudTipo' => 'B', 'AudDato' => 'Horario ' . $horCodi];
            }

            $this->updateFechaHoraFranco($conn);
            $conn->commit();

            $this->auditor->add($auditItems);

            $this->resp->respuesta(
                ['eliminados' => $eliminados],
                count($eliminados),
                'OK',
                200,
                $this->inicio,
                count($eliminados),
                $idCompany
            );
        } catch (\PDOException $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al eliminar horarios sin asignar', 400);
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            $this->log->trace('Horario::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // Métodos privados
    // ─────────────────────────────────────────────────────────────────

    private function applyDefaults(array $item, string $fechaHora): array
    {
        // Detectar si HorColor viene desde el POST con formato hex antes de aplicar defaults
        if (isset($item['HorColor']) && $item['HorColor'] !== '' && preg_match('/^#([0-9a-fA-F]{6})$/', $item['HorColor'])) {
            $item['_horColorFromPost'] = true;
        } else {
            $item['_horColorFromPost'] = false;
            // Si no viene como hex, forzar el default numérico (negro)
            if (!isset($item['HorColor']) || $item['HorColor'] === '') {
                $item['HorColor'] = '-16777216';
            }
        }

        $defaults = [
            'FechaHora' => $fechaHora,
            // Días
            'HorDomi' => '0', 'HorLune' => '0', 'HorMart' => '0', 'HorMier' => '0',
            'HorJuev' => '0', 'HorVier' => '0', 'HorSaba' => '0', 'HorFeri' => '0',
            // Horas De
            'HorDoDe' => '00:00', 'HorLuDe' => '00:00', 'HorMaDe' => '00:00', 'HorMiDe' => '00:00',
            'HorJuDe' => '00:00', 'HorViDe' => '00:00', 'HorSaDe' => '00:00', 'HorFeDe' => '00:00',
            // Horas Ha
            'HorDoHa' => '00:00', 'HorLuHa' => '00:00', 'HorMaHa' => '00:00', 'HorMiHa' => '00:00',
            'HorJuHa' => '00:00', 'HorViHa' => '00:00', 'HorSaHa' => '00:00', 'HorFeHa' => '00:00',
            // Horas Re
            'HorDoRe' => '00:00', 'HorLuRe' => '00:00', 'HorMaRe' => '00:00', 'HorMiRe' => '00:00',
            'HorJuRe' => '00:00', 'HorViRe' => '00:00', 'HorSaRe' => '00:00', 'HorFeRe' => '00:00',
            // Horas Hs
            'HorDoHs' => '00:00', 'HorLuHs' => '00:00', 'HorMaHs' => '00:00', 'HorMiHs' => '00:00',
            'HorJuHs' => '00:00', 'HorViHs' => '00:00', 'HorSaHs' => '00:00', 'HorFeHs' => '00:00',
            // Límites
            'HorDoLi' => '0', 'HorLuLi' => '0', 'HorMaLi' => '0', 'HorMiLi' => '0',
            'HorJuLi' => '0', 'HorViLi' => '0', 'HorSaLi' => '0', 'HorFeLi' => '0',
        ];

        foreach ($defaults as $key => $val) {
            if (!isset($item[$key]) || $item[$key] === '') {
                $item[$key] = $val;
            }
        }

        return $item;
    }

    private function existeHorario(int $horCodi): bool
    {
        $conn = $this->conect->conn();
        $sql = "SELECT COUNT(*) FROM HORARIOS WHERE HorCodi = :HorCodi";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':HorCodi', $horCodi, \PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    private function insertHorario(array $data, \PDO $conn): void
    {
        // try {
        $sql = "INSERT INTO HORARIOS (
            HorCodi, HorDesc, HorID, HorColor,
            HorDomi, HorLune, HorMart, HorMier, HorJuev, HorVier, HorSaba, HorFeri,
            HorDoDe, HorLuDe, HorMaDe, HorMiDe, HorJuDe, HorViDe, HorSaDe, HorFeDe,
            HorDoHa, HorLuHa, HorMaHa, HorMiHa, HorJuHa, HorViHa, HorSaHa, HorFeHa,
            HorDoRe, HorLuRe, HorMaRe, HorMiRe, HorJuRe, HorViRe, HorSaRe, HorFeRe,
            HorDoLi, HorLuLi, HorMaLi, HorMiLi, HorJuLi, HorViLi, HorSaLi, HorFeLi,
            HorDoHs, HorLuHs, HorMaHs, HorMiHs, HorJuHs, HorViHs, HorSaHs, HorFeHs,
            FechaHora
        ) VALUES (
            :HorCodi, :HorDesc, :HorID, :HorColor,
            :HorDomi, :HorLune, :HorMart, :HorMier, :HorJuev, :HorVier, :HorSaba, :HorFeri,
            :HorDoDe, :HorLuDe, :HorMaDe, :HorMiDe, :HorJuDe, :HorViDe, :HorSaDe, :HorFeDe,
            :HorDoHa, :HorLuHa, :HorMaHa, :HorMiHa, :HorJuHa, :HorViHa, :HorSaHa, :HorFeHa,
            :HorDoRe, :HorLuRe, :HorMaRe, :HorMiRe, :HorJuRe, :HorViRe, :HorSaRe, :HorFeRe,
            :HorDoLi, :HorLuLi, :HorMaLi, :HorMiLi, :HorJuLi, :HorViLi, :HorSaLi, :HorFeLi,
            :HorDoHs, :HorLuHs, :HorMaHs, :HorMiHs, :HorJuHs, :HorViHs, :HorSaHs, :HorFeHs,
            :FechaHora
        )";

        $stmt = $conn->prepare($sql);

        // SMALLINT → PARAM_INT
        $stmt->bindValue(':HorCodi', (int) $data['HorCodi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorDomi', (int) $data['HorDomi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorLune', (int) $data['HorLune'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMart', (int) $data['HorMart'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMier', (int) $data['HorMier'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorJuev', (int) $data['HorJuev'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorVier', (int) $data['HorVier'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorSaba', (int) $data['HorSaba'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorFeri', (int) $data['HorFeri'], \PDO::PARAM_INT);

        // Límites allowed0a100 → PARAM_INT
        $stmt->bindValue(':HorDoLi', (int) $data['HorDoLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorLuLi', (int) $data['HorLuLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMaLi', (int) $data['HorMaLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMiLi', (int) $data['HorMiLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorJuLi', (int) $data['HorJuLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorViLi', (int) $data['HorViLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorSaLi', (int) $data['HorSaLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorFeLi', (int) $data['HorFeLi'], \PDO::PARAM_INT);

        // HorColor decimal negativo → PARAM_INT
        $stmt->bindValue(':HorColor', (int) $data['HorColor'], \PDO::PARAM_INT);

        // VARCHAR → PARAM_STR
        $stmt->bindValue(':HorDesc', $data['HorDesc'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorID', $data['HorID'], \PDO::PARAM_STR);

        // Horas De (HH:MM) → PARAM_STR
        $stmt->bindValue(':HorDoDe', $data['HorDoDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuDe', $data['HorLuDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaDe', $data['HorMaDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiDe', $data['HorMiDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuDe', $data['HorJuDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViDe', $data['HorViDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaDe', $data['HorSaDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeDe', $data['HorFeDe'], \PDO::PARAM_STR);

        // Horas Ha (HH:MM) → PARAM_STR
        $stmt->bindValue(':HorDoHa', $data['HorDoHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuHa', $data['HorLuHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaHa', $data['HorMaHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiHa', $data['HorMiHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuHa', $data['HorJuHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViHa', $data['HorViHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaHa', $data['HorSaHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeHa', $data['HorFeHa'], \PDO::PARAM_STR);

        // Horas Re (HH:MM) → PARAM_STR
        $stmt->bindValue(':HorDoRe', $data['HorDoRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuRe', $data['HorLuRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaRe', $data['HorMaRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiRe', $data['HorMiRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuRe', $data['HorJuRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViRe', $data['HorViRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaRe', $data['HorSaRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeRe', $data['HorFeRe'], \PDO::PARAM_STR);

        // Horas Hs (HH:MM) → PARAM_STR
        $stmt->bindValue(':HorDoHs', $data['HorDoHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuHs', $data['HorLuHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaHs', $data['HorMaHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiHs', $data['HorMiHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuHs', $data['HorJuHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViHs', $data['HorViHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaHs', $data['HorSaHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeHs', $data['HorFeHs'], \PDO::PARAM_STR);

        // FechaHora → PARAM_STR
        $stmt->bindValue(':FechaHora', $data['FechaHora'], \PDO::PARAM_STR);

        $stmt->execute();
    }

    private function updateHorario(array $data, \PDO $conn): void
    {

        $sql = "UPDATE HORARIOS SET
            HorDesc    = :HorDesc,
            HorID      = :HorID,
            HorColor   = :HorColor,
            HorDomi    = :HorDomi,
            HorLune    = :HorLune,
            HorMart    = :HorMart,
            HorMier    = :HorMier,
            HorJuev    = :HorJuev,
            HorVier    = :HorVier,
            HorSaba    = :HorSaba,
            HorFeri    = :HorFeri,
            HorDoDe    = :HorDoDe,
            HorLuDe    = :HorLuDe,
            HorMaDe    = :HorMaDe,
            HorMiDe    = :HorMiDe,
            HorJuDe    = :HorJuDe,
            HorViDe    = :HorViDe,
            HorSaDe    = :HorSaDe,
            HorFeDe    = :HorFeDe,
            HorDoHa    = :HorDoHa,
            HorLuHa    = :HorLuHa,
            HorMaHa    = :HorMaHa,
            HorMiHa    = :HorMiHa,
            HorJuHa    = :HorJuHa,
            HorViHa    = :HorViHa,
            HorSaHa    = :HorSaHa,
            HorFeHa    = :HorFeHa,
            HorDoRe    = :HorDoRe,
            HorLuRe    = :HorLuRe,
            HorMaRe    = :HorMaRe,
            HorMiRe    = :HorMiRe,
            HorJuRe    = :HorJuRe,
            HorViRe    = :HorViRe,
            HorSaRe    = :HorSaRe,
            HorFeRe    = :HorFeRe,
            HorDoLi    = :HorDoLi,
            HorLuLi    = :HorLuLi,
            HorMaLi    = :HorMaLi,
            HorMiLi    = :HorMiLi,
            HorJuLi    = :HorJuLi,
            HorViLi    = :HorViLi,
            HorSaLi    = :HorSaLi,
            HorFeLi    = :HorFeLi,
            HorDoHs    = :HorDoHs,
            HorLuHs    = :HorLuHs,
            HorMaHs    = :HorMaHs,
            HorMiHs    = :HorMiHs,
            HorJuHs    = :HorJuHs,
            HorViHs    = :HorViHs,
            HorSaHs    = :HorSaHs,
            HorFeHs    = :HorFeHs,
            FechaHora  = :FechaHora
        WHERE HorCodi = :HorCodi";

        $stmt = $conn->prepare($sql);

        // PK
        $stmt->bindValue(':HorCodi', (int) $data['HorCodi'], \PDO::PARAM_INT);

        // SMALLINT días → PARAM_INT
        $stmt->bindValue(':HorDomi', (int) $data['HorDomi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorLune', (int) $data['HorLune'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMart', (int) $data['HorMart'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMier', (int) $data['HorMier'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorJuev', (int) $data['HorJuev'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorVier', (int) $data['HorVier'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorSaba', (int) $data['HorSaba'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorFeri', (int) $data['HorFeri'], \PDO::PARAM_INT);

        // Límites → PARAM_INT
        $stmt->bindValue(':HorDoLi', (int) $data['HorDoLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorLuLi', (int) $data['HorLuLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMaLi', (int) $data['HorMaLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorMiLi', (int) $data['HorMiLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorJuLi', (int) $data['HorJuLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorViLi', (int) $data['HorViLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorSaLi', (int) $data['HorSaLi'], \PDO::PARAM_INT);
        $stmt->bindValue(':HorFeLi', (int) $data['HorFeLi'], \PDO::PARAM_INT);

        // HorColor decimal negativo → PARAM_INT
        $stmt->bindValue(':HorColor', (int) $data['HorColor'], \PDO::PARAM_INT);

        // VARCHAR → PARAM_STR
        $stmt->bindValue(':HorDesc', $data['HorDesc'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorID', $data['HorID'], \PDO::PARAM_STR);

        // Horas De → PARAM_STR
        $stmt->bindValue(':HorDoDe', $data['HorDoDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuDe', $data['HorLuDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaDe', $data['HorMaDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiDe', $data['HorMiDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuDe', $data['HorJuDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViDe', $data['HorViDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaDe', $data['HorSaDe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeDe', $data['HorFeDe'], \PDO::PARAM_STR);

        // Horas Ha → PARAM_STR
        $stmt->bindValue(':HorDoHa', $data['HorDoHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuHa', $data['HorLuHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaHa', $data['HorMaHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiHa', $data['HorMiHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuHa', $data['HorJuHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViHa', $data['HorViHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaHa', $data['HorSaHa'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeHa', $data['HorFeHa'], \PDO::PARAM_STR);

        // Horas Re → PARAM_STR
        $stmt->bindValue(':HorDoRe', $data['HorDoRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuRe', $data['HorLuRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaRe', $data['HorMaRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiRe', $data['HorMiRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuRe', $data['HorJuRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViRe', $data['HorViRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaRe', $data['HorSaRe'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeRe', $data['HorFeRe'], \PDO::PARAM_STR);

        // Horas Hs → PARAM_STR
        $stmt->bindValue(':HorDoHs', $data['HorDoHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorLuHs', $data['HorLuHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMaHs', $data['HorMaHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorMiHs', $data['HorMiHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorJuHs', $data['HorJuHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorViHs', $data['HorViHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorSaHs', $data['HorSaHs'], \PDO::PARAM_STR);
        $stmt->bindValue(':HorFeHs', $data['HorFeHs'], \PDO::PARAM_STR);

        // FechaHora → PARAM_STR
        $stmt->bindValue(':FechaHora', $data['FechaHora'], \PDO::PARAM_STR);
        $stmt->execute();
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

    private function rgbHexToDecimal(string $hex): int
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $rgb24 = ($r << 16) | ($g << 8) | $b;
        $unsigned = 0xFF000000 | $rgb24;

        // En PHP 64bit, 0xFF000000 es positivo; restar 2^32 para obtener el complemento negativo
        return $unsigned - 4294967296;
    }

    /**
     * Verifica integridad referencial en las 8 tablas relacionadas.
     * Retorna el nombre de la primera tabla con registros vinculados,
     * o null si el horario puede eliminarse con seguridad.
     */
    private function checkConsistencia(int $horCodi, \PDO $conn): ?string
    {
        $tablas = [
            'ROTACIO1' => 'RotHora',
            'PERHOALT' => 'LeHAHora',
            'HORALE1' => 'Ho1Hora',
            'HORALE2' => 'Ho2Hora',
            'HORAGR1' => 'Ho3Hora',
            'HORAGR2' => 'Ho4Hora',
            'HORASE1' => 'Ho5Hora',
            'HORASE2' => 'Ho6Hora',
        ];

        foreach ($tablas as $tabla => $columna) {
            $sql = "SELECT COUNT(*) FROM {$tabla} WHERE {$columna} = :HorCodi";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':HorCodi', $horCodi, \PDO::PARAM_INT);
            $stmt->execute();
            if ((int) $stmt->fetchColumn() > 0) {
                return $tabla;
            }
        }

        return null;
    }

    /**
     * Actualiza el campo FechaHora del horario de franco (HorCodi = 0).
     * @param \PDO $conn
     * @return void
     */
    private function updateFechaHoraFranco(\PDO $conn): void
    {
        $sql = "UPDATE HORARIOS SET FechaHora = :FechaHora WHERE HorCodi = 0";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':FechaHora', (new ConnectSqlSrv())->FechaHora(), \PDO::PARAM_STR);
        $stmt->execute();
    }

    private function deleteHorario(int $horCodi, \PDO $conn): void
    {
        $sql = "DELETE FROM HORARIOS WHERE HorCodi = :HorCodi";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':HorCodi', $horCodi, \PDO::PARAM_INT);
        $stmt->execute();
        $this->updateFechaHoraFranco($conn);
    }
}
