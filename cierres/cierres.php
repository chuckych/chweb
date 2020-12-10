<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title><?= MODULOS['cierres'] ?></title>
    <style>


    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-custom', 'white', 'task.png', MODULOS['cierres'], '') ?>
        <!-- Fin Encabezado -->
        <form action="insert.php" method="post" class="alta_cierre">
            <div class="row bg-white p-3 radius">
                <div class="col-12 col-sm-6">
                    <div class="row">
                        <div class="col-12 m-0">
                            <div class="form-inline mt-2">
                                <!-- Tipo personal -->
                                <input type="hidden" name="alta_cierre" id="alta_cierre">
                                <label for="Tipo" class="mr-2 w120">Tipo de Personal</label>
                                <select class="select2 form-control w150" id="Tipo" name="Tipo">
                                    <?php
                                    foreach (TIPO_PER as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                                <button type="button" class="fontq btn btn-link text-decoration-none text-secondary" id="trash_all">Limpiar Filtro</button>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="form-inline">
                                <!-- Empresa -->
                                <label for="Emp" class="mr-2 w120">Empresa</label>
                                <select class="form-control selectjs_empresa w300" id="Emp" name="Emp">
                                </select>
                                <span id="trash_emp" class="trash"></span>
                            </div>
                            <div class=" form-inline mt-2">
                                <!-- Planta -->
                                <label for="Plan" class="mr-2 w120">Planta</label>
                                <select class="form-control selectjs_plantas w300" id="Plan" name="Plan">
                                </select>
                                <span id="trash_plan" class="trash"></span>
                            </div>
                            <div class="form-inline mt-2">
                                <!-- Sector -->
                                <label for="Sect" class="mr-2 w120">Sector</label>
                                <select class="form-control selectjs_sectores w300" id="Sect" name="Sect">
                                </select>
                                <span id="trash_sect" class="trash"></span>
                            </div>
                            <div class="form-inline mt-2" id="select_seccion">
                                <!-- Seccion -->
                                <label for="Sec2" class="mr-2 w120">Sección</label>
                                <select disabled class="form-control select_seccion w300" id="Sec2" name="Sec2">
                                </select>
                                <span id="trash_secc" class="trash"></span>
                            </div>
                            <div class="form-inline mt-2">
                                <!-- Grupos -->
                                <label for="Grup" class="mr-2 w120">Grupos</label>
                                <select class="form-control selectjs_grupos w300" id="Grup" name="Grup">
                                </select>
                                <span id="trash_grup" class="trash"></span>
                            </div>
                            <div class="form-inline mt-2">
                                <!-- Sucursal -->
                                <label for="Sucur" class="mr-2 w120">Sucursal</label>
                                <select class="form-control selectjs_sucursal w300" id="Sucur" name="Sucur">
                                </select>
                                <span id="trash_sucur" class="trash"></span>
                            </div>
                            <div class="form-inline mt-2">
                                <!-- Legajo -->
                                <label for="Per" class="mr-2 w120">Personal</label>
                                <select class="form-control selectjs_personal w300" id="Per" name="Per">
                                </select>
                                <span id="trash_per" class="trash"></span>
                            </div>
                            <div class="form-inline mt-3">
                                <!-- Fecha de cierre -->
                                <label for="Sucur" class="mr-2 w120">Fecha de cierre</label>
                                <input type="date" name="cierre" id="cierre" class="form-control w150" value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="form-inline mt-3">
                                <!-- Eliminar Cierres -->
                                <div class="custom-control custom-switch" data-toggle="tooltip" id="switch" data-placement="right" data-html="true" title="<span class''>AL ACTIVAR ESTA OPCION, SE QUITARAN LOS CIERRES DE LOS LEGAJOS SELECCIONADOS</span>">
                                    <input type="checkbox" class="custom-control-input" id="EliminaCierre" name="Quita">
                                    <label class="custom-control-label" style="padding-top: 2px;" for="EliminaCierre">Quitar Cierres</label>
                                </div>
                            </div>

                        </div>
                        <input type="hidden" name="SelEmpresa" id="SelEmpresa">
                        <input type="hidden" name="SelPlanta" id="SelPlanta">
                        <input type="hidden" name="SelSector" id="SelSector">
                        <input type="hidden" name="SelSeccion" id="SelSeccion">
                        <input type="hidden" name="SelGrupo" id="SelGrupo">
                        <input type="hidden" name="SelSucursal" id="SelSucursal">
                    </div>
                </div>
                <div class="col-12 col-sm-6 table-responsive pt-2">
                    <table class="table table-hover text-nowrap w-100 table-sm table-borderless " id="GetPersonal">
                        <thead class="">
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" name="select_all" value="1" id="Personal-select-all" type="checkbox">
                                        <label class="custom-control-label" for="Personal-select-all"></label>
                                    </div>
                                </th>
                                <th>LEGAJO</th>
                                <th>NOMBRE</th>
                                <th>CIERRE</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-12 mt-2 d-flex justify-content-end">
                    <div class="form-inline mt-2">
                        <button type="submit" class="btn bg-custom btn-sm text-white fontq w150" id="submit"></button>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <div id="respuesta" class="alert d-none fonth text-wrap">
                        <div id="respuetatext" class="text-wrap"></div>

                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "../../js/jquery.php";
    require __DIR__ . "../../js/DataTable.php";
    ?>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="js/procesar.js"></script>
    <script src="js/select.js"></script>
    <script src="js/trash-select.js"></script>

</body>

</html>