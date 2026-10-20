<dialog id="dialogNovedad1" style="width: 450px; max-width: 95%;" class="fadeIn">
    <div class="dialog-header">
        <p class="dialog-header-title" id="dialog-novedad1-title">Editar Novedad Adicional</p>
    </div>
    <div class="dialog-body">
        <div class="row d-none">
            <div class="col-12 col-md-4 form-group">
                <label for="N1NovItem">Item</label>
                <input type="text" inputmode="numeric" class="form-control" id="N1NovItem" maxlength="5">
            </div>
        </div>
        <div class="d-flex flex-column w-100">
            <div class="row">
                <div class="col-12 col-md-12 form-group">
                    <label for="N1NovCoAd">Novedad</label>
                    <select class="form-control" id="N1NovCoAd">
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 form-group">
                    <label for="N1NovMeJo">Para</label>
                    <select class="form-control" id="N1NovMeJo">
                        <option value="0">Todos</option>
                        <option value="1">Mensuales</option>
                        <option value="2">Jornales</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 form-group">
                    <label for="N1NovDia">Dia</label>
                    <select class="form-control" id="N1NovDia">
                        <option value="0">Domingo</option>
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miercoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sabado</option>
                        <option value="7">Feriado nacional</option>
                        <option value="8">Feriado optativo</option>
                        <option value="9">Feriado empresarial</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4 form-group">
                    <label for="N1NovFact">Factor</label>
                    <input type="text" class="form-control" id="N1NovFact" maxlength="12" placeholder="0.00" value="1">
                </div>
                <div class="col-12 col-md-4 form-group">
                    <label for="N1NovDesde">Desde</label>
                    <input type="time" class="form-control" id="N1NovDesde" step="60">
                </div>
                <div class="col-12 col-md-4 form-group">
                    <label for="N1NovHasta">Hasta</label>
                    <input type="time" class="form-control" id="N1NovHasta" step="60">
                </div>
            </div>
        </div>
    </div>
    <div class="dialog-footer">
        <button class="font09 pointer float-right btn btn-outline-secondary border-0" id="cancel-dialog-novedad1">
            Cerrar
        </button>
        <button class="btn btn-custom font09" id="accept-dialog-novedad1">
            Aceptar
        </button>
    </div>
</dialog>