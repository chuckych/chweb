<?php

class FichasEstruct
{
    private $api;
    private $request;
    private $userID;
    private $sesion_data;
    private $estruct;
    private $tipoEstructura;

    public function __construct($estruct, $tipoEstructura)
    {
        $this->sesion_data    = $_SESSION['Data'] ?? array();
        $this->userID         = $this->sesion_data['UserID']; // ID del usuario logueado
        $this->api            = new Request; // Instancia Request
        $this->request        = Flight::request();
        $this->estruct        = $estruct;
        $this->tipoEstructura = $tipoEstructura;
    }
    public function param($param)
    {
        $sesionEstruct = $this->sesion_data['UserEstruct']['estructura_ch'] ?? ''; // Estructura de control horarios del usuario logueado

        if (!$sesionEstruct) {;
            Flight::json(array("status" => "error", "data" => [], "total" => 0, "error" => 'Sesión expirada'));
            exit;
        }

        $EmprSesion  = $this->arrayParams($sesionEstruct['empresa_ch']); // filtro de empresas por usuario logueado
        $PlanSesion  = $this->arrayParams($sesionEstruct['planta_ch']); // filtro de plantas por usuario logueado
        $SectSesion  = $this->arrayParams($sesionEstruct['sector_ch']); // filtro de sectores por usuario logueado
        $Sec2Sesion  = $this->arrayParams($sesionEstruct['seccion_ch']); // filtro de secciones por usuario logueado
        $ConvSesion  = $this->arrayParams($sesionEstruct['convenio_ch']); // filtro de convenios por usuario logueado
        $GrupSesion  = $this->arrayParams($sesionEstruct['grupo_ch']); // filtro de grupos por usuario logueado
        $SucuSesion  = $this->arrayParams($sesionEstruct['sucursal_ch']); // filtro de sucursales por usuario logueado
        $THoraSesion = $this->arrayParams($sesionEstruct['thora_ch']); // filtro de tipos de horas por usuario logueado
        $NoveSesion  = $this->arrayParams($sesionEstruct['novedad_ch']); // filtro de tipos de horas por usuario logueado

        $desc     = $this->request->data->desc ?? '';
        $Esta     = $this->arrayParams('0,1,2');
        $Empr     = $this->arrayParams($this->request->data->Empr);
        $Plan     = $this->arrayParams($this->request->data->Plan);
        $Sect     = $this->arrayParams($this->request->data->Sect);
        $Sec2     = $this->arrayParams($this->request->data->Sec2);
        $Conv     = $this->arrayParams($this->request->data->Conv);
        $Grup     = $this->arrayParams($this->request->data->Grup);
        $Sucu     = $this->arrayParams($this->request->data->Sucu);
        $Lega     = $this->arrayParams($this->request->data->Lega);
        $Tipo     = $this->arrayParams($this->request->data->Tipo);
        $THora    = $this->arrayParams($this->request->data->THora);
        $Nove     = $this->arrayParams($this->request->data->Nove);
        $NoveTipo = $this->arrayParams($this->request->data->NoveTipo);

        $FechaIni = $this->validarFecha($this->request->data->FechaIni) ?? '';
        $FechaFin = $this->validarFecha($this->request->data->FechaFin) ?? '';

        $HoraMin = $this->validarHora($this->request->data->HoraMin) ? $this->request->data->HoraMin : '';
        $HoraMax = $this->validarHora($this->request->data->HoraMax) ? $this->request->data->HoraMax : '';

        if ($this->sesion_data) {
            $Empr  = $this->arrayParamsMerge($Empr, $EmprSesion); // merge de los filtros de empresas del usuario logueado con los filtros del usuario actual
            $Plan  = $this->arrayParamsMerge($Plan, $PlanSesion); // merge de los filtros de plantas del usuario logueado con los filtros del usuario actual
            $Sect  = $this->arrayParamsMerge($Sect, $SectSesion); // merge de los filtros de sectores del usuario logueado con los filtros del usuario actual
            $Sec2  = $this->arrayParamsMerge($Sec2, $Sec2Sesion); // merge de los filtros de secciones del usuario logueado con los filtros del usuario actual
            $Conv  = $this->arrayParamsMerge($Conv, $ConvSesion); // merge de los filtros de convenios del usuario logueado con los filtros del usuario actual
            $Grup  = $this->arrayParamsMerge($Grup, $GrupSesion); // merge de los filtros de grupos del usuario logueado con los filtros del usuario actual
            $Sucu  = $this->arrayParamsMerge($Sucu, $SucuSesion); // merge de los filtros de sucursales del usuario logueado con los filtros del usuario actual
            $THora = $this->arrayParamsMerge($THora, $THoraSesion); // merge de los filtros de tipos de horas del usuario logueado con los filtros del usuario actual
            $Nove  = $this->arrayParamsMerge($Nove, $NoveSesion); // merge de los filtros de novedades del usuario logueado con los filtros del usuario actual
        }

        $parameters = [
            'desc'      => $desc,
            'Esta'      => $Esta ?? [],
            'Empr'      => $Empr ?? [],
            'Plan'      => $Plan ?? [],
            'Sect'      => $Sect ?? [],
            'Sec2'      => $Sec2 ?? [],
            'Conv'      => $Conv ?? [],
            'Grup'      => $Grup ?? [],
            'Sucu'      => $Sucu ?? [],
            'Lega'      => $Lega ?? [],
            'Tipo'      => $Tipo ?? [],
            'THora'     => $THora ?? [],
            'Nove'      => $Nove ?? [],
            'NoveTipo'  => $NoveTipo ?? [],
            'FechaIni'  => $FechaIni ?? '',
            'FechaFin'  => $FechaFin ?? '',
            'HoraMin'   => $HoraMin ?? '',
            'HoraMax'   => $HoraMax ?? '',
            'estruct'   => $this->estruct ?? '',
        ];
        return $parameters[$param] ?? [];
    }
    private function bodyParams()
    {
        $query = array(
            "Desc"      => $this->param('desc'),
            "Sector"    => $this->param('Sect'),
            "Lega"      => $this->param('Lega'),
            "Empr"      => $this->param('Empr'),
            "Plan"      => $this->param('Plan'),
            "Conv"      => $this->param('Conv'),
            "Sect"      => $this->param('Sect'),
            "Sec2"      => $this->param('Sec2'),
            "Grup"      => $this->param('Grup'),
            "Sucu"      => $this->param('Sucu'),
            "Tipo"      => $this->param('Tipo'),
            "FechaIni"  => $this->param('FechaIni'),
            "FechaFin"  => $this->param('FechaFin'),
            "HoraMin"   => $this->param('HoraMin'),
            "HoraMax"   => $this->param('HoraMax'),
            "THora"     => $this->param('THora'),
            "Nove"      => $this->param('Nove'),
            "NoveTipo"  => $this->param('NoveTipo'),
            "Esta"      => $this->param('Esta'),
            "start"     => 0,
            "length"    => 10
        );
        return $query;
    }
    public function get()
    {
        try {

            if (!$this->estruct) {
                throw new Exception("Estructura no definida");
            }

            if (!$this->userID) {
                throw new Exception('Sesión expirada');
            }

            $url  = "/v1/{$this->tipoEstructura}/estruct/{$this->estruct}";
            $return = json_decode($this->api->chapi($url, $this->bodyParams(), 'POST', ''), true); // Llamada a la API de Control Horario

            $return['MESSAGE'] = $return['MESSAGE'] ?? '';
            $return['DATA'] = $return['DATA'] ?? '';

            if ($return['MESSAGE'] != 'OK') {
                $error = (!($return['DATA'])) ? $return['MESSAGE'] : $return['DATA'];
                throw new Exception($error);
            }

            $arr = array(
                "status"  => "ok",
                "data"    => ($return['DATA']) ? $return['DATA'] : [],
                "total"   => $return['TOTAL'] ?? 0,
                "query"   => $this->bodyParams(),
                "estruct" => $this->estruct ?? '',
            );
        } catch (\Throwable $th) {
            $arr = array(
                "status" => "error",
                "data"   => [],
                "total"  => 0,
                "error"  => $th->getMessage(),
            );
        }
        Flight::json($arr);
    }
    private function arrayParams($param)
    {
        if ($param === '' || $param === null) {
            return [];
        }
        return explode(",", $param);
    }
    private function arrayParamsMerge($array, $arraySesion)
    {
        $array = array_unique(array_merge($array, $arraySesion));
        return array_values($array);
    }
    private function validarFecha($date)
    {
        if (!\DateTime::createFromFormat('Y-m-d', $date)) {
            return false;
        }
        return $date;
    }
    private function validarHora($hora)
    {
        if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $hora)) {
            return false;
        }
        return true;
    }
}
