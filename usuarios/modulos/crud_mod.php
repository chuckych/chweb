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
    PrintError('TipoMod', 'No Hay TipoMod');
    /** Imprimo json con resultado */
    exit;
}
if (valida_campo($_POST['recidRol'])) {
    PrintError('recidRol', 'No Hay recidRol');
    /** Imprimo json con resultado */
    exit;
}
/** CRUD MODULOS */
if (($_SERVER["REQUEST_METHOD"] == "POST")) {

    function DeleteModRol($recidRol, $TipoMod)
    {
        require __DIR__ . '../../../config/conect_mysql.php';
        $Query = "DELETE mod_roles FROM mod_roles LEFT JOIN modulos ON mod_roles.modulo = modulos.id WHERE mod_roles.recid_rol = '$recidRol' AND modulos.idtipo = '$TipoMod'";
        if (mysqli_query($link, $Query)) {
            /** Hacemos el delete de todos los modulos del tipo */
            mysqli_close($link);
            return true;
        } else {
            mysqli_close($link);
            return false;
        }
    }
    function InsertModRol($recidRol, $IdRol, $Modulo, $Fecha)
    {
        if (pdoQuery("INSERT INTO mod_roles(recid_rol, id_rol, modulo, fecha) VALUES('$recidRol', '$IdRol', '$Modulo', '$Fecha')")) {
            return true;
        } else {
            return false;
        }
    }
    
    $Modulo   = ($_POST['amod']);
    $TipoMod  = test_input($_POST['TipoMod']);
    $recidRol = test_input($_POST['recidRol']);
    $IdRol    = test_input($_POST['IdRol']);
    $Fecha    = date("Y-m-d H:i:s");

    $audCuenta = simple_pdoQuery("SELECT clientes.id as 'id', clientes.nombre 'nombre', roles.nombre as 'nombre_rol' FROM roles INNER JOIN clientes on roles.cliente = clientes.id WHERE roles.id = $IdRol LIMIT 1");

    $obj_modulos=array_pdoQuery("SELECT modulos.id AS 'id_modulo', modulos.nombre AS 'nombre_modulo', tipo_modulo.descripcion AS 'tipo_modulo', modulos.idtipo as 'idtipo' FROM modulos INNER JOIN tipo_modulo ON modulos.idtipo=tipo_modulo.id WHERE modulos.idtipo=$TipoMod");

    $Query = "SELECT * FROM mod_roles LEFT JOIN modulos ON mod_roles.modulo = modulos.id WHERE mod_roles.recid_rol = '$recidRol' AND modulos.idtipo = '$TipoMod'";
    /** Hacemos Select para ver si existen modulos del tipo */
    // PrintRespuestaJson('ok', $Query ); exit;

    if (CountRegMayorCeroMySql($Query)) {
        /** Si Hay modulos del tipo en la tabla mod_roles */

        if (DeleteModRol($recidRol, $TipoMod)) {

            $nombre_modulo = filtrarObjeto($obj_modulos, 'idtipo', $TipoMod);
            // print_r($nombre_modulo);exit;
            auditoria("Rol ($IdRol) $audCuenta[nombre_rol]. Todos los Módulos. ($nombre_modulo[tipo_modulo])", 'B', $audCuenta['id'], '1');
            /** Hacemos el delete de todos los modulos del tipo */
            if (!valida_campo($_POST['amod'])) {
                /** Si recibimos datos de modulos. Hacemos el insert de los mismos */
                foreach ($Modulo as $key => $ValueMod) {
                    /** recorremos el array de los modulos recibido */
                    /** Hacemos el insert de los modulos */
                    if (!InsertModRol($recidRol, $IdRol, $ValueMod, $Fecha)) {
                        /** Si hubo error */
                        mysqli_error($link);
                        PrintError('ErrorInsert', mysqli_error($link));
                        /** Imprimo json con resultado */
                        // mysqli_close($link); /** Cerramos conexion con Mysql */
                        exit;
                    } else {
                        $nombre_modulo = filtrarObjeto($obj_modulos, 'id_modulo', $ValueMod);
                        // print_r($nombre_modulo);exit;
                        auditoria("Rol ($IdRol) $audCuenta[nombre_rol]. Módulo $nombre_modulo[nombre_modulo]. ($nombre_modulo[tipo_modulo])", 'M', $audCuenta['id'], '1');
                    }
                }
                //PrintOK('Datos Guardados'); /** Imprimo json con resultado */
                PrintRespuestaJson('ok', 'Datos Guardados');
                // mysqli_close($link); /** Cerramos conexion con Mysql */
                exit;
            } else {
                /** Si no recibimos datos de modulos devolvemos mensaje del delete de los modulos */
                PrintRespuestaJson('ok', 'Datos Guardados');
                // mysqli_close($link); /** Cerramos conexion con Mysql */
                exit;
            }
        } else {
            mysqli_error($link);
            PrintError('ErrorDelete', mysqli_errno($link));
            /** Imprimo json con resultado */
            // mysqli_close($link); /** Cerramos conexion con Mysql */
            // PrintRespuestaJson('error', 'Error');
            exit;
        }
    } else {
        if (!valida_campo($_POST['amod'])) {
            /** Si recibimos datos de modulos. Hacemos el insert de los mismos */
            foreach ($Modulo as $key => $ValueMod) {
                /** recorremos el array de los modulos recibido */
                // PrintError('ErrorInsert',$IdRol); exit;
                /** Hacemos el insert de los modulos */
                if (!InsertModRol($recidRol, $IdRol, $ValueMod, $Fecha)) {
                    /** Si hubo error */
                    // mysqli_error($link);
                    PrintError('ErrorInsert', 'Error');
                    /** Imprimo json con resultado */
                    // mysqli_close($link);
                    /** Cerramos conexion con Mysql */
                    exit;
                }
            }
            PrintRespuestaJson('ok', 'Datos Guardadoss');
            exit;
        } else {
            PrintRespuestaJson('ok', 'Datos Guardados');
            exit;
        }
    }
}
/** FIN CRUD MÓDULOS */
