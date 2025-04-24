<!doctype html>
<html lang="es">

<head>
    <link href="estilos.css" rel="stylesheet" type="text/css" />
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../../llamadas.php"; ?>
    <title><?= MODULOS['prysmian'] ?></title>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container">
        <?php require __DIR__ . '/../../../nav.php'; ?>
        <!-- Encabezado -->
        <?=
            encabezado_mod2('bg-custom', 'white', 'folder-symlink-fill', MODULOS['prysmian'], '25', 'text-white mr-2');
        ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white p-2 radius wrapper" hidden>
            <div class="col-12 col-sm-8">
                <div class="form-row">
                    <!-- Tipo personal -->
                    <div class="d-none">
                        <div class="col-12 m-0 mt-2 d-flex flex-column">
                            <label for="Tipo" class="mr-2 w120">Tipo de Personal</label>
                            <select class="selectjs_tipo form-control w150" id="Tipo" name="Tipo">
                                <option value="0">Mensuales</option>
                                <option value="1">Jornales</option>
                            </select>
                        </div>
                        <!-- Año -->
                        <div class="col-12 col-sm-4 mt-2">
                            <label for="Anio">Año</label>
                            <select class="form-control selectjs_year w-100" id="Anio">
                            </select>
                            <span id="trash_anio" class="trash"></span>
                        </div>
                        <!-- Mes -->
                        <div class="col-12 col-sm-4 mt-2">
                            <label for="Mes">Mes:</label>
                            <select class="ml-3 form-control selectjs_month w-100" id="Mes">
                            </select>
                        </div>
                        <!-- Jornal -->
                        <div class="col-12 col-sm-4 mt-2 div_jornal loader-in">
                            <div class="fadeIn">
                                <label for="Quincena" class="w80">Jornal</label>
                                <select class="selectjs_jornal form-control" id="Quincena">
                                    <option value="1">Primer</option>
                                    <option value="2">Segundo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Tipo -->
                    <div class="col-12 col-sm-6 mt-2 div_tipo">
                    </div>
                    <!-- Jornal -->
                    <div class="col-12 col-sm-6 mt-2 div_jornales">
                    </div>
                    <!-- Años -->
                    <div class="col-12 mt-2 div_años">
                    </div>
                    <!-- Meses -->
                    <div class="col-12 mt-2 div_meses">
                    </div>
                    <!-- Fecha desde / hasta -->
                    <div class="col-12 mt-2">
                        <label for="date-picker" class="">Fecha desde / hasta:</label>
                        <div class="d-flex flex-column align-items-end">
                            <input id="date-picker" type="text"
                                class="date-picker form-control w-100 h40 ls1 text-center div_fechas">
                            <span class="count_days font08 mt-2"></span>
                        </div>
                    </div>
                    <!-- Tipo Reporte -->
                    <div class="col-12 m-0 mt-2 d-flex justify-content-between align-items-end">
                        <!-- Tipo Reporte -->
                        <div class="d-flex flex-column">
                            <label for="reporte" class="mr-2 w120">Reporte</label>
                            <div class="d-inline-flex align-items-end" style="gap:10px;">
                                <select class="selectjs_reporte form-control w200" id="reporte">
                                    <option value="1">Inasistencias</option>
                                    <option value="2">Reporte de Actividad</option>
                                    <!-- <option disabled value="3">Conceptos a Liquidar - Jornales</option> -->
                                    <!-- <option disabled value="4">Conceptos a Liquidar - Mensuales</option> -->
                                </select>
                                <button hidden
                                    class="btn btn-sm btn-outline-secondary p-2 border px-3 hint--top hint--rounded hint--default hint--no-shadow"
                                    aria-label="Configurar conceptos" id="config-actividad" style="width:60px;">
                                    <i class="bi bi-gear"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Button Generar -->
                        <div class="d-inline-flex" style="gap:5px;">
                            <button type="button" class="btn bg-outline-secondary border btn-sm font08"
                                id="view">Mostrar</button>
                            <button type="button" class="btn bg-custom btn-sm text-white font08"
                                id="xls">Exportar</button>
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

            <div class="col-12 col-sm-4 mt-4 user-select-none">
                <label class="w-100">Referencias</label>
                <table class="table w-100">
                    <tbody>
                        <tr>
                            <td class="fw4">Mensuales</td>
                            <td class="text-center ls1"><span class="MensDesde"></span></td>
                            <td class="text-center">al</td>
                            <td class="text-center ls1"><span class="MensHasta"></span></td>
                            <td class="w-100"></td>
                        </tr>
                        <tr>
                            <td class="fw4">1° Jornal</td>
                            <td class="text-center ls1"><span class="Jor1Desde"></span></td>
                            <td class="text-center">al</td>
                            <td class="text-center ls1"><span class="Jor1Hasta"></span></td>
                            <td class="w-100"></td>
                        </tr>
                        <tr>
                            <td class="fw4">2° Jornal</td>
                            <td class="text-center ls1"><span class="Jor2Desde"></span></td>
                            <td class="text-center">al</td>
                            <td class="text-center ls1"><span class="Jor2Hasta"></span></td>
                            <td class="w-100"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-row bg-white p-2">
            <div class="col-12 col-sm-6 col-md-7" id="div_table_tipo_hora" style="min-height:500px;" hidden>
            </div>
            <div class="col-12 col-sm-6" id="div_table_novedades" style="min-height:500px;" hidden>
            </div>
        </div>
        <div class="row bg-white p-2">
            <div class="col-12" id="div_table" hidden>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fadeIn" id="configModal" tabindex="-1" aria-labelledby="configModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h6 class="modal-title" id="configModalLabel">Modal title</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="bi bi-x-lg"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . ".../../../../js/jquery.php";
    require __DIR__ . ".../../../../js/DateRanger.php";
    require __DIR__ . ".../../../../js/DataTable.php";
    ?>
    <script src="../../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../../js/moment.min.js"></script>
    <script src="../../../js/select2.min.js"></script>
    <script src="../../../js/Sortable.min.js"></script>

    <script src="js/main.js?<?= version_file("/informes/custom/prysmian/js/main.js") ?>"></script>
</body>

</html>