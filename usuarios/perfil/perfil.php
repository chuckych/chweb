<?php require __DIR__ . '/crud.php';
?>
<!doctype html>
<html lang="es">

<head><?php require __DIR__ . "../../../llamadas.php"; ?><title>Cambiar Contrase&ntilde;a</title>
</head>

<body class="animate__animated animate__fadeIn">
    <div class="container shadow pb-2">
        <?php
        $countModRol = (count($_SESSION['MODS_ROL']));
        if ($countModRol != '1') {
            echo '<div class="">';
        } else {
            echo '<div class="d-none">';
        }
        require __DIR__ . '../../../nav.php';
        echo '</div>';
        $ocultar = $_GET['true'] == true ? 'd-none' : '';
        ?>
        <?=
        encabezado_mod2('bg-custom', 'white', 'shield-lock-fill', 'Cambiar Contrase&ntilde;a', '25', 'text-white mr-2');
        ?>
        <div class="row">
            <div class="col-12 col-sm-10 col-md-6 col-xl-5">
                <?= notif_ok_var('true', 'Contrase&ntilde;a modificada') ?>
                <div class="p-3">
                    <h4 class="card-title text-secondary"><?= $_SESSION["NOMBRE_SESION"] ?></h4>
                    <p class="text-secondary p-0 d-flex align-items-center">
                        <?= $icon_building ?>
                        <span class="fw5 ml-1">&nbsp;<?= $_SESSION["CLIENTE"] ?></span>
                    </p>
                    <p class="text-secondary p-0 d-flex align-items-center">
                        <?= $icon_person ?>
                        <span class="fw5 ml-1">&nbsp;<?= $_SESSION["user"] ?></span>
                    </p>
                    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" name="perfil" onsubmit="return validar_form()" class="<?= $ocultar ?>">
                        <label for="clave" class="fw4 d-flex align-items-center">
                            <?= $icon_key ?>
                            <span class="ml-1">Cambiar Contraseña</span>
                        </label>
                        <div class="form-inline">
                            <div class="input-group">
                                <div class="input-group-prepend" style="width:100% !important">
                                    <input required type="password" autofocus class="form-control h50 w350" name="clave" id="clave" placeholder="" onKeyUp="contar(this.form)">
                                    <div class="border border-0 pointer" id="uc_mostrar" style="position: absolute;right:15px; top:13px"><i class="bi bi-eye-slash-fill text-secondary"></i></div>
                                </div>
                            </div>
                            <input type="text" class="d-none d-sm-block form-control w40 border-0 ml-2 h50 bg-white text-dark fw5" readonly name="escritos">
                            <input type="hidden" class="" name="recid" id="" placeholder="" value="<?= $_SESSION["RECID_USER"] ?>">
                        </div>
                        <div class="text-danger mt-2 fontq fw5"><?= $error_clave ?></div>
                        <div class="alert alert-info mt-3 radius" style="max-width:350px;" role="alert">
                            <h6 class="alert-heading">Requisito de contraseña</h6>
                            <p class="mb-0 fontq fw4 ml-1">- 8 caracteres mínimo</p>
                            <p class="mb-0 fontq fw4 ml-1">- Letras mayúsculas</p>
                            <p class="mb-0 fontq fw4 ml-1">- Letras minúsculas</p>
                            <p class="mb-0 fontq fw4 ml-1">- Números</p>
                        </div>
                        <button type="submit" name="aceptar" class="btn bg-custom w350 h40 text-white opa8 btn-sm px-5 btn-mobile">Aceptar</button>
                    </form>
                    <a href="../../inicio/" class="btn btn-outline-custom px-4 pt-2 btn-mobile fontq mt-3">Salir</a>
                </div>
            </div>
        </div>
    </div><?php require __DIR__ . "../../../js/jquery.php"; ?>
    <script src="../../login/login-min.js?v=<?=vjs()?>"></script>
    <script>
        function validaCampos() {
            var clave = $("#clave").val();
            if ($.trim(clave) == "") {
                toastr.error("Campo contraseña obligatorio", "Aviso!");
                return false;
            }
        }
    </script>
    <script language="javascript" type="text/javascript">
        function validar_form() {
            var contrasenna = document.getElementById('clave').value;
            if (validar_clave(contrasenna) == true) {} else {
                alert('La contraseña ingresada no cumple los requisitos mínimos');
                return false;
            }
        }

        function validar_clave(contrasenna) {
            if (contrasenna.length >= 8) {
                var mayuscula = false;
                var minuscula = false;
                var numero = false;
                for (var i = 0; i < contrasenna.length; i++) {
                    if (contrasenna.charCodeAt(i) >= 65 && contrasenna.charCodeAt(i) <= 90) {
                        mayuscula = true;
                    } else if (contrasenna.charCodeAt(i) >= 97 && contrasenna.charCodeAt(i) <= 122) {
                        minuscula = true;
                    } else if (contrasenna.charCodeAt(i) >= 48 && contrasenna.charCodeAt(i) <= 57) {
                        numero = true;
                    }
                }
                if (mayuscula == true && minuscula == true && numero == true) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        }
    </script>
    <script languaje="javascript">
        function contar(form) {
            n = form.clave.value.length;
            t = 8; {
                form.escritos.value = n;
                form.restantes.value = (t - n);
            }
        }
    </script>
</body>

</html>