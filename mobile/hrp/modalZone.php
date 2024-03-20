<!-- Modal -->
<form action="" method="post" class="border px-3 pb-3 mt-1" id="formZone">
    <div class="modal" id="modalZone" tabindex="-1" aria-labelledby="modalZoneLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <p class="modal-title h6" id="modalZoneLabel">Modal title</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="bi bi-x-lg"></span>
                    </button>
                </div>
                <div class="">
                    <input type="hidden" id="formZoneTipo">
                </div>
                <div class="modal-body pt-0">
                    <!-- <div id="rowNuevaZona"> -->
                    <div class="form-row">
                        <div class="col-12 mb-2" id="divGeocomplete">
                            <div class="form-group">
                                <input id="geocomplete" type="text" class="form-control h40"
                                    placeholder="Ingrese un lugar o dirección" value="">
                            </div>
                        </div>
                        <div class="col-12 col-lg-6" id="divForm">
                            <div class="form-row">
                                <!-- <div class="pb-4">
                                        <input type="reset" value="Reset"
                                            class="float-right btn btn-outline-custom border btn-sm fontq">
                                    </div> -->
                                <div id="divLatLng" style="display: none;">
                                    <div class="col-6 mb-2">
                                        <input name="lat" type="" class="form-control border-0" id="formZoneLat"
                                            readonly>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <input name="lng" type="" class="form-control border-0" id="formZoneLng"
                                            readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-8">
                                    <label for="formZoneNombre" class="text-nowrap fontq w80">Nombre <span
                                            class="requerido"></span></label>
                                    <input type="text" class="form-control h40" id="formZoneNombre"
                                        name="formZoneNombre" placeholder="Nombre de la zona" maxlength="50">
                                </div>
                                <div class="col-12 col-sm-4 mt-2 mt-sm-0">
                                    <label for="formZoneRadio" class="text-nowrap fontq w80">Radio <span
                                            class="requerido"></span></label>
                                    <select name="formZoneRadio" id="formZoneRadio" class="form-control">
                                        <option value="100">100</option>
                                        <option value="200">200</option>
                                        <option value="300">300</option>
                                        <option value="400">400</option>
                                        <option value="500">500</option>
                                        <option value="600">600</option>
                                        <option value="700">700</option>
                                        <option value="800">800</option>
                                        <option value="900">900</option>
                                        <option value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="col-4 py-2 col-sm-4">
                                    <label for="formZoneEvento">Evento</label>
                                    <input class="form-control h40" type="tel" name="formZoneEvento" id="formZoneEvento"
                                        placeholder="Evento" maxlength="4">
                                </div>
                                <div class="col-12">
                                    <div class="d-inline-flex py-3">
                                        <div name="formatted_address" value="" class="fontq" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-end">
                                        <div>
                                            <a id="reset" href="#"
                                                class="mb-2 px-3 btn btn-outline-custom border btn-sm fontq">
                                                <span>Resetar Marcador <i class="bi bi-geo-alt-fill"></i></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div id="divNearZone" class="col-12"></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 px-sm-3" id="divMapCanva">
                            <div class="parentMapCanvas">
                                <div class="map_canvas" id="map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border-0 btn-sm fontq h40 w100 text-secondary"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-custom fontq h40 w100" id="submitZone">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</form>