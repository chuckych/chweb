<div class="form-signin animate__animated animate__fadeIn mb-5 pt-5">
<form class="card card-md shadow-lg border-0" action="login/validaSesion2.php" method="POST" autocomplete="off" id="formUser">
        <div class="card-body">
            <!-- <p class="card-title text-center h3">Iniciar Sesi&oacute;n</p> -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="usuario" name="user" autocomplete="off">
                <label for="usuario">Usuario</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="clave" name="clave" autocomplete="off">
                <label for="clave">Contrase&ntilde;a</label>
            </div>
            <div class="">
                <button type="submit" class="btn btn-primary w-100" id="submitLogin">INGRESAR</button>
            </div>
            <div class="mt-3 flex-center-between">
                <div class="">
                    <button type="button" class="font07 btn btn-sm btn-outline-info" id="logoutLogin">Salir</button>
                </div>
                <div class="">
                    <button type="button" class="btn btn-outline-info" value="rfid" id="changeLogin"><i class="bi bi-credit-card-2-front"></i></button>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="js/login.js?<?= vjs() ?>"></script>