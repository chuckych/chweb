<div class="collapse" id="collapseFiltros">
  <div class="py-2">
    <div class="row">
      <div class="col-12 col-sm-3">
        <!-- Nombre -->
        <label for="nombreAud" class="mb-1 fontq">Nombre</label>
        <select class="form-control" id="nombreAud" name="nombreAud"></select>
      </div>
      <div class="col-12 col-sm-3">
        <!-- Usuario -->
        <label for="userAud" class="mb-1 fontq">Usuario</label>
        <select class="form-control" id="userAud" name="userAud"></select>
      </div>
      <div class="col-12 col-sm-3">
        <!-- Cuenta -->
        <label for="cuentaAud" class="mb-1 fontq">Cuenta</label>
        <select class="form-control" id="cuentaAud" name="cuentaAud"></select>
      </div>
      <div class="col-12 col-sm-3">
        <!-- Tipo -->
        <label for="tipoAud" class="mb-1 fontq">Tipo </label>
        <select class="form-control" id="tipoAud" name="tipoAud"></select>
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-12 col-sm-2">
        <!-- Hora -->
        <label for="horaAud" class="mb-1 fontq">Hora Inicio</label>
        <input type="search" class="form-control h40 HoraMask" id="horaAud" name="horaAud" placeholder="00:00:00" autocomplete="off" style="height: 40px !important;">
      </div>
      <div class="col-12 col-sm-2">
        <!-- Hora -->
        <label for="horaAud2" class="mb-1 fontq">Hora Fin</label>
        <input type="search" class="form-control h40 HoraMask" id="horaAud2" name="horaAud2" placeholder="00:00:00" autocomplete="off" style="height: 40px !important;">
      </div>
      <div class="col-12 col-sm-2">
        <!-- Id Sesion -->
        <label for="idSesionAud" class="mb-1 fontq">ID Sesion</label>
        <select class="form-control" id="idSesionAud" name="idSesionAud"></select>
      </div>
      <div class="col-12 col-sm-6">
      </div>
    </div>
  </div>
</div>
<script src="../../js/select2.min.js"></script>
<script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
<script src="select.js?<?= microtime(true) ?>"></script>