<?php
$error_clave='';
if (($_SERVER["REQUEST_METHOD"] == "POST") && (array_key_exists('aceptar',$_POST))) {

    require __DIR__ . '../../../config/conect_mysql.php';
    $clave      = test_input($_POST["clave"]);
    $recid      = test_input($_POST["recid"]);
    $contraauto = password_hash($clave, PASSWORD_DEFAULT);
    $fecha      = date("Y/m/d H:i:s");

    /* Validamos clave  */
        if(strlen($clave) < 8){
            $error_clave = "La clave debe tener al menos 8 caracteres";
            return false;
         }
         elseif (!preg_match('`[a-z]`', $clave)){
            $error_clave = "La clave debe tener al menos una letra minúscula";
            return false;
         }
         elseif (!preg_match('`[A-Z]`', $clave)){
            $error_clave = "La clave debe tener al menos una letra mayúscula";
            return false;
         }
         elseif (!preg_match('`[0-9]`', $clave)){
            $error_clave = "La clave debe tener al menos un caracter numérico";
            return false;
         }  else {
        $query = "UPDATE usuarios SET
        clave      = '$contraauto',
        fecha       = '$fecha'
        WHERE recid = '$recid'
        ";
        $rs_update = mysqli_query($link, $query);
        
        if($rs_update){
            unset($_SESSION["HASH_CLAVE"]);
            $_SESSION["HASH_CLAVE"] = ($contraauto);
            header("Location:/" . HOMEHOST . "/usuarios/perfil/?true=true");
        }
    }
    mysqli_close($link);
}
