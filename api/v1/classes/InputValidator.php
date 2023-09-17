<?php

namespace Classes;

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
            'required'      => "El campo $field es requerido",
            'email'         => "El campo $field debe ser una dirección de correo válida",
            'numeric'       => "El campo $field debe ser un número",
            'numeric10'     => "El campo $field debe ser un número y menor a 10 dígitos",
            'numeric5'      => "El campo $field debe ser un número y menor a 5 dígitos",
            'date'          => "El campo $field debe ser una fecha válida con formato yyyy-mm-dd",
            'time'          => "El campo $field debe ser una hora válida con formato hh:mm",
            'varchar40'     => "El campo $field debe tener una longitud menor a 40 caracteres",
            'allowed01'     => "El campo $field debe tener un valor permitido. [0, 1]",
            'allowed012'    => "El campo $field debe tener un valor permitido. [0, 1, 2]",
            'arrAllowed012' => "El campo $field debe ser un arreglo con valores permitidos. [0, 1, 2]",
            'arrAllowed01'  => "El campo $field debe ser un arreglo con valores permitidos. [0, 1]",
            'smallint'      => "El campo $field debe ser un número entero y menor a 32767",
            'arrSmallint'   => "El campo $field debe ser un arreglo de números enteros y menor a 32767",
            'int'           => "El campo $field debe ser un número entero y menor a 2147483647",
            'intempty'      => "El campo $field debe ser un número entero",
            'arrInt'        => "El campo $field debe ser un arreglo de números enteros",
            'decima12.2'    => "El campo $field debe ser un número decimal con 2 decimales y menor a 12 dígitos",
            'datetime'      => "El campo $field debe ser una fecha y hora válida con formato yyyy-mm-dd hh:mm:ss",
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
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'numeric':
                if (!is_numeric($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'numeric10':
                if (!is_numeric($value) || strlen($value) > 10) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'numeric5':
                if (!is_numeric($value) || strlen($value) > 5) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'date':
                if (!\DateTime::createFromFormat('Y-m-d', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'time':
                if (!preg_match('/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'varchar40':
                if (strlen($value) > 40) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'allowed012':
                if (!in_array($value, ['0', '1', '2'])) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'allowed01':
                if ($value && !in_array($value, ['0', '1'])) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'arrAllowed012':
                $value = $value ?? [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                foreach ($value as $val) {
                    if (!in_array($val, ['0', '1', '2'])) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule));
                    }
                }
                break;
            case 'arrAllowed01':
                $value = $value ?? [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                foreach ($value as $val) {
                    if (!in_array($val, ['0', '1'])) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule));
                    }
                }
                break;
            case 'smallint':
                if (filter_var($value, FILTER_VALIDATE_INT, $smallintOpt) === false) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'arrSmallint':
                $value = $value ?? [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                foreach ($value as $val) {
                    if (filter_var($val, FILTER_VALIDATE_INT, $smallintOpt) === false) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule));
                    }
                }
                break;
            case 'int':
                if (filter_var($value, FILTER_VALIDATE_INT, $intOpt) === false) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'intempty':
                if ($value) {
                    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule));
                    }
                }
                break;
            case 'arrInt':
                $value = $value ?? [];
                if (!is_array($value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                foreach ($value as $val) {
                    if (filter_var($val, FILTER_VALIDATE_INT) === false) {
                        throw new ValidationException($this->generateErrorMessage($field, $rule));
                    }
                }
                break;
            case 'decima12.2':
                if (!preg_match('/^[0-9]{1,10}(\.[0-9]{1,2})?$/', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
            case 'datetime':
                if (!\DateTime::createFromFormat('Y-m-d H:i:s', $value)) {
                    throw new ValidationException($this->generateErrorMessage($field, $rule));
                }
                break;
        }
    }
}
