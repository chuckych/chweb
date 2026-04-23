<div class="row mt-2" id="form-nuevo-campo">
    <div class="col-12">
        <div class="card border radius">
            <div class="card-body border">
                <!-- <h6 class="mb-3">Definir campos</h6> -->

                <div id="campo-feedback" class="alert alert-danger d-none mb-3" role="alert"></div>

                <div class="form-row align-items-end">
                    <div class="col-sm-6 col-md-2 mb-3">
                        <label for="campo-posicion">Posición</label>
                        <input type="number" min="1" step="1" class="form-control h40" id="campo-posicion"
                            placeholder="Ej: 1">
                    </div>

                    <div class="col-sm-6 col-md-3 mb-3">
                        <label for="campo-tipo">Tipo de campo</label>
                        <select id="campo-tipo" class="form-control" style="width:100%;">
                            <option value=""></option>
                            <option value="legajo">Legajo</option>
                            <option value="fecha">Fecha</option>
                            <option value="novedades">Novedades</option>
                            <option value="horas">Horas</option>
                        </select>
                    </div>

                    <div class="col-sm-6 col-md-3 mb-3 d-none fadeIn" id="subtipo-wrapper">
                        <label for="campo-subtipo">Valor</label>
                        <select id="campo-subtipo" class="form-control" style="width:100%;"></select>
                        <small id="subtipo-help" class="form-text text-muted d-none">Cargando...</small>
                    </div>

                    <div class="col-sm-6 col-md-2 mb-3">
                        <label for="campo-tamano">Tamaño</label>
                        <input type="number" min="1" step="1" class="form-control h40" id="campo-tamano"
                            placeholder="Ej: 5">
                    </div>

                    <div class="col-sm-6 col-md-2 mb-3">
                        <label for="campo-formato">Formato</label>
                        <select id="campo-formato" class="form-control" style="width:100%;">
                            <option value=""></option>
                            <option value="numero">Numero</option>
                            <option value="decimal">Decimal</option>
                            <option value="YYYY-MM-DD">Fecha YYYY-MM-DD</option>
                            <option value="MM-DD-YYYY">Fecha MM-DD-YYYY</option>
                            <option value="DD-MM-YYYY">Fecha DD-MM-YYYY</option>
                        </select>
                    </div>
                </div>
                <!-- Nota -->
                <div class="mt-2">
                    <small>
                        <ul class="m-0 pl-3">
                            <li>Para editar un campo, debe añadir otro en la misma posición.</li>
                            <li>El orden de los campos se puede modificar arrastrando el ícono <i class="bi bi-list"></i></li>
                            <li>Puede eliminar un campo haciendo clic en el ícono <i class="bi bi-trash"></i></li>
                        </ul>
                    </small>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-custom font09" id="btn-agregar-campo">Agregar campo</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3 mb-3">
    <div class="col-12">
        <div class="table-responsive">
            <div class="border radius p-3">
                <table class="table text-nowrap w-100 fadeIn">
                    <thead class="font08">
                        <tr>
                            <th class="h40">Posición</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Tamaño</th>
                            <th>Formato</th>
                            <th class="text-right" style="width: 140px;"></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-campos-body">
                        <tr id="tabla-campos-vacio">
                            <td colspan="6" class="text-center text-muted">Todavia no hay campos agregados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>