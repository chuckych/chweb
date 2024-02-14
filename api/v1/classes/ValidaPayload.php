<?php

namespace Classes;

class ValidarPayload
{
    function validar($value, $key, $type = 'str', $length = 1, $validArr = array())
    {
        if ($value) {
            if ($type == 'int') {
                if ($value) {
                    if (!is_numeric($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser {int}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    } else {
                        if (!filter_var($value, FILTER_VALIDATE_INT)) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$value'", 400, microtime(true), 0, 0));
                            exit;
                        }
                    }
                    if (strlen($value) > $length) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    if (($value) < 0 || ($value) > 2147483648) {
                        http_response_code(400);
                        if (($value) > 2147483648) {
                            (response(array(), 0, "Parámetro '$key' no puede ser mayor '2147483648'. Valor '$value'", 400, microtime(true), 0, 0));
                            exit;
                        }
                        (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '1'. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                }
            }
            if ($type == 'int01') {
                if ($value) {
                    switch ($value) {
                        case(!is_numeric($value)):
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor '$value'", 400, microtime(true), 0, 0));
                            // exit;
                            break;
                        case(!filter_var($value, FILTER_VALIDATE_INT)):
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor = '$value'", 400, microtime(true), 0, 0));
                            // exit;
                            break;
                        case(strlen($value) > $length):
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' debe ser igual a '$length' caracter. Valor '$value'", 400, microtime(true), 0, 0));
                            // exit;
                            break;
                        case(($value) < 0):
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' debe ser mayor o igual a '1'. Valor '$value'", 400, microtime(true), 0, 0));
                            // exit;
                            break;
                        case(($value) > 1):
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' no puede ser mayor '1'. Valor '$value'", 400, microtime(true), 0, 0));
                            // exit;
                            break;
                        default:
                            break;
                    }

                    // if (!is_numeric($value)) {
                    //     http_response_code(400);
                    //     (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor '$value'", 400, microtime(true), 0, 0));
                    //     exit;
                    // } else {
                    //     if (!filter_var($value, FILTER_VALIDATE_INT)) {
                    //         http_response_code(400);
                    //         (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor = '$value'", 400, microtime(true), 0, 0));
                    //         exit;
                    //     }
                    // }
                    // if (strlen($value) > $length) {
                    //     http_response_code(400);
                    //     (response(array(), 0, "Parámetro '$key' debe ser igual a '$length' caracter. Valor '$value'", 400, microtime(true), 0, 0));
                    //     exit;
                    // }
                    // if (($value) < 0) {
                    //     http_response_code(400);
                    //     (response(array(), 0, "Parámetro '$key' debe ser mayor o igual a '1'. Valor '$value'", 400, microtime(true), 0, 0));
                    //     exit;
                    // }
                    // if (($value) > 1) {
                    //     http_response_code(400);
                    //     (response(array(), 0, "Parámetro '$key' no puede ser mayor '1'. Valor '$value'", 400, microtime(true), 0, 0));
                    //     exit;
                    // }
                }
            }
            if ($type == 'intArray') {
                if ($value) {
                    if (!is_array($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    foreach (array_unique($value) as $v) {
                        if ($v) {
                            if (!is_numeric($v)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, microtime(true), 0, 0));
                                exit;
                            } else {
                                if (!filter_var($v, FILTER_VALIDATE_INT)) {
                                    http_response_code(400);
                                    (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, microtime(true), 0, 0));
                                    exit;
                                }
                            }
                        }
                        if (($v) < 0) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '0'", 400, microtime(true), 0, 0));
                            exit;
                        }
                        if (strlen($v) > $length) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$v'", 400, microtime(true), 0, 0));
                            exit;
                        }
                    }
                }
            }
            if ($type == 'intArrayM8') {
                if ($value) {
                    if (!is_array($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    foreach ($value as $v) {
                        if ($v) {
                            if (!is_numeric($v)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, microtime(true), 0, 0));
                                exit;
                            } else {
                                if (!filter_var($v, FILTER_VALIDATE_INT)) {
                                    http_response_code(400);
                                    (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, microtime(true), 0, 0));
                                    exit;
                                }
                            }
                        }
                        if (($v) < 0) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '0'", 400, microtime(true), 0, 0));
                            exit;
                        }
                        if (strlen($v) > $length) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$v'", 400, microtime(true), 0, 0));
                            exit;
                        }
                        if (($v) > 8) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser menor o igual a '8'", 400, microtime(true), 0, 0));
                            exit;
                        }
                    }
                }
            }
            if ($type == 'intArrayM0') { // {int}mayor a 0
                if ($value) {
                    if (!is_array($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    foreach ($value as $v) {
                        if ($v) {
                            if (!is_numeric($v)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if (!filter_var($v, FILTER_VALIDATE_INT)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if ($v === 0) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser mayor a '0'", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if ($v < 0) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' no debe ser menor a '0'", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if (strlen($v) > $length) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length'. Valor '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                        }
                    }
                }
            }
            if ($type == 'numArray01') {
                if ($value) {
                    if (!is_array($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    foreach ($value as $v) {
                        if ($v) {
                            if (!is_numeric($v)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if (($v) < 0) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '0'. Valor = '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if (($v) > 1) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de '0' o '1'. Valor = '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if (strlen($v) > $length) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length'.Valor '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                        }
                    }
                }
            }
            if ($type == 'strArray') {
                if ($value) {
                    if (!is_array($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    foreach ($value as $v) {
                        if (strlen($v) > $length) {
                            if ($v) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length'. Valor '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                        }
                    }
                }
            }
            if ($type == 'strArrayMMlength') {
                if ($value) {
                    if (!is_array($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    foreach ($value as $v) {
                        if ($v) {
                            if (strlen($v) <> $length) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' debe contener '$length'. Valor '$v'", 400, microtime(true), 0, 0));
                                exit;
                            }
                        }
                    }
                }
            }
            if ($type == 'str') {
                if ($value) {
                    if (strlen($value) > $length) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$value", 400, microtime(true), 0, 0));
                        exit;
                    }
                }
            }
            if ($type == 'arrfecha') {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Se espera un array del parametro \"$key\"", 400, microtime(true), 0, 0));
                    exit;
                }
                if ($value) {
                    foreach ($value as $v) {
                        validaFecha($v);
                    }
                }
            }
            if ($type == 'strArraySel2') {
                if ($value) {
                    if (!is_array($value)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, microtime(true), 0, 0));
                        exit;
                    }
                    foreach ($value as $v) {
                        if (strlen($v) < 3 && $v != '') {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la secciónxx.", 400, microtime(true), 0, 0));
                            exit;
                        }
                        if ($v) {
                            if (!strpos($v, '-')) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección.", 400, microtime(true), 0, 0));
                                exit;
                            }
                            $vArr = explode('-', $v);
                            if (count($vArr) > 2) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección.", 400, microtime(true), 0, 0));
                                exit;
                            }
                            $index0 = ($vArr[0]);
                            $index1 = ($vArr[1]);
                            if ($index0 == '0' || $index1 == '0') {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección y los valores no pueden ser 0 (ceros)", 400, microtime(true), 0, 0));
                                exit;
                            }
                            if (!is_numeric($index0) || !is_numeric($index1)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección y los valores deben ser números enteros", 400, microtime(true), 0, 0));
                                exit;
                            }
                        }
                    }
                }
            }
        }
        if ($type == 'strValid') {
            if ($value) {
                if (!in_array($value, $validArr)) {
                    $valores = implode(', ', $validArr);
                    http_response_code(400);
                    (response("Valor de parámetro '$key' es inválido. Valor '$value'. Valores disponibles: $valores", 0, 'Error', 400, microtime(true), 0, 0));
                    exit;
                }
            } else {
                http_response_code(400);
                (response("Parámetro $key es requerido.", 0, 'Error', 400, microtime(true), 0, 0));
                exit;
            }
        }
        return $value;
    }
}
