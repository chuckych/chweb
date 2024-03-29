<!-- Modal -->
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