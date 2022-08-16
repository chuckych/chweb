<style>
    .container-fluid {
        display: flex;
        align-items: center;
    }
</style>
<div class="form-signin animate__animated animate__fadeIn">
    <form class="card card-md border-0 shadow-lg" action="." method="get" autocomplete="off">
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
                <button type="submit" class="btn btn-primary w-100">INGRESAR</button>
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-info" value="rfid" id="changeLogin"><i class="bi bi-credit-card-2-front"></i></button>
            </div>
        </div>
    </form>
</div>
<script src="js/login.js?<?=vjs()?>"></script>