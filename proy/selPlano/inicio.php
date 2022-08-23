<div class="animate__animated animate__fadeIn container">
    <div class="row">
        <div class="col-12" id="listSelPlano" style="display: none;">
            <table class="table w-100 text-nowrap" id="selectPlano">
            </table>
            <div class="row mt-sm-3 mt-1 mb-4" id="listSelPlanoRow">

            </div>
            <div class="mt-4">
                <button class="mt-2 btn btn-green float-end px-4" id="omitePlano">Omitir</button>
                <button class="mt-2 btn btn-azure float-end px-3 me-2" id="btnAltaPlano">+ Nuevo Plano</button>
            </div>
        </div>
    </div>
    <?= progressBar(3) ?>
</div>
<script src="/<?= HOMEHOST ?>/proy/selPlano/dataSelPlano.js?<?= vjs(true) ?>"></script>