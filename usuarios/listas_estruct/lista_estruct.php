<?php
// $modulo = '2';
$Cliente = ExisteCliente($_GET['_c']);
if (!ExisteUser($_GET['_c'], $_GET['uid'])) {
    echo '<div class="alert alert-danger fontq fw5"><b>No existe usuario</b></div>';
    exit;
}
?>
<style>
    .table-active>td {
        background-color: #F8F9FA !important;
    }
</style>
<input type="hidden" id="cliente" value="<?= $_GET['_c'] ?>">
<input type="hidden" id="uid" value="<?= $_GET['uid'] ?>">
<div class="row bg-white mt-n3">
    <div class="col-12 d-inline-flex justify-content-between align-items-center">
        <div class="custom-control custom-switch" data-titler="Quitar relación de estructura" id="divRelacion">
            <input type="checkbox" checked class="custom-control-input" id="relacionesSwith" disabled>
            <label class="custom-control-label mr-2" for="relacionesSwith" style="padding-top: 3px;"><i class="bi bi-diagram-3-fill text-secondary fonth"></i><span class="fontp text-secondary ml-2" id="spanFinishTable"></span></label>
        </div>
        <button class="btn btn-sm btn-link fontq float-right p-1" data-titlel="Inicializar Estructura" id="initEstruct">Inicializar <i class="bi bi-eraser"></i></button>
    </div>
    <div class="col-12">
        <div class="border p-2">
            <nav class="fontq">
                <div class="nav nav-tabs bg-light" id="nav-tab" role="tablist">
                    <a class="px-3 nav-item nav-link active text-dark" id="empresa-tab" data-toggle="tab" href="#empresa" role="tab" aria-controls="empresa" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaEstruct(1) ?></span><!-- Empresas -->
                        <span class="text-tab d-block d-sm-none">Emp</span><!-- Empresas -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="planta-tab" data-toggle="tab" href="#planta" role="tab" aria-controls="planta" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaEstruct(2) ?></span><!-- Plantas -->
                        <span class="text-tab d-block d-sm-none">Plan</span><!-- Plantas -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="convenio-tab" data-toggle="tab" href="#convenio" role="tab" aria-controls="convenio" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaEstruct(3) ?></span><!-- Convenios -->
                        <span class="text-tab d-block d-sm-none">Conv</span><!-- Convenios -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="sector-tab" data-toggle="tab" href="#sector" role="tab" aria-controls="sector" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaEstruct(4) ?></span><!-- sectores -->
                        <span class="text-tab d-block d-sm-none">Sect</span><!-- sectores -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="grupo-tab" data-toggle="tab" href="#grupo" role="tab" aria-controls="grupo" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaEstruct(6) ?></span><!-- Grupos -->
                        <span class="text-tab d-block d-sm-none">Grup</span><!-- Grupos -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="sucursal-tab" data-toggle="tab" href="#sucursal" role="tab" aria-controls="sucursal" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaEstruct(7) ?></span><!-- Sucursales -->
                        <span class="text-tab d-block d-sm-none">Suc</span><!-- Sucursales -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaEstruct(8) ?></span>
                        <span class="text-tab d-block d-sm-none">Per</span>
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="copyListas-tab" data-toggle="tab" href="#copyListas" role="tab" aria-controls="copyListas" aria-selected="true">
                        <span class="text-tab d-none d-sm-block">Copiar Estructura</span>
                        <span class="text-tab d-block d-sm-none">Copiar</span>
                    </a>
                </div>
            </nav>
            <div class="tab-content px-2" id="nav-tabContent">
            </div>
        </div>
    </div>
</div>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataEmpr.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataPlan.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataConv.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataSect.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataSecc.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataGrup.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataSucu.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataPers.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas_estruct/js/dataCopy.js?v=<?= vjs() ?>"></script>
<script>
    $('#modalListasLabel').html(`<div><label class="fontq w70 my-0">Usuario:</label><span class="fw5 fontq" id="nombreUsuario"><?= $_GET['nombre'] ?></div><div><label class="fontq w70 my-0">Rol:</label><span class="fw5 fontq nombreRol"><?= $_GET['rol_n'] ?></span></div>`)
</script>