<?php

namespace Classes;

use Classes\Response;
use Classes\Log;
use Classes\ConnectSqlSrv;
use Classes\Tools;
use Flight;


class ParaGene
{
    private $resp;
    private $request;
    private $getData;
    private $query;
    private $log;
    private $conect;
    private $tools;

    function __construct()
    {
        $this->resp = new Response;
        $this->request = Flight::request();
        $this->getData = $this->request->data->getData();
        $this->query = $this->request->query->getData();
        $this->log = new Log;
        $this->conect = new ConnectSqlSrv;
        $this->tools = new Tools;
    }

    /**
     * Recupera datos de la tabla PARAGENE.
     */
    public function get()
    {
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
        $this->resp->respuesta($rs, 0, 'OK', 200, $inicio, 0, 0);
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
        $this->resp->respuesta($rs, 0, 'OK', 200, $inicio, 0, 0);
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
            $Data['FechaHora'] = $this->tools->formatDateTime($Data['FechaHora']);
            if ($return) {
                return $Data;
            }
            $this->resp->respuesta($Data, 0, 'OK', 200, $inicio, 0, 0);
        } catch (\Throwable $th) {
            $this->log->write($th->getMessage(), 'error');
            $this->resp->respuesta($th->getMessage(), 1, 'Error', 400, $inicio, 0, 0);
        }
    }
}
