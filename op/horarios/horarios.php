<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['horarios'] ?></title>
    <style>
        .table.dataTable {
            margin-top: 0 !important;
        }
        #tableHorarios thead{
            display: none;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'reloj.png', 'Administración de ' . MODULOS['horarios'], '') ?>
        <div class="row bg-white py-3 invisible" id="PersonalTable">
            <div class="col-12">
                <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal" data-target="#Filtros">
                    Filtros
                </button>
                <span id="trash_all" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Limpiar Filtros</b>" class="fontq text-secondary mx-1 pointer"><i class="bi bi-trash"></i></span>
                <!-- </div> -->
                <div class="custom-control custom-switch float-right pt-1">
                    <input type="checkbox" class="custom-control-input" name="_eg" id="_eg">
                    <label class="custom-control-label" for="_eg" style="padding-top: 3px;" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Filtrar Legajos Inactivos</b>">De Baja</label>
                </div>
                <div class="custom-control custom-switch float-right pt-1 mr-2">
                    <input type="checkbox" class="custom-control-input" name="_porApNo" id="_porApNo">
                    <label class="custom-control-label" for="_porApNo" style="padding-top: 3px;" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Ordenar por Nombre</b>">Por Nombre</label>
                </div>
            </div>
            <div class="col-12 col-sm-5 pt-3">
                <table class="table table-hover text-nowrap w-100" id="tablePersonal"></table>
            </div>
            <div class="col-12 col-sm-7 mt-sm-4 w-100">
                <div class="h-100">
                    <div class="" id="divData"></div>
                    <div class="mb-2" id="divHorarioActual"></div>
                    <div class="animate__animated animate__fadeIn" id="detalleHorario">
                        <div class="text-secondary text-center w-100 mt-5 d-flex align-items-center justify-content-center w-100" style="height: 200px;"><p>Seleccionar un Legajo</p></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="p-3" id="divGrillaHorario"></div>
            </div>
            <div id="divModal"></div>
            <div id="divactModal"></div>
            <div id="divactModalCit"></div>
                
        </div>
    </div>
    <!-- Fin Encabezado -->
    </div>
    <!-- fin container -->
    <?php

    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLES */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="../../js/bootbox.min.js"></script>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="js/data.js?v=<?= vjs() ?>"></script>
    <script src="getSelect/select.js?v=<?= vjs() ?>"></script>
</body>

</html>