<div class="form-signin animate__animated animate__fadeIn mb-5 pt-5">
    <form class="card card-md shadow-lg border-0" action="login/validaSesion.php" method="POST" autocomplete="off" id="formRFID">
        <div class="card-body">
            <!-- <p class="card-title text-center h3">Iniciar Sesi&oacuten</p> -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control text-center" id="tarjeta" name="tarjeta" value="">
                <label for="clave">Su Tarjeta</label>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary w-100" id="submitLogin">INGRESAR</button>
            </div>
            <div class="flex-center-between">
                <div class="mt-3">
                    <button type="button" class="font07 btn btn-sm btn-outline-info" id="logoutLogin">Salir</button>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-info" value="user" id="changeLogin"><i class="bi bi-person"></i></button>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="js/login.js?<?= vjs() ?>"></script>