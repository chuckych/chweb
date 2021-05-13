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
                    <div class="col-auto pr-1 w150"><span class="picFoto"></span></div>
                    <div class="col pl-2 text-left text-secondary font-weight-normal">
                        <div class="datos">
                        <!-- <p><label class="w70">Nombre: </label><span class="fontq text-uppercase text-wrap font-weight-bold picName"></span></p> -->
                        <p><label class="w70">Phone ID: </label><span class="fontq text-dark text-wrap picUid"></span></p>
                        <p><label class="w70">Fecha: </label><span class="fontq text-dark text-wrap picDia"></span></p>
                        <p><label class="w70">Hora: </label><span class="fontq text-dark text-wrap picHora"></span></p>
                        <p><label class="w70">Evento: </label><span class="fontq text-dark text-wrap picTipo"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 d-none" id="rowRespuesta">
                    <div class="col-12">
                        <span id="respuesta"></span>
                    </div>
                </div>
                <div class="row mt-4 d-none" id="rowCreaZona">
                    <form action="zonas/insert-zone.php" method="POST" id="CrearZona">
                        <div class="col-12">
                        <div class="form-inline">
                            <input name="lat" type="hidden">
                            <input name="lng" type="hidden">
                            <input name="alta_zona" type="hidden" value="true" class="">
                            <label for="nombre" class="text-nowrap fontq w80">Nombre</label>
                            <input type="text" class="form-control h40 w300" id="nombre" required name="nombre" placeholder="Nombre de la zona" pattern="[a-zA-Z0-9- _ñ]+">
                        </div>
                        <div class="form-inline mt-2">
                            <label for="metros" class="text-nowrap fontq w80">Radio</label>
                            <select name="metros" id="metros" class="select2 form-control w300 h40">
                                <?php
                                foreach (RADIOS as $key => $value) {
                                    echo '<option value="' . $value . '">' . $key . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group row mt-2">
                            <div class="col-12">
                                <button type="submit" class="float-right btn btn-custom btn-sm px-3 opa8 fontq" name="submit" value="true" id="btnSubmitZone">Aceptar</button>
                                <button type="button" class="float-right btn btn-link text-decoration-none text-secondary btn-sm px-3 fontq" id="cancelZone">Cancelar</button>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
                <div class="row bg-white">
                    <div id="mapzone" class="m-3 img-fluid rounded"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-custom btn-sm border fontq" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>