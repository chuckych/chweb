<?php
require_once __DIR__ . '/../../config/index.php';
?>
<!-- Novedaes Tab -->
<div class="tab-pane fade invisible show active" id="novedades" role="tabpanel" aria-labelledby="novedades-tab">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="table-responsive mt-2">
                <table class="table text-wrap w-100 border table-hover" id="tableNovedades"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Novedaes Tab -->
<!-- Otras Novedades Tab -->
<div class="tab-pane fade invisible" id="o_novedades" role="tabpanel" aria-labelledby="o_novedades-tab">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="table-responsive mt-2">
                <table class="table text-wrap w-100 border table-hover" id="tableONovedades"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Otras Novedades Tab -->
<!-- Horarios Tab -->
<div class="tab-pane fade invisible" id="horarios" role="tabpanel" aria-labelledby="horarios-tab">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableHorarios"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Horarios Tab -->
<!-- Rotaciones Tab -->
<div class="tab-pane fade invisible" id="rotaciones" role="tabpanel" aria-labelledby="rotaciones-tab">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableRotaciones"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Rotaciones Tab -->
<!-- Tipo Horas Tab -->
<div class="tab-pane fade invisible" id="tipoHoras" role="tabpanel" aria-labelledby="tipoHoras-tab">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableTipoHoras"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Tipo Horas Tab -->
<!-- Copiar Listas Tab -->
<div class="tab-pane fade invisible" id="copyListas" role="tabpanel" aria-labelledby="copyListas-tab">
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="alert alert-warning fw5 fontq mt-3">Copiar configuracion de las Listas del rol <span
                    class="font-weight-bold nombreRol"></span> a Roles</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableCopyListas"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Copiar Listas Tab -->
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataNov-min.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataoNov-min.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataHorarios-min.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataRotacion-min.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataTipoHora-min.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataCopyLista-min.js?v=<?= vjs() ?>"></script>