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
    $("#tarProyNomFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Proyecto",
        dropdownParent: $('#offcanvasFiltrosTar'),
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
            url: `../proy/data/select/selTarFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "tarProyNomFiltro",
                    TareEstado: $("input[name=FiltroTarEstTipo]:checked").val(),
                    tarEmprFiltro: $('#tarEmprFiltro').val(),
                    tarProcNomFiltro: $('#tarProcNomFiltro').val(),
                    tarPlanoFiltro: $('#tarPlanoFiltro').val(),
                    tarRespFiltro: $('#tarRespFiltro').val(),
                    FiltroTarFechas: $("#FiltroTarFechas").val(),
                    tableTareas_filter: $("#tableTareas_filter input").val(),
                    tarProyEsta: $("input[name=tarProyEsta]:checked").val() ?? ''
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#tarProcNomFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Proceso",
        dropdownParent: $('#offcanvasFiltrosTar'),
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
            url: `../proy/data/select/selTarFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "tarProcNomFiltro",
                    TareEstado: $("input[name=FiltroTarEstTipo]:checked").val(),
                    // tarEmprFiltro: $('#tarEmprFiltro').val(),
                    tarProyNomFiltro: $('#tarProyNomFiltro').val(),
                    tarPlanoFiltro: $('#tarPlanoFiltro').val(),
                    tarRespFiltro: $('#tarRespFiltro').val(),
                    FiltroTarFechas: $("#FiltroTarFechas").val(),
                    tableTareas_filter: $("#tableTareas_filter input").val(),
                    tarProyEsta: $("input[name=tarProyEsta]:checked").val() ?? ''

                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    })
    $("#tarEmprFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Empresa",
        dropdownParent: $('#offcanvasFiltrosTar'),
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
            url: `../proy/data/select/selTarFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "tarEmprFiltro",
                    TareEstado: $("input[name=FiltroTarEstTipo]:checked").val(),
                    // tarEmprFiltro: $('#tarEmprFiltro').val(),
                    tarProyNomFiltro: $('#tarProyNomFiltro').val(),
                    tarProcNomFiltro: $('#tarProcNomFiltro').val(),
                    tarPlanoFiltro: $('#tarPlanoFiltro').val(),
                    tarRespFiltro: $('#tarRespFiltro').val(),
                    FiltroTarFechas: $("#FiltroTarFechas").val(),
                    tableTareas_filter: $("#tableTareas_filter input").val(),
                    tarProyEsta: $("input[name=tarProyEsta]:checked").val() ?? ''
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    })
    $("#tarRespFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Responsable",
        dropdownParent: $('#offcanvasFiltrosTar'),
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
            url: `../proy/data/select/selTarFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "tarRespFiltro",
                    TareEstado: $("input[name=FiltroTarEstTipo]:checked").val(),
                    tarEmprFiltro: $('#tarEmprFiltro').val(),
                    tarProyNomFiltro: $('#tarProyNomFiltro').val(),
                    tarProcNomFiltro: $('#tarProcNomFiltro').val(),
                    tarPlanoFiltro: $('#tarPlanoFiltro').val(),
                    // tarRespFiltro: $('#tarRespFiltro').val(),
                    FiltroTarFechas: $("#FiltroTarFechas").val(),
                    tableTareas_filter: $("#tableTareas_filter input").val(),
                    tarProyEsta: $("input[name=tarProyEsta]:checked").val() ?? ''
                };
            },
            processResults: function (data) {
                return {
                    results: data,
                };
            },
        },
    });
    $("#tarPlanoFiltro").select2({
        language: "es",
        multiple: false,
        allowClear: opt2["allowClear"],
        language: "es",
        placeholder: "Filtrar Plano",
        dropdownParent: $('#offcanvasFiltrosTar'),
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
            url: `../proy/data/select/selTarFiltros.php?${Date.now()}`,
            dataType: "json",
            type: "POST",
            delay: opt2["delay"],
            data: function (params) {
                return {
                    q: params.term,
                    NomFiltro: "tarPlanoFiltro",
                    TareEstado: $("input[name=FiltroTarEstTipo]:checked").val(),
                    tarEmprFiltro: $('#tarEmprFiltro').val(),
                    tarProyNomFiltro: $('#tarProyNomFiltro').val(),
                    tarProcNomFiltro: $('#tarProcNomFiltro').val(),
                    // tarPlanoFiltro: $('#tarPlanoFiltro').val(),
                    tarRespFiltro: $('#tarRespFiltro').val(),
                    FiltroTarFechas: $("#FiltroTarFechas").val(),
                    tableTareas_filter: $("#tableTareas_filter input").val(),
                    tarProyEsta: $("input[name=tarProyEsta]:checked").val() ?? ''
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
            $("#tableTareas").DataTable().ajax.reload();
        });
    }

    function refreshUnselected(slectjs) {
        $(slectjs).on('select2:unselecting', function (e) {
            $("#tableTareas").DataTable().ajax.reload();
        });
    }
    $("input[name=tarProyEsta]").on('change', function (e) {
        $("#tableTareas").DataTable().ajax.reload();
    });

    refreshSelected('#tarProyNomFiltro');
    refreshUnselected('#tarProyNomFiltro');

    refreshSelected('#tarProcNomFiltro');
    refreshUnselected('#tarProcNomFiltro');

    refreshSelected('#tarEmprFiltro');
    refreshUnselected('#tarEmprFiltro');

    refreshSelected('#tarRespFiltro');
    refreshUnselected('#tarRespFiltro');

    refreshSelected('#tarPlanoFiltro');
    refreshUnselected('#tarPlanoFiltro');

    $(".tarLimpiaFiltro").click(function () {
        $("#tarProyNomFiltro").val(null).trigger('change');
        $("#tarProcNomFiltro").val(null).trigger('change');
        $("#tarEmprFiltro").val(null).trigger('change');
        $("#tarRespFiltro").val(null).trigger('change');
        $("#tarPlanoFiltro").val(null).trigger('change');
        $("#FiltroTodos").prop('checked', true);
        $('#FiltroTarFechas').data('daterangepicker').setStartDate(moment());
        $('#FiltroTarFechas').data('daterangepicker').setEndDate(moment());
        $('#FiltroTarFechas').val(null);
        $('#LimpiarTarFecha').hide();
        $('#limpiarSearch').hide();
        $('.chosenDate').remove();
        $("#tableTareas_filter input").val("");
        $("#tableTareas").DataTable().search('').draw();
        $("#tarProyEstaAbierto").prop('checked', true);
    });

    $("#limpiarSearch").click(function () {
        $('#limpiarSearch').fadeOut();
        $("#tableTareas").DataTable().search('').draw();
    });

    $('#tableTareas_filter input').on('keyup', function (e) {
        let btn = $('#limpiarSearch');
        ($(this).val() != "") ? btn.fadeIn() : btn.hide();
    });

    let minmaxDate = (t, f1, f2, selectorRange) => {
        let data = new FormData();
        data.append('t', t);
        data.append('f1', f1);
        data.append('f2', f2);
        axios({
            method: 'post',
            url: 'data/minmaxdate.php',
            data: data,
            headers: { "Content-Type": "multipart/form-data" }
        }).then(function (response) {
            let d = response.data.Mensaje;
            if (response.data.status == 'ok') {
                let maxFormat = d.fecha.maxFormat;
                let minFormat = d.fecha.minFormat;

                $(selectorRange).daterangepicker({
                    singleDatePicker: false,
                    showDropdowns: true,
                    showWeekNumbers: false,
                    autoUpdateInput: true,
                    parentEl: $('.divEstadoTar'),
                    opens: "left",
                    drops: "auto",
                    autoApply: true,
                    minDate: (minFormat),
                    maxDate: (maxFormat),
                    linkedCalendars: false,
                    ranges: {
                        'Hoy': [
                            moment(), moment()
                        ],
                        'Ayer': [
                            moment().subtract(1, 'days'),
                            moment().subtract(1, 'days')
                        ],
                        'Esta semana': [
                            moment().day(1),
                            moment().day(7)
                        ],
                        'Semana Anterior': [
                            moment().subtract(1, 'week').day(1),
                            moment().subtract(1, 'week').day(7)
                        ],
                        'Este mes': [
                            moment().startOf('month'),
                            moment().endOf('month')
                        ],
                        '1er Quincena': [
                            moment().startOf('month'),
                            moment().startOf('month').add(14, 'days')
                        ],
                        '2da Quincena': [
                            moment().startOf('month').add(15, 'days'),
                            moment().endOf('month')
                        ],
                        'Mes anterior': [
                            moment().subtract(1, 'month').startOf('month'),
                            moment().subtract(1, 'month').endOf('month')
                        ],
                        'Últimos 30 días': [
                            moment().subtract(29, 'days'),
                            moment()
                        ],
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
                        monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                        firstDay: 1,
                        alwaysShowCalendars: true,
                        applyButtonClasses: "btn btn-tabler",
                    },
                });

                $('#FiltroTarFechas').val(null);
                $('#LimpiarTarFecha').hide();
                $("#LimpiarTarFecha").click(function () {
                    $('#FiltroTarFechas').data('daterangepicker').setStartDate(moment());
                    $('#FiltroTarFechas').data('daterangepicker').setEndDate(moment());
                    $('#FiltroTarFechas').val(null);
                    $("#tableTareas").DataTable().ajax.reload();
                    $(this).hide();
                    $('.chosenDate').remove()
                });
                $('#FiltroTarFechas').on('apply.daterangepicker', function (ev, picker) {
                    if (picker.chosenLabel != 'Personalizado') {
                        $('.chosenDate').remove()
                        $('.textDate').append('<span class="me-2 chosenDate float-end border p-3 font-weight-normal badge bg-dark-lt animate__animated animate__fadeInUp text-capitalize font08"></span>')
                        $('.chosenDate').html((picker.chosenLabel))
                    }
                    $("#tableTareas").DataTable().ajax.reload();
                });
                $('#FiltroTarFechas').on('change', function (e) {
                    if ($('#FiltroTarFechas').val() != "") {
                        $('#LimpiarTarFecha').show();
                    }
                });
            }

        }).catch(function (error) {
            alert('ERROR minmaxDate\n' + error);
        }).then(function () {

        });
    }

    minmaxDate('proy_tareas', 'TareIni', 'TareFin', '#FiltroTarFechas');
});