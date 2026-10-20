<dialog id="dialogHoras" style="width: 450px; max-width: 95%;" class="fadeIn">
    <div class="dialog-header">
        <p class="dialog-header-title" id="dialog-horas-title">Editar Novedad por Hora</p>
    </div>
    <div class="dialog-body">
        <div class="row">
            <div class="col-12 form-group">
                <label for="HNovHora">Tipo de Hora</label>
                <select class="form-control" id="HNovHora">
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="HNovFact">Factor</label>
                <input type="text" class="form-control" id="HNovFact" maxlength="12" placeholder="0.00" value="1">
            </div>
        </div>
    </div>
    <div class="dialog-footer">
        <button class="font09 pointer float-right btn btn-outline-secondary border-0" id="cancel-dialog-horas">
            Cerrar
        </button>
        <button class="btn btn-custom font09" id="accept-dialog-horas">
            Aceptar
        </button>
    </div>
</dialog>
