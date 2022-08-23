<div class="animate__animated animate__fadeIn container">
    <div class="row">
        <div class="col-12">

            <div class="bg-white py-3">
                <div class="mt-2 mt-sm-0">
                    <h3 class="card-title font12 font-weight-bold text-tabler w-100 text-center">¿Confirma los datos seleccionados?</h3>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <ul class="list list-timeline">
                        <li>
                            <div class="list-timeline-icon bg-success">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="list-timeline-content">
                                <div class="list-timeline-time">Proyecto</div>
                                <p class="list-timeline-title ProyNom"></p>
                                <p class="text-muted ProyDesc"></p>
                            </div>
                        </li>
                        <li>
                            <div class="list-timeline-icon bg-success">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="list-timeline-content">
                                <div class="list-timeline-time">Proceso</div>
                                <p class="list-timeline-title ProcDesc"></p>
                            </div>
                        </li>
                        <li>
                            <div class="list-timeline-icon bg-success">
                                <i class="bi bi-check-lg"></i>
                            </div>
                            <div class="list-timeline-content">
                                <div class="list-timeline-time">Plano</div>
                                <p class="list-timeline-title PlanoDesc"></p>
                                <p class="text-muted PlanoCod"></p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="text-center">
                    <div class="mt-5 mt-sm-0"></div>
                    <button type="button" class="btn btn-lg font09 btn-light border-0" id="tarCancelar">Cancelar</button>
                    <button type="button" class="btn btn-lg font09 btn-green border-0 ms-4" id="tarSubmit">Confirmar</button>
                </div>
            </div>
            <?= progressBar(4) ?>
        </div>
    </div>
</div>
<script src="/<?= HOMEHOST ?>/proy/finalizar/dataFinalizar.js?<?= vjs() ?>"></script>