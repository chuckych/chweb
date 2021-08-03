<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

FusNuloPOST('submit', '');
/** MODIFICACION DE ROL*/
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'editarol')) {
    require __DIR__ . '../../../config/conect_mysql.php';
    $nombre       = test_input($_POST["nombre"]);
    $nombre_nuevo = test_input($_POST["nombre_nuevo"]);
    $id           = test_input($_POST["id"]);
    $recid_c      = test_input($_POST["recid_c"]);
    $fecha        = date("Y/m/d H:i:s");
    $UpdateRol = ($nombre === $nombre_nuevo) ? false : true;
    /* Comprobamos campos vacios  */
    if ((valida_campo($nombre_nuevo)) or (valida_campo($recid_c)) or (valida_campo($id))) {
        PrintRespuestaJson('error', 'Campo Requerido');
        exit;
    } else {
        if ($UpdateRol) {
            $CheckDuplicado = CountRegMayorCeroMySql("SELECT roles.nombre FROM roles INNER JOIN clientes ON roles.cliente=clientes.id WHERE roles.nombre='$nombre_nuevo' AND clientes.recid='$recid_c'");
            $CheckDuplicado = false;
            if ($CheckDuplicado) {
                PrintRespuestaJson('error', 'Ya existe Rol ' . $nombre_nuevo);
                exit;
            }
            $query = "UPDATE roles SET nombre='$nombre_nuevo', fecha='$fecha' WHERE id='$id'";
            if ((mysqli_query($link, $query))) {
                PrintRespuestaJson('ok', 'Datos Guardados');
                /** Si se Guardo con exito */
                mysqli_close($link);
                exit;
            } else {
                PrintRespuestaJson('error', mysqli_error($link));
                mysqli_close($link);
                exit;
            }
        } else {
            PrintRespuestaJson('nocambios', 'No Hay Cambios');
        }
    }
}
/** FIN MODIFICACION DE ROL */
/** BORRAR ROL */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'deleteRol')) {
    require __DIR__ . '../../../config/conect_mysql.php';
    $id     = test_input($_POST["id"]);
    $nombre = test_input($_POST["nombre"]);
    /* Comprobamos campos vacíos  */
    $query = "DELETE FROM roles WHERE roles.id='$id'";

    if ((mysqli_query($link, $query))) {
        PrintRespuestaJson('ok', 'Se eliminó el rol <span class="fw5">' . ($nombre) . '.</span>');
        /** Si se Guardo con exito */
        mysqli_close($link);
        exit;
    } elseif (mysqli_errno($link) == 1451) {
        PrintRespuestaJson('error', 'Existe Información en usuarios.');
        mysqli_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', mysqli_error($link));
        mysqli_close($link);
        exit;
    }
}
/** FIN BORRAR ROL */
/** ALTA DE ROL */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'addRol')) {
    require __DIR__ . '../../../config/conect_mysql.php';
    $nombre  = test_input($_POST["nombre"]);
    $recid_c = test_input($_POST["recid_c"]);
    $recid      = recid();
    $fecha      = date("Y/m/d H:i:s");
    /* Comprobamos campos vacios  */
    if ((valida_campo($nombre)) or (valida_campo($recid_c))) {
        PrintRespuestaJson('error', 'Campo Requerido');
        exit;
    } else {
        $CheckDuplicado = CountRegMayorCeroMySql("SELECT roles.nombre FROM roles INNER JOIN clientes ON roles.cliente=clientes.id WHERE roles.nombre='$nombre' AND clientes.recid='$recid_c'");

        if ($CheckDuplicado) {
            PrintRespuestaJson('error', 'Ya existe Rol ' . $nombre);
            exit;
        }

        $query = "SELECT clientes.id FROM clientes where clientes.recid = '$recid_c '";
        $rs = mysqli_query($link, $query);
        while ($row= mysqli_fetch_assoc($rs) ) {
            $cliente = $row['id'];
        }
        mysqli_free_result($rs);


        /* INSERTAMOS */
        $query = "INSERT INTO roles (recid, cliente, nombre, fecha_alta, fecha ) VALUES( '$recid', '$cliente', '$nombre', '$fecha', '$fecha')";
        if ((mysqli_query($link, $query))) {
            PrintRespuestaJson('ok', 'Datos Guardados');
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
/** FIN ALTA DE ROL */
