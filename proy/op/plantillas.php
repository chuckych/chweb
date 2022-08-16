<style>
    ul .page-link {
        height: 45px !important;
        width: 45px !important;
        border-radius: 0 !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
    }

    td .card {
        border-radius: 0 !important;
        border: 0 !important;
        border-bottom: 1px solid #ddd !important;
    }

    td .card:hover {
        background-color: #f8fafc !important;
    }

    td {
        border-style: none;
    }

    .cardSelPlantilla {
        color: #206bc4;
        background: #206bc40a;
        border-color: #90b5e2;
        margin-top: 15px;
    }
</style>
<div class="animate__animated animate__fadeIn container">
    <div class="row">
        <div class="col-12 col-sm-6 bg-white">
            <table class="table text-wrap invisible border p-2 w-100" id="tablePlantillas"></table>
        </div>
        <div class="col-12 col-sm-6 bg-white">
            <input type="hidden" id="selPlantilla">
            <p id="selPlantillaNombre"></p>
            <table class="table text-wrap invisible border p-2 w-100" id="tablePlantProcesos"></table>
        </div>
    </div>
</div>
<script src="op/js/dataPlantillas.js?<?= vjs() ?>"></script>
<script src="op/js/dataPlantillaProc.js?<?= vjs() ?>"></script>