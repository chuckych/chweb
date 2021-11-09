<?php
ini_set('max_execution_time', 300);
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
ExisteModRol('7');
E_ALL();
/** Tiempo maximo de duracion del script 300 segundos=(5 Minutos) */
$border = $ErrNombre = $ErrUsuario = $ErrRol = $ErrContraseña = $duplicado = $nombre = $usuario = $rol = $contraseña = $Errl = '';
/** ALTA DE USUARIO */
$_POST['LegaPass'] = $_POST['LegaPass'] ?? '';
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'Importar')) {

    $rol    = test_input($_POST['rol']);
    $id_c_  = test_input($_POST['id_c']);
    $ident_ = test_input($_POST['ident']);

    $fecha  = date("Y/m/d H:i:s");
    /* Comprobamos campos vacios  */
    if (empty($_POST['_l'])) {
        PrintRespuestaJson('error', 'Debe seleccionar al menos un legajo');
        exit;
    }
    if (valida_campo($rol)) {
        PrintRespuestaJson('error', 'Campo Rol es requerido');
        exit;
    }
        $tiempo_ini = microtime(true);
        $url   = host() . "/" . HOMEHOST . "/data/getImpoPerso.php?tk=" . token() . "&_c=" . $_POST['_c'] . "&_l%5B%5D%3D=" . implode("&_l%5B%5D%3D=", $_POST['_l']);

        $array = json_decode(getRemoteFile($url), true);
        if (is_array($array)) :
            $rowcount = (count($array['impo_personal']));
        endif;
        $data = $array['impo_personal'];

        foreach ($data as $value) {

            $nombre     = $value['n'];
            $legajo  = $value['l'];
            if (test_input($_POST['LegaPass']) == 'true') {
                $userauto   = $value['l'];
                $contraauto = password_hash($value['d'], PASSWORD_DEFAULT);
            } else {
                $caract      = array(",", "-", ":", "|", ".", "´", ";", "ñ", "Ñ");
                $nombre_u    = str_replace($caract, "", $value['n']);
                $userauto    = strtolower($ident_) . '-' . strtok(strtolower($nombre_u), " \n\t") . "-" . $legajo;
                $contraauto  = password_hash($userauto, PASSWORD_DEFAULT);
            }

            $recid       = recid();

            /* INSERTAMOS */
            $query = "INSERT INTO usuarios (recid, nombre, usuario, rol, clave, cliente, legajo, fecha_alta, fecha ) VALUES( '$recid', '$nombre', '$userauto', '$rol', '$contraauto', '$id_c_', '$legajo','$fecha', '$fecha');";
            // $rs_insert = mysqli_query($link, $query); // or die(mysqli_error($link));
            if (insert_pdoQuery($query)) {
                $dataUser = simple_pdoQuery("SELECT usuarios.id AS 'id_user', roles.nombre AS 'nombre_rol' FROM usuarios INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.recid='$recid' ORDER BY usuarios.fecha_alta DESC LIMIT 1");
                auditoria("Usuario ($dataUser[id_user]) $userauto. Nombre: $nombre. Legajo ($legajo). Rol ($rol) $dataUser[nombre_rol]", 'A', $id_c_, '1');
            } else {
                PrintRespuestaJson('error', "Error al importar");
                exit;
            }
        }
        $count       = count($data);
        $tiempo_fini = microtime(true);
        $duracion    = round($tiempo_fini - $tiempo_ini, 2);
        PrintRespuestaJson('ok', "<b>Usuarios importados correctamente</b><br>Duración: $duracion Segundos.<br>Usuarios importados: $count");
        exit;
}
/** FIN ALTA DE USUARIO */
