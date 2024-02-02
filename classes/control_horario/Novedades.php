<?php
class Novedades
{
    /**
     * Funcion que actualiza las horas de un caso en CH
     * @param int $idCaso id del caso
     * @return array Retorna un array con la respuesta de la api de CH.
     */
    public function updateNovedades($idCaso)
    {
        $db = new DBDatos();
        $request = new Request();
        $data = $db->novedadesToCH($idCaso);
        // return $data;
        if (is_array($data)) {
            $r = $request->chapi('/v1/novedades/', $data, 'PUT');
            return json_decode($r, true);
        }
        return [];
    }
}
