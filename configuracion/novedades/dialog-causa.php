<dialog id="dialogCausa" style="max-width: 90%;" class="fadeIn">
    <div class="dialog-header">
        <p class="dialog-header-title">Editar Causa</p>
    </div>
    <div class="dialog-body">
        <div class="d-flex flex-column w-100">
            <div class="d-inline-flex w-100 align-items-start mb-2 justify-content-between">
                <div class="d-inline-flex gap8">
                    <div class="form-group">
                        <label for="ENovCodi">Código</label>
                        <input type="text" inputmode="numeric" disabled class="form-control w80" id="ENovCodi" data-field="NovCodi">
                    </div>
                    <div class="form-group">
                        <label for="ENovDesc">Descripción</label>
                        <input type="text" class="form-control" id="ENovDesc" maxlength="40" data-field="NovDesc">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dialog-footer">
        <button class="font09 pointer float-right btn btn-outline-secondary border-0" id="cancel-dialog-causa">
            Cerrar
        </button>
        <button class="btn btn-custom font09" id="accept-dialog-causa">
            Aceptar
        </button>
    </div>
</dialog>