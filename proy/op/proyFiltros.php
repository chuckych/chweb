<div class="offcanvas offcanvas-start shadow" data-bs-scroll="true" data-bs-backdrop="true" tabindex="-1"
    id="offcanvasFiltros" aria-labelledby="offcanvasFiltrosLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title h2" id="offcanvasFiltrosLabel">Filtros</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <!-- <label class="form-label">Nombre</label> -->
                        <select title="Filtrar Proyecto" class="form-control w-100" name="ProyNomFiltro"
                            id="ProyNomFiltro"></select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-group w-100" id="form-group-Empr">
                        <!-- <label class="form-label">Empresa</label> -->
                        <select title="Filtrar Empresas" class="form-control w-100" name="ProyEmprFiltro"
                            id="ProyEmprFiltro"></select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <!-- <label class="form-label">Responsable</label> -->
                        <select title="Filtrar Responsable" class="form-control w-100" name="ProyRespFiltro"
                            id="ProyRespFiltro"></select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <!-- <label class="form-label">Plantilla</label> -->
                        <select title="Filtrar Plantilla" class="form-control w-100" name="ProyPlantFiltro"
                            id="ProyPlantFiltro"></select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <!-- <label class="form-label">Estado</label> -->
                        <select title="Filtrar Estado" class="form-control w-100" name="ProyEstaFiltro"
                            id="ProyEstaFiltro"></select>
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <div class="float-end">
                        <button class="btn btn-outline-info h50 font08 ProyLimpiaFiltro"><i
                                class="bi bi-eraser me-2"></i> Limpiar Filtros</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once __DIR__ . '/../../funciones.php';
?>
<script src="op/js/filtroProy.js?<?= version_file("/proy/op/js/filtroProy.js") ?>"></script>