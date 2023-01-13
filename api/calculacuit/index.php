<?php
header("Content-Type: application/json");
date_default_timezone_set('America/Argentina/Buenos_Aires');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(400);
    echo json_encode(array('status' => 'Error', 'Mensaje' => 'Metodo Invalido'));
    exit;
}

$_GET['genero'] = $_GET['genero']  ?? '';
$_GET['dni']    = $_GET['dni']  ?? '';

if (strlen(intval($_GET['dni'])) < 7) {
    http_response_code(400);
    echo json_encode(array('status' => 'Error', 'Mensaje' => 'El DNI debe ser mayor a 7 caracteres'));
    exit;
}
if (strlen(intval($_GET['dni'])) > 8) {
    http_response_code(400);
    echo json_encode(array('status' => 'Error', 'Mensaje' => 'El DNI debe ser menor o igual a 8 caracteres'));
    exit;
}
if (empty($_GET['genero'])) {
    http_response_code(400);
    echo json_encode(array('status' => 'Error', 'Mensaje' => 'El parametro genero es requerido'));
    exit;
}
$_GET['genero'] = ucwords($_GET['genero']);

$validGender = array("M", "F", "S");

if (!in_array($_GET['genero'], $validGender)) {
    http_response_code(400);
    echo json_encode(array('status' => 'Error', 'Mensaje' => 'El parametro genero debe ser: ' . implode(' | ', $validGender)));
    exit;
}
/**
 * @param {str} document_number -> string solo digitos
 * @param {str} gender -> debe contener H, M o S
 * @return {str}
 **/
function getCuil($document_number, $gender)
{
    /** Formula: https://es.wikipedia.org/wiki/Clave_%C3%9Anica_de_Identificaci%C3%B3n_Tributaria */
    $AB = '';
    $C  = '';
    define('HOMBRE', ["HOMBRE", "M", "MALE"]);
    define('MUJER', ["MUJER", "F", "FEMALE"]);
    define('SOCIEDAD', ["SOCIEDAD", "S", "SOCIETY"]);

    $gender = ucwords($gender);
    $document_number = str_pad($document_number, 8, '0', STR_PAD_LEFT);

    // Defino el valor del prefijo.
    if (array_search($gender, HOMBRE)) {
        $AB = "20";
    } else if (array_search($gender, MUJER)) {
        $AB = "27";
    } else {
        $AB = "30";
    }

    $multiplicadores = [3, 2, 7, 6, 5, 4, 3, 2];

    // Realizo las dos primeras multiplicaciones por separado.
    $calculo = intval(substr($AB, 0, 1)) * 5 + intval(substr($AB, 1, 1)) * 4;
    /*
    * Recorro el arreglo y el numero de document_number para
    * realizar las multiplicaciones.
    */
    for ($i = 0; $i < 8; $i++) {
        $calculo += intval(substr($document_number, $i, 1)) * $multiplicadores[$i];
    }
    // Calculo el resto.
    $resto = (intval($calculo) % 11);
    /*
    * Llevo a cabo la evaluacion de las tres condiciones para
    * determinar el valor de C y conocer el valor definitivo de
    * AB.
    */
    if ($AB != 30 && $resto == 1) {
        if ($AB == 20) {
            $C = "9";
        } else {
            $C = "4";
        }
        $AB = "23";
    } else if ($resto === 0) {
        $C = "0";
    } else {
        $C = 11 - $resto;
    }
    $cuil_cuit = $AB . '-' . $document_number . '-' . $C;
    $text = date('H:i:s') . " DNI: \"$document_number\". GENERO: \"$gender\". CUIL : \"$cuil_cuit\".\n";
    file_put_contents(date('Y-m-d') . '_logRequest.log', $text, FILE_APPEND | LOCK_EX);
    return $cuil_cuit;
}

$CUIL = (getCuil($_GET['dni'], $_GET['genero']));
http_response_code(200);
echo json_encode(array('status' => 'ok', 'Mensaje' => $CUIL));
exit;
?>