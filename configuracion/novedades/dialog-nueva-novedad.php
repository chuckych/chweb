<dialog id="dialogNuevaNovedad" style="width: 450px; max-width: 95%;" class="fadeIn">
    <div class="dialog-header">
        <p class="dialog-header-title" id="dialog-nueva-novedad-title">Nueva Novedad</p>
    </div>
    <div class="dialog-body">
        <div class="row">
            <div class="col-12 col-md-4 form-group">
                <label for="ANovCodi">Codigo</label>
                <input type="text" inputmode="numeric" class="form-control" id="ANovCodi">
            </div>
            <div class="col-12 col-md-8 form-group">
                <label for="ANovDesc">Descripcion</label>
                <input type="text" class="form-control" id="ANovDesc" maxlength="40">
            </div>
        </div>
        <div class="row">
            <div class="col-12 form-group">
                <label for="ANovTipo">Tipo</label>
                <select class="form-control" id="ANovTipo">
                    <option value="0">Llegada tarde</option>
                    <option value="1">Incumplimiento</option>
                    <option value="2">Salida anticipada</option>
                    <option value="3">Ausencia</option>
                    <option value="4">Licencia</option>
                    <option value="5">Accidente</option>
                    <option value="6">Vacaciones</option>
                    <option value="7">Suspension</option>
                    <option value="8">ART</option>
                </select>
            </div>
        </div>
    </div>
    <div class="dialog-footer">
        <button class="font09 pointer float-right btn btn-outline-secondary border-0" id="cancel-dialog-nueva-novedad">
            Cerrar
        </button>
        <button class="btn btn-custom font09" id="accept-dialog-nueva-novedad">
            Crear
        </button>
    </div>
</dialog>
