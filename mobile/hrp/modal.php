<!-- Modal -->
<div class="modal fade" id="pic" data-keyboard="true" tabindex="-1" aria-labelledby="picLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title text-secondary picName font-weight-bold text-uppercase" id="picLabel"></h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="latitud">
                    <input type="hidden" id="longitud">
                    <input type="hidden" id="zona">
                    <input type="hidden" id="map_size" value='5'>
                    <div class="col-auto pr-1 w150 divFoto"><span class="picFoto"></span></div>
                    <div class="col pl-2 text-left text-secondary font-weight-normal">
                        <div class="datos">
                        <!-- <p><label class="w70">Nombre: </label><span class="fontq text-uppercase text-wrap font-weight-bold picName"></span></p> -->
                        <p><label class="w70">Legajo: </label><span class="fontq text-dark text-wrap picIDUser"></span></p>
                        <p><label class="w70">Phone ID: </label><span class="fontq text-dark text-wrap picUid"></span></p>
                        <p><label class="w70">Fecha: </label><span class="fontq text-dark text-wrap picDia"></span></p>
                        <p><label class="w70">Hora: </label><span class="fontq text-dark text-wrap picHora"></span></p>
                        <p><label class="w70">Evento: </label><span class="fontq text-dark text-wrap picTipo ls1"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row bg-white">
                    <div id="mapzone" class="mt-4 img-fluid rounded"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-custom btn-sm border fontq" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>