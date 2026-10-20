<?php

namespace Classes;

use DateTime;
use function in_array;
use function is_array;
use function is_string;

class InputValidator
{
    private array $data;
    private array $fields = [];
    private array $rules = [];
    private array $customRules = [];
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }
    public function addCustomRule(string $ruleName, callable $validationFunction, string $errorMessage)
    {
        $this->customRules[$ruleName] = [
            'validate' => $validationFunction,
            'message' => $errorMessage,
        ];
    }
    public function validate()
    {
        foreach ($this->rules as $field => $rules) {
            $this->fields[] = $field;
            foreach ($rules as $rule) {
                if (isset($this->customRules[$rule])) {
                    $this->validateCustomRule($field, $this->customRules[$rule]['validate'], $this->customRules[$rule]['message']);
                } else {
                    $this->validateRule($field, $rule);
                }
            }
        }
        return $this;
    }
    /**
     * Summary of validateCustomRule
     * @param mixed $field
     * @param mixed $validationFunction
     * @param mixed $errorMessage
     * @throws ValidationException
     * @return void
     */
    private function validateCustomRule($field, $validationFunction, $errorMessage)
    {
        $value = $this->data[$field];
        if (!$validationFunction($value)) {
            throw new ValidationException($errorMessage);
        }
    }
    public function getErrors()
    {
        return $this->errors;
    }
    /**
     * Summary of getMessage
     * @param mixed $field
     * @param mixed $rule
     * @return string
     */
    public function getMessage($field, $rule)
    {
        return "El campo \"$field\" no cumple con la regla \"$rule\"";
    }
    /**
     * Summary of generateErrorMessage
     * @param mixed $field
     * @param mixed $rule
     * @return string
     */
    private function generateErrorMessage($field, $rule)
    {
        $messages = [
            'allowed01' => "El campo $field debe tener un valor permitido. [0, 1]",
            'allowed012' => "El campo $field debe tener un valor permitido. [0, 1, 2]",
            'allowed1a7' => "El campo $field debe tener un valor permitido. [1, 2, 3, 4, 5, 6, 7]",
            'allowed0a3' => "El campo $field debe tener un valor permitido. [0, 1, 2, 3]",
            'allowed0a5' => "El campo $field debe tener un valor permitido. [0, 1, 2, 3, 4, 5]",
            'allowed0a100' => "El campo $field debe tener un valor permitido. [0, 1, 2, ..., 100]",
            'allowed0a9999' => "El campo $field debe tener un valor permitido. [0, 1, 2, ..., 9999]",
            'allowed0a999' => "El campo $field debe tener un valor permitido. [0, 1, 2, ..., 999]",
            'allowed1a12' => "El campo $field debe tener un valor permitido. [1, 2, 3, ..., 12]",
            'arrAllowed01' => "El campo $field debe ser un arreglo con valores permitidos. [0, 1]",
            'arrAllowed01Opt' => "El campo $field debe ser un arreglo con valores permitidos. [0, 1]",
            'arrAllowed012' => "El campo $field debe ser un arreglo con valores permitidos. [0, 1, 2]",
            'arrAllowed1a7' => "El campo $field debe ser un arreglo con valores permitidos. [1, 2, 3, 4, 5, 6, 7]",
            'arrAllowed0a8' => "El campo $field debe ser un arreglo con valores permitidos. [0, 1, 2, 3, 4, 5, 6, 7, 8]",
            'arrInt' => "El campo $field debe ser un arreglo de números enteros",
            'arrSmallint' => "El campo $field debe ser un arreglo de números enteros y menor a 32767",
            'arrSmallintEmpty' => "El campo $field debe ser un arreglo de números y menor a 32767",
            'arrVarcharEmpty' => "El campo $field debe ser un arreglo de cadenas de texto o vacío",
            'boolean' => "El campo $field debe ser un valor booleano",
            'date' => "El campo $field debe ser una fecha válida con formato yyyy-mm-dd",
            'futuredate' => "El campo $field debe ser una fecha futura válida con formato yyyy-mm-dd",
            'dateEmpty' => "El campo $field debe ser una fecha válida con formato yyyy-mm-dd o vacío",
            'datetime' => "El campo $field debe ser una fecha y hora válida con formato yyyy-mm-dd hh:mm:ss",
            'decima12.2' => "El campo $field debe ser un número decimal con 2 decimales y menor a 12 dígitos",
            'email' => "El campo $field debe ser una dirección de correo válida",
            'int' => "El campo $field debe ser un número entero y menor a 2147483647",
            'intempty' => "El campo $field debe ser un número entero",
            'numeric' => "El campo $field debe ser un número",
            'numeric10' => "El campo $field debe ser un número y menor a 10 dígitos",
            'numeric5' => "El campo $field debe ser un número y menor a 5 dígitos",
            'required' => "El campo $field es requerido",
            'requiredPermittedCero' => "El campo $field es requerido, pero se permite el valor cero",
            'smallint' => "El campo $field debe ser un número entero y menor a 32767",
            'tiponov' => "El campo $field debe ser un valor permitido: " . $this->validTipoNov(),
            'smallintEmpty' => "El campo $field debe ser un número entero y menor a 32767 o vacío",
            'time' => "El campo $field debe ser una hora válida con formato hh:mm",
            'timeEmpty' => "El campo $field debe ser una hora válida con formato hh:mm o vacío",
            'varchar1' => "El campo $field debe tener una longitud menor a 1 caracteres",
            'varchar3' => "El campo $field debe tener una longitud menor a 3 caracteres",
            'varchar10' => "El campo $field debe tener una longitud menor a 10 caracteres",
            'varchar100' => "El campo $field debe tener una longitud menor a 100 caracteres",
            'varchar200' => "El campo $field debe tener una longitud menor a 200 caracteres",
            'varchar20' => "El campo $field debe tener una longitud menor a 20 caracteres",
            'varchar40' => "El campo $field debe tener una longitud menor a 40 caracteres",
            'varcharMax' => "El campo $field debe tener una longitud menor a 2147483647 caracteres",
            'rgbColor' => "El campo $field debe ser un color RGB válido con formato #RRGGBB",
        ];

        return $messages[$rule] ?? "Error desconocido en la regla de validación";
    }
    /**
     * Summary of validateRule
     * @param mixed $field
     * @param mixed $rule
     * @throws ValidationException
     * @return void
     */
    private function validateRule($field, $rule)
    {
        $value = $this->data[$field] ?? null;

        $smallintOpt = [
            'options' => [
                'min_range' => 0,
                'max_range' => 32767,
            ],
        ];
        $intOpt = [
            'options' => [
                'min_range' => 1,
                'max_range' => 2147483647,
            ],
        ];

        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'requiredPermittedCero':
                if ($value === null || $value === '') {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'numeric':
                if (!is_numeric($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'numeric10':
                // if (!is_numeric($value) || strlen($value) > 10) {
                if (!is_numeric($value) || ($value !== null && strlen($value) > 10)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'numeric5':
                // if (!is_numeric($value) || strlen($value) > 5) {
                if (!is_numeric($value) || ($value !== null && strlen($value) > 5)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'date':
                $fecha = $this->validateDateFormat($value);
                if (!$fecha) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'futuredate':
                $fecha = $this->validateDateFormat($value);
                if (!$fecha) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                if ($fecha <= new DateTime()) {
                    throw new ValidationException("El campo $field debe ser una fecha futura válida con formato yyyy-mm-dd o yyyymmdd", 400);
                }
                break;
            case 'dateEmpty':
                if ($value) {
                    $fecha = $this->validateDateFormat($value);
                    if (!$fecha) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'time':
                if ($value && !preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'timeEmpty':
                if ($value && !preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar40':
                if ($value !== null && strlen($value) > 40) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar10':
                if ($value !== null && strlen($value) > 10) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar20':
                if ($value !== null && strlen($value) > 20) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar1':
                if ($value !== null && strlen($value) > 1) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar3':
                if ($value !== null && strlen($value) > 3) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar100':
                if ($value !== null && strlen($value) > 100) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar200':
                if ($value !== null && strlen($value) > 200) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varcharMax':
                if ($value !== null && strlen($value) > 2147483647) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;

            case 'allowed012':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 2), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'allowed0a9999':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 9999), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'allowed0a999':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 999), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'allowed1a12':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(1, 12), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'allowed01':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 1), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'arrAllowed012':
                $value ??= [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                foreach ($value as $val) {
                    if (!in_array($val, ['0', '1', '2'])) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'arrAllowed1a7':
                $value ??= [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                foreach ($value as $val) {
                    if (!in_array($val, ['1', '2', '3', '4', '5', '6', '7'])) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'allowed0a100':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 100), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'allowed0a3':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 3), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'allowed0a5':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 5), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'arrAllowed01':
                $value ??= [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                foreach ($value as $val) {
                    if (!in_array($val, ['0', '1'])) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'arrAllowed01Opt':
                $value ??= [];
                foreach ($value as $val) {
                    if (!in_array($val, ['0', '1'])) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'smallint':
                if (filter_var($value, FILTER_VALIDATE_INT, $smallintOpt) === false) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'tiponov':
                $valueInt = filter_var($value, FILTER_VALIDATE_INT);
                if ($valueInt === false || !in_array($valueInt, range(0, 8), true)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'smallintEmpty':
                if ($value && filter_var($value, FILTER_VALIDATE_INT, $smallintOpt) === false) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'arrSmallint':
                $value ??= [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                foreach ($value as $val) {
                    if (filter_var($val, FILTER_VALIDATE_INT, $smallintOpt) === false) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'arrSmallintEmpty':
                $value ??= [];
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if ($val && filter_var($val, FILTER_VALIDATE_INT, $smallintOpt) === false) {
                            throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                        }
                    }
                }
                break;
            case 'arrVarcharEmpty':
                $value ??= [];
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if ($val && !is_string($val)) {
                            throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                        }
                    }
                }
                break;
            case 'int':
                if (filter_var($value, FILTER_VALIDATE_INT, $intOpt) === false) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'intempty':
                if ($value) {
                    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'arrInt':
                $value ??= [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                foreach ($value as $val) {
                    if (filter_var($val, FILTER_VALIDATE_INT) === false) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'decima12.2':
                if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'datetime':
                if (!DateTime::createFromFormat('Y-m-d H:i:s', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'boolean':
                if (!is_bool($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'rgbColor':
                if (!preg_match('/^#([0-9a-fA-F]{6})$/', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
        }
    }

    /**
     * Valida una fecha aceptando múltiples formatos
     * @param string $value Valor a validar
     * @param array $formats Array de formatos a intentar (default: ['Y-m-d', 'Ymd'])
     * @return DateTime|false Retorna objeto DateTime si es válido, false si no
     */
    private function validateDateFormat($value, $formats = ['Y-m-d', 'Ymd'])
    {
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $value);
            // Verificar que la fecha sea válida y que el formato coincida exactamente
            if ($date && $date->format($format) === $value) {
                return $date;
            }
        }
        return false;
    }

    /**
     * Summary of validTipoNov
     * Retorna una cadena con las opciones de tipo de novedad en el formato "clave = valor" separadas por comas. Ejemplo: "0 = Llegada tarde, 1 = Incumplimiento, 2 = Salida anticipada, 3 = Ausencia, 4 = Licencia, 5 = Accidente, 6 = Vacaciones, 7 = Suspensión, 8 = ART"
     * @return string
     */
    private function validTipoNov()
    {
        return "0 = Llegada tarde, 1 = Incumplimiento, 2 = Salida anticipada, 3 = Ausencia, 4 = Licencia, 5 = Accidente, 6 = Vacaciones, 7 = Suspensión, 8 = ART";
    }
}
