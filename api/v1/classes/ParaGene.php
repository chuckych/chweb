<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\Tools;
use Classes\InputValidator;
use Classes\ValidationException;
use Flight;
use flight\net\Request;


class ParaGene
{
    private Response $resp;
    private Request $request;
    private array $getData;
    private array $query;
    private Log $log;
    private ConnectSqlSrv $conect;
    private Tools $tools;
    private string $NameLog;


    public function __construct()
    {
        $this->resp = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->query = $this->request->query->getData();
        $this->log = new Log;
        $this->conect = new ConnectSqlSrv;
        $this->tools = new Tools;
        $this->NameLog = date('Ymd') . '_paragene.log';
    }

    /**
     * Recupera datos de la tabla PARAGENE.
     */
    public function get()
    {
        try {
            $inicio = microtime(true);
            $sql = "SELECT * FROM PARAGENE";
            $Data = $this->conect->executeQueryWhithParams($sql);
            foreach ($Data as &$element) {
                $rs = [
                    'Etiquetas' => [
                        'EmprSin' => $element['ParEmprSin'],
                        'EmprPlu' => $element['ParEmprPlu'],
                        'PlanSin' => $element['ParPlanSin'],
                        'PlanPlu' => $element['ParPlanPlu'],
                        'SucuSin' => $element['ParSucuSin'],
                        'SucuPlu' => $element['ParSucuPlu'],
                        'GrupSin' => $element['ParGrupSin'],
                        'GrupPlu' => $element['ParGrupPlu'],
                        'SectSin' => $element['ParSectSin'],
                        'SectPlu' => $element['ParSectPlu'],
                        'SeccSin' => $element['ParSeccSin'],
                        'SeccPlu' => $element['ParSeccPlu'],
                    ],
                    'ParDato' => [
                        'LegDocu' => $element['ParDatoLegDocu'],
                        'LegCUIL' => $element['ParDatoLegCUIL'],
                        'LegEmpr' => $element['ParDatoLegEmpr'],
                        'LegPlan' => $element['ParDatoLegPlan'],
                        'LegSucu' => $element['ParDatoLegSucu'],
                        'LegGrup' => $element['ParDatoLegGrup'],
                        'LegSect' => $element['ParDatoLegSect'],
                        'LegSecc' => $element['ParDatoLegSecc'],
                        'LegTare' => $element['ParDatoLegTare'],
                        'LegFeIn' => $element['ParDatoLegFeIn'],
                        'LegReCH' => $element['ParDatoLegReCH'],
                    ],
                    'FechaHora' => $element['FechaHora'],
                ];
            }
            $this->resp->respuesta($rs ?? [], 0, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $this->log->trace('ParaGene::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al obtener parametros generales', 400);
        } catch (\Exception $e) {
            $this->log->trace('ParaGene::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    /**
     * Actualiza etiquetas de la tabla PARAGENE para ParCodi = 0.
     */
    public function update()
    {
        $inicio = microtime(true);
        $idCompany = defined('ID_COMPANY') ? ID_COMPANY : 0;

        $etiquetas = $this->getData['Etiquetas'] ?? $this->getData;

        if (!is_array($etiquetas) || empty($etiquetas)) {
            $this->resp->respuesta([], 0, 'No se recibieron datos de Etiquetas', 400, $inicio, 0, $idCompany);
            return;
        }

        $rules = [
            'EmprSin' => ['required', 'varchar10'],
            'EmprPlu' => ['required', 'varchar10'],
            'PlanSin' => ['required', 'varchar10'],
            'PlanPlu' => ['required', 'varchar10'],
            'SucuSin' => ['required', 'varchar10'],
            'SucuPlu' => ['required', 'varchar10'],
            'GrupSin' => ['required', 'varchar10'],
            'GrupPlu' => ['required', 'varchar10'],
            'SectSin' => ['required', 'varchar10'],
            'SectPlu' => ['required', 'varchar10'],
            'SeccSin' => ['required', 'varchar10'],
            'SeccPlu' => ['required', 'varchar10'],
        ];

        try {
            (new InputValidator($etiquetas, $rules))->validate();
        } catch (ValidationException $e) {
            $this->resp->respuesta([], 0, $e->getMessage(), 400, $inicio, 0, $idCompany);
            return;
        }

        try {
            $conn = $this->conect->conn();

            $sqlExists = "SELECT COUNT(*) AS total FROM PARAGENE WHERE ParCodi = 0";
            $stmtExists = $conn->prepare($sqlExists);
            $stmtExists->execute();
            $exists = (int) ($stmtExists->fetchColumn() ?? 0);

            if ($exists === 0) {
                throw new \Exception('No existe el registro ParCodi = 0 en PARAGENE', 404);
            }

            $sql = "UPDATE PARAGENE SET
                        ParEmprSin = :ParEmprSin,
                        ParEmprPlu = :ParEmprPlu,
                        ParPlanSin = :ParPlanSin,
                        ParPlanPlu = :ParPlanPlu,
                        ParSucuSin = :ParSucuSin,
                        ParSucuPlu = :ParSucuPlu,
                        ParGrupSin = :ParGrupSin,
                        ParGrupPlu = :ParGrupPlu,
                        ParSectSin = :ParSectSin,
                        ParSectPlu = :ParSectPlu,
                        ParSeccSin = :ParSeccSin,
                        ParSeccPlu = :ParSeccPlu
                    WHERE ParCodi = 0";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':ParEmprSin', (string) $etiquetas['EmprSin'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParEmprPlu', (string) $etiquetas['EmprPlu'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParPlanSin', (string) $etiquetas['PlanSin'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParPlanPlu', (string) $etiquetas['PlanPlu'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParSucuSin', (string) $etiquetas['SucuSin'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParSucuPlu', (string) $etiquetas['SucuPlu'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParGrupSin', (string) $etiquetas['GrupSin'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParGrupPlu', (string) $etiquetas['GrupPlu'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParSectSin', (string) $etiquetas['SectSin'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParSectPlu', (string) $etiquetas['SectPlu'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParSeccSin', (string) $etiquetas['SeccSin'], \PDO::PARAM_STR);
            $stmt->bindValue(':ParSeccPlu', (string) $etiquetas['SeccPlu'], \PDO::PARAM_STR);
            $stmt->execute();

            $rs = [
                'Etiquetas' => [
                    'EmprSin' => (string) $etiquetas['EmprSin'],
                    'EmprPlu' => (string) $etiquetas['EmprPlu'],
                    'PlanSin' => (string) $etiquetas['PlanSin'],
                    'PlanPlu' => (string) $etiquetas['PlanPlu'],
                    'SucuSin' => (string) $etiquetas['SucuSin'],
                    'SucuPlu' => (string) $etiquetas['SucuPlu'],
                    'GrupSin' => (string) $etiquetas['GrupSin'],
                    'GrupPlu' => (string) $etiquetas['GrupPlu'],
                    'SectSin' => (string) $etiquetas['SectSin'],
                    'SectPlu' => (string) $etiquetas['SectPlu'],
                    'SeccSin' => (string) $etiquetas['SeccSin'],
                    'SeccPlu' => (string) $etiquetas['SeccPlu'],
                ],
            ];

            $this->resp->respuesta($rs, 1, 'OK', 200, $inicio, 1, $idCompany);
        } catch (\PDOException $e) {
            $this->log->trace('ParaGene::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al actualizar parametros generales', 400);
        } catch (\Exception $e) {
            $this->log->trace('ParaGene::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }

    public function liquid()
    {
        $inicio = microtime(true);
        $cols = ['ParPeMeD', 'ParPeMeH', 'ParPeJ1D', 'ParPeJ1H', 'ParPeJ2D', 'ParPeJ2H', 'FechaHora'];
        $sql = "SELECT " . implode(", ", $cols) . " FROM PARACONT WHERE ParCodi=0";
        $Data = $this->conect->executeQueryWhithParams($sql);
        foreach ($Data as &$element) {
            $rs = [
                'MensDesde' => intval($element['ParPeMeD']),
                'MensHasta' => intval($element['ParPeMeH']),
                'Jor1Desde' => intval($element['ParPeJ1D']),
                'Jor1Hasta' => intval($element['ParPeJ1H']),
                'Jor2Desde' => intval($element['ParPeJ2D']),
                'Jor2Hasta' => intval($element['ParPeJ2H']),
                // 'FechaHora' => $element['FechaHora'],
            ];
        }
        $this->resp->respuesta($rs ?? [], 0, 'OK', 200, $inicio, 0, 0);
    }
    /**
     * Devuelve una matriz de datos de la tabla PARAGENE.
     *
     * @return array La matriz de datos de la tabla PARAGENE.
     */
    public function return()
    {
        $sql = "SELECT * FROM PARAGENE";
        $Data = $this->conect->executeQueryWhithParams($sql);
        $rs = [];
        foreach ($Data as &$element) {
            $rs = [
                'Etiquetas' => [
                    'EmprSin' => $element['ParEmprSin'],
                    'EmprPlu' => $element['ParEmprPlu'],
                    'PlanSin' => $element['ParPlanSin'],
                    'PlanPlu' => $element['ParPlanPlu'],
                    'SucuSin' => $element['ParSucuSin'],
                    'SucuPlu' => $element['ParSucuPlu'],
                    'GrupSin' => $element['ParGrupSin'],
                    'GrupPlu' => $element['ParGrupPlu'],
                    'SectSin' => $element['ParSectSin'],
                    'SectPlu' => $element['ParSectPlu'],
                    'SeccSin' => $element['ParSeccSin'],
                    'SeccPlu' => $element['ParSeccPlu'],
                ],
                'ParDato' => [
                    'LegDocu' => $element['ParDatoLegDocu'],
                    'LegCUIL' => $element['ParDatoLegCUIL'],
                    'LegEmpr' => $element['ParDatoLegEmpr'],
                    'LegPlan' => $element['ParDatoLegPlan'],
                    'LegSucu' => $element['ParDatoLegSucu'],
                    'LegGrup' => $element['ParDatoLegGrup'],
                    'LegSect' => $element['ParDatoLegSect'],
                    'LegSecc' => $element['ParDatoLegSecc'],
                    'LegTare' => $element['ParDatoLegTare'],
                    'LegFeIn' => $element['ParDatoLegFeIn'],
                    'LegReCH' => $element['ParDatoLegReCH'],
                ],
                'FechaHora' => $element['FechaHora'],
            ];
        }
        // file_put_contents('paragene.log', print_r($rs, true));
        return $rs;
    }

    /**
     * Recupera datos de la tabla DBData.
     */
    public function dbData($return = false)
    {
        $inicio = microtime(true);
        try {
            $sql = "SELECT TOP 1 * FROM DBData";
            $Data = $this->conect->executeQueryWhithParams($sql);
            $Data = ($Data[0]);
            $BDVersion = $Data['BDVersion'];
            $BDVersion = explode("_", $BDVersion);
            $Data['SystemVer'] = intval($BDVersion[1]) ?? 0;
            $Data['FechaHora'] = $this->tools->formatDateTime($Data['FechaHora']) ?? '';
            if ($return) {
                return $Data ?? [];
            }
            $this->resp->respuesta($Data ?? [], 0, 'OK', 200, $inicio, 0, 0);
        } catch (\PDOException $e) {
            $this->log->trace('ParaGene::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw new \Exception('Error al obtener dbData', 400);
        } catch (\Exception $e) {
            $this->log->trace('ParaGene::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            throw $e;
        }
    }
}
