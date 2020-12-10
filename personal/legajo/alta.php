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
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="js/enviar.js"></script>
    <script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="..\..\js\bootstrap-notify-master\bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <?php
    $opt2 = array(
        'MinLength' => 0, 'SelClose' => 0, 'MaxInpLength' => 10, 'delay' => 250
    );
    ?>
    <script src="js/trash-select.js"></script>
    <script src="js/mascaras.js"></script>
    <script src="js/calculaEdad.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                minimumResultsForSearch: -1
            });
        });
        $(document).ready(function() {
            $("#LegFeNa").change(function() {
                calcularEdad();
            });
        });
    </script>
    <?php require __DIR__ . "/js/valSelect.php"; ?>
    <script>
        $('#label_LegMail').tooltip('show');

        /** Select Naciones */
        $(document).ready(function() {
            $(".selectjs_naciones").select2({
                multiple: false,
                language: "es",
                placeholder: "Seleccionar",
                minimumInputLength: <?= $opt2['MinLength'] ?>,
                minimumResultsForSearch: 2,
                maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                selectOnClose: <?= $opt2['SelClose'] ?>,
                language: {
                    noResults: function() {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function(args) {
                        var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                        return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                    },
                    maximumSelected: function() {
                        return 'Puede seleccionar solo una opción'
                    }
                },
                ajax: {
                    url: "/<?= HOMEHOST ?>/data/getNaciones.php",
                    dataType: "json",
                    type: "GET",
                    delay: <?= $opt2['delay'] ?>,
                    data: function(params) {
                        return {
                            q: params.term,
                            _c: $("#_c").val(),
                            _r: $("#_r").val(),
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        }
                    }
                }
            })
        });

        /** Select Provincias */
        $(document).ready(function() {
            $(".selectjs_provincias").select2({
                multiple: false,
                language: "es",
                placeholder: "Seleccionar",
                minimumInputLength: <?= $opt2['MinLength'] ?>,
                minimumResultsForSearch: 2,
                maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                selectOnClose: <?= $opt2['SelClose'] ?>,
                language: {
                    noResults: function() {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function(args) {
                        var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                        return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                    },
                    maximumSelected: function() {
                        return 'Puede seleccionar solo una opción'
                    }
                },
                ajax: {
                    url: "/<?= HOMEHOST ?>/data/getProvincias.php",
                    dataType: "json",
                    type: "GET",
                    delay: <?= $opt2['delay'] ?>,
                    data: function(params) {
                        return {
                            q: params.term,
                            _c: $("#_c").val(),
                            _r: $("#_r").val(),
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        }
                    },
                }
            })
        });

        /** Select Localidades */
        $(document).ready(function() {
            $(".selectjs_localidad").select2({
                multiple: false,
                language: "es",
                placeholder: "Seleccionar",
                minimumInputLength: <?= $opt2['MinLength'] ?>,
                minimumResultsForSearch: 2,
                maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                selectOnClose: <?= $opt2['SelClose'] ?>,
                language: {
                    noResults: function() {
                        return 'No hay resultados..'
                    },
                    inputTooLong: function(args) {
                        var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                        return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                    },
                    maximumSelected: function() {
                        return 'Puede seleccionar solo una opción'
                    }
                },
                ajax: {
                    url: "/<?= HOMEHOST ?>/data/getLocalidad.php",
                    dataType: "json",
                    type: "GET",
                    delay: <?= $opt2['delay'] ?>,
                    data: function(params) {
                        return {
                            q: params.term,
                            _c: $("#_c").val(),
                            _r: $("#_r").val(),
                        }
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        }
                    },
                }
            })
        });


        $('.selectjs_sectores').on('select2:select', function(e) {
            $("#select_seccion").removeClass("d-none");
            $("#trash_sect").removeClass("d-none");
            $('.selectjs_secciones').val(null).trigger('change');
            /** Asignamos valor de sector al id SecCodi del modal de alta seccion */
            $('#SecCodi').val($(this).val());
            /** Obtenemos el nombre del text de la seleccion para mostrar en el modal de seccion */
            var nombresector = $('.selectjs_sectores :selected').text();
            $("#SectorHelpBlock").html('Sector: ' + nombresector);
        });

        $('.selectjs_secciones').on('select2:select', function(e) {
            $("#trash_secc").removeClass("d-none");
        });
        
        $('#empresa-tab').on('shown.bs.tab', function(e) {
            /** Select Empresas */
            $(document).ready(function() {
                $(".selectjs_empresas").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 4,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getEmpresas.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select ProvinciasEMP */
            $(document).ready(function() {
                $(".selectjs_provinciasEmp").select2({
                    dropdownParent: "#altaEmpresa",
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 4,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getProvincias.php?_c=<?= $_SESSION["RECID_CLIENTE"] ?>&_r=<?= $_SESSION['RECID_ROL'] ?>",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select LocalidadesEMP */
            $(document).ready(function() {
                $(".selectjs_localidadEmp").select2({
                    dropdownParent: "#altaEmpresa",
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 4,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getLocalidad.php?_c=<?= $_SESSION["RECID_CLIENTE"] ?>&_r=<?= $_SESSION['RECID_ROL'] ?>",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select Plantas */
            $(document).ready(function() {
                $(".selectjs_plantas").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 4,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getPlantas.php?_c=<?= $_SESSION["RECID_CLIENTE"] ?>&_r=<?= $_SESSION['RECID_ROL'] ?>",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select Sectores */
            $(document).ready(function() {
                $(".selectjs_sectores").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getSectores.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                                _leg: $("#LegNume").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select Secciones */
            $(document).ready(function() {
                $(".selectjs_secciones").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getSecciones.php?_c=<?= $_SESSION["RECID_CLIENTE"] ?>&_r=<?= $_SESSION['RECID_ROL'] ?>",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                sect: $("#LegSect").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select Grupos */
            $(document).ready(function() {
                $(".selectjs_grupos").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getGrupos.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select Sucursales */
            $(document).ready(function() {
                $(".selectjs_sucursal").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getSucursales.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select Tareas de producción */
            $(document).ready(function() {
                $(".selectjs_tarea").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getTareas.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Select Convenio */
            $(document).ready(function() {
                $(".selectjs_convenio").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getConvenios.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

        });

        // Al seleccionar la pestaña liquidación
        $('#liquid-tab').on('show.bs.tab', function(e) {
            /** Select Premios */
            $(document).ready(function() {
                $(".selectjs_premios").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getPremios.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });
            /** Table Perineg */

            $('#Perineg').DataTable({
                deferRender: true,
                "ajax": {
                    url: "../../data/getPerineg.php",
                    type: "GET",
                    'data': {
                        q2: LegNume
                    },
                },
                columns: [{
                    "class": "align-middle ls1",
                    "data": "InEgFeIn"
                }, {
                    "class": "align-middle ls1",
                    "data": "InEgFeEg"
                }, {
                    "class": "align-middle",
                    "data": "InEgCaus"
                }, {
                    "class": "align-middle text-center",
                    "data": "eliminar"
                }, {
                    "class": "align-middle w-100",
                    "data": "null"
                }],
                paging: false,
                scrollX: false,
                scrollCollapse: false,
                searching: false,
                info: false,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanish.json"
                },
            });

            /** Table Perpremio */
            $('#Perpremio').DataTable({
                deferRender: true,
                "ajax": {
                    url: "../../data/getPerPremi.php",
                    type: "GET",
                    'data': {
                        q2: LegNume
                    },
                },
                columns: [{
                    "class": "align-middle ls1 text-center",
                    "data": "LPreCodi"
                }, {
                    "class": "align-middle ls1",
                    "data": "PreDesc"
                }, {
                    "class": "align-middle text-center",
                    "data": "eliminar"
                }, {
                    "class": "align-middle w-100",
                    "data": "null"
                }],
                paging: false,
                scrollX: false,
                scrollCollapse: false,
                searching: false,
                info: false,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanish.json"
                },
            });

            /** Select OtroConLeg */
            $(document).ready(function() {
                $(".selectjs_conceptos").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getOtroConLeg.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });

            /** Tabla OtrosConLeg */
            $('#OtrosConLeg').DataTable({
                deferRender: true,
                "ajax": {
                    url: "../../data/getConLeg.php",
                    type: "GET",
                    'data': {
                        q2: LegNume
                    },
                },
                columns: [{
                    "class": "align-middle ls1 text-center",
                    "data": "OTROConCodi"
                }, {
                    "class": "align-middle ls1",
                    "data": "OTROConDesc"
                }, {
                    "class": "align-middle ls1 text-center",
                    "data": "OTROConValor"
                }, {
                    "class": "align-middle text-center",
                    "data": "eliminar"
                }, {
                    "class": "align-middle w-100",
                    "data": "null"
                }],
                paging: false,
                scrollX: false,
                scrollCollapse: false,
                searching: false,
                info: false,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanish.json"
                },
            });

            $('#liquid-tab').on('hide.bs.tab', function(e) {
                $('#Perpremio').DataTable().clear().draw().destroy();
                $('#Perineg').DataTable().clear().draw().destroy();
                $('#OtrosConLeg').DataTable().clear().draw().destroy();
            })

        })

        // Al seleccionar la pestaña control y procesos
        $('#control-tab').on('show.bs.tab', function(e) {
            /** Select Regla de control */
            $(document).ready(function() {
                $(".selectjs_regla").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getReglaCo.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })
            });
        });

        // Al seleccionar la pestaña horarios
        $('#horarios-tab').on('show.bs.tab', function(e) {
            /** Select horario alternativo */
            $('#altahorarioal').on('shown.bs.modal', function(e) {
                $(document).ready(function() {
                    $(".selectjs_horarioal").select2({
                        dropdownParent: "#altahorarioal",
                        multiple: false,
                        language: "es",
                        placeholder: "Seleccionar",
                        minimumInputLength: <?= $opt2['MinLength'] ?>,
                        minimumResultsForSearch: 10,
                        maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                        selectOnClose: <?= $opt2['SelClose'] ?>,
                        language: {
                            noResults: function() {
                                return 'No hay resultados..'
                            },
                            inputTooLong: function(args) {
                                var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                                return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                            },
                            maximumSelected: function() {
                                return 'Puede seleccionar solo una opción'
                            }
                        },
                        ajax: {
                            url: "/<?= HOMEHOST ?>/data/getHorarios.php",
                            dataType: "json",
                            type: "GET",
                            delay: <?= $opt2['delay'] ?>,
                            data: function(params) {
                                return {
                                    q: params.term,
                                    _c: $("#_c").val(),
                                    _r: $("#_r").val(),
                                }
                            },
                            processResults: function(data) {
                                return {
                                    results: data
                                }
                            },
                        }
                    }).select2('open');
                });
            });
            /** Table PerHoAlt */
            $('#PerHoAlt').DataTable({
                deferRender: true,
                "ajax": {
                    url: "../../data/GetPerHoAl.php",
                    type: "GET",
                    'data': {
                        q2: LegNume
                    },
                },
                columns: [{
                    "class": "align-middle ls1 text-center",
                    "data": "LeHAHora"
                }, {
                    "class": "align-middle",
                    "data": "HorDesc"
                }, {
                    "class": "align-middle text-center",
                    "data": "eliminar"
                }, {
                    "class": "align-middle w-100",
                    "data": "null"
                }],
                paging: false,
                scrollX: false,
                scrollCollapse: false,
                searching: false,
                info: false,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanish.json"
                },
            });
            $('#horarios-tab').on('hide.bs.tab', function(e) {
                $('#PerHoAlt').DataTable().clear().draw().destroy();
            })
        });
        $('#identifica-tab').on('show.bs.tab', function(e) {
            /** Table identifica */
            $('#Identifica-table').DataTable({
                deferRender: true,
                "ajax": {
                    url: "../../data/GetIdentifica.php",
                    type: "GET",
                    'data': {
                        q2: LegNume
                    },
                },
                columns: [{
                    "class": "align-middle ls1",
                    "data": "IDCodigo"
                }, {
                    "class": "align-middle text-center",
                    "data": "IDFichada"
                }, {
                    "class": "align-middle",
                    "data": "IDVence"
                }, {
                    "class": "align-middle text-center",
                    "data": "eliminar"
                }, {
                    "class": "align-middle w-100",
                    "data": "null"
                }],
                paging: false,
                scrollX: false,
                scrollCollapse: false,
                searching: false,
                info: false,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanish.json"
                },
            });

            $('#identifica-tab').on('hide.bs.tab', function(e) {
                $('#Identifica-table').DataTable().clear().draw().destroy();
            })
        });
        $('#dispositivo-tab').on('show.bs.tab', function(e) {
            $(document).ready(function() {

                $(".selectjs_grupocapt").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getGrupoCapt.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })

                $(".selectjs_Relojes").select2({
                    multiple: false,
                    language: "es",
                    placeholder: "Seleccionar",
                    minimumInputLength: <?= $opt2['MinLength'] ?>,
                    minimumResultsForSearch: 10,
                    maximumInputLength: <?= $opt2['MaxInpLength'] ?>,
                    selectOnClose: <?= $opt2['SelClose'] ?>,
                    language: {
                        noResults: function() {
                            return 'No hay resultados..'
                        },
                        inputTooLong: function(args) {
                            var message = 'Máximo <?= $opt2['MaxInpLength'] ?>caracteres. Elimine ' + overChars + ' caracter';
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
                            return 'Ingresar <?= $opt2['MinLength'] . ' ' ?>o mas caracteres'
                        },
                        maximumSelected: function() {
                            return 'Puede seleccionar solo una opción'
                        }
                    },
                    ajax: {
                        url: "/<?= HOMEHOST ?>/data/getRelojes.php",
                        dataType: "json",
                        type: "GET",
                        delay: <?= $opt2['delay'] ?>,
                        data: function(params) {
                            return {
                                q: params.term,
                                _c: $("#_c").val(),
                                _r: $("#_r").val(),
                            }
                        },
                        processResults: function(data) {
                            return {
                                results: data
                            }
                        },
                    }
                })

            });

            var LegGrHa = $('#LegGrHa').val();
            var LegNume = $('#LegNume').val();

            var table = $('#GrupoCapt').DataTable({
                "ajax": {
                    url: "../../data/GetReloHabi.php",
                    type: "GET",
                    'data': {
                        q2: LegGrHa,
                    },
                },
                columns: [{
                    "class": "align-middle ls1",
                    "data": "Serie"
                }, {
                    "class": "align-middle",
                    "data": "Descrip"
                }, {
                    "class": "align-middle",
                    "data": "Marca"
                }, {
                    "class": "align-middle w-100",
                    "data": "null"
                }],
                paging: false,
                scrollX: false,
                scrollCollapse: false,
                searching: false,
                info: false,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanish.json"
                },
            });

            var table = $('#TablePerRelo').DataTable({
                "ajax": {
                    url: "../../data/GetPerRelo.php",
                    type: "GET",
                    'data': {
                        q2: LegNume,
                    },
                },
                columns: [{
                    "class": "align-middle ls1",
                    "data": "Serie"
                }, {
                    "class": "align-middle ls1",
                    "data": "Descrip"
                }, {
                    "class": "align-middle",
                    "data": "Marca"
                }, {
                    "class": "align-middle ls1",
                    "data": "Desde"
                }, {
                    "class": "align-middle ls1 fw4",
                    "data": "Vence"
                }, {
                    "class": "align-middle text-center",
                    "data": "eliminar"
                }, {
                    "class": "align-middle w-100",
                    "data": "null"
                }],
                paging: false,
                scrollX: false,
                scrollCollapse: false,
                searching: false,
                info: false,
                ordering: false,
                language: {
                    "url": "../../js/DataTableSpanish.json"
                },
            });

            $('#dispositivo-tab').on('hide.bs.tab', function(e) {
                $('#GrupoCapt').DataTable().clear().draw().destroy();
                $('#TablePerRelo').DataTable().clear().draw().destroy();
            })
        });
    </script>
</body>

</html>