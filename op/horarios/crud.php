<?php
session_start();
require __DIR__ . '../../../config/index.php';
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

// require __DIR__ . '../../../vendor/autoload.php';

// use Carbon\Carbon;

E_ALL();

require __DIR__ . '../../../config/conect_mssql.php';
$params    = array();
$options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$data      = array();
$FechaHora = date('Ymd H:i:s');
$FechaHoy  = date('Ymd');

$_POST['tipo'] = $_POST['tipo'] ?? '';

if (($_SERVER["REQUEST_METHOD"] == "POST") && (array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'u_horale1')) {
    if (($_SESSION["ABM_ROL"]['mTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para modificar horarios');
        exit;
    };
    $_POST['Codhor']  = $_POST['Codhor'] ?? '';
    $_POST['Codhor2'] = $_POST['Codhor2'] ?? '';
    $_POST['NumLega'] = $_POST['NumLega'] ?? '';
    $_POST['Fecha']   = $_POST['Fecha'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $Codhor2 = test_input($_POST['Codhor2']);
    /** Horario Original */
    $NumLega = test_input($_POST['NumLega']);
    $Fecha   = test_input($_POST['Fecha']);

    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'El horario es requerido');
        exit;
    };
    if (valida_campo($Codhor2)) {
        PrintRespuestaJson('error', 'El horario original es requerido');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido');
        exit;
    };
    if (valida_campo($Fecha)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };

    $query = "SELECT 1 FROM HORALE1 WHERE Ho1Hora = '$Codhor2' AND Ho1Fech = '$Fecha' AND Ho1Lega = '$NumLega'";

    if (!CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'No existe el registro');
        exit;
    }

    $query = "UPDATE HORALE1 set Ho1Hora = '$Codhor' WHERE Ho1Hora = '$Codhor2' AND Ho1Fech = '$Fecha' AND Ho1Lega = '$NumLega' ";
    if (UpdateRegistro($query)) {

        $tiempo_inicio_proceso = microtime(true);
        $Dato    = 'Horario Desde: ' . Fech_Format_Var($Fecha, 'd/m/Y') . '. Horario: ' . $Codhor . '. Legajo: ' . $NumLega;
        $FechaIni = $Fecha;
        $FechaFin = date('Ymd');
        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
        $arrayFechas    = (fechaIniFinDias(FechaString($Fecha), date('Ymd'), 31));

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Asignación modificada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);

        audito_ch('M', $Dato, '33');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'd_horale1')) {
    if (($_SESSION["ABM_ROL"]['bTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para eliminar horarios');
        exit;
    };
    $_POST['Codhor']  = $_POST['Codhor'] ?? '';
    $_POST['NumLega'] = $_POST['NumLega'] ?? '';
    $_POST['Fecha']   = $_POST['Fecha'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $NumLega = test_input($_POST['NumLega']);
    $Fecha   = test_input($_POST['Fecha']);

    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'El horario es requerido');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido');
        exit;
    };
    if (valida_campo($Fecha)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };

    $query = "SELECT 1 FROM HORALE1 WHERE Ho1Hora = '$Codhor' AND Ho1Fech = '$Fecha' AND Ho1Lega = '$NumLega'";

    if (!CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'No existe el registro');
        exit;
    }

    $query = "DELETE FROM HORALE1 WHERE Ho1Hora = '$Codhor' AND Ho1Fech = '$Fecha' AND Ho1Lega = '$NumLega'";
    if (UpdateRegistro($query)) {
        $tiempo_inicio_proceso = microtime(true);
        $Dato    = 'Horario Desde: ' . Fech_Format_Var($Fecha, 'd/m/Y') . '. Horario: ' . $Codhor . '. Legajo: ' . $NumLega;
        $FechaIni = $Fecha;
        $FechaFin = date('Ymd');
        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
        $arrayFechas    = (fechaIniFinDias(FechaString($Fecha), date('Ymd'), 31));

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Asignación eliminada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);
        audito_ch('B', $Dato, '33');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'c_horale1')) {

    if (($_SESSION["ABM_ROL"]['aTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para asignar horarios');
        exit;
    };

    $_POST['Codhor']  = $_POST['Codhor'] ?? '';
    $_POST['NumLega'] = $_POST['NumLega'] ?? '';
    $_POST['FDesde']   = $_POST['FDesde'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $NumLega = test_input($_POST['NumLega']);
    $FDesde  = test_input(($_POST['FDesde']));
    $Fecha   = test_input(dr_fecha($_POST['FDesde']));

    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'El horario es requerido');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido');
        exit;
    };
    if (valida_campo($Fecha)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };

    $query = "SELECT 1 FROM HORALE1 WHERE Ho1Fech = '$Fecha' AND Ho1Lega = '$NumLega'";

    if (CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'Ya existe asignación para la fecha.');
        exit;
    }

    $query = "INSERT INTO HORALE1 (Ho1Lega,Ho1Fech,Ho1Hora,FechaHora) VALUES ('$NumLega','$Fecha','$Codhor','$FechaHora')";

    if (InsertRegistro($query)) {

        $tiempo_inicio_proceso = microtime(true);

        $Dato    = 'Horario Desde: ' . $FDesde . '. Horario: ' . $Codhor . '. Legajo: ' . $NumLega;
        $FechaIni = $Fecha;
        $FechaFin = date('Ymd');
        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
        $arrayFechas    = (fechaIniFinDias(FechaString($Fecha), date('Ymd'), 31));

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Asignación creada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);
        audito_ch('A', $Dato, '33');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'c_horale2')) {
    if (($_SESSION["ABM_ROL"]['aTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para asignar horarios');
        exit;
    };
    $_POST['Codhor']  = $_POST['Codhor'] ?? '';
    $_POST['NumLega'] = $_POST['NumLega'] ?? '';
    $_POST['FDesde']   = $_POST['FDesde'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $NumLega = test_input($_POST['NumLega']);
    $Fecha = explode(' al ', $_POST['FDesdeHasta']);
    $FechaIni   = test_input(dr_fecha($Fecha[0]));
    $FechaFin   = test_input(dr_fecha($Fecha[1]));

    // PrintRespuestaJson('error', $FechaFin);
    //     exit;

    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'El horario es requerido');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido');
        exit;
    };
    if (valida_campo($Fecha)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };

    $query = "SELECT 1 FROM HORALE2 WHERE Ho2Fec1 = '$FechaIni' AND Ho2Lega = '$NumLega'";
    // PrintRespuestaJson('error', $query);
    // exit;
    if (CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'Ya existe asignación para la fecha.');
        exit;
    }

    $query = "INSERT INTO HORALE2 (Ho2Lega,Ho2Fec1,Ho2Fec2,Ho2Hora,FechaHora) VALUES ('$NumLega','$FechaIni','$FechaFin','$Codhor','$FechaHora')";

    if (InsertRegistro($query)) {

        $tiempo_inicio_proceso = microtime(true);

        $Dato    = 'Horario Desde Hasta: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' - ' . Fech_Format_Var($FechaFin, 'd/m/Y') . '. Horario: ' . $Codhor . '. Legajo: ' . $NumLega;

        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Asignación creada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);
        audito_ch('A', $Dato, '33');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'u_horale2')) {
    if (($_SESSION["ABM_ROL"]['mTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para modificar horarios');
        exit;
    };
    $_POST['Codhor']  = $_POST['Codhor'] ?? '';
    $_POST['Codhor2'] = $_POST['Codhor2'] ?? '';
    $_POST['NumLega'] = $_POST['NumLega'] ?? '';
    $_POST['FDesde']  = $_POST['FDesde'] ?? '';
    $_POST['FHasta']  = $_POST['FHasta'] ?? '';
    $_POST['FHasta2'] = $_POST['FHasta2'] ?? '';

    $Codhor           = test_input($_POST['Codhor']);
    $Codhor2          = test_input($_POST['Codhor2']);
    /** Horario Original */
    $NumLega          = test_input($_POST['NumLega']);
    $FechaIni         = test_input(dr_fecha($_POST['FDesde']));
    $FechaFin         = test_input(dr_fecha($_POST['FHasta']));
    $FechaFin2        = test_input(dr_fecha($_POST['FHasta2']));

    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'El horario es requerido');
        exit;
    };
    if (valida_campo($Codhor2)) {
        PrintRespuestaJson('error', 'El horario original es requerido');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido');
        exit;
    };
    if (valida_campo($FechaIni)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };
    if (valida_campo($FechaFin)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };

    $query = "SELECT 1 FROM HORALE2 WHERE Ho2Hora = '$Codhor2' AND Ho2Fec1 = '$FechaIni' AND Ho2Fec2 = '$FechaFin2' AND Ho2Lega = '$NumLega'";

    if (!CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'No existe Asignación');
        exit;
    }
    $query = "SELECT 1 FROM HORALE2 WHERE Ho2Hora = '$Codhor' AND Ho2Fec1 = '$FechaIni' AND Ho2Fec2 = '$FechaFin' AND Ho2Lega = '$NumLega'";

    if (CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'Ya existe asignación para la fecha');
        exit;
    }

    $query = "UPDATE HORALE2 set Ho2Hora = '$Codhor', Ho2Fec2 = '$FechaFin' WHERE Ho2Hora = '$Codhor2' AND Ho2Fec1 = '$FechaIni' AND Ho2Fec2 = '$FechaFin2' AND Ho2Lega = '$NumLega' ";
    if (UpdateRegistro($query)) {

        $tiempo_inicio_proceso = microtime(true);

        $Dato    = 'Horario Desde Hasta: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' - ' . Fech_Format_Var($FechaFin, 'd/m/Y') . '. Horario: ' . $Codhor . '. Legajo: ' . $NumLega;

        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Asignación modificada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);
        audito_ch('M', $Dato, '33');
        // PrintRespuestaJson('ok', 'Asignación modificada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'd_horale2')) {
    if (($_SESSION["ABM_ROL"]['bTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para eliminar horarios');
        exit;
    };
    $_POST['Codhor']   = $_POST['Codhor'] ?? '';
    $_POST['NumLega']  = $_POST['NumLega'] ?? '';
    $_POST['FechaIni'] = $_POST['FechaIni'] ?? '';
    $_POST['Fecha']    = $_POST['Fecha'] ?? '';
    $_POST['FechaFin'] = $_POST['FechaFin'] ?? '';

    $Codhor   = test_input($_POST['Codhor']);
    $NumLega  = test_input($_POST['NumLega']);
    $FechaIni = test_input(dr_fecha($_POST['FechaIni']));
    $FechaFin = test_input(dr_fecha($_POST['FechaFin']));

    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'El horario es requerido');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido');
        exit;
    };
    if (valida_campo($FechaIni)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };
    if (valida_campo($FechaFin)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };

    $query = "SELECT 1 FROM HORALE2 WHERE Ho2Hora = '$Codhor' AND Ho2Fec1 = '$FechaIni' AND Ho2Fec2 = '$FechaFin' AND Ho2Lega = '$NumLega'";

    if (!CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'No existe el registro');
        exit;
    }

    $query = "DELETE FROM HORALE2 WHERE Ho2Hora = '$Codhor' AND Ho2Fec1 = '$FechaIni' AND Ho2Fec2 = '$FechaFin' AND Ho2Lega = '$NumLega'";
    if (UpdateRegistro($query)) {

        $tiempo_inicio_proceso = microtime(true);

        $Dato    = 'Horario Desde Hasta: ' . Fech_Format_Var($FechaIni, 'd/m/Y') . ' - ' . Fech_Format_Var($FechaFin, 'd/m/Y') . '. Horario: ' . $Codhor . '. Legajo: ' . $NumLega;

        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Asignación eliminada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);

        audito_ch('B', $Dato, '33');
        // PrintRespuestaJson('ok', 'Asignación eliminada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'c_rotacion')) {
    if (($_SESSION["ABM_ROL"]['aTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para asignar horarios');
        exit;
    };
    $_POST['Codhor']   = $_POST['Codhor'] ?? '';
    $_POST['NumLega']  = $_POST['NumLega'] ?? '';
    $_POST['RotFecha'] = $_POST['RotFecha'] ?? '';
    $_POST['RotDia']   = $_POST['RotDia'] ?? '';
    $_POST['RoLVenc']  = $_POST['RoLVenc'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $NumLega = test_input($_POST['NumLega']);
    $RotDia  = test_input($_POST['RotDia']);
    $RoLVenc = test_input($_POST['RoLVenc']);
    $Fecha   = test_input(dr_fecha($_POST['RotFecha']));
    $RoLVenc = ($_POST['RoLVenc']) ? test_input(dr_fecha($_POST['RoLVenc'])) : '20991231';

    if (intval($RoLVenc) < intval($Fecha)) {
        PrintRespuestaJson('error', 'El <b>Vencimiento</b> no puede ser menor a la <b>Fecha</b> de inicio de la Rotación.');
        exit;
    };
    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'La rotación es requerida.');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido.');
        exit;
    };
    if (valida_campo($Fecha)) {
        PrintRespuestaJson('error', 'La Fecha es requerida.');
        exit;
    };
    if (valida_campo($RotDia)) {
        PrintRespuestaJson('error', 'Dia de comienzo requerido.');
        exit;
    };

    $query = "SELECT 1 FROM ROTALEG WHERE RolFech = '$Fecha' AND RolLega = '$NumLega'";

    if (CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'Ya existe asignación para la fecha.');
        exit;
    }

    $query = "INSERT INTO ROTALEG (RolLega,RolFech,RolRota,RolDias,FechaHora,RoLVenc) VALUES ('$NumLega','$Fecha','$Codhor','$RotDia','$FechaHora','$RoLVenc')";

    if (InsertRegistro($query)) {

        $tiempo_inicio_proceso = microtime(true);
        $Dato    = 'Rotación: ' . Fech_Format_Var($Fecha, 'd/m/Y') . '. Rotación: ' . $Codhor . '. Legajo: ' . $NumLega;
        $FechaIni = $Fecha;
        $FechaFin = date('Ymd');
        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
        $arrayFechas    = (fechaIniFinDias(FechaString($Fecha), date('Ymd'), 31));

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        audito_ch('A', $Dato, '33');
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Rotación asignada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'u_rotacion')) {
    if (($_SESSION["ABM_ROL"]['mTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para modificar horarios');
        exit;
    };
    $_POST['Codhor']   = $_POST['Codhor'] ?? '';
    $_POST['Codhor2']  = $_POST['Codhor2'] ?? '';
    $_POST['NumLega']  = $_POST['NumLega'] ?? '';
    $_POST['RotFecha'] = $_POST['RotFecha'] ?? '';
    $_POST['RotDia']   = $_POST['RotDia'] ?? '';
    $_POST['RoLVenc']  = $_POST['RoLVenc'] ?? '';
    $RoLVenc = ($_POST['RoLVenc']) ? test_input(dr_fecha($_POST['RoLVenc'])) : '20991231';

    $Codhor  = test_input($_POST['Codhor']);
    $Codhor2 = test_input($_POST['Codhor2']);
    $NumLega = test_input($_POST['NumLega']);
    $RotDia  = test_input($_POST['RotDia']);
    $Fecha   = test_input(dr_fecha($_POST['RotFecha']));

    if (intval($RoLVenc) < intval($Fecha)) {
        PrintRespuestaJson('error', 'El <b>Vencimiento</b> no puede ser menor a la <b>Fecha</b> de inicio de la Rotación.');
        exit;
    };
    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'La rotación es requerida.');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido.');
        exit;
    };
    if (valida_campo($Fecha)) {
        PrintRespuestaJson('error', 'La Fecha es requerida.');
        exit;
    };
    if (valida_campo($RotDia)) {
        PrintRespuestaJson('error', 'Dia de comienzo requerido.');
        exit;
    };

    $query = "SELECT 1 FROM ROTALEG WHERE RolFech = '$Fecha' AND RolLega = '$NumLega'";

    if (!CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'No existe registro.');
        exit;
    }

    // $query = "INSERT INTO ROTALEG (RolLega,RolFech,RolRota,RolDias,FechaHora) VALUES ('$NumLega','$Fecha','$Codhor','$RotDia','$FechaHora')";

    $query = "UPDATE ROTALEG SET RolRota = '$Codhor', RolDias = '$RotDia', FechaHora = '$FechaHora', RoLVenc = '$RoLVenc' WHERE RolFech = '$Fecha' AND RolLega = '$NumLega'";

    if (UpdateRegistro($query)) {
        $tiempo_inicio_proceso = microtime(true);
        $Dato    = 'Rotación: ' . Fech_Format_Var($Fecha, 'd/m/Y') . '. Rotación: ' . $Codhor . '. Legajo: ' . $NumLega;
        $FechaIni = $Fecha;
        $FechaFin = date('Ymd');
        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
        $arrayFechas    = (fechaIniFinDias(FechaString($Fecha), date('Ymd'), 31));

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Rotación modificada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);
        audito_ch('M', $Dato, '33');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'd_rotacion')) {
    if (($_SESSION["ABM_ROL"]['bTur'] == '0')) {
        PrintRespuestaJson('error', 'No tiene permisos para eliminar horarios');
        exit;
    };
    $_POST['Codhor']   = $_POST['Codhor'] ?? '';
    $_POST['NumLega']  = $_POST['NumLega'] ?? '';
    $_POST['Fecha']    = $_POST['Fecha'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $NumLega = test_input($_POST['NumLega']);
    $Fecha   = test_input(dr_fecha($_POST['Fecha']));

    if (valida_campo($Codhor)) {
        PrintRespuestaJson('error', 'la rotación es requerida');
        exit;
    };
    if (valida_campo($NumLega)) {
        PrintRespuestaJson('error', 'El Legajo es requerido');
        exit;
    };
    if (valida_campo($Fecha)) {
        PrintRespuestaJson('error', 'La Fecha es requerida');
        exit;
    };

    $query = "SELECT 1 FROM ROTALEG WHERE RolFech = '$Fecha' AND RolLega = '$NumLega'";

    if (!CountRegistrosMayorCero($query)) {
        PrintRespuestaJson('error', 'No existe registro.');
        exit;
    }

    $query = "DELETE FROM ROTALEG WHERE RolFech = '$Fecha' AND RolLega = '$NumLega'";
    if (UpdateRegistro($query)) {
        $tiempo_inicio_proceso = microtime(true);
        $Dato    = 'Rotación: ' . Fech_Format_Var($Fecha, 'd/m/Y') . '. Rotación: ' . $Codhor . '. Legajo: ' . $NumLega;
        $FechaIni = $Fecha;
        $FechaFin = date('Ymd');
        $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
        $arrayFechas    = (fechaIniFinDias(FechaString($Fecha), date('Ymd'), 31));

        if ($FechaIni <= date('Ymd')) {
            $arrayFechas    = (fechaIniFinDias(($FechaIni), $FechaFin, 31));
            $arrRespuesta = array();
            if ($totalDias > 31) {
                foreach ($arrayFechas as $date) {
                    $tiempo_ini = microtime(true);
                    $procesar = procesar_legajo($NumLega, $date['FechaIni'], $date['FechaFin']);
                    $totalDias = totalDiasFechas($date['FechaIni'], $date['FechaFin']);
                    if (($procesar == 'Terminado')) {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    } else {
                        $tiempo_fini = microtime(true);
                        $duracion = round($tiempo_fini - $tiempo_ini, 2);
                        $arrRespuesta[] = array('Desde' => $date['FechaIni'], 'Hasta' => $date['FechaFin'], 'Procesado' => 'Sin Procesar ' . $totalDias . ' días', 'Tiempo' => $duracion);
                    }
                }
            } else {
                $tiempo_ini = microtime(true);
                $FechaFin = ($FechaFin > date('Ymd')) ? date('Ymd') : $FechaFin;
                $totalDias = totalDiasFechas($FechaIni, $FechaFin) + 1;
                $procesar = procesar_legajo($NumLega, $FechaIni, $FechaFin);
                if (($procesar == 'Terminado')) {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Procesado. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                } else {
                    $tiempo_fini = microtime(true);
                    $duracion = round($tiempo_fini - $tiempo_ini, 2);
                    $arrRespuesta = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. ' . $totalDias . ' días', 'Tiempo' => $duracion);
                }
            }
        } else {
            $tiempo_ini = microtime(true);
            $tiempo_fini = microtime(true);
            $duracion = round($tiempo_fini - $tiempo_ini, 2);
            $procesar = 'Fecha Posterior a la actual';
            $arrRespuesta[] = array('Desde' => Fech_Format_Var($FechaIni, 'd/m/Y'), 'Hasta' => Fech_Format_Var($FechaFin, 'd/m/Y'), 'Procesado' => 'Sin Procesar. Fecha Posterior a la actual ' . $totalDias . ' días', 'Tiempo' => $duracion);
        }
        $tiempo_fin_proceso = microtime(true);
        $duracion_proceso    = round($tiempo_fin_proceso - $tiempo_inicio_proceso, 2);
        $data = array('status' => 'ok', 'Mensaje' => 'Rotación eliminada correctamente', 'Detalle' => $arrRespuesta, 'Duracion' => $duracion_proceso);
        echo json_encode($data);
        audito_ch('B', $Dato, '33');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}
PrintRespuestaJson('error', 'Errors');
exit;
