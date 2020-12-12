<?php
$border = $ErrNombre = $ErrUsuario = $ErrRol = $ErrContraseña = $duplicado = $nombre = $usuario = $rol = $contraseña = '';
/** ALTA DE USUARIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'alta')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $nombre  = test_input($_POST["nombre"]);
    $usuario = test_input($_POST["usuario"]);
    $cliente = test_input($_POST["cliente"]);
    $ident   = test_input($_POST["ident"]);
    // $recid_c     = test_input($_POST["recid_c"]);
    // $userauto    = (!empty($nombre)) ? strtok(strtolower($nombre), " \n\t")."-".sprintf( "%04d", rand(0,9999)) : '';
    $userauto    = (empty($usuario)) ? strtolower($ident).'-'.strtok(strtolower($nombre), " \n\t")."-".sprintf( "%04d", rand(0,9999)) : strtolower($ident).'-'.$usuario."-".sprintf( "%04d", rand(0,9999));
    $rol         = test_input($_POST["rol"]);
    $contraseña  = test_input($_POST["contraseña"]);
    $contraauto  = password_hash($userauto, PASSWORD_DEFAULT);
    $contraseña1 = (empty($contraseña)) ? $contraauto : password_hash($contraseña, PASSWORD_DEFAULT);
    $recid       = recid();
    $fecha       = date("Y/m/d H:i:s");
    $border      = 'border border-danger';
    /* Comprobamos campos vacíos  */
    // if ((valida_campo($nombre)) or (valida_campo($usuario)) or (valida_campo($rol)) or (valida_campo($contraseña))) {
    if ((valida_campo($nombre) or (valida_campo($cliente) or (valida_campo($rol))))) {
        $ErrNombre     = (valida_campo($nombre)) ? $border : '';
        $ErrCliente     = (valida_campo($cliente)) ? $border : '';
        // $ErrUsuario    = (valida_campo($usuario)) ? $border : '';
        $ErrRol        = (valida_campo($rol)) ? $border : '';
        // $ErrContraseña = (valida_campo($contraseña)) ? $border : '';
    } else {
        /* INSERTAMOS */
        $query = "INSERT INTO usuarios (recid, nombre, usuario, rol, clave, cliente, fecha_alta, fecha ) VALUES( '$recid', '$nombre', '$userauto', '$rol', '$contraseña1', '$cliente', '$fecha', '$fecha')";
        $rs_insert = mysqli_query($link, $query);
        mysqli_error($link);
        // print_r($query);
        // print_r(mysqli_error_list($link));
        
        if (mysqli_errno($link) == 1062) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>".mysqli_error($link)."</div>";
        } elseif(mysqli_errno($link) == 1452) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Error: ".mysqli_errno($link)."<br />".mysqli_error($link)."</div>";
        }else{
            header("Location:/" . HOMEHOST . "/usuarios/?_c=$_GET[_c]&alta");
        }
    }
    mysqli_close($link);
}
/** FIN ALTA DE USUARIO */
/** MODIFICACIÓN DE USUARIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'editar')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $nombre     = test_input($_POST["nombre"]);
    $usuario    = test_input($_POST["usuario"]);
    $cliente    = test_input($_POST["cliente"]);
    $rol        = test_input($_POST["rol"]);
    $recid      = test_input($_POST["recid"]);
    // $recid_c      = test_input($_POST["recid_c"]);
    $fecha      = date("Y/m/d H:i:s");
    $border     = 'border border-danger';
    /* Comprobamos campos vacíos  */
    if ((valida_campo($nombre)) or (valida_campo($usuario)) or (valida_campo($rol)) or (valida_campo($cliente))) {
        $ErrNombre     = (valida_campo($nombre)) ? $border : '';
        $ErrCliente     = (valida_campo($cliente)) ? $border : '';
        $ErrUsuario    = (valida_campo($usuario)) ? $border : '';
        $ErrRol        = (valida_campo($rol)) ? $border : '';
    } else {
        /* UPDATE USUARIO */
        $query = "UPDATE usuarios SET
        nombre      = '$nombre',
        usuario     = '$usuario',
        rol         = '$rol',
        cliente     = '$cliente',
        fecha       = '$fecha'
        WHERE recid = '$recid'
        ";
        $rs_insert = mysqli_query($link, $query);
        // print_r($query);
        // print_r(mysqli_error_list($link));
        mysqli_error($link);
        if (mysqli_errno($link) == 1062) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Nombre de usuario duplicado</div>";
        } elseif(mysqli_errno($link) == 1452) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>No existe el rol</div>";
        }else{
            header("Location:/" . HOMEHOST . "/usuarios/?_c=$_GET[_c]&truem=$nombre");
        }
    }
    mysqli_close($link);
}
/** FIN MODIFICACIÓN DE USUARIO */
/** MODIFICACIÓN DE ESTADO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'modestado')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $recid        = test_input($_POST["recid_e"]);
    $nombre       = test_input($_POST["nombre"]);
    $fecha        = date("Y/m/d H:i:s");
    $cambio_estado = (test_input($_POST["cambioestado"]) == 0) ? '1' : '0';
    $query = "UPDATE usuarios SET usuarios.estado='$cambio_estado', usuarios.fecha='$fecha' WHERE usuarios.recid='$recid' ";
    $rs_insert = mysqli_query($link, $query);
    header("Location:/" . HOMEHOST . "/usuarios/?_c=$_GET[_c]&truee=$nombre&e=$cambio_estado");
    mysqli_close($link);
}
/** FIN MODIFICACIÓN DE ESTADO */
/** BORRAR DE USUARIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'trash')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $recid      = test_input($_POST["recid"]);
    $nombre     = urlencode(test_input($_POST["nombre"]));
    /* Comprobamos campos vacíos  */
        $query = "DELETE FROM usuarios WHERE usuarios.recid='$recid'";
        $rs_insert = mysqli_query($link, $query);
        mysqli_error($link);
        if (mysqli_errno($link) == 1451) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Existe Información en usuarios</div>";
        } else {
            header("Location:/" . HOMEHOST . "/usuarios/?_c=$_GET[_c]&trued=$nombre");
        }
    mysqli_close($link);
}
/** FIN BORRAR DE USUARIO */
/** RESETEAR CONTRASEÑA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'key')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $usuario    = test_input($_POST["usuario"]);
    $nombre     = urlencode(test_input($_POST["nombre"]));
    $contraauto = password_hash($usuario, PASSWORD_DEFAULT);
    $recid      = test_input($_POST["recid"]);
    $fecha      = date("Y/m/d H:i:s");
    /* Comprobamos campos vacíos  */
    if ((valida_campo($recid))) {

    } else {
        $query = "UPDATE usuarios SET clave = '$contraauto', fecha = '$fecha' WHERE recid = '$recid'";
        $rs_update = mysqli_query($link, $query);
        // print_r($query);exit;

        if($rs_update){
            unset($_SESSION["HASH_CLAVE"]);
            $_SESSION["HASH_CLAVE"] = ($contraauto);
            header("Location:/" . HOMEHOST . "/usuarios/?_c=$_GET[_c]&true=$nombre");
        }
    }
    mysqli_close($link);
}
/** FIN RESETEAR CONTRASEÑA */
