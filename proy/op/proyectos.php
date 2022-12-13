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

    td.border-bottom {
        border-bottom: 1px solid #cecece !important;
    }

    @media screen and (min-width: 350px) {
        #btnActualizarGrillaProy {
            display: none;
        }
    }
</style>
<div class="animate__animated animate__fadeIn container">
    <div class="row">
        <div class="col-12 bg-white">
            <table class="table text-nowrap invisible border p-3 w-100" id="tableProyectos"></table>
        </div>
    </div>
</div>
<script src="op/js/dataProyectos.js?<?=version_file("/proy/op/js/dataProyectos.js")?>"></script>
<script src="op/js/select.js?<?=version_file("/proy/op/js/select.js")?>"></script>