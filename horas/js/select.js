/** Select */
$(document).ready(function () {
    $('#Tipo').css({ "width": "200px" });
    $('.form-control').css({ "width": "100%" });
    $('#HoraMin').mask('00:00');
    $('#HoraMax').mask('00:00');

    $('#Filtros').on('shown.bs.modal', function () {
        var opt2 = { MinLength: "0", SelClose: false, MaxInpLength: "10", delay: "250", allowClear: true };
        $(".selectjs_empresa").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Empresas",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getEmpFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        // Emp    : $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        Thora: $("#Thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_plantas").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Plantas",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getPlanFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        // Plan   : $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        Thora: $("#Thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_sectores").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Sectores",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getSectFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        // Sect   : $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        Thora: $("#Thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".select_seccion").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Secciones",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getSec2Fichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        // Sec2   : $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        Thora: $("#Thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_grupos").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Grupos",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getGrupFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        // Grup   : $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        Thora: $("#Thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_sucursal").select2({
            allowClear: opt2["allowClear"],
            multiple: true,
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Sucursales",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getSucFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        // Sucur  : $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        thora: $("#thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_personal").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Legajos",
            minimumInputLength: 0,
            minimumResultsForSearch: 5,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + "2" + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getLegajosFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        // Per    : $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        thora: $("#thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_tipoper").select2({
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Tipo de Personal",
            minimumInputLength: 0,
            minimumResultsForSearch: -1,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + "2" + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getTipoPerFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        // Tipo   : $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        thora: $("#thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });
        $(".selectjs_thora").select2({
            multiple: true,
            allowClear: opt2["allowClear"],
            language: "es",
            dropdownParent: $('#Filtros'),
            placeholder: "Tipo de Horas",
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 10,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return 'No hay resultados..'
                },
                inputTooLong: function (args) {
                    var message = 'Máximo ' + opt2["MaxInpLength"] + ' caracteres. Elimine ' + overChars + ' caracter';
                    if (overChars != 1) {
                        message += 'es'
                    }
                    return message
                },
                searching: function () {
                    return 'Buscando..'
                },
                errorLoading: function () {
                    return 'Sin datos..'
                },
                inputTooShort: function () {
                    return 'Ingresar ' + opt2["MinLength"] + ' o mas caracteres'
                },
                maximumSelected: function () {
                    return 'Puede seleccionar solo una opción'
                }
            },
            ajax: {
                url: "/" + $("#_homehost").val() + "/horas/getSelect/getTHoraFichas.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        Per: $("#Per").val(),
                        Tipo: $("#Tipo").val(),
                        Emp: $("#Emp").val(),
                        Plan: $("#Plan").val(),
                        Sect: $("#Sect").val(),
                        Sec2: $("#Sec2").val(),
                        Grup: $("#Grup").val(),
                        Sucur: $("#Sucur").val(),
                        _dr: $("#_dr").val(),
                        _l: $("#_l").val(),
                        // thora  : $("#thora").val(),
                        SHoras: $("#SHoras").val(),
                        HoraMin: $("#HoraMin").val(),
                        HoraMax: $("#HoraMax").val(),
                        Calculos: $("#Calculos").val()
                    }
                },
                processResults: function (data) {
                    return {
                        results: data
                    }
                },
            }
        });

        function refreshSelected(slectjs) {
            $(slectjs).on('select2:select', function (e) {
                ActualizaTablas()
            });
        }
        function refreshUnselected(slectjs) {
            $(slectjs).on('select2:unselecting', function (e) {
                ActualizaTablas()
            });
        }
        function refreshOnChange(selector) {
            $(selector).change(function () {
                ActualizaTablas()
            });
        };

        refreshSelected('.selectjs_empresa');
        refreshSelected('.selectjs_plantas');
        refreshSelected('.select_seccion');
        refreshSelected('.selectjs_grupos');
        refreshSelected('.selectjs_sucursal');
        refreshSelected('.selectjs_personal');
        refreshSelected('.selectjs_thora');
        refreshSelected('.selectjs_tipoper');

        refreshUnselected('.selectjs_empresa');
        refreshUnselected('.selectjs_plantas');
        refreshUnselected('.select_seccion');
        refreshUnselected('.selectjs_grupos');
        refreshUnselected('.selectjs_sucursal');
        refreshUnselected('.selectjs_personal');
        refreshUnselected('.selectjs_thora');
        refreshUnselected('.selectjs_tipoper');

        $('.selectjs_sectores').on('select2:select', function (e) {
            $(".select_seccion").prop("disabled", false);
            $('.select_seccion').val(null).trigger('change');
            ActualizaTablas()
            var nombresector = $('.selectjs_sectores :selected').text();
            $("#DatosFiltro").html('Sector: ' + nombresector);
        });
        $('.selectjs_sectores').on('select2:unselecting', function (e) {
            $(".select_seccion").prop("disabled", true);
            $('.select_seccion').val(null).trigger('change');
            ActualizaTablas()
        });

        $('#TipoIngreso').val(1);
        $("#TipoIngreso1").change(function () {
            if ($("#TipoIngreso1").is(":checked")) {
                $('#TipoIngreso').val(1)
            }
            ActualizaTablas()
        });
        $('#Calculos').val(null);
        $("#Calculos").change(function () {
            if ($("#Calculos").is(":checked")) {
                $('#Calculos').val(1)
            }
            else {
                $('#Calculos').val(null)
            }
            ActualizaTablas()
        });

        $("#SHoras2").change(function () {
            if ($("#SHoras2").is(":checked")) {
                $('#TipoIngreso').val(2)
            }
            ActualizaTablas()
        });

        $('#HoraMin').change(function () {
            if (!$('#HoraMin').val()) {
                $('#HoraMin').css('border', '1px solid red')
                $('#minmaxreq').addClass('text-danger')
                switchClass('#minmaxreq', 'text-danger fw5 animate__animated animate__flash', 'text-secondary')
            } else {
                $('#HoraMin').css('border', '1px solid #ddd')
                if ($('#HoraMax').val()) {
                    switchClass('#minmaxreq', 'text-secondary', 'text-danger fw5 animate__animated animate__flash')
                    ActualizaTablas()
                }
            }
        });
        $('#HoraMax').change(function () {
            if (!$('#HoraMax').val()) {
                $('#HoraMax').css('border', '1px solid red')
                switchClass('#minmaxreq', 'text-danger fw5 animate__animated animate__flash', 'text-secondary')
            } else {
                $('#HoraMax').css('border', '1px solid #ddd')
                if ($('#HoraMin').val()) {
                    switchClass('#minmaxreq', 'text-secondary', 'text-danger fw5 animate__animated animate__flash')
                    ActualizaTablas()
                }
            }
        });
        refreshOnChange("#Calculos");
        // refreshOnChange("#HoraMin");
        // refreshOnChange("#HoraMax");
    });
});

$('#Filtros').on('hidden.bs.modal', function (e) {
    $('#Filtros').modal('dispose');
});
