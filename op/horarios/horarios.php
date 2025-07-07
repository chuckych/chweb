<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['horarios'] ?></title>
    <style>
        .table.dataTable {
            margin-top: 0 !important;
        }

        #tableHorarios thead {
            display: none;
        }

        .table td {
            font-size: 14px !important;
        }

        .btn {
            display: inline-flex;
            align-items: center;

        }

        .divFiltros {
            display: flex;
            justify-content: space-between;
        }

        .dayGrilla {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 5px;
            padding-left: 16px;
            padding-right: 16px;
            border: 1px solid #ddd;
        }

        .dayGrilla2 {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 5px;
            padding-left: 13px;
            padding-right: 13px;
            border: 1px solid #ddd;
        }

        #tableHorarios_paginate,
        #tableHorarios_info {
            margin-top: 0px !important;
            padding-top: 0px !important;
        }

        .pagination-bottom .dataTables_paginate {
            margin-top: 0px !important;
            padding-top: 0px !important;
        }

        #tableHorarios td {
            border-top: 0px solid #dee2e6;
        }

        tr .action {
            opacity: 0.2;
        }

        tr:hover .action {
            opacity: 1;
        }

        .bi-check-square-fill {
            color: var(--main-bg-modcolor) !important;
            opacity: 0.8;
        }

        .dataTables_paginate {
            margin-top: 0px !important;
        }

        #tablePersonal_paginate,
        #tabla_paginate {
            margin-top: 20px !important;
        }
    </style>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'reloj.png', 'Administración de ' . MODULOS['horarios'], '') ?>
        <div class="row bg-white m-0 my-3">
            <div class="col-12 w-100 divFiltros px-2">
                <div class="d-inline-flex" style="gap:5px">
                    <div class="d-flex flex-row border radius">
                        <button type="button" class="btn btn-outline-custom hint--top border-0 Filtros"
                            data-toggle="modal" data-target="#Filtros" aria-label="Filtrar datos">
                            <span class="fontq">Filtros</span>
                        </button>
                        <button id="trash_all" aria-label="Limpiar Filtros"
                            class="hint--top btn border-0 btn-outline-custom fontq"><i class="bi bi-trash"></i></button>
                    </div>
                    <button class="btn border radius btn-outline-custom font08 hint--top" data-toggle="collapse"
                        href="#collapseMasivos" role="button" aria-expanded="false" aria-controls="collapseMasivos"
                        aria-label="Según filtros">
                        Ingreso Masivo
                    </button>
                </div>
                <div>
                    <span class="hint--top d-none" aria-label="Filtrar Legajos Inactivos">
                        <div class="custom-control custom-switch float-right pt-1">
                            <input type="checkbox" class="custom-control-input" name="_eg" id="_eg">
                            <label class="custom-control-label" for="_eg" style="padding-top: 3px;">De Baja</label>
                        </div>
                    </span>

                    <span class="hint--top d-none d-sm-block" aria-label="Ordenar por Nombre">
                        <div class="custom-control custom-switch float-right pt-1 mr-2">
                            <input type="checkbox" class="custom-control-input" checked name="_porApNo" id="_porApNo">
                            <label class="custom-control-label" for="_porApNo" style="padding-top: 3px;">Por
                                Nombre</label>
                        </div>
                    </span>
                </div>
            </div>
            <div class="col-12 d-flex px-2">
                <div class="collapse" id="collapseMasivos">
                    <div class="pt-2"></div>
                    <div class="d-flex p-1 border radius" style="gap: 5px;">
                        <button class="btn btn-sm border-0 btn-outline-custom m_horale1 custom-white">
                        </button>
                        <button class="btn btn-sm border-0 btn-outline-custom m_rota custom-white">
                        </button>
                        <button class="btn btn-sm border-0 btn-outline-custom m_cita custom-white">
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row bg-white invisible m-0 m-sm-2" id="PersonalTable">
            <div class="col-12 col-sm-5">
                <table class="table table-hover text-nowrap w-100" id="tablePersonal"></table>
            </div>
            <div class="col-12 col-sm-7 w-100 mt-sm-0 mt-2">
                <div class="h-100 border radius p-2 bg-ddd">
                    <div id="divData">

                    </div>
                    <div class="fadeIn" id="detalleHorario" hidden>

                        <div class="divTablas" style="display: none;">

                            <div class="dataLegajo p-1 fw5 text-secondary">

                            </div>

                            <div class="mb-2 d-flex p-1 border-0 w-100 radius justify-content-between fadeIn"
                                style="gap: 5px;">
                                <div>
                                    <button class="btn btn-sm border btn-outline-custom l_horale1 custom-white"
                                        aria-label="Añadir Horario">
                                    </button>
                                    <button class="btn btn-sm border btn-outline-custom l_rota custom-white"
                                        aria-label="Añadir Rotación">
                                    </button>
                                    <button class="btn btn-sm border btn-outline-custom l_cita custom-white"
                                        aria-label="Añadir Citación">
                                    </button>
                                </div>
                                <div class="btn-group-toggle border radius bg-white p-1 d-none d-sm-block"
                                    data-toggle="buttons">
                                    <label
                                        class="btn btn-sm btn-outline-secondary border-0 radius active font07 verHorarios"
                                        aria-label="Ocultar Horarios">
                                        <input type="checkbox" checked> Horarios
                                    </label>
                                    <label class="btn btn-sm btn-outline-secondary border-0 radius font07 verRotaciones"
                                        aria-label="Ocultar Rotaciones">
                                        <input type="checkbox" checked> Rotaciones
                                    </label>
                                    <label class="btn btn-sm btn-outline-secondary border-0 radius font07 verCitaciones"
                                        aria-label="Ocultar Citaciones">
                                        <input type="checkbox" checked> Citaciones
                                    </label>
                                </div>
                            </div>

                            <div class="radius mb-2 bg-white p-2 border" id="divHorariosDesde">
                                <div class="d-inline-flex justify-content-between w-100"
                                    style="border-bottom: 1px solid #ddd;">
                                    <div class="px-2 font08 py-1 d-inline-flex w-100 justify-content-between align-items-center"
                                        id="titleDesde">
                                        Horarios Desde
                                    </div>
                                    <div class="position-relative p-1">
                                        <input type="text" class="d-none searchDesde h25">
                                    </div>
                                </div>
                                <div class="overflow-auto w-100" style="max-height:250px">
                                    <table class="table w-100 text-nowrap" id="Horale1"></table>
                                </div>
                            </div>
                            <div class="radius mb-2 bg-white p-2 border" id="divHorariosDesdeHasta">
                                <div class="d-inline-flex justify-content-between w-100"
                                    style="border-bottom: 1px solid #ddd;">
                                    <div class="px-2 font08 py-1 d-inline-flex w-100 justify-content-between align-items-center"
                                        id="titleDesdeHasta">Horarios Desde Hasta</div>
                                    <div class="position-relative p-1">
                                        <input type="text" class="d-none searchDesdeHasta h25">
                                    </div>
                                </div>
                                <div class="overflow-auto w-100" style="max-height:250px">
                                    <table class="table text-nowrap w-100" id="Horale2"></table>
                                </div>
                            </div>
                            <div class="radius mb-2 bg-white p-2 border" id="divRotaciones">
                                <div class="d-inline-flex justify-content-between w-100"
                                    style="border-bottom: 1px solid #ddd;">
                                    <div class="px-2 font08 py-1 d-inline-flex w-100 justify-content-between align-items-center"
                                        id="titleRotaciones">Rotaciones</div>
                                    <div class="position-relative p-1">
                                        <input type="text" class="d-none searchRotaciones h25">
                                    </div>
                                </div>
                                <div class="overflow-auto w-100" style="max-height:250px">
                                    <table class="table text-nowrap w-100" id="table-rota"></table>
                                </div>
                            </div>
                            <div class="toast RotaDeta border-0" role="alert" aria-live="polite" data-autohide="false"
                                aria-atomic="true">
                            </div>
                            <div class="radius mb-2 bg-white p-2 border" id="divCitaciones">
                                <div class="d-inline-flex justify-content-between w-100"
                                    style="border-bottom: 1px solid #ddd;">
                                    <div class="px-2 font08 py-1 d-inline-flex w-100 justify-content-between align-items-center"
                                        id="titleCitaciones">Citaciones</div>
                                    <div class="position-relative p-1">
                                        <input type="text" class="d-none searchCitaciones h25">
                                    </div>
                                </div>
                                <div class="overflow-auto w-100" style="max-height:250px">
                                    <table class="table text-nowrap w-100" id="table-citacion"></table>
                                </div>
                            </div>
                        </div>
                        <span id="divSelectLegajo">
                            <div class="text-center w-100 mt-5 d-flex align-items-center justify-content-center w-100"
                                style="height: 200px;">
                                <p class="p-2 px-3 bg-light radius">Seleccionar un Legajo</p>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="p-2" id="divGrillaHorario"></div>
            </div>
            <div class="border p-1 w-100 rounded mb-3 shadow-sm bg-ddd" id="divGrillaHorario2" style="display:none;">
                <div class="col-12 mb-3 d-flex justify-content-between w-100 py-3">
                    <div class="w-100 d-flex flex-column align-items-start justify-content-center">
                        <span>Horarios asignados</span>
                        <span class="font08" id="nameLegajo"></span>
                    </div>
                    <div class="input-group d-inline-flex justify-content-end align-items-center">
                        <div class="hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow"
                            aria-label="Seleccionar fechas">
                            <div class="d-inline-flex radius border w-100">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-0 bg-white" id="Refresh">
                                        <svg class="bi mr-1" width="18" height="18" fill="currentColor">
                                            <use xlink:href="../../img/bootstrap-icons.svg#calendar-range"></use>
                                        </svg>
                                    </span>
                                </div>
                                <div>
                                    <input id="_dr" name="_dr" type="text"
                                        class="form-control text-center border-0 h40 w250" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <table id="tabla" class="table text-nowrap fadeInDown w-100 bg-white p-2 rounded border"
                        style="display:none;">
                    </table>
                </div>
            </div>
        </div>
        <div id="divModal"></div>
        <div id="modal_horale1"></div>
        <div id="divactModal"></div>
        <div id="divactModalCit"></div>

    </div>
    </div>
    <!-- Fin Encabezado -->
    </div>
    <!-- fin container -->
    <?php

    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLES */
    require __DIR__ . "/../../js/DataTable.php";
    ?>
    <script src="../../js/bootbox.min.js"></script>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="js/data.js?<?= version_file("/op/horarios/js/data.js") ?>"></script>
</body>

</html>