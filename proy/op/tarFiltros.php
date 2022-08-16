<div class="offcanvas offcanvas-start shadow" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1" id="offcanvasFiltrosTar" aria-labelledby="offcanvasFiltrosTarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title h2" id="offcanvasFiltrosTarLabel">Filtros</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="">
                    <label class="font07 text-muted pb-2">Estado Proyecto</label>
                    <div class="border p-1 flex-center-between w-100">
                        <label class="form-selectgroup-item w-100">
                            <input type="radio" name="tarProyEsta" id="tarProyEstaAbierto" value="abierto" class="form-selectgroup-input" checked="">
                            <span class="form-selectgroup-label flex-center-center h40 border-0">
                                <span class="font07">Abierto</span>
                            </span>
                        </label>
                        <label class="form-selectgroup-item w-100">
                            <input type="radio" name="tarProyEsta" id="tarProyEstaPausado" value="pausado" class="form-selectgroup-input">
                            <span class="form-selectgroup-label flex-center-center h40 border-0">
                                <span class="font07">Pausado</span>
                            </span>
                        </label>
                        <label class="form-selectgroup-item w-100">
                            <input type="radio" name="tarProyEsta" id="tarProyEstaCerrado" value="cerrado" class="form-selectgroup-input">
                            <span class="form-selectgroup-label flex-center-center h40 border-0">
                                <span class="font07">Cerrado</span>
                            </span>
                        </label>
                        <label class="form-selectgroup-item w-100">
                            <input type="radio" name="tarProyEsta" id="tarProyEstaTodos" value="" class="form-selectgroup-input">
                            <span class="form-selectgroup-label flex-center-center h40 border-0">
                                <span class="font07">Todos</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="form-group">
                    <select title="Filtrar Proyecto" class="form-control w-100" name="tarProyNomFiltro" id="tarProyNomFiltro"></select>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="form-group">
                    <select title="Filtrar Proceso" class="form-control w-100" name="tarProcNomFiltro" id="tarProcNomFiltro"></select>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="form-group w-100" id="form-group-Empr">
                    <select title="Filtrar Empresas" class="form-control w-100" name="tarEmprFiltro" id="tarEmprFiltro"></select>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="form-group">
                    <select title="Filtrar Plano" class="form-control w-100" name="tarPlanoFiltro" id="tarPlanoFiltro"></select>
                </div>
            </div>
            <div class="col-12 mb-3">
                <div class="form-group form-group-tarRespFiltro">
                    <select title="Filtrar Responsable" class="form-control w-100" name="tarRespFiltro" id="tarRespFiltro"></select>
                </div>
            </div>
            <div class="col-12 flex-center-end">
                <button class="btn btn-outline-azure h40 font08 tarLimpiaFiltro" data-titlel="Limpiar Filtros"><i class="bi bi-eraser"></i></button>
            </div>
        </div>
    </div>
</div>
<script src="op/js/filtroTar.js?<?= microtime(true) ?>"></script>