<?php
$border = $ErrNombre = $duplicado = $nombre = '';
/** ALTA DE ROL */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'alta')) {
    require __DIR__ . '../../../config/conect_mysql.php';
    $nombre  = test_input($_POST["nombre"]);
    $cliente = test_input($_POST["cliente"]);
    // $recid_c     = test_input($_POST["recid_c"]);
    $recid      = recid();
    $fecha      = date("Y/m/d H:i:s");
    $border     = 'border border-danger';
    /* Comprobamos campos vacios  */
    if ((valida_campo($nombre))) {
        $ErrNombre     = (valida_campo($nombre)) ? $border : '';
    } else {
        $query = "SELECT roles.nombre FROM roles WHERE roles.cliente='$cliente' AND roles.nombre = '$nombre'";
        $rs = mysqli_query($link, $query);
        $CountRolC = mysqli_num_rows($rs);
        // print_r($CountRolC);
        if ($CountRolC == 0) {
            /* INSERTAMOS */
            $query = "INSERT INTO roles (recid, cliente, nombre, fecha_alta, fecha ) VALUES( '$recid', '$cliente', '$nombre', '$fecha', '$fecha')";
            $rs_insert = mysqli_query($link, $query);
            // mysqli_error($link);
            if (mysqli_errno($link) == 1062) {
                $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Nombre de Rol</div>";
            } else {
                header("Location:/" . HOMEHOST . "/usuarios/roles/?_c=$_GET[_c]&alta");
            }
            mysqli_free_result($rs);
        } else {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Ya existe un Rol con el nombre $nombre</div>";
        }
    }
    mysqli_close($link);
}
/** FIN ALTA DE ROL */
/** MODIFICACION DE ROL */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'editar')) {
    require __DIR__ . '../../../config/conect_mysql.php';
    $nombre  = test_input($_POST["nombre"]);
    $nombre2 = test_input($_POST["nombre2"]);
    $recid   = test_input($_POST["recid"]);
    $cliente = test_input($_POST["cliente"]);
    // $recid_c     = test_input($_POST["recid_c"]);
    $fecha      = date("Y/m/d H:i:s");
    $border     = 'border border-danger';
    ($nombre === $nombre2) ? header("Location:/" . HOMEHOST . "/usuarios/roles/?_c=$_GET[_c]&alta") : '';
    /* Comprobamos campos vacios  */
    if ((valida_campo($nombre))) {
        $ErrNombre     = (valida_campo($nombre)) ? $border : '';
    } else {
        $query = "SELECT roles.nombre FROM roles WHERE roles.cliente='$cliente' AND roles.nombre = '$nombre'";
        $rs = mysqli_query($link, $query);
        $CountRolC = mysqli_num_rows($rs);
        // print_r($CountRolC);
        if ($CountRolC == 0) {
            $query = "UPDATE roles SET
        nombre  = '$nombre',
        fecha   = '$fecha'
        WHERE recid='$recid'
        ";
            $rs_insert = mysqli_query($link, $query);
            mysqli_error($link);
            if (mysqli_errno($link) == 1062) {
                $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Nombre de usuario duplicado</div>";
            } else {
                header("Location:/" . HOMEHOST . "/usuarios/roles/?_c=$_GET[_c]&alta");
            }
            mysqli_free_result($rs);
        } else {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Ya existe un Rol con el nombre $nombre</div>";
        }
    }
    mysqli_close($link);
}
/** BORRAR DE ROL */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'trash')) {
    $recid = test_input($_POST["recid"]);

    $url = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid_c=" . $_GET['_c'] . "&recid=" . $recid;
    echo $url;
    $json = file_get_contents($url);
    $array = json_decode($json, TRUE);
    if (is_array($array)) :
        $rowcount = (count($array[0]['roles']));
    endif;
    $data_rol = $array[0]['roles'];
    if (is_array($data_rol)) :
        foreach ($data_rol as $value) :
            $cant_roles     = $value['cant_roles'];
            $cant_modulos   = $value['cant_modulos'];
            $cant_sectores  = $value['cant_sectores'];
            $cant_grupos    = $value['cant_grupos'];
            $cant_plantas   = $value['cant_plantas'];
            $cant_sucur     = $value['cant_sucur'];
            $cant_empresas  = $value['cant_empresas'];
            $cant_convenios = $value['cant_convenios'];
            $sum_cant = array(
                $cant_roles,
                $cant_modulos,
                $cant_sectores,
                $cant_grupos,
                $cant_plantas,
                $cant_sucur,
                $cant_empresas,
                $cant_convenios
            );
            print_r($sum_cant);
        endforeach;
    endif;
    // exit;
    if (array_sum($sum_cant) <= 0) {
        require __DIR__ . '../../../config/conect_mysql.php';
        // $recid_c     = test_input($_POST["recid_c"]);
        /* Comprobamos campos vacios  */
        $query = "DELETE FROM roles WHERE roles.recid='$recid'";
        $rs_insert = mysqli_query($link, $query);
        mysqli_error($link);
        if (mysqli_errno($link) == 1451) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Existe Información en usuarios</div>";
        } else {
            header("Location:/" . HOMEHOST . "/usuarios/roles/?_c=$_GET[_c]");
        }
        mysqli_close($link);
    } else {
        header("Location:/" . HOMEHOST . "/usuarios/roles/?_c=$_GET[_c]&err");
    }
}
/** FIN BORRAR DE ROL */
