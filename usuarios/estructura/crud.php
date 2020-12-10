<?php
$border = $ErrModulo = $ErrModulo2 = $duplicado = $nombre = '';
/** ALTA */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == $submit)) {

        if(isset($_POST['seccion'])):
        /** para el crud de secciones */
        $e_nombretabla = 'secc_roles'; /** nombre de la tabla de la BD */
        $e_coltabla1   = 'seccion'; /** nombre de la columna de del seccion */
        $e_coltabla2   = 'recid_rol'; /** nombre de la columna del recid */
        $e_coltabla3   = 'id_rol'; /** nombre de la columna del id del valor */
        $e_coltabla4   = 'cliente'; /** nombre de la columna del id de cliente */
        $e_coltabla5   = 'sector'; /** nombre de la columna del sector de la seccion */
        $Secc_Sector = ($_POST['sector']);
        endif;
    
    require __DIR__ . '../../../config/conect_mysql.php';
    $est       = empty($_POST["est"]) ? $_POST["est"] = '' : $_POST["est"];
    $recid_rol = ($_GET['_r']);
    $fecha     = date("Y/m/d H:i:s");
    $error    = '<div class="col-12 alert alert-danger fontq">Debe Seleccionar un Valor.</div>';
    /* Comprobamos campos vacios  */
    if (empty($est)) {
        $Errest = (empty($est)) ? $error : '';
    } else {
        foreach ($est as $value) {
               $est = $value;
               if(isset($_POST['seccion'])){
                $query = "INSERT INTO $e_nombretabla($e_coltabla2, $e_coltabla3, $e_coltabla1, $e_coltabla4 , $e_coltabla5, fecha) VALUES('$recid_rol', '$id_Rol', '$est', '$idClienteRol', '$Secc_Sector', '$fecha')";
               }else{
                $query = "INSERT INTO $e_nombretabla($e_coltabla2, $e_coltabla3, $e_coltabla1, $e_coltabla4 , fecha) VALUES('$recid_rol', '$id_Rol', '$est', '$idClienteRol', '$fecha')";
               }
            $rs_insert = mysqli_query($link, $query);
            // mysqli_error($link);
        }
        // print_r(mysqli_error_list($link));
        // exit;
        header("Location:/" . HOMEHOST . "/usuarios/estructura/?_r=$_GET[_r]&_c=$_GET[_c]&e=$_GET[e]");
    }
    mysqli_close($link);
}
/** FIN ALTA  */
/** BAJA  */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == $submitb)) {
    require __DIR__ . '../../../config/conect_mysql.php';

    if(isset($_POST['seccion'])):
        /** para el crud de secciones */
        $e_nombretabla = 'secc_roles'; /** nombre de la tabla de la BD */
        $e_coltabla1   = 'seccion'; /** nombre de la columna de del seccion */
        $e_coltabla2   = 'recid_rol'; /** nombre de la columna del recid */
        $e_coltabla3   = 'id_rol'; /** nombre de la columna del id del valor */
        $e_coltabla4   = 'cliente'; /** nombre de la columna del id de cliente */
        $e_coltabla5   = 'sector'; /** nombre de la columna del sector de la seccion */
        $Secc_Sector = ($_POST['sector']);
    endif;

    $est = empty($_POST["est"]) ? $_POST["est"]='' : $_POST["est"];
    $recid_rol = ($_GET['_r']);
    $fecha     = date("Y/m/d H:i:s");
    $error     = '<div class="col-12 alert alert-danger fontq">Debe Seleccionar un Valor.</div>';
    /* Comprobamos campos vacios  */
    if (empty($est)) {
        $Errest2 = (empty($est)) ? $error : '';
    } else {
        foreach ($est as $value) {
            $est=$value;
            if(isset($_POST['seccion'])){ /** para el delete de las secciones */
                $query = "DELETE FROM $e_nombretabla WHERE $e_nombretabla.$e_coltabla1 = '$est' and $e_nombretabla.$e_coltabla2 ='$_GET[_r]' AND $e_nombretabla.$e_coltabla5 = '$_POST[sector]'";
                $rs_delete = mysqli_query($link, $query);
            }else{
                $query = "DELETE FROM $e_nombretabla WHERE $e_nombretabla.$e_coltabla1 = '$est' and $e_nombretabla.$e_coltabla2 ='$_GET[_r]'";
                if($_GET['e']=='sectores'){
                    if(mysqli_query($link, $query)){
                        $query_s = "DELETE FROM secc_roles WHERE secc_roles.sector = '$est' AND secc_roles.$e_coltabla2 ='$_GET[_r]'";
                        // print_r($query_s);
                        // exit;
                        $rs_delete = mysqli_query($link, $query_s);
                    }
                }else{
                    $rs_delete = mysqli_query($link, $query);
                }
                
            }
            // print_r($query);
            // mysqli_error($link);
        }
        // print_r(mysqli_error_list($link));
        header("Location:/" . HOMEHOST . "/usuarios/estructura/?_r=$_GET[_r]&_c=$_GET[_c]&e=$_GET[e]");
    }
    mysqli_close($link);
}
/** FIN BAJA */
