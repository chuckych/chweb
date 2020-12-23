<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', '1');

FusNuloPOST('submit', '');
/** ALTA DE CLIENTE */
$fecha = date("Y/m/d H:i:s");
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'AltaCuenta')) {

    require __DIR__ . '../../../config/conect_mysql.php';

    $nombre     = test_input($_POST['nombre']);
    $ident      = test_input($_POST['ident']);
    $n_ident    = str_replace(" ", "", $nombre);
    $tkmobile   = test_input($_POST['tkmobile']);
    $WebService = test_input($_POST['WebService']);
    $identauto  = (empty($ident)) ? substr(strtoupper($n_ident), 0, 3) : $ident;

    $CheckDuplicado = CountRegMayorCeroMySql("SELECT clientes.ident FROM clientes WHERE clientes.ident='$identauto'");
    if ($CheckDuplicado) {
        $identauto  = Ident();
    }
    // $identauto = str_replace(" ", "", $identauto);
    $auth  = empty($_POST['auth']) ? '0' : '1';
    $host  = test_input($_POST['host']);
    $db    = test_input($_POST['db']);
    $user  = test_input($_POST['user']);
    $pass  = test_input($_POST['pass']);
    $recid = recid();

    /* Comprobamos campos vacíos  */
    if ((valida_campo($nombre))) {
        PrintRespuestaJson('error', 'Campo Nombre de Cuenta Requerido');
        exit;
    } else {
        /* INSERTAMOS CLIENTE EN TABLA CLIENTES */
        $query = "INSERT INTO clientes (recid, ident, nombre, host, db, user, pass, auth, tkmobile, WebService, fecha_alta, fecha ) VALUES( '$recid', '$identauto','$nombre', '$host', '$db', '$user', '$pass', '$auth', '$tkmobile', '$WebService','$fecha', '$fecha')";
        $rs_insert = mysqli_query($link, $query);
 
        if ($rs_insert) {
            PrintRespuestaJson('ok', 'Cuenta Creada');
            /** Si se Guardo con exito */
            mysqli_close($link);
            exit;
        } elseif (mysqli_errno($link) == 1062) {
            PrintRespuestaJson('error', 'Ya existe una cuenta con el nombre: '.$nombre);
            mysqli_close($link);
            exit;
        } else {
            PrintRespuestaJson('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
}
/** FIN ALTA DE CLIENTE */
/** MODIFICACION DE CLIENTE */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'EditCuenta')) {
    require __DIR__ . '../../../config/conect_mysql.php';

    $nombre     = test_input($_POST['nombre']);
    $host       = test_input($_POST['host']);
    $db         = test_input($_POST['db']);
    $user       = test_input($_POST['user']);
    $pass       = test_input($_POST['pass']);
    $tkmobile   = test_input($_POST['tkmobile']);
    $WebService = test_input($_POST['WebService']);
    $auth       = empty($_POST['auth']) ? '0' : '1';
    $recid      = test_input($_POST['recid']);
    
    /* Comprobamos campos vacios  */
    if ((valida_campo($nombre))) {
        PrintRespuestaJson('error', 'Campo Nombre de Cuenta Requerido');
        exit;
    } else {

        $query="UPDATE clientes SET nombre='$nombre', host='$host', db='$db', user='$user', pass='$pass', auth='$auth', tkmobile='$tkmobile', WebService='$WebService', fecha='$fecha' WHERE recid='$recid' ";

        $rs = mysqli_query($link, $query);
        if ($rs) {
            PrintRespuestaJson('ok', 'Cuenta Modificada');
            /** Si se Guardo con exito */
            mysqli_close($link);
            exit;
        } elseif (mysqli_errno($link) == 1062) {
            PrintRespuestaJson('error', 'Ya existe una cuenta con el nombre: '.$nombre);
            mysqli_close($link);
            exit;
        } else {
            PrintRespuestaJson('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
    }
}
/** FIN MODIFICACION DE CLIENTE */
/** BORRAR DE CLIENTE */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'trash')) {
    require __DIR__ . '../../../config/conect_mysql.php';
    $recid      = test_input($_POST["recid"]);
    // $recid_c     = test_input($_POST["recid_c"]);
    /* Comprobamos campos vacios  */
    $query = "DELETE FROM clientes WHERE clientes.recid='$recid'";
    $rs_insert = mysqli_query($link, $query);
    mysqli_error($link);
    if (mysqli_errno($link) == 1451) {
        $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Existe Información en usuarios</div>";
    } else {
        header("Location:/" . HOMEHOST . "/usuarios/clientes/");
    }
    mysqli_close($link);
}
/** FIN BORRAR DE CLIENTE */
