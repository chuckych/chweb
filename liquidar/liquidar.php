<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title><?= MODULOS['liquidar'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-custom', 'white', 'descargar.png', MODULOS['liquidar'], '') ?>
        <!-- Fin Encabezado -->
        <form action="insert.php" method="post" class="alta_liquidacion">
            <div class="row bg-white p-3 radius">
                <div class="col-12 col-sm-6 shadow-sm">
                    <div class="row">
                        <div class="col-12 m-0">
                            <div class="mt-2">
                                <!-- Tipo personal -->
                                <input type="hidden" name="alta_liquidacion" id="alta_liquidacion">
                                <label for="Tipo" class="mr-2 w120">Tipo de Personal</label>
                                <select class="select2 form-control w150" id="Tipo" name="Tipo">
                                    <?php
                                    foreach (TIPO_PER as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                                <button type="button" class="fontq btn btn-outline-custom border-0 float-right" id="trash_all"><i class="bi bi-eraser"></i> Limpiar Filtros</button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div class="mt-2">
                                <label for="LegaIni" class="w120">Legajo desde:</label>
                                <input type="tel" name="LegaIni" id="LegaIni" class="form-control w-100 text-center h40" value="1" max="999999999">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <div class="mt-2">
                                <label for="LegaFin" class="mx-1">Legajo hasta:</label>
                                <input type="tel" name="LegaFin" id="LegaFin" class="form-control w-100 text-center h40" value="999999999" max="999999999">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <label for="Anio">Año</label>
                            <select class="form-control selectjs_anio w-100" id="Anio">
                                <option selected value="<?= date('Y') ?>"><?= date('Y') ?></option>
                            </select>
                            <span id="trash_anio" class="trash"></span>
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <label for="Mes">Mes:</label>
                            <select class="ml-3 form-control selectjs_mes w-100" id="Mes">
                            </select>
                        </div>
                        <div class="col-12 mt-3" id="divJornal" style="display: none;">
                            <div class="animate__animated animate__fadeIn">
                                <label for="Quincena" class="w80">Jornal</label>
                                <select class="select2_quincena form-control w120" id="Quincena">
                                    <?php
                                    foreach (QUINCENA as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <label for="ProcLegaIni" class="">Fecha desde / hasta:</label>
                            <input type="text" class="form-control w-100 h40 ls1 text-center" name="_drLiq">
                        </div>
                        <div class="col-12 col-sm-6 mt-2">
                            <!-- Empresa -->
                            <label for="Emp" class="mr-2 w-100">Empresa</label>
                            <select class="form-control selectjs_empresa w-100" id="Emp" name="Emp">
                            </select>
                            <span id="trash_emp" class="trash"></span>
                        </div>
                        <div class="col-12 col-sm-6 mt-1">
                            <!-- Planta -->
                            <label for="Plan" class="mr-2 w-100">Planta</label>
                            <select class="form-control selectjs_plantas w-100" id="Plan" name="Plan">
                            </select>
                            <span id="trash_plan" class="trash"></span>
                        </div>
                        <div class="col-12 col-sm-6 mt-1">
                            <div class="">
                                <!-- Sector -->
                                <label for="Sect" class="mr-2 w-100">Sector</label>
                                <select class="form-control selectjs_sectores w-100" id="Sect" name="Sect">
                                </select>
                                <span id="trash_sect" class="trash"></span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-1">
                            <div class="" id="select_seccion">
                                <!-- Seccion -->
                                <label for="Sec2" class="mr-2 w-100">Sección</label>
                                <select disabled class="form-control select_seccion w-100" id="Sec2" name="Sec2">
                                </select>
                                <span id="trash_secc" class="trash"></span>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-1">
                            <!-- Grupos -->
                            <label for="Grup" class="mr-2 w120">Grupos</label>
                            <select class="form-control selectjs_grupos w-100" id="Grup" name="Grup">
                            </select>
                            <span id="trash_grup" class="trash"></span>
                        </div>
                        <div class="col-12 col-sm-6 mt-1">
                            <!-- Sucursal -->
                            <label for="Sucur" class="mr-2">Sucursal</label>
                            <select class="form-control selectjs_sucursal w-100" id="Sucur" name="Sucur">
                            </select>
                            <span id="trash_sucur" class="trash"></span>
                        </div>


                        <div class="col-12 mt-3">
                            <div class="form-inline mt-3 d-flex justify-content-end pb-3">
                                <button type="submit" class="btn bg-custom btn-sm text-white fontq w150" id="submit"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="SelEmpresa" id="SelEmpresa">
                <input type="hidden" name="SelPlanta" id="SelPlanta">
                <input type="hidden" name="SelSector" id="SelSector">
                <input type="hidden" name="SelSeccion" id="SelSeccion">
                <input type="hidden" name="SelGrupo" id="SelGrupo">
                <input type="hidden" name="SelSucursal" id="SelSucursal">
                <input type="hidden" id="MensDesde">
                <input type="hidden" id="MensHasta">
                <input type="hidden" id="Jor1Desde">
                <input type="hidden" id="Jor1Hasta">
                <input type="hidden" id="Jor2Desde">
                <input type="hidden" id="Jor2Hasta">
                <input type="hidden" id="ArchDesc">
                <input type="hidden" id="ArchNomb">
                <input type="hidden" id="ArchPath">
                <input type="hidden" id="TipoPer">
                <input type="hidden" id="TipoJornal" value="1">
                <input type="hidden" id="date" value="<?= date("Y-m-d") ?>">

                <div class="col-12 col-sm-6 mt-2">
                    <table class="table table-sm">
                        <thead class="">
                            <tr>
                                <th class="border-bottom-0"></th>
                                <th class="text-center border-bottom-0">Desde</th>
                                <th class="text-center border-bottom-0">Hasta</th>
                                <th class="w-100 text-center border-bottom-0"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw4">Mensuales</td>
                                <td class="text-center ls1"><span class="MensDesde"></span></td>
                                <td class="text-center ls1"><span class="MensHasta"></span></td>
                                <td class="w-100"></td>
                            </tr>
                            <tr>
                                <td class="fw4">1° Jornal</td>
                                <td class="text-center ls1"><span class="Jor1Desde"></span></td>
                                <td class="text-center ls1"><span class="Jor1Hasta"></span></td>
                                <td class="w-100"></td>
                            </tr>
                            <tr>
                                <td class="fw4">2° Jornal</td>
                                <td class="text-center ls1"><span class="Jor2Desde"></span></td>
                                <td class="text-center ls1"><span class="Jor2Hasta"></span></td>
                                <td class="w-100"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-sm">
                        <tbody>
                            <td class="fw4">Descripción</td>
                            <td class=""><span class="ArchDesc"></span></td>
                            <td class="w-100"></td>
                            </tr>
                            <tr>
                                <td class="fw4">Nombre</td>
                                <td class=""><span class="ArchNomb"></span></td>
                                <td class="w-100"></td>

                            </tr>
                            <tr id="trDownload" style="display: none">
                                <td class="fw5"><span id="tdDescargar">Descargar</span></td>
                                <td class=""><span class="ArchPath"></span></td>
                                <td class=""></td>
                                <td class=""></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="archivo overflow-auto p-3 d-none" style="max-height: 360px;">
                    </div>
                </div>
            </div>
        </form>
    </div>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "../../js/jquery.php";
    require __DIR__ . "../../js/DateRanger.php";
    require __DIR__ . "../../js/DataTable.php";
    ?>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../js/moment.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="js/procesar.js?v=<?= vjs() ?>"></script>
    <script src="js/select.js?v=<?= vjs() ?>"></script>
    <script src="js/trash-select.js?v=<?= vjs() ?>"></script>

</body>

</html>