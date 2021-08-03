<?php
session_start();

require __DIR__ . '../../config/index.php';
E_ALL();
// secure_auth_ch();
$Modulo = '999';
$bgcolor = 'bg-custom ';
/** ALTA DE CLIENTE */
$border = $ErrNombre = $error = $duplicado = '';
if (($_SERVER["REQUEST_METHOD"] == "POST") && ($_POST['submit'] == 'alta')) {
    require __DIR__ . '../../config/conect_mysql.php';
    $nombre    = test_input($_POST['nombre']);
    $ident     = test_input($_POST['ident']);
    $n_ident   = str_replace(" ", "", $nombre);
    $identauto = (empty($ident)) ? substr(strtoupper($n_ident), 0, 3) : $ident;
    $recid = recid();
    $fecha = date("Y/m/d H:i:s");
    $error = 'Campo obligatorio';
    /* Comprobamos campos vacíos  */
    if ((valida_campo($nombre))) {
        $ErrNombre = (valida_campo($nombre)) ? $error : '';
    } else {
        /* INSERTAMOS CLIENTE EN TABLA CLIENTES */
        $query = "INSERT INTO clientes (recid, ident, nombre, fecha_alta, fecha ) VALUES('$recid', '$identauto','$nombre','$fecha', '$fecha')";
        $rs_insert = mysqli_query($link, $query);

        if (mysqli_errno($link) == 1062) {
            $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>" . mysqli_error($link) . "</div>";
        } else {
            $query = "SELECT id from clientes where recid = '$recid'";
            $rs = mysqli_query($link, $query);
            while ($a = mysqli_fetch_assoc($rs)) {
                $cliente = $a['id'];
            }
            mysqli_free_result($rs);
            $nombre = 'SISTEMA';
            $query = "SELECT roles.nombre FROM roles WHERE roles.cliente='$cliente' AND roles.nombre = '$nombre'";
            $rs = mysqli_query($link, $query);
            $CountRolC = mysqli_num_rows($rs);
            mysqli_free_result($rs);
            if ($CountRolC == 0) {
                /* INSERTAMOS ROL*/
                $query = "INSERT INTO roles (recid, cliente, nombre, fecha_alta, fecha ) VALUES( '$recid', '$cliente', '$nombre', '$fecha', '$fecha')";
                $rs_insert = mysqli_query($link, $query);
                // mysqli_error($link);
                if (mysqli_errno($link) == 1062) {
                    $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Nombre de Rol</div>";
                } else {
                    $query = "SELECT roles.id FROM roles WHERE roles.recid='$recid'";
                    $rs = mysqli_query($link, $query);
                    while ($a = mysqli_fetch_assoc($rs)) {
                        $rol = $a['id'];
                    }
                    mysqli_free_result($rs);
                    $ident = $identauto;
                    $nombre = 'SISTEMA';
                    $usuario = 'sistema';
                    $user_auto    = (empty($usuario)) ? strtolower($ident) . '-' . strtok(strtolower($nombre), " \n\t") . "-" . sprintf("%04d", rand(0, 9999)) : strtolower($ident) . '-' . $usuario . "-" . date('Y');
                    $contraseña  = test_input($_POST["contraseña"]);
                    $contraauto  = password_hash($user_auto, PASSWORD_DEFAULT);
                    $contraseña1 = (empty($contraseña)) ? $contraauto : password_hash($contraseña, PASSWORD_DEFAULT);
                    $fecha       = date("Y/m/d H:i:s");
                    /* INSERTAMOS USUARIOS */
                    $query = "INSERT INTO usuarios (recid, nombre, usuario, rol, clave, cliente, fecha_alta, fecha, principal) VALUES( '$recid', '$nombre', '$user_auto', '$rol', '$contraseña1', '$cliente', '$fecha', '$fecha','1')";
                    $rs_insert = mysqli_query($link, $query);

                    if (mysqli_errno($link) == 1062) {
                        $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>" . mysqli_error($link) . "</div>";
                    } elseif (mysqli_errno($link) == 1452) {
                        $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Error: " . mysqli_errno($link) . "<br />" . mysqli_error($link) . "</div>";
                    } else {
                        /** ALTA DE MÓDULOS */
                        $modulo[] = array(1, 7);
                        $recid_rol = $recid;
                        // print_r($modulo);
                        foreach ($modulo[0] as $value) {
                            $modulo = $value;
                            $query = "INSERT INTO mod_roles(recid_rol, modulo, fecha ) VALUES( '$recid_rol', '$modulo', '$fecha')";
                            $rs_insert = mysqli_query($link, $query);
                            // mysqli_error($link);
                        }
                        // print_r($query);exit;
                        header("Location:/" . HOMEHOST . "/login/?p=check_login.php&conf=$user_auto");
                        /** FIN ALTA DE MÓDULOS */
                    }
                }
            } else {
                $duplicado = "<div class='fontq alert alert-danger animate__animated animate__fadeInDown mt-3 border-0 radius-0 fw4'>Ya existe un Rol con el nombre $nombre</div>";
            }
        }
    }
    mysqli_close($link);
}
/** FIN ALTA DE CLIENTE */
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>Iniciar</title>
</head>

<body class="animate__animated animate__fadeIn">
    <div class="container shadow pb-2">
        <?= encabezado_mod($bgcolor, 'white', 'rueda.png', 'Iniciar', ''); ?>
        <?php if (inicio() == 0) { ?>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="">
                        <p class="lead fw5">ALTA CUENTA INICIAL</p>
                    </div>
                </div>
                <div class="col-12">
                    <?= $duplicado ?>
                    <?= $error ?>
                </div>
                <div class="col-12 bg-light">
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="">
                        <input type="text" required name="nombre" id="nombre" class="form-control h50 w-100" placeholder="Nombre de Cuenta">
                        <button type="submit" name="submit" id="alta" class="mt-2 btn text-white btn-block h50 fontq <?= $bgcolor ?>" value="alta">CREAR CUENTA</button>
                    </form>
                </div>
            </div>
        <?php } else {
            header("Location:/" . HOMEHOST . "/login/");
        } ?>
    </div>
    <?php require __DIR__ . "../../js/jquery.php"; ?>
</body>

</html>