<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
session_start(); //Inicia una nueva sesión o reanuda la existente
header('Content-type: text/html; charset=utf-8'); //Para evitar problemas de acentos
require __DIR__ . '../../../config/index.php'; //config
ultimoacc(); //Actualiza el ultimo acceso del usuario
secure_auth_ch_json(); // Chequeo de authenticacion
header("Content-Type: application/json");
ExisteModRol('7'); // 7 = Personal
E_ALL(); // Report all PHP errors
/** ALTA DE USUARIO */
$_POST['LegaPass'] = $_POST['LegaPass'] ?? '';
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'Importar')) {

    $rol    = test_input($_POST['rol']); // Rol
    $id_c_  = test_input($_POST['id_c']); // id_c
    $ident_ = test_input($_POST['ident']); // ident

    $fecha  = fechaHora2(); // Fecha y hora actual
    /* Comprobamos campos vacios  */
    if (empty($_POST['_l'])) { // si no se selecciono ningun legajo
        PrintRespuestaJson('error', 'Debe seleccionar al menos un legajo');
        exit;
    }
    if (valida_campo($rol)) { // si no se selecciono ningun rol
        PrintRespuestaJson('error', 'Campo Rol es requerido'); // Mensaje de error
        exit;
    }
        $tiempo_ini = microtime(true); // Tiempo inicial

        $getFicLega = implode(",",$_POST['_l']); // Fichero con legajos 
        $query="SELECT DISTINCT PERSONAL.LegNume AS 'pers_legajo', PERSONAL.LegApNo AS 'pers_nombre', PERSONAL.LegDocu AS 'pers_dni' FROM personal WHERE PERSONAL.LegNume > 0 AND PERSONAL.LegFeEg='1753-01-01 00:00:00.000' AND PERSONAL.LegNume IN ($getFicLega) ORDER BY pers_nombre ASC";

        $data =  arrayQueryDataMS($query); // traemos los datos del personal seleccionado en la lista

        foreach ($data as $value) { // recorremos los legajos seleccionados

            $nombre = $value['pers_nombre'];
            $legajo = $value['pers_legajo'];
            if (test_input($_POST['LegaPass']) == 'true') {
                $userauto   = $value['pers_legajo'];
                $contraauto = password_hash($value['pers_dni'], PASSWORD_DEFAULT);
            } else {
                $caract      = array(",", "-", ":", "|", ".", "´", ";", "ñ", "Ñ");
                $nombre_u    = str_replace($caract, "", $value['pers_nombre']);
                $userauto    = strtolower($ident_) . '-' . strtok(strtolower($nombre_u), " \n\t") . "-" . $legajo;
                $contraauto  = password_hash($userauto, PASSWORD_DEFAULT);
            }
            $recid = recid();
            /* INSERTAMOS */
            $query = "INSERT INTO usuarios (recid, nombre, usuario, rol, clave, cliente, legajo, fecha_alta, fecha ) VALUES( '$recid', '$nombre', '$userauto', '$rol', '$contraauto', '$id_c_', '$legajo','$fecha', '$fecha');"; // insertamos los datos
            if (insert_pdoQuery($query)) { // si se inserto correctamente
                $dataUser = simple_pdoQuery("SELECT usuarios.id AS 'id_user', roles.nombre AS 'nombre_rol' FROM usuarios INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.recid='$recid' ORDER BY usuarios.fecha_alta DESC LIMIT 1");
                auditoria("Usuario ($dataUser[id_user]) $userauto. Nombre: $nombre. Legajo ($legajo). Rol ($rol) $dataUser[nombre_rol]", 'A', $id_c_, '1');
            } else { // si no se inserto correctamente
                PrintRespuestaJson('error', "Error al importar");
                exit;
            }
        }
        $count       = count($data); // conteo de los legajos seleccionados
        $tiempo_fini = microtime(true); // tiempo final
        $duracion    = round($tiempo_fini - $tiempo_ini, 2); // tiempo de ejecucion
        PrintRespuestaJson('ok', "<b>Usuarios importados correctamente</b><br>Duración: $duracion Segundos.<br>Usuarios importados: $count"); // enviamos la respuesta
        exit; // salimos del script
}
/** FIN ALTA DE USUARIO */
