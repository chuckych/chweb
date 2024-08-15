<?php require __DIR__ . '/data.php'; ?>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 20px;
        }

        .select2-container .select2-selection--single {
            height: 32px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 3px;
        }

        .form-inline label {
            justify-content: left;
        }
    </style>
    <title><?= MODULOS['personal'] ?> » Legajo</title>
</head>

<body class="animate__animated  animate__fadeIn" <?= $CalcEdad ?>>
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-fich', 'white', 'usuarios.png', 'Legajo: ' . $_GET['_leg'] . ' &#8250; ' . $pers['LegApNo'], '') ?>
        <!-- Fin Encabezado -->
        <form action="update.php" method="POST" id="Update_Leg" class="Update_Leg">
            <input type="hidden" name="LegNume" value="<?= $_GET['_leg'] ?>" id="LegNume">
            <input type="hidden" name="Update_Leg" value="true">
            <div class="row bg-white py-2">
                <div class="col-12">
                </div>
                <div class="col-12 p-3">
                    <nav class="fontq">
                        <div class="nav nav-tabs bg-light" id="nav-tab" role="tablist">
                            <a class="p-3 nav-item nav-link active text-secondary" id="home-tab" data-toggle="tab" href="#datos" role="tab" aria-controls="datos" aria-selected="true"><span class="text-tab">Datos Personales</span></a>
                            <a class="p-3 nav-item nav-link text-secondary" id="empresa-tab" data-toggle="tab" href="#empresa" role="tab" aria-controls="empresa" aria-selected="true"><span class="text-tab">Empresa</span></a>
                            <a class="p-3 nav-item nav-link text-secondary" id="liquid-tab" data-toggle="tab" href="#liquid" role="tab" aria-controls="liquid" aria-selected="true"><span class="text-tab">Liquidación</span></a>
                            <a class="p-3 nav-item nav-link text-secondary" id="control-tab" data-toggle="tab" href="#control" role="tab" aria-controls="control" aria-selected="true"><span class="text-tab">Control y Procesos</span></a>
                            <a class="p-3 nav-item nav-link text-secondary" id="horarios-tab" data-toggle="tab" href="#horarios" role="tab" aria-controls="horarios" aria-selected="true"><span class="text-tab">Horarios</span></a>
                            <a class="p-3 nav-item nav-link text-secondary" id="identifica-tab" data-toggle="tab" href="#identifica" role="tab" aria-controls="identifica" aria-selected="true"><span class="text-tab">Identificadores</span></a>
                            <a class="p-3 nav-item nav-link text-secondary" id="dispositivo-tab" data-toggle="tab" href="#dispositivo" role="tab" aria-controls="dispositivo" aria-selected="true"><span class="text-tab">Dispositivo</span></a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <?php require __DIR__ . '/tab-datos.php'; ?>
                        <?php require __DIR__ . '/tab-empresa.php'; ?>
                        <?php require __DIR__ . '/tab-liquidacion.php'; ?>
                        <?php require __DIR__ . '/tab-control.php'; ?>
                        <?php require __DIR__ . '/tab-horarios.php'; ?>
                        <?php require __DIR__ . '/tab-identifica.php'; ?>
                        <?php require __DIR__ . '/tab-dispositivo.php'; ?>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end pb-2 align-items-center">
                    <a href="../" class="px-3 btn btn-sm fontq btn-outline-custom border mr-2 p-2"><span class="">Salir</span></a>
                    <button type="submit" class="p-2 px-3 btn btn-sm fontq btn-custom" id="btnGuardar">Guardar</button>
                </div>
                <div id="alerta_UpdateLega" class="fontq text-right d-none mt-2 col-12">
                    <span class="p-2 respuesta_UpdateLega fw5 align-middle mr-2"></span>
                    <br /><span class="mensaje_UpdateLega"></span>
                </div>
            </div>
        </form>
        <?php require __DIR__ . '/modalNacion.php' ?>
        <?php require __DIR__ . '/modalProvincia.php' ?>
        <?php require __DIR__ . '/modalLocalidad.php' ?>
        <?php require __DIR__ . '/modalEmpresas.php' ?>
        <?php require __DIR__ . '/modalPlanta.php' ?>
        <?php require __DIR__ . '/modalConvenio.php' ?>
        <?php require __DIR__ . '/modalSectores.php' ?>
        <?php require __DIR__ . '/modalSeccion.php' ?>
        <?php require __DIR__ . '/modalGrupos.php' ?>
        <?php require __DIR__ . '/modalSucursal.php' ?>
        <?php require __DIR__ . '/modalTareas.php' ?>
        <?php require __DIR__ . '/modalHistorial.php' ?>
        <?php require __DIR__ . '/modalPremios.php' ?>
        <?php require __DIR__ . '/modalConceptos.php' ?>
        <?php require __DIR__ . '/modalHorarioAl.php' ?>
        <?php require __DIR__ . '/modalIdentifica.php' ?>
        <?php require __DIR__ . '/modalPerRelo.php' ?>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    require __DIR__ . "../../../js/DataTable.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DateRanger.php";
    ?>
    <script>
        const NUMERO_LEGAJO = '<?= $_GET['_leg'] ?>';
        const HOMEHOST = '<?= HOMEHOST ?>';
    </script>
    <script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../../js/bootbox.min.js"></script>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="js/tables.js?v=<?= version_file("/personal/legajo/js/tables.js") ?>"></script>
    <script src="js/enviar.js?v=<?= version_file("/personal/legajo/js/enviar.js") ?>"></script>
    <?php
    $opt2 = array(
        'MinLength' => 0, 'SelClose' => 0, 'MaxInpLength' => 10, 'delay' => 250
    );
    ?>
    <script src="js/trash-select.js?v=<?= version_file("/personal/legajo/js/trash-select.js") ?>"></script>
    <script src="js/mascaras.js?v=<?= version_file("/personal/legajo/js/mascaras.js") ?>"></script>
    <script src="js/calculaEdad.js?v=<?= version_file("/personal/legajo/js/calculaEdad.js") ?>"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                minimumResultsForSearch: -1
            });

            $("#LegFeNa").change(function() {
                calcularEdad();
            });
        });
    </script>
    <?php require __DIR__ . "/js/valSelect.php"; ?>

    <script>
        const selectEstruct = (selector, urlData) => {
            // si select2 esta inicializado, return
            if ($(selector).hasClass('select2-hidden-accessible')) {
                return;
            }

            $(selector).select2({
                multiple: false,
                language: "es",
                placeholder: "Seleccionar",
                minimumInputLength: 0,
                minimumResultsForSearch: 10,
                maximumInputLength: 10,
                selectOnClose: 0,
                language: {
                    noResults: function() {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function(args) {
                        var message = 'Máximo 10 caracteres. Elimine ' + overChars + ' caracter';
                        if (overChars != 1) {
                            message += 'es'
                        }
                        return message
                    },
                    searching: function() {
                        return 'Buscando..'
                    },
                    errorLoading: function() {
                        return 'Sin datos..'
                    },
                    inputTooShort: function() {
                        return 'Ingresar 0 o mas caracteres'
                    },
                    maximumSelected: function() {
                        return 'Puede seleccionar solo una opción'
                    }
                },
                ajax: {
                    url: `/<?= HOMEHOST ?>/data/${urlData}`,
                    dataType: "json",
                    type: "GET",
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            _c: $("#_c").val(),
                            _r: $("#_r").val(),
                            sect: $("#LegSect").val() || '',
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        }
                    },
                }
            })
        }
        $('#label_LegMail').tooltip('show');
        singleDatePicker('#LegFeIn', 'center', 'down')
        singleDatePicker('#LegFeEg', 'center', 'down', moment())
        singleDatePicker('#IDVence', 'center', 'down')
        singleDatePicker('#LegFeNa', 'center', 'down')
        singleDatePicker('#CierreFech', 'center', 'down')
        singleDatePicker('#InEgFeEg', 'center', 'down')
        singleDatePicker('#InEgFeIn', 'center', 'down')
        $('#IDVence').val('');
        $('#InEgFeEg').val('');
        $('#InEgFeIn').val('');

        <?php
        if (!$persLegFeNa) {
            echo "$('#LegFeNa').val('');";
        }
        if (!$persLegFeEg) {
            echo "$('#LegFeEg').val('');";
        }
        if (!$persLegFeIn) {
            echo "$('#LegFeIn').val('');";
        }
        if (!$persCierreFech) {
            echo "$('#CierreFech').val('');";
        }
        ?>
        /** Select Naciones */
        $(document).ready(function() {
            selectEstruct(".selectjs_naciones", "getNaciones.php");
            selectEstruct(".selectjs_provincias", "getProvincias.php");
            selectEstruct(".selectjs_localidad", "getLocalidad.php");
        });
        $('.selectjs_sectores').on('select2:select', function(e) {
            $("#select_seccion").removeClass("d-none");
            $("#trash_sect").removeClass("d-none");
            $('.selectjs_secciones').val(null).trigger('change');
            /** Asignamos valor de sector al id SecCodi del modal de alta seccion */
            $('#SecCodi').val($(this).val());
            /** Obtenemos el nombre del text de la selección para mostrar en el modal de seccion */
            const nombresector = $('.selectjs_sectores :selected').text();
            $("#SectorHelpBlock").html('Sector: ' + nombresector);
        });

        $('.selectjs_secciones').on('select2:select', function(e) {
            $("#trash_secc").removeClass("d-none");
        });

        $('#empresa-tab').on('show.bs.tab', function(e) {
            selectEstruct(".selectjs_empresas", "getEmpresas.php");
            selectEstruct(".selectjs_provinciasEmp", "getProvincias.php?");
            selectEstruct(".selectjs_localidadEmp", "getLocalidad.php?");
            selectEstruct(".selectjs_plantas", "getPlantas.php?");
            selectEstruct(".selectjs_convenio", "getConvenios.php?");
            selectEstruct(".selectjs_sectores", "getSectores.php?");
            selectEstruct(".selectjs_secciones", "getSecciones.php?");
            selectEstruct(".selectjs_grupos", "getGrupos.php?");
            selectEstruct(".selectjs_sucursal", "getSucursales.php?");
            selectEstruct(".selectjs_tarea", "getTareas.php?");
        });
    </script>
</body>

</html>