<?php

namespace Classes;

class Tools
{
    public function validarFecha($fecha)
    {
        if ($fecha) {
            $f = explode('-', $fecha);

            if (count($f) != 3) {
                return 'Formato de fecha incorrecto';
            }

            $err = '';
            $y = $f[0];
            $m = ($f[1] > 12 || $f[1] == 0) ? $err .= "Mes ($f[1]) Incorrecto. " : $f[1];
            $d = ($f[2] > 31) ? $err .= "Dia ($f[2]) Incorrecto. " : $f[2];

            if ($err) {
                $err = trim($err);
                return $err;
            }
            $f = "$y-$m-$d";
            return false;
        }
    }
    public function padLeft($str, $length, $pad = ' ')
    {
        if ($str && $length) {
            return str_pad($str, intval($length), $pad, STR_PAD_LEFT);
        } else {
            return false;
        }
    }
    public function dividefecha31dias($startDate, $endDate)
    {
        function splitDates($startDate, $endDate)
        {
            $dateRange = [];
            $currentDate = $startDate;

            while ($currentDate <= $endDate) {
                $nextDate = strtotime('+31 days', $currentDate);
                if ($nextDate > $endDate) {
                    $nextDate = $endDate;
                }
                $dateRange[] = [
                    "FechaMin" => date("Y-m-d", $currentDate),
                    "FechaMax" => date("Y-m-d", $nextDate)
                ];
                $currentDate = strtotime('+1 day', $nextDate);
                if ($currentDate == $endDate) {
                    break;
                }
            }
            return $dateRange;
        }

        try {

            if (!\DateTime::createFromFormat('Y-m-d', $startDate)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }
            if (!\DateTime::createFromFormat('Y-m-d', $endDate)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }
            // funcion para calcular si la fecha de inicio es mayor a la fecha de fin
            if (strtotime($startDate) > strtotime($endDate)) {
                throw new \Exception('startDate no puede ser mayor endDate', 1);
            }

            $startDate = strtotime($startDate);
            $endDate = strtotime($endDate);

            $dateSegments = splitDates($startDate, $endDate);

            return $dateSegments;
        } catch (\Exception $th) {
            return false;
        }
    }
    // Calcular cantidad de dias entre las fechas
    public function diasEntreFechas($fechaini, $fechafin)
    {
        try {
            // Validar que existan las fechas 
            if (!\DateTime::createFromFormat('Y-m-d', $fechaini)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }
            if (!\DateTime::createFromFormat('Y-m-d', $fechafin)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }
            // funcion para calcular si la fecha de inicio es mayor a la fecha de fin
            if (strtotime($fechaini) > strtotime($fechafin)) {
                throw new \Exception('startDate no puede ser mayor endDate', 1);
            }
            $datetime1 = new \DateTime($fechaini);
            $datetime2 = new \DateTime($fechafin);
            $interval = $datetime1->diff($datetime2);
            $dias = $interval->format('%a');
            return $dias;
        } catch (\Exception $th) {
            return false;
        }
    }
    public function jsonNoValido()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return json_last_error_msg();
        } else {
            return false;
        }
    }
    public function filtrarElementoArray($array, $key, $value)
    {
        $result = array_filter($array, function ($item) use ($key, $value) {
            return ($item[$key] === $value);
        });
        return $result;
    }
    public function formatDateTime($date)
    {
        $date = new \DateTime($date);
        return $date->format('Y-m-d H:i:s');
    }
}
