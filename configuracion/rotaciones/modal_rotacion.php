<div class="modal fadeIn" id="modal-rotacion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content bg-light p-2">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex flex-column w-100">
                    <div class="d-inline-flex w-100 align-items-center mb-2 justify-content-between">
                        <p class="modal-title text-truncate" style="max-width: 100%;">Nueva rotación</p>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="bi bi-x-lg"></span>
                        </button>
                    </div>
                    <div class="config-card w-100">
                        <div class="row g-3 header-inputs align-items-end">
                            <div class="col-md-3 col-12">
                                <label class="form-label-sm">Código</label>
                                <input type="number" class="form-control" id="RotCodi" min="1" max="32767" step="1" inputmode="numeric" pattern="[0-9]*">
                            </div>
                            <div class="col-md-9 col-12">
                                <label class="form-label-sm">Descripción</label>
                                <input type="text" class="form-control" id="RotDesc" maxlength="40" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body mt-n2">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">Detalle de horarios</h6>
                    <button type="button" class="btn font09 btn-custom border" id="btn-add-item-rotacion">
                        Agregar item
                    </button>
                </div>
                <div class="table-responsive">
                    <div class="p-2 shadow-sm border radius bg-white">
                        <table class="table w-100" id="tbl-rotacion-items">
                            <thead>
                                <tr>
                                    <th class="d-none">Item</th>
                                    <th class="w-100">Horario</th>
                                    <th>Días</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <small class="text-muted">Cada fila representa un item de rotación. Item y horario no pueden repetirse dentro de la misma rotación.</small>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="font09 pointer float-right btn btn-outline-secondary border" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-custom font09" id="btn-save-rotacion">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fadeIn" id="modal-confirm-delete-rotacion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Confirmar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="bi bi-x-lg"></span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="confirm-delete-rotacion-text">¿Confirma eliminar esta rotación?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="font09 pointer float-right btn btn-outline-secondary border" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger font09" id="btn-confirm-delete-rotacion">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fadeIn" id="modal-importar-rotaciones" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Importar rotaciones</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="bi bi-x-lg"></span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-2 font09">Descargue el archivo de ejemplo para completar las rotaciones con el formato correcto.</p>
                <label for="import-rotaciones-file" class="btn btn-outline-custom font09 border mb-2 radius pointer">
                    Seleccionar archivo Excel
                </label>
                <br>
                <span id="selected-rotaciones-file-name" class="font08 ml-2 text-muted">Ningún archivo seleccionado</span>
                <input type="file" id="import-rotaciones-file" hidden accept=".xls,.xlsx">
                <br>
                <div id="import-rotaciones-alert" class="alert alert-warning d-none mb-0" role="alert">
                    <div class="font-weight-bold mb-1">Advertencias encontradas antes de importar</div>
                    <ul class="mb-0 pl-3 font08" id="import-rotaciones-alert-list"></ul>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-between">
                <button type="button" class="font09 pointer btn btn-outline-secondary border" data-dismiss="modal">Cerrar</button>
                <div class="d-flex align-items-center" style="gap:8px;">
                    <button type="button" class="btn btn-outline-custom font09 border" id="btn-descargar-ejemplo-importar-rotaciones-xls">
                        Descargar ejemplo
                    </button>
                    <button type="button" class="btn btn-custom font09" id="btn-importar-rotaciones-xls-modal">
                        Importar XLS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fadeIn" id="modal-unused-rotaciones" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Rotaciones no utilizadas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="bi bi-x-lg"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive" style="display:none;">
                    <table class="shadow-sm text-nowrap w-100 table p-3 border radius loader-in" id="tblUnusedRotaciones"></table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="font09 pointer float-right btn btn-outline-secondary border" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-danger font09" id="btn-confirm-unused-rotacion">Eliminar todo</button>
            </div>
        </div>
    </div>
</div>
