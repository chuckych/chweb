<?php
$border = $ErrNombre = $duplicado = $nombre = $auth = $host = $db = $user = $pass = $ident = $identauto = $tkmobile = '';
/** ALTA DE CLIENTE */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'alta')) {
    require __DIR__ . '../../../config/conect_mysql.php';
       $nombre    = test_input($_POST['nombre']);
       $ident     = test_input($_POST['ident']);
       $n_ident   = str_replace(" ", "", $nombre);
       $tkmobile  = test_input($_POST['tkmobile']);
       $WebService  = test_input($_POST['WebService']);
       $identauto = (empty($ident)) ? substr(strtoupper($n_ident), 0, 3) : $ident;
    // $identauto = str_replace(" ", "", $identauto);

         $auth = empty($_POST['auth']) ? '0' : '1';
         $host = test_input($_POST['host']);
           $db = test_input($_POST['db']);
         $user = test_input($_POST['user']);
         $pass = test_input($_POST['pass']);
        $recid = recid();
        $fecha = date("Y/m/d H:i:s");
       $border = 'border border-danger';
    /* Comprobamos campos vacíos  */
    // if ((valida_campo($nombre)) or (valida_campo($usuario)) or (valida_campo($rol)) or (valida_campo($contraseña))) {
    if ((valida_campo($nombre))) {
        $ErrNombre = (valida_campo($nombre)) ? $border : '';
        $collapse = (valida_campo($nombre)) ? '' : 'collapse';
    } else {
        /* INSERTAMOS CLIENTE EN TABLA CLIENTES */
        $query = "INSERT INTO clientes (recid, ident, nombre, host, db, user, pass, auth, tkmobile, WebService, fecha_alta, fecha ) VALUES( '$recid', '$identauto','$nombre', '$host', '$db', '$user', '$pass', '$auth', '$tkmobile', '$WebService','$fecha', '$fecha')";
        $rs_insert = mysqli_query($link, $query);
        // mysqli_error($link);
        // print_r(mysqli_error_list($link)).PHP_EOL;
        // print_r($query);
        // exit;
        if (mysqli_errno($link) == 1062) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>".mysqli_error($link)."</div>";
        } else{
            header("Location:index.php");
        }
    }
    mysqli_close($link);
}
/** FIN ALTA DE CLIENTE */
/** MODIFICACION DE CLIENTE */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'editar')) {
    require __DIR__ . '../../../config/conect_mysql.php';
        $nombre = test_input($_POST['nombre']);
          $host = test_input($_POST['host']);
            $db = test_input($_POST['db']);
          $user = test_input($_POST['user']);
          $pass = test_input($_POST['pass']);
      $tkmobile = test_input($_POST['tkmobile']);
    $WebService = test_input($_POST['WebService']);
          $auth = empty($_POST['auth']) ? '0' : '1';
         $recid = test_input($_POST['recid']);
         $fecha = date("Y/m/d H:i:s");
        $border = 'border border-danger';
    /* Comprobamos campos vacios  */
    if ((valida_campo($nombre))) {
        $ErrNombre     = (valida_campo($nombre)) ? $border : '';
    } else {
      
        $query = "UPDATE clientes SET
            nombre = '$nombre',
              host = '$host',
                db = '$db',
              user = '$user',
              pass = '$pass',
              auth = '$auth',
          tkmobile = '$tkmobile',
        WebService = '$WebService',
           fecha = '$fecha'
        WHERE recid='$recid'
        ";
        $rs_insert = mysqli_query($link, $query);
        mysqli_error($link);
        if (mysqli_errno($link) == 1062) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Nombre de usuario duplicado</div>";
        } elseif(mysqli_errno($link) == 1452) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>No existe el rol</div>";
        }else{
            header("Location:index.php");
        }
    }
    mysqli_close($link);
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