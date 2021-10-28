<!-- Modal -->
<style>
  #detalleAud .modal-body span {
    font-size: .8rem;
  }

  #detalleAud .modal-body label {
    width: 90px;
    margin: 0;
  }
</style>
<div class="modal animate__animated animate__fadeIn" id="detalleAud" tabindex="-1" aria-labelledby="detalleAudLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <h6 class="card-title mt-1">Detalle de auditoría</h6>
            <div><label for="aud_nomb">Nombre: </label><span id="aud_nomb"></span></div>
            <div><label for="aud_user">Usuario: </label><span id="aud_user"></span></div>
            <div><label for="aud_nacu">Cuenta: </label><span id="aud_nacu"></span></div>
            <div><label for="aud_fech">Fecha Hora: </label><span id="aud_fech" class="ls1"></span></div>
            <div><label for="aud_tipo">Tipo: </label><span id="aud_tipo"></span></div>
            <div><label for="aud_modu">Módulo: </label><span id="aud_modu"></span></div>
            <div><label for="aud_dato">Dato: </label><span id="aud_dato"></span></div>
          </div>
          <div class="col-12 mt-3">
            <h6 class="card-title">Detalle de sesión</h6>
            <div><label for="log_feho">Fecha Hora: </label><span id="log_feho" class="ls1"></span></div>
            <div><label for="log_nrol">Rol: </label><span id="log_nrol"></span></div>
            <div><label for="log_d_ip">IP: </label><span id="log_d_ip"></span></div>
            <div><label for="log_agen">Agente: </label><span id="log_agen"></span></div>
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-custom border fontq opa8" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>