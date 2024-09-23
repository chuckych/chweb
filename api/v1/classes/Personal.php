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
        $this->resp       = new Response;
        $this->request    = Flight::request();
        $this->getData    = $this->request->data->getData();
        $this->query      = $this->request->query->getData();
        $this->log        = new Log;
        $this->conect     = new ConnectSqlSrv;
        $this->tools      = new Tools;
        $this->paraGene   = new ParaGene;
        $this->auditor    = new Auditor;
        $this->url        = $this->request->url;
    }
    public function return_legajos($connDB = '')
    {
        $conn = $this->conect->check_connection($connDB);
        $FHora      = $this->tools->get_fecha_hora($conn, 'PERSONAL');
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
}
