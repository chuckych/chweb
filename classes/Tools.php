<?php

class Tools
{
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
}
