<!-- Modal -->
<form action="" method="post" class="border px-3 pb-3 shadow-sm mt-1" id="formUser">
    <div class="modal" id="modalUser" tabindex="-1" aria-labelledby="modalUserLabel" aria-hidden="true">
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