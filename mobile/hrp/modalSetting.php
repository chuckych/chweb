<!-- Modal -->
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