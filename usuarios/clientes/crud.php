<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

FusNuloPOST('submit', '');
/** ALTA DE CLIENTE */
$fecha = date("Y/m/d H:i:s");
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'AltaCuenta')) {

    // require __DIR__ . '../../../config/conect_mysql.php';

    $AppCode      = test_input($_POST['AppCode']);
    $nombre       = test_input($_POST['nombre']);
    $ApiMobileHRP = test_input($_POST['ApiMobileHRP']);
    $ident        = test_input($_POST['ident']);
    $n_ident      = str_replace(" ", "", $nombre);
    $tkmobile     = test_input($_POST['tkmobile']);
    $WebService   = test_input($_POST['WebService']);
    $localCH = test_input($_POST['localCH'] ?? '0');
    $ApiMobileHRPApp = test_input($_POST['ApiMobileHRPApp'] ?? '');
    $identauto    = (empty($ident)) ? substr(strtoupper($n_ident), 0, 3) : $ident;

    $hostCHWeb = ($_POST['hostCHWeb']);
    $hostCHWeb =  escape_sql_wild($hostCHWeb);

    (valida_campo($nombre)) ? PrintRespuestaJson('error', 'Campo Nombre de Cuenta Requerido') . exit : '';
    (valida_campo($hostCHWeb)) ? PrintRespuestaJson('error', 'Campo Host de Cuenta Requerido') . exit : '';

    if ($AppCode) {
        if (strlen($AppCode) < 8) {
            PrintRespuestaJson('error', 'Campo App Code debe ser de 8 digitos');
            exit;
        }
    }

    $CheckDuplicado = count_pdoQuery("SELECT clientes.ident FROM clientes WHERE clientes.ident='$identauto'");
    if ($CheckDuplicado) {
        $identauto  = Ident();
    }
    // $identauto = str_replace(" ", "", $identauto);
    $auth  = empty($_POST['auth']) ? '0' : '1';
    $host  = ($_POST['host']);
    $host  = escape_sql_wild($host);
    $db    = test_input($_POST['db']);
    $user  = test_input($_POST['user']);
    $pass  = test_input($_POST['pass']);
    $recid = (!empty($AppCode)) ? $AppCode : recid();

    $tkmobilehrp = sha1($recid);

    if (count_pdoQuery("SELECT 1 FROM clientes where nombre = '$nombre' LIMIT 1")) {
        PrintRespuestaJson('error', 'Ya existe una cuenta con el nombre: ' . $nombre);
        exit;
    }
    if (count_pdoQuery("SELECT 1 FROM clientes where ident = '$identauto' LIMIT 1")) {
        PrintRespuestaJson('error', "Identificador de cuenta ya existe. Ingrese otro por favor");
        exit;
    }
    /* Comprobamos campos vacíos  */
    /* INSERTAMOS CLIENTE EN TABLA CLIENTES */
    $query = "INSERT INTO clientes (recid, ident, nombre, host, db, user, pass, auth, tkmobile, WebService, ApiMobileHRP, fecha_alta, localCH, UrlAppMobile, fecha ) VALUES( '$recid', '$identauto','$nombre', '$host', '$db', '$user', '$pass', '$auth', '$tkmobile', '$WebService', '$ApiMobileHRP', '$fecha', '$localCH', '$ApiMobileHRPApp', '$fecha')";
    $rs_insert = pdoQuery($query);

    if ($rs_insert) {

        $r = simple_pdoQuery("SELECT id, nombre FROM clientes where recid = '$recid'");
        $readHost = simple_pdoQuery("SELECT * FROM `params` where `descripcion` = 'host' and `modulo` = 1 and `cliente` = '$r[id]'");
        if ($readHost) {
            $query = "UPDATE `params` SET `valores` = '$hostCHWeb' WHERE `params`.`descripcion` = 'host' and `params`.`modulo` = 1 and `params`.`cliente` = '$r[id]'";
            $rs = pdoQuery($query);
        } else {
            $query = "INSERT INTO `params` (`descripcion`, `valores`, `modulo`, `cliente`) VALUES ('host', '$hostCHWeb', 1, '$r[id]')";
            $rs = pdoQuery($query); // Insertamos el host de CHWeb
        }
        PrintRespuestaJson('ok', 'Cuenta Creada');
        /** Si se Guardo con exito */
        $audCuenta = simple_pdoQuery("SELECT clientes.id FROM clientes WHERE clientes.nombre = '$nombre' LIMIT 1");
        auditoria("Cuenta ($nombre)", '1', $audCuenta['id'], '1');
        write_apiKeysFile();
        // mysqli_close($link);
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
    }
}
/** FIN ALTA DE CLIENTE */
/** MODIFICACION DE CLIENTE */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'EditCuenta')) {
    // require __DIR__ . '../../../config/conect_mysql.php';
    $_POST['hostCHWeb'] = $_POST['hostCHWeb'] ?? '';

    $nombre          = test_input($_POST['nombre']);
    $AppCode         = test_input($_POST['AppCode']);
    $ApiMobileHRP    = test_input($_POST['ApiMobileHRP']);
    $host            = ($_POST['host']);
    $hostCHWeb       = ($_POST['hostCHWeb']);
    $hostCHWeb       = escape_sql_wild($hostCHWeb);
    $host            = escape_sql_wild($host);
    $db              = test_input($_POST['db']);
    $user            = test_input($_POST['user']);
    $pass            = test_input($_POST['pass']);
    $tkmobile        = test_input($_POST['tkmobile']);
    $WebService      = test_input($_POST['WebService']);
    $localCH         = test_input($_POST['localCH'] ?? '0');
    $ApiMobileHRPApp = test_input($_POST['ApiMobileHRPApp'] ?? '');
    $auth            = empty($_POST['auth']) ? '0' : '1';
    $recid           = test_input($_POST['recid']);
    $recid2          = test_input($_POST['recid']);
    $recid = (!empty($AppCode)) ? $AppCode : $recid2;
    /* Comprobamos campos vacios  */
    (valida_campo($nombre)) ? PrintRespuestaJson('error', 'Campo Nombre de Cuenta Requerido') . exit : '';
    (valida_campo($hostCHWeb)) ? PrintRespuestaJson('error', 'Campo Host de Cuenta Requerido') . exit : '';

    if ($recid) {
        if (strlen($recid) < 8) {
            PrintRespuestaJson('error', 'Campo App Code debe ser de 8 digitos');
            exit;
        }
    }

    if (count_pdoQuery("SELECT 1 FROM clientes where nombre = '$nombre' and recid != '$recid2' LIMIT 1")) {
        PrintRespuestaJson('error', 'Ya existe una cuenta con el nombre: ' . $nombre);
        exit;
    }

    $query = "UPDATE clientes SET nombre='$nombre', host='$host', db='$db', user='$user', pass='$pass', auth='$auth', tkmobile='$tkmobile', WebService='$WebService', ApiMobileHRP = '$ApiMobileHRP', localCH = '$localCH', UrlAppMobile = '$ApiMobileHRPApp', fecha='$fecha', recid='$recid' WHERE recid='$recid2'";

    $rs = pdoQuery($query);
    if ($rs) {

        $recid2 = ($recid2 != $recid) ? $recid : $recid2; // Si el appcode es distinto al recid. El recid2 es el Appcode

        $r = simple_pdoQuery("SELECT id, nombre FROM clientes where recid = '$recid2'");
        $readHost = simple_pdoQuery("SELECT * FROM `params` where `descripcion` = 'host' and `modulo` = 1 and `cliente` = '$r[id]'");
        if ($readHost) {
            $query = "UPDATE `params` SET `valores` = '$hostCHWeb' WHERE `params`.`descripcion` = 'host' and `params`.`modulo` = 1 and `params`.`cliente` = '$r[id]'";
            $rs = pdoQuery($query);
            PrintRespuestaJson('ok', 'Cuenta Editada');
        } else {
            $query = "INSERT INTO `params` (`descripcion`, `valores`, `modulo`, `cliente`) VALUES ('host', '$hostCHWeb', 1, '$r[id]')";
            $rs = pdoQuery($query); // Insertamos el host de CHWeb
            PrintRespuestaJson('ok', 'Cuenta Editada');
        }
        /** Si se Guardo con exito */
        auditoria("Cuenta ($nombre). AppCode: $recid2", '3', $r['id'], '1');
        write_apiKeysFile();
        exit;
    } else {
        PrintRespuestaJson('error', 'Error');
        exit;
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
