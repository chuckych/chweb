<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['infornovc'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'informes.png', MODULOS['infornovc'], '') ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <?php
        $FechaMinMax = (fecha_min_max2('FICHAS3', 'FICHAS3.FicFech'));
        $FirstDate = $FechaMinMax['min'];
        /** FirstDate */
        $FirstYear = Fech_Format_Var($FechaMinMax['min'], 'Y');
        /** FirstYear */
        $maxDate   = $FechaMinMax['max'];
        // $maxDate   = date('Y-m-d');
        /** maxDate */
        $maxYear   = date('Y');
        /** maxYear */
        $FechaIni = $FechaMinMax['max'];
        $FechaFin = $FechaMinMax['max'];
        // $FechaIni = date('Y-m-d');
        // $FechaFin = date('Y-m-d');
        ?>
        <div class="row bg-white py-2">
            <div class="col-12 col-sm-6 mt-2">
                <div class="custom-control custom-switch custom-control-inline">
                    <input type="radio" class="custom-control-input" id="PorLegajo" name="ordenar" value="0">
                    <label class="custom-control-label" for="PorLegajo" style="padding-top: 3px;"><span class="text-dark">Por Legajo</span></label>
                </div>
                <div class="custom-control custom-switch custom-control-inline ml-1">
                    <input type="radio" class="custom-control-input" id="PorNombre" name="ordenar" value="1">
                    <label class="custom-control-label" for="PorNombre" style="padding-top: 3px;"><span class="text-dark">Por Nombre</span></label>
                </div>
                <input type="hidden" id="ordenar">
                
                <!-- <button type="button" class="ml-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel">.xls <img src="../img/xls.png" class="w15" alt="Exportar Excel"></button> -->
            </div>
            <div class="col-12 col-sm-6">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <button class="btn btn-outline-custom fontq border mr-1 btn-sm" type="button" data-toggle="modal" data-target="#ConceptosModal">Conceptos</button>

                    <button type="button" class="btn btn-outline-custom border btn-sm fontq" data-toggle="collapse" data-target="#rowFiltros" aria-expanded="false" aria-controls="rowFiltros">
                        Filtros
                    </button>
                    <span id="trash_allIn" title="Limpiar Filtros" class="trash align-middle mt-2 fw5 ml-1"></span>
                    <label for="_drnovc" class="d-none">Fecha</label>
                    <input type="text" readonly class="ml-2 form-control text-center w250 ls2" name="_drnovc" id="_drnovc">
                </div>
            </div>
        </div>
        <div class="row bg-white collapse invisible" id="rowFiltros">
            <div class="col-12">
                <!-- Tipo -->
                <label for="Tipo" class="mb-1 fontq"><span class="mr-1 d-none d-sm-none d-md-none d-lg-block mb-1 fontq">Tipo de Personal: </span></label>
                <select class="selectjs_tipoper" id="Tipo" name="Tipo">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Empresa -->
                <label for="Emp" class="mb-1 fontq">Empresas</label>
                <select class="form-control selectjs_empresa" id="Emp" name="Emp">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Planta -->
                <label for="Plan" class="mb-1 w100 fontq">Plantas </label>
                <select class="form-control selectjs_plantas" id="Plan" name="Plan">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Sector -->
                <label for="Sect" class="mb-1 w100 fontq">Sectores</label>
                <select class="form-control selectjs_sectores" id="Sect" name="Sect">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Seccion -->
                <label for="Sec2" class="mb-1 w100 fontq">Secciónes</label>
                <select disabled class="form-control select_seccion" id="Sec2" name="Sec2">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Grupos -->
                <label for="Grup" class="mb-1 w100 fontq">Grupos</label>
                <select class="form-control selectjs_grupos" id="Grup" name="Grup">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Sucursal -->
                <label for="Sucur" class="mb-1 w100 fontq">Sucursales</label>
                <select class="form-control selectjs_sucursal" id="Sucur" name="Sucur">
                </select>
            </div>
            <div class="col-12 ">
                <!-- Legajo -->
                <label for="Per" class="mb-1 w100 fontq">Legajos</label>
                <div class="d-flex align-items-center">
                    <select class="form-control selectjs_personal" id="Per" name="Per">
                    </select>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 table-responsive">
                <table id="GetPresentismo" class="table w-100 text-nowrap">
                    <thead>
                        <th>Legajo</th>
                        <th style="min-width: 230px !important;">Apellido y Nombre</th>
                        <th>Fecha Desde</th>
                        <th>Fecha Hasta</th>
                        <th>Total Días<br>Presentes</th>
                        <th>Total Días<br>Ausentes</th>
                        <th>Total<br>Días</th>
                        <th>Meses<br>Presentes</th>
                        <th>Meses<br>Ausentes</th>
                        <th>Total<br>Meses</th>
                        <!-- <th>Total Meses<br>Fecha</th> -->
                        <th></th>
                    </thead>
                </table>
            </div>
        </div>
        <?php
        require __DIR__ . "./modalConceptos.php";
        ?>
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
    <!-- <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script> -->
    <!-- <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script> -->
    <script src="../../js/select2.min.js"></script>
    <script src="js/data.js?v=<?= vjs() ?>"></script>
    <script src="js/dataConceptos.js?v=<?= vjs() ?>"></script>
    <script src="js/select.js?v=<?= vjs() ?>"></script>
    <script src="js/FicExcel.js?v=<?= vjs() ?>"></script>
</body>

</html>