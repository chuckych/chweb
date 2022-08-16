$(function () {
    "use strict"; // Start of use strict

    let opt2 = {
        MinLength: "0",
        SelClose: false,
        MaxInpLength: "10",
        delay: "250",
        allowClear: true,
    };

    function template(data) {
        if ($(data.html).length === 0) {
            return data.text;
        }
        return $(data.html);
    }

    $("#TareProy").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Seleccionar Proyecto",
        dropdownParent: $('#tarModal'),
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 10,
        maximumInputLength: opt2["MaxInpLength"],
        selectOnClose: opt2["SelClose"],
        language: {
            noResults: function () {
                return "No hay resultados..";
            },
            inputTooLong: function (args) {
                var message =
                    "Máximo " +
                    opt2["MaxInpLength"] +
                    " caracteres. Elimine " +
                    overChars +
                    " caracter";
                if (overChars != 1) {
                    message += "es";
                }
                return message;
            },
            searching: function () {
                return "Buscando..";
            },
            errorLoading: function () {
                return "Sin datos..";
            },
            removeAllItems: function () {
                return "Borrar";
            },
            inputTooShort: function () {
                return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
            },
            maximumSelected: function () {
                return "Puede seleccionar solo una opción";
            },
            loadingMore: function () {
                return "Cargando más resultados…";
            },
        },
        ajax: {
            url: `../proy/data/select/selProyFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "ProyNomFiltro",
                    FiltroEstTipo: "Abierto",
                    // ProyNomFiltro: '',
                    // ProyEmprFiltro: $('#ProyEmprFiltro').val(),
                    // ProyRespFiltro: $('#ProyRespFiltro').val(),
                    // ProyPlantFiltro: $('#ProyPlantFiltro').val(),
                    // ProyEstaFiltro: $('#ProyEstaFiltro').val(),
                    // ProyFiltroTarFechas: $("#FiltroTarFechas").val()
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#TareProy").on('select2:select', function (e) {
        $("#TareProc").val('').trigger("change");
        $('#TareProc').select2('open');
    });
    $("#TareProy").on('select2:unselecting', function (e) {
        $("#TareProc").val('').trigger("change");
    });

    $("#TareResp").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Responsable",
        dropdownParent: $('#tarModal'),
        // templateResult: template,
        // templateSelection: template,
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 10,
        maximumInputLength: opt2["MaxInpLength"],
        selectOnClose: opt2["SelClose"],
        language: {
            noResults: function () {
                return "No hay resultados..";
            },
            inputTooLong: function (args) {
                let message = "Máximo " + opt2.MaxInpLength + " caracteres. Elimine " + overChars + " caracter";
                if (overChars != 1) {
                    message += "es";
                }
                return message;
            },
            searching: function () {
                return "Buscando..";
            },
            errorLoading: function () {
                return "Sin datos..";
            },
            removeAllItems: function () {
                return "Borrar";
            },
            inputTooShort: function () {
                return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
            },
            maximumSelected: function () {
                return "Puede seleccionar solo una opción";
            },
            loadingMore: function () {
                return "Cargando más resultados…";
            },
        },
        ajax: {
            url: "../proy/data/select/selResponsable.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });

    if ($("#TareProy").val()) {
        $("#TareProc").select2({
            language: "es",
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            placeholder: "Proceso",
            dropdownParent: $('#tarModal'),
            // templateResult: template,
            // templateSelection: template,
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 10,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return "No hay resultados..";
                },
                inputTooLong: function (args) {
                    let message = "Máximo " + opt2.MaxInpLength + " caracteres. Elimine " + overChars + " caracter";
                    if (overChars != 1) {
                        message += "es";
                    }
                    return message;
                },
                searching: function () {
                    return "Buscando..";
                },
                errorLoading: function () {
                    return "Sin datos..";
                },
                removeAllItems: function () {
                    return "Borrar";
                },
                inputTooShort: function () {
                    return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
                },
                maximumSelected: function () {
                    return "Puede seleccionar solo una opción";
                },
                loadingMore: function () {
                    return "Cargando más resultados…";
                },
            },
            ajax: {
                url: "../proy/data/getProcesos.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        selectProc: $("#TareProy").trigger("change").val(),
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
        });
        $("#TarePlano").select2({
            language: "es",
            multiple: false,
            allowClear: opt2["allowClear"],
            language: "es",
            placeholder: "Plano",
            dropdownParent: $('#tarModal'),
            templateResult: template,
            // templateSelection: template,
            minimumInputLength: opt2["MinLength"],
            minimumResultsForSearch: 10,
            maximumInputLength: opt2["MaxInpLength"],
            selectOnClose: opt2["SelClose"],
            language: {
                noResults: function () {
                    return "No hay resultados..";
                },
                inputTooLong: function (args) {
                    let message = "Máximo " + opt2.MaxInpLength + " caracteres. Elimine " + overChars + " caracter";
                    if (overChars != 1) {
                        message += "es";
                    }
                    return message;
                },
                searching: function () {
                    return "Buscando..";
                },
                errorLoading: function () {
                    return "Sin datos..";
                },
                removeAllItems: function () {
                    return "Borrar";
                },
                inputTooShort: function () {
                    return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
                },
                maximumSelected: function () {
                    return "Puede seleccionar solo una opción";
                },
                loadingMore: function () {
                    return "Cargando más resultados…";
                },
            },
            ajax: {
                url: "../proy/data/getPlanos.php",
                dataType: "json",
                type: "POST",
                delay: opt2["delay"],
                data: function (params) {
                    return {
                        q: params.term,
                        selectPlano: true,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
        });
    }

    select2EmptyRemove("#TareProy");
    select2EmptyRemove("#TareProc");
    select2EmptyRemove("#TareResp");
    checkLengthInput('#TareHoraFin', 5)
    checkLengthInput('#TareHoraIni', 5)
    checkLengthInput('#TareFechaFin', 10)
    checkLengthInput('#TareFechaIni', 10)
    // $('#TareProy').on('select2:select', function (e) {
    //     $("#select2-TareProy-container").removeClass("border border-danger border-wide");
    // });

    $("#ProyEmpr").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Empresa",
        dropdownParent: $('#tarModal'),
        // templateResult: template,
        // templateSelection: template,
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 10,
        maximumInputLength: opt2["MaxInpLength"],
        selectOnClose: opt2["SelClose"],
        language: {
            noResults: function () {
                return "No hay resultados..";
            },
            inputTooLong: function (args) {
                var message =
                    "Máximo " +
                    opt2["MaxInpLength"] +
                    " caracteres. Elimine " +
                    overChars +
                    " caracter";
                if (overChars != 1) {
                    message += "es";
                }
                return message;
            },
            searching: function () {
                return "Buscando..";
            },
            errorLoading: function () {
                return "Sin datos..";
            },
            removeAllItems: function () {
                return "Borrar";
            },
            inputTooShort: function () {
                return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
            },
            maximumSelected: function () {
                return "Puede seleccionar solo una opción";
            },
            loadingMore: function () {
                return "Cargando más resultados…";
            },
        },
        ajax: {
            url: `../proy/data/select/selEmpresas.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#ProyPlant").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Plantilla procesos",
        dropdownParent: $('#tarModal'),
        templateResult: template,
        // templateSelection: template,
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 10,
        maximumInputLength: opt2["MaxInpLength"],
        selectOnClose: opt2["SelClose"],
        language: {
            noResults: function () {
                return "No hay resultados..";
            },
            inputTooLong: function (args) {
                var message =
                    "Máximo " +
                    opt2["MaxInpLength"] +
                    " caracteres. Elimine " +
                    overChars +
                    " caracter";
                if (overChars != 1) {
                    message += "es";
                }
                return message;
            },
            searching: function () {
                return "Buscando..";
            },
            errorLoading: function () {
                return "Sin datos..";
            },
            removeAllItems: function () {
                return "Borrar";
            },
            inputTooShort: function () {
                return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
            },
            maximumSelected: function () {
                return "Puede seleccionar solo una opción";
            },
            loadingMore: function () {
                return "Cargando más resultados…";
            },
        },
        ajax: {
            url: "../proy/data/select/selPlantilla.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#ProyEsta").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Estado",
        dropdownParent: $('#tarModal'),
        templateResult: template,
        // templateSelection: template,
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 10,
        maximumInputLength: opt2["MaxInpLength"],
        selectOnClose: opt2["SelClose"],
        language: {
            noResults: function () {
                return "No hay resultados..";
            },
            inputTooLong: function (args) {
                let message = "Máximo " + opt2.MaxInpLength + " caracteres. Elimine " + overChars + " caracter";
                if (overChars != 1) {
                    message += "es";
                }
                return message;
            },
            searching: function () {
                return "Buscando..";
            },
            errorLoading: function () {
                return "Sin datos..";
            },
            removeAllItems: function () {
                return "Borrar";
            },
            inputTooShort: function () {
                return "Ingresar " + opt2["MinLength"] + " o mas caracteres";
            },
            maximumSelected: function () {
                return "Puede seleccionar solo una opción";
            },
            loadingMore: function () {
                return "Cargando más resultados…";
            },
        },
        ajax: {
            url: "../proy/data/select/selEstado.php",
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });

    $('.date').on('show.daterangepicker', function (ev, picker) {
        // $.notifyClose();
        // notify("Seleccione una Fecha de Inicio y Fin", "info", 0, "right");
    });
    $('.date').on('hide.daterangepicker', function (ev, picker) {
        // $.notifyClose();
    });

    $('.HoraMask').mask(maskBehavior, spOptions);

    $('.date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: false,
        showWeekNumbers: false,
        autoUpdateInput: true,
        maxDate: moment(),
        opens: "left",
        drops: "up",
        autoApply: true,
        linkedCalendars: false,
        ranges: {
            'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
        },
        locale: {
            format: "DD/MM/YYYY",
            separator: " al ",
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Para",
            customRangeLabel: "Personalizado",
            weekLabel: "Sem",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            "monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            firstDay: 1,
            alwaysShowCalendars: true,
            applyButtonClasses: "btn btn-tabler",
        },
    });
    $('#TareFechaFin').on('change', function (e) {
        e.preventDefault();
        if ($(this).val().length == 10) {
            $(this).removeClass("border border-danger border-wide");
        } else {
            $(this).addClass("border border-danger border-wide");
        }
    });
    $('#TareFechaIni').on('change', function (e) {
        e.preventDefault();
        if ($(this).val().length == 10) {
            $(this).removeClass("border border-danger border-wide");
        } else {
            $(this).addClass("border border-danger border-wide");
        }
    });

});