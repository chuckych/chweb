<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['horasign'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <?= encabezado_mod('bg-custom', 'white', 'horario.png', MODULOS['horasign'], ''); ?>
        <div class="row bg-white radius pt-3 mb-0 pb-0">
            <div class="col-12 col-sm-6">
                <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal" data-target="#Filtros">
                    Filtros
                </button>
                <span id="trash_all" title="Limpiar Filtros" class="invisible trash align-middle pb-0"></span>
            </div>
            <div class="col-12 col-sm-6">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <input type="text" readonly class="mx-2 form-control text-center w250 ls2" name="_dr" id="_dr">
                    <button type="button" id="Refresh" disabled class="btn px-2 border-0 fontq float-right bg-custom text-white opa8">
                        <svg width="1.3em" height="1.3em" viewBox="0 0 16 16" class="bi bi-arrow-repeat" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2.854 7.146a.5.5 0 0 0-.708 0l-2 2a.5.5 0 1 0 .708.708L2.5 8.207l1.646 1.647a.5.5 0 0 0 .708-.708l-2-2zm13-1a.5.5 0 0 0-.708 0L13.5 7.793l-1.646-1.647a.5.5 0 0 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0 0-.708z" />
                            <path fill-rule="evenodd" d="M8 3a4.995 4.995 0 0 0-4.192 2.273.5.5 0 0 1-.837-.546A6 6 0 0 1 14 8a.5.5 0 0 1-1.001 0 5 5 0 0 0-5-5zM2.5 7.5A.5.5 0 0 1 3 8a5 5 0 0 0 9.192 2.727.5.5 0 1 1 .837.546A6 6 0 0 1 2 8a.5.5 0 0 1 .501-.5z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="row bg-white pb-sm-3" id="pagLega">
            <div class="col-12 d-flex justify-content-sm-end align-items-center animate__animated animate__fadeIn">
                <input type="text" data-mask="000000000" reverse="true" id="Per2" class="form-control mr-2 w100 mt-n2 text-center" style="height: 15px;">
                <table class="table table-borderless text-nowrap w-auto table-sm" id="GetPersonal">

                </table>
            </div>
        </div>
            <!-- </form> -->
            <div class="row bg-white pt-2">
                <div class="col-12 animate__animated animate__fadeIn pb-3 table-responsive">
                    <table class="table table-hover text-wrap w-100" id="GetHorarios">
                    <thead>
                    <tr>
                        <th>LEG.</th>
                        <th>NOMBRE</th>
                        <th>FECHA</th>
                        <th>DIA</th>
                        <th>HORARIO</th>
                        <th>COD</th>
                        <th>DESCRIPCIÓN</th>
                    </tr>
                    </thead>
                    </table>
                </div>
            </div>
    </div>
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../../js/DataTable.php";
    require 'modal_Filtros.html';
    ?>
    <script src="../../js/select2.min.js"></script>
    <script src="js/data.js"></script>
    <script src="js/select.js"></script>
    <script src="js/trash-select.js"></script>
</body>

</html>