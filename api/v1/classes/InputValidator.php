<?php

namespace Classes;

use DateTime;

class ValidationException extends \Exception
{
}

class InputValidator
{
    private $data;
    private $fields = [];
    private $rules = [];
    private $customRules = [];
    private $errors = [];

    public function __construct($data, $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
    }
    public function addCustomRule($ruleName, $validationFunction, $errorMessage)
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
    public function getMessage($field, $rule)
    {
        return "El campo \"$field\" no cumple con la regla \"$rule\"";
    }
    private function generateErrorMessage($field, $rule)
    {
        $messages = [
            'allowed01' => "El campo $field debe tener un valor permitido. [0, 1]",
            'allowed012' => "El campo $field debe tener un valor permitido. [0, 1, 2]",
            'allowed1a7' => "El campo $field debe tener un valor permitido. [1, 2, 3, 4, 5, 6, 7]",
            'arrAllowed01' => "El campo $field debe ser un arreglo con valores permitidos. [0, 1]",
            'arrAllowed012' => "El campo $field debe ser un arreglo con valores permitidos. [0, 1, 2]",
            'arrAllowed1a7' => "El campo $field debe ser un arreglo con valores permitidos. [1, 2, 3, 4, 5, 6, 7]",
            'arrInt' => "El campo $field debe ser un arreglo de números enteros",
            'arrSmallint' => "El campo $field debe ser un arreglo de números enteros y menor a 32767",
            'arrSmallintEmpty' => "El campo $field debe ser un arreglo de números y menor a 32767",
            'boolean' => "El campo $field debe ser un valor booleano",
            'date' => "El campo $field debe ser una fecha válida con formato yyyy-mm-dd",
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
            'smallint' => "El campo $field debe ser un número entero y menor a 32767",
            'smallintEmpty' => "El campo $field debe ser un número entero y menor a 32767 o vacío",
            'time' => "El campo $field debe ser una hora válida con formato hh:mm",
            'timeEmpty' => "El campo $field debe ser una hora válida con formato hh:mm o vacío",
            'varchar1' => "El campo $field debe tener una longitud menor a 1 caracteres",
            'varchar10' => "El campo $field debe tener una longitud menor a 10 caracteres",
            'varchar100' => "El campo $field debe tener una longitud menor a 100 caracteres",
            'varchar200' => "El campo $field debe tener una longitud menor a 200 caracteres",
            'varchar20' => "El campo $field debe tener una longitud menor a 20 caracteres",
            'varchar40' => "El campo $field debe tener una longitud menor a 40 caracteres",
            'varcharMax' => "El campo $field debe tener una longitud menor a 2147483647 caracteres",
        ];

        return $messages[$rule] ?? "Error desconocido en la regla de validación";
    }
    private function validateRule($field, $rule)
    {
        $value = $this->data[$field] ?? null;

        $smallintOpt = array(
            'options' => array(
                'min_range' => 0,
                'max_range' => 32767,
            ),
        );
        $intOpt = array(
            'options' => array(
                'min_range' => 1,
                'max_range' => 2147483647,
            ),
        );
        switch ($rule) {
            case 'required':
                if (empty($value)) {
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
                if (!is_numeric($value) || strlen($value) > 10) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'numeric5':
                if (!is_numeric($value) || strlen($value) > 5) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'date':
                if (!DateTime::createFromFormat('Y-m-d', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                $fecha = date_create($value); // Valida la fecha
                if (!$fecha) { // Si la fecha no es válida
                    $e = date_get_last_errors(); // Obtiene los errores de la fecha
                    foreach ($e['errors'] as $error) { // Recorre los errores
                        throw new ValidationException("Error de Fecha $field: $error", 400); // Lanza una excepción con el error
                    } // Fin recorre errores
                }
                break;
            case 'dateEmpty':
                if ($value && !DateTime::createFromFormat('Y-m-d', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                $fecha = date_create($value); // Valida la fecha
                if (!$fecha) { // Si la fecha no es válida
                    $e = date_get_last_errors(); // Obtiene los errores de la fecha
                    foreach ($e['errors'] as $error) { // Recorre los errores
                        throw new ValidationException("Error de Fecha $field: $error", 400); // Lanza una excepción con el error
                    } // Fin recorre errores
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
                if (strlen($value) > 40) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar10':
                if (strlen($value) > 10) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar20':
                if (strlen($value) > 20) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar1':
                if (strlen($value) > 1) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
            case 'varchar100':
                if (strlen($value) > 100) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varchar200':
                if (strlen($value) > 200) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'varcharMax':
                if (strlen($value) > 2147483647) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;

            case 'allowed012':
                if (!in_array($value, ['0', '1', '2'])) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'allowed01':
                if ($value && !in_array($value, ['0', '1'])) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'arrAllowed012':
                $value = $value ?? [];
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
                $valor = $value ?? [];
                if (!is_array($valor)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                foreach ($valor as $val) {
                    if (!in_array($val, ['1', '2', '3', '4', '5', '6', '7'])) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                    }
                }
                break;
            case 'arrAllowed01':
                $value = $value ?? [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
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
            case 'smallintEmpty':
                if ($value && filter_var($value, FILTER_VALIDATE_INT, $smallintOpt) === false) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule), 400);
                }
                break;
            case 'arrSmallint':
                $value = $value ?? [];
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
                $value = $value ?? [];
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if ($val && filter_var($val, FILTER_VALIDATE_INT, $smallintOpt) === false) {
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
                $value = $value ?? [];
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
        }
    }
}
