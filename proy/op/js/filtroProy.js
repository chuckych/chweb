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
    $("#ProyNomFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Proyecto",
        dropdownParent: $('#offcanvasFiltros'),
        minimumInputLength: opt2["MinLength"],
        minimumResultsForSearch: 10,
        maximumInputLength: opt2["MaxInpLength"],
        templateResult: template,
        // templateSelection: template,
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
                    FiltroEstTipo: $("input[name=FiltroEstTipo]:checked").val(),
                    ProyNomFiltro: '',
                    ProyEmprFiltro: $('#ProyEmprFiltro').val(),
                    ProyRespFiltro: $('#ProyRespFiltro').val(),
                    ProyPlantFiltro: $('#ProyPlantFiltro').val(),
                    ProyEstaFiltro: $('#ProyEstaFiltro').val(),
                    ProyFiltroFechas: $("#FiltroFechas").val()
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#ProyEmprFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Empresa",
        dropdownParent: $('#offcanvasFiltros'),
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
                    NomFiltro: "ProyEmpFiltro",
                    FiltroEstTipo: $("input[name=FiltroEstTipo]:checked").val(),
                    ProyNomFiltro: $('#ProyNomFiltro').val(),
                    ProyEmprFiltro: '',
                    ProyRespFiltro: $('#ProyRespFiltro').val(),
                    ProyPlantFiltro: $('#ProyPlantFiltro').val(),
                    ProyEstaFiltro: $('#ProyEstaFiltro').val(),
                    ProyFiltroFechas: $("#FiltroFechas").val()
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    })
    $("#ProyRespFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Responsable",
        dropdownParent: $('#offcanvasFiltros'),
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
            url: `../proy/data/select/selProyFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "ProyRespFiltro",
                    FiltroEstTipo: $("input[name=FiltroEstTipo]:checked").val(),
                    ProyNomFiltro: $('#ProyNomFiltro').val(),
                    ProyEmprFiltro: $('#ProyEmprFiltro').val(),
                    ProyRespFiltro: '',
                    ProyPlantFiltro: $('#ProyPlantFiltro').val(),
                    ProyEstaFiltro: $('#ProyEstaFiltro').val(),
                    ProyFiltroFechas: $("#FiltroFechas").val()
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#ProyPlantFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Plantilla Proceso",
        dropdownParent: $('#offcanvasFiltros'),
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
            url: `../proy/data/select/selProyFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "ProyPlantFiltro",
                    FiltroEstTipo: $("input[name=FiltroEstTipo]:checked").val(),
                    ProyNomFiltro: $('#ProyNomFiltro').val(),
                    ProyEmprFiltro: $('#ProyEmprFiltro').val(),
                    ProyRespFiltro: $('#ProyRespFiltro').val(),
                    ProyPlantFiltro: '',
                    ProyEstaFiltro: $('#ProyEstaFiltro').val(),
                    ProyFiltroFechas: $("#FiltroFechas").val()
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#ProyEstaFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Estado",
        dropdownParent: $('#offcanvasFiltros'),
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
            url: `../proy/data/select/selProyFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "ProyEstaFiltro",
                    FiltroEstTipo: $("input[name=FiltroEstTipo]:checked").val(),
                    ProyNomFiltro: $('#ProyNomFiltro').val(),
                    ProyEmprFiltro: $('#ProyEmprFiltro').val(),
                    ProyRespFiltro: $('#ProyRespFiltro').val(),
                    ProyPlantFiltro: $('#ProyPlantFiltro').val(),
                    ProyEstaFiltro: '',
                    ProyFiltroFechas: $("#FiltroFechas").val()
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });

    function refreshSelected(slectjs) {
        $(slectjs).on('select2:select', function (e) {
            $("#tableProyectos").DataTable().ajax.reload();
        });
    }

    function refreshUnselected(slectjs) {
        $(slectjs).on('select2:unselecting', function (e) {
            $("#tableProyectos").DataTable().ajax.reload();
        });
    }
    refreshSelected('#ProyEmprFiltro');
    refreshUnselected('#ProyEmprFiltro');

    refreshSelected('#ProyRespFiltro');
    refreshUnselected('#ProyRespFiltro');

    refreshSelected('#ProyPlantFiltro');
    refreshUnselected('#ProyPlantFiltro');

    refreshSelected('#ProyEstaFiltro');
    refreshUnselected('#ProyEstaFiltro');

    refreshSelected('#ProyNomFiltro');
    refreshUnselected('#ProyNomFiltro');

    $(".ProyLimpiaFiltro").click(function () {
        $("#ProyEmprFiltro").val(null).trigger('change');
        $("#ProyRespFiltro").val(null).trigger('change');
        $("#ProyPlantFiltro").val(null).trigger('change');
        $("#ProyEstaFiltro").val(null).trigger('change');
        $("#ProyNomFiltro").val(null).trigger('change');
        $("#FiilroAbierto").prop('checked', true);
        $('#FiltroFechas').data('daterangepicker').setStartDate(moment());
        $('#FiltroFechas').data('daterangepicker').setEndDate(moment());
        $('#FiltroFechas').val(null);
        $('#LimpiarFecha').hide();
        $("#tableProyectos").DataTable().ajax.reload();
    });

    $('#FiltroFechas').on('show.daterangepicker', function (ev, picker) {
        $(".container").addClass('backdrop');
    });
    $('#FiltroFechas').on('hide.daterangepicker', function (ev, picker) {
        $(".container").removeClass('backdrop');
    });

    $('#FiltroFechas').daterangepicker({
        singleDatePicker: false,
        showDropdowns: false,
        showWeekNumbers: false,
        autoUpdateInput: true,
        opens: "left",
        autoApply: true,
        linkedCalendars: false,
        ranges: {
            // 'Hoy': [moment(), moment()],
            'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Esta semana': [moment().day(1), moment().day(7)],
            'Semana Anterior': [moment().subtract(1, 'week').day(1), moment().subtract(1, 'week').day(7)],
            'Próxima Semana': [moment().add(1, 'week').day(1), moment().add(1, 'week').day(7)],
            'Próximo Mes': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')],
            'Este mes': [moment().startOf('month'), moment().endOf('month')],
            // 'Mes anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            // 'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
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
    $('#FiltroFechas').val(null);

    $('#LimpiarFecha').hide();
    $("#LimpiarFecha").click(function () {
        $('#LimpiarFecha').hide();
        $('#FiltroFechas').data('daterangepicker').setStartDate(moment());
        $('#FiltroFechas').data('daterangepicker').setEndDate(moment());
        $('#FiltroFechas').val(null);
        $("#tableProyectos").DataTable().ajax.reload();
    });

    $('#FiltroFechas').on('apply.daterangepicker', function (ev, picker) {
        $('#LimpiarFecha').show();
        $("#tableProyectos").DataTable().ajax.reload();
    });
});