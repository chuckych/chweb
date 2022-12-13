<div class="animate__animated animate__fadeIn container">
    <div class="row">
        <div class="col-12" id="listSelProc" style="display: none;">
            <table class="table w-100 text-nowrap" id="selectProc">
            </table>
            <div class="row mt-sm-3 mt-1 mb-4" id="listSelProcRow">
            </div>
            <?= progressBar(2) ?>
        </div>
    </div>
</div>
<script src="/<?= HOMEHOST ?>/proy/selProc/dataSelProc.js?<?=version_file("/proy/selProc/dataSelProc.js")?>"></script>