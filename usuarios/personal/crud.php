<?php
ini_set('max_execution_time', 300);
/** Tiempo maximo de duracion del script 300 segundos=(5 Minutos) */
$border = $ErrNombre = $ErrUsuario = $ErrRol = $ErrContraseña = $duplicado = $nombre = $usuario = $rol = $contraseña = $Errl = '';
/** ALTA DE USUARIO */
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'Importar')) {
    require __DIR__ . '../../../config/conect_mysql.php';

    $rol    = test_input($_POST['rol']);
    $id_c_  = test_input($_POST['id_c']);
    $ident_ = test_input($_POST['ident']);
    $fecha  = date("Y/m/d H:i:s");
    /* Comprobamos campos vacios  */
    // if ((valida_campo($nombre)) or (valida_campo($usuario)) or (valida_campo($rol)) or (valida_campo($contraseña))) {
    if ((empty($_POST['_l']) or (valida_campo($rol)))) {
        header("Location:/" . HOMEHOST . "/usuarios/personal/?_c=$_GET[_c]&error");
    } else {
        
        $tiempo_ini = microtime(true);
        $url   = host() . "/" . HOMEHOST . "/data/getImpoPerso.php?tk=" . token() . "&_c=" . $_POST['_c'] . "&_l%5B%5D%3D=" . implode("&_l%5B%5D%3D=", $_POST['_l']);
        // echo $url;
        // exit;
        $json  = file_get_contents($url);
        $array = json_decode($json, TRUE);
        if (is_array($array)) :
            $rowcount = (count($array['impo_personal']));
        endif;
        $data = $array['impo_personal'];
        // echo '<pre>';
        // print_r($data); exit;

        foreach ($data as $value) {

            $nombre     = $value['n'];
            $legajo  = $value['l'];
            if (test_input($_POST['LegaPass']) == 'true') {
                $userauto   = $value['l'];
                $contraauto = password_hash($value['d'], PASSWORD_DEFAULT);
            }else{
                $caract = array(",", "-", ":", "|", ".", "´", ";", "ñ", "Ñ");
                $nombre_u = str_replace($caract, "", $value['n']);
                $userauto    = strtolower($ident_) . '-' . strtok(strtolower($nombre_u), " \n\t") . "-" . $legajo;
                // $userauto    = strtolower($ident_) . '-' . strtok(strtolower($nombre_u), " \n\t") . "-" . sprintf("%04d", rand(0, 9999));
                $contraauto  = password_hash($userauto, PASSWORD_DEFAULT);
            }
            
            $recid       = recid();

            /* INSERTAMOS */
            $query = "INSERT INTO usuarios (recid, nombre, usuario, rol, clave, cliente, legajo, fecha_alta, fecha ) VALUES( '$recid', '$nombre', '$userauto', '$rol', '$contraauto', '$id_c_', '$legajo','$fecha', '$fecha')";
            $rs_insert = mysqli_multi_query($link, $query);
            // print_r($query);
        }
        if ($rs_insert) {
            $count = count($data);
            $tiempo_fini = microtime(true);
            $duracion    = round($tiempo_fini - $tiempo_ini, 2);
            header("Location:/" . HOMEHOST . "/usuarios/?_c=$_GET[_c]&okimpo&dur=&v=$duracion&ct=$count");
        } else {
            echo '<h1>Error al importar datos</h1>';
            // print_r(mysqli_error_list($link));
        }
        // mysqli_error($link);
        // // print_r($query);
        // // print_r(mysqli_error_list($link));

        // if (mysqli_errno($link) == 1062) {
        //     $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>".mysqli_error($link)."</div>";
        // } elseif(mysqli_errno($link) == 1452) {
        //     $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Error: ".mysqli_errno($link)."<br />".mysqli_error($link)."</div>";
        // }else{
    }
    mysqli_close($link);
}
/** FIN ALTA DE USUARIO */
