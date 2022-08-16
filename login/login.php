<?php
$checked = (isset($_COOKIE['user'])) ? 'checked ' : '';
$usuario = (isset($_COOKIE['user'])) ? $_COOKIE['user'] : '';
$clave   = (isset($_COOKIE['clave'])) ? $_COOKIE['clave'] : '';
$Modulo  = '999';
$bgcolor = 'bg-custom ';
$_GET['l'] = $_GET['l'] ?? false;
// session_destroy();
$self = explode('/', $_SERVER['PHP_SELF']);
$self = $self[1];
?>
<?php if (inicio() == 0) {
    header("Location:/" . HOMEHOST . "/op/");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Ingreso CH WEB</title>
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <style>
        @media only screen and (max-width: 320px) {
            .ancho {
                width: 100%;
            }
        }

        @media only screen and (min-width: 375px) {
            .ancho {
                width: 360px;
            }
        }
    </style>
</head>
<?php require __DIR__ . '../../config/conect_mysql.php'; ?>

<body class="body">
    <div class="vh-100 p-4 fw4 animate__animated animate__fadeIn">
        <form action="?p=check_login.php" method="POST" autocomplete=off class="w-100" onsubmit="ShowLoading()">
        <input type="hidden" value="<?=$_GET['l']?>" name="lasturl">
        <input type="hidden" value="<?=$self?>" id="selfHome">
            <div class="row">
                <div class="mx-auto col-12 col-md-6 col-sm-8 col-lg-5 col-xl-4 p-0 border-0">
                    <div class="mx-auto shadow ancho">
                        <div class="p-4 bg-custom text-white fw4" style="border-top-left-radius: 0.3em 0.3em; border-top-right-radius: 0.3em 0.3em;">
                            <p class="text-left lead mb-0">INGRESO | CH-WEB </p><span class="text-left fontq">Iniciar sesi&oacute;n para continuar.</span>
                        </div>
                        <div class="p-4 bg-white">
                            <div class="form-group">
                                <label for="user" class="fw4">Usuario</label>
                                <input required type="text" class="form-control text-lowercase mr-1 mb-2 h50" id="user" placeholder="" name="user" value="<?= $usuario ?>">
                                <!-- <label for="clave" class="fw4">Contrase&ntilde;a</label>
                                <input required type="password" class="form-control h50" id="clave" placeholder="" name="clave" value="<?= $clave ?>"> -->
                                <div class="input-group">
                                    <div class="input-group-prepend" style="width:100% !important">
                                        <input required type="password" class="form-control h50" id="clave" placeholder="" name="clave" value="<?= $clave; ?>">
                                        <div class="border border-0 pointer" id="uc_mostrar" style="position: absolute;right:15px; top:13px"><i class="bi bi-eye-slash-fill text-secondary"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row px-3 mt-4">
                                <div class="custom-control custom-switch">
                                    <input <?= $checked ?> type="checkbox" class="custom-control-input" name="guarda" id="guarda">
                                    <label class="custom-control-label fw4" for="guarda" style="padding-top:3px;">Recordarme</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" id="btnIngresar">
                                <button type="submit" class="btn-block btn btn-lg opa7 h60 bg-custom text-white font1" style="border-bottom-left-radius: 0.3em 0.3em; border-bottom-right-radius: 0.3em 0.3em;border-top-left-radius: 0.0em 0.0em;border-top-right-radius: 0.0em 0.0em;">
                                    <span>INGRESAR</span>
                                </button>
                            </div>
                        </div>
                        <?php
                        if (isset($_GET['error'])) {
                            echo '<div class="row animate__animated animate__flipInX"><div class="col mb-0 "><div class="radius text-white opa8 bg-danger p-3" role="alert">Datos Incorrectos.</div></div></div>';
                        }
                        ?>
                    </div>
                </div>
        </form>
    </div>
    <?php require __DIR__ . "../../js/jquery.php"; ?>
    <script src="login.js?v=<?=vjs()?>"></script>
</body>

</html>