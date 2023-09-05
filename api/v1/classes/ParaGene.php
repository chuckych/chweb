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
    public function get()
    {
        $inicio = microtime(true);
        $sql = "SELECT * FROM PARAGENE";
        $Data = $this->conect->executeQueryWhithParams($sql);
        foreach ($Data as &$element) {
            $rs = array(
                'Etiquetas' => array(
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
                ),
                'ParDato' => array(
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
                ),
                'FechaHora' => $element['FechaHora'],
            );
        }
        $this->resp->response($rs, 0, 'OK', 200, $inicio, 0, 0);
    }
}
