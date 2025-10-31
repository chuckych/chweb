<!-- Modal -->
<div class="modal fadeIn" id="pic" data-keyboard="true" tabindex="-1" aria-labelledby="picLabel" aria-hidden="true">
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
                        <div class="picFoto" style="height:175px;background-color: #f0f0f0"></div>
                    </div>
                    <div class="col pl-2 text-left font-weight-normal">
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
<!-- modalUser -->
<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="formUser">
    <div class="modal fadeIn" id="modalUser" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <p class="modal-title h6" id="modalUserLabel">Modal title</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <input type="hidden" hidden id="tipo" name="tipo">
                <div class="modal-body pt-0">
                    <div class="form-row">
                        <div class="col-12 py-2 col-sm-4" id="divid_user">
                            <label for="formUserID">ID</label>
                            <input class="form-control h40" type="tel" name="formUserID" id="formUserID"
                                placeholder="ID de Usuario" maxlength="11">
                        </div>
                        <div class="col-12 py-2 col-sm-8">
                            <label for="formUserName">Nombre</label>
                            <input class="form-control h40" type="text" name="formUserName" id="formUserName"
                                placeholder="Nombre y Apellido" maxlength="50">
                        </div>
                        <div class="col-12"></div>
                        <div class="col-12 py-2">
                            <div class="form-row">
                                <div class="col-12 col-sm-6">
                                    <label for="formUserEstado">Estado</label>
                                    <div class="btn-group btn-group-toggle border p-1 w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-success fontq border-0" id="labelActivo">
                                            <input value="0" type="radio" name="formUserEstado" id="formUserEstadoAct">
                                            Activo
                                        </label>
                                        <label class="btn btn-outline-danger fontq border-0 ml-1" id="labelInactivo">
                                            <input type="radio" value="1" name="formUserEstado" id="formUserEstadoBloc">
                                            Bloqueado
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 mt-2 mt-sm-0">
                                    <label for="formUserArea">Visualizar Zona en App <i
                                            data-titlel="Informar zona al fichar en App"
                                            class="bi bi-info-circle"></i></label>
                                    <div class="btn-group btn-group-toggle border p-1 w-100" data-toggle="buttons">
                                        <label class="btn btn-outline-success fontq border-0" id="labelAreaActivo">
                                            <input value="1" type="radio" name="formUserArea" id="formUserAreaAct">
                                            Activo
                                        </label>
                                        <label class="btn btn-outline-danger fontq border-0 ml-1"
                                            id="labelAreaInactivo">
                                            <input type="radio" value="0" name="formUserArea" id="formUserAreaBloc">
                                            Inactivo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <label for="_drUser">Bloqueo por Fechas</label>
                                    <span class="bi bi-eraser text-secondary fontq pointer cleanDate"
                                        data-titlet="Borrar Fecha"></span>
                                    <div class="">
                                        <input type="text" readonly
                                            class="pointer h40 form-control text-center bg-white" name="formUserExpired"
                                            id="_drUser" placeholder="Selecionar Fechas">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 pb-2 hide">
                            <label for="formUserMotivo">Motivo Bloqueo</label>
                            <input class="form-control h40" name="formUserMotivo" id="formUserMotivo"
                                placeholder="Motivo de bloqueo" maxlength="75">
                        </div>
                        <!-- <div class="col-12 pb-2 hide">
                            <label for="formUserRegid">Reg ID</label>
                            <textarea class="form-control p-3" name="formUserRegid" id="formUserRegid"
                                style="height: 120px !important;"
                                placeholder="Regid proporcionado desde la App"></textarea>
                        </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border-0 btn-sm fontq h40 w100 text-secondary"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-custom fontq h40 w100" id="submitUser">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- modalDevice -->
<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="formDevice">
    <div class="modal" id="modalDevice" tabindex="-1" aria-labelledby="modalDeviceLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <p class="modal-title h6" id="modalDeviceLabel">Modal title</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="bi bi-x-lg"></span>
                    </button>
                </div>
                <input type="hidden" hidden id="formDeviceTipo" name="formDeviceTipo">
                <div class="modal-body mt-n3">
                    <div class="row">
                        <div class="col-12" id="divPhoneID">
                            <label for="formDevicePhoneID">PhoneID <span class="requerido"></span></label>
                            <input class="form-control h40 bg-light" type="tel" readonly name="formDevicePhoneID"
                                id="formDevicePhoneID" maxlength="20" placeholder="Ingrese el PhoneID">
                        </div>
                        <div class="col-8 py-2 col-sm-8">
                            <label for="formDeviceNombre">Nombre <span class="requerido"></span></label>
                            <input class="form-control h40" type="text" name="formDeviceNombre" id="formDeviceNombre"
                                placeholder="Nombre" maxlength="50">
                        </div>
                        <div class="col-4 py-2 col-sm-4">
                            <label for="formDeviceEvento">Evento</label>
                            <input class="form-control h40" type="tel" name="formDeviceEvento" id="formDeviceEvento"
                                placeholder="Evento" maxlength="4">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border-0 btn-sm fontq h40 w100 text-secondary"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-custom fontq h40 w100"
                        id="submitDevice">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- modalSetting -->
<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="deviceSetting">
    <div class="modal fadeIn" id="modalSetting" tabindex="-1" aria-labelledby="modalSettingLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <p class="modal-title h6" id="modalSettingLabel">Modal title</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="bi bi-x-lg"></span>
                    </button>
                </div>
                <input type="hidden" hidden id="deviceSettingTipo" name="deviceSettingTipo">
                <div class="modal-body mt-n3">
                    <div class="form-row">
                        <div class="col-6 py-2 col-sm-4">
                            <label for="deviceSettingUsuario">Usuario <span class="requerido"></span></label>
                            <input class="form-control h40" type="text" name="deviceSettingUsuario"
                                id="deviceSettingUsuario" placeholder="Usuario" maxlength="9">
                        </div>
                        <div class="col-6 py-2 col-sm-3">
                            <label for="deviceSettingTMEF">Tiempo
                                <span data-titlet="Tiempo mínimo entre registraciones" class="bi bi-info-circle"></span>
                            </label>
                            <input class="form-control h40" type="tel" name="deviceSettingTMEF" id="deviceSettingTMEF"
                                placeholder="Tiempo mínimo" maxlength="4" value="60">
                        </div>
                        <div class="col-12 py-2 col-sm-5">
                            <label for="deviceSettingRememberUser">Recordar Usuario <span
                                    data-titlet="Recordar Usuario en App" class="bi bi-info-circle"></span></label>
                            <div class="btn-group btn-group-toggle border p-1 w-100" data-toggle="buttons">
                                <label class="btn btn-outline-success fontq border-0 active" id="labelAreaActivo">
                                    <input value="1" type="radio" name="deviceSettingRememberUser"
                                        id="deviceSettingRememberUserAct" checked> Activo
                                </label>
                                <label class="btn btn-outline-danger fontq border-0 ml-1" id="labelAreaInactivo">
                                    <input type="radio" value="0" name="deviceSettingRememberUser"
                                        id="deviceSettingRememberUserBloc"> Inactivo
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="custom-control custom-switch custom-control-inline d-flex justify-content-end">
                                <input type="checkbox" class="custom-control-input" id="deviceInitialize"
                                    name="deviceInitialize" value="0">
                                <label class="custom-control-label" for="deviceInitialize" style="padding-top: 3px;">
                                    <span class="text-dark">Inhabilitar Dispositivo</span>
                                    <span
                                        data-titlel="Inactiva el dispositivo. Para activar, requiere Token o configurar."
                                        class="bi bi-info-circle"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border-0 btn-sm fontq h40 w100 text-secondary"
                        data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm btn-custom fontq h40 w100"
                        id="submitSetting">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- modalTrain -->
<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="formTrain">
    <div class="modal fadeIn" id="modalTrain" tabindex="-1" aria-labelledby="modalTrainLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="min-height:500px">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <p class="modal-title h6" id="modalTrainLabel"></p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="bi bi-x-lg"></span>
                    </button>
                    <input type="hidden" name="selected" id="selectedPhoto">
                    <input type="hidden" name="userID" id="userPhoto">
                    <input type="hidden" name="type" id="typeEnroll">
                </div>
                <div class="modal-body pt-0">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border btn-sm fontq h40 w100 text-secondary"
                        data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-sm btn-custom fontq h40 w100" id="submitTrain">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- modalZone -->
<form action="" method="post" class="border px-3 pb-3 mt-1" id="formZone">
    <div class="modal fadeIn" id="modalZone" tabindex="-1" aria-labelledby="modalZoneLabel" aria-hidden="true">
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