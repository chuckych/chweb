<?php

namespace Classes;

use Classes\Log;
use Classes\Response;
use Classes\Tools;

class RRHHWebService
{
    private string $url;
    private Log $log;
    private Response $resp;
    private Tools $tools;
    private string $NameLog;

    public function __construct()
    {
        $this->log = new Log; // Instancia de la clase Log
        $this->resp = new Response;
        $this->tools = new Tools;
        $this->url = getenv('WEBSERVICE') . '/RRHHWebService';
        $this->NameLog = date('Ymd') . '_RRHHWebService.log';
    }
    public function baseUrl()
    {
        if (!getenv('WEBSERVICE')) { // Si no hay url del WebService
            $text = "No se estableció el baseUrl del webservice"; // set error message
            $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
            throw new \Exception($text, 1);
        }
        return $this->url;
    }

    // Envío: 
    // POST http://xxx.xxx.xxx.xxx:6400/RRHHWebService/Procesar HTTP/1.1 
    // Content-Type: text/html 
    // {Usuario=usuario, Legajos=[], TipoDePersonal=0, LegajoDesde=1, LegajoHasta=99999999, FechaDesde=01/01/2020, FechaHasta=01/01/2020, Empresa=0, Planta=0, Sucursal=0, Grupo=0, Sector=0, Seccion=0} 

    // Respuesta: 
    // HTTPCode: 201 OK 
    // {ProcesoId=Identificador de Proceso} 

    // HTTPCode: 404 Not Found 
    // Descripción del Error 

    // Parámetros Obligatorios: 
    // Usuario=usuario Legajos=[] o TipoDePersonal=0,LegajoDesde=1, LegajoHasta=99999999 
    // FechaDesde=01/01/2020 
    // FechaHasta=01/01/2020 
    // Parámetros Opcionales: Empresa=0 Planta=0 Sucursal=0 Grupo=0 Sector=0 Seccion=0 

    /** 
     * Procesa los legajos a partir de un arreglo de legajos
     * @param array $Legajos Arreglo de legajos
     * @param string $FechaDesde Fecha desde
     * @param string $FechaHasta Fecha hasta
     * @return  bool Respuesta del WebService
     * @example $Legajos = [1,2,3,4,5,6,7,8,9,10];
     * @example $FechaDesde = '2023-08-23';
     * @example $FechaHasta = '2023-08-26';
     */
    public function procesar_legajos(string $FechaDesde, string $FechaHasta, array $Legajos = []): bool
    {
        try {

            $this->ping();

            if (!\is_array($Legajos) || empty($FechaDesde) || empty($FechaHasta)) { // Valida los parametros
                throw new \Exception('Parametros no validos', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaDesde)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaHasta)) { // Valida la fecha hasta
                throw new \Exception('Fecha hasta no es valida', 1);
            }

            $ruta = $this->baseUrl() . '/' . "Procesar"; // Ruta del WebService

            $dateSegments = $this->tools->dividefecha31dias($FechaDesde, $FechaHasta); // Divide las fechas en segmentos de 31 Dias

            // Dividir legajos en bloques de 20 legajos
            $lengthChunk = 20; // Tamaño del chunk para legajos
            $LegajosSegment = array_chunk($Legajos, $lengthChunk); // Dividir el arreglo en bloques de 20 legajos

            // $Legajos = (is_array($Legajos)) ? implode(';', $Legajos) : '';

            if ($dateSegments) { // Si hay segmentos de fechas procesa los legajos en cada segmento
                $ch = curl_init();

                foreach ($LegajosSegment as $Legas) { // Recorre los bloques de legajos
                    $countLegas = \count($Legas); // Cuenta la cantidad de legajos en el bloque
                    $Legajo = ($countLegas === 1) ? $Legas[0] : '';

                    $Legas = (\is_array($Legas)) ? implode(';', $Legas) : '';

                    foreach ($dateSegments as $segment) { // Recorre los segmentos

                        $FechaDesde = date('d/m/Y', strtotime($segment['FechaMin']));
                        $FechaHasta = date('d/m/Y', strtotime($segment['FechaMax']));

                        if (strtotime($segment['FechaMin']) > time()) { // Si la fecha minima es mayor a la fecha actual
                            break; // Salir del bucle
                        }
                        if (strtotime($segment['FechaMax']) > time()) { // Si la fecha maxima es mayor a la fecha actual
                            $FechaHasta = date('d/m/Y'); // La fecha maxima es la fecha actual
                        }

                        $post_data = "{Usuario=Supervisor, Legajos=[{$Legas}],FechaDesde='{$FechaDesde}',FechaHasta='{$FechaHasta}'}"; // Parametros del WebService
                        if ($countLegas === 1) {
                            $post_data = "{Usuario=Supervisor, Legajos=[], LegajoDesde='$Legajo',LegajoHasta='$Legajo',FechaDesde='{$FechaDesde}',FechaHasta='{$FechaHasta}'}"; // Parametros del WebService
                        }

                        curl_setopt($ch, CURLOPT_URL, $ruta);
                        curl_setopt($ch, CURLOPT_POST, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500); // Establecer el tiempo de espera en milisegundos
                        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // Tiempo de espera para la conexión
                        $respuesta = curl_exec($ch);
                        $curl_errno = curl_errno($ch);
                        // $curl_error = curl_error($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        if ($curl_errno > 0) {
                            $text = "{$curl_errno} : Error al procesar."; // set error 
                            throw new \Exception($text, $httpCode);
                        }
                        if ($httpCode == 404) {
                            $text = "{$respuesta} : Error al procesar."; // set error 
                            throw new \Exception($text, $httpCode);
                        }
                        $days = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']);
                        $text = "Legajos procesados [{$Legas}] - {$FechaDesde} a {$FechaHasta} {$days} días";
                        $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
                    }
                }

                if (PHP_VERSION_ID >= 80000) {
                    unset($ch);
                } else {
                    curl_close($ch);
                } // close curl handle
                return true;
            }
            return false;
        } catch (\Exception $e) {
            $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            return false;
        }
    }

    /**
     * Procesa legajos o filtros generales en un único parámetro.
     *
     * Reglas:
     * - Si Legajos tiene datos, procesa por bloques de 50 e ignora filtros de Empresa/Planta/etc.
     * - Si Legajos está vacío, procesa directamente por filtros + TipoDePersonal.
     *
     * @param array $params
     * @return bool
     */
    public function procesar(array $params = []): bool
    {
        try {
            $this->ping();

            $FechaDesde = $params['FechaDesde'] ?? '';
            $FechaHasta = $params['FechaHasta'] ?? '';
            $Legajos = $params['Legajos'] ?? [];

            if (!\is_array($Legajos) || empty($FechaDesde) || empty($FechaHasta)) {
                throw new \Exception('Parametros no validos', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaDesde)) {
                throw new \Exception('Fecha desde no es valida', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaHasta)) {
                throw new \Exception('Fecha hasta no es valida', 1);
            }

            $ruta = $this->baseUrl() . '/' . 'Procesar';
            $dateSegments = $this->tools->dividefecha31dias($FechaDesde, $FechaHasta);

            if (!$dateSegments) {
                return false;
            }

            $normalizarFiltro = static function ($value): string {
                if ($value === null) {
                    return '0';
                }

                if (\is_string($value) && trim($value) === '') {
                    return '0';
                }

                return (string) $value;
            };

            $filtros = [
                'Empresa' => $normalizarFiltro($params['Empresa'] ?? null),
                'Planta' => $normalizarFiltro($params['Planta'] ?? null),
                'Sucursal' => $normalizarFiltro($params['Sucursal'] ?? null),
                'Grupo' => $normalizarFiltro($params['Grupo'] ?? null),
                'Sector' => $normalizarFiltro($params['Sector'] ?? null),
                'Seccion' => $normalizarFiltro($params['Seccion'] ?? null),
                'TipoDePersonal' => $normalizarFiltro($params['TipoDePersonal'] ?? null),
            ];
            $lengthChunk = 20; // Tamaño del chunk para legajos
            $LegajosSegment = !empty($Legajos) ? array_chunk($Legajos, $lengthChunk) : [];
            $ch = curl_init();

            if (!empty($LegajosSegment)) {
                foreach ($LegajosSegment as $Legas) {
                    \usleep(100000); // Pausa de 0.1 segundos para evitar saturar el WebService
                    $countLegas = \count($Legas);
                    $Legajo = ($countLegas === 1) ? $Legas[0] : '';
                    $Legas = (\is_array($Legas)) ? implode(';', $Legas) : '';

                    foreach ($dateSegments as $segment) {
                        $FechaDesdeSegmento = date('d/m/Y', strtotime($segment['FechaMin']));
                        $FechaHastaSegmento = date('d/m/Y', strtotime($segment['FechaMax']));

                        if (strtotime($segment['FechaMin']) > time()) {
                            break;
                        }

                        if (strtotime($segment['FechaMax']) > time()) {
                            $FechaHastaSegmento = date('d/m/Y');
                        }

                        $post_data = "{Usuario=Supervisor, Legajos=[{$Legas}],FechaDesde='{$FechaDesdeSegmento}',FechaHasta='{$FechaHastaSegmento}'}";

                        if ($countLegas === 1) {
                            $post_data = "{Usuario=Supervisor, Legajos=[], LegajoDesde='$Legajo',LegajoHasta='$Legajo',FechaDesde='{$FechaDesdeSegmento}',FechaHasta='{$FechaHastaSegmento}'}";
                        }

                        curl_setopt($ch, CURLOPT_URL, $ruta);
                        curl_setopt($ch, CURLOPT_POST, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        $respuesta = curl_exec($ch);
                        $curl_errno = curl_errno($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        if ($curl_errno > 0) {
                            throw new \Exception("{$curl_errno} : Error al procesar.", $httpCode);
                        }

                        if ($httpCode == 404) {
                            throw new \Exception("{$respuesta} : Error al procesar.", $httpCode);
                        }

                        $days = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']);
                        $text = "Legajos procesados [{$Legas}] - {$FechaDesdeSegmento} a {$FechaHastaSegmento} {$days} días";
                        $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
                    }
                }
            } else {
                foreach ($dateSegments as $segment) {
                    $FechaDesdeSegmento = date('d/m/Y', strtotime($segment['FechaMin']));
                    $FechaHastaSegmento = date('d/m/Y', strtotime($segment['FechaMax']));

                    if (strtotime($segment['FechaMin']) > time()) {
                        break;
                    }

                    if (strtotime($segment['FechaMax']) > time()) {
                        $FechaHastaSegmento = date('d/m/Y');
                    }

                    $post_data = "{Usuario=Supervisor, Legajos=[], TipoDePersonal={$filtros['TipoDePersonal']}, LegajoDesde=1, LegajoHasta=99999999, FechaDesde={$FechaDesdeSegmento}, FechaHasta={$FechaHastaSegmento}, Empresa={$filtros['Empresa']}, Planta={$filtros['Planta']}, Sucursal={$filtros['Sucursal']}, Grupo={$filtros['Grupo']}, Sector={$filtros['Sector']}, Seccion={$filtros['Seccion']}}";

                    // error_log(json_encode($post_data)).exit;

                    curl_setopt($ch, CURLOPT_URL, $ruta);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $respuesta = curl_exec($ch);
                    $curl_errno = curl_errno($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($curl_errno > 0) {
                        throw new \Exception("{$curl_errno} : Error al procesar.", $httpCode);
                    }

                    if ($httpCode == 404) {
                        throw new \Exception("{$respuesta} : Error al procesar.", $httpCode);
                    }

                    $days = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']) - 1;
                    $text = "Procesamiento por filtros - {$post_data} - {$FechaDesdeSegmento} a {$FechaHastaSegmento} {$days} días";
                    $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
                }
            }

            if (PHP_VERSION_ID >= 80000) {
                unset($ch);
            } else {
                curl_close($ch);
            }

            return true;
        } catch (\Exception $e) {
            $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            return false;
        }
    }

    /**
     * Procesa legajos o filtros generales en un único parámetro.
     *
     * Reglas:
     * - Si Legajos tiene datos, procesa por bloques de 50 e ignora filtros de Empresa/Planta/etc.
     * - Si Legajos está vacío, procesa directamente por filtros + TipoDePersonal.
     *
     * @param array $params
     * @return bool
     */
    public function fichar_horario(array $params = []): bool
    {
        try {
            $this->ping();

            $FechaDesde = $params['FechaDesde'] ?? '';
            $FechaHasta = $params['FechaHasta'] ?? '';
            $Legajos = $params['Legajos'] ?? [];

            if (!\is_array($Legajos) || empty($FechaDesde) || empty($FechaHasta)) {
                throw new \Exception('Parametros no validos', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaDesde)) {
                throw new \Exception('Fecha desde no es valida', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaHasta)) {
                throw new \Exception('Fecha hasta no es valida', 1);
            }

            $ruta = $this->baseUrl() . '/' . 'FicharHorario';
            $dateSegments = $this->tools->dividefecha31dias($FechaDesde, $FechaHasta);

            if (!$dateSegments) {
                return false;
            }

            $normalizarFiltro = static function ($value): string {
                if ($value === null) {
                    return '0';
                }

                if (\is_string($value) && trim($value) === '') {
                    return '0';
                }

                return (string) $value;
            };

            $TipoDeFichada = $normalizarFiltro($params['TipoDeFichada'] ?? null);
            $Laboral = $normalizarFiltro($params['Laboral'] ?? null);

            $filtros = [
                'Empresa' => $normalizarFiltro($params['Empresa'] ?? null),
                'Planta' => $normalizarFiltro($params['Planta'] ?? null),
                'Sucursal' => $normalizarFiltro($params['Sucursal'] ?? null),
                'Grupo' => $normalizarFiltro($params['Grupo'] ?? null),
                'Sector' => $normalizarFiltro($params['Sector'] ?? null),
                'Seccion' => $normalizarFiltro($params['Seccion'] ?? null),
                'TipoDePersonal' => $normalizarFiltro($params['TipoDePersonal'] ?? null),
            ];

            $lengthChunk = 20; // Tamaño del chunk para legajos
            $LegajosSegment = !empty($Legajos) ? array_chunk($Legajos, $lengthChunk) : [];
            $ch = curl_init();

            if (!empty($LegajosSegment)) {
                foreach ($LegajosSegment as $Legas) {
                    $countLegas = \count($Legas);
                    $Legajo = ($countLegas === 1) ? $Legas[0] : '';
                    $Legas = (\is_array($Legas)) ? implode(';', $Legas) : '';

                    foreach ($dateSegments as $segment) {
                        $FechaDesdeSegmento = date('d/m/Y', strtotime($segment['FechaMin']));
                        $FechaHastaSegmento = date('d/m/Y', strtotime($segment['FechaMax']));

                        if (strtotime($segment['FechaMin']) > time()) {
                            break;
                        }

                        if (strtotime($segment['FechaMax']) > time()) {
                            $FechaHastaSegmento = date('d/m/Y');
                        }

                        $post_data = "{Usuario=Supervisor, Legajos=[{$Legas}],FechaDesde='{$FechaDesdeSegmento}',FechaHasta='{$FechaHastaSegmento}', TipoDeFichada={$TipoDeFichada}, Laboral={$Laboral}}";

                        if ($countLegas === 1) {
                            $post_data = "{Usuario=Supervisor, Legajos=[], LegajoDesde='$Legajo',LegajoHasta='$Legajo',FechaDesde='{$FechaDesdeSegmento}',FechaHasta='{$FechaHastaSegmento}', TipoDeFichada={$TipoDeFichada}, Laboral={$Laboral}}";
                        }

                        curl_setopt($ch, CURLOPT_URL, $ruta);
                        curl_setopt($ch, CURLOPT_POST, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        $respuesta = curl_exec($ch);
                        $curl_errno = curl_errno($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        if ($curl_errno > 0) {
                            throw new \Exception("{$curl_errno} : Error al procesar.", $httpCode);
                        }

                        if ($httpCode == 404) {
                            throw new \Exception("{$respuesta} : Error al procesar.", $httpCode);
                        }

                        $days = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']);
                        $text = "Legajos procesados [{$Legas}] - {$FechaDesdeSegmento} a {$FechaHastaSegmento} {$days} días";
                        $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
                    }
                }
            } else {
                foreach ($dateSegments as $segment) {
                    $FechaDesdeSegmento = date('d/m/Y', strtotime($segment['FechaMin']));
                    $FechaHastaSegmento = date('d/m/Y', strtotime($segment['FechaMax']));

                    if (strtotime($segment['FechaMin']) > time()) {
                        break;
                    }

                    if (strtotime($segment['FechaMax']) > time()) {
                        $FechaHastaSegmento = date('d/m/Y');
                    }

                    $post_data = "{Usuario=Supervisor, Legajos=[], TipoDePersonal={$filtros['TipoDePersonal']}, LegajoDesde=1, LegajoHasta=99999999, FechaDesde={$FechaDesdeSegmento}, FechaHasta={$FechaHastaSegmento}, Empresa={$filtros['Empresa']}, Planta={$filtros['Planta']}, Sucursal={$filtros['Sucursal']}, Grupo={$filtros['Grupo']}, Sector={$filtros['Sector']}, Seccion={$filtros['Seccion']}, TipoDeFichada={$TipoDeFichada}, Laboral={$Laboral}}";

                    // error_log(json_encode($post_data)).exit;

                    curl_setopt($ch, CURLOPT_URL, $ruta);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $respuesta = curl_exec($ch);
                    $curl_errno = curl_errno($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($curl_errno > 0) {
                        throw new \Exception("{$curl_errno} : Error al procesar.", $httpCode);
                    }

                    if ($httpCode == 404) {
                        throw new \Exception("{$respuesta} : Error al procesar.", $httpCode);
                    }

                    $days = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']) - 1;
                    $text = "Procesamiento por filtros - {$post_data} - {$FechaDesdeSegmento} a {$FechaHastaSegmento} {$days} días";
                    $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
                }
            }

            if (PHP_VERSION_ID >= 80000) {
                unset($ch);
            } else {
                curl_close($ch);
            }

            return true;
        } catch (\Exception $e) {
            $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ', $this->NameLog, $e);
            return false;
        }
    }

    /**
     * Summary of ping
     * @throws \Exception
     * @return bool
     */
    private function ping()
    {
        $ch = curl_init(); // Inicializar el objeto curl
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl() . '/Ping?'); // Establecer la URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor true= si, false= no
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500);
        // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1); // Establecer el tiempo de espera en milisegundos
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // The number of seconds to wait while trying to connect
        // Especificar cabeceras
        $headers = [
            'Connection: keep-alive',
            'Accept: */*',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Especificar método
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_exec($ch); // extract information from response

        $curl_errno = curl_errno($ch); // get error code
        $curl_error = curl_error($ch); // get error information

        if ($curl_errno > 0) { // si hay error
            $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
            throw new \Exception($text, 1);
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
        if (PHP_VERSION_ID >= 80000) {
            unset($ch);
        } else {
            curl_close($ch);
        } // close curl handle

        if ($http_code != '201') {
            throw new \Exception("Error Ping WebService. \"Cod: $curl_errno: $curl_error\"", 1);
        }
        // $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': Ping Correcto', $this->NameLog);
        return true;
    }
    /** 
     * Proyectar Horas a partir de un arreglo de legajos
     * @param array $Legajos Arreglo de legajos
     * @param string $FechaDesde Fecha desde
     * @param string $FechaHasta Fecha hasta
     * @return string Respuesta del WebService
     * @example $Legajos = [1,2,3,4,5,6,7,8,9,10];
     * @example $FechaDesde = '2023-08-23';
     * @example $FechaHasta = '2023-08-26';
     */
    public function proyectar_horas(string $FechaDesde, string $FechaHasta, array $Legajos = [])
    {
        try {

            $this->ping();

            if (!\is_array($Legajos) || empty($FechaDesde) || empty($FechaHasta)) { // Valida los parametros
                throw new \Exception('Parametros no validos', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaDesde)) { // Valida la fecha desde 
                throw new \Exception('Fecha desde no es valida', 1);
            }

            if (!\DateTime::createFromFormat('Y-m-d', $FechaHasta)) { // Valida la fecha hasta
                throw new \Exception('Fecha hasta no es valida', 1);
            }

            $ruta = $this->baseUrl() . '/' . "Proyectar"; // Ruta del WebService

            $dateSegments = $this->tools->dividefecha31dias($FechaDesde, $FechaHasta); // Divide las fechas en segmentos de 31 Dias

            // Dividir legajos en bloques de 20 legajos
            $lengthChunk = 20; // Tamaño del chunk para legajos
            $LegajosSegment = array_chunk($Legajos, $lengthChunk);

            // $Legajos = (is_array($Legajos)) ? implode(';', $Legajos) : '';

            if ($dateSegments) { // Si hay segmentos de fechas procesa los legajos en cada segmento

                $ch = curl_init();

                foreach ($LegajosSegment as $Legas) { // Recorre los bloques de legas

                    $Legas = (is_array($Legas)) ? implode(';', $Legas) : '';

                    foreach ($dateSegments as $segment) { // Recorre los segmentos

                        $FechaDesde = date('d/m/Y', strtotime($segment['FechaMin']));
                        $FechaHasta = date('d/m/Y', strtotime($segment['FechaMax']));

                        if (strtotime($segment['FechaMin']) <= time()) { // Si la fecha minima en menosr o o igual a la fecha actual.
                            throw new \Exception('Fecha desde no puede ser menor o igual a la fecha actual', 1);
                        }
                        if (strtotime($segment['FechaMax']) <= time()) { // Si la fecha maxima es mayor a la fecha actual
                            throw new \Exception('Fecha hasta no puede ser menor o igual a la fecha actual', 1);
                        }

                        $post_data = "{Usuario=Supervisor, Legajos=[{$Legas}],FechaDesde='{$FechaDesde}',FechaHasta='{$FechaHasta}'}"; // Parametros del WebService

                        curl_setopt($ch, CURLOPT_URL, $ruta);
                        curl_setopt($ch, CURLOPT_POST, TRUE);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500); // Establecer el tiempo de espera en milisegundos
                        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // Tiempo de espera para la conexión
                        $respuesta = curl_exec($ch);
                        $curl_errno = curl_errno($ch);
                        // $curl_error = curl_error($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        if ($curl_errno > 0) {
                            $text = "{$curl_errno} : Error al procesar."; // set error 
                            throw new \Exception($text, $httpCode);
                        }
                        if ($httpCode == 404) {
                            $text = "{$respuesta} : Error al procesar."; // set error 
                            throw new \Exception($text, $httpCode);
                        }
                        $days = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']);
                        $text = "Legajos procesados [{$Legas}] - {$FechaDesde} a {$FechaHasta} {$days} días";
                        $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
                    }
                }

                if (PHP_VERSION_ID >= 80000) {
                    unset($ch);
                } else {
                    curl_close($ch);
                } // close curl handle
                return true;
            }
            return false;
        } catch (\Exception $e) {
            $this->log->trace('RRHHWebService::' . __FUNCTION__, $this->NameLog, $e);
            return $e;
        }
    }

    /** 
     * Ingresa Novedades a partir de un arreglo de legajos
     * @param array $Legajos Arreglo de legajos
     * @param string $FechaDesde Fecha desde
     * @param string $FechaHasta Fecha hasta
     * @param string $CodNovedad Código de la novedad
     * @return string Respuesta del WebService
     * @example $Legajos = [1,2,3,4,5,6,7,8,9,10];
     * @example $FechaDesde = '2023-08-23';
     * @example $FechaHasta = '2023-08-26';
     */
    public function ingresar_novedades(string $FechaDesde, string $FechaHasta, $CodNovedad, $HorasNovedad = '00:00', $Laboral = '0', $Justifica = '0', $Observacion = '', $Causa = '0', $Empresa = '0', $Planta = '0', $Sector = '0', $Seccion = '0', $Grupo = '0', $Sucursal = '0', array $Legajos = [])
    {
        try {
            // Validaciones
            $this->validarConexionWebService();
            $this->validarParametrosNovedades($Legajos, $FechaDesde, $FechaHasta, $CodNovedad, $HorasNovedad);

            $ruta = $this->baseUrl() . '/Novedades';
            $dateSegments = $this->tools->dividefecha31dias($FechaDesde, $FechaHasta);

            if (!$dateSegments) {
                return false;
            }

            $lengthChunk = 20; // Tamaño del chunk para legajos
            $LegajosSegment = empty($Legajos) ? [[]] : array_chunk($Legajos, $lengthChunk);

            $parametrosBase = compact('CodNovedad', 'HorasNovedad', 'Laboral', 'Justifica', 'Observacion', 'Causa', 'Empresa', 'Planta', 'Sector', 'Seccion', 'Grupo', 'Sucursal');

            $this->procesarNovedadesPorSegmentos($ruta, $LegajosSegment, $dateSegments, $parametrosBase);

            return true;
        } catch (\Exception $e) {
            $this->log->trace('RRHHWebService::' . __FUNCTION__, $this->NameLog, $e);
            throw new \Exception($e->getMessage(), 400);
        }
    }

    /**
     * Valida la conexión con el WebService
     */
    private function validarConexionWebService()
    {
        $this->ping();
    }

    /**
     * Valida los parámetros de novedades
     */
    private function validarParametrosNovedades($Legajos, $FechaDesde, $FechaHasta, $CodNovedad, $HorasNovedad)
    {
        if (!\is_array($Legajos) || empty($FechaDesde) || empty($FechaHasta)) {
            throw new \Exception('Parametros no validos', 1);
        }

        if (!\DateTime::createFromFormat('Y-m-d', $FechaDesde)) {
            throw new \Exception('Fecha desde no es valida', 1);
        }

        if (!\DateTime::createFromFormat('Y-m-d', $FechaHasta)) {
            throw new \Exception('Fecha hasta no es valida', 1);
        }

        if (\strtotime($FechaDesde) > \strtotime($FechaHasta)) {
            throw new \Exception('Fecha desde no puede ser mayor a fecha hasta', 1);
        }

        if (!preg_match('/^\d{2}:\d{2}$/', $HorasNovedad)) {
            throw new \Exception('Horas Novedad no tiene formato HH:MM', 1);
        }

        if (empty($CodNovedad) || !\is_numeric($CodNovedad)) {
            throw new \Exception('Codigo de Novedad no puede estar vacio y debe ser numerico', 1);
        }
    }

    /**
     * Procesa novedades por segmentos de fechas y legajos
     */
    private function procesarNovedadesPorSegmentos(string $ruta, array $LegajosSegment, array $dateSegments, array $parametrosBase)
    {
        $ch = curl_init();

        foreach ($LegajosSegment as $Legajos) {
            $esSegmentoVacio = empty($Legajos);
            $countLegajos = 0;
            $Legajo = '';

            if (!$esSegmentoVacio) {
                $countLegajos = \count($Legajos);
                $Legajo = ($countLegajos === 1) ? $Legajos[0] : '';
                $Legajos = implode(';', $Legajos);
            }

            foreach ($dateSegments as $segment) {
                $FechaDesde = date('d/m/Y', \strtotime($segment['FechaMin']));
                $FechaHasta = date('d/m/Y', \strtotime($segment['FechaMax']));

                // Construir parámetros
                $params = $this->construirParametrosNovedad(
                    $parametrosBase,
                    $FechaDesde,
                    $FechaHasta,
                    $esSegmentoVacio ? null : ($countLegajos === 1 ? $Legajo : $Legajos),
                    $esSegmentoVacio
                );

                $post_data = $this->convertirParametrosAString($params);

                // Ejecutar request
                $this->ejecutarRequestNovedad($ch, $ruta, $post_data);

                // Log
                $days = $this->tools->diasEntreFechas($segment['FechaMin'], $segment['FechaMax']);
                $legajosInfo = $esSegmentoVacio ? 'Todos los legajos' : "[$Legajos]";
                $text = "Legajos procesados {$legajosInfo} - {$FechaDesde} a {$FechaHasta} {$days} días";
                $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $text, $this->NameLog);
                // $this->log->trace('RRHHWebService::' . __FUNCTION__ . ': ' . $post_data, $this->NameLog);
            }
        }

        if (PHP_VERSION_ID >= 80000) {
            unset($ch);
        } else {
            curl_close($ch);
        } // close curl handle
    }

    /**
     * Construye los parámetros para el request de novedad
     */
    private function construirParametrosNovedad($parametrosBase, $FechaDesde, $FechaHasta, $legajos, $esTodosLosLegajos = false)
    {
        $params = [
            'Usuario' => 'Supervisor',
            'Novedad' => $parametrosBase['CodNovedad'],
            'Horas' => $parametrosBase['HorasNovedad'],
            'Laboral' => $parametrosBase['Laboral'],
            'Justifica' => $parametrosBase['Justifica'],
            'FechaDesde' => $FechaDesde,
            'FechaHasta' => $FechaHasta,
            'Observacion' => $parametrosBase['Observacion'],
            'Causa' => $parametrosBase['Causa'],
            'Empresa' => $parametrosBase['Empresa'],
            'Planta' => $parametrosBase['Planta'],
            'Sector' => $parametrosBase['Sector'],
            'Seccion' => $parametrosBase['Seccion'],
            'Grupo' => $parametrosBase['Grupo'],
            'Sucursal' => $parametrosBase['Sucursal']
        ];

        // Configurar legajos según el caso
        if ($esTodosLosLegajos) {
            $params['Legajos'] = '[]';
            $params['LegajoDesde'] = 1;
            $params['LegajoHasta'] = 99999999;
        } elseif (is_string($legajos) && strpos($legajos, ';') === false) {
            // Un solo legajo
            $params['Legajos'] = '[]';
            $params['LegajoDesde'] = $legajos;
            $params['LegajoHasta'] = $legajos;
        } else {
            // Múltiples legajos
            $params['Legajos'] = "[{$legajos}]";
        }

        return $params;
    }

    /**
     * Convierte array de parámetros a string formato WebService
     */
    private function convertirParametrosAString($params)
    {
        return '{' . implode(', ', array_map(
            fn($k, $v) => "$k='$v'",
            array_keys($params),
            array_values($params)
        )) . '}';
    }

    /**
     * Ejecuta el request cURL a la API de novedades
     */
    private function ejecutarRequestNovedad($ch, $ruta, $post_data)
    {
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $respuesta = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($curl_errno > 0) {
            throw new \Exception("{$curl_errno} : Error al ingresar novedades.", $httpCode);
        }

        if ($httpCode == 404) {
            throw new \Exception("{$respuesta} : Error al ingresar novedades.", $httpCode);
        }
    }
}