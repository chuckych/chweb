<?php
$modulo = '2';
$Cliente = ExisteCliente($_GET['_c']);
$Rol     = ExisteRol3($_GET['_r'], $_GET['id'])
?>
<style>
    #tableNovedades .dtrg-level-0 td {
        font-size: 1rem;
        /* background-color: #F8F9FA !important; */
        font-weight: 500 !important;
        border-bottom: 2px solid #cecece !important;
    }

    #tableNovedades .table-active>td {
        background-color: #F8F9FA !important;

    }

    #tableONovedades .dtrg-level-0 td {
        font-size: 1rem;
        /* background-color: #F8F9FA !important; */
        font-weight: 500 !important;
        border-bottom: 2px solid #cecece !important;
    }

    #tableONovedades .table-active>td {
        background-color: #F8F9FA !important;

    }

    #tableHorarios .dtrg-level-0 td {
        font-size: 1rem;
        /* background-color: #F8F9FA !important; */
        font-weight: 500 !important;
        border-bottom: 2px solid #cecece !important;
    }

    #tableHorarios .table-active>td {
        background-color: #F8F9FA !important;

    }

    #tableRotaciones .dtrg-level-0 td {
        font-size: 1rem;
        /* background-color: #F8F9FA !important; */
        font-weight: 500 !important;
        border-bottom: 2px solid #cecece !important;
    }

    #tableRotaciones .table-active>td {
        background-color: #F8F9FA !important;

    }

    #tableTipoHoras .dtrg-level-0 td {
        font-size: 1rem;
        /* background-color: #F8F9FA !important; */
        font-weight: 500 !important;
        border-bottom: 2px solid #cecece !important;
    }

    #tableTipoHoras .table-active>td {
        background-color: #F8F9FA !important;

    }
</style>
<input type="hidden" id="recid_rol" value="<?= $Rol['recid'] ?>">
<input type="hidden" id="cliente_rol" value="<?= $_GET['_c'] ?>">
<input type="hidden" id="id_rol" value="<?= $Rol['id'] ?>">
<div class="row bg-white mt-n3">
    <div class="col-12">
        <div class="border p-2">
            <nav class="fontq">
                <div class="nav nav-tabs bg-light" id="nav-tab" role="tablist">
                    <a class="px-3 nav-item nav-link active text-dark" id="novedades-tab" data-toggle="tab" href="#novedades" role="tab" aria-controls="novedades" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaRol(1) ?></span><!-- Novedades -->
                        <span class="text-tab d-block d-sm-none">Nov</span><!-- Novedades -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="o_novedades-tab" data-toggle="tab" href="#o_novedades" role="tab" aria-controls="o_novedades" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaRol(2) ?></span><!-- Otras Novedades -->
                        <span class="text-tab d-block d-sm-none">O Nov</span><!-- Otras Novedades -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="horarios-tab" data-toggle="tab" href="#horarios" role="tab" aria-controls="horarios" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaRol(3) ?></span><!-- Horarios -->
                        <span class="text-tab d-block d-sm-none">Hor</span><!-- Horarios -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="rotaciones-tab" data-toggle="tab" href="#rotaciones" role="tab" aria-controls="rotaciones" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaRol(4) ?></span><!-- Rotaciones -->
                        <span class="text-tab d-block d-sm-none">Rot</span><!-- Rotaciones -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="tipoHoras-tab" data-toggle="tab" href="#tipoHoras" role="tab" aria-controls="tipoHoras" aria-selected="true">
                        <span class="text-tab d-none d-sm-block"><?= listaRol(5) ?></span><!-- Tipos de Horas -->
                        <span class="text-tab d-block d-sm-none">T Hor</span><!-- Tipos de Horas -->
                    </a>
                    <a class="px-3 nav-item nav-link text-dark" id="copyListas-tab" data-toggle="tab" href="#copyListas" role="tab" aria-controls="copyListas" aria-selected="true">
                        <span class="text-tab d-none d-sm-block">Copiar Listas</span>
                        <span class="text-tab d-block d-sm-none">Copiar</span>
                    </a>
                </div>
            </nav>
            <div class="tab-content px-2" id="nav-tabContent">
                <?php //require 'tabs.php?v=' . vjs() ?>
            </div>
        </div>
    </div>
</div>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataNov.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataoNov.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataHorarios.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataRotacion.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataTipoHora.js?v=<?= vjs() ?>"></script>
<script src="/<?= HOMEHOST ?>/usuarios/listas/js/dataCopyLista.js?v=<?= vjs() ?>"></script>
<script>
    $('#modalListasLabel').html(`<div><label class="fontq w70 my-0">Cuenta:</label><span class="fw5 fontq"><?= $Cliente ?></span></div><div><label class="fontq w70 my-0">Rol:</label><span class="fw5 fontq nombreRol"><?= $Rol['nombre'] ?></span></div>`)
</script>