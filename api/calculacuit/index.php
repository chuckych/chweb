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
<script>
    //     function getCuilCuit(document_number, gender) {
    //         /**
    //          * Cuil format is: AB - document_number - C
    //          * Author: Nahuel Sanchez, Woile
    //          *
    //          * @param {str} document_number -> string solo digitos
    //          * @param {str} gender -> debe contener HOMBRE, MUJER o SOCIEDAD
    //          *
    //          * @return {str}
    //          **/
    //         "use strict";
    //         const HOMBRE = ["HOMBRE", "M", "MALE"],
    //             MUJER = ["MUJER", "F", "FEMALE"],
    //             SOCIEDAD = ["SOCIEDAD", "S", "SOCIETY"];
    //         let AB, C;

    //         /**
    //          * Verifico que el document_number tenga exactamente ocho numeros y que
    //          * la cadena no contenga letras.
    //          */
    //         if (document_number.length != 8 || isNaN(document_number)) {
    //             if (document_number.length == 7 && !isNaN(document_number)) {
    //                 document_number = "0".concat(document_number);
    //             } else {
    //                 // Muestro un error en caso de no serlo.
    //                 throw "El numero de document_number ingresado no es correcto.";
    //             }
    //         }

    //         /**
    //          * De esta manera permitimos que el gender venga en minusculas,
    //          * mayusculas y titulo.
    //          */
    //         gender = gender.toUpperCase();

    //         // Defino el valor del prefijo.
    //         if (HOMBRE.indexOf(gender) >= 0) {
    //             AB = "20";
    //         } else if (MUJER.indexOf(gender) >= 0) {
    //             AB = "27";
    //         } else {
    //             AB = "30";
    //         }

    //         /*
    //          * Los numeros (excepto los dos primeros) que le tengo que
    //          * multiplicar a la cadena formada por el prefijo y por el
    //          * numero de document_number los tengo almacenados en un arreglo.
    //          */
    //         const multiplicadores = [3, 2, 7, 6, 5, 4, 3, 2];

    //         // Realizo las dos primeras multiplicaciones por separado.
    //         let calculo = parseInt(AB.charAt(0)) * 5 + parseInt(AB.charAt(1)) * 4;

    //         /*
    //          * Recorro el arreglo y el numero de document_number para
    //          * realizar las multiplicaciones.
    //          */
    //         for (let i = 0; i < 8; i++) {
    //             calculo += parseInt(document_number.charAt(i)) * multiplicadores[i];
    //         }

    //         // Calculo el resto.
    //         let resto = parseInt(calculo) % 11;

    //         /*
    //          * Llevo a cabo la evaluacion de las tres condiciones para
    //          * determinar el valor de C y conocer el valor definitivo de
    //          * AB.
    //          */
    if (SOCIEDAD.indexOf(gender) < 0 && resto == 1) {
        if (HOMBRE.indexOf(gender) >= 0) {
            C = "9";
        } else {
            C = "4";
        }
        AB = "23";
    } else if (resto === 0) {
        C = "0";
    } else {
        C = 11 - resto;
    }
    //         const example = `${AB}-${document_number}-${C}`;
    //         // Show example
    //         console.log(example);

    //         // Generate cuit
    //         const cuil_cuit = `${AB}${document_number}${C}`;
    //         return cuil_cuit;
    //     }

    //     getCuilCuit('7660214', 'M')
    // 
</script>