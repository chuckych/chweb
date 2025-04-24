<?php
require_once __DIR__ . '/../../config/index.php';
?>
<!-- Empresas Tab -->
<div class="tab-pane fade show active invisible" id="empresa" role="tabpanel" aria-labelledby="empresa-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-wrap w-100 border table-hover" id="tableEmpresa"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Empresas Tab -->
<!-- Plantas Tab -->
<div class="tab-pane fade invisible" id="planta" role="tabpanel" aria-labelledby="planta-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-wrap w-100 border table-hover" id="tablePlantas"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Plantas Tab -->
<!-- Convenios -->
<div class="tab-pane fade invisible" id="convenio" role="tabpanel" aria-labelledby="convenio-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableConvenios"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Convenios -->
<!-- Sectores / Secciones Tab -->
<div class="tab-pane fade invisible" id="sector" role="tabpanel" aria-labelledby="sector-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableSectores"></table>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableSecciones"></table>
            </div>
            <div id="seccion"></div>
        </div>
    </div>
</div>
<!-- Fin Sectores / Secciones Tab -->
<!-- Grupos Tab -->
<div class="tab-pane fade invisible" id="grupo" role="tabpanel" aria-labelledby="grupo-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableGrupos"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Grupos Tab -->
<!-- Sucursales Tab -->
<div class="tab-pane fade invisible" id="sucursal" role="tabpanel" aria-labelledby="sucursal-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableSucursales"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Sucursales Tab -->
<!-- Personal Tab -->
<div class="tab-pane fade invisible" id="personal" role="tabpanel" aria-labelledby="personal-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tablePersonal"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Personal Tab -->
<!-- Copiar Listas Tab -->
<div class="tab-pane fade invisible" id="copyListas" role="tabpanel" aria-labelledby="copyListas-tab">
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="alert alert-warning fw5 fontq mt-3">Copiar configuracion de la estructura del
                usuario:<br /><span class="font-weight-bold nombreUsuario"></span><br />a la siguiente selección</div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="table-responsive mt-2">
                <table class="table text-nowrap w-100 border table-hover" id="tableCopyListas"></table>
            </div>
        </div>
    </div>
</div>
<!-- Fin Copiar Listas Tab -->
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataEmpr.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataPlan.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataConv.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataSect.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataSecc.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataGrup.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataSucu.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataPers.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataCopy.js?v=<?= vjs() ?>"></script>