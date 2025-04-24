<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title>Configuraci&oacute;n » M&oacute;dulos</title>
    <style>
        .dtrg-level-1 {
            font-size: .8rem !important;
        }

        table.dataTable tr.dtrg-group.dtrg-level-0 td {
            font-weight: normal !important;
            font-size: .8rem !important;
            background-color: #fafafa !important;
            border: 0px solid #efefef !important;
            color: #333 !important;
        }

        table.dataTable td {
            vertical-align: middle;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <?=
            encabezado_mod2('bg-custom', 'white', 'layout-text-window', 'Configuraci&oacute;n Módulos', '25', 'text-white mr-2');
        ?>
        <div class="row">
            <div class="col-12 mt-2 d-flex justify-content-between">
                <button class="btn btn-sm btn-outline-custom fontp border" id="AddMod" title="Agregar M&oacute;dulo">
                    <span class="d-none d-sm-inline">Agregar M&oacute;dulo</span>
                    <span class="d-inline d-sm-none">M&oacute;dulo</span>
                    <svg class="bi" width="15" height="15" fill="currentColor">
                        <use xlink:href="../../img/bootstrap-icons.svg#plus" />
                    </svg>
                </button>
                <button class="btn btn-sm btn-outline-custom fontp border" id="Refresh" title="Actualizar Grilla">
                    <span class="d-none d-sm-inline">Actualizar</span>
                    <span class="d-inline d-sm-none">
                        <svg class="bi" width="15" height="15" fill="currentColor">
                            <use xlink:href="../../img/bootstrap-icons.svg#arrow-repeat" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <div class="row" id="rowTable">
            <div class="col-12 col-lg-6 table-responsive" id="divModulos" style="display: none;">
                <table class="table w-100 w-sm-auto" id="modulos">
                    <thead>
                        <th></th>
                        <th>MODULO</th>
                        <th>TIPO</th>
                        <th class="text-center">#</th>
                        <th>ESTADO</th>
                        <th></th>
                    </thead>
                </table>
            </div>
            <span class="fontq col-12" id="aguarde">Aguarde . . .</span>
        </div>
        <div class="row" id="RowAddMod" style="display: none;">
            <form action="crud.php" class="w-100" method="POST" id="formAddMod">
                <div class="col-sm-6 col-lg-3">
                    <span class="fontp p-1 fw4" id="tituloForm">

                    </span>
                    <div class="form-inline mt-2">
                        <label for="NombreMod" class="w80"><span class="mr-2">Descripci&oacute;n</span></label>
                        <input type="text" class="form-control w-100 h40" id="NombreMod" name="NombreMod">
                        <input type="hidden" hidden class="w30" id="IdMod" name="IdMod">
                        <input type="hidden" hidden class="" id="accion" name="accion" value="">
                    </div>
                    <div class="form-inline mt-1">
                        <label for="TipoMod" class="w80"><span class="mr-2">Tipo</span></label>
                        <select name="TipoMod" id="TipoMod" class="form-control custom-select w-100 h40">
                            <!-- <option value="" selected></option> -->
                        </select>
                    </div>
                    <div class="form-inline mt-1">
                        <label for="EstadoMod" class="w80"><span class="mr-2">Estado</span></label>
                        <select name="EstadoMod" id="EstadoMod" class="form-control custom-select w-100 h40">
                            <option value="0" selected>Activo</option>
                            <option value="1">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-inline mt-1">
                        <label for="OrdenMod" class="w80"><span class="mr-2">Orden</span></label>
                        <input type="number" class="form-control w-100 h40" id="OrdenMod" name="OrdenMod">
                    </div>
                    <div class="form-inline mt-3">
                        <button type="submit" class="btn btn-custom fontq btn-block border-0 px-3"
                            id="Aceptar"></button>
                    </div>
                    <div class="form-inline mt-2 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-custom fontq btn-mobile border-0"
                            id="CancelMod">Cancelar</button>
                    </div>
                </div>
                <div class="col-12 mt-2 fontq fw5" id="respuesta" style="display:none;">

                </div>
            </form>
        </div>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "/../../js/jquery.php";
    require __DIR__ . "/../../js/DataTable.php";
    ?>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../js/datatable/dataTables.rowGroup.min.js"></script>
    <script src="js/datamods.js?v=<?= vjs() ?>"></script>
</body>

</html>