<?php
session_start();
require __DIR__ . '../../../config/index.php';
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('America/Argentina/Buenos_Aires');
setlocale(LC_TIME, "es_ES");
secure_auth_ch_json();

E_ALL();

require __DIR__ . '../../../config/conect_mssql.php';
$params    = array();
$options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$data      = array();
$FechaHora = date('Ymd H:i:s');

$_POST['tipo'] = $_POST['tipo'] ?? '';

if (($_SERVER["REQUEST_METHOD"] == "POST") && (array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'u_horale1')) {
    if (($_SESSION["ABM_ROL"]['mTur']=='0')) {
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
        $Dato    = 'Horario Desde: '.Fech_Format_Var($Fecha, 'd/m/Y').'. Horario: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('M', $Dato);
        PrintRespuestaJson('ok', 'Asignación modificada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
} else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'd_horale1')) {
    if (($_SESSION["ABM_ROL"]['bTur']=='0')) {
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
        $Dato    = 'Horario Desde: '.Fech_Format_Var($Fecha, 'd/m/Y').'. Horario: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Asignación eliminada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'c_horale1')) {
    if (($_SESSION["ABM_ROL"]['aTur']=='0')) {
        PrintRespuestaJson('error', 'No tiene permisos para asignar horarios');
        exit;
    };

    $_POST['Codhor']  = $_POST['Codhor'] ?? '';
    $_POST['NumLega'] = $_POST['NumLega'] ?? '';
    $_POST['FDesde']   = $_POST['FDesde'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $NumLega = test_input($_POST['NumLega']);
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
        $Dato    = 'Horario Desde: '.Fech_Format_Var($Fecha, 'd/m/Y').'. Horario: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('A', $Dato);
        PrintRespuestaJson('ok', 'Asignación creada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'c_horale2')) {
    if (($_SESSION["ABM_ROL"]['aTur']=='0')) {
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
        $Dato    = 'Horario Desde Hasta: '.Fech_Format_Var($FechaIni, 'd/m/Y').' - '.Fech_Format_Var($FechaFin, 'd/m/Y').'. Horario: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('A', $Dato);
        PrintRespuestaJson('ok', 'Asignación creada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'u_horale2')) {
    if (($_SESSION["ABM_ROL"]['mTur']=='0')) {
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
        $Dato    = 'Horario Desde Hasta: '.Fech_Format_Var($FechaIni, 'd/m/Y').' - '.Fech_Format_Var($FechaFin, 'd/m/Y').'. Horario: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('M', $Dato);
        PrintRespuestaJson('ok', 'Asignación modificada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'd_horale2')) {
    if (($_SESSION["ABM_ROL"]['bTur']=='0')) {
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
        $Dato    = 'Horario Desde Hasta: '.Fech_Format_Var($FechaIni, 'd/m/Y').' - '.Fech_Format_Var($FechaFin, 'd/m/Y').'. Horario: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Asignación eliminada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'c_rotacion')) {
    if (($_SESSION["ABM_ROL"]['aTur']=='0')) {
        PrintRespuestaJson('error', 'No tiene permisos para asignar horarios');
        exit;
    };
    $_POST['Codhor']  = $_POST['Codhor'] ?? '';
    $_POST['NumLega'] = $_POST['NumLega'] ?? '';
    $_POST['RotFecha']   = $_POST['RotFecha'] ?? '';
    $_POST['RotDia']   = $_POST['RotDia'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $NumLega = test_input($_POST['NumLega']);
    $RotDia = test_input($_POST['RotDia']);
    $Fecha   = test_input(dr_fecha($_POST['RotFecha']));

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

    $query = "INSERT INTO ROTALEG (RolLega,RolFech,RolRota,RolDias,FechaHora) VALUES ('$NumLega','$Fecha','$Codhor','$RotDia','$FechaHora')";

    if (InsertRegistro($query)) {
        $Dato    = 'Rotación: '.Fech_Format_Var($Fecha, 'd/m/Y').'. Rotación: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('A', $Dato);
        PrintRespuestaJson('ok', 'Rotación asignada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'u_rotacion')) {
    if (($_SESSION["ABM_ROL"]['mTur']=='0')) {
        PrintRespuestaJson('error', 'No tiene permisos para modificar horarios');
        exit;
    };
    $_POST['Codhor']   = $_POST['Codhor'] ?? '';
    $_POST['Codhor2']  = $_POST['Codhor2'] ?? '';
    $_POST['NumLega']  = $_POST['NumLega'] ?? '';
    $_POST['RotFecha'] = $_POST['RotFecha'] ?? '';
    $_POST['RotDia']   = $_POST['RotDia'] ?? '';

    $Codhor  = test_input($_POST['Codhor']);
    $Codhor2 = test_input($_POST['Codhor2']);
    $NumLega = test_input($_POST['NumLega']);
    $RotDia  = test_input($_POST['RotDia']);
    $Fecha   = test_input(dr_fecha($_POST['RotFecha']));

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

    $query = "INSERT INTO ROTALEG (RolLega,RolFech,RolRota,RolDias,FechaHora) VALUES ('$NumLega','$Fecha','$Codhor','$RotDia','$FechaHora')";

    $query = "UPDATE ROTALEG SET RolRota = '$Codhor', RolDias = '$RotDia', FechaHora = '$FechaHora' WHERE RolFech = '$Fecha' AND RolLega = '$NumLega'";

    if (UpdateRegistro($query)) {
        $Dato    = 'Rotación: '.Fech_Format_Var($Fecha, 'd/m/Y').'. Rotación: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('M', $Dato);
        PrintRespuestaJson('ok', 'Rotación modificada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}else if ((array_key_exists('Codhor', $_POST)) && ($_POST['tipo'] == 'd_rotacion')) {
    if (($_SESSION["ABM_ROL"]['bTur']=='0')) {
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
        $Dato    = 'Rotación: '.Fech_Format_Var($Fecha, 'd/m/Y').'. Rotación: ' . $Codhor.'. Legajo: ' . $NumLega;
        audito_ch('B', $Dato);
        PrintRespuestaJson('ok', 'Rotación eliminada correctamente.');
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}
PrintRespuestaJson('error', 'Errors');
exit;
