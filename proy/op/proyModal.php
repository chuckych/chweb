<form action="" method="post" autocomplete="off" id="proyForm">
    <div class="modal modal-blur fade" id="proyModal" tabindex="-1" aria-labelledby="proyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">ALTA PROYECTO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" name="ProyNom" id="ProyNom"
                                    placeholder="Nombre del proyecto">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-8 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Descripción</label>
                                <input type="text" class="form-control" name="ProyDesc" id="ProyDesc"
                                    placeholder="Descripción del proyecto">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                            <div class="form-group w-100" id="form-group-Empr">
                                <label class="form-label">Empresa</label>
                                <select class="form-control w-100" name="ProyEmpr" id="ProyEmpr"></select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Responsable</label>
                                <select class="form-control w-100" name="ProyResp" id="ProyResp"></select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Plantilla Procesos</label>
                                <select class="form-control w-100" name="ProyPlant" id="ProyPlant"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-control w-100" name="ProyEsta" id="ProyEsta"></select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Inicio</label>
                                <input type="text" class="form-control text-center tracking-wide ProyIniFin" name="ProyIniFin"
                                    id="ProyIniFin" placeholder="Inicio">
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Plantilla Planos</label>
                                <select class="form-control w-100" name="ProyPlantPlanos" id="ProyPlantPlanos"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control h120 p-3" name="ProyObs" id="ProyObs"
                                    placeholder="Observaciones"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto btn-light h50" data-bs-dismiss="modal">Cerrar</button>
                    <div id="divSubmit">
                        <button type="submit" class="btn btn-teal h50" id="ProySubmit"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="op/js/select.js?<?=vjs()?>"></script>