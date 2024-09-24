<?php

namespace Classes;

use Classes\ConnectSqlSrv;
use Classes\InputValidator;

class Tools
{
    private $log;
    private $conect;

    public function __construct()
    {
        $this->log = new Log();
        $this->conect = new ConnectSqlSrv();
    }
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
            // función para calcular si la fecha de inicio es mayor a la fecha de fin
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
    // Calcular cantidad de Dias entre las fechas
    public function diasEntreFechas($fechaIni, $fechaFin)
    {
        try {
            // Validar que existan las fechas 
            if (!\DateTime::createFromFormat('Y-m-d', $fechaIni)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }
            if (!\DateTime::createFromFormat('Y-m-d', $fechaFin)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }
            // función para calcular si la fecha de inicio es mayor a la fecha de fin
            if (strtotime($fechaIni) > strtotime($fechaFin)) {
                throw new \Exception('startDate no puede ser mayor endDate', 1);
            }
            $datetime1 = new \DateTime($fechaIni);
            $datetime2 = new \DateTime($fechaFin);
            $interval = $datetime1->diff($datetime2);
            $days = $interval->format('%a');
            return $days;
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
    public function formatDateTime($date, $format = 'Y-m-d H:i:s')
    {
        $date = new \DateTime($date);
        return $date->format($format);
    }
    public function date_time_str($date, $format = 'YmdHis')
    {
        $date = new \DateTime($date);
        return $date->format($format);
    }
    public function agrupar_por($array, $key)
    {
        if (!$array) return [];
        $result = [];
        foreach ($array as $item) {
            $result[$item[$key]][] = $item;
        }
        return $result ?? [];
    }
    public function get_fecha_hora($connDB = '', $tabla)
    {
        try {
            if (!$tabla) throw new \Exception("Tabla en get_fecha_hora no especificada", 400);
            $conn = $this->conect->check_connection($connDB);

            $sql = "SELECT MAX(FechaHora) as FechaHora FROM $tabla";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $data =  $data[0]['FechaHora'] ?? '';
            return $this->date_time_str($data);
        } catch (\Throwable $th) {
            throw new \Exception("Error al obtener FechaHora en {$tabla}", 400);
        }
    }
    public function return_cache($connDB = '', $tabla, $cacheFecha, $cacheName)
    {
        $conn = $this->conect->check_connection($connDB);

        $FHora      = $this->get_fecha_hora($conn, $tabla);
        $FHoraCache = $this->log->get_cache($cacheFecha, '.txt') ?? 0;

        $obj = new \stdClass();
        $obj->data  = [];
        $obj->FHora = $FHora;
        $obj->total = 0;

        if ($FHoraCache < 20240924004403) { // este fragmento se usa para actualizar toda la cache manualmente si es necesario 
            return $obj;
        }

        if ($FHoraCache >= $FHora) { // Si la fecha de cache es mayor o igual a la fecha de la tabla
            $cache = $this->log->get_cache($cacheName); // Obtener cache
            if ($cache) { // Si existe cache
                $obj->data = $cache; // Asignar cache a data
                $obj->total = count($cache); // Asignar total de registros
                return $obj; // Retornar objeto
            } else { // Si no existe cache
                return $obj; // Retornar objeto vacío
            }
        }
        return $obj; // Retornar objeto vacío
    }
    public function validar_datos($datos, $rules, $customValueKey, $nameLog)
    {
        $datosModificados = [];
        try {

            if ($this->jsonNoValido()) {
                $error = 'Json no valido: ' . $this->jsonNoValido();
                throw new \Exception($error, 400);
            }

            if (!is_array($datos)) {
                throw new \Exception("No se recibieron datos", 204);
            }
            if (!is_array($rules)) {
                throw new \Exception("No se recibieron reglas de validación", 204);
            }

            $keyData = array_keys($customValueKey); // Obtengo las claves del array $customValueKey
            $dato = $datos;
            foreach ($keyData as $keyD) { // Recorro las claves del array $customValueKey
                if (!array_key_exists($keyD, $dato) || empty($dato[$keyD])) { // Si no existe la clave en el array $dato o esta vacío
                    $dato[$keyD] = $customValueKey[$keyD]; // Le asigno el valor por defecto del array $customValueKey
                }
            }
            $datosModificados = $dato; // Guardo los datos modificados en un array
            $validator = new InputValidator($dato, $rules); // Instancia la clase InputValidator y le paso los datos y las reglas de validación del array $rules
            $validator->validate(); // Valido los datos
            return $datosModificados;
        } catch (\Exception $e) {
            $code = $e->getCode();
            $this->log->write($e->getMessage(), date('Ymd') . '_' . $nameLog . '.log');
            throw new \Exception($e->getMessage(), $code);
        }
    }
    /**
     * Calcula si el tiempo de descanso es mayor o igual al tiempo total a trabajar.
     *
     * @param string $entrada   Hora de entrada en formato HH:MM
     * @param string $salida    Hora de salida en formato HH:MM
     * @param string $descanso  Tiempo de descanso en formato HH:MM
     * @return bool             Verdadero si el descanso es mayor o igual al tiempo total a trabajar, falso en caso contrario
     */
    public function validar_si_descanso_es_mayor_a_tiempo_trabajado($entrada, $salida, $descanso): bool
    {
        // Convertimos todas las horas a formato de 24 horas y a minutos.
        $entrada_minutos = date('Hi', strtotime($entrada));
        $salida_minutos = date('Hi', strtotime($salida));
        $descanso_minutos = date('Hi', strtotime($descanso));

        // Si la hora de salida es menor que la de entrada, asumimos que es del día siguiente
        if ($salida_minutos < $entrada_minutos) {
            // Calculamos el tiempo hasta las 23:59 del día de entrada
            $tiempo_hasta_medianoche = (2359 - $entrada_minutos) + 1;
            // Calculamos el tiempo desde las 00:00 hasta la hora de salida del día siguiente
            $tiempo_desde_medianoche = $salida_minutos;
            // Sumamos ambos tiempos para obtener el total
            $tiempo_total = $tiempo_hasta_medianoche + $tiempo_desde_medianoche;
        } else {
            $tiempo_total = $salida_minutos - $entrada_minutos;
        }

        // Validamos si el descanso es mayor o igual al tiempo total trabajado
        return $descanso_minutos >= $tiempo_total;
    }
    public function calcularHorasTrabajadas($entrada, $salida, $descanso): string
    {
        // Convertimos las horas a minutos desde la medianoche
        $entradaMinutos = $this->convertirAMinutos($entrada);
        $salidaMinutos = $this->convertirAMinutos($salida);
        $descansoMinutos = $this->convertirAMinutos($descanso);

        $minutosTrabajoTotal = 0;

        if ($salidaMinutos < $entradaMinutos) {
            // La salida es al día siguiente
            $minutosTrabajoTotal = (24 * 60 - $entradaMinutos) + $salidaMinutos;
        } else {
            $minutosTrabajoTotal = $salidaMinutos - $entradaMinutos;
        }

        // Restar el descanso si es distinto de '00:00'
        if ($descanso !== '00:00') {
            $minutosTrabajoTotal -= $descansoMinutos;
        }

        // Asegurarse de que el resultado no sea negativo
        $minutosTrabajoTotal = max(0, $minutosTrabajoTotal);

        // Convertir minutos a formato H:i
        return $this->convertirAHorasMinutos($minutosTrabajoTotal);
    }

    // Función auxiliar para convertir hora en formato "HH:mm" a minutos desde la medianoche
    public function convertirAMinutos($hora)
    {
        list($horas, $minutos) = explode(':', $hora);
        return intval($horas) * 60 + intval($minutos);
    }

    // Función auxiliar para convertir minutos a formato "H:i"
    public function convertirAHorasMinutos($minutos)
    {
        $horas = floor($minutos / 60);
        $minutosRestantes = $minutos % 60;
        return sprintf("%02d:%02d", $horas, $minutosRestantes);
    }
}
