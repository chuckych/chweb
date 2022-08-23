<div class="animate__animated animate__fadeIn">
    <div class="row">
        <div class="col-12" id="btnSelProy">
            <div class="border-0 mt-sm-0 mt-4">
                <div class="text-center p-sm-4">
                    <p class="m-0 p-0 display-4 animate__animated animate__fadeInUp" id="inicioNombre"></p>
                </div>
            </div>
            <div class="border-0 mt-2">
                <div class="d-flex justify-content-center">
                    <button type="button" class="px-5 btn btn-teal h80" id="btnSelProyecto">
                        <span class="display-6"><i class="bi bi-plus-circle"></i> Nueva Tarea</span>
                    </button>
                </div>
            </div>
            <div class="mt-3" style="display: none;" id="divTarPend">
                <p class="h2 text-center p-2 m-0" id="titleTareas">Tareas Pendientes</p>
                <table class="table w-100 text-wrap border shadow-sm p-2 m-0 animate__animated animate__fadeInDown" id="tableTarUser">
                </table>
            </div>
        </div>
        <div class="col-12" id="listSelProy" style="display: none;">
            <table class="table w-100 text-nowrap" id="selectProy">
            </table>
            <div class="row mt-sm-3 mt-1 mb-4" id="listSelProyRow">
            </div>
            <?= progressBar(1) ?>
        </div>
        <?php access_log_proy('SelProy'); ?>
    </div>
</div>
<script src="/<?= HOMEHOST ?>/proy/inicio/dataInicio.js?<?= vjs() ?>"></script>