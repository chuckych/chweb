<!-- Modal -->
<div class="modal fade" id="pic" data-keyboard="true" tabindex="-1" aria-labelledby="picLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div>
                    <h6 class="modal-title text-secondary picName font-weight-bold text-uppercase" id="picLabel"></h6><span class="fontq text-dark text-wrap picIDUser"></span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body pt-0 mb-sm-3">
                <div class="row">
                    <input type="hidden" id="latitud">
                    <input type="hidden" id="modalNombre">
                    <input type="hidden" id="longitud">
                    <input type="hidden" id="zona">
                    <input type="hidden" id="map_size" value='5'>
                    <input type="hidden" id="modalFoto">
                    <div class="col-auto pr-1 w150 divFoto"><span class="picFoto"></span></div>
                    <div class="col pl-2 text-left text-secondary font-weight-normal">
                        <div class="datos">
                            <p class="p-0 m-0">
                                <label class="w70 fontp text-secondary">Fecha: </label>
                                <div class="fontq text-dark text-wrap picDia"></div>
                            </p>
                            <p class="p-0">
                                <label class="w70 fontp text-secondary">Hora: </label>
                                <div class="fontq text-dark text-wrap picHora"></div>
                            </p>
                            <p class="p-0">
                                <label class="w70 fontp text-secondary">Dispositivo: </label>
                                <div class="fontq text-dark text-wrap picDevice"></div>
                            </p>
                            <p class="p-0">
                                <label class="w70 fontp text-secondary">Tipo: </label>
                                <div class="fontq text-dark text-wrap picTipo ls1"></div>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row bg-white">
                    <div id="mapzone" class="img-fluid rounded m-3"></div>
                </div>
                <span id="noGPS"></span>
            </div>
            <div class="modal-footer d-sm-none d-block">
                <button type="button" class="btn btn-outline-custom border btn-sm fontq float-right" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>