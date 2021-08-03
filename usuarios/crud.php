<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

FusNuloPOST('submit', '');

$border = $ErrNombre = $ErrUsuario = $ErrRol = $ErrContraseña = $duplicado = $nombre = $usuario = $rol = $contraseña = '';
$fecha = date("Y/m/d H:i:s");
/** ALTA DE USUARIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'alta')) {
    require __DIR__ . '../../config/conect_mysql.php';

    $a_nombre  = test_input($_POST["a_nombre"]);
    $a_usuario = test_input($_POST["a_usuario"]);
    $a_legajo  = test_input($_POST["a_legajo"]);
    $a_rol     = test_input($_POST["a_rol"]);
    $a_recid   = test_input($_POST["a_recid"]);

    $query = "SELECT clientes.id as 'id', clientes.ident as 'ident' FROM clientes WHERE recid='$a_recid'";
    $stmt  = mysqli_query($link, $query);
    while ($row = mysqli_fetch_assoc($stmt)) {
        $ident   = $row['ident'];
        $cliente = $row['id'];
    }
    mysqli_free_result($stmt);
    $userauto    = (empty($a_usuario)) ? strtolower($ident) . '-' . strtok(strtolower($a_nombre), " \n\t") . "-" . sprintf("%04d", rand(0, 9999)) : strtolower($ident) . '-' . $a_usuario . "-" . sprintf("%04d", rand(0, 9999));
    $contraseña  = '';
    $contraauto  = password_hash($userauto, PASSWORD_DEFAULT);
    $contraseña1 = (empty($contraseña)) ? $contraauto : password_hash($contraseña, PASSWORD_DEFAULT);
    $recid       = recid();
    /* Comprobamos campos vacíos  */
    // if ((valida_campo($nombre)) or (valida_campo($usuario)) or (valida_campo($rol)) or (valida_campo($contraseña))) {
    if ((valida_campo($a_nombre) or (valida_campo($cliente) or (valida_campo($a_rol))))) {
        PrintRespuestaJson('error', 'Campos Requeridos');
        exit;
    } else {
        /* INSERTAMOS */
        $query = "INSERT INTO usuarios (recid, nombre, usuario, rol, clave, cliente, legajo, fecha_alta, fecha ) VALUES ( '$recid', '$a_nombre', '$userauto', '$a_rol', '$contraseña1', '$cliente', '$a_legajo','$fecha', '$fecha')";

        if ((mysqli_query($link, $query))) {
            PrintRespuestaJson('ok', 'Usuario creado correctamente');
            /** Si se Guardo con exito */
            mysqli_close($link);
            exit;
        } elseif (mysqli_errno($link) == 1062) {
            PrintRespuestaJson('error', 'Nombre de usuario ya existe');
            mysqli_close($link);
            exit;
        } elseif (mysqli_errno($link) == 1452) {
            PrintRespuestaJson('error', 'No existe el Rol');
            mysqli_close($link);
            exit;
        } else {
            PrintRespuestaJson('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
}
/** FIN ALTA DE USUARIO */
/** MODIFICACIÓN DE USUARIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'editar')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $e_nombre  = test_input($_POST["e_nombre"]);
    $e_usuario = test_input($_POST["e_usuario"]);
    $e_legajo  = test_input($_POST["e_legajo"]);
    $e_rol     = test_input($_POST["e_rol"]);
    $e_uid     = test_input($_POST["e_uid"]);
    /* Comprobamos campos vacíos  */
    if ((valida_campo($e_nombre)) or (valida_campo($e_usuario)) or (valida_campo($e_rol))) {
        PrintRespuestaJson('error', 'Campos Requeridos');
        exit;
    } else {
        /* UPDATE USUARIO */
        $query = "UPDATE usuarios SET nombre='$e_nombre', usuario='$e_usuario', legajo='$e_legajo', rol='$e_rol', fecha='$fecha' WHERE id ='$e_uid'";
        $stmt = mysqli_query($link, $query);
        if (($stmt)) {
            PrintRespuestaJson('ok', 'Datos Guardados');
            /** Si se Guardo con exito */
            mysqli_close($link);
            exit;
        } elseif (mysqli_errno($link) == 1062) {
            PrintRespuestaJson('error', 'Nombre de usuario ya existe');
            mysqli_close($link);
            exit;
        } elseif (mysqli_errno($link) == 1452) {
            PrintRespuestaJson('error', 'No existe el Rol');
            mysqli_close($link);
            exit;
        } else {
            PrintRespuestaJson('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
}
/** FIN MODIFICACIÓN DE USUARIO */
/** MODIFICACIÓN DE ESTADO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'estado')) {
    require __DIR__ . '../../config/conect_mysql.php';

    $id         = test_input($_POST["uid"]);
    $nombre     = test_input($_POST["nombre"]);
    $estado     = (test_input($_POST["estado"]) == 0) ? '1' : '0';
    $textEstado = (test_input($_POST["estado"]) == 0) ? 'inhabilitó' : 'habilitó';

    $query = "UPDATE usuarios SET usuarios.estado='$estado', usuarios.fecha='$fecha' WHERE usuarios.id='$id'";

    if ((mysqli_query($link, $query))) {
        PrintRespuestaJson('ok', 'Se '.$textEstado.' el usuario <span class="fw5">' . test_input($_POST["nombre"]) . '.</span>');
        /** Si se Guardo con exito */
        mysqli_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', mysqli_error($link));
        mysqli_close($link);
        exit;
    }
}
/** FIN MODIFICACIÓN DE ESTADO */
/** BORRAR DE USUARIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'delete')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $id     = test_input($_POST["uid"]);
    $nombre = test_input($_POST["nombre"]);
    /* Comprobamos campos vacíos  */
    $query = "DELETE FROM usuarios WHERE usuarios.id='$id'";

    if ((mysqli_query($link, $query))) {
        PrintRespuestaJson('ok', 'Se eliminó el usuario <span class="fw5">' . ($nombre) . '.</span>');
        /** Si se Guardo con exito */
        mysqli_close($link);
        exit;
    } elseif (mysqli_errno($link) == 1451){
        PrintRespuestaJson('error', 'Existe Información en usuarios.');
        mysqli_close($link);
        exit;
    }else{
        PrintRespuestaJson('error', mysqli_error($link));
        mysqli_close($link);
        exit;
    }
}
/** FIN BORRAR DE USUARIO */
/** RESETEAR CONTRASEÑA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'key')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $usuario    = test_input($_POST["usuario"]);
    $nombre     = urlencode(test_input($_POST["nombre"]));
    $contraauto = password_hash($usuario, PASSWORD_DEFAULT);
    $uid      = test_input($_POST["uid"]);
    $fecha      = date("Y/m/d H:i:s");
    /* Comprobamos campos vacíos  */
    if ((valida_campo($uid)) or (valida_campo($usuario)) or (valida_campo($nombre))) {
        PrintRespuestaJson('error', 'Campos Requeridos');
        exit;
    } else {
        // sleep(2);
        $query = "UPDATE usuarios SET clave = '$contraauto', fecha = '$fecha' WHERE id = '$uid'";
        if ((mysqli_query($link, $query))) {

            unset($_SESSION["HASH_CLAVE"]);
            $_SESSION["HASH_CLAVE"] = ($contraauto);

            PrintRespuestaJson('ok', 'Clave de <span class="fw5">' . test_input($_POST["nombre"]) . '</span> generada correctamente.<br />Su nueva clave es: <span class="fw5">' . $usuario . '</span>');
            /** Si se Guardo con exito */
            mysqli_close($link);
            exit;
        } else {
            PrintRespuestaJson('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
}
/** FIN RESETEAR CONTRASEÑA */
