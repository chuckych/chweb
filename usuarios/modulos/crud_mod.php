<?php
session_start();
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
E_ALL();

$data = array();

/** Declaro variable si no existe */
FusNuloPOST('TipoMod', ''); 
FusNuloPOST('amod', '');
FusNuloPOST('recidRol', '');
FusNuloPOST('IdRol', '');

if (valida_campo($_POST['TipoMod'])) {
    PrintError('TipoMod', 'No Hay TipoMod'); /** Imprimo json con resultado */
    exit;
}
if (valida_campo($_POST['recidRol'])) {
    PrintError('recidRol', 'No Hay recidRol'); /** Imprimo json con resultado */
    exit;
}
/** CRUD MODULOS */
if (($_SERVER["REQUEST_METHOD"] == "POST")) {


    function DeleteModRol($recidRol,$TipoMod){
        require __DIR__ . '../../../config/conect_mysql.php';
        $Query = "DELETE mod_roles FROM mod_roles LEFT JOIN modulos ON mod_roles.modulo = modulos.id WHERE mod_roles.recid_rol = '$recidRol' AND modulos.idtipo = '$TipoMod'";
        if (mysqli_query($link, $Query)) {  /** Hacemos el delete de todos los modulos del tipo */
            return true;
            mysqli_close($link);
        }else {
            return false;
            mysqli_close($link);
        }
    }
    function InsertModRol($recidRol, $IdRol, $Modulo,$Fecha){
        require __DIR__ . '../../../config/conect_mysql.php';
        $Query = "INSERT INTO mod_roles(recid_rol, id_rol, modulo, fecha) VALUES('$recidRol', '$IdRol', '$Modulo', '$Fecha')";
        // PrintError('ErrorInsert',$Query); exit;
        $rs_insert = mysqli_query($link, $Query);
        if ($rs_insert) { /** Si el insert se hizo correctamente */
            return true;
            mysqli_close($link);
        } else { /** Si hubo error */
            return false;
            mysqli_close($link);
        }
    }
    
    $Modulo   = ($_POST['amod']);
    $TipoMod  = test_input($_POST['TipoMod']);
    $recidRol = test_input($_POST['recidRol']);
    $IdRol    = test_input($_POST['IdRol']);
    $Fecha    = date("Y-m-d H:i:s");

    
    $Query = "SELECT * FROM mod_roles LEFT JOIN modulos ON mod_roles.modulo = modulos.id WHERE mod_roles.recid_rol = '$recidRol' AND modulos.idtipo = '$TipoMod'"; /** Hacemos Select para ver si existen modulos del tipo */
    // PrintRespuestaJson('ok', $Query ); exit;
    
    if(CountRegMayorCeroMySql($Query)){ /** Si Hay modulos del tipo en la tabla mod_roles */

        if (DeleteModRol($recidRol,$TipoMod)) { /** Hacemos el delete de todos los modulos del tipo */
            if (!valida_campo($_POST['amod'])) { /** Si recibimos datos de modulos. Hacemos el insert de los mismos */
                foreach ($Modulo as $key => $ValueMod) { /** recorremos el array de los modulos recibido */
                    /** Hacemos el insert de los modulos */
                    if (!InsertModRol($recidRol,$IdRol,$ValueMod,$Fecha)) { /** Si hubo error */
                        mysqli_error($link); PrintError('ErrorInsert',mysqli_error($link)); /** Imprimo json con resultado */
                        // mysqli_close($link); /** Cerramos conexion con Mysql */
                        exit;
                    }
                }
                //PrintOK('Datos Guardados'); /** Imprimo json con resultado */
                PrintRespuestaJson('ok', 'Datos Guardados');
                // mysqli_close($link); /** Cerramos conexion con Mysql */
                exit;
            } else { /** Si no recibimos datos de modulos devolvemos mensaje del delete de los modulos */
                PrintRespuestaJson('ok', 'Datos Guardados');
                // mysqli_close($link); /** Cerramos conexion con Mysql */
                exit;
            }
        } else {
            mysqli_error($link); 
            PrintError('ErrorDelete',mysqli_errno($link)); /** Imprimo json con resultado */
            // mysqli_close($link); /** Cerramos conexion con Mysql */
            // PrintRespuestaJson('error', 'Error');
            exit;
        }

    } else {
        if (!valida_campo($_POST['amod'])) { 
            /** Si recibimos datos de modulos. Hacemos el insert de los mismos */
            foreach ($Modulo as $key => $ValueMod) { /** recorremos el array de los modulos recibido */
                // PrintError('ErrorInsert',$IdRol); exit;
                /** Hacemos el insert de los modulos */
                if (!InsertModRol($recidRol, $IdRol, $ValueMod ,$Fecha)) { /** Si hubo error */
                    mysqli_error($link); PrintError('ErrorInsert',mysqli_errno($link)); /** Imprimo json con resultado */
                    mysqli_close($link); /** Cerramos conexion con Mysql */
                    exit;
                }
            }
            PrintRespuestaJson('ok', 'Datos Guardadoss');
            exit;
        }else{
            PrintRespuestaJson('ok', 'Datos Guardados');
            exit;
        }
    }
}
/** FIN CRUD MÓDULOS */
