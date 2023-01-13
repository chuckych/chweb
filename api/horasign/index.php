<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();
$request = Flight::request();
// Flight::json($dataC['WebServiceCH'].'/RRHHWebService', 200) . exit;
$Horario = '';
if ($request->method != 'POST') {
    http_response_code(400);
    (response(array(), 0, 'Invalid Request Method: ' . $request->method, 400, $time_start, 0, $idCompany));
    exit;
}

$stmtHorarios = $dbApiQuery("SELECT HorCodi, HorDesc, HorID FROM HORARIOS") ?? '';

function pingWebService($textError, $webService) // Funcion para validar que el Webservice de Control Horario esta disponible
{
    $url = rutaWebService($webService, "Ping?");
    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // The number of seconds to wait while trying to connect
    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    $response   = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
        writeLog($text, __DIR__ . "../../logs/" . date('Ymd') . "_errorWebService.log", '');
        http_response_code(400);
        (response(array(), 0, "Error Interno. WS", 400, 0, 0, 0));
        exit;
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
    //return curl_getinfo($ch, CURLINFO_HTTP_CODE); // retornar el codigo de respuesta
    curl_close($ch); // close curl handle
    return ($http_code == 201) ? true : (response(array(), 0, $textError, 400, 0, 0, 0)) . exit; // escribir en el log
}
pingWebService('Error Interno WS', $dataC['WebServiceCH'] . '/RRHHWebService');

function rutaWebService($webService, $Comando)
{
    return $webService . "/" . $Comando;
}
/** PARA EL WEBSERVICE CH*/
function respuestaWebService($respuesta)
{
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    return ($respuesta[0]);
}
function EstadoProceso($url)
{
    do {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        curl_close($ch);
    } while (respuestaWebService($respuesta) == 'Pendiente');
    return respuestaWebService($respuesta);
}

function getHorario($FechaDesde, $FechaHasta, $Legajos, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion, $webService)
{
    $time_start = microtime(true);
    $Legajos = implode(';', $Legajos);
    $FechaDesde = fecha($FechaDesde, 'd/m/Y');
    $FechaHasta = fecha($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService($webService, "Horarios");
    $post_data = "{Usuario=SUPERVISOR,Legajos=[$Legajos],TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion}";
    // print_r($post_data).exit;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $text = "Error al obtener Horario. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
    if ($curl_errno > 0) {
        writeLog($text, __DIR__ . "../../logs/" . date('Ymd') . "_errorGetHorario.log", '');
        http_response_code(400);
        (response($text, '0', 'Error', 400, $time_start, 0, ''));
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        writeLog($text, __DIR__ . '../../logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        $data = array('status' => 'error', 'dato' => $respuesta);
        http_response_code(400);
        (response($respuesta, '0', 'Error', 400, $time_start, 0, ''));
        // echo json_encode($data);
        exit;
    }
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    $processID = ($respuesta[1]);
    $url = rutaWebService($webService, "Estado?ProcesoId=" . $processID);
    if ($httpCode == 201) {
        return array('ProcesoId' => $processID, 'Estado' => EstadoProceso($url));
        // exit;
    } else {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
    }
}

$wc = '';

$dp = $request->data;

$start  = start();
$length = length();

$dp->FechaDesde     = ($dp->FechaDesde) ?? [];
$dp->FechaDesde     = vp($dp->FechaDesde, 'FechaDesde', 'str', 10);

$dp->FechaHasta     = ($dp->FechaHasta) ?? [];
$dp->FechaHasta     = vp($dp->FechaHasta, 'FechaHasta', 'str', 10);

$dp->LegajoDesde    = ($dp->LegajoDesde) ?? [];
$dp->LegajoDesde    = vp($dp->LegajoDesde, 'LegajoDesde', 'str', 9);

$dp->Legajos = ($dp->Legajos) ?? [];
$dp->Legajos = vp($dp->Legajos, 'Legajos', 'strArray', 9);

$dp->LegajoHasta    = ($dp->LegajoHasta) ?? [];
$dp->LegajoHasta    = vp($dp->LegajoHasta, 'LegajoHasta', 'str', 9);

$dp->TipoDePersonal = ($dp->TipoDePersonal) ?? [];
$dp->TipoDePersonal = vp($dp->TipoDePersonal, 'TipoDePersonal', 'int', 1);

$dp->Empresa        = ($dp->Empresa) ?? [];
$dp->Empresa        = vp($dp->Empresa, 'Empresa', 'int', 4);

$dp->Planta         = ($dp->Planta) ?? [];
$dp->Planta         = vp($dp->Planta, 'Planta', 'int', 4);

$dp->Sucursal       = ($dp->Sucursal) ?? [];
$dp->Sucursal       = vp($dp->Sucursal, 'Sucursal', 'int', 4);

$dp->Grupo          = ($dp->Grupo) ?? [];
$dp->Grupo          = vp($dp->Grupo, 'Grupo', 'int', 4);

$dp->Sector         = ($dp->Sector) ?? [];
$dp->Sector         = vp($dp->Sector, 'Sector', 'int', 4);

$dp->Seccion        = ($dp->Seccion) ?? [];
$dp->Seccion        = vp($dp->Seccion, 'Seccion', 'int', 4);

$getHorario = getHorario(
    ($dp->FechaDesde),
    ($dp->FechaHasta),
    ($dp->Legajos),
    $dp->LegajoDesde,
    $dp->LegajoHasta,
    $dp->TipoDePersonal,
    $dp->Empresa,
    $dp->Planta,
    $dp->Sucursal,
    $dp->Grupo,
    $dp->Sector,
    $dp->Seccion,
    $dataC['WebServiceCH'] . '/RRHHWebService',
);
// Flight::json($getHorario, 200) . exit;
// $arrHorario = preg_split('/(\r|\n)/', $getHorario['Estado'], -1, 1);
// print_r($getHorario['Estado']).exit;
$data = array();

if ($getHorario && $getHorario['Estado'] != 'Terminado') {
    $arrHorario = preg_split('/(\r|\n)/', $getHorario['Estado'], -1, 1);
    // $arrHorario = preg_split("/[\s]+/", $getHorario['Estado'], -1, 1);
    // print_r($arrHorario) . exit;
    foreach ($arrHorario as $key => $value) {
        $explode    = explode(',', $value);
        $legajo     = $explode[0];
        $fecha      = fecha(str_replace('/', '-', $explode[1]));
        $desde      = $explode[2];
        $hasta      = $explode[3];
        $descanso   = $explode[4];
        $laboral    = $explode[5];
        $feriado    = $explode[6];
        $asignacion = $explode[7];
        $codigo     = $explode[8];
        $dia        = diaSemana($fecha);

        $horariodesc = '';
        if ($codigo > 0) {
            $filtroHorarios = filtrarObjetoArr($stmtHorarios, 'HorCodi', $codigo);

            foreach ($filtroHorarios as $key => $a) {
                $horario = array(
                    'cod'  => $a['HorCodi'],
                    'desc' => $a['HorDesc'],
                    'horID' => $a['HorID']
                );
            }
            $horario['desc'] = str_replace("  ", " ", $horario['desc']);
            $horariodesc = (intval($asignacion) == 0) ? '' : ' ' . $horario['desc'];
            $horariodesc = (intval($codigo) == 0) ? '' : ' ' . $horario['desc'];
        }
        $horario['desc'] = $horario['desc'] ?? '';
        $horario['cod'] = $horario['cod'] ?? '';
        $horario['horID'] = $horario['horID'] ?? '';

        switch (intval($asignacion)) {
            case 0:
                $tipo = 'Sin Horario Asignado';
                break;
            case 3:
                $tipo = 'Desde Hasta por Legajo';
                break;
            case 9:
                $tipo = 'Desde por Legajo';
                break;
            case 6:
                $tipo = 'Rotación por Legajo';
                break;
            case 7:
                $tipo = 'Rotación por Sector';
                break;
            case 8:
                $tipo = 'Rotación por Grupo';
                break;
            case 1:
                $tipo = 'Citación';
                $horariodesc = '';
                break;
            case 10:
                $tipo = 'Desde por Sector';
                break;
            case 11:
                $tipo = 'Desde por Grupo';
                break;

            default:
                $tipo = '';
                break;
        }
        switch (intval($laboral)) {
            case 0:
                $diaLaboral = 'No';
                break;
            case 1:
                $diaLaboral = 'Sí';
                break;

            default:
                $diaLaboral = '';
                break;
        }
        switch (intval($feriado)) {
            case 0:
                $diaFeriado = 'No';
                break;
            case 1:
                $diaFeriado = 'Sí';
                break;

            default:
                $diaFeriado = '';
                break;
        }

        if (intval($laboral) == 1) {
            $vsHorario = $desde . ' a ' . $hasta;
        } else {
            if (intval($feriado) == 0) {
                $vsHorario = 'Franco';
            } else {
                $vsHorario = 'Feriado';
            }
        }
        $Mensaje = (($getHorario['Estado'])) ? $tipo . ' (' . $vsHorario . ') ' . $horariodesc : 'Sin datos';
        $Horario = (($horario['desc'])) ? $horario['desc'] : 'Sin datos';
        $HorarioID = (($horario['horID'])) ? $horario['horID'] : '-';

        $data[] = array(
            'Codigo'       => intval($codigo),
            'Horario'      => $Horario,
            'HorarioID'      => $HorarioID,
            'Fecha'        => $fecha,
            'Dia'          => $dia,
            'Feriado'      => ($diaFeriado),
            'Laboral'      => ($diaLaboral),
            'Desde'        => $desde,
            'Hasta'        => $hasta,
            'Descanso'     => $descanso,
            'Legajo'       => intval($legajo),
            'TipoAsign'    => ($tipo),
            'TipoAsignStr' => $Mensaje
        );
    }
}

$countData    = count($data);
http_response_code(200);
(response($data, '0', 'OK', 200, $time_start, $countData, $idCompany));
exit;
