<!-- Modal -->
<div class="modal fade" id="pic" data-keyboard="true" tabindex="-1" aria-labelledby="picLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" style="min-height: 550px;">
        <div class="modal-content">
            <div class="modal-header border-0" style="min-height:81px">
                <div>
                    <h6 class="modal-title text-dark picName font-weight-bold text-uppercase" id="picLabel">
                        <span class="text-light bg-light">xxxxxxxx xxxxxxx</span>
                    </h6>
                    <span class="fontq text-dark text-wrap picIDUser">
                        <span class="text-light bg-light">xxxxxxxx</span>
                    </span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body pt-0">
                <div class="row">
                    <input type="hidden" id="latitud">
                    <input type="hidden" id="modalNombre">
                    <input type="hidden" id="longitud">
                    <input type="hidden" id="zona">
                    <input type="hidden" id="map_size" value='5'>
                    <input type="hidden" id="modalFoto">
                    <div class="col-auto pr-1 w150 divFoto">
                        <div class="picFoto" style="height:175px;background-color: #f0f0f0""></div>
                    </div>
                    <div class=" col pl-2 text-left font-weight-normal">
                            <div class="datos">
                                <p class="p-0 m-0">
                                    <label class="w100 fontp bg-loading">Fecha / Hora: </label>
                                <div class="fontq text-dark text-wrap picDia">
                                    <span class="text-light bg-light">xxxxxxxx xxxxxxxx</span>
                                </div>
                                </p>
                                <p class="p-0">
                                    <label class="w70 fontp bg-loading">Dispositivo: </label>
                                <div class="fontq text-dark text-wrap picDevice">
                                    <span class="text-light bg-light">xxxxxxxxxxxxxxxx</span>
                                </div>
                                </p>
                                <!-- <p class="p-0">
                                <label class="w70 fontp bg-loading">Tipo: </label>
                                <div class="fontq text-dark text-wrap picTipo ls1"></div>
                            </p> -->
                                <p class="p-0">
                                    <label class="w70 fontp bg-loading">Zona: </label>
                                <div class="fontq text-dark text-wrap picZona">
                                    <span class="text-light bg-light">xxxxxxxxxxxxxxxx</span>
                                </div>
                                </p>
                                <p class="p-0">
                                    <label class="fontp bg-loading">Reconocimiento Facial: </label>
                                <div class="fontq text-dark text-wrap picFace">
                                    <span class="text-light bg-light">xxxxxxxx</span>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div id="divError">

                    </div>
                    <div class="row bg-white">
                        <div id="mapzone" class="m-3 radius "
                            style="width:100%; height:250px; max-width:500px; aspect-ratio:464/250; object-fit:cover;background-color: #f0f0f0">

                        </div>
                    </div>
                    <span id="noGPS"></span>
                </div>
                <div class="modal-footer d-sm-none d-block mt-n3">
                    <button type="button" class="btn btn-outline-custom border btn-sm fontq float-right"
                        data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>