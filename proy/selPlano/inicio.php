<div class="animate__animated animate__fadeIn container">
    <div class="row">
        <div class="col-12" id="listSelPlano" style="display: none;">
            <table class="table w-100 text-nowrap" id="selectPlano">
            </table>
            <div class="row mt-3" id="listSelPlanoRow">

            </div>
            <button class="mt-2 btn btn-green float-end px-4" id="omitePlano">Omitir</button>
        </div>
    </div>
    <?= progressBar(3) ?>
</div>
<script src="/<?= HOMEHOST ?>/proy/selPlano/dataSelPlano.js?<?= vjs(true) ?>"></script>