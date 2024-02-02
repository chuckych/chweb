<?php

class Estruct
{
    private $api;
    private $request;
    private $userID;
    private $sesion_data;

    public function __construct()
    {
        $this->sesion_data = $_SESSION['Data'] ?? array();
        $this->userID = $this->sesion_data['UserID']; // ID del usuario logueado
        $this->api = new Request; // Instancia Request
        $this->request = Flight::request();
    }
    public function get($estruct)
    {
        try {

            if (!$estruct) {
                throw new Exception("Estructura no definida");
            }

            if (!$this->userID) {
                throw new Exception('Sesión expirada');
            }

            $cuerpo = array(
                "Estruct" => $estruct,
                "start" => 0,
                "length" => 1000,
            );

            $url = "/estruct/";
            $return = json_decode($this->api->chapi($url, '', 'GET', $cuerpo), true); // Llamada a la API de Control Horario

            $return['MESSAGE'] = $return['MESSAGE'] ?? '';
            $return['DATA'] = $return['DATA'] ?? [];

            if ($return['MESSAGE'] != 'OK') {
                $error = (!($return['DATA'])) ? $return['MESSAGE'] : $return['DATA'];
                throw new Exception($error);
            }

            $data = $return['DATA'];

            $arr = array(
                "draw" => '0',
                "recordsTotal" => $return['COUNT'],
                "recordsFiltered" => $return['TOTAL'] ?? 0,
                "data" => $data,
                "file" => $this->createJson($data, 'json/' . $estruct),
            );

        } catch (\Throwable $th) {
            $arr = array(
                "draw" => '',
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => array(),
                "error" => $th->getMessage(),
            );
        }
        Flight::json($arr);
    }
    public function createJson($data, $name)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = fopen($name . ".json", "w");
        fwrite($file, $json);
        fclose($file);
        return 'se creo el archivo';
    }
}
