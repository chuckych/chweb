<?php

namespace Classes;

class ValidaRequest
{
    public function interperson($array)
    {
        $tools = new tools();
        $errores = array();
        $optionsInt = array(
            'options' => array(
                'min_range' => 0,
                'max_range' => 2147483648
            )
        );

        if ($array) {
            foreach ($array as $key => $value) {
                // Validar longitud de cada valor
                foreach ($value as $k => $v) {
                    switch ($k) {
                        case 'LegNume':
                            // Validar LegNume como entero
                            if (empty($v)) {
                                $errores[] = "El valor de LegNume en el elemento $key es requerido.";
                                unset($array[$key]);
                            }
                            if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                $errores[] = "El valor de LegNume en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegApNo':
                            if (empty($v)) {
                                $errores[] = "El valor de LegApNo en el elemento $key es requerido.";
                                unset($array[$key]);
                            }

                            // Validar LegApNo como string
                            if (!is_string($v)) {
                                $errores[] = "El valor de LegApNo en el elemento $key no es un string.";
                                unset($array[$key]);
                            }
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de LegApNo en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTDoc':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegTDoc en el elemento $key solo admite los siguientes valores 0;1;2;3;4.";
                                unset($array[$key]);
                            }
                            if (($v) > 4) {
                                $errores[] = "El valor de LegTDoc en el elemento $key solo admite los siguientes valores 0;1;2;3;4.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDocu':
                            if ($v) {
                                if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                    $errores[] = "El valor de LegDocu en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                    unset($array[$key]);
                                }
                            }
                            break;
                        case 'LegCUIT':
                            if (strlen($v) > 13) {
                                $errores[] = "El valor de LegCUIT en el elemento $key supera los 13 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDomi':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de LegDomi en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDoNu':
                            if ($v) {
                                if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                    $errores[] = "El valor de LegDoNu en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                    unset($array[$key]);
                                }
                            }
                            break;
                        case 'LegDoPi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de LegDoPi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de LegDoPi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDoDP':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de LegDoDP en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegDoOb':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de LegDoOb en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegCOPO':
                            if (strlen($v) > 8) {
                                $errores[] = "El valor de LegCOPO en el elemento $key supera los 8 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTel1':
                            if (strlen($v) > 15) {
                                $errores[] = "El valor de LegTel1 en el elemento $key supera los 15 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTel2':
                            if (strlen($v) > 15) {
                                $errores[] = "El valor de LegTel2 en el elemento $key supera los 15 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTel3':
                            if (strlen($v) > 15) {
                                $errores[] = "El valor de LegTel3 en el elemento $key supera los 15 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTeO1':
                            if (strlen($v) > 20) {
                                $errores[] = "El valor de LegTeO1 en el elemento $key supera los 20 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTeO2':
                            if (strlen($v) > 20) {
                                $errores[] = "El valor de LegTeO2 en el elemento $key supera los 20 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegMail':
                            if (strlen($v) > 250) {
                                $errores[] = "El valor de LegMail en el elemento $key supera los 250 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegEsCi':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegEsCi en el elemento $key solo admite los siguientes valores 0;1;2;3.";
                                unset($array[$key]);
                            }
                            if (($v) > 3) {
                                $errores[] = "El valor de LegEsCi en el elemento $key solo admite los siguientes valores 0;1;2;3.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegSexo':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegSexo en el elemento $key solo admite los siguientes valores 0;1.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegFeNa':
                            $fecha = $tools->validarFecha($v);
                            if ($fecha != false) {
                                $errores[] = "El valor de LegFeNa en el elemento $key es incorrecto. $fecha";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegTipo':
                            if (strlen($v) > 1) {
                                $errores[] = "El valor de LegTipo en el elemento $key solo admite los siguientes valores 0;1.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegFeIn':
                            $fecha = $tools->validarFecha($v);
                            if ($fecha != false) {
                                $errores[] = "El valor de LegFeIn en el elemento $key es incorrecto. $fecha";
                                unset($array[$key]);
                            }
                            break;
                        case 'LegFeEg':
                            $fecha = $tools->validarFecha($v);
                            if ($fecha != false) {
                                $errores[] = "El valor de LegFeEg en el elemento $key es incorrecto. $fecha";
                                unset($array[$key]);
                            }
                            break;
                        case 'NacCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de NacCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de NacCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'NacDesc':
                            if (strlen($v) > 30) {
                                $errores[] = "El valor de NacDesc en el elemento $key supera los 30 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ProCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de ProCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de ProCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ProDesc':
                            if (strlen($v) > 30) {
                                $errores[] = "El valor de ProDesc en el elemento $key supera los 30 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LocCodi':
                            $v = ($v) ? $v : 0;
                            if (filter_var($v, FILTER_VALIDATE_INT, $optionsInt) === false) {
                                $errores[] = "El valor de LocCodi en el elemento $key no es un entero válido o está fuera del rango especificado.";
                                unset($array[$key]);
                            }
                            break;
                        case 'LocDesc':
                            if (strlen($v) > 50) {
                                $errores[] = "El valor de LocDesc en el elemento $key supera los 50 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'EmpCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de EmpCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de EmpCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'EmpRazon':
                            if (strlen($v) > 50) {
                                $errores[] = "El valor de EmpRazon en el elemento $key supera los 50 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'PlaCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de PlaCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de PlaCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'PlaDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de PlaDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SucCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de SucCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de SucCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SucDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de SucDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SecCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de SecCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de SecCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'SecDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de SecDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'Se2Codi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de Se2Codi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de Se2Codi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'Se2Desc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de Se2Desc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'GruCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de GruCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de GruCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'GruDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de GruDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ConCodi':
                            if (strlen($v) > 5) {
                                $errores[] = "El valor de ConCodi en el elemento $key supera los 5 caracteres.";
                                unset($array[$key]);
                            }
                            if (($v) > 32767) {
                                $errores[] = "El valor de ConCodi en el elemento $key no puede ser superior a 32767.";
                                unset($array[$key]);
                            }
                            break;
                        case 'ConDesc':
                            if (strlen($v) > 40) {
                                $errores[] = "El valor de ConDesc en el elemento $key supera los 40 caracteres.";
                                unset($array[$key]);
                            }
                            break;
                    }
                }
            }
            $a = array(
                "errores" => $errores,
                "payload" => array_values($array),
            );
            return $a;
        }
        return array();
    }
}
