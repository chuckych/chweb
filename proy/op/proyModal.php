<form action="" method="post" autocomplete="off" id="proyForm">
    <div class="modal modal-blur fade" id="proyModal" tabindex="-1" aria-labelledby="proyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">ALTA PROYECTO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="bg-white p-3">
                        <ul class="nav nav-tabs" data-bs-toggle="tabs">
                            <li class="nav-item font09">
                                <a href="#tabs-proyecto" class="nav-link radius-0 active h50 w100 flex-center-center" data-bs-toggle="tab">
                                    Proyecto
                                </a>
                            </li>
                            <li class="nav-item font09">
                                <a href="#tabs-planos" class="nav-link radius-0 h50 w100 flex-center-center" data-bs-toggle="tab">
                                    Planos
                                </a>
                            </li>
                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane show active mt-2" id="tabs-proyecto">
                                    <div class="row">
                                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" class="form-control" name="ProyNom" id="ProyNom" placeholder="Nombre del proyecto">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-8 col-12 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Descripción</label>
                                                <input type="text" class="form-control" name="ProyDesc" id="ProyDesc" placeholder="Descripción del proyecto">
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
                                                <label class="form-label">Inicio</label>
                                                <input type="text" class="form-control text-center tracking-wide ProyIniFin" name="ProyIniFin" id="ProyIniFin" placeholder="Inicio">
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
                                                <label class="form-label">Plantilla Procesos</label>
                                                <select class="form-control w-100" name="ProyPlant" id="ProyPlant"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <div class="form-group">
                                                <label class="form-label">Observaciones</label>
                                                <textarea class="form-control p-3 " name="ProyObs" id="ProyObs" placeholder="Observaciones" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 56px;"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane mt-2" id="tabs-planos">
                                    <div class="row">
                                        <div class="col-sm-6 col-lg-4 col-12 mb-3">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Plantilla Planos</label>
                                                <select class="form-control w-100" name="ProyPlantPlanos" id="ProyPlantPlanos"></select>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Asignar Planos</label>
                                                <select class="form-control" name="ProyAsignPlanos" id="ProyAsignPlanos"></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-8 col-12 mb-3">
                                            <label class="form-label">Planos Asignados</label>
                                            <div class="bg-white border">
                                                <div class="card-header h50 px-3">
                                                    <span class="card-title form-label font09">Total: (<span id="TotalPlanosAsignados">0</span>)</span>
                                                </div>
                                                <div class="list-group list-group-flush overflow-auto" id="cardProyPlanos" style="max-height: 150px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3 mb-3">

                                        </div>
                                        <div class="col-auto"></div>
                                    </div>
                                    <div class="row border p-1">
                                        <label class="form-label pt-2">Crear Plano</label>
                                        <div class="col-sm-6 col-lg-3 col-12 mb-3">
                                            <div class="form-group w-100" id="form-group-Empr">
                                                <input type="text" placeholder="Nombre" name="PlanoDesc" id="PlanoDesc" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-3 col-12 mb-3">
                                            <div class="form-group">
                                                <input type="text" placeholder="Código" name="PlanoCod" id="PlanoCod" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-lg-6 col-12 mb-3">
                                            <div class="form-group">
                                                <div class="d-inline-flex w-100">
                                                    <input type="text" placeholder="Observaciones" name="PlanoObs" id="PlanoObs" class="form-control w-100">&nbsp;
                                                    <button type="button" class="btn bg-azure-lt submitPlanoProy"><i class="bi bi-plus font15"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn me-auto btn-light h50" data-bs-dismiss="modal">Cerrar</button>
                    <div id="divSubmit">
                        <button type="submit" class="btn btn-teal h50" id="ProySubmit"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
require_once __DIR__ . '../../../funciones.php';
?>
<script src="op/js/select.js?<?=version_file("/proy/op/js/select.js")?>"></script>