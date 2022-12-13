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
    $("#ProyEmpr").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Empresa",
        dropdownParent: $('#proyModal'),
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
        dropdownParent: $('#proyModal'),
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
        dropdownParent: $('#proyModal'),
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
    $("#ProyResp").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Responsable del proyecto",
        dropdownParent: $('#proyModal'),
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
    
    $('#ProyEmpr').on("select2:select", function (e) {
    });

    $('#ProyIniFin').on('show.daterangepicker', function (ev, picker) {
        $.notifyClose();
        notify("Seleccione una Fecha de Inicio y Fin", "info", 0, "right");
    });
    $('#ProyIniFin').on('hide.daterangepicker', function (ev, picker) {
        $.notifyClose();
    });

    $('#ProyIniFin').daterangepicker({
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

});