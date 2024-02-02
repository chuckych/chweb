<?php
class Horas
{
    /**
     * Funcion que actualiza las horas de un caso en CH
     * @param int $idCaso id del caso
     * @return array Retorna un array con la respuesta de la api de CH.
     */
    public function updateHoras($idCaso)
    {
        $db = new DBDatos();
        $request = new Request();
        $data = $db->horasToCH($idCaso);
        // return $data;
        if (is_array($data)) {
            $r = $request->chapi('/v1/horas/', $data, 'PUT');
            return json_decode($r, true);
        }
        return [];
    }
}
