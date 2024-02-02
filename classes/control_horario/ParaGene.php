<?php
class ParaGene
{

    /**
     * Retorna los parámetros genéricos de la aplicación de control horario
     * @return array Retorna un array con la respuesta de la api de CH.
     * @throws Exception
     */
    public function get()
    {
        $request = new Request();
        // return $data;
        $r = $request->chapi('/v1/paragene/', '', 'GET');
        $r = json_decode($r, true);
        if ($r['DATA']) {
            $r["DATA"]["Etiquetas"]["ConvSin"] = "Convenio";
            $r["DATA"]["Etiquetas"]["ConvPlu"] = "Convenios";
            $r["DATA"]["Etiquetas"]["LegaSin"] = "Legajo";
            $r["DATA"]["Etiquetas"]["LegaPlu"] = "Legajos";
            $r["DATA"]["Etiquetas"]["ThoraSin"] = "Tipo de hora";
            $r["DATA"]["Etiquetas"]["ThoraPlu"] = "Tipos de hora";
            $r["DATA"]["Etiquetas"]["NoveSin"] = "Novedad";
            $r["DATA"]["Etiquetas"]["NovePlu"] = "Novedades";
            $r["DATA"]["Etiquetas"]["TipoNoveSin"] = "Tipo de Novedad";
            $r["DATA"]["Etiquetas"]["TipoNovePlu"] = "Tipos de Novedad";
            $r["DATA"]["Etiquetas"]["MinMaxSin"] = "Min/Max Horas hechas";
            $r["DATA"]["Etiquetas"]["MinMaxPlu"] = "Min/Max Horas hechas";
        }
        if ($r['DATA']['ParDato']) {
            $this->createJson($r['DATA']['ParDato'], 'json/ParDato');
        }
        if ($r['DATA']['Etiquetas']) {
            $this->createJson($r['DATA']['Etiquetas'], 'json/Etiquetas');
        }
        Flight::json($r);
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
