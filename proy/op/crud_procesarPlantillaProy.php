<?php
$w = ($_POST['ProyPlantPlanos'] == 'NULL') ? "PlantDesc = '$_POST[ProyNom]'" : "PlantID = '$_POST[ProyPlantPlanos]'"; // variable para buscar la plantilla en la query de checkPlantilla. Si es null buscamos por el nombre del proyecto. Sino es null buscamos por el id de plantilla recibido por post.
$checkPlantilla = count_pdoQuery("SELECT 1 FROM proy_plantillas WHERE proy_plantillas.$w AND proy_plantillas.PlantMod = '44' AND proy_plantillas.Cliente = '$dataUser[ID_CLIENTE]' LIMIT 1"); // hacemos un count para ver si existe alguna plantilla.

$i = array( // definimos un array con datos para insertar una nueva plantilla
    "query" => "INSERT INTO proy_plantillas (PlantDesc, PlantMod, PlantAlta, Cliente) VALUES ('$_POST[ProyNom]', '44', '$FechaHora', '$dataUser[ID_CLIENTE]')",
    "audito" => array("Proyectos - Plantilla Planos: $_POST[ProyNom].", 'A', '', 44)
);
$a = $i['audito']; // variable de auditoria.
/** 
 * Si no existe ninguna plantilla, la creamos y auditamos la creacion de la misma, 
 * si existe no hacemos nada;
 */
(!$checkPlantilla) ? ((pdoQuery($i['query'])) ? (auditoria($a[0], $a[1], $a[2], $a[3])) : PrintRespuestaJson('ERROR', 'Error al crear la plantilla') . exit) : '';

$dataPlantilla = simple_pdoQuery("SELECT proy_plantillas.PlantID AS 'id_plantilla', proy_plantillas.PlantDesc AS 'nombre_plantilla' FROM proy_plantillas WHERE proy_plantillas.$w AND proy_plantillas.Cliente = '$dataUser[ID_CLIENTE]' ORDER BY proy_plantillas.PlantAlta DESC LIMIT 1"); // buscamos datos de la plantilla creada.

$valores = implode(',',array_unique(explode(',',str_replace(' ','',$_POST['ProyLiPlanos'])))); // valores de planos enviados por post separados por comas. Ej. 54,55,45,15

$checkPlanos = count_pdoQuery("SELECT 1 FROM proy_plantilla_plano WHERE proy_plantilla_plano.PlaPlanoID = '$dataPlantilla[id_plantilla]' LIMIT 1"); // Chequeamos si la plantilla tiene planos asignados

$query_u = array( // array de update proy_plantilla_plano
    "query" => "UPDATE proy_plantilla_plano SET proy_plantilla_plano.PlaPlanos = '$valores' WHERE proy_plantilla_plano.PlaPlanoID = '$dataPlantilla[id_plantilla]'",
    "audito" => array("Proyectos - Planos Plantilla: ($dataPlantilla[id_plantilla]) $dataPlantilla[nombre_plantilla]. Se actualizaron valores", 'M', '', '44')
);
$query_i = array( // array de insert proy_plantilla_plano
    "query" => "INSERT INTO proy_plantilla_plano(PlaPlanoID, PlaPlanos, PlaPlanoAlta) VALUES ('$dataPlantilla[id_plantilla]', '$valores', '$FechaHora')",
    "audito" => array("Proyectos - Planos Plantilla: ($dataPlantilla[id_plantilla]) $dataPlantilla[nombre_plantilla]. Se agregaron valores", 'A', '', '44')
);

$a = ($checkPlanos) ? $query_u : $query_i;
$au = $a['audito'];
if (pdoQuery($a['query'])) { // Si se asignaron los planos
    auditoria($au[0], $au[1], $au[2], $au[3]); // auditamos
} else {
    // PrintRespuestaJson('ERROR', 'Error al modificar la plantilla.') . exit; // Mostramos error
    PrintRespuestaJson('ERROR', $a['query']) . exit; // Mostramos error

}
$_POST['ProyPlantPlanos'] = $dataPlantilla['id_plantilla'];